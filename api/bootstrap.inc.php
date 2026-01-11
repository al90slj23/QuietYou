<?php
/**
 * 轻养到家 - API 引导文件
 * ZERO 框架规范
 * 
 * 所有 API 入口文件应首先引入此文件
 */

// 定义常量
define('QUIETYOU', true);
define('API_ROOT', __DIR__);
define('PROJECT_ROOT', dirname(__DIR__));

// 错误报告（生产环境应关闭）
error_reporting(E_ALL);
ini_set('display_errors', getenv('APP_DEBUG') === 'true' ? '1' : '0');

// 设置时区
date_default_timezone_set('Asia/Shanghai');

// 加载环境变量
$envFile = PROJECT_ROOT . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '#') === 0) continue;
        if (strpos($line, '=') === false) continue;
        list($key, $value) = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);
        if (!getenv($key)) {
            putenv("$key=$value");
        }
    }
}

// 加载核心文件
require_once API_ROOT . '/db.inc.php';
require_once API_ROOT . '/auth.inc.php';
require_once API_ROOT . '/response.inc.php';

// CORS 处理
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// 处理 OPTIONS 预检请求
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

/**
 * 获取请求参数
 */
function input($key = null, $default = null) {
    static $input = null;
    
    if ($input === null) {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        
        if (strpos($contentType, 'application/json') !== false) {
            $input = json_decode(file_get_contents('php://input'), true) ?: [];
        } else {
            $input = array_merge($_GET, $_POST);
        }
    }
    
    if ($key === null) {
        return $input;
    }
    
    return $input[$key] ?? $default;
}

/**
 * 验证必填参数
 */
function validate_required($fields) {
    $input = input();
    $missing = [];
    
    foreach ((array)$fields as $field) {
        if (!isset($input[$field]) || $input[$field] === '') {
            $missing[] = $field;
        }
    }
    
    if (!empty($missing)) {
        Response::badRequest('缺少必填参数: ' . implode(', ', $missing));
    }
    
    return true;
}

/**
 * 验证手机号格式
 */
function validate_phone($phone) {
    return preg_match('/^1[3-9]\d{9}$/', $phone);
}

/**
 * 生成订单号
 */
function generate_order_no() {
    return date('YmdHis') . str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);
}
