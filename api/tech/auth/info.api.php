<?php
/**
 * ================================================================
 * 文件名: info.api.php
 * 创建时间: 2026-01-13
 * ================================================================
 *
 * 【文件职责】
 * 获取当前登录技师信息
 *
 * ================================================================
 */

require_once dirname(dirname(dirname(__FILE__))) . '/bootstrap.include.php';

// 验证技师登录
$tech = auth_technician();

// 获取技师详细信息
$techInfo = db()->fetch(
    "SELECT t.*, s.base_profile_name as shop_name 
     FROM qy_technician_list t 
     LEFT JOIN qy_shop_list s ON t.base_shop_id = s.id 
     WHERE t.id = ?",
    [$tech['id']]
);

// 获取今日统计
$today = date('Y-m-d');
$todayStats = db()->fetch(
    "SELECT 
        COUNT(*) as order_count,
        COALESCE(SUM(base_price_total), 0) as income,
        COALESCE(SUM(base_service_duration), 0) as service_minutes
     FROM qy_order_list 
     WHERE base_technician_id = ? 
       AND DATE(created_at) = ? 
       AND base_status IN ('completed', 'reviewed')",
    [$tech['id'], $today]
);

// 获取待处理订单数
$pendingCount = db()->fetch(
    "SELECT COUNT(*) as count FROM qy_order_list 
     WHERE base_technician_id = ? AND base_status IN ('pending', 'accepted')",
    [$tech['id']]
)['count'];

Response::success([
    'id' => $techInfo['id'],
    'name' => $techInfo['base_profile_name'],
    'phone' => substr($techInfo['base_profile_phone'], 0, 3) . '****' . substr($techInfo['base_profile_phone'], -4),
    'avatar' => $techInfo['base_profile_avatar'],
    'type' => $techInfo['base_type'],
    'is_certified' => (bool)$techInfo['base_is_certified'],
    'status' => $techInfo['base_status'],
    'shop_id' => $techInfo['base_shop_id'],
    'shop_name' => $techInfo['shop_name'],
    'rating' => floatval($techInfo['base_stat_rating']),
    'order_count' => intval($techInfo['base_stat_order_count']),
    'repeat_rate' => floatval($techInfo['base_stat_repeat_rate']),
    'today_stats' => [
        'order_count' => intval($todayStats['order_count']),
        'income' => floatval($todayStats['income']),
        'service_hours' => round($todayStats['service_minutes'] / 60, 1)
    ],
    'pending_count' => intval($pendingCount)
]);
