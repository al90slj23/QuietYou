<?php
/**
 * ================================================================
 * 文件名: complete.api.php
 * 创建时间: 2026-01-13
 * ================================================================
 *
 * 【文件职责】
 * 技师完成服务接口
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

if ($order['base_status'] !== 'serving') {
    Response::badRequest('订单状态不允许完成');
}

$now = date('Y-m-d H:i:s');
$startedAt = $order['base_started_at'];
$duration = $startedAt ? round((strtotime($now) - strtotime($startedAt)) / 60) : 0;

db()->beginTransaction();

try {
    // 更新订单状态
    db()->update(
        'qy_order_list',
        [
            'base_status' => 'completed',
            'base_completed_at' => $now,
            'base_service_duration' => $duration,
            'updated_at' => $now
        ],
        'id = :id',
        ['id' => $orderId]
    );
    
    // 更新技师状态为在线
    db()->update(
        'qy_technician_list',
        ['base_status' => 'online', 'updated_at' => $now],
        'id = :id',
        ['id' => $tech['id']]
    );
    
    // 更新技师订单统计
    db()->query(
        "UPDATE qy_technician_list SET 
            base_stat_order_count = base_stat_order_count + 1,
            updated_at = ?
         WHERE id = ?",
        [$now, $tech['id']]
    );
    
    // 创建收入记录
    db()->insert('qy_income_list', [
        'base_type' => 'order',
        'base_user_type' => 'technician',
        'base_user_id' => $tech['id'],
        'base_order_id' => $orderId,
        'base_amount' => $order['base_price_tech'],
        'base_status' => 'pending',
        'created_at' => $now,
        'updated_at' => $now
    ]);
    
    db()->commit();
} catch (Exception $e) {
    db()->rollback();
    Response::serverError('操作失败');
}

Response::success(null, '服务已完成');
