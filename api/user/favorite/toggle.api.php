<?php
/**
 * ================================================================
 * 文件名: toggle.api.php
 * 创建时间: 2026-01-13
 * ================================================================
 *
 * 【文件职责】
 * 用户收藏/取消收藏 API
 *
 * ================================================================
 */

require_once dirname(dirname(dirname(__FILE__))) . '/bootstrap.include.php';

// 验证用户登录
$user = auth_customer();

// 验证必填参数
validate_required(['type', 'target_id']);

$type = input('type'); // technician, shop
$targetId = (int)input('target_id');

if (!in_array($type, ['technician', 'shop'])) {
    Response::badRequest('无效的收藏类型');
}

// 验证目标是否存在
if ($type === 'technician') {
    $target = db()->fetch(
        "SELECT id FROM qy_technician_list WHERE id = ? AND base_status_active = 1",
        [$targetId]
    );
} else {
    $target = db()->fetch(
        "SELECT id FROM qy_shop_list WHERE id = ? AND base_status_active = 1",
        [$targetId]
    );
}

if (!$target) {
    Response::notFound('目标不存在');
}

// 检查是否已收藏
$existing = db()->fetch(
    "SELECT id FROM qy_user_favorite_list 
     WHERE base_user_id = ? AND base_target_type = ? AND base_target_id = ?",
    [$user['id'], $type, $targetId]
);

if ($existing) {
    // 取消收藏
    db()->execute("DELETE FROM qy_user_favorite_list WHERE id = ?", [$existing['id']]);
    Response::success(['is_favorite' => false], '已取消收藏');
} else {
    // 添加收藏
    db()->execute(
        "INSERT INTO qy_user_favorite_list (base_user_id, base_target_type, base_target_id) VALUES (?, ?, ?)",
        [$user['id'], $type, $targetId]
    );
    Response::success(['is_favorite' => true], '收藏成功');
}
