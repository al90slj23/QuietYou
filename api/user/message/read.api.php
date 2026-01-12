<?php
/**
 * ================================================================
 * 文件名: read.api.php
 * 创建时间: 2026-01-13
 * ================================================================
 *
 * 【文件职责】
 * 用户标记消息已读 API
 *
 * ================================================================
 */

require_once dirname(dirname(dirname(__FILE__))) . '/bootstrap.include.php';

// 验证用户登录
$user = auth_customer();

$messageId = input('id');
$readAll = input('read_all', false);

if ($readAll) {
    // 全部标记已读
    db()->execute(
        "UPDATE qy_message_list SET base_is_read = 1, base_time_read = NOW() 
         WHERE base_receiver_type = 'user' AND base_receiver_id = ? AND base_is_read = 0",
        [$user['id']]
    );
    Response::success(null, '已全部标记为已读');
} elseif ($messageId) {
    // 标记单条已读
    db()->execute(
        "UPDATE qy_message_list SET base_is_read = 1, base_time_read = NOW() 
         WHERE id = ? AND base_receiver_type = 'user' AND base_receiver_id = ?",
        [$messageId, $user['id']]
    );
    Response::success(null, '已标记为已读');
} else {
    Response::badRequest('请指定消息ID或设置read_all=true');
}
