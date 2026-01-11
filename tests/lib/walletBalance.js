/**
 * 钱包余额一致性逻辑
 * Property 23: Wallet Balance Consistency
 * Validates: Requirements 10.1, 10.4
 */

/**
 * 交易类型枚举
 */
const TransactionType = {
  ORDER_INCOME: 'order_income',      // 订单收入
  WITHDRAWAL: 'withdrawal',          // 提现
  WITHDRAWAL_FAILED: 'withdrawal_failed', // 提现失败退回
  REFUND: 'refund',                  // 退款扣除
  BONUS: 'bonus'                     // 奖励
};

/**
 * 创建钱包对象
 * @param {number} availableBalance - 可用余额
 * @param {number} pendingBalance - 待结算余额
 * @param {number} totalEarnings - 累计收入
 * @returns {Object} 钱包对象
 */
function createWallet(availableBalance = 0, pendingBalance = 0, totalEarnings = 0) {
  return {
    availableBalance: Math.max(0, availableBalance),
    pendingBalance: Math.max(0, pendingBalance),
    totalEarnings: Math.max(0, totalEarnings),
    transactions: []
  };
}

/**
 * 添加订单收入（先进入待结算）
 * @param {Object} wallet - 钱包对象
 * @param {number} amount - 金额
 * @param {string} orderId - 订单ID
 * @returns {Object} 更新后的钱包
 */
function addOrderIncome(wallet, amount, orderId) {
  if (amount <= 0) {
    return { ...wallet, error: '金额必须大于0' };
  }

  return {
    ...wallet,
    pendingBalance: wallet.pendingBalance + amount,
    totalEarnings: wallet.totalEarnings + amount,
    transactions: [
      ...wallet.transactions,
      {
        type: TransactionType.ORDER_INCOME,
        amount: amount,
        orderId: orderId,
        timestamp: new Date().toISOString(),
        status: 'pending'
      }
    ],
    error: null
  };
}

/**
 * 结算待结算余额（待结算转为可用）
 * @param {Object} wallet - 钱包对象
 * @param {number} amount - 结算金额
 * @returns {Object} 更新后的钱包
 */
function settleBalance(wallet, amount) {
  if (amount <= 0) {
    return { ...wallet, error: '结算金额必须大于0' };
  }

  if (amount > wallet.pendingBalance) {
    return { ...wallet, error: '结算金额超过待结算余额' };
  }

  return {
    ...wallet,
    availableBalance: wallet.availableBalance + amount,
    pendingBalance: wallet.pendingBalance - amount,
    error: null
  };
}

/**
 * 申请提现
 * @param {Object} wallet - 钱包对象
 * @param {number} amount - 提现金额
 * @param {number} minAmount - 最低提现金额
 * @returns {Object} 更新后的钱包
 */
function requestWithdrawal(wallet, amount, minAmount = 50) {
  if (amount < minAmount) {
    return { ...wallet, error: `最低提现金额为${minAmount}元` };
  }

  if (amount > wallet.availableBalance) {
    return { ...wallet, error: '提现金额超过可用余额' };
  }

  return {
    ...wallet,
    availableBalance: wallet.availableBalance - amount,
    transactions: [
      ...wallet.transactions,
      {
        type: TransactionType.WITHDRAWAL,
        amount: -amount,
        timestamp: new Date().toISOString(),
        status: 'processing'
      }
    ],
    error: null
  };
}

/**
 * 提现失败退回
 * @param {Object} wallet - 钱包对象
 * @param {number} amount - 退回金额
 * @returns {Object} 更新后的钱包
 */
function refundWithdrawal(wallet, amount) {
  if (amount <= 0) {
    return { ...wallet, error: '退回金额必须大于0' };
  }

  return {
    ...wallet,
    availableBalance: wallet.availableBalance + amount,
    transactions: [
      ...wallet.transactions,
      {
        type: TransactionType.WITHDRAWAL_FAILED,
        amount: amount,
        timestamp: new Date().toISOString(),
        status: 'completed'
      }
    ],
    error: null
  };
}

/**
 * 订单退款扣除
 * @param {Object} wallet - 钱包对象
 * @param {number} amount - 扣除金额
 * @param {string} orderId - 订单ID
 * @returns {Object} 更新后的钱包
 */
function deductRefund(wallet, amount, orderId) {
  if (amount <= 0) {
    return { ...wallet, error: '扣除金额必须大于0' };
  }

  // 优先从待结算扣除，不足部分从可用余额扣除
  let pendingDeduct = Math.min(amount, wallet.pendingBalance);
  let availableDeduct = amount - pendingDeduct;

  if (availableDeduct > wallet.availableBalance) {
    return { ...wallet, error: '余额不足以扣除退款' };
  }

  return {
    ...wallet,
    availableBalance: wallet.availableBalance - availableDeduct,
    pendingBalance: wallet.pendingBalance - pendingDeduct,
    totalEarnings: wallet.totalEarnings - amount,
    transactions: [
      ...wallet.transactions,
      {
        type: TransactionType.REFUND,
        amount: -amount,
        orderId: orderId,
        timestamp: new Date().toISOString(),
        status: 'completed'
      }
    ],
    error: null
  };
}

/**
 * 验证钱包余额一致性
 * 规则：可用余额 + 待结算余额 <= 累计收入（因为可能有提现）
 * @param {Object} wallet - 钱包对象
 * @returns {boolean} 是否一致
 */
function validateBalanceConsistency(wallet) {
  // 计算交易历史中的净收入
  const netIncome = wallet.transactions.reduce((sum, tx) => {
    if (tx.type === TransactionType.ORDER_INCOME || tx.type === TransactionType.BONUS) {
      return sum + tx.amount;
    }
    if (tx.type === TransactionType.REFUND) {
      return sum + tx.amount; // 负数
    }
    return sum;
  }, 0);

  // 计算已提现金额
  const withdrawn = wallet.transactions.reduce((sum, tx) => {
    if (tx.type === TransactionType.WITHDRAWAL) {
      return sum + Math.abs(tx.amount);
    }
    if (tx.type === TransactionType.WITHDRAWAL_FAILED) {
      return sum - tx.amount; // 退回的要减掉
    }
    return sum;
  }, 0);

  // 可用 + 待结算 + 已提现 应该等于累计收入
  const currentTotal = wallet.availableBalance + wallet.pendingBalance + withdrawn;
  
  // 允许小数精度误差
  return Math.abs(currentTotal - wallet.totalEarnings) < 0.01;
}

/**
 * 获取钱包摘要
 * @param {Object} wallet - 钱包对象
 * @returns {Object} 摘要信息
 */
function getWalletSummary(wallet) {
  const incomeTransactions = wallet.transactions.filter(
    tx => tx.type === TransactionType.ORDER_INCOME || tx.type === TransactionType.BONUS
  );
  
  const withdrawalTransactions = wallet.transactions.filter(
    tx => tx.type === TransactionType.WITHDRAWAL
  );

  return {
    availableBalance: wallet.availableBalance,
    pendingBalance: wallet.pendingBalance,
    totalEarnings: wallet.totalEarnings,
    transactionCount: wallet.transactions.length,
    incomeCount: incomeTransactions.length,
    withdrawalCount: withdrawalTransactions.length
  };
}

module.exports = {
  TransactionType,
  createWallet,
  addOrderIncome,
  settleBalance,
  requestWithdrawal,
  refundWithdrawal,
  deductRefund,
  validateBalanceConsistency,
  getWalletSummary
};
