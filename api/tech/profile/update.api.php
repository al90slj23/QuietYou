<?php
/**
 * ================================================================
 * 文件名: update.api.php
 * 创建时间: 2026-01-13
 * ================================================================
 *
 * 【文件职责】
 * 技师更新个人资料 API
 *
 * ================================================================
 */

require_once dirname(dirname(dirname(__FILE__))) . '/bootstrap.include.php';

// 验证技师登录
$tech = auth_technician();

// 获取参数
$realname = input('realname');
$avatar = input('avatar');
$gender = input('gender');
$intro = input('intro');
$photos = input('photos');
$experienceYears = input('experience_years');

$updates = [];
$params = [];

if ($realname !== null) {
    if (mb_strlen($realname) < 2 || mb_strlen($realname) > 20) {
        Response::badRequest('姓名长度应在2-20字之间');
    }
    $updates[] = "base_profile_realname = ?";
    $params[] = $realname;
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

if ($intro !== null) {
    if (mb_strlen($intro) > 500) {
        Response::badRequest('个人简介不能超过500字');
    }
    $updates[] = "base_profile_intro = ?";
    $params[] = $intro;
}

if ($photos !== null) {
    if (!is_array($photos)) {
        Response::badRequest('照片格式错误');
    }
    if (count($photos) > 9) {
        Response::badRequest('最多上传9张照片');
    }
    $updates[] = "base_profile_photos = ?";
    $params[] = json_encode($photos);
}

if ($experienceYears !== null) {
    $years = (int)$experienceYears;
    if ($years < 0 || $years > 50) {
        Response::badRequest('从业年限应在0-50年之间');
    }
    $updates[] = "base_profile_experience_years = ?";
    $params[] = $years;
}

if (empty($updates)) {
    Response::badRequest('没有需要更新的内容');
}

$params[] = $tech['id'];
db()->execute(
    "UPDATE qy_technician_list SET " . implode(', ', $updates) . " WHERE id = ?",
    $params
);

Response::success(null, '资料已更新');
