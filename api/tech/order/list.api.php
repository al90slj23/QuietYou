<?php
/**
 * ================================================================
 * 文件名: list.api.php
 * 创建时间: 2026-01-13
 * ================================================================
 *
 * 【文件职责】
 * 技师端订单列表接口
 *
 * ================================================================
 */

require_once dirname(dirname(dirname(__FILE__))) . '/bootstrap.include.php';

$tech = auth_technician();

$status = input('status', 'all');
$page = max(1, intval(input('page', 1)));
$pageSize = min(50, max(1, intval(input('page_size', 10))));
$offset = ($page - 1) * $pageSize;

// 构建查询条件
$where = "base_technician_id = ?";
$params = [$tech['id']];

$statusMap = [
    'pending' => ['pending'],
    'accepted' => ['accepted'],
    'serving' => ['serving'],
    'completed' => ['completed', 'reviewed']
];

if ($status !== 'all' && isset($statusMap[$status])) {
    $placeholders = implode(',', array_fill(0, count($statusMap[$status]), '?'));
    $where .= " AND base_status IN ($placeholders)";
    $params = array_merge($params, $statusMap[$status]);
}

// 查询总数
$total = db()->fetch(
    "SELECT COUNT(*) as count FROM qy_order_list WHERE $where AND is_deleted = 0",
    $params
)['count'];

// 查询列表
$orders = db()->fetchAll(
    "SELECT o.*, 
            u.base_profile_nickname as customer_name,
            u.base_profile_phone as customer_phone,
            s.base_profile_name as service_name
     FROM qy_order_list o
     LEFT JOIN qy_user_list u ON o.base_user_id = u.id
     LEFT JOIN qy_service_list s ON o.base_service_id = s.id
     WHERE $where AND o.is_deleted = 0
     ORDER BY o.created_at DESC
     LIMIT $pageSize OFFSET $offset",
    $params
);

$list = array_map(function($order) {
    return [
        'id' => $order['id'],
        'order_no' => $order['base_order_no'],
        'service_name' => $order['service_name'],
        'customer_name' => $order['customer_name'] ? mb_substr($order['customer_name'], 0, 1) . '**' : '顾客',
        'scheduled_time' => $order['base_scheduled_at'],
        'address' => $order['base_address'],
        'price' => floatval($order['base_price_total']),
        'status' => $order['base_status'],
        'order_type' => $order['base_order_type'],
        'created_at' => $order['created_at']
    ];
}, $orders);

Response::paginate($list, $total, $page, $pageSize);
