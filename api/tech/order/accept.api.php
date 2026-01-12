<?php
/**
 * ================================================================
 * 文件名: accept.api.php
 * 创建时间: 2026-01-13
 * ================================================================
 *
 * 【文件职责】
 * 技师接单接口
 *
 * ================================================================
 */

require_once dirname(dirname(dirname(__FILE__))) . '/bootstrap.include.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    Response::badRequest('请求方法不允许');
}

$tech = auth_technician();

$orderId = input('id');
if (!$orderId) {
    Response::badRequest('订单ID不能为空');
}

$order = db()->fetch(
    "SELECT * FROM qy_order_list WHERE id = ? AND base_technician_id = ? AND is_deleted = 0",
    [$orderId, $tech['id']]
);

if (!$order) {
    Response::notFound('订单不存在');
}

if ($order['base_status'] !== 'pending') {
    Response::badRequest('订单状态不允许接单');
}

db()->update(
    'qy_order_list',
    [
        'base_status' => 'accepted',
        'base_accepted_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s')
    ],
    'id = :id',
    ['id' => $orderId]
);

Response::success(null, '接单成功');
