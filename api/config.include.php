<?php
/**
 * 轻养到家 - API 配置文件
 * 
 * 复制 .env.example 为 .env 并配置数据库连接
 */

// 加载环境变量
$envFile = dirname(dirname(__FILE__)) . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '#') === 0) continue;
        if (strpos($line, '=') === false) continue;
        list($key, $value) = explode('=', $line, 2);
        $_ENV[trim($key)] = trim($value);
    }
}

// 数据库配置
define('DB_HOST', $_ENV['DB_HOST'] ?? 'localhost');
define('DB_PORT', $_ENV['DB_PORT'] ?? '3306');
define('DB_NAME', $_ENV['DB_NAME'] ?? 'qingyang');
define('DB_USER', $_ENV['DB_USER'] ?? 'root');
define('DB_PASS', $_ENV['DB_PASS'] ?? '');
define('DB_CHARSET', 'utf8mb4');

// JWT 配置
define('JWT_SECRET', $_ENV['JWT_SECRET'] ?? 'qingyang-secret-key-change-in-production');
define('JWT_EXPIRE', 86400 * 7); // 7天

// API 配置
define('API_DEBUG', $_ENV['API_DEBUG'] ?? false);

// 分成比例配置
define('COMMISSION_PLATFORM', 0.15);  // 平台 15%
define('COMMISSION_SHOP', 0.30);      // 商家 30%
define('COMMISSION_TECH', 0.55);      // 技师 55%

// 提现配置
define('WITHDRAW_MIN', 100);          // 最低提现金额
define('WITHDRAW_FEE_RATE', 0);       // 提现手续费率
