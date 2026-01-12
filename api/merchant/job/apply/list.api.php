<?php
/**
 * ================================================================
 * 文件名: list.api.php
 * 创建时间: 2026-01-13
 * ================================================================
 *
 * 【文件职责】
 * 商户查看招聘申请列表 API
 *
 * ================================================================
 */

require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/bootstrap.include.php';

// 验证商户登录
$merchant = auth_merchant();

// 验证必填参数
validate_required(['job_id']);

$jobId = (int)input('job_id');
$status = input('status', '');

// 验证招聘属于该商户
$job = db()->fetch(
    "SELECT * FROM qy_job_list WHERE id = ? AND base_shop_id = ?",
    [$jobId, $merchant['id']]
);

if (!$job) {
    Response::notFound('招聘信息不存在');
}

// 构建查询条件
$where = "a.base_job_id = ?";
$params = [$jobId];

if ($status !== '') {
    $where .= " AND a.base_status = ?";
    $params[] = (int)$status;
}

// 查询申请列表
$list = db()->fetchAll(
    "SELECT a.*, t.base_profile_realname as tech_name, t.base_profile_avatar as tech_avatar,
            t.base_profile_gender as tech_gender, t.base_profile_experience_years as tech_experience,
            t.base_is_certified as tech_certified, t.base_stat_rating_avg as tech_rating,
            t.base_auth_phone as tech_phone
     FROM qy_job_apply_list a
     LEFT JOIN qy_technician_list t ON a.base_technician_id = t.id
     WHERE $where
     ORDER BY a.base_time_created DESC",
    $params
);

$statusNames = [0 => '待处理', 1 => '已通过', 2 => '已拒绝', 3 => '已撤回'];

$formattedList = array_map(function($item) use ($statusNames) {
    return [
        'id' => $item['id'],
        'technician' => [
            'id' => $item['base_technician_id'],
            'name' => $item['tech_name'],
            'avatar' => $item['tech_avatar'],
            'phone' => $item['tech_phone'],
            'gender' => (int)$item['tech_gender'],
            'experience' => (int)$item['tech_experience'],
            'certified' => (bool)$item['tech_certified'],
            'rating' => (float)$item['tech_rating']
        ],
        'message' => $item['base_message'],
        'status' => (int)$item['base_status'],
        'status_name' => $statusNames[$item['base_status']] ?? '',
        'reply' => $item['base_reply'],
        'created_at' => $item['base_time_created']
    ];
}, $list);

Response::success(['list' => $formattedList]);
