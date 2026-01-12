<?php
/**
 * ================================================================
 * 文件名: withdraw.api.php
 * 创建时间: 2026-01-13
 * ================================================================
 *
 * 【文件职责】
 * 商户申请提现 API
 *
 * ================================================================
 */

require_once dirname(dirname(dirname(__FILE__))) . '/bootstrap.include.php';

// 验证商户登录
$merchant = auth_merchant();

// 验证必填参数
validate_required(['amount']);

$amount = (float)input('amount');

if ($amount <= 0) {
    Response::badRequest('提现金额必须大于0');
}

// 获取商户钱包
$wallet = db()->fetch(
    "SELECT * FROM qy_wallet_list WHERE base_owner_type = 'shop' AND base_owner_id = ?",
    [$merchant['id']]
);

if (!$wallet) {
    Response::badRequest('钱包不存在');
}

if ($amount > $wallet['base_balance_available']) {
    Response::badRequest('可用余额不足');
}

$minWithdraw = 100;
if ($amount < $minWithdraw) {
    Response::badRequest("最低提现金额为 {$minWithdraw} 元");
}

db()->beginTransaction();

try {
    $newBalance = $wallet['base_balance_available'] - $amount;
    db()->execute(
        "UPDATE qy_wallet_list SET base_balance_available = ? WHERE id = ?",
        [$newBalance, $wallet['id']]
    );
    
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
