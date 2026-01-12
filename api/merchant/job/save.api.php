<?php
/**
 * ================================================================
 * 文件名: save.api.php
 * 创建时间: 2026-01-13
 * ================================================================
 *
 * 【文件职责】
 * 商户保存招聘信息 API
 *
 * ================================================================
 */

require_once dirname(dirname(dirname(__FILE__))) . '/bootstrap.include.php';

// 验证商户登录
$merchant = auth_merchant();

// 验证必填参数
validate_required(['title', 'type', 'salary_min', 'salary_max', 'salary_type']);

$id = input('id');
$title = trim(input('title'));
$type = (int)input('type');
$description = input('description', '');
$salaryMin = (float)input('salary_min');
$salaryMax = (float)input('salary_max');
$salaryType = (int)input('salary_type');
$requireGender = (int)input('require_gender', 0);
$requireExperience = (int)input('require_experience', 0);
$requireCertified = (int)input('require_certified', 0);
$count = max(1, (int)input('count', 1));
$expireAt = input('expire_at');

if (mb_strlen($title) < 2 || mb_strlen($title) > 50) {
    Response::badRequest('标题长度应在2-50字之间');
}

if (!in_array($type, [1, 2, 3])) {
    Response::badRequest('无效的招聘类型');
}

if ($salaryMin < 0 || $salaryMax < $salaryMin) {
    Response::badRequest('薪资范围不正确');
}

if ($id) {
    // 更新
    $existing = db()->fetch(
        "SELECT * FROM qy_job_list WHERE id = ? AND base_shop_id = ?",
        [$id, $merchant['id']]
    );
    
    if (!$existing) {
        Response::notFound('招聘信息不存在');
    }
    
    db()->execute(
        "UPDATE qy_job_list SET 
         base_title = ?, base_type = ?, base_description = ?,
         base_salary_min = ?, base_salary_max = ?, base_salary_type = ?,
         base_require_gender = ?, base_require_experience = ?, base_require_certified = ?,
         base_count = ?, base_time_expire = ?
         WHERE id = ?",
        [$title, $type, $description, $salaryMin, $salaryMax, $salaryType,
         $requireGender, $requireExperience, $requireCertified, $count, $expireAt, $id]
    );
} else {
    // 新增
    db()->execute(
        "INSERT INTO qy_job_list 
         (base_shop_id, base_title, base_type, base_description,
          base_salary_min, base_salary_max, base_salary_type,
          base_require_gender, base_require_experience, base_require_certified,
          base_count, base_status, base_time_expire)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, ?)",
        [$merchant['id'], $title, $type, $description, $salaryMin, $salaryMax, $salaryType,
         $requireGender, $requireExperience, $requireCertified, $count, $expireAt]
    );
    $id = db()->lastInsertId();
}

Response::success(['id' => $id], '保存成功');
