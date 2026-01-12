<?php
/**
 * ================================================================
 * 文件名: list.api.php
 * 创建时间: 2026-01-13
 * ================================================================
 *
 * 【文件职责】
 * 技师浏览招聘列表 API
 *
 * ================================================================
 */

require_once dirname(dirname(dirname(__FILE__))) . '/bootstrap.include.php';

// 验证技师登录
$tech = auth_technician();

// 获取参数
$page = max(1, (int)input('page', 1));
$pageSize = min(50, max(1, (int)input('page_size', 20)));
$type = input('type', ''); // 1=长期, 2=短期, 3=兼职
$offset = ($page - 1) * $pageSize;

// 构建查询条件
$where = "j.base_status = 1 AND (j.base_time_expire IS NULL OR j.base_time_expire > NOW())";
$params = [];

if ($type) {
    $where .= " AND j.base_type = ?";
    $params[] = (int)$type;
}

// 查询总数
$total = db()->fetchColumn(
    "SELECT COUNT(*) FROM qy_job_list j WHERE $where",
    $params
);

// 查询列表
$params[] = $pageSize;
$params[] = $offset;
$list = db()->fetchAll(
    "SELECT j.*, s.base_profile_name as shop_name, s.base_profile_logo as shop_logo,
            s.base_profile_address as shop_address
     FROM qy_job_list j
     LEFT JOIN qy_shop_list s ON j.base_shop_id = s.id
     WHERE $where
     ORDER BY j.base_time_created DESC
     LIMIT ? OFFSET ?",
    $params
);

// 获取技师已申请的职位
$appliedJobs = db()->fetchAll(
    "SELECT base_job_id, base_status FROM qy_job_apply_list WHERE base_technician_id = ?",
    [$tech['id']]
);
$appliedMap = [];
foreach ($appliedJobs as $apply) {
    $appliedMap[$apply['base_job_id']] = $apply['base_status'];
}

// 格式化数据
$formattedList = array_map(function($item) use ($appliedMap) {
    $typeNames = [1 => '长期聘用', 2 => '短期借调', 3 => '兼职'];
    $salaryTypes = [1 => '月', 2 => '日', 3 => '时', 4 => '次'];
    
    return [
        'id' => $item['id'],
        'title' => $item['base_title'],
        'type' => (int)$item['base_type'],
        'type_name' => $typeNames[$item['base_type']] ?? '',
        'description' => $item['base_description'],
        'salary' => [
            'min' => (float)$item['base_salary_min'],
            'max' => (float)$item['base_salary_max'],
            'type' => (int)$item['base_salary_type'],
            'type_name' => $salaryTypes[$item['base_salary_type']] ?? ''
        ],
        'require' => [
            'gender' => (int)$item['base_require_gender'],
            'experience' => (int)$item['base_require_experience'],
            'certified' => (bool)$item['base_require_certified']
        ],
        'count' => (int)$item['base_count'],
        'shop' => [
            'id' => $item['base_shop_id'],
            'name' => $item['shop_name'],
            'logo' => $item['shop_logo'],
            'address' => $item['shop_address']
        ],
        'expire_at' => $item['base_time_expire'],
        'created_at' => $item['base_time_created'],
        'apply_status' => $appliedMap[$item['id']] ?? null
    ];
}, $list);

Response::success([
    'list' => $formattedList,
    'pagination' => [
        'total' => (int)$total,
        'page' => $page,
        'page_size' => $pageSize,
        'total_pages' => ceil($total / $pageSize)
    ]
]);
