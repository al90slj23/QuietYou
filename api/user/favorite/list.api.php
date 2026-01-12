<?php
/**
 * ================================================================
 * 文件名: list.api.php
 * 创建时间: 2026-01-13
 * ================================================================
 *
 * 【文件职责】
 * 用户收藏列表 API
 *
 * ================================================================
 */

require_once dirname(dirname(dirname(__FILE__))) . '/bootstrap.include.php';

// 验证用户登录
$user = auth_customer();

// 获取参数
$type = input('type', 'technician'); // technician, shop
$page = max(1, (int)input('page', 1));
$pageSize = min(50, max(1, (int)input('page_size', 20)));
$offset = ($page - 1) * $pageSize;

// 查询总数
$total = db()->fetchColumn(
    "SELECT COUNT(*) FROM qy_user_favorite_list WHERE base_user_id = ? AND base_target_type = ?",
    [$user['id'], $type]
);

// 查询收藏列表
$favorites = db()->fetchAll(
    "SELECT * FROM qy_user_favorite_list 
     WHERE base_user_id = ? AND base_target_type = ?
     ORDER BY base_time_created DESC
     LIMIT ? OFFSET ?",
    [$user['id'], $type, $pageSize, $offset]
);

$targetIds = array_column($favorites, 'base_target_id');
$list = [];

if ($targetIds) {
    $placeholders = implode(',', array_fill(0, count($targetIds), '?'));
    
    if ($type === 'technician') {
        $items = db()->fetchAll(
            "SELECT * FROM qy_technician_list WHERE id IN ($placeholders)",
            $targetIds
        );
        $list = array_map(function($t) {
            return [
                'id' => $t['id'],
                'type' => 'technician',
                'realname' => $t['base_profile_realname'],
                'avatar' => $t['base_profile_avatar'],
                'gender' => (int)$t['base_profile_gender'],
                'is_certified' => (bool)$t['base_is_certified'],
                'online_status' => (int)$t['base_status_online'],
                'rating_avg' => (float)$t['base_stat_rating_avg'],
                'order_count' => (int)$t['base_stat_order_count']
            ];
        }, $items);
    } else {
        $items = db()->fetchAll(
            "SELECT * FROM qy_shop_list WHERE id IN ($placeholders)",
            $targetIds
        );
        $list = array_map(function($s) {
            return [
                'id' => $s['id'],
                'type' => 'shop',
                'name' => $s['base_profile_name'],
                'logo' => $s['base_profile_logo'],
                'address' => $s['base_profile_address'],
                'technician_count' => (int)$s['base_stat_technician_count'],
                'order_count' => (int)$s['base_stat_order_count']
            ];
        }, $items);
    }
}

Response::success([
    'list' => $list,
    'pagination' => [
        'total' => (int)$total,
        'page' => $page,
        'page_size' => $pageSize,
        'total_pages' => ceil($total / $pageSize)
    ]
]);
