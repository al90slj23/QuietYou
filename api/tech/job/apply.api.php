<?php
/**
 * ================================================================
 * 文件名: apply.api.php
 * 创建时间: 2026-01-13
 * ================================================================
 *
 * 【文件职责】
 * 技师申请职位 API
 *
 * ================================================================
 */

require_once dirname(dirname(dirname(__FILE__))) . '/bootstrap.include.php';

// 验证技师登录
$tech = auth_technician();

// 验证必填参数
validate_required(['job_id']);

$jobId = (int)input('job_id');
$message = input('message', '');

// 查询职位
$job = db()->fetch(
    "SELECT * FROM qy_job_list WHERE id = ? AND base_status = 1",
    [$jobId]
);

if (!$job) {
    Response::notFound('职位不存在或已关闭');
}

// 检查是否已过期
if ($job['base_time_expire'] && strtotime($job['base_time_expire']) < time()) {
    Response::badRequest('该职位已过期');
}

// 检查是否已申请
$existing = db()->fetch(
    "SELECT * FROM qy_job_apply_list WHERE base_job_id = ? AND base_technician_id = ?",
    [$jobId, $tech['id']]
);

if ($existing) {
    if ($existing['base_status'] == 3) {
        // 已撤回，可以重新申请
        db()->execute(
            "UPDATE qy_job_apply_list SET base_status = 0, base_message = ? WHERE id = ?",
            [$message, $existing['id']]
        );
    } else {
        Response::badRequest('您已申请过该职位');
    }
} else {
    // 检查要求
    if ($job['base_require_gender'] && $job['base_require_gender'] != $tech['base_profile_gender']) {
        Response::badRequest('您不符合性别要求');
    }
    
    if ($job['base_require_experience'] > $tech['base_profile_experience_years']) {
        Response::badRequest('您的从业经验不足');
    }
    
    if ($job['base_require_certified'] && !$tech['base_is_certified']) {
        Response::badRequest('该职位要求平台认证');
    }
    
    // 创建申请
    db()->execute(
        "INSERT INTO qy_job_apply_list (base_job_id, base_technician_id, base_message, base_status) 
         VALUES (?, ?, ?, 0)",
        [$jobId, $tech['id'], $message]
    );
}

Response::success(null, '申请已提交');
