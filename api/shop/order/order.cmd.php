<?php
/**
 * 轻养到家 - 商家端订单 API
 * ZERO 框架规范 - 冒号语法
 * 
 * GET /api/shop/order              订单列表
 * GET /api/shop/order:statistics   订单统计
 * GET /api/shop/order:123          订单详情
 */

require_once dirname(dirname(dirname(__FILE__))) . '/bootstrap.inc.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';
$id = $_GET['id'] ?? null;

if ($method !== 'GET') {
    Response::badRequest('请求方法不允许');
}

// 需要登录
$auth = auth_require(TokenType::SHOP);

if ($action === 'statistics') {
    getStatistics($auth);
} elseif ($id) {
    getOrderDetail($auth, $id);
} else {
    getOrderList($auth);
}

/**
 * 获取订单列表
 */
function getOrderList($auth) {
    $technicianId = input('technician_id');
    $status = input('status');
    $dateFrom = input('date_from');
    $dateTo = input('date_to');
    $page = max(1, (int)input('page', 1));
    $pageSize = min(50, max(1, (int)input('page_size', 20)));
    
    $where = ['o.base_shop_id = :shop_id'];
    $params = ['shop_id' => $auth['id']];
    
    if ($technicianId) {
        $where[] = 'o.base_technician_id = :tech_id';
        $params['tech_id'] = $technicianId;
    }
    
    if ($status !== null && $status !== '') {
        $where[] = 'o.base_status_order = :status';
        $params['status'] = $status;
    }
    
    if ($dateFrom) {
        $where[] = 'DATE(o.base_time_scheduled) >= :date_from';
        $params['date_from'] = $dateFrom;
    }
    
    if ($dateTo) {
        $where[] = 'DATE(o.base_time_scheduled) <= :date_to';
        $params['date_to'] = $dateTo;
    }
    
    $whereClause = implode(' AND ', $where);
    
    $total = db()->fetch(
        "SELECT COUNT(*) as count FROM qy_order_list o WHERE $whereClause",
        $params
    )['count'];
    
    $offset = ($page - 1) * $pageSize;
    $orders = db()->fetchAll(
        "SELECT o.id, o.base_order_no as order_no, o.base_service_name as service_name,
                o.base_time_scheduled as scheduled_time, o.base_price_total as total_amount,
                o.base_status_order as status, o.base_time_created as created_at,
                t.base_profile_realname as technician_name,
                u.base_profile_nickname as user_name
         FROM qy_order_list o
         LEFT JOIN qy_technician_list t ON o.base_technician_id = t.id
         LEFT JOIN qy_user_list u ON o.base_user_id = u.id
         WHERE $whereClause
         ORDER BY o.base_time_scheduled DESC
         LIMIT $offset, $pageSize",
        $params
    );
    
    Response::paginate($orders, $total, $page, $pageSize);
}

/**
 * 获取订单详情
 */
function getOrderDetail($auth, $id) {
    $order = db()->fetch(
        'SELECT o.*, t.base_profile_realname as technician_name,
                u.base_profile_nickname as user_name
         FROM qy_order_list o
         LEFT JOIN qy_technician_list t ON o.base_technician_id = t.id
         LEFT JOIN qy_user_list u ON o.base_user_id = u.id
         WHERE o.id = ? AND o.base_shop_id = ?',
        [$id, $auth['id']]
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
        'user_name' => $order['user_name'],
        'technician_name' => $order['technician_name'],
        'address' => [
            'contact' => $order['base_address_contact'],
            'phone' => substr($order['base_address_phone'], 0, 3) . '****' . substr($order['base_address_phone'], -4),
            'detail' => $order['base_address_detail']
        ],
        'price' => [
            'service' => $order['base_price_service'],
            'tip' => $order['base_price_tip'],
            'total' => $order['base_price_total']
        ],
        'status' => $order['base_status_order'],
        'remark' => $order['base_remark'],
        'started_at' => $order['base_time_started'],
        'completed_at' => $order['base_time_completed'],
        'created_at' => $order['base_time_created']
    ]);
}

/**
 * 获取订单统计
 */
function getStatistics($auth) {
    $period = input('period', 'today'); // today, week, month
    
    switch ($period) {
        case 'week':
            $startDate = date('Y-m-d', strtotime('-7 days'));
            break;
        case 'month':
            $startDate = date('Y-m-01');
            break;
        case 'today':
        default:
            $startDate = date('Y-m-d');
    }
    
    // 订单统计
    $stats = db()->fetch(
        'SELECT 
            COUNT(*) as total_orders,
            SUM(CASE WHEN base_status_order = 5 THEN 1 ELSE 0 END) as completed_orders,
            SUM(CASE WHEN base_status_order IN (1,2,3,4) THEN 1 ELSE 0 END) as active_orders,
            SUM(CASE WHEN base_status_order = 6 THEN 1 ELSE 0 END) as cancelled_orders,
            COALESCE(SUM(CASE WHEN base_status_order = 5 THEN base_price_total ELSE 0 END), 0) as total_revenue
         FROM qy_order_list
         WHERE base_shop_id = ? AND DATE(base_time_created) >= ?',
        [$auth['id'], $startDate]
    );
    
    // 技师在线数
    $onlineTechs = db()->fetch(
        'SELECT COUNT(*) as count FROM qy_technician_list 
         WHERE base_shop_id = ? AND base_status_online = 1',
        [$auth['id']]
    )['count'];
    
    // 计算商家分成
    $platformRate = 0.15;
    $shopRate = 0.30;
    $shopIncome = round($stats['total_revenue'] * $shopRate, 2);
    
    Response::success([
        'period' => $period,
        'orders' => [
            'total' => (int)$stats['total_orders'],
            'completed' => (int)$stats['completed_orders'],
            'active' => (int)$stats['active_orders'],
            'cancelled' => (int)$stats['cancelled_orders']
        ],
        'revenue' => [
            'total' => round($stats['total_revenue'], 2),
            'shop_income' => $shopIncome
        ],
        'online_technicians' => (int)$onlineTechs
    ]);
}
