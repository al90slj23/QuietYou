<?php
/**
 * ================================================================
 * 文件名: apply.api.php
 * 创建时间: 2026-01-13
 * ================================================================
 *
 * 【文件职责】
 * 技师申请认证 API
 *
 * ================================================================
 */

require_once dirname(dirname(dirname(__FILE__))) . '/bootstrap.include.php';

// 验证技师登录
$tech = auth_technician();

// 检查是否已认证
if ($tech['base_is_certified']) {
    Response::badRequest('您已通过认证');
}

// 检查是否正在审核中
if ($tech['base_status_verify'] == 0) {
    Response::badRequest('您的认证申请正在审核中');
}

// 验证必填参数
validate_required(['idcard_front', 'idcard_back']);

$idcardFront = input('idcard_front');
$idcardBack = input('idcard_back');
$health = input('health', '');
$professional = input('professional', '');

// 更新认证资料
db()->execute(
    "UPDATE qy_technician_list SET 
     base_cert_idcard_front = ?,
     base_cert_idcard_back = ?,
     base_cert_health = ?,
     base_cert_professional = ?,
     base_status_verify = 0
     WHERE id = ?",
    [$idcardFront, $idcardBack, $health, $professional, $tech['id']]
);

Response::success(null, '认证申请已提交，请等待审核');
