<?php
/**
 * ================================================================
 * 文件名: list.api.php
 * 创建时间: 2026-01-13
 * ================================================================
 *
 * 【文件职责】
 * 商户招聘列表 API
 *
 * ================================================================
 */

require_once dirname(dirname(dirname(__FILE__))) . '/bootstrap.include.php';

// 验证商户登录
$merchant = auth_merchant();

// 获取参数
$page = max(1, (int)input('page', 1));
$pageSize = min(50, max(1, (int)input('page_size', 20)));
$status = input('status', '');
$offset = ($page - 1) * $pageSize;

// 构建查询条件
$where = "base_shop_id = ?";
$params = [$merchant['id']];

if ($status !== '') {
    $where .= " AND base_status = ?";
    $params[] = (int)$status;
}

// 查询总数
$total = db()->fetchColumn(
    "SELECT COUNT(*) FROM qy_job_list WHERE $where",
    $params
);

// 查询列表
$params[] = $pageSize;
$params[] = $offset;
$list = db()->fetchAll(
    "SELECT * FROM qy_job_list WHERE $where ORDER BY base_time_created DESC LIMIT ? OFFSET ?",
    $params
);

// 获取每个招聘的申请数
$jobIds = array_column($list, 'id');
$applyCounts = [];
if ($jobIds) {
    $placeholders = implode(',', array_fill(0, count($jobIds), '?'));
    $counts = db()->fetchAll(
        "SELECT base_job_id, COUNT(*) as count FROM qy_job_apply_list 
         WHERE base_job_id IN ($placeholders) GROUP BY base_job_id",
        $jobIds
    );
    foreach ($counts as $c) {
        $applyCounts[$c['base_job_id']] = (int)$c['count'];
    }
}

$typeNames = [1 => '长期聘用', 2 => '短期借调', 3 => '兼职'];
$salaryTypes = [1 => '月', 2 => '日', 3 => '时', 4 => '次'];

$formattedList = array_map(function($item) use ($typeNames, $salaryTypes, $applyCounts) {
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
        'count' => (int)$item['base_count'],
        'apply_count' => $applyCounts[$item['id']] ?? 0,
        'status' => (int)$item['base_status'],
        'expire_at' => $item['base_time_expire'],
        'created_at' => $item['base_time_created']
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
