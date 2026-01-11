<?php
/**
 * 轻养到家 - 用户端认证 API
 * ZERO 框架规范 - 冒号语法
 * 
 * POST /api/user/auth              发送验证码
 * POST /api/user/auth:verify       验证登录
 * POST /api/user/auth:refresh      刷新 Token
 * GET  /api/user/auth:profile      获取用户信息
 * PUT  /api/user/auth:profile      更新用户信息
 */

require_once dirname(dirname(dirname(__FILE__))) . '/bootstrap.inc.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

switch ($action) {
    case '':
        // POST /api/user/auth - 发送验证码
        if ($method !== 'POST') {
            Response::badRequest('请求方法不允许');
        }
        sendVerifyCode();
        break;
        
    case 'verify':
        // POST /api/user/auth:verify - 验证登录
        if ($method !== 'POST') {
            Response::badRequest('请求方法不允许');
        }
        verifyLogin();
        break;
        
    case 'refresh':
        // POST /api/user/auth:refresh - 刷新 Token
        if ($method !== 'POST') {
            Response::badRequest('请求方法不允许');
        }
        refreshToken();
        break;
        
    case 'profile':
        if ($method === 'GET') {
            getProfile();
        } elseif ($method === 'PUT') {
            updateProfile();
        } else {
            Response::badRequest('请求方法不允许');
        }
        break;
        
    default:
        Response::notFound('接口不存在');
}

/**
 * 发送验证码
 */
function sendVerifyCode() {
    validate_required(['phone']);
    
    $phone = input('phone');
    
    if (!validate_phone($phone)) {
        Response::badRequest('手机号格式不正确');
    }
    
    // 检查发送频率
    if (!VerifyCode::canSend($phone)) {
        $wait = VerifyCode::getRemainingWaitTime($phone);
        Response::error(10002, "请{$wait}秒后再试");
    }
    
    // 生成验证码
    $code = VerifyCode::generate(6);
    
    // 存储验证码
    VerifyCode::store($phone, $code);
    
    // TODO: 实际发送短信（Demo 阶段直接返回验证码）
    Response::success([
        'phone' => $phone,
        'code' => $code, // 生产环境应移除
        'expires_in' => VerifyCode::EXPIRY
    ], '验证码已发送');
}

/**
 * 验证登录
 */
function verifyLogin() {
    validate_required(['phone', 'code']);
    
    $phone = input('phone');
    $code = input('code');
    
    if (!validate_phone($phone)) {
        Response::badRequest('手机号格式不正确');
    }
    
    // 验证验证码
    if (!VerifyCode::verify($phone, $code)) {
        Response::error(10004, '验证码错误或已过期');
    }
    
    // 查找或创建用户
    $user = db()->fetch(
        'SELECT * FROM qy_user_list WHERE base_auth_phone = ?',
        [$phone]
    );
    
    if (!$user) {
        // 创建新用户
        $userId = db()->insert('qy_user_list', [
            'base_auth_phone' => $phone,
            'base_profile_nickname' => '用户' . substr($phone, -4),
            'base_profile_avatar' => '',
            'base_profile_gender' => 0,
            'base_status_active' => 1,
            'base_time_created' => date('Y-m-d H:i:s'),
            'base_time_updated' => date('Y-m-d H:i:s')
        ]);
        
        $user = db()->fetch('SELECT * FROM qy_user_list WHERE id = ?', [$userId]);
    }
    
    // 检查用户状态
    if ($user['base_status_active'] != 1) {
        Response::error(10005, '账号已被禁用');
    }
    
    // 生成 Token
    $token = Auth::generateToken($user['id'], TokenType::USER);
    $refreshToken = Auth::generateRefreshToken($user['id'], TokenType::USER);
    
    Response::success([
        'token' => $token,
        'refresh_token' => $refreshToken,
        'expires_in' => AuthConfig::expiry(),
        'user' => [
            'id' => $user['id'],
            'phone' => substr($phone, 0, 3) . '****' . substr($phone, -4),
            'nickname' => $user['base_profile_nickname'],
            'avatar' => $user['base_profile_avatar'],
            'gender' => $user['base_profile_gender']
        ]
    ], '登录成功');
}

/**
 * 刷新 Token
 */
function refreshToken() {
    validate_required(['refresh_token']);
    
    $refreshToken = input('refresh_token');
    $result = Auth::refreshToken($refreshToken);
    
    if (!$result) {
        Response::unauthorized('刷新令牌无效或已过期');
    }
    
    Response::success([
        'token' => $result['token'],
        'refresh_token' => $result['refresh_token'],
        'expires_in' => AuthConfig::expiry()
    ]);
}

/**
 * 获取用户信息
 */
function getProfile() {
    $auth = auth_require(TokenType::USER);
    
    $user = db()->fetch(
        'SELECT * FROM qy_user_list WHERE id = ?',
        [$auth['id']]
    );
    
    if (!$user) {
        Response::notFound('用户不存在');
    }
    
    Response::success([
        'id' => $user['id'],
        'phone' => substr($user['base_auth_phone'], 0, 3) . '****' . substr($user['base_auth_phone'], -4),
        'nickname' => $user['base_profile_nickname'],
        'avatar' => $user['base_profile_avatar'],
        'gender' => $user['base_profile_gender'],
        'created_at' => $user['base_time_created']
    ]);
}

/**
 * 更新用户信息
 */
function updateProfile() {
    $auth = auth_require(TokenType::USER);
    
    $data = [];
    
    if (input('nickname') !== null) {
        $data['base_profile_nickname'] = input('nickname');
    }
    if (input('avatar') !== null) {
        $data['base_profile_avatar'] = input('avatar');
    }
    if (input('gender') !== null) {
        $data['base_profile_gender'] = (int)input('gender');
    }
    
    if (empty($data)) {
        Response::badRequest('没有要更新的数据');
    }
    
    $data['base_time_updated'] = date('Y-m-d H:i:s');
    
    db()->update('qy_user_list', $data, 'id = :id', ['id' => $auth['id']]);
    
    Response::success(null, '更新成功');
}
