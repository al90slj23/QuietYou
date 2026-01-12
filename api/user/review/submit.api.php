<?php
/**
 * ================================================================
 * 文件名: submit.api.php
 * 创建时间: 2026-01-13
 * ================================================================
 *
 * 【文件职责】
 * 用户提交评价 API（多维度评分）
 *
 * ================================================================
 */

require_once dirname(dirname(dirname(__FILE__))) . '/bootstrap.include.php';

// 验证用户登录
$user = auth_customer();

// 验证必填参数
validate_required(['order_id', 'rating_overall']);

$orderId = (int)input('order_id');
$ratingOverall = (int)input('rating_overall');
$ratingSkill = (int)input('rating_skill', $ratingOverall);
$ratingAttitude = (int)input('rating_attitude', $ratingOverall);
$ratingPunctual = (int)input('rating_punctual', $ratingOverall);
$ratingCommunication = (int)input('rating_communication', $ratingOverall);
$ratingHygiene = (int)input('rating_hygiene', $ratingOverall);
$content = input('content', '');
$photos = input('photos', []);
$tags = input('tags', []);
$isAnonymous = (int)input('is_anonymous', 0);

// 验证评分范围
foreach ([$ratingOverall, $ratingSkill, $ratingAttitude, $ratingPunctual, $ratingCommunication, $ratingHygiene] as $rating) {
    if ($rating < 1 || $rating > 5) {
        Response::badRequest('评分应在1-5之间');
    }
}

// 查询订单
$order = db()->fetch(
    "SELECT * FROM qy_order_list WHERE id = ? AND base_user_id = ?",
    [$orderId, $user['id']]
);

if (!$order) {
    Response::notFound('订单不存在');
}

if ($order['base_status_order'] != 5) {
    Response::badRequest('只能评价已完成的订单');
}

// 检查是否已评价
$existing = db()->fetch(
    "SELECT id FROM qy_review_list WHERE base_order_id = ?",
    [$orderId]
);

if ($existing) {
    Response::badRequest('该订单已评价');
}

// 创建评价
db()->execute(
    "INSERT INTO qy_review_list 
     (base_order_id, base_user_id, base_technician_id, base_shop_id, base_reviewer_type,
      base_rating_overall, base_rating_skill, base_rating_attitude, base_rating_punctual,
      base_rating_communication, base_rating_hygiene, base_content, base_photos, base_tags, base_is_anonymous)
     VALUES (?, ?, ?, ?, 1, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
    [$orderId, $user['id'], $order['base_technician_id'], $order['base_shop_id'],
     $ratingOverall, $ratingSkill, $ratingAttitude, $ratingPunctual, $ratingCommunication, $ratingHygiene,
     $content, json_encode($photos), json_encode($tags), $isAnonymous]
);

// 更新技师评分统计
if ($order['base_technician_id']) {
    $avgRating = db()->fetchColumn(
        "SELECT AVG(base_rating_overall) FROM qy_review_list WHERE base_technician_id = ?",
        [$order['base_technician_id']]
    );
    $ratingCount = db()->fetchColumn(
        "SELECT COUNT(*) FROM qy_review_list WHERE base_technician_id = ?",
        [$order['base_technician_id']]
    );
    
    db()->execute(
        "UPDATE qy_technician_list SET base_stat_rating_avg = ?, base_stat_rating_count = ? WHERE id = ?",
        [round($avgRating, 2), $ratingCount, $order['base_technician_id']]
    );
}

Response::success(null, '评价成功');
