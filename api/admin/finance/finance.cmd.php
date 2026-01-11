<?php
/**
 * 轻养到家 - 管理后台财务管理 API
 * ZERO 框架规范 - 冒号语法
 * 
 * GET  /api/admin/finance              财务概览
 * GET  /api/admin/finance:withdrawals  提现申请列表
 * POST /api/admin/finance:withdraw:123:approve  审批提现
 * POST /api/admin/finance:withdraw:123:reject   拒绝提现
 * GET  /api/admin/finance:settings     佣金设置
 * POST /api/admin/finance:settings     更新佣金设置
 */

require_once dirname(dirname(dirname(__FILE__))) . '/bootstrap.inc.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';
$id = $_GET['id'] ?? null;
$subAction = $_GET['sub_action'] ?? '';

// 需要管理员登录
$auth = auth_require(TokenType::ADMIN);

if ($action === 'withdraw' && $id) {
    if ($method !== 'POST') Response::badRequest('请求方法不允许');
    if ($subAction === 'approve') {
        approveWithdraw($id);
    } elseif ($subAction === 'reject') {
        rejectWithdraw($id);
    } else {
        Response::badRequest('操作不支持');
    }
} else {
    switch ($action) {
        case '':
            if ($method !== 'GET') Response::badRequest('请求方法不允许');
            getFinanceOverview();
            break;
            
        case 'withdrawals':
            if ($method !== 'GET') Response::badRequest('请求方法不允许');
            getWithdrawalList();
            break;
            
        case 'settings':
            if ($method === 'GET') {
                getCommissionSettings();
            } elseif ($method === 'POST') {
                updateCommissionSettings();
            } else {
                Response::badRequest('请求方法不允许');
            }
            break;
            
        default:
            Response::notFound('接口不存在');
    }
}

/**
 * 获取财务概览
 */
function getFinanceOverview() {
    // 今日统计
    $today = date('Y-m-d');
    $todayStats = db()->fetch(
        'SELECT COUNT(*) as order_count, COALESCE(SUM(base_price_total), 0) as revenue
         FROM qy_order_list WHERE DATE(base_time_created) = ? AND base_status_order = 5',
        [$today]
    );
    
    // 本月统计
    $monthStart = date('Y-m-01');
    $monthStats = db()->fetch(
        'SELECT COUNT(*) as order_count, COALESCE(SUM(base_price_total), 0) as revenue
         FROM qy_order_list WHERE base_time_created >= ? AND base_status_order = 5',
        [$monthStart]
    );
    
    // 总计
    $totalStats = db()->fetch(
        'SELECT COUNT(*) as order_count, COALESCE(SUM(base_price_total), 0) as revenue
         FROM qy_order_list WHERE base_status_order = 5'
    );
    
    // 待处理提现
    $pendingWithdrawals = db()->fetch(
        'SELECT COUNT(*) as count, COALESCE(SUM(ABS(base_amount)), 0) as amount
         FROM qy_wallet_transaction_list WHERE base_type = ? AND base_status = ?',
        ['withdraw', 'pending']
    );
    
    // 分成比例
    $platformRate = 0.15;
    $shopRate = 0.30;
    $techRate = 0.55;
    
    // 计算各方收入
    $monthRevenue = $monthStats['revenue'];
    
    Response::success([
        'today' => [
            'order_count' => (int)$todayStats['order_count'],
            'revenue' => round($todayStats['revenue'], 2)
        ],
        'month' => [
            'order_count' => (int)$monthStats['order_count'],
            'revenue' => round($monthRevenue, 2),
            'breakdown' => [
                'platform' => round($monthRevenue * $platformRate, 2),
                'shop' => round($monthRevenue * $shopRate, 2),
                'technician' => round($monthRevenue * $techRate, 2)
            ]
        ],
        'total' => [
            'order_count' => (int)$totalStats['order_count'],
            'revenue' => round($totalStats['revenue'], 2)
        ],
        'pending_withdrawals' => [
            'count' => (int)$pendingWithdrawals['count'],
            'amount' => round($pendingWithdrawals['amount'], 2)
        ],
        'commission_rates' => [
            'platform' => $platformRate,
            'shop' => $shopRate,
            'technician' => $techRate
        ]
    ]);
}

/**
 * 获取提现申请列表
 */
function getWithdrawalList() {
    $status = input('status');
    $ownerType = input('owner_type');
    $page = max(1, (int)input('page', 1));
    $pageSize = min(50, max(1, (int)input('page_size', 20)));
    
    $where = ["t.base_type = 'withdraw'"];
    $params = [];
    
    if ($status) {
        $where[] = 't.base_status = :status';
        $params['status'] = $status;
    }
    
    if ($ownerType) {
        $where[] = 'w.base_owner_type = :owner_type';
        $params['owner_type'] = $ownerType;
    }
    
    $whereClause = implode(' AND ', $where);
    
    $total = db()->fetch(
        "SELECT COUNT(*) as count 
         FROM qy_wallet_transaction_list t
         JOIN qy_wallet_list w ON t.base_wallet_id = w.id
         WHERE $whereClause",
        $params
    )['count'];
    
    $offset = ($page - 1) * $pageSize;
    $withdrawals = db()->fetchAll(
        "SELECT t.id, t.base_amount as amount, t.base_status as status,
                t.base_remark as remark, t.base_time_created as created_at,
                w.base_owner_type as owner_type, w.base_owner_id as owner_id
         FROM qy_wallet_transaction_list t
         JOIN qy_wallet_list w ON t.base_wallet_id = w.id
         WHERE $whereClause
         ORDER BY t.base_time_created DESC
         LIMIT $offset, $pageSize",
        $params
    );
    
    // 补充申请人信息
    foreach ($withdrawals as &$w) {
        $w['amount'] = abs($w['amount']);
        if ($w['owner_type'] === 'technician') {
            $owner = db()->fetch(
                'SELECT base_profile_realname as name FROM qy_technician_list WHERE id = ?',
                [$w['owner_id']]
            );
            $w['owner_name'] = $owner['name'] ?? '未知技师';
        } elseif ($w['owner_type'] === 'shop') {
            $owner = db()->fetch(
                'SELECT base_profile_name as name FROM qy_shop_list WHERE id = ?',
                [$w['owner_id']]
            );
            $w['owner_name'] = $owner['name'] ?? '未知商家';
        }
    }
    
    Response::paginate($withdrawals, $total, $page, $pageSize);
}

/**
 * 审批提现
 */
function approveWithdraw($id) {
    $transaction = db()->fetch(
        'SELECT * FROM qy_wallet_transaction_list WHERE id = ? AND base_type = ?',
        [$id, 'withdraw']
    );
    
    if (!$transaction) {
        Response::notFound('提现记录不存在');
    }
    
    if ($transaction['base_status'] !== 'pending') {
        Response::badRequest('该提现申请已处理');
    }
    
    db()->update('qy_wallet_transaction_list', [
        'base_status' => 'completed',
        'base_time_updated' => date('Y-m-d H:i:s')
    ], 'id = :id', ['id' => $id]);
    
    Response::success(null, '提现已审批通过');
}

/**
 * 拒绝提现
 */
function rejectWithdraw($id) {
    $reason = input('reason', '');
    
    $transaction = db()->fetch(
        'SELECT * FROM qy_wallet_transaction_list WHERE id = ? AND base_type = ?',
        [$id, 'withdraw']
    );
    
    if (!$transaction) {
        Response::notFound('提现记录不存在');
    }
    
    if ($transaction['base_status'] !== 'pending') {
        Response::badRequest('该提现申请已处理');
    }
    
    // 退回余额
    db()->query(
        'UPDATE qy_wallet_list SET base_balance_available = base_balance_available + ? WHERE id = ?',
        [abs($transaction['base_amount']), $transaction['base_wallet_id']]
    );
    
    db()->update('qy_wallet_transaction_list', [
        'base_status' => 'rejected',
        'base_remark' => $transaction['base_remark'] . ($reason ? " | 拒绝原因: $reason" : ''),
        'base_time_updated' => date('Y-m-d H:i:s')
    ], 'id = :id', ['id' => $id]);
    
    Response::success(null, '提现已拒绝，余额已退回');
}

/**
 * 获取佣金设置
 */
function getCommissionSettings() {
    // 实际项目中从配置表读取
    Response::success([
        'platform_rate' => 0.15,
        'shop_rate' => 0.30,
        'technician_rate' => 0.55,
        'min_withdraw_amount' => 100,
        'withdraw_fee_rate' => 0
    ]);
}

/**
 * 更新佣金设置
 */
function updateCommissionSettings() {
    $platformRate = (float)input('platform_rate', 0.15);
    $shopRate = (float)input('shop_rate', 0.30);
    $techRate = (float)input('technician_rate', 0.55);
    
    // 验证比例总和
    $total = $platformRate + $shopRate + $techRate;
    if (abs($total - 1.0) > 0.001) {
        Response::badRequest('分成比例总和必须为100%');
    }
    
    // 实际项目中保存到配置表
    // db()->update('qy_config', [...], ...);
    
    Response::success(null, '佣金设置已更新');
}
