<?php
/**
 * ================================================================
 * 文件名: detail.api.php
 * 创建时间: 2026-01-13
 * ================================================================
 *
 * 【文件职责】
 * 用户查看店铺详情 API
 *
 * ================================================================
 */

require_once dirname(dirname(dirname(__FILE__))) . '/bootstrap.include.php';

// 验证必填参数
validate_required(['id']);

$shopId = (int)input('id');

// 查询店铺
$shop = db()->fetch(
    "SELECT * FROM qy_shop_list WHERE id = ? AND base_status_active = 1",
    [$shopId]
);

if (!$shop) {
    Response::notFound('店铺不存在');
}

// 获取店铺技师
$technicians = db()->fetchAll(
    "SELECT * FROM qy_technician_list 
     WHERE base_shop_id = ? AND base_status_active = 1 AND base_status_verify = 1
     ORDER BY base_stat_rating_avg DESC
     LIMIT 10",
    [$shopId]
);

// 获取店铺服务（通过技师关联）
$services = db()->fetchAll(
    "SELECT DISTINCT si.* FROM qy_service_item_list si
     INNER JOIN qy_technician_service_list ts ON si.id = ts.base_service_id
     INNER JOIN qy_technician_list t ON ts.base_technician_id = t.id
     WHERE t.base_shop_id = ? AND si.base_status_active = 1
     ORDER BY si.base_sort_order",
    [$shopId]
);

// 获取店铺评价
$reviews = db()->fetchAll(
    "SELECT r.*, u.base_profile_nickname as user_nickname, u.base_profile_avatar as user_avatar
     FROM qy_review_list r
     LEFT JOIN qy_user_list u ON r.base_user_id = u.id
     WHERE r.base_shop_id = ? AND r.base_status_visible = 1
     ORDER BY r.base_time_created DESC
     LIMIT 5",
    [$shopId]
);

// 检查是否已收藏
$isFavorite = false;
$user = auth_user();
if ($user) {
    $favorite = db()->fetch(
        "SELECT id FROM qy_user_favorite_list 
         WHERE base_user_id = ? AND base_target_type = 'shop' AND base_target_id = ?",
        [$user['id'], $shopId]
    );
    $isFavorite = (bool)$favorite;
}

Response::success([
    'id' => $shop['id'],
    'name' => $shop['base_profile_name'],
    'logo' => $shop['base_profile_logo'],
    'intro' => $shop['base_profile_intro'],
    'address' => $shop['base_profile_address'],
    'phone' => $shop['base_auth_phone'],
    'stats' => [
        'technician_count' => (int)$shop['base_stat_technician_count'],
        'order_count' => (int)$shop['base_stat_order_count']
    ],
    'is_favorite' => $isFavorite,
    'technicians' => array_map(function($t) {
        return [
            'id' => $t['id'],
            'realname' => $t['base_profile_realname'],
            'avatar' => $t['base_profile_avatar'],
            'gender' => (int)$t['base_profile_gender'],
            'is_certified' => (bool)$t['base_is_certified'],
            'online_status' => (int)$t['base_status_online'],
            'rating_avg' => (float)$t['base_stat_rating_avg'],
            'order_count' => (int)$t['base_stat_order_count']
        ];
    }, $technicians),
    'services' => array_map(function($s) {
        return [
            'id' => $s['id'],
            'name' => $s['base_profile_name'],
            'desc' => $s['base_profile_desc'],
            'price_base' => (float)$s['base_price_base'],
            'duration' => (int)$s['base_duration_minutes']
        ];
    }, $services),
    'reviews' => array_map(function($r) {
        return [
            'id' => $r['id'],
            'user' => [
                'nickname' => $r['base_is_anonymous'] ? '匿名用户' : $r['user_nickname'],
                'avatar' => $r['base_is_anonymous'] ? '' : $r['user_avatar']
            ],
            'rating' => (int)$r['base_rating_overall'],
            'content' => $r['base_content'],
            'created_at' => $r['base_time_created']
        ];
    }, $reviews)
]);
