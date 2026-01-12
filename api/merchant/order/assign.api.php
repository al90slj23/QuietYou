<?php
/**
 * ================================================================
 * 文件名: assign.api.php
 * 创建时间: 2026-01-13
 * ================================================================
 *
 * 【文件职责】
 * 商户指派技师 API
 *
 * ================================================================
 */

require_once dirname(dirname(dirname(__FILE__))) . '/bootstrap.include.php';

// 验证商户登录
$merchant = auth_merchant();

// 验证必填参数
validate_required(['order_id', 'technician_id']);

$orderId = (int)input('order_id');
$technicianId = (int)input('technician_id');

// 查询订单
$order = db()->fetch(
    "SELECT * FROM qy_order_list WHERE id = ? AND base_shop_id = ?",
    [$orderId, $merchant['id']]
);

if (!$order) {
    Response::notFound('订单不存在');
}

if ($order['base_status_order'] != 1) {
    Response::badRequest('只能为待接单的订单指派技师');
}

// 查询技师
$tech = db()->fetch(
    "SELECT * FROM qy_technician_list WHERE id = ? AND base_shop_id = ? AND base_status_active = 1",
    [$technicianId, $merchant['id']]
);

if (!$tech) {
    Response::notFound('技师不存在或不属于本店');
}

if ($tech['base_status_online'] == 2) {
    Response::badRequest('该技师正在服务中');
}

// 指派技师
db()->execute(
    "UPDATE qy_order_list SET base_technician_id = ?, base_status_order = 2 WHERE id = ?",
    [$technicianId, $orderId]
);

Response::success(null, '指派成功');
