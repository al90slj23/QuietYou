<?php
/**
 * 轻养到家 - 数据库连接
 * ZERO 框架规范
 */

// 防止直接访问
if (!defined('QUIETYOU')) {
    define('QUIETYOU', true);
}

/**
 * 数据库配置
 */
class DBConfig {
    // 从环境变量或默认值获取配置
    public static function get($key, $default = '') {
        return getenv($key) ?: $default;
    }
    
    public static function host() {
        return self::get('DB_HOST', 'localhost');
    }
    
    public static function port() {
        return self::get('DB_PORT', '3306');
    }
    
    public static function name() {
        return self::get('DB_NAME', 'quietyou');
    }
    
    public static function user() {
        return self::get('DB_USER', 'root');
    }
    
    public static function pass() {
        return self::get('DB_PASS', '');
    }
    
    public static function charset() {
        return 'utf8mb4';
    }
}

/**
 * 数据库连接类
 */
class DB {
    private static $instance = null;
    private $pdo = null;
    
    private function __construct() {
        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=%s',
            DBConfig::host(),
            DBConfig::port(),
            DBConfig::name(),
            DBConfig::charset()
        );
        
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
        ];
        
        try {
            $this->pdo = new PDO($dsn, DBConfig::user(), DBConfig::pass(), $options);
        } catch (PDOException $e) {
            throw new Exception('数据库连接失败: ' . $e->getMessage());
        }
    }
    
    /**
     * 获取单例实例
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * 获取 PDO 实例
     */
    public function getPdo() {
        return $this->pdo;
    }
    
    /**
     * 执行查询
     */
    public function query($sql, $params = []) {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
    
    /**
     * 获取单条记录
     */
    public function fetch($sql, $params = []) {
        return $this->query($sql, $params)->fetch();
    }
    
    /**
     * 获取多条记录
     */
    public function fetchAll($sql, $params = []) {
        return $this->query($sql, $params)->fetchAll();
    }
    
    /**
     * 插入记录
     */
    public function insert($table, $data) {
        $fields = array_keys($data);
        $placeholders = array_map(function($f) { return ':' . $f; }, $fields);
        
        $sql = sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            $table,
            implode(', ', $fields),
            implode(', ', $placeholders)
        );
        
        $this->query($sql, $data);
        return $this->pdo->lastInsertId();
    }
    
    /**
     * 更新记录
     */
    public function update($table, $data, $where, $whereParams = []) {
        $sets = [];
        foreach ($data as $field => $value) {
            $sets[] = "$field = :$field";
        }
        
        $sql = sprintf(
            'UPDATE %s SET %s WHERE %s',
            $table,
            implode(', ', $sets),
            $where
        );
        
        return $this->query($sql, array_merge($data, $whereParams))->rowCount();
    }
    
    /**
     * 删除记录
     */
    public function delete($table, $where, $params = []) {
        $sql = sprintf('DELETE FROM %s WHERE %s', $table, $where);
        return $this->query($sql, $params)->rowCount();
    }
    
    /**
     * 开始事务
     */
    public function beginTransaction() {
        return $this->pdo->beginTransaction();
    }
    
    /**
     * 提交事务
     */
    public function commit() {
        return $this->pdo->commit();
    }
    
    /**
     * 回滚事务
     */
    public function rollback() {
        return $this->pdo->rollBack();
    }
    
    /**
     * 获取最后插入的 ID
     */
    public function lastInsertId() {
        return $this->pdo->lastInsertId();
    }
}

/**
 * 快捷函数：获取数据库实例
 */
function db() {
    return DB::getInstance();
}
