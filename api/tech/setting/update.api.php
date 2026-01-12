<?php
/**
 * ================================================================
 * 文件名: update.api.php
 * 创建时间: 2026-01-13
 * ================================================================
 *
 * 【文件职责】
 * 技师更新接单设置 API
 *
 * ================================================================
 */

require_once dirname(dirname(dirname(__FILE__))) . '/bootstrap.include.php';

// 验证技师登录
$tech = auth_technician();

// 获取参数
$acceptOrder = input('accept_order');
$serviceRange = input('service_range');
$onlineStatus = input('online_status');

$updates = [];
$params = [];

// 接单开关
if ($acceptOrder !== null) {
    $updates[] = "base_setting_accept_order = ?";
    $params[] = (int)$acceptOrder;
}

// 服务范围
if ($serviceRange !== null) {
    $range = (int)$serviceRange;
    if ($range < 1 || $range > 50) {
        Response::badRequest('服务范围应在1-50公里之间');
    }
    $updates[] = "base_setting_service_range = ?";
    $params[] = $range;
}

// 在线状态
if ($onlineStatus !== null) {
    $status = (int)$onlineStatus;
    if (!in_array($status, [0, 1, 3])) { // 0=离线, 1=在线, 3=休息
        Response::badRequest('无效的在线状态');
    }
    $updates[] = "base_status_online = ?";
    $params[] = $status;
}

if (empty($updates)) {
    Response::badRequest('没有需要更新的内容');
}

$params[] = $tech['id'];
db()->execute(
    "UPDATE qy_technician_list SET " . implode(', ', $updates) . " WHERE id = ?",
    $params
);

Response::success(null, '设置已更新');
