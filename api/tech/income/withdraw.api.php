<?php
/**
 * ================================================================
 * 文件名: withdraw.api.php
 * 创建时间: 2026-01-13
 * ================================================================
 *
 * 【文件职责】
 * 技师申请提现 API
 *
 * ================================================================
 */

require_once dirname(dirname(dirname(__FILE__))) . '/bootstrap.include.php';

// 验证技师登录
$tech = auth_technician();

// 验证必填参数
validate_required(['amount']);

$amount = (float)input('amount');

if ($amount <= 0) {
    Response::badRequest('提现金额必须大于0');
}

// 获取技师钱包
$wallet = db()->fetch(
    "SELECT * FROM qy_wallet_list WHERE base_owner_type = 'technician' AND base_owner_id = ?",
    [$tech['id']]
);

if (!$wallet) {
    Response::badRequest('钱包不存在');
}

if ($amount > $wallet['base_balance_available']) {
    Response::badRequest('可用余额不足');
}

// 最低提现金额
$minWithdraw = 10;
if ($amount < $minWithdraw) {
    Response::badRequest("最低提现金额为 {$minWithdraw} 元");
}

// 开始事务
db()->beginTransaction();

try {
    // 扣减可用余额
    $newBalance = $wallet['base_balance_available'] - $amount;
    db()->execute(
        "UPDATE qy_wallet_list SET base_balance_available = ? WHERE id = ?",
        [$newBalance, $wallet['id']]
    );
    
    // 创建提现记录
    db()->execute(
        "INSERT INTO qy_wallet_transaction_list 
         (base_wallet_id, base_type, base_amount, base_balance_after, base_remark, base_status) 
         VALUES (?, 'withdraw', ?, ?, '申请提现', 0)",
        [$wallet['id'], $amount, $newBalance]
    );
    
    db()->commit();
    
    Response::success([
        'balance' => $newBalance
    ], '提现申请已提交，预计1-3个工作日到账');
    
} catch (Exception $e) {
    db()->rollBack();
    Response::serverError('提现失败，请稍后重试');
}
