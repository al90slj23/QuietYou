<?php
/**
 * 轻养到家 - 管理后台订单管理 API
 * ZERO 框架规范 - 冒号语法
 * 
 * GET  /api/admin/order              订单列表
 * GET  /api/admin/order:123          订单详情
 * POST /api/admin/order:123:refund   退款
 */

require_once dirname(dirname(dirname(__FILE__))) . '/bootstrap.include.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';
$id = $_GET['id'] ?? null;

// 需要管理员登录
$auth = auth_require(TokenType::ADMIN);

if ($id) {
    if ($action === 'refund' && $method === 'POST') {
        refundOrder($id);
    } elseif ($method === 'GET') {
        getOrderDetail($id);
    } else {
        Response::badRequest('请求方法不允许');
    }
} else {
    if ($method !== 'GET') Response::badRequest('请求方法不允许');
    getOrderList();
}

/**
 * 获取订单列表
 */
function getOrderList() {
    $keyword = input('keyword');
    $status = input('status');
    $dateFrom = input('date_from');
    $dateTo = input('date_to');
    $page = max(1, (int)input('page', 1));
    $pageSize = min(50, max(1, (int)input('page_size', 20)));
    
    $where = ['1=1'];
    $params = [];
    
    if ($keyword) {
        $where[] = '(o.base_order_no LIKE :keyword OR u.base_profile_nickname LIKE :keyword2 OR t.base_profile_realname LIKE :keyword3)';
        $params['keyword'] = "%$keyword%";
        $params['keyword2'] = "%$keyword%";
        $params['keyword3'] = "%$keyword%";
    }
    
    if ($status !== null && $status !== '') {
        $where[] = 'o.base_status_order = :status';
        $params['status'] = $status;
    }
    
    if ($dateFrom) {
        $where[] = 'DATE(o.base_time_created) >= :date_from';
        $params['date_from'] = $dateFrom;
    }
    
    if ($dateTo) {
        $where[] = 'DATE(o.base_time_created) <= :date_to';
        $params['date_to'] = $dateTo;
    }
    
    $whereClause = implode(' AND ', $where);
    
    $total = db()->fetch(
        "SELECT COUNT(*) as count FROM qy_order_list o
         LEFT JOIN qy_user_list u ON o.base_user_id = u.id
         LEFT JOIN qy_technician_list t ON o.base_technician_id = t.id
         WHERE $whereClause",
        $params
    )['count'];
    
    $offset = ($page - 1) * $pageSize;
    $orders = db()->fetchAll(
        "SELECT o.id, o.base_order_no as order_no, o.base_service_name as service_name,
                o.base_time_scheduled as scheduled_time, o.base_price_total as total_amount,
                o.base_status_order as status, o.base_time_created as created_at,
                u.base_profile_nickname as user_name,
                t.base_profile_realname as technician_name
         FROM qy_order_list o
         LEFT JOIN qy_user_list u ON o.base_user_id = u.id
         LEFT JOIN qy_technician_list t ON o.base_technician_id = t.id
         WHERE $whereClause
         ORDER BY o.base_time_created DESC
         LIMIT $offset, $pageSize",
        $params
    );
    
    Response::paginate($orders, $total, $page, $pageSize);
}

/**
 * 获取订单详情
 */
function getOrderDetail($id) {
    $order = db()->fetch(
        'SELECT o.*, u.base_profile_nickname as user_name, u.base_auth_phone as user_phone,
                t.base_profile_realname as technician_name, t.base_auth_phone as tech_phone,
                s.base_profile_name as shop_name
         FROM qy_order_list o
         LEFT JOIN qy_user_list u ON o.base_user_id = u.id
         LEFT JOIN qy_technician_list t ON o.base_technician_id = t.id
         LEFT JOIN qy_shop_list s ON o.base_shop_id = s.id
         WHERE o.id = ?',
        [$id]
    );
    
    if (!$order) {
        Response::notFound('订单不存在');
    }
    
    Response::success([
        'id' => $order['id'],
        'order_no' => $order['base_order_no'],
        'service_name' => $order['base_service_name'],
        'scheduled_time' => $order['base_time_scheduled'],
        'duration' => $order['base_duration_minutes'],
        'user' => [
            'id' => $order['base_user_id'],
            'name' => $order['user_name'],
            'phone' => $order['user_phone'] ? substr($order['user_phone'], 0, 3) . '****' . substr($order['user_phone'], -4) : ''
        ],
        'technician' => [
            'id' => $order['base_technician_id'],
            'name' => $order['technician_name'],
            'phone' => $order['tech_phone'] ? substr($order['tech_phone'], 0, 3) . '****' . substr($order['tech_phone'], -4) : ''
        ],
        'shop_name' => $order['shop_name'],
        'address' => [
            'contact' => $order['base_address_contact'],
            'phone' => $order['base_address_phone'],
            'detail' => $order['base_address_detail']
        ],
        'price' => [
            'service' => $order['base_price_service'],
            'tip' => $order['base_price_tip'],
            'total' => $order['base_price_total']
        ],
        'status' => $order['base_status_order'],
        'pay_status' => $order['base_status_pay'],
        'pay_method' => $order['base_pay_method'],
        'pay_time' => $order['base_pay_time'],
        'pay_transaction_id' => $order['base_pay_transaction_id'],
        'remark' => $order['base_remark'],
        'cancel_reason' => $order['base_cancel_reason'],
        'cancel_time' => $order['base_cancel_time'],
        'started_at' => $order['base_time_started'],
        'completed_at' => $order['base_time_completed'],
        'created_at' => $order['base_time_created']
    ]);
}

/**
 * 退款
 */
function refundOrder($id) {
    validate_required(['amount', 'reason']);
    
    $amount = (float)input('amount');
    $reason = input('reason');
    
    $order = db()->fetch('SELECT * FROM qy_order_list WHERE id = ?', [$id]);
    if (!$order) {
        Response::notFound('订单不存在');
    }
    
    if ($order['base_status_order'] == 7) {
        Response::badRequest('订单已退款');
    }
    
    if ($amount > $order['base_price_total']) {
        Response::badRequest('退款金额不能超过订单金额');
    }
    
    // 更新订单状态
    db()->update('qy_order_list', [
        'base_status_order' => 7, // 已退款
        'base_cancel_reason' => '管理员退款: ' . $reason,
        'base_cancel_time' => date('Y-m-d H:i:s')
    ], 'id = :id', ['id' => $id]);
    
    // TODO: 实际退款处理
    
    Response::success(null, '退款成功');
}
