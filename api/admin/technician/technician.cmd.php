<?php
/**
 * 轻养到家 - 管理后台技师管理 API
 * ZERO 框架规范 - 冒号语法
 * 
 * GET /api/admin/technician           技师列表
 * GET /api/admin/technician:pending   待审核列表
 * GET /api/admin/technician:123       技师详情
 * PUT /api/admin/technician:123:verify 审核
 * PUT /api/admin/technician:123:status 更新状态
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
        verifyTechnician($id);
    } elseif ($action === 'status' && $method === 'PUT') {
        updateTechnicianStatus($id);
    } elseif ($method === 'GET') {
        getTechnicianDetail($id);
    } else {
        Response::badRequest('请求方法不允许');
    }
} else {
    if ($method !== 'GET') Response::badRequest('请求方法不允许');
    getTechnicianList();
}

/**
 * 获取技师列表
 */
function getTechnicianList() {
    $keyword = input('keyword');
    $verifyStatus = input('verify_status');
    $onlineStatus = input('online_status');
    $page = max(1, (int)input('page', 1));
    $pageSize = min(50, max(1, (int)input('page_size', 20)));
    
    $where = ['1=1'];
    $params = [];
    
    if ($keyword) {
        $where[] = '(t.id LIKE :keyword OR t.base_auth_phone LIKE :keyword2 OR t.base_profile_realname LIKE :keyword3)';
        $params['keyword'] = "%$keyword%";
        $params['keyword2'] = "%$keyword%";
        $params['keyword3'] = "%$keyword%";
    }
    
    if ($verifyStatus !== null && $verifyStatus !== '') {
        $where[] = 't.base_status_verify = :verify';
        $params['verify'] = $verifyStatus;
    }
    
    if ($onlineStatus !== null && $onlineStatus !== '') {
        $where[] = 't.base_status_online = :online';
        $params['online'] = $onlineStatus;
    }
    
    $whereClause = implode(' AND ', $where);
    
    $total = db()->fetch(
        "SELECT COUNT(*) as count FROM qy_technician_list t WHERE $whereClause",
        $params
    )['count'];
    
    $offset = ($page - 1) * $pageSize;
    $technicians = db()->fetchAll(
        "SELECT t.id, t.base_auth_phone as phone, t.base_profile_realname as name,
                t.base_profile_avatar as avatar, t.base_profile_gender as gender,
                t.base_stat_rating_avg as rating, t.base_stat_order_count as order_count,
                t.base_status_verify as verify_status, t.base_status_online as online_status,
                t.base_status_active as status, t.base_time_created as created_at,
                s.base_profile_name as shop_name
         FROM qy_technician_list t
         LEFT JOIN qy_shop_list s ON t.base_shop_id = s.id
         WHERE $whereClause
         ORDER BY t.id DESC
         LIMIT $offset, $pageSize",
        $params
    );
    
    foreach ($technicians as &$tech) {
        $tech['phone'] = substr($tech['phone'], 0, 3) . '****' . substr($tech['phone'], -4);
        $tech['rating'] = round($tech['rating'], 2);
    }
    
    Response::paginate($technicians, $total, $page, $pageSize);
}

/**
 * 获取待审核列表
 */
function getPendingList() {
    $technicians = db()->fetchAll(
        'SELECT id, base_auth_phone as phone, base_profile_realname as name,
                base_profile_gender as gender, base_time_created as created_at
         FROM qy_technician_list
         WHERE base_status_verify = 0
         ORDER BY base_time_created ASC'
    );
    
    foreach ($technicians as &$tech) {
        $tech['phone'] = substr($tech['phone'], 0, 3) . '****' . substr($tech['phone'], -4);
    }
    
    Response::success($technicians);
}

/**
 * 获取技师详情
 */
function getTechnicianDetail($id) {
    $tech = db()->fetch(
        'SELECT t.*, s.base_profile_name as shop_name
         FROM qy_technician_list t
         LEFT JOIN qy_shop_list s ON t.base_shop_id = s.id
         WHERE t.id = ?',
        [$id]
    );
    
    if (!$tech) {
        Response::notFound('技师不存在');
    }
    
    Response::success([
        'id' => $tech['id'],
        'phone' => substr($tech['base_auth_phone'], 0, 3) . '****' . substr($tech['base_auth_phone'], -4),
        'name' => $tech['base_profile_realname'],
        'avatar' => $tech['base_profile_avatar'],
        'gender' => $tech['base_profile_gender'],
        'intro' => $tech['base_profile_intro'],
        'photos' => json_decode($tech['base_profile_photos'], true) ?: [],
        'certifications' => [
            'idcard_front' => $tech['base_cert_idcard_front'],
            'idcard_back' => $tech['base_cert_idcard_back'],
            'health' => $tech['base_cert_health'],
            'professional' => $tech['base_cert_professional']
        ],
        'shop_id' => $tech['base_shop_id'],
        'shop_name' => $tech['shop_name'],
        'rating' => round($tech['base_stat_rating_avg'], 2),
        'order_count' => $tech['base_stat_order_count'],
        'rating_count' => $tech['base_stat_rating_count'],
        'verify_status' => $tech['base_status_verify'],
        'online_status' => $tech['base_status_online'],
        'status' => $tech['base_status_active'],
        'created_at' => $tech['base_time_created']
    ]);
}

/**
 * 审核技师
 */
function verifyTechnician($id) {
    validate_required(['action']); // approve, reject
    
    $action = input('action');
    $reason = input('reason', '');
    
    $tech = db()->fetch('SELECT * FROM qy_technician_list WHERE id = ?', [$id]);
    if (!$tech) {
        Response::notFound('技师不存在');
    }
    
    if ($tech['base_status_verify'] != 0) {
        Response::badRequest('该技师已审核');
    }
    
    if ($action === 'approve') {
        db()->update('qy_technician_list', [
            'base_status_verify' => 1
        ], 'id = :id', ['id' => $id]);
        Response::success(null, '审核通过');
    } elseif ($action === 'reject') {
        db()->update('qy_technician_list', [
            'base_status_verify' => 2
        ], 'id = :id', ['id' => $id]);
        Response::success(null, '已拒绝');
    } else {
        Response::badRequest('无效的操作');
    }
}

/**
 * 更新技师状态
 */
function updateTechnicianStatus($id) {
    validate_required(['status']);
    
    $status = (int)input('status');
    
    $tech = db()->fetch('SELECT id FROM qy_technician_list WHERE id = ?', [$id]);
    if (!$tech) {
        Response::notFound('技师不存在');
    }
    
    db()->update('qy_technician_list', [
        'base_status_active' => $status,
        'base_status_online' => $status ? 0 : 0 // 禁用时强制下线
    ], 'id = :id', ['id' => $id]);
    
    Response::success(null, $status ? '技师已启用' : '技师已停用');
}
