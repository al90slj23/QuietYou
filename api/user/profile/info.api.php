<?php
/**
 * ================================================================
 * 文件名: info.api.php
 * 创建时间: 2026-01-13
 * ================================================================
 *
 * 【文件职责】
 * 获取用户个人资料 API
 *
 * ================================================================
 */

require_once dirname(dirname(dirname(__FILE__))) . '/bootstrap.include.php';

// 验证用户登录
$user = auth_customer();

// 获取订单统计
$orderStats = db()->fetch(
    "SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN base_status_order = 0 THEN 1 ELSE 0 END) as pending_pay,
        SUM(CASE WHEN base_status_order IN (1, 2, 3, 4) THEN 1 ELSE 0 END) as ongoing,
        SUM(CASE WHEN base_status_order = 5 THEN 1 ELSE 0 END) as completed
     FROM qy_order_list WHERE base_user_id = ?",
    [$user['id']]
);

// 获取收藏数
$favoriteCount = db()->fetchColumn(
    "SELECT COUNT(*) FROM qy_user_favorite_list WHERE base_user_id = ?",
    [$user['id']]
);

// 获取优惠券数
$couponCount = db()->fetchColumn(
    "SELECT COUNT(*) FROM qy_user_coupon_list WHERE base_user_id = ? AND base_status = 0",
    [$user['id']]
);

// 获取未读消息数
$unreadCount = db()->fetchColumn(
    "SELECT COUNT(*) FROM qy_message_list 
     WHERE base_receiver_type = 'user' AND base_receiver_id = ? AND base_is_read = 0",
    [$user['id']]
);

Response::success([
    'id' => $user['id'],
    'phone' => $user['base_auth_phone'],
    'nickname' => $user['base_profile_nickname'],
    'avatar' => $user['base_profile_avatar'],
    'gender' => (int)$user['base_profile_gender'],
    'stats' => [
        'order_total' => (int)$orderStats['total'],
        'order_pending_pay' => (int)$orderStats['pending_pay'],
        'order_ongoing' => (int)$orderStats['ongoing'],
        'order_completed' => (int)$orderStats['completed'],
        'favorite_count' => (int)$favoriteCount,
        'coupon_count' => (int)$couponCount,
        'unread_count' => (int)$unreadCount
    ]
]);
