<?php
/**
 * ================================================================
 * 文件名: login.api.php
 * 创建时间: 2026-01-13
 * ================================================================
 *
 * 【文件职责】
 * 商户登录 API
 *
 * ================================================================
 */

require_once dirname(dirname(dirname(__FILE__))) . '/bootstrap.include.php';

// 验证必填参数
validate_required(['phone', 'code']);

$phone = input('phone');
$code = input('code');

// 验证手机号格式
if (!validate_phone($phone)) {
    Response::badRequest('手机号格式不正确');
}

// 验证验证码（开发环境跳过）
if (getenv('APP_DEBUG') !== 'true') {
    if (!VerifyCode::verify($phone, $code)) {
        Response::badRequest('验证码错误或已过期');
    }
}

// 查询商户
$merchant = db()->fetch(
    "SELECT * FROM qy_shop_list WHERE base_auth_phone = ? AND is_deleted = 0",
    [$phone]
);

if (!$merchant) {
    // 自动注册
    db()->execute(
        "INSERT INTO qy_shop_list (base_auth_phone, base_status_verify, base_status_active) 
         VALUES (?, 0, 1)",
        [$phone]
    );
    $merchantId = db()->lastInsertId();
    $merchant = db()->fetch("SELECT * FROM qy_shop_list WHERE id = ?", [$merchantId]);
}

// 检查状态
if ($merchant['base_status_active'] == 0) {
    Response::forbidden('店铺已被禁用');
}

// 生成 token
$token = bin2hex(random_bytes(32));
$expireAt = date('Y-m-d H:i:s', strtotime('+30 days'));

// 更新 token
db()->execute(
    "UPDATE qy_shop_list SET base_auth_token = ?, base_auth_expire_at = ? WHERE id = ?",
    [$token, $expireAt, $merchant['id']]
);

Response::success([
    'token' => $token,
    'expire_at' => $expireAt,
    'merchant' => [
        'id' => $merchant['id'],
        'phone' => $merchant['base_auth_phone'],
        'name' => $merchant['base_profile_name'],
        'logo' => $merchant['base_profile_logo'],
        'verify_status' => (int)$merchant['base_status_verify']
    ]
]);
