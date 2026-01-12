<?php
/**
 * ================================================================
 * 文件名: info.api.php
 * 创建时间: 2026-01-13
 * ================================================================
 *
 * 【文件职责】
 * 获取技师认证状态 API
 *
 * ================================================================
 */

require_once dirname(dirname(dirname(__FILE__))) . '/bootstrap.include.php';

// 验证技师登录
$tech = auth_technician();

Response::success([
    'is_certified' => (bool)$tech['base_is_certified'],
    'verify_status' => (int)$tech['base_status_verify'], // 0=待审核, 1=已通过, 2=已拒绝
    'idcard_front' => $tech['base_cert_idcard_front'],
    'idcard_back' => $tech['base_cert_idcard_back'],
    'health' => $tech['base_cert_health'],
    'professional' => $tech['base_cert_professional']
]);
