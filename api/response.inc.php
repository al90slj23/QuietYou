<?php
/**
 * 轻养到家 - API 响应工具
 * ZERO 框架规范
 */

// 防止直接访问
if (!defined('QUIETYOU')) {
    define('QUIETYOU', true);
}

/**
 * API 响应类
 */
class Response {
    /**
     * 成功响应
     */
    public static function success($data = null, $msg = 'success') {
        self::json([
            'code' => 0,
            'msg' => $msg,
            'data' => $data
        ]);
    }
    
    /**
     * 错误响应
     */
    public static function error($code, $msg, $data = null) {
        self::json([
            'code' => $code,
            'msg' => $msg,
            'data' => $data
        ]);
    }
    
    /**
     * 参数错误
     */
    public static function badRequest($msg = '参数错误', $code = 40000) {
        http_response_code(400);
        self::error($code, $msg);
    }
    
    /**
     * 未授权
     */
    public static function unauthorized($msg = '未登录或登录已过期', $code = 10001) {
        http_response_code(401);
        self::error($code, $msg);
    }
    
    /**
     * 禁止访问
     */
    public static function forbidden($msg = '无权访问', $code = 10003) {
        http_response_code(403);
        self::error($code, $msg);
    }
    
    /**
     * 资源不存在
     */
    public static function notFound($msg = '资源不存在', $code = 40400) {
        http_response_code(404);
        self::error($code, $msg);
    }
    
    /**
     * 服务器错误
     */
    public static function serverError($msg = '服务器错误', $code = 50000) {
        http_response_code(500);
        self::error($code, $msg);
    }
    
    /**
     * 输出 JSON
     */
    public static function json($data) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    /**
     * 分页响应
     */
    public static function paginate($list, $total, $page, $pageSize) {
        self::success([
            'list' => $list,
            'pagination' => [
                'total' => $total,
                'page' => $page,
                'page_size' => $pageSize,
                'total_pages' => ceil($total / $pageSize)
            ]
        ]);
    }
}

/**
 * 快捷函数
 */
function json_success($data = null, $msg = 'success') {
    Response::success($data, $msg);
}

function json_error($code, $msg, $data = null) {
    Response::error($code, $msg, $data);
}
