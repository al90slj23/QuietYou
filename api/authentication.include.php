<?php
/**
 * 轻养到家 - 认证函数库
 * ZERO 框架规范
 * 
 * Requirements: 1.3, 1.4
 * - Session Token 持久化
 * - Token 过期处理
 */

// 防止直接访问
if (!defined('QUIETYOU')) {
    define('QUIETYOU', true);
}

/**
 * JWT 配置
 */
class AuthConfig {
    // JWT 密钥
    public static function secret() {
        return getenv('JWT_SECRET') ?: 'quietyou-default-secret-key-change-in-production';
    }
    
    // Token 有效期（秒）- 默认 7 天
    public static function expiry() {
        return (int)(getenv('JWT_EXPIRY') ?: 604800);
    }
    
    // 刷新 Token 有效期（秒）- 默认 30 天
    public static function refreshExpiry() {
        return (int)(getenv('JWT_REFRESH_EXPIRY') ?: 2592000);
    }
}

/**
 * Token 类型枚举
 */
class TokenType {
    const USER = 'user';
    const TECHNICIAN = 'technician';
    const SHOP = 'shop';
    const ADMIN = 'admin';
}

/**
 * 认证类
 */
class Auth {
    /**
     * Base64 URL 安全编码
     */
    private static function base64UrlEncode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
    
    /**
     * Base64 URL 安全解码
     */
    private static function base64UrlDecode($data) {
        return base64_decode(strtr($data, '-_', '+/'));
    }
    
    /**
     * 生成 JWT Token
     * 
     * @param int $userId 用户ID
     * @param string $type Token 类型（user/technician/shop/admin）
     * @param array $extra 额外数据
     * @return string JWT Token
     */
    public static function generateToken($userId, $type = TokenType::USER, $extra = []) {
        $header = [
            'alg' => 'HS256',
            'typ' => 'JWT'
        ];
        
        $now = time();
        $payload = array_merge([
            'iss' => 'quietyou',           // 签发者
            'sub' => $userId,               // 主题（用户ID）
            'type' => $type,                // Token 类型
            'iat' => $now,                  // 签发时间
            'exp' => $now + AuthConfig::expiry(), // 过期时间
            'jti' => self::generateJti()    // 唯一标识
        ], $extra);
        
        $headerEncoded = self::base64UrlEncode(json_encode($header));
        $payloadEncoded = self::base64UrlEncode(json_encode($payload));
        
        $signature = hash_hmac(
            'sha256',
            "$headerEncoded.$payloadEncoded",
            AuthConfig::secret(),
            true
        );
        $signatureEncoded = self::base64UrlEncode($signature);
        
        return "$headerEncoded.$payloadEncoded.$signatureEncoded";
    }
    
    /**
     * 生成刷新 Token
     */
    public static function generateRefreshToken($userId, $type = TokenType::USER) {
        return self::generateToken($userId, $type, [
            'exp' => time() + AuthConfig::refreshExpiry(),
            'refresh' => true
        ]);
    }
    
    /**
     * 生成唯一标识
     */
    private static function generateJti() {
        return bin2hex(random_bytes(16));
    }
    
    /**
     * 验证 Token
     * 
     * @param string $token JWT Token
     * @return array|false 解码后的 payload 或 false
     */
    public static function verifyToken($token) {
        if (empty($token)) {
            return false;
        }
        
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return false;
        }
        
        list($headerEncoded, $payloadEncoded, $signatureEncoded) = $parts;
        
        // 验证签名
        $expectedSignature = self::base64UrlEncode(
            hash_hmac(
                'sha256',
                "$headerEncoded.$payloadEncoded",
                AuthConfig::secret(),
                true
            )
        );
        
        if (!hash_equals($expectedSignature, $signatureEncoded)) {
            return false;
        }
        
        // 解码 payload
        $payload = json_decode(self::base64UrlDecode($payloadEncoded), true);
        if (!$payload) {
            return false;
        }
        
        // 验证过期时间
        if (isset($payload['exp']) && $payload['exp'] < time()) {
            return false;
        }
        
        return $payload;
    }
    
    /**
     * 从请求头获取 Token
     */
    public static function getTokenFromHeader() {
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';
        
        if (preg_match('/^Bearer\s+(.+)$/i', $authHeader, $matches)) {
            return $matches[1];
        }
        
        return null;
    }
    
    /**
     * 获取当前认证用户
     * 
     * @return array|null 用户信息或 null
     */
    public static function getCurrentUser() {
        $token = self::getTokenFromHeader();
        if (!$token) {
            return null;
        }
        
        $payload = self::verifyToken($token);
        if (!$payload) {
            return null;
        }
        
        return [
            'id' => $payload['sub'],
            'type' => $payload['type'],
            'payload' => $payload
        ];
    }
    
    /**
     * 要求认证
     * 如果未认证，返回 401 错误
     * 
     * @param string|array $allowedTypes 允许的 Token 类型
     * @return array 用户信息
     */
    public static function requireAuth($allowedTypes = null) {
        $user = self::getCurrentUser();
        
        if (!$user) {
            self::unauthorized('未登录或登录已过期');
        }
        
        if ($allowedTypes !== null) {
            $allowedTypes = (array)$allowedTypes;
            if (!in_array($user['type'], $allowedTypes)) {
                self::forbidden('无权访问');
            }
        }
        
        return $user;
    }
    
    /**
     * 返回未认证错误
     */
    public static function unauthorized($message = '未授权') {
        http_response_code(401);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'code' => 10001,
            'msg' => $message,
            'data' => null
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    /**
     * 返回禁止访问错误
     */
    public static function forbidden($message = '禁止访问') {
        http_response_code(403);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'code' => 10003,
            'msg' => $message,
            'data' => null
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    /**
     * 检查 Token 是否即将过期（剩余时间小于 1 天）
     */
    public static function isTokenExpiringSoon($token) {
        $payload = self::verifyToken($token);
        if (!$payload) {
            return true;
        }
        
        $remainingTime = $payload['exp'] - time();
        return $remainingTime < 86400; // 小于 1 天
    }
    
    /**
     * 刷新 Token
     * 
     * @param string $refreshToken 刷新 Token
     * @return array|false 新的 Token 对或 false
     */
    public static function refreshToken($refreshToken) {
        $payload = self::verifyToken($refreshToken);
        
        if (!$payload || empty($payload['refresh'])) {
            return false;
        }
        
        $userId = $payload['sub'];
        $type = $payload['type'];
        
        return [
            'token' => self::generateToken($userId, $type),
            'refresh_token' => self::generateRefreshToken($userId, $type)
        ];
    }
}

/**
 * 验证码管理类
 */
class VerifyCode {
    // 验证码有效期（秒）
    const EXPIRY = 300; // 5 分钟
    
    // 发送间隔（秒）
    const INTERVAL = 60;
    
    /**
     * 生成验证码
     */
    public static function generate($length = 6) {
        $code = '';
        for ($i = 0; $i < $length; $i++) {
            $code .= mt_rand(0, 9);
        }
        return $code;
    }
    
    /**
     * 存储验证码（使用文件存储，生产环境建议使用 Redis）
     */
    public static function store($phone, $code) {
        $cacheDir = sys_get_temp_dir() . '/quietyou_verify';
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0755, true);
        }
        
        $data = [
            'code' => $code,
            'phone' => $phone,
            'created_at' => time(),
            'expires_at' => time() + self::EXPIRY
        ];
        
        $file = $cacheDir . '/' . md5($phone) . '.json';
        file_put_contents($file, json_encode($data));
        
        return true;
    }
    
    /**
     * 验证验证码
     */
    public static function verify($phone, $code) {
        $cacheDir = sys_get_temp_dir() . '/quietyou_verify';
        $file = $cacheDir . '/' . md5($phone) . '.json';
        
        if (!file_exists($file)) {
            return false;
        }
        
        $data = json_decode(file_get_contents($file), true);
        
        // 检查是否过期
        if ($data['expires_at'] < time()) {
            unlink($file);
            return false;
        }
        
        // 检查验证码是否匹配
        if ($data['code'] !== $code) {
            return false;
        }
        
        // 验证成功，删除验证码
        unlink($file);
        return true;
    }
    
    /**
     * 检查是否可以发送验证码（防止频繁发送）
     */
    public static function canSend($phone) {
        $cacheDir = sys_get_temp_dir() . '/quietyou_verify';
        $file = $cacheDir . '/' . md5($phone) . '.json';
        
        if (!file_exists($file)) {
            return true;
        }
        
        $data = json_decode(file_get_contents($file), true);
        
        // 检查发送间隔
        if (time() - $data['created_at'] < self::INTERVAL) {
            return false;
        }
        
        return true;
    }
    
    /**
     * 获取剩余等待时间
     */
    public static function getRemainingWaitTime($phone) {
        $cacheDir = sys_get_temp_dir() . '/quietyou_verify';
        $file = $cacheDir . '/' . md5($phone) . '.json';
        
        if (!file_exists($file)) {
            return 0;
        }
        
        $data = json_decode(file_get_contents($file), true);
        $elapsed = time() - $data['created_at'];
        
        if ($elapsed >= self::INTERVAL) {
            return 0;
        }
        
        return self::INTERVAL - $elapsed;
    }
}

/**
 * 密码工具类
 */
class Password {
    /**
     * 哈希密码
     */
    public static function hash($password) {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }
    
    /**
     * 验证密码
     */
    public static function verify($password, $hash) {
        return password_verify($password, $hash);
    }
}

/**
 * 快捷函数：获取当前用户
 */
function auth_user() {
    return Auth::getCurrentUser();
}

/**
 * 快捷函数：要求认证
 */
function auth_require($types = null) {
    return Auth::requireAuth($types);
}
