<?php
/**
 * 轻养到家 - 管理后台商家管理 API
 * ZERO 框架规范 - 冒号语法
 * 
 * GET /api/admin/shop              商家列表
 * GET /api/admin/shop:pending      待审核列表
 * GET /api/admin/shop:123          商家详情
 * PUT /api/admin/shop:123:verify   审核
 * PUT /api/admin/shop:123:status   更新状态
 * PUT /api/admin/shop:123:commission 设置佣金
 */

require_once dirname(dirname(dirname(__FILE__))) . '/bootstrap.inc.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';
$id = $_GET['id'] ?? null;

// 需要管理员登录
$auth = auth_require(TokenType::ADMIN);

if ($action === 'pending') {
    if ($method !== 'GET') Response::badRequest('请求方法不允许');
    getPendingList();
} elseif ($id) {
    if ($action === 'verify' && $method === 'PUT') {
        verifyShop($id);
    } elseif ($action === 'status' && $method === 'PUT') {
        updateShopStatus($id);
    } elseif ($action === 'commission' && $method === 'PUT') {
        updateCommission($id);
    } elseif ($method === 'GET') {
        getShopDetail($id);
    } else {
        Response::badRequest('请求方法不允许');
    }
} else {
    if ($method !== 'GET') Response::badRequest('请求方法不允许');
    getShopList();
}

/**
 * 获取商家列表
 */
function getShopList() {
    $keyword = input('keyword');
    $verifyStatus = input('verify_status');
    $page = max(1, (int)input('page', 1));
    $pageSize = min(50, max(1, (int)input('page_size', 20)));
    
    $where = ['1=1'];
    $params = [];
    
    if ($keyword) {
        $where[] = '(id LIKE :keyword OR base_auth_phone LIKE :keyword2 OR base_profile_name LIKE :keyword3)';
        $params['keyword'] = "%$keyword%";
        $params['keyword2'] = "%$keyword%";
        $params['keyword3'] = "%$keyword%";
    }
    
    if ($verifyStatus !== null && $verifyStatus !== '') {
        $where[] = 'base_status_verify = :verify';
        $params['verify'] = $verifyStatus;
    }
    
    $whereClause = implode(' AND ', $where);
    
    $total = db()->fetch(
        "SELECT COUNT(*) as count FROM qy_shop_list WHERE $whereClause",
        $params
    )['count'];
    
    $offset = ($page - 1) * $pageSize;
    $shops = db()->fetchAll(
        "SELECT id, base_auth_phone as phone, base_profile_name as name,
                base_profile_logo as logo, base_contact_name as contact_name,
                base_stat_technician_count as tech_count, base_stat_order_count as order_count,
                base_commission_rate as commission_rate, base_status_verify as verify_status,
                base_status_active as status, base_time_created as created_at
         FROM qy_shop_list
         WHERE $whereClause
         ORDER BY id DESC
         LIMIT $offset, $pageSize",
        $params
    );
    
    foreach ($shops as &$shop) {
        $shop['phone'] = substr($shop['phone'], 0, 3) . '****' . substr($shop['phone'], -4);
    }
    
    Response::paginate($shops, $total, $page, $pageSize);
}

/**
 * 获取待审核列表
 */
function getPendingList() {
    $shops = db()->fetchAll(
        'SELECT id, base_auth_phone as phone, base_profile_name as name,
                base_contact_name as contact_name, base_time_created as created_at
         FROM qy_shop_list
         WHERE base_status_verify = 0
         ORDER BY base_time_created ASC'
    );
    
    foreach ($shops as &$shop) {
        $shop['phone'] = substr($shop['phone'], 0, 3) . '****' . substr($shop['phone'], -4);
    }
    
    Response::success($shops);
}

/**
 * 获取商家详情
 */
function getShopDetail($id) {
    $shop = db()->fetch('SELECT * FROM qy_shop_list WHERE id = ?', [$id]);
    
    if (!$shop) {
        Response::notFound('商家不存在');
    }
    
    // 获取技师列表
    $technicians = db()->fetchAll(
        'SELECT id, base_profile_realname as name, base_stat_rating_avg as rating,
                base_stat_order_count as order_count, base_status_online as online_status
         FROM qy_technician_list WHERE base_shop_id = ? AND base_status_active = 1',
        [$id]
    );
    
    Response::success([
        'id' => $shop['id'],
        'phone' => substr($shop['base_auth_phone'], 0, 3) . '****' . substr($shop['base_auth_phone'], -4),
        'name' => $shop['base_profile_name'],
        'logo' => $shop['base_profile_logo'],
        'intro' => $shop['base_profile_intro'],
        'address' => $shop['base_profile_address'],
        'contact_name' => $shop['base_contact_name'],
        'certifications' => [
            'license' => $shop['base_cert_license'],
            'permit' => $shop['base_cert_permit']
        ],
        'tech_count' => $shop['base_stat_technician_count'],
        'order_count' => $shop['base_stat_order_count'],
        'commission_rate' => $shop['base_commission_rate'],
        'verify_status' => $shop['base_status_verify'],
        'status' => $shop['base_status_active'],
        'created_at' => $shop['base_time_created'],
        'technicians' => $technicians
    ]);
}

/**
 * 审核商家
 */
function verifyShop($id) {
    validate_required(['action']);
    
    $action = input('action');
    
    $shop = db()->fetch('SELECT * FROM qy_shop_list WHERE id = ?', [$id]);
    if (!$shop) {
        Response::notFound('商家不存在');
    }
    
    if ($shop['base_status_verify'] != 0) {
        Response::badRequest('该商家已审核');
    }
    
    if ($action === 'approve') {
        db()->update('qy_shop_list', ['base_status_verify' => 1], 'id = :id', ['id' => $id]);
        Response::success(null, '审核通过');
    } elseif ($action === 'reject') {
        db()->update('qy_shop_list', ['base_status_verify' => 2], 'id = :id', ['id' => $id]);
        Response::success(null, '已拒绝');
    } else {
        Response::badRequest('无效的操作');
    }
}

/**
 * 更新商家状态
 */
function updateShopStatus($id) {
    validate_required(['status']);
    
    $status = (int)input('status');
    
    $shop = db()->fetch('SELECT id FROM qy_shop_list WHERE id = ?', [$id]);
    if (!$shop) {
        Response::notFound('商家不存在');
    }
    
    db()->update('qy_shop_list', ['base_status_active' => $status], 'id = :id', ['id' => $id]);
    
    // 如果停用商家，同时停用其技师
    if (!$status) {
        db()->update('qy_technician_list', [
            'base_status_active' => 0,
            'base_status_online' => 0
        ], 'base_shop_id = :shop_id', ['shop_id' => $id]);
    }
    
    Response::success(null, $status ? '商家已启用' : '商家已停用');
}

/**
 * 设置佣金比例
 */
function updateCommission($id) {
    validate_required(['rate']);
    
    $rate = (float)input('rate');
    
    if ($rate < 0 || $rate > 100) {
        Response::badRequest('佣金比例必须在0-100之间');
    }
    
    $shop = db()->fetch('SELECT id FROM qy_shop_list WHERE id = ?', [$id]);
    if (!$shop) {
        Response::notFound('商家不存在');
    }
    
    db()->update('qy_shop_list', ['base_commission_rate' => $rate], 'id = :id', ['id' => $id]);
    
    Response::success(null, '佣金比例已更新');
}
