<?php
/**
 * 轻养到家 - 商家端财务 API
 * ZERO 框架规范 - 冒号语法
 * 
 * GET  /api/shop/finance              财务概览
 * GET  /api/shop/finance:settlement   结算记录
 * POST /api/shop/finance:withdraw     申请提现
 */

require_once dirname(dirname(dirname(__FILE__))) . '/bootstrap.inc.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

// 需要登录
$auth = auth_require(TokenType::SHOP);

switch ($action) {
    case '':
        if ($method !== 'GET') Response::badRequest('请求方法不允许');
        getFinanceOverview($auth);
        break;
        
    case 'settlement':
        if ($method !== 'GET') Response::badRequest('请求方法不允许');
        getSettlementRecords($auth);
        break;
        
    case 'withdraw':
        if ($method !== 'POST') Response::badRequest('请求方法不允许');
        requestWithdraw($auth);
        break;
        
    default:
        Response::notFound('接口不存在');
}

/**
 * 获取财务概览
 */
function getFinanceOverview($auth) {
    // 获取钱包
    $wallet = db()->fetch(
        'SELECT * FROM qy_wallet_list WHERE base_owner_type = ? AND base_owner_id = ?',
        ['shop', $auth['id']]
    );
    
    if (!$wallet) {
        $wallet = [
            'base_balance_available' => 0,
            'base_balance_pending' => 0,
            'base_balance_total' => 0
        ];
    }
    
    // 本月统计
    $monthStart = date('Y-m-01');
    $monthStats = db()->fetch(
        'SELECT 
            COUNT(*) as order_count,
            COALESCE(SUM(base_price_total), 0) as total_revenue
         FROM qy_order_list
         WHERE base_shop_id = ? AND base_status_order = 5 AND base_time_created >= ?',
        [$auth['id'], $monthStart]
    );
    
    // 计算分成
    $platformRate = 0.15;
    $shopRate = 0.30;
    $techRate = 0.55;
    
    $totalRevenue = $monthStats['total_revenue'];
    $platformFee = round($totalRevenue * $platformRate, 2);
    $shopIncome = round($totalRevenue * $shopRate, 2);
    $techPayout = round($totalRevenue * $techRate, 2);
    
    // 技师收入排行
    $techRank = db()->fetchAll(
        'SELECT t.id, t.base_profile_realname as name, 
                COUNT(o.id) as order_count,
                COALESCE(SUM(o.base_price_total), 0) as revenue
         FROM qy_technician_list t
         LEFT JOIN qy_order_list o ON t.id = o.base_technician_id 
            AND o.base_status_order = 5 AND o.base_time_created >= ?
         WHERE t.base_shop_id = ?
         GROUP BY t.id
         ORDER BY revenue DESC
         LIMIT 5',
        [$monthStart, $auth['id']]
    );
    
    Response::success([
        'wallet' => [
            'available' => round($wallet['base_balance_available'], 2),
            'pending' => round($wallet['base_balance_pending'], 2),
            'total' => round($wallet['base_balance_total'], 2)
        ],
        'month_stats' => [
            'order_count' => (int)$monthStats['order_count'],
            'total_revenue' => round($totalRevenue, 2),
            'breakdown' => [
                'platform_fee' => $platformFee,
                'shop_income' => $shopIncome,
                'tech_payout' => $techPayout
            ]
        ],
        'tech_rank' => $techRank
    ]);
}

/**
 * 获取结算记录
 */
function getSettlementRecords($auth) {
    $page = max(1, (int)input('page', 1));
    $pageSize = min(50, max(1, (int)input('page_size', 20)));
    
    $wallet = db()->fetch(
        'SELECT id FROM qy_wallet_list WHERE base_owner_type = ? AND base_owner_id = ?',
        ['shop', $auth['id']]
    );
    
    if (!$wallet) {
        Response::success([
            'list' => [],
            'pagination' => ['total' => 0, 'page' => 1, 'page_size' => $pageSize, 'total_pages' => 0]
        ]);
        return;
    }
    
    $total = db()->fetch(
        'SELECT COUNT(*) as count FROM qy_wallet_transaction_list WHERE base_wallet_id = ?',
        [$wallet['id']]
    )['count'];
    
    $offset = ($page - 1) * $pageSize;
    $records = db()->fetchAll(
        'SELECT id, base_type as type, base_amount as amount,
                base_balance_after as balance_after, base_remark as remark,
                base_time_created as created_at
         FROM qy_wallet_transaction_list
         WHERE base_wallet_id = ?
         ORDER BY base_time_created DESC
         LIMIT ?, ?',
        [$wallet['id'], $offset, $pageSize]
    );
    
    Response::paginate($records, $total, $page, $pageSize);
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
    if ($amount < 100) {
        Response::badRequest('最低提现金额为100元');
    }
    
    // 获取钱包
    $wallet = db()->fetch(
        'SELECT * FROM qy_wallet_list WHERE base_owner_type = ? AND base_owner_id = ?',
        ['shop', $auth['id']]
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
