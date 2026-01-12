<?php
/**
 * ================================================================
 * 文件名: borrow.api.php
 * 创建时间: 2026-01-13
 * ================================================================
 *
 * 【文件职责】
 * 商户借调技师 API
 *
 * ================================================================
 */

require_once dirname(dirname(dirname(__FILE__))) . '/bootstrap.include.php';

// 验证商户登录
$merchant = auth_merchant();

// 验证必填参数
validate_required(['technician_id']);

$technicianId = (int)input('technician_id');
$remark = input('remark', '');

// 查询技师
$tech = db()->fetch(
    "SELECT t.*, s.base_profile_name as shop_name 
     FROM qy_technician_list t
     LEFT JOIN qy_shop_list s ON t.base_shop_id = s.id
     WHERE t.id = ? AND t.base_status_active = 1",
    [$technicianId]
);

if (!$tech) {
    Response::notFound('技师不存在');
}

if ($tech['base_shop_id'] == $merchant['id']) {
    Response::badRequest('不能借调自己店铺的技师');
}

if ($tech['base_shop_id'] == 0) {
    Response::badRequest('散技师无需借调，可直接邀请加入');
}

if ($tech['base_status_online'] == 2) {
    Response::badRequest('该技师正在服务中');
}

// 检查是否已有待处理的借调申请
$existing = db()->fetch(
    "SELECT * FROM qy_borrow_list 
     WHERE base_technician_id = ? AND base_to_shop_id = ? AND base_status IN (0, 1, 2)",
    [$technicianId, $merchant['id']]
);

if ($existing) {
    Response::badRequest('已有进行中的借调申请');
}

// 创建借调申请
db()->execute(
    "INSERT INTO qy_borrow_list 
     (base_technician_id, base_from_shop_id, base_to_shop_id, base_status, base_remark) 
     VALUES (?, ?, ?, 0, ?)",
    [$technicianId, $tech['base_shop_id'], $merchant['id'], $remark]
);

Response::success(null, '借调申请已提交，等待对方确认');
