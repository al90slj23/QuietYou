<?php
/**
 * ================================================================
 * 文件名: update.api.php
 * 创建时间: 2026-01-13
 * ================================================================
 *
 * 【文件职责】
 * 用户更新个人资料 API
 *
 * ================================================================
 */

require_once dirname(dirname(dirname(__FILE__))) . '/bootstrap.include.php';

// 验证用户登录
$user = auth_customer();

// 获取参数
$nickname = input('nickname');
$avatar = input('avatar');
$gender = input('gender');

$updates = [];
$params = [];

if ($nickname !== null) {
    if (mb_strlen($nickname) < 2 || mb_strlen($nickname) > 20) {
        Response::badRequest('昵称长度应在2-20字之间');
    }
    $updates[] = "base_profile_nickname = ?";
    $params[] = $nickname;
}

if ($avatar !== null) {
    $updates[] = "base_profile_avatar = ?";
    $params[] = $avatar;
}

if ($gender !== null) {
    if (!in_array((int)$gender, [0, 1, 2])) {
        Response::badRequest('无效的性别');
    }
    $updates[] = "base_profile_gender = ?";
    $params[] = (int)$gender;
}

if (empty($updates)) {
    Response::badRequest('没有需要更新的内容');
}

$params[] = $user['id'];
db()->execute(
    "UPDATE qy_user_list SET " . implode(', ', $updates) . " WHERE id = ?",
    $params
);

Response::success(null, '资料已更新');
