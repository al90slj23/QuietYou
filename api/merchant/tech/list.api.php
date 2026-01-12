<?php
/**
 * ================================================================
 * 文件名: list.api.php
 * 创建时间: 2026-01-13
 * ================================================================
 *
 * 【文件职责】
 * 商户技师列表 API
 *
 * ================================================================
 */

require_once dirname(dirname(dirname(__FILE__))) . '/bootstrap.include.php';

// 验证商户登录
$merchant = auth_merchant();

// 获取参数
$page = max(1, (int)input('page', 1));
$pageSize = min(50, max(1, (int)input('page_size', 20)));
$status = input('status', ''); // online, offline, busy
$offset = ($page - 1) * $pageSize;

// 构建查询条件
$where = "base_shop_id = ? AND base_status_active = 1";
$params = [$merchant['id']];

if ($status === 'online') {
    $where .= " AND base_status_online = 1";
} elseif ($status === 'offline') {
    $where .= " AND base_status_online = 0";
} elseif ($status === 'busy') {
    $where .= " AND base_status_online = 2";
}

// 查询总数
$total = db()->fetchColumn(
    "SELECT COUNT(*) FROM qy_technician_list WHERE $where",
    $params
);

// 查询列表
$params[] = $pageSize;
$params[] = $offset;
$list = db()->fetchAll(
    "SELECT * FROM qy_technician_list WHERE $where ORDER BY base_time_created DESC LIMIT ? OFFSET ?",
    $params
);

$onlineStatus = [0 => '离线', 1 => '在线', 2 => '服务中', 3 => '休息中'];

$formattedList = array_map(function($item) use ($onlineStatus) {
    return [
        'id' => $item['id'],
        'phone' => $item['base_auth_phone'],
        'realname' => $item['base_profile_realname'],
        'avatar' => $item['base_profile_avatar'],
        'gender' => (int)$item['base_profile_gender'],
        'experience_years' => (int)$item['base_profile_experience_years'],
        'is_certified' => (bool)$item['base_is_certified'],
        'online_status' => (int)$item['base_status_online'],
        'online_status_name' => $onlineStatus[$item['base_status_online']] ?? '',
        'accept_order' => (bool)$item['base_setting_accept_order'],
        'stats' => [
            'order_count' => (int)$item['base_stat_order_count'],
            'rating_avg' => (float)$item['base_stat_rating_avg'],
            'rating_count' => (int)$item['base_stat_rating_count']
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
