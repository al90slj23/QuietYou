<?php
/**
 * ================================================================
 * 文件名: info.api.php
 * 创建时间: 2026-01-13
 * ================================================================
 *
 * 【文件职责】
 * 获取技师接单设置 API
 *
 * ================================================================
 */

require_once dirname(dirname(dirname(__FILE__))) . '/bootstrap.include.php';

// 验证技师登录
$tech = auth_technician();

Response::success([
    'accept_order' => (bool)$tech['base_setting_accept_order'],
    'service_range' => (int)$tech['base_setting_service_range'],
    'online_status' => (int)$tech['base_status_online']
]);
