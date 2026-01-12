<?php
/**
 * ================================================================
 * 文件名: list.api.php
 * 创建时间: 2026-01-13
 * ================================================================
 *
 * 【文件职责】
 * 技师评价列表 API
 *
 * ================================================================
 */

require_once dirname(dirname(dirname(__FILE__))) . '/bootstrap.include.php';

// 验证技师登录
$tech = auth_technician();

// 获取参数
$page = max(1, (int)input('page', 1));
$pageSize = min(50, max(1, (int)input('page_size', 20)));
$rating = input('rating', ''); // 筛选评分
$offset = ($page - 1) * $pageSize;

// 构建查询条件
$where = "r.base_technician_id = ? AND r.base_status_visible = 1";
$params = [$tech['id']];

if ($rating) {
    $where .= " AND r.base_rating_overall = ?";
    $params[] = (int)$rating;
}

// 查询总数
$total = db()->fetchColumn(
    "SELECT COUNT(*) FROM qy_review_list r WHERE $where",
    $params
);

// 查询列表
$params[] = $pageSize;
$params[] = $offset;
$list = db()->fetchAll(
    "SELECT r.*, u.base_profile_nickname as user_nickname, u.base_profile_avatar as user_avatar,
            o.base_service_name as service_name
     FROM qy_review_list r
     LEFT JOIN qy_user_list u ON r.base_user_id = u.id
     LEFT JOIN qy_order_list o ON r.base_order_id = o.id
     WHERE $where
     ORDER BY r.base_time_created DESC
     LIMIT ? OFFSET ?",
    $params
);

// 统计各评分数量
$stats = db()->fetchAll(
    "SELECT base_rating_overall as rating, COUNT(*) as count 
     FROM qy_review_list 
     WHERE base_technician_id = ? AND base_status_visible = 1
     GROUP BY base_rating_overall",
    [$tech['id']]
);

$ratingStats = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
foreach ($stats as $stat) {
    $ratingStats[$stat['rating']] = (int)$stat['count'];
}

// 格式化数据
$formattedList = array_map(function($item) {
    return [
        'id' => $item['id'],
        'order_id' => $item['base_order_id'],
        'service_name' => $item['service_name'],
        'user' => [
            'nickname' => $item['base_is_anonymous'] ? '匿名用户' : $item['user_nickname'],
            'avatar' => $item['base_is_anonymous'] ? '' : $item['user_avatar']
        ],
        'rating' => [
            'overall' => (int)$item['base_rating_overall'],
            'skill' => (int)$item['base_rating_skill'],
            'attitude' => (int)$item['base_rating_attitude'],
            'punctual' => (int)$item['base_rating_punctual'],
            'communication' => (int)$item['base_rating_communication'],
            'hygiene' => (int)$item['base_rating_hygiene']
        ],
        'content' => $item['base_content'],
        'photos' => $item['base_photos'] ? json_decode($item['base_photos'], true) : [],
        'tags' => $item['base_tags'] ? json_decode($item['base_tags'], true) : [],
        'reply' => $item['base_reply'],
        'reply_time' => $item['base_reply_time'],
        'created_at' => $item['base_time_created']
    ];
}, $list);

Response::success([
    'stats' => [
        'total' => (int)$total,
        'average' => $tech['base_stat_rating_avg'],
        'by_rating' => $ratingStats
    ],
    'list' => $formattedList,
    'pagination' => [
        'total' => (int)$total,
        'page' => $page,
        'page_size' => $pageSize,
        'total_pages' => ceil($total / $pageSize)
    ]
]);
