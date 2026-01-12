<?php
/**
 * ================================================================
 * 文件名: update.api.php
 * 创建时间: 2026-01-13
 * ================================================================
 *
 * 【文件职责】
 * 商户更新店铺信息 API
 *
 * ================================================================
 */

require_once dirname(dirname(dirname(__FILE__))) . '/bootstrap.include.php';

// 验证商户登录
$merchant = auth_merchant();

// 获取参数
$name = input('name');
$logo = input('logo');
$intro = input('intro');
$address = input('address');
$contactName = input('contact_name');

$updates = [];
$params = [];

if ($name !== null) {
    if (mb_strlen($name) < 2 || mb_strlen($name) > 50) {
        Response::badRequest('店铺名称长度应在2-50字之间');
    }
    $updates[] = "base_profile_name = ?";
    $params[] = $name;
}

if ($logo !== null) {
    $updates[] = "base_profile_logo = ?";
    $params[] = $logo;
}

if ($intro !== null) {
    if (mb_strlen($intro) > 500) {
        Response::badRequest('店铺简介不能超过500字');
    }
    $updates[] = "base_profile_intro = ?";
    $params[] = $intro;
}

if ($address !== null) {
    $updates[] = "base_profile_address = ?";
    $params[] = $address;
}

if ($contactName !== null) {
    $updates[] = "base_contact_name = ?";
    $params[] = $contactName;
}

if (empty($updates)) {
    Response::badRequest('没有需要更新的内容');
}

$params[] = $merchant['id'];
db()->execute(
    "UPDATE qy_shop_list SET " . implode(', ', $updates) . " WHERE id = ?",
    $params
);

Response::success(null, '信息已更新');
