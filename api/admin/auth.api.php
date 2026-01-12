<?php
/**
 * 轻养到家 - 管理后台认证 API
 * ZERO 框架规范 - 冒号语法
 * 
 * POST /api/admin/auth              管理员登录
 */

require_once dirname(dirname(__FILE__)) . '/bootstrap.include.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method !== 'POST') {
    Response::badRequest('请求方法不允许');
}

validate_required(['username', 'password']);

$username = input('username');
$password = input('password');

// Demo 阶段使用固定账号
if ($username === 'admin' && $password === 'admin123') {
    $token = Auth::generateToken(1, TokenType::ADMIN);
    $refreshToken = Auth::generateRefreshToken(1, TokenType::ADMIN);
    
    Response::success([
        'token' => $token,
        'refresh_token' => $refreshToken,
        'expires_in' => AuthConfig::expiry(),
        'admin' => [
            'id' => 1,
            'username' => 'admin',
            'name' => '超级管理员'
        ]
    ], '登录成功');
} else {
    Response::error(10004, '用户名或密码错误');
}
