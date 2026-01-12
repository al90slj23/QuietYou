<?php
/**
 * ================================================================
 * 文件名: list.api.php
 * 创建时间: 2026-01-13
 * ================================================================
 *
 * 【文件职责】
 * 技师收入明细列表 API
 *
 * ================================================================
 */

require_once dirname(dirname(dirname(__FILE__))) . '/bootstrap.include.php';

// 验证技师登录
$tech = auth_technician();

// 获取参数
$page = max(1, (int)input('page', 1));
$pageSize = min(50, max(1, (int)input('page_size', 20)));
$type = input('type', ''); // income, withdraw, refund
$offset = ($page - 1) * $pageSize;

// 获取技师钱包
$wallet = db()->fetch(
    "SELECT * FROM qy_wallet_list WHERE base_owner_type = 'technician' AND base_owner_id = ?",
    [$tech['id']]
);

if (!$wallet) {
    // 创建钱包
    db()->execute(
        "INSERT INTO qy_wallet_list (base_owner_type, base_owner_id) VALUES ('technician', ?)",
        [$tech['id']]
    );
    $wallet = [
        'base_balance_available' => 0,
        'base_balance_pending' => 0,
        'base_balance_total' => 0
    ];
}

// 构建查询条件
$where = "base_wallet_id = ?";
$params = [$wallet['id'] ?? 0];

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

// 格式化数据
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
