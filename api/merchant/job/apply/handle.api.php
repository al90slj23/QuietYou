<?php
/**
 * ================================================================
 * 文件名: handle.api.php
 * 创建时间: 2026-01-13
 * ================================================================
 *
 * 【文件职责】
 * 商户处理招聘申请 API
 *
 * ================================================================
 */

require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/bootstrap.include.php';

// 验证商户登录
$merchant = auth_merchant();

// 验证必填参数
validate_required(['apply_id', 'action']);

$applyId = (int)input('apply_id');
$action = input('action'); // accept, reject
$reply = input('reply', '');

if (!in_array($action, ['accept', 'reject'])) {
    Response::badRequest('无效的操作');
}

// 查询申请
$apply = db()->fetch(
    "SELECT a.*, j.base_shop_id 
     FROM qy_job_apply_list a
     LEFT JOIN qy_job_list j ON a.base_job_id = j.id
     WHERE a.id = ?",
    [$applyId]
);

if (!$apply || $apply['base_shop_id'] != $merchant['id']) {
    Response::notFound('申请不存在');
}

if ($apply['base_status'] != 0) {
    Response::badRequest('该申请已处理');
}

$newStatus = $action === 'accept' ? 1 : 2;

db()->execute(
    "UPDATE qy_job_apply_list SET base_status = ?, base_reply = ? WHERE id = ?",
    [$newStatus, $reply, $applyId]
);

// 如果通过，可以选择直接添加技师到店铺
if ($action === 'accept') {
    $addToShop = input('add_to_shop', false);
    if ($addToShop) {
        $tech = db()->fetch(
            "SELECT * FROM qy_technician_list WHERE id = ?",
            [$apply['base_technician_id']]
        );
        
        if ($tech && $tech['base_shop_id'] == 0) {
            db()->execute(
                "UPDATE qy_technician_list SET base_shop_id = ?, base_type = 1 WHERE id = ?",
                [$merchant['id'], $tech['id']]
            );
            db()->execute(
                "UPDATE qy_shop_list SET base_stat_technician_count = base_stat_technician_count + 1 WHERE id = ?",
                [$merchant['id']]
            );
        }
    }
}

Response::success(null, $action === 'accept' ? '已通过申请' : '已拒绝申请');
