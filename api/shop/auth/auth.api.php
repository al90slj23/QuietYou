<?php
/**
 * 轻养到家 - 商家端认证 API
 * ZERO 框架规范 - 冒号语法
 * 
 * POST /api/shop/auth              发送验证码/登录
 * POST /api/shop/auth:register     入驻申请
 * POST /api/shop/auth:verify       验证登录
 * GET  /api/shop/auth:profile      获取商家信息
 * PUT  /api/shop/auth:profile      更新商家信息
 */

require_once dirname(dirname(dirname(__FILE__))) . '/bootstrap.include.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

switch ($action) {
    case '':
        if ($method !== 'POST') Response::badRequest('请求方法不允许');
        sendVerifyCode();
        break;
        
    case 'register':
        if ($method !== 'POST') Response::badRequest('请求方法不允许');
        register();
        break;
        
    case 'verify':
        if ($method !== 'POST') Response::badRequest('请求方法不允许');
        verifyLogin();
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
    
    if (!VerifyCode::canSend($phone)) {
        $wait = VerifyCode::getRemainingWaitTime($phone);
        Response::error(10002, "请{$wait}秒后再试");
    }
    
    $code = VerifyCode::generate(6);
    VerifyCode::store($phone, $code);
    
    Response::success([
        'phone' => $phone,
        'code' => $code,
        'expires_in' => VerifyCode::EXPIRY
    ], '验证码已发送');
}

/**
 * 商家入驻申请
 */
function register() {
    validate_required(['phone', 'code', 'name', 'contact_name', 'license']);
    
    $phone = input('phone');
    $code = input('code');
    $name = input('name');
    $contactName = input('contact_name');
    $address = input('address', '');
    $intro = input('intro', '');
    $license = input('license');
    $permit = input('permit', '');
    
    if (!validate_phone($phone)) {
        Response::badRequest('手机号格式不正确');
    }
    
    if (!VerifyCode::verify($phone, $code)) {
        Response::error(10004, '验证码错误或已过期');
    }
    
    // 检查是否已注册
    $existing = db()->fetch(
        'SELECT id, base_status_verify FROM qy_shop_list WHERE base_auth_phone = ?',
        [$phone]
    );
    
    if ($existing) {
        if ($existing['base_status_verify'] == 0) {
            Response::error(10006, '您的申请正在审核中');
        } elseif ($existing['base_status_verify'] == 1) {
            Response::error(10007, '该手机号已注册，请直接登录');
        }
    }
    
    // 创建商家记录
    $shopId = db()->insert('qy_shop_list', [
        'base_auth_phone' => $phone,
        'base_profile_name' => $name,
        'base_profile_logo' => '',
        'base_profile_intro' => $intro,
        'base_profile_address' => $address,
        'base_cert_license' => $license,
        'base_cert_permit' => $permit,
        'base_contact_name' => $contactName,
        'base_stat_technician_count' => 0,
        'base_stat_order_count' => 0,
        'base_commission_rate' => 15.00,
        'base_status_verify' => 0,
        'base_status_active' => 1,
        'base_time_created' => date('Y-m-d H:i:s')
    ]);
    
    // 创建钱包
    db()->insert('qy_wallet_list', [
        'base_owner_type' => 'shop',
        'base_owner_id' => $shopId,
        'base_balance_available' => 0,
        'base_balance_pending' => 0,
        'base_balance_total' => 0,
        'base_time_updated' => date('Y-m-d H:i:s')
    ]);
    
    Response::success([
        'shop_id' => $shopId
    ], '入驻申请已提交，请等待审核');
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
    
    if (!VerifyCode::verify($phone, $code)) {
        Response::error(10004, '验证码错误或已过期');
    }
    
    $shop = db()->fetch(
        'SELECT * FROM qy_shop_list WHERE base_auth_phone = ?',
        [$phone]
    );
    
    if (!$shop) {
        Response::error(10008, '账号不存在，请先申请入驻');
    }
    
    if ($shop['base_status_verify'] == 0) {
        Response::error(10006, '您的申请正在审核中');
    }
    
    if ($shop['base_status_verify'] == 2) {
        Response::error(10009, '您的申请已被拒绝');
    }
    
    if ($shop['base_status_active'] != 1) {
        Response::error(10005, '账号已被禁用');
    }
    
    $token = Auth::generateToken($shop['id'], TokenType::SHOP);
    $refreshToken = Auth::generateRefreshToken($shop['id'], TokenType::SHOP);
    
    Response::success([
        'token' => $token,
        'refresh_token' => $refreshToken,
        'expires_in' => AuthConfig::expiry(),
        'shop' => [
            'id' => $shop['id'],
            'name' => $shop['base_profile_name'],
            'logo' => $shop['base_profile_logo']
        ]
    ], '登录成功');
}

/**
 * 获取商家信息
 */
function getProfile() {
    $auth = auth_require(TokenType::SHOP);
    
    $shop = db()->fetch(
        'SELECT * FROM qy_shop_list WHERE id = ?',
        [$auth['id']]
    );
    
    if (!$shop) {
        Response::notFound('商家不存在');
    }
    
    $wallet = db()->fetch(
        'SELECT * FROM qy_wallet_list WHERE base_owner_type = ? AND base_owner_id = ?',
        ['shop', $auth['id']]
    );
    
    Response::success([
        'id' => $shop['id'],
        'phone' => substr($shop['base_auth_phone'], 0, 3) . '****' . substr($shop['base_auth_phone'], -4),
        'name' => $shop['base_profile_name'],
        'logo' => $shop['base_profile_logo'],
        'intro' => $shop['base_profile_intro'],
        'address' => $shop['base_profile_address'],
        'contact_name' => $shop['base_contact_name'],
        'technician_count' => $shop['base_stat_technician_count'],
        'order_count' => $shop['base_stat_order_count'],
        'commission_rate' => $shop['base_commission_rate'],
        'wallet' => [
            'available' => $wallet['base_balance_available'] ?? 0,
            'pending' => $wallet['base_balance_pending'] ?? 0,
            'total' => $wallet['base_balance_total'] ?? 0
        ]
    ]);
}

/**
 * 更新商家信息
 */
function updateProfile() {
    $auth = auth_require(TokenType::SHOP);
    
    $data = [];
    
    if (input('logo') !== null) {
        $data['base_profile_logo'] = input('logo');
    }
    if (input('intro') !== null) {
        $data['base_profile_intro'] = input('intro');
    }
    if (input('address') !== null) {
        $data['base_profile_address'] = input('address');
    }
    if (input('contact_name') !== null) {
        $data['base_contact_name'] = input('contact_name');
    }
    
    if (empty($data)) {
        Response::badRequest('没有要更新的数据');
    }
    
    db()->update('qy_shop_list', $data, 'id = :id', ['id' => $auth['id']]);
    
    Response::success(null, '更新成功');
}
