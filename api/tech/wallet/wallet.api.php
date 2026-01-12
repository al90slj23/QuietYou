<?php
/**
 * 轻养到家 - 技师端钱包 API
 * ZERO 框架规范 - 冒号语法
 * 
 * GET  /api/tech/wallet              钱包信息
 * GET  /api/tech/wallet:transactions 流水记录
 * POST /api/tech/wallet:withdraw     申请提现
 */

require_once dirname(dirname(dirname(__FILE__))) . '/bootstrap.include.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

// 需要登录
$auth = auth_require(TokenType::TECHNICIAN);

switch ($action) {
    case '':
        if ($method !== 'GET') Response::badRequest('请求方法不允许');
        getWalletInfo($auth);
        break;
        
    case 'transactions':
        if ($method !== 'GET') Response::badRequest('请求方法不允许');
        getTransactions($auth);
        break;
        
    case 'withdraw':
        if ($method !== 'POST') Response::badRequest('请求方法不允许');
        requestWithdraw($auth);
        break;
        
    default:
        Response::notFound('接口不存在');
}

/**
 * 获取钱包信息
 */
function getWalletInfo($auth) {
    $wallet = db()->fetch(
        'SELECT * FROM qy_wallet_list WHERE base_owner_type = ? AND base_owner_id = ?',
        ['technician', $auth['id']]
    );
    
    if (!$wallet) {
        // 创建钱包
        db()->insert('qy_wallet_list', [
            'base_owner_type' => 'technician',
            'base_owner_id' => $auth['id'],
            'base_balance_available' => 0,
            'base_balance_pending' => 0,
            'base_balance_total' => 0,
            'base_time_updated' => date('Y-m-d H:i:s')
        ]);
        
        $wallet = [
            'base_balance_available' => 0,
            'base_balance_pending' => 0,
            'base_balance_total' => 0
        ];
    }
    
    // 获取本月统计
    $monthStart = date('Y-m-01');
    $monthStats = db()->fetch(
        'SELECT COUNT(*) as order_count, COALESCE(SUM(base_amount), 0) as income
         FROM qy_wallet_transaction_list t
         JOIN qy_wallet_list w ON t.base_wallet_id = w.id
         WHERE w.base_owner_type = ? AND w.base_owner_id = ?
         AND t.base_type = ? AND t.base_time_created >= ?',
        ['technician', $auth['id'], 'income', $monthStart]
    );
    
    Response::success([
        'available' => round($wallet['base_balance_available'], 2),
        'pending' => round($wallet['base_balance_pending'], 2),
        'total' => round($wallet['base_balance_total'], 2),
        'month_stats' => [
            'income' => round($monthStats['income'], 2),
            'order_count' => (int)$monthStats['order_count']
        ]
    ]);
}

/**
 * 获取交易记录
 */
function getTransactions($auth) {
    $type = input('type'); // income, withdraw
    $page = max(1, (int)input('page', 1));
    $pageSize = min(50, max(1, (int)input('page_size', 20)));
    
    $wallet = db()->fetch(
        'SELECT id FROM qy_wallet_list WHERE base_owner_type = ? AND base_owner_id = ?',
        ['technician', $auth['id']]
    );
    
    if (!$wallet) {
        Response::success([
            'list' => [],
            'pagination' => ['total' => 0, 'page' => 1, 'page_size' => $pageSize, 'total_pages' => 0]
        ]);
        return;
    }
    
    $where = ['base_wallet_id = :wallet_id'];
    $params = ['wallet_id' => $wallet['id']];
    
    if ($type) {
        $where[] = 'base_type = :type';
        $params['type'] = $type;
    }
    
    $whereClause = implode(' AND ', $where);
    
    $total = db()->fetch(
        "SELECT COUNT(*) as count FROM qy_wallet_transaction_list WHERE $whereClause",
        $params
    )['count'];
    
    $offset = ($page - 1) * $pageSize;
    $transactions = db()->fetchAll(
        "SELECT id, base_type as type, base_amount as amount, 
                base_balance_after as balance_after, base_remark as remark,
                base_time_created as created_at
         FROM qy_wallet_transaction_list
         WHERE $whereClause
         ORDER BY base_time_created DESC
         LIMIT $offset, $pageSize",
        $params
    );
    
    Response::paginate($transactions, $total, $page, $pageSize);
}

/**
 * 申请提现
 */
function requestWithdraw($auth) {
    validate_required(['amount']);
    
    $amount = (float)input('amount');
    $bankAccount = input('bank_account', '');
    $bankName = input('bank_name', '');
    
    // 验证最低提现金额
    if ($amount < 50) {
        Response::badRequest('最低提现金额为50元');
    }
    
    // 获取钱包
    $wallet = db()->fetch(
        'SELECT * FROM qy_wallet_list WHERE base_owner_type = ? AND base_owner_id = ?',
        ['technician', $auth['id']]
    );
    
    if (!$wallet) {
        Response::badRequest('钱包不存在');
    }
    
    // 验证余额
    if ($amount > $wallet['base_balance_available']) {
        Response::badRequest('可提现余额不足');
    }
    
    // 扣减可用余额
    db()->query(
        'UPDATE qy_wallet_list SET base_balance_available = base_balance_available - ? WHERE id = ?',
        [$amount, $wallet['id']]
    );
    
    // 添加提现记录
    db()->insert('qy_wallet_transaction_list', [
        'base_wallet_id' => $wallet['id'],
        'base_type' => 'withdraw',
        'base_amount' => -$amount,
        'base_balance_after' => $wallet['base_balance_available'] - $amount,
        'base_order_id' => 0,
        'base_remark' => '提现申请' . ($bankName ? " - $bankName" : ''),
        'base_time_created' => date('Y-m-d H:i:s')
    ]);
    
    Response::success(null, '提现申请已提交，预计1-3个工作日到账');
}
