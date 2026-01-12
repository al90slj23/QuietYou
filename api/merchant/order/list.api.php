<?php
/**
 * ================================================================
 * 文件名: list.api.php
 * 创建时间: 2026-01-13
 * ================================================================
 *
 * 【文件职责】
 * 商户订单列表 API
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
$orderType = input('order_type', '');
$offset = ($page - 1) * $pageSize;

// 构建查询条件
$where = "o.base_shop_id = ?";
$params = [$merchant['id']];

if ($status !== '') {
    $where .= " AND o.base_status_order = ?";
    $params[] = (int)$status;
}

if ($orderType !== '') {
    $where .= " AND o.base_order_type = ?";
    $params[] = (int)$orderType;
}

// 查询总数
$total = db()->fetchColumn(
    "SELECT COUNT(*) FROM qy_order_list o WHERE $where",
    $params
);

// 查询列表
$params[] = $pageSize;
$params[] = $offset;
$list = db()->fetchAll(
    "SELECT o.*, u.base_profile_nickname as user_nickname, u.base_profile_avatar as user_avatar,
            t.base_profile_realname as tech_name, t.base_profile_avatar as tech_avatar
     FROM qy_order_list o
     LEFT JOIN qy_user_list u ON o.base_user_id = u.id
     LEFT JOIN qy_technician_list t ON o.base_technician_id = t.id
     WHERE $where
     ORDER BY o.base_time_created DESC
     LIMIT ? OFFSET ?",
    $params
);

$statusNames = [
    0 => '待支付', 1 => '待接单', 2 => '已接单', 3 => '出发中',
    4 => '服务中', 5 => '已完成', 6 => '已取消', 7 => '已退款'
];
$orderTypes = [1 => '上门服务', 2 => '到店服务', 3 => '借调服务'];

$formattedList = array_map(function($item) use ($statusNames, $orderTypes) {
    return [
        'id' => $item['id'],
        'order_no' => $item['base_order_no'],
        'order_type' => (int)$item['base_order_type'],
        'order_type_name' => $orderTypes[$item['base_order_type']] ?? '',
        'service_name' => $item['base_service_name'],
        'user' => [
            'id' => $item['base_user_id'],
            'nickname' => $item['user_nickname'],
            'avatar' => $item['user_avatar']
        ],
        'technician' => $item['base_technician_id'] ? [
            'id' => $item['base_technician_id'],
            'name' => $item['tech_name'],
            'avatar' => $item['tech_avatar']
        ] : null,
        'address' => $item['base_order_type'] == 1 ? [
            'contact' => $item['base_address_contact'],
            'phone' => $item['base_address_phone'],
            'detail' => $item['base_address_detail']
        ] : null,
        'scheduled_time' => $item['base_time_scheduled'],
        'price' => [
            'service' => (float)$item['base_price_service'],
            'tip' => (float)$item['base_price_tip'],
            'total' => (float)$item['base_price_total']
        ],
        'status' => (int)$item['base_status_order'],
        'status_name' => $statusNames[$item['base_status_order']] ?? '',
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
