<?php
/**
 * ================================================================
 * 文件名: start.api.php
 * 创建时间: 2026-01-13
 * ================================================================
 *
 * 【文件职责】
 * 技师开始服务接口
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

if ($order['base_status'] !== 'accepted') {
    Response::badRequest('订单状态不允许开始服务');
}

db()->update(
    'qy_order_list',
    [
        'base_status' => 'serving',
        'base_started_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s')
    ],
    'id = :id',
    ['id' => $orderId]
);

// 更新技师状态为服务中
db()->update(
    'qy_technician_list',
    ['base_status' => 'serving', 'updated_at' => date('Y-m-d H:i:s')],
    'id = :id',
    ['id' => $tech['id']]
);

Response::success(null, '已开始服务');
