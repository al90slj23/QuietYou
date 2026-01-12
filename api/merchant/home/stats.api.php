<?php
/**
 * ================================================================
 * 文件名: stats.api.php
 * 创建时间: 2026-01-13
 * ================================================================
 *
 * 【文件职责】
 * 商户工作台统计数据 API
 *
 * ================================================================
 */

require_once dirname(dirname(dirname(__FILE__))) . '/bootstrap.include.php';

// 验证商户登录
$merchant = auth_merchant();

$today = date('Y-m-d');

// 今日订单数
$todayOrders = db()->fetchColumn(
    "SELECT COUNT(*) FROM qy_order_list 
     WHERE base_shop_id = ? AND DATE(base_time_created) = ?",
    [$merchant['id'], $today]
);

// 今日收入
$todayIncome = db()->fetchColumn(
    "SELECT COALESCE(SUM(base_price_total), 0) FROM qy_order_list 
     WHERE base_shop_id = ? AND DATE(base_time_created) = ? AND base_status_order = 5",
    [$merchant['id'], $today]
);

// 待处理订单
$pendingOrders = db()->fetchColumn(
    "SELECT COUNT(*) FROM qy_order_list 
     WHERE base_shop_id = ? AND base_status_order IN (1, 2)",
    [$merchant['id']]
);

// 进行中订单
$ongoingOrders = db()->fetchColumn(
    "SELECT COUNT(*) FROM qy_order_list 
     WHERE base_shop_id = ? AND base_status_order IN (3, 4)",
    [$merchant['id']]
);

// 本月订单数
$monthOrders = db()->fetchColumn(
    "SELECT COUNT(*) FROM qy_order_list 
     WHERE base_shop_id = ? AND DATE_FORMAT(base_time_created, '%Y-%m') = ?",
    [$merchant['id'], date('Y-m')]
);

// 本月收入
$monthIncome = db()->fetchColumn(
    "SELECT COALESCE(SUM(base_price_total), 0) FROM qy_order_list 
     WHERE base_shop_id = ? AND DATE_FORMAT(base_time_created, '%Y-%m') = ? AND base_status_order = 5",
    [$merchant['id'], date('Y-m')]
);

// 技师数量
$techCount = db()->fetchColumn(
    "SELECT COUNT(*) FROM qy_technician_list WHERE base_shop_id = ? AND base_status_active = 1",
    [$merchant['id']]
);

// 在线技师数
$onlineTechCount = db()->fetchColumn(
    "SELECT COUNT(*) FROM qy_technician_list 
     WHERE base_shop_id = ? AND base_status_active = 1 AND base_status_online = 1",
    [$merchant['id']]
);

// 获取钱包余额
$wallet = db()->fetch(
    "SELECT * FROM qy_wallet_list WHERE base_owner_type = 'shop' AND base_owner_id = ?",
    [$merchant['id']]
);

// 最近订单
$recentOrders = db()->fetchAll(
    "SELECT o.*, u.base_profile_nickname as user_nickname, t.base_profile_realname as tech_name
     FROM qy_order_list o
     LEFT JOIN qy_user_list u ON o.base_user_id = u.id
     LEFT JOIN qy_technician_list t ON o.base_technician_id = t.id
     WHERE o.base_shop_id = ?
     ORDER BY o.base_time_created DESC
     LIMIT 5",
    [$merchant['id']]
);

$statusNames = [
    0 => '待支付', 1 => '待接单', 2 => '已接单', 3 => '出发中',
    4 => '服务中', 5 => '已完成', 6 => '已取消', 7 => '已退款'
];

$formattedOrders = array_map(function($order) use ($statusNames) {
    return [
        'id' => $order['id'],
        'order_no' => $order['base_order_no'],
        'service_name' => $order['base_service_name'],
        'user_nickname' => $order['user_nickname'],
        'tech_name' => $order['tech_name'],
        'total' => (float)$order['base_price_total'],
        'status' => (int)$order['base_status_order'],
        'status_name' => $statusNames[$order['base_status_order']] ?? '',
        'created_at' => $order['base_time_created']
    ];
}, $recentOrders);

Response::success([
    'today' => [
        'orders' => (int)$todayOrders,
        'income' => (float)$todayIncome
    ],
    'month' => [
        'orders' => (int)$monthOrders,
        'income' => (float)$monthIncome
    ],
    'pending' => [
        'orders' => (int)$pendingOrders,
        'ongoing' => (int)$ongoingOrders
    ],
    'technician' => [
        'total' => (int)$techCount,
        'online' => (int)$onlineTechCount
    ],
    'wallet' => $wallet ? [
        'available' => (float)$wallet['base_balance_available'],
        'pending' => (float)$wallet['base_balance_pending']
    ] : ['available' => 0, 'pending' => 0],
    'recent_orders' => $formattedOrders
]);
