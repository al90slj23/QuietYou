<?php
/**
 * ================================================================
 * 文件名: list.api.php
 * 创建时间: 2026-01-13
 * ================================================================
 *
 * 【文件职责】
 * 用户优惠券列表 API
 *
 * ================================================================
 */

require_once dirname(dirname(dirname(__FILE__))) . '/bootstrap.include.php';

// 验证用户登录
$user = auth_customer();

// 获取参数
$status = input('status', ''); // 0=未使用, 1=已使用, 2=已过期
$page = max(1, (int)input('page', 1));
$pageSize = min(50, max(1, (int)input('page_size', 20)));
$offset = ($page - 1) * $pageSize;

// 构建查询条件
$where = "uc.base_user_id = ?";
$params = [$user['id']];

if ($status !== '') {
    $where .= " AND uc.base_status = ?";
    $params[] = (int)$status;
}

// 查询总数
$total = db()->fetchColumn(
    "SELECT COUNT(*) FROM qy_user_coupon_list uc WHERE $where",
    $params
);

// 查询列表
$params[] = $pageSize;
$params[] = $offset;
$list = db()->fetchAll(
    "SELECT uc.*, c.base_name as coupon_name, c.base_type as coupon_type, 
            c.base_value as coupon_value, c.base_min_amount as min_amount
     FROM qy_user_coupon_list uc
     LEFT JOIN qy_coupon_list c ON uc.base_coupon_id = c.id
     WHERE $where
     ORDER BY uc.base_time_created DESC
     LIMIT ? OFFSET ?",
    $params
);

$typeNames = [1 => '满减券', 2 => '折扣券', 3 => '立减券'];
$statusNames = [0 => '未使用', 1 => '已使用', 2 => '已过期'];

$formattedList = array_map(function($item) use ($typeNames, $statusNames) {
    return [
        'id' => $item['id'],
        'coupon_id' => $item['base_coupon_id'],
        'name' => $item['coupon_name'],
        'type' => (int)$item['coupon_type'],
        'type_name' => $typeNames[$item['coupon_type']] ?? '',
        'value' => (float)$item['coupon_value'],
        'min_amount' => (float)$item['min_amount'],
        'status' => (int)$item['base_status'],
        'status_name' => $statusNames[$item['base_status']] ?? '',
        'expire_at' => $item['base_time_expire'],
        'used_at' => $item['base_time_used'],
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
