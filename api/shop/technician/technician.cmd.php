<?php
/**
 * 轻养到家 - 商家端技师管理 API
 * ZERO 框架规范 - 冒号语法
 * 
 * GET    /api/shop/technician           技师列表
 * POST   /api/shop/technician:invite    邀请技师
 * PUT    /api/shop/technician:123       更新技师信息
 * DELETE /api/shop/technician:123       移除技师
 */

require_once dirname(dirname(dirname(__FILE__))) . '/bootstrap.inc.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';
$id = $_GET['id'] ?? null;

// 需要登录
$auth = auth_require(TokenType::SHOP);

if ($action === 'invite') {
    if ($method !== 'POST') Response::badRequest('请求方法不允许');
    inviteTechnician($auth);
} elseif ($id) {
    switch ($method) {
        case 'GET':
            getTechnicianDetail($auth, $id);
            break;
        case 'PUT':
            updateTechnician($auth, $id);
            break;
        case 'DELETE':
            removeTechnician($auth, $id);
            break;
        default:
            Response::badRequest('请求方法不允许');
    }
} else {
    if ($method !== 'GET') Response::badRequest('请求方法不允许');
    getTechnicianList($auth);
}

/**
 * 获取技师列表
 */
function getTechnicianList($auth) {
    $status = input('status'); // online status
    $page = max(1, (int)input('page', 1));
    $pageSize = min(50, max(1, (int)input('page_size', 20)));
    
    $where = ['base_shop_id = :shop_id', 'base_status_active = 1'];
    $params = ['shop_id' => $auth['id']];
    
    if ($status !== null && $status !== '') {
        $where[] = 'base_status_online = :status';
        $params['status'] = $status;
    }
    
    $whereClause = implode(' AND ', $where);
    
    $total = db()->fetch(
        "SELECT COUNT(*) as count FROM qy_technician_list WHERE $whereClause",
        $params
    )['count'];
    
    $offset = ($page - 1) * $pageSize;
    $technicians = db()->fetchAll(
        "SELECT id, base_profile_realname as name, base_profile_avatar as avatar,
                base_profile_gender as gender, base_stat_rating_avg as rating,
                base_stat_order_count as order_count, base_status_online as online_status,
                base_shop_commission_rate as commission_rate
         FROM qy_technician_list
         WHERE $whereClause
         ORDER BY base_stat_order_count DESC
         LIMIT $offset, $pageSize",
        $params
    );
    
    Response::paginate($technicians, $total, $page, $pageSize);
}

/**
 * 获取技师详情
 */
function getTechnicianDetail($auth, $id) {
    $tech = db()->fetch(
        'SELECT * FROM qy_technician_list WHERE id = ? AND base_shop_id = ?',
        [$id, $auth['id']]
    );
    
    if (!$tech) {
        Response::notFound('技师不存在');
    }
    
    // 获取本月订单统计
    $monthStart = date('Y-m-01');
    $monthStats = db()->fetch(
        'SELECT COUNT(*) as order_count, COALESCE(SUM(base_price_total), 0) as revenue
         FROM qy_order_list
         WHERE base_technician_id = ? AND base_status_order = 5 AND base_time_created >= ?',
        [$id, $monthStart]
    );
    
    Response::success([
        'id' => $tech['id'],
        'name' => $tech['base_profile_realname'],
        'avatar' => $tech['base_profile_avatar'],
        'gender' => $tech['base_profile_gender'],
        'phone' => substr($tech['base_auth_phone'], 0, 3) . '****' . substr($tech['base_auth_phone'], -4),
        'intro' => $tech['base_profile_intro'],
        'rating' => round($tech['base_stat_rating_avg'], 2),
        'order_count' => $tech['base_stat_order_count'],
        'online_status' => $tech['base_status_online'],
        'commission_rate' => $tech['base_shop_commission_rate'],
        'month_stats' => [
            'order_count' => (int)$monthStats['order_count'],
            'revenue' => round($monthStats['revenue'], 2)
        ]
    ]);
}

/**
 * 邀请技师
 */
function inviteTechnician($auth) {
    validate_required(['phone']);
    
    $phone = input('phone');
    $commissionRate = input('commission_rate', 55.00);
    
    if (!validate_phone($phone)) {
        Response::badRequest('手机号格式不正确');
    }
    
    // 查找技师
    $tech = db()->fetch(
        'SELECT * FROM qy_technician_list WHERE base_auth_phone = ?',
        [$phone]
    );
    
    if (!$tech) {
        Response::badRequest('该技师未注册，请先让技师完成注册');
    }
    
    if ($tech['base_status_verify'] != 1) {
        Response::badRequest('该技师尚未通过认证');
    }
    
    if ($tech['base_shop_id'] && $tech['base_shop_id'] != $auth['id']) {
        Response::badRequest('该技师已加入其他商家');
    }
    
    if ($tech['base_shop_id'] == $auth['id']) {
        Response::badRequest('该技师已在您的团队中');
    }
    
    // 关联技师到商家
    db()->update('qy_technician_list', [
        'base_shop_id' => $auth['id'],
        'base_shop_commission_rate' => $commissionRate
    ], 'id = :id', ['id' => $tech['id']]);
    
    // 更新商家技师数
    db()->query(
        'UPDATE qy_shop_list SET base_stat_technician_count = base_stat_technician_count + 1 WHERE id = ?',
        [$auth['id']]
    );
    
    Response::success(null, '技师邀请成功');
}

/**
 * 更新技师信息（主要是分成比例）
 */
function updateTechnician($auth, $id) {
    $tech = db()->fetch(
        'SELECT * FROM qy_technician_list WHERE id = ? AND base_shop_id = ?',
        [$id, $auth['id']]
    );
    
    if (!$tech) {
        Response::notFound('技师不存在');
    }
    
    $data = [];
    
    if (input('commission_rate') !== null) {
        $rate = (float)input('commission_rate');
        if ($rate < 0 || $rate > 100) {
            Response::badRequest('分成比例必须在0-100之间');
        }
        $data['base_shop_commission_rate'] = $rate;
    }
    
    if (empty($data)) {
        Response::badRequest('没有要更新的数据');
    }
    
    db()->update('qy_technician_list', $data, 'id = :id', ['id' => $id]);
    
    Response::success(null, '更新成功');
}

/**
 * 移除技师
 */
function removeTechnician($auth, $id) {
    $tech = db()->fetch(
        'SELECT * FROM qy_technician_list WHERE id = ? AND base_shop_id = ?',
        [$id, $auth['id']]
    );
    
    if (!$tech) {
        Response::notFound('技师不存在');
    }
    
    // 检查是否有进行中的订单
    $activeOrder = db()->fetch(
        'SELECT id FROM qy_order_list 
         WHERE base_technician_id = ? AND base_shop_id = ? AND base_status_order IN (1, 2, 3, 4)',
        [$id, $auth['id']]
    );
    
    if ($activeOrder) {
        Response::badRequest('该技师有进行中的订单，无法移除');
    }
    
    // 解除关联
    db()->update('qy_technician_list', [
        'base_shop_id' => 0,
        'base_shop_commission_rate' => 55.00
    ], 'id = :id', ['id' => $id]);
    
    // 更新商家技师数
    db()->query(
        'UPDATE qy_shop_list SET base_stat_technician_count = base_stat_technician_count - 1 WHERE id = ? AND base_stat_technician_count > 0',
        [$auth['id']]
    );
    
    Response::success(null, '技师已移除');
}
