<?php
/**
 * ================================================================
 * 文件名: stats.api.php
 * 创建时间: 2026-01-13
 * ================================================================
 *
 * 【文件职责】
 * 技师工作台统计数据 API
 *
 * ================================================================
 */

require_once dirname(dirname(dirname(__FILE__))) . '/bootstrap.include.php';

// 验证技师登录
$tech = auth_technician();

$today = date('Y-m-d');

// 今日订单数
$todayOrders = db()->fetchColumn(
    "SELECT COUNT(*) FROM qy_order_list 
     WHERE base_technician_id = ? AND DATE(base_time_created) = ?",
    [$tech['id'], $today]
);

// 今日收入
$todayIncome = db()->fetchColumn(
    "SELECT COALESCE(SUM(base_price_total), 0) FROM qy_order_list 
     WHERE base_technician_id = ? AND DATE(base_time_created) = ? AND base_status_order = 5",
    [$tech['id'], $today]
);

// 待接单数
$pendingOrders = db()->fetchColumn(
    "SELECT COUNT(*) FROM qy_order_list 
     WHERE base_technician_id = ? AND base_status_order = 1",
    [$tech['id']]
);

// 进行中订单数
$ongoingOrders = db()->fetchColumn(
    "SELECT COUNT(*) FROM qy_order_list 
     WHERE base_technician_id = ? AND base_status_order IN (2, 3, 4)",
    [$tech['id']]
);

// 本月订单数
$monthOrders = db()->fetchColumn(
    "SELECT COUNT(*) FROM qy_order_list 
     WHERE base_technician_id = ? AND DATE_FORMAT(base_time_created, '%Y-%m') = ?",
    [$tech['id'], date('Y-m')]
);

// 本月收入
$monthIncome = db()->fetchColumn(
    "SELECT COALESCE(SUM(base_price_total), 0) FROM qy_order_list 
     WHERE base_technician_id = ? AND DATE_FORMAT(base_time_created, '%Y-%m') = ? AND base_status_order = 5",
    [$tech['id'], date('Y-m')]
);

// 获取钱包余额
$wallet = db()->fetch(
    "SELECT * FROM qy_wallet_list WHERE base_owner_type = 'technician' AND base_owner_id = ?",
    [$tech['id']]
);

// 最近待处理订单
$recentOrders = db()->fetchAll(
    "SELECT o.*, u.base_profile_nickname as user_nickname
     FROM qy_order_list o
     LEFT JOIN qy_user_list u ON o.base_user_id = u.id
     WHERE o.base_technician_id = ? AND o.base_status_order IN (1, 2, 3, 4)
     ORDER BY o.base_time_scheduled ASC
     LIMIT 5",
    [$tech['id']]
);

$formattedOrders = array_map(function($order) {
    $statusNames = [1 => '待接单', 2 => '已接单', 3 => '出发中', 4 => '服务中'];
    return [
        'id' => $order['id'],
        'order_no' => $order['base_order_no'],
        'service_name' => $order['base_service_name'],
        'user_nickname' => $order['user_nickname'],
        'scheduled_time' => $order['base_time_scheduled'],
        'status' => (int)$order['base_status_order'],
        'status_name' => $statusNames[$order['base_status_order']] ?? ''
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
    'wallet' => $wallet ? [
        'available' => (float)$wallet['base_balance_available'],
        'pending' => (float)$wallet['base_balance_pending']
    ] : ['available' => 0, 'pending' => 0],
    'recent_orders' => $formattedOrders,
    'online_status' => (int)$tech['base_status_online'],
    'accept_order' => (bool)$tech['base_setting_accept_order']
]);
