<?php
/**
 * ================================================================
 * 文件名: info.api.php
 * 创建时间: 2026-01-13
 * ================================================================
 *
 * 【文件职责】
 * 获取技师个人资料 API
 *
 * ================================================================
 */

require_once dirname(dirname(dirname(__FILE__))) . '/bootstrap.include.php';

// 验证技师登录
$tech = auth_technician();

// 获取归属店铺信息
$shop = null;
if ($tech['base_shop_id']) {
    $shop = db()->fetch(
        "SELECT id, base_profile_name as name, base_profile_logo as logo 
         FROM qy_shop_list WHERE id = ?",
        [$tech['base_shop_id']]
    );
}

// 获取钱包信息
$wallet = db()->fetch(
    "SELECT * FROM qy_wallet_list WHERE base_owner_type = 'technician' AND base_owner_id = ?",
    [$tech['id']]
);

Response::success([
    'id' => $tech['id'],
    'phone' => $tech['base_auth_phone'],
    'type' => (int)$tech['base_type'],
    'realname' => $tech['base_profile_realname'],
    'avatar' => $tech['base_profile_avatar'],
    'gender' => (int)$tech['base_profile_gender'],
    'intro' => $tech['base_profile_intro'],
    'photos' => $tech['base_profile_photos'] ? json_decode($tech['base_profile_photos'], true) : [],
    'experience_years' => (int)$tech['base_profile_experience_years'],
    'is_certified' => (bool)$tech['base_is_certified'],
    'verify_status' => (int)$tech['base_status_verify'],
    'online_status' => (int)$tech['base_status_online'],
    'stats' => [
        'order_count' => (int)$tech['base_stat_order_count'],
        'rating_avg' => (float)$tech['base_stat_rating_avg'],
        'rating_count' => (int)$tech['base_stat_rating_count'],
        'repeat_rate' => (float)$tech['base_stat_repeat_rate']
    ],
    'shop' => $shop,
    'wallet' => $wallet ? [
        'available' => (float)$wallet['base_balance_available'],
        'pending' => (float)$wallet['base_balance_pending'],
        'total' => (float)$wallet['base_balance_total']
    ] : null
]);
