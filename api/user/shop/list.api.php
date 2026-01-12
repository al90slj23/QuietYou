<?php
/**
 * ================================================================
 * 文件名: list.api.php
 * 创建时间: 2026-01-13
 * ================================================================
 *
 * 【文件职责】
 * 用户附近店铺列表 API
 *
 * ================================================================
 */

require_once dirname(dirname(dirname(__FILE__))) . '/bootstrap.include.php';

// 获取参数
$page = max(1, (int)input('page', 1));
$pageSize = min(50, max(1, (int)input('page_size', 20)));
$lat = (float)input('lat', 0);
$lng = (float)input('lng', 0);
$keyword = input('keyword', '');
$offset = ($page - 1) * $pageSize;

// 构建查询条件
$where = "base_status_active = 1 AND base_status_verify = 1";
$params = [];

if ($keyword) {
    $where .= " AND (base_profile_name LIKE ? OR base_profile_address LIKE ?)";
    $params[] = "%$keyword%";
    $params[] = "%$keyword%";
}

// 查询总数
$total = db()->fetchColumn(
    "SELECT COUNT(*) FROM qy_shop_list WHERE $where",
    $params
);

// 查询列表
$params[] = $pageSize;
$params[] = $offset;
$list = db()->fetchAll(
    "SELECT * FROM qy_shop_list WHERE $where ORDER BY base_stat_order_count DESC LIMIT ? OFFSET ?",
    $params
);

$formattedList = array_map(function($item) {
    return [
        'id' => $item['id'],
        'name' => $item['base_profile_name'],
        'logo' => $item['base_profile_logo'],
        'intro' => $item['base_profile_intro'],
        'address' => $item['base_profile_address'],
        'phone' => $item['base_auth_phone'],
        'stats' => [
            'technician_count' => (int)$item['base_stat_technician_count'],
            'order_count' => (int)$item['base_stat_order_count']
        ]
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
