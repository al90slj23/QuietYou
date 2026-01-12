<?php
/**
 * ================================================================
 * 文件名: list.api.php
 * 创建时间: 2026-01-13
 * ================================================================
 *
 * 【文件职责】
 * 用户消息列表 API
 *
 * ================================================================
 */

require_once dirname(dirname(dirname(__FILE__))) . '/bootstrap.include.php';

// 验证用户登录
$user = auth_customer();

// 获取参数
$page = max(1, (int)input('page', 1));
$pageSize = min(50, max(1, (int)input('page_size', 20)));
$type = input('type', ''); // order, review, system
$offset = ($page - 1) * $pageSize;

// 构建查询条件
$where = "base_receiver_type = 'user' AND base_receiver_id = ?";
$params = [$user['id']];

if ($type) {
    $where .= " AND base_type = ?";
    $params[] = $type;
}

// 查询总数
$total = db()->fetchColumn(
    "SELECT COUNT(*) FROM qy_message_list WHERE $where",
    $params
);

// 未读数
$unreadCount = db()->fetchColumn(
    "SELECT COUNT(*) FROM qy_message_list WHERE $where AND base_is_read = 0",
    $params
);

// 查询列表
$params[] = $pageSize;
$params[] = $offset;
$list = db()->fetchAll(
    "SELECT * FROM qy_message_list WHERE $where ORDER BY base_time_created DESC LIMIT ? OFFSET ?",
    $params
);

$formattedList = array_map(function($item) {
    return [
        'id' => $item['id'],
        'type' => $item['base_type'],
        'title' => $item['base_title'],
        'content' => $item['base_content'],
        'extra' => $item['base_extra'] ? json_decode($item['base_extra'], true) : null,
        'is_read' => (bool)$item['base_is_read'],
        'created_at' => $item['base_time_created']
    ];
}, $list);

Response::success([
    'unread_count' => (int)$unreadCount,
    'list' => $formattedList,
    'pagination' => [
        'total' => (int)$total,
        'page' => $page,
        'page_size' => $pageSize,
        'total_pages' => ceil($total / $pageSize)
    ]
]);
