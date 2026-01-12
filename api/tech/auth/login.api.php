<?php
/**
 * ================================================================
 * 文件名: login.api.php
 * 创建时间: 2026-01-13
 * ================================================================
 *
 * 【文件职责】
 * 技师端登录接口
 *
 * ================================================================
 */

require_once dirname(dirname(dirname(__FILE__))) . '/bootstrap.include.php';

// 只允许 POST 请求
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    Response::badRequest('请求方法不允许');
}

// 验证参数
validate_required(['phone', 'code']);

$phone = input('phone');
$code = input('code');

// 验证手机号格式
if (!validate_phone($phone)) {
    Response::badRequest('手机号格式不正确');
}

// TODO: 验证短信验证码
// 开发阶段，验证码为 123456
if ($code !== '123456') {
    Response::badRequest('验证码错误');
}

// 查询技师信息
$tech = db()->fetch(
    "SELECT * FROM qy_technician_list WHERE base_profile_phone = ? AND is_deleted = 0",
    [$phone]
);

if (!$tech) {
    // 技师不存在，返回提示
    Response::error(40001, '该手机号未注册为技师，请先申请入驻');
}

// 检查技师状态
if ($tech['base_status'] === 'disabled') {
    Response::error(40002, '账号已被禁用，请联系客服');
}

// 生成 token
$token = bin2hex(random_bytes(32));
$expireAt = date('Y-m-d H:i:s', strtotime('+7 days'));

// 更新 token
db()->update(
    'qy_technician_list',
    [
        'base_auth_token' => $token,
        'base_auth_expire_at' => $expireAt,
        'updated_at' => date('Y-m-d H:i:s')
    ],
    'id = :id',
    ['id' => $tech['id']]
);

// 返回登录信息
Response::success([
    'token' => $token,
    'expire_at' => $expireAt,
    'technician' => [
        'id' => $tech['id'],
        'name' => $tech['base_profile_name'],
        'phone' => substr($phone, 0, 3) . '****' . substr($phone, -4),
        'avatar' => $tech['base_profile_avatar'],
        'type' => $tech['base_type'],
        'is_certified' => (bool)$tech['base_is_certified'],
        'shop_id' => $tech['base_shop_id']
    ]
]);
