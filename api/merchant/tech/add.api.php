<?php
/**
 * ================================================================
 * 文件名: add.api.php
 * 创建时间: 2026-01-13
 * ================================================================
 *
 * 【文件职责】
 * 商户添加技师 API
 *
 * ================================================================
 */

require_once dirname(dirname(dirname(__FILE__))) . '/bootstrap.include.php';

// 验证商户登录
$merchant = auth_merchant();

// 验证必填参数
validate_required(['phone']);

$phone = input('phone');
$realname = input('realname', '');

// 验证手机号格式
if (!validate_phone($phone)) {
    Response::badRequest('手机号格式不正确');
}

// 检查技师是否已存在
$existing = db()->fetch(
    "SELECT * FROM qy_technician_list WHERE base_auth_phone = ?",
    [$phone]
);

if ($existing) {
    if ($existing['base_shop_id'] == $merchant['id']) {
        Response::badRequest('该技师已在您的店铺中');
    }
    if ($existing['base_shop_id'] > 0) {
        Response::badRequest('该技师已归属其他店铺');
    }
    // 散技师，绑定到店铺
    db()->execute(
        "UPDATE qy_technician_list SET base_shop_id = ?, base_type = 1 WHERE id = ?",
        [$merchant['id'], $existing['id']]
    );
    $techId = $existing['id'];
} else {
    // 创建新技师
    db()->execute(
        "INSERT INTO qy_technician_list 
         (base_auth_phone, base_profile_realname, base_shop_id, base_type, base_status_active) 
         VALUES (?, ?, ?, 1, 1)",
        [$phone, $realname, $merchant['id']]
    );
    $techId = db()->lastInsertId();
}

// 更新店铺技师数量
db()->execute(
    "UPDATE qy_shop_list SET base_stat_technician_count = base_stat_technician_count + 1 WHERE id = ?",
    [$merchant['id']]
);

Response::success(['id' => $techId], '添加成功');
