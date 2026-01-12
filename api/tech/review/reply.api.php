<?php
/**
 * ================================================================
 * 文件名: reply.api.php
 * 创建时间: 2026-01-13
 * ================================================================
 *
 * 【文件职责】
 * 技师回复评价 API
 *
 * ================================================================
 */

require_once dirname(dirname(dirname(__FILE__))) . '/bootstrap.include.php';

// 验证技师登录
$tech = auth_technician();

// 验证必填参数
validate_required(['review_id', 'reply']);

$reviewId = (int)input('review_id');
$reply = trim(input('reply'));

if (mb_strlen($reply) > 500) {
    Response::badRequest('回复内容不能超过500字');
}

// 查询评价
$review = db()->fetch(
    "SELECT * FROM qy_review_list WHERE id = ? AND base_technician_id = ?",
    [$reviewId, $tech['id']]
);

if (!$review) {
    Response::notFound('评价不存在');
}

if ($review['base_reply']) {
    Response::badRequest('已回复过该评价');
}

// 更新回复
db()->execute(
    "UPDATE qy_review_list SET base_reply = ?, base_reply_time = NOW() WHERE id = ?",
    [$reply, $reviewId]
);

Response::success(null, '回复成功');
