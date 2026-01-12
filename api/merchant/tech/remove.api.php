<?php
/**
 * ================================================================
 * 文件名: remove.api.php
 * 创建时间: 2026-01-13
 * ================================================================
 *
 * 【文件职责】
 * 商户移除技师 API
 *
 * ================================================================
 */

require_once dirname(dirname(dirname(__FILE__))) . '/bootstrap.include.php';

// 验证商户登录
$merchant = auth_merchant();

// 验证必填参数
validate_required(['technician_id']);

$technicianId = (int)input('technician_id');

// 查询技师
$tech = db()->fetch(
    "SELECT * FROM qy_technician_list WHERE id = ? AND base_shop_id = ?",
    [$technicianId, $merchant['id']]
);

if (!$tech) {
    Response::notFound('技师不存在或不属于本店');
}

// 检查是否有进行中的订单
$ongoingOrders = db()->fetchColumn(
    "SELECT COUNT(*) FROM qy_order_list 
     WHERE base_technician_id = ? AND base_shop_id = ? AND base_status_order IN (1, 2, 3, 4)",
    [$technicianId, $merchant['id']]
);

if ($ongoingOrders > 0) {
    Response::badRequest('该技师有进行中的订单，无法移除');
}

// 移除技师（变为散技师）
db()->execute(
    "UPDATE qy_technician_list SET base_shop_id = 0, base_type = 2 WHERE id = ?",
    [$technicianId]
);

// 更新店铺技师数量
db()->execute(
    "UPDATE qy_shop_list SET base_stat_technician_count = GREATEST(0, base_stat_technician_count - 1) WHERE id = ?",
    [$merchant['id']]
);

Response::success(null, '移除成功');
