<?php
/**
 * ================================================================
 * 文件名: detail.api.php
 * 创建时间: 2026-01-13
 * ================================================================
 *
 * 【文件职责】
 * 商户订单详情 API
 *
 * ================================================================
 */

require_once dirname(dirname(dirname(__FILE__))) . '/bootstrap.include.php';

// 验证商户登录
$merchant = auth_merchant();

// 验证必填参数
validate_required(['id']);

$orderId = (int)input('id');

// 查询订单
$order = db()->fetch(
    "SELECT o.*, u.base_profile_nickname as user_nickname, u.base_profile_avatar as user_avatar,
            u.base_auth_phone as user_phone,
            t.base_profile_realname as tech_name, t.base_profile_avatar as tech_avatar,
            t.base_auth_phone as tech_phone
     FROM qy_order_list o
     LEFT JOIN qy_user_list u ON o.base_user_id = u.id
     LEFT JOIN qy_technician_list t ON o.base_technician_id = t.id
     WHERE o.id = ? AND o.base_shop_id = ?",
    [$orderId, $merchant['id']]
);

if (!$order) {
    Response::notFound('订单不存在');
}

// 查询评价
$review = db()->fetch(
    "SELECT * FROM qy_review_list WHERE base_order_id = ?",
    [$orderId]
);

$statusNames = [
    0 => '待支付', 1 => '待接单', 2 => '已接单', 3 => '出发中',
    4 => '服务中', 5 => '已完成', 6 => '已取消', 7 => '已退款'
];
$orderTypes = [1 => '上门服务', 2 => '到店服务', 3 => '借调服务'];

Response::success([
    'id' => $order['id'],
    'order_no' => $order['base_order_no'],
    'order_type' => (int)$order['base_order_type'],
    'order_type_name' => $orderTypes[$order['base_order_type']] ?? '',
    'service' => [
        'id' => $order['base_service_id'],
        'name' => $order['base_service_name']
    ],
    'user' => [
        'id' => $order['base_user_id'],
        'nickname' => $order['user_nickname'],
        'avatar' => $order['user_avatar'],
        'phone' => $order['user_phone']
    ],
    'technician' => $order['base_technician_id'] ? [
        'id' => $order['base_technician_id'],
        'name' => $order['tech_name'],
        'avatar' => $order['tech_avatar'],
        'phone' => $order['tech_phone']
    ] : null,
    'address' => [
        'contact' => $order['base_address_contact'],
        'phone' => $order['base_address_phone'],
        'detail' => $order['base_address_detail'],
        'lat' => (float)$order['base_address_lat'],
        'lng' => (float)$order['base_address_lng']
    ],
    'time' => [
        'scheduled' => $order['base_time_scheduled'],
        'started' => $order['base_time_started'],
        'completed' => $order['base_time_completed'],
        'created' => $order['base_time_created']
    ],
    'duration' => (int)$order['base_duration_minutes'],
    'price' => [
        'service' => (float)$order['base_price_service'],
        'tip' => (float)$order['base_price_tip'],
        'total' => (float)$order['base_price_total']
    ],
    'pay' => [
        'method' => $order['base_pay_method'],
        'time' => $order['base_pay_time'],
        'status' => (int)$order['base_status_pay']
    ],
    'status' => (int)$order['base_status_order'],
    'status_name' => $statusNames[$order['base_status_order']] ?? '',
    'remark' => $order['base_remark'],
    'cancel_reason' => $order['base_cancel_reason'],
    'review' => $review ? [
        'rating' => (int)$review['base_rating_overall'],
        'content' => $review['base_content']
    ] : null
]);
