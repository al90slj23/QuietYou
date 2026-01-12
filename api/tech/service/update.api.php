<?php
/**
 * ================================================================
 * 文件名: update.api.php
 * 创建时间: 2026-01-13
 * ================================================================
 *
 * 【文件职责】
 * 技师更新/添加服务项目 API
 *
 * ================================================================
 */

require_once dirname(dirname(dirname(__FILE__))) . '/bootstrap.include.php';

// 验证技师登录
$tech = auth_technician();

// 验证必填参数
validate_required(['service_id', 'price']);

$serviceId = (int)input('service_id');
$price = (float)input('price');
$isActive = input('is_active', 1);

if ($price <= 0) {
    Response::badRequest('价格必须大于0');
}

// 验证服务是否存在
$service = db()->fetch(
    "SELECT * FROM qy_service_item_list WHERE id = ? AND base_status_active = 1",
    [$serviceId]
);

if (!$service) {
    Response::notFound('服务项目不存在');
}

// 检查是否已添加
$existing = db()->fetch(
    "SELECT * FROM qy_technician_service_list WHERE base_technician_id = ? AND base_service_id = ?",
    [$tech['id'], $serviceId]
);

if ($existing) {
    // 更新
    db()->execute(
        "UPDATE qy_technician_service_list 
         SET base_price = ?, base_status_active = ? 
         WHERE id = ?",
        [$price, $isActive, $existing['id']]
    );
} else {
    // 新增
    db()->execute(
        "INSERT INTO qy_technician_service_list 
         (base_technician_id, base_service_id, base_price, base_status_active) 
         VALUES (?, ?, ?, ?)",
        [$tech['id'], $serviceId, $price, $isActive]
    );
}

Response::success(null, '保存成功');
