<?php
/**
 * ================================================================
 * 文件名: info.api.php
 * 创建时间: 2026-01-13
 * ================================================================
 *
 * 【文件职责】
 * 获取商户信息 API
 *
 * ================================================================
 */

require_once dirname(dirname(dirname(__FILE__))) . '/bootstrap.include.php';

// 验证商户登录
$merchant = auth_merchant();

// 获取技师数量
$techCount = db()->fetchColumn(
    "SELECT COUNT(*) FROM qy_technician_list WHERE base_shop_id = ? AND base_status_active = 1",
    [$merchant['id']]
);

// 获取钱包信息
$wallet = db()->fetch(
    "SELECT * FROM qy_wallet_list WHERE base_owner_type = 'shop' AND base_owner_id = ?",
    [$merchant['id']]
);

Response::success([
    'id' => $merchant['id'],
    'phone' => $merchant['base_auth_phone'],
    'name' => $merchant['base_profile_name'],
    'logo' => $merchant['base_profile_logo'],
    'intro' => $merchant['base_profile_intro'],
    'address' => $merchant['base_profile_address'],
    'contact_name' => $merchant['base_contact_name'],
    'verify_status' => (int)$merchant['base_status_verify'],
    'commission_rate' => (float)$merchant['base_commission_rate'],
    'stats' => [
        'technician_count' => (int)$techCount,
        'order_count' => (int)$merchant['base_stat_order_count']
    ],
    'wallet' => $wallet ? [
        'available' => (float)$wallet['base_balance_available'],
        'pending' => (float)$wallet['base_balance_pending'],
        'total' => (float)$wallet['base_balance_total']
    ] : null
]);
