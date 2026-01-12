<?php
/**
 * 轻养到家 - 管理后台用户管理 API
 * ZERO 框架规范 - 冒号语法
 * 
 * GET /api/admin/user              用户列表
 * GET /api/admin/user:123          用户详情
 * PUT /api/admin/user:123:status   更新状态
 */

require_once dirname(dirname(dirname(__FILE__))) . '/bootstrap.include.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';
$id = $_GET['id'] ?? null;

// 需要管理员登录
$auth = auth_require(TokenType::ADMIN);

if ($id) {
    if ($action === 'status' && $method === 'PUT') {
        updateUserStatus($id);
    } elseif ($method === 'GET') {
        getUserDetail($id);
    } else {
        Response::badRequest('请求方法不允许');
    }
} else {
    if ($method !== 'GET') Response::badRequest('请求方法不允许');
    getUserList();
}

/**
 * 获取用户列表
 */
function getUserList() {
    $keyword = input('keyword');
    $status = input('status');
    $page = max(1, (int)input('page', 1));
    $pageSize = min(50, max(1, (int)input('page_size', 20)));
    
    $where = ['1=1'];
    $params = [];
    
    if ($keyword) {
        $where[] = '(id LIKE :keyword OR base_auth_phone LIKE :keyword2 OR base_profile_nickname LIKE :keyword3)';
        $params['keyword'] = "%$keyword%";
        $params['keyword2'] = "%$keyword%";
        $params['keyword3'] = "%$keyword%";
    }
    
    if ($status !== null && $status !== '') {
        $where[] = 'base_status_active = :status';
        $params['status'] = $status;
    }
    
    $whereClause = implode(' AND ', $where);
    
    $total = db()->fetch(
        "SELECT COUNT(*) as count FROM qy_user_list WHERE $whereClause",
        $params
    )['count'];
    
    $offset = ($page - 1) * $pageSize;
    $users = db()->fetchAll(
        "SELECT id, base_auth_phone as phone, base_profile_nickname as nickname,
                base_profile_avatar as avatar, base_profile_gender as gender,
                base_status_active as status, base_time_created as created_at
         FROM qy_user_list
         WHERE $whereClause
         ORDER BY id DESC
         LIMIT $offset, $pageSize",
        $params
    );
    
    // 获取订单统计
    foreach ($users as &$user) {
        $stats = db()->fetch(
            'SELECT COUNT(*) as order_count, COALESCE(SUM(base_price_total), 0) as total_amount
             FROM qy_order_list WHERE base_user_id = ? AND base_status_order = 5',
            [$user['id']]
        );
        $user['order_count'] = (int)$stats['order_count'];
        $user['total_amount'] = round($stats['total_amount'], 2);
        $user['phone'] = substr($user['phone'], 0, 3) . '****' . substr($user['phone'], -4);
    }
    
    Response::paginate($users, $total, $page, $pageSize);
}

/**
 * 获取用户详情
 */
function getUserDetail($id) {
    $user = db()->fetch(
        'SELECT * FROM qy_user_list WHERE id = ?',
        [$id]
    );
    
    if (!$user) {
        Response::notFound('用户不存在');
    }
    
    // 订单统计
    $stats = db()->fetch(
        'SELECT COUNT(*) as order_count, COALESCE(SUM(base_price_total), 0) as total_amount
         FROM qy_order_list WHERE base_user_id = ? AND base_status_order = 5',
        [$id]
    );
    
    // 最近订单
    $recentOrders = db()->fetchAll(
        'SELECT id, base_order_no as order_no, base_service_name as service_name,
                base_price_total as amount, base_status_order as status, base_time_created as created_at
         FROM qy_order_list WHERE base_user_id = ?
         ORDER BY base_time_created DESC LIMIT 5',
        [$id]
    );
    
    Response::success([
        'id' => $user['id'],
        'phone' => substr($user['base_auth_phone'], 0, 3) . '****' . substr($user['base_auth_phone'], -4),
        'nickname' => $user['base_profile_nickname'],
        'avatar' => $user['base_profile_avatar'],
        'gender' => $user['base_profile_gender'],
        'status' => $user['base_status_active'],
        'created_at' => $user['base_time_created'],
        'stats' => [
            'order_count' => (int)$stats['order_count'],
            'total_amount' => round($stats['total_amount'], 2)
        ],
        'recent_orders' => $recentOrders
    ]);
}

/**
 * 更新用户状态
 */
function updateUserStatus($id) {
    validate_required(['status']);
    
    $status = (int)input('status');
    
    $user = db()->fetch('SELECT id FROM qy_user_list WHERE id = ?', [$id]);
    if (!$user) {
        Response::notFound('用户不存在');
    }
    
    db()->update('qy_user_list', [
        'base_status_active' => $status,
        'base_time_updated' => date('Y-m-d H:i:s')
    ], 'id = :id', ['id' => $id]);
    
    Response::success(null, $status ? '用户已启用' : '用户已禁用');
}
