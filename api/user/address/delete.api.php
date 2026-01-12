<?php
/**
 * ================================================================
 * 文件名: delete.api.php
 * 创建时间: 2026-01-13
 * ================================================================
 *
 * 【文件职责】
 * 用户删除地址 API
 *
 * ================================================================
 */

require_once dirname(dirname(dirname(__FILE__))) . '/bootstrap.include.php';

// 验证用户登录
$user = auth_customer();

// 验证必填参数
validate_required(['id']);

$addressId = (int)input('id');

// 查询地址
$address = db()->fetch(
    "SELECT * FROM qy_user_address_list WHERE id = ? AND base_user_id = ?",
    [$addressId, $user['id']]
);

if (!$address) {
    Response::notFound('地址不存在');
}

// 删除地址
db()->execute("DELETE FROM qy_user_address_list WHERE id = ?", [$addressId]);

Response::success(null, '删除成功');
