<?php
/**
 * ================================================================
 * 文件名: pool.api.php
 * 创建时间: 2026-01-13
 * ================================================================
 *
 * 【文件职责】
 * 技师池列表（可借调技师）API
 *
 * ================================================================
 */

require_once dirname(dirname(dirname(__FILE__))) . '/bootstrap.include.php';

// 验证商户登录
$merchant = auth_merchant();

// 获取参数
$page = max(1, (int)input('page', 1));
$pageSize = min(50, max(1, (int)input('page_size', 20)));
$gender = input('gender', '');
$certified = input('certified', '');
$offset = ($page - 1) * $pageSize;

// 构建查询条件 - 排除本店技师
$where = "t.base_shop_id != ? AND t.base_status_active = 1 AND t.base_status_verify = 1";
$params = [$merchant['id']];

if ($gender !== '') {
    $where .= " AND t.base_profile_gender = ?";
    $params[] = (int)$gender;
}

if ($certified !== '') {
    $where .= " AND t.base_is_certified = ?";
    $params[] = (int)$certified;
}

// 查询总数
$total = db()->fetchColumn(
    "SELECT COUNT(*) FROM qy_technician_list t WHERE $where",
    $params
);

// 查询列表
$params[] = $pageSize;
$params[] = $offset;
$list = db()->fetchAll(
    "SELECT t.*, s.base_profile_name as shop_name, s.base_profile_logo as shop_logo
     FROM qy_technician_list t
     LEFT JOIN qy_shop_list s ON t.base_shop_id = s.id
     WHERE $where
     ORDER BY t.base_stat_rating_avg DESC, t.base_stat_order_count DESC
     LIMIT ? OFFSET ?",
    $params
);

$formattedList = array_map(function($item) {
    return [
        'id' => $item['id'],
        'realname' => $item['base_profile_realname'],
        'avatar' => $item['base_profile_avatar'],
        'gender' => (int)$item['base_profile_gender'],
        'intro' => $item['base_profile_intro'],
        'experience_years' => (int)$item['base_profile_experience_years'],
        'is_certified' => (bool)$item['base_is_certified'],
        'online_status' => (int)$item['base_status_online'],
        'stats' => [
            'order_count' => (int)$item['base_stat_order_count'],
            'rating_avg' => (float)$item['base_stat_rating_avg'],
            'repeat_rate' => (float)$item['base_stat_repeat_rate']
        ],
        'shop' => $item['base_shop_id'] ? [
            'id' => $item['base_shop_id'],
            'name' => $item['shop_name'],
            'logo' => $item['shop_logo']
        ] : null
    ];
}, $list);

Response::success([
    'list' => $formattedList,
    'pagination' => [
        'total' => (int)$total,
        'page' => $page,
        'page_size' => $pageSize,
        'total_pages' => ceil($total / $pageSize)
    ]
]);
