<?php
/**
 * ================================================================
 * 文件名: list.api.php
 * 创建时间: 2026-01-13
 * ================================================================
 *
 * 【文件职责】
 * 商户收入明细列表 API
 *
 * ================================================================
 */

require_once dirname(dirname(dirname(__FILE__))) . '/bootstrap.include.php';

// 验证商户登录
$merchant = auth_merchant();

// 获取参数
$page = max(1, (int)input('page', 1));
$pageSize = min(50, max(1, (int)input('page_size', 20)));
$type = input('type', '');
$offset = ($page - 1) * $pageSize;

// 获取商户钱包
$wallet = db()->fetch(
    "SELECT * FROM qy_wallet_list WHERE base_owner_type = 'shop' AND base_owner_id = ?",
    [$merchant['id']]
);

if (!$wallet) {
    db()->execute(
        "INSERT INTO qy_wallet_list (base_owner_type, base_owner_id) VALUES ('shop', ?)",
        [$merchant['id']]
    );
    $wallet = [
        'id' => db()->lastInsertId(),
        'base_balance_available' => 0,
        'base_balance_pending' => 0,
        'base_balance_total' => 0
    ];
}

// 构建查询条件
$where = "base_wallet_id = ?";
$params = [$wallet['id']];

if ($type) {
    $where .= " AND base_type = ?";
    $params[] = $type;
}

// 查询总数
$total = db()->fetchColumn(
    "SELECT COUNT(*) FROM qy_wallet_transaction_list WHERE $where",
    $params
);

// 查询列表
$params[] = $pageSize;
$params[] = $offset;
$list = db()->fetchAll(
    "SELECT * FROM qy_wallet_transaction_list 
     WHERE $where 
     ORDER BY base_time_created DESC 
     LIMIT ? OFFSET ?",
    $params
);

$formattedList = array_map(function($item) {
    return [
        'id' => $item['id'],
        'type' => $item['base_type'],
        'amount' => (float)$item['base_amount'],
        'balance_after' => (float)$item['base_balance_after'],
        'order_id' => $item['base_order_id'],
        'remark' => $item['base_remark'],
        'status' => $item['base_status'],
        'created_at' => $item['base_time_created']
    ];
}, $list);

Response::success([
    'wallet' => [
        'available' => (float)$wallet['base_balance_available'],
        'pending' => (float)$wallet['base_balance_pending'],
        'total' => (float)$wallet['base_balance_total']
    ],
    'list' => $formattedList,
    'pagination' => [
        'total' => (int)$total,
        'page' => $page,
        'page_size' => $pageSize,
        'total_pages' => ceil($total / $pageSize)
    ]
]);
