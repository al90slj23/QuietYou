<?php
/**
 * ================================================================
 * 文件名: detail.api.php
 * 创建时间: 2026-01-13
 * ================================================================
 *
 * 【文件职责】
 * 技师端订单详情接口
 *
 * ================================================================
 */

require_once dirname(dirname(dirname(__FILE__))) . '/bootstrap.include.php';

$tech = auth_technician();

$orderId = input('id');
if (!$orderId) {
    Response::badRequest('订单ID不能为空');
}

$order = db()->fetch(
    "SELECT o.*, 
            u.base_profile_nickname as customer_name,
            u.base_profile_phone as customer_phone,
            u.base_profile_avatar as customer_avatar,
            s.base_profile_name as service_name,
            s.base_duration as service_duration,
            shop.base_profile_name as shop_name
     FROM qy_order_list o
     LEFT JOIN qy_user_list u ON o.base_user_id = u.id
     LEFT JOIN qy_service_list s ON o.base_service_id = s.id
     LEFT JOIN qy_shop_list shop ON o.base_shop_id = shop.id
     WHERE o.id = ? AND o.base_technician_id = ? AND o.is_deleted = 0",
    [$orderId, $tech['id']]
);

if (!$order) {
    Response::notFound('订单不存在');
}

Response::success([
    'id' => $order['id'],
    'order_no' => $order['base_order_no'],
    'service_name' => $order['service_name'],
    'service_duration' => intval($order['service_duration']),
    'customer' => [
        'name' => $order['customer_name'] ? mb_substr($order['customer_name'], 0, 1) . '**' : '顾客',
        'phone' => $order['customer_phone'] ? substr($order['customer_phone'], 0, 3) . '****' . substr($order['customer_phone'], -4) : '',
        'avatar' => $order['customer_avatar']
    ],
    'scheduled_time' => $order['base_scheduled_at'],
    'address' => $order['base_address'],
    'latitude' => floatval($order['base_latitude']),
    'longitude' => floatval($order['base_longitude']),
    'price' => floatval($order['base_price_total']),
    'tech_income' => floatval($order['base_price_tech']),
    'status' => $order['base_status'],
    'order_type' => $order['base_order_type'],
    'shop_name' => $order['shop_name'],
    'remark' => $order['base_remark'],
    'created_at' => $order['created_at'],
    'started_at' => $order['base_started_at'],
    'completed_at' => $order['base_completed_at']
]);
