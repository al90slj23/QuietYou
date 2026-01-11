<?php
/**
 * 轻养到家 - 技师端认证 API
 * ZERO 框架规范 - 冒号语法
 * 
 * POST /api/tech/auth              发送验证码/登录
 * POST /api/tech/auth:register     注册申请
 * POST /api/tech/auth:verify       验证登录
 * GET  /api/tech/auth:profile      获取技师信息
 * PUT  /api/tech/auth:profile      更新技师信息
 */

require_once dirname(dirname(dirname(__FILE__))) . '/bootstrap.inc.php';

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
        'code' => $code, // 生产环境应移除
        'expires_in' => VerifyCode::EXPIRY
    ], '验证码已发送');
}

/**
 * 技师注册申请
 */
function register() {
    validate_required(['phone', 'code', 'realname', 'gender', 'idcard_front', 'idcard_back', 'health_cert']);
    
    $phone = input('phone');
    $code = input('code');
    $realname = input('realname');
    $gender = (int)input('gender');
    $intro = input('intro', '');
    $idcardFront = input('idcard_front');
    $idcardBack = input('idcard_back');
    $healthCert = input('health_cert');
    $professionalCert = input('professional_cert', '');
    $shopId = input('shop_id', 0);
    
    if (!validate_phone($phone)) {
        Response::badRequest('手机号格式不正确');
    }
    
    if (!VerifyCode::verify($phone, $code)) {
        Response::error(10004, '验证码错误或已过期');
    }
    
    // 检查是否已注册
    $existing = db()->fetch(
        'SELECT id, base_status_verify FROM qy_technician_list WHERE base_auth_phone = ?',
        [$phone]
    );
    
    if ($existing) {
        if ($existing['base_status_verify'] == 0) {
            Response::error(10006, '您的申请正在审核中');
        } elseif ($existing['base_status_verify'] == 1) {
            Response::error(10007, '该手机号已注册，请直接登录');
        }
    }
    
    // 创建技师记录
    $techId = db()->insert('qy_technician_list', [
        'base_auth_phone' => $phone,
        'base_profile_realname' => $realname,
        'base_profile_avatar' => '',
        'base_profile_gender' => $gender,
        'base_profile_intro' => $intro,
        'base_profile_photos' => '[]',
        'base_cert_idcard_front' => $idcardFront,
        'base_cert_idcard_back' => $idcardBack,
        'base_cert_health' => $healthCert,
        'base_cert_professional' => $professionalCert,
        'base_shop_id' => $shopId,
        'base_shop_commission_rate' => 55.00,
        'base_stat_order_count' => 0,
        'base_stat_rating_avg' => 0,
        'base_stat_rating_count' => 0,
        'base_status_verify' => 0, // 待审核
        'base_status_online' => 0,
        'base_status_active' => 1,
        'base_location_lat' => 0,
        'base_location_lng' => 0,
        'base_time_created' => date('Y-m-d H:i:s')
    ]);
    
    // 创建钱包
    db()->insert('qy_wallet_list', [
        'base_owner_type' => 'technician',
        'base_owner_id' => $techId,
        'base_balance_available' => 0,
        'base_balance_pending' => 0,
        'base_balance_total' => 0,
        'base_time_updated' => date('Y-m-d H:i:s')
    ]);
    
    Response::success([
        'technician_id' => $techId
    ], '注册申请已提交，请等待审核');
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
    
    $tech = db()->fetch(
        'SELECT * FROM qy_technician_list WHERE base_auth_phone = ?',
        [$phone]
    );
    
    if (!$tech) {
        Response::error(10008, '账号不存在，请先注册');
    }
    
    if ($tech['base_status_verify'] == 0) {
        Response::error(10006, '您的申请正在审核中');
    }
    
    if ($tech['base_status_verify'] == 2) {
        Response::error(10009, '您的申请已被拒绝');
    }
    
    if ($tech['base_status_active'] != 1) {
        Response::error(10005, '账号已被禁用');
    }
    
    $token = Auth::generateToken($tech['id'], TokenType::TECHNICIAN);
    $refreshToken = Auth::generateRefreshToken($tech['id'], TokenType::TECHNICIAN);
    
    Response::success([
        'token' => $token,
        'refresh_token' => $refreshToken,
        'expires_in' => AuthConfig::expiry(),
        'technician' => [
            'id' => $tech['id'],
            'name' => $tech['base_profile_realname'],
            'avatar' => $tech['base_profile_avatar'],
            'online_status' => $tech['base_status_online']
        ]
    ], '登录成功');
}

/**
 * 获取技师信息
 */
function getProfile() {
    $auth = auth_require(TokenType::TECHNICIAN);
    
    $tech = db()->fetch(
        'SELECT t.*, s.base_profile_name as shop_name
         FROM qy_technician_list t
         LEFT JOIN qy_shop_list s ON t.base_shop_id = s.id
         WHERE t.id = ?',
        [$auth['id']]
    );
    
    if (!$tech) {
        Response::notFound('技师不存在');
    }
    
    // 获取钱包信息
    $wallet = db()->fetch(
        'SELECT * FROM qy_wallet_list WHERE base_owner_type = ? AND base_owner_id = ?',
        ['technician', $auth['id']]
    );
    
    Response::success([
        'id' => $tech['id'],
        'phone' => substr($tech['base_auth_phone'], 0, 3) . '****' . substr($tech['base_auth_phone'], -4),
        'name' => $tech['base_profile_realname'],
        'avatar' => $tech['base_profile_avatar'],
        'gender' => $tech['base_profile_gender'],
        'intro' => $tech['base_profile_intro'],
        'photos' => json_decode($tech['base_profile_photos'], true) ?: [],
        'shop_id' => $tech['base_shop_id'],
        'shop_name' => $tech['shop_name'],
        'rating' => round($tech['base_stat_rating_avg'], 2),
        'order_count' => $tech['base_stat_order_count'],
        'rating_count' => $tech['base_stat_rating_count'],
        'online_status' => $tech['base_status_online'],
        'wallet' => [
            'available' => $wallet['base_balance_available'] ?? 0,
            'pending' => $wallet['base_balance_pending'] ?? 0,
            'total' => $wallet['base_balance_total'] ?? 0
        ]
    ]);
}

/**
 * 更新技师信息
 */
function updateProfile() {
    $auth = auth_require(TokenType::TECHNICIAN);
    
    $data = [];
    
    if (input('avatar') !== null) {
        $data['base_profile_avatar'] = input('avatar');
    }
    if (input('intro') !== null) {
        $data['base_profile_intro'] = input('intro');
    }
    if (input('photos') !== null) {
        $data['base_profile_photos'] = json_encode(input('photos'));
    }
    
    if (empty($data)) {
        Response::badRequest('没有要更新的数据');
    }
    
    db()->update('qy_technician_list', $data, 'id = :id', ['id' => $auth['id']]);
    
    Response::success(null, '更新成功');
}
