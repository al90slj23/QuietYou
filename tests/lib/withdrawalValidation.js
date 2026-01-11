/**
 * 提现验证逻辑
 * Property 24: Withdrawal Minimum Validation
 * Validates: Requirements 10.3
 */

/**
 * 提现状态枚举
 */
const WithdrawalStatus = {
  PENDING: 'pending',       // 待处理
  PROCESSING: 'processing', // 处理中
  COMPLETED: 'completed',   // 已完成
  FAILED: 'failed',         // 失败
  CANCELLED: 'cancelled'    // 已取消
};

/**
 * 默认配置
 */
const DEFAULT_CONFIG = {
  minAmount: 50,           // 最低提现金额
  maxAmount: 50000,        // 单笔最高提现金额
  dailyLimit: 100000,      // 每日提现限额
  processingDays: 3        // 处理天数
};

/**
 * 验证提现金额
 * @param {number} amount - 提现金额
 * @param {number} availableBalance - 可用余额
 * @param {Object} config - 配置
 * @returns {Object} 验证结果 { valid, error }
 */
function validateWithdrawalAmount(amount, availableBalance, config = DEFAULT_CONFIG) {
  // 金额必须为正数
  if (amount <= 0) {
    return { valid: false, error: '提现金额必须大于0' };
  }

  // 最低金额验证
  if (amount < config.minAmount) {
    return { valid: false, error: `最低提现金额为${config.minAmount}元` };
  }

  // 最高金额验证
  if (amount > config.maxAmount) {
    return { valid: false, error: `单笔最高提现金额为${config.maxAmount}元` };
  }

  // 余额验证
  if (amount > availableBalance) {
    return { valid: false, error: '提现金额超过可用余额' };
  }

  return { valid: true, error: null };
}

/**
 * 验证银行卡信息
 * @param {Object} bankInfo - 银行卡信息
 * @returns {Object} 验证结果 { valid, error }
 */
function validateBankInfo(bankInfo) {
  if (!bankInfo) {
    return { valid: false, error: '请填写银行卡信息' };
  }

  if (!bankInfo.cardNumber || bankInfo.cardNumber.length < 16) {
    return { valid: false, error: '请填写有效的银行卡号' };
  }

  if (!bankInfo.bankName) {
    return { valid: false, error: '请选择开户银行' };
  }

  if (!bankInfo.holderName) {
    return { valid: false, error: '请填写持卡人姓名' };
  }

  return { valid: true, error: null };
}

/**
 * 验证每日提现限额
 * @param {number} amount - 本次提现金额
 * @param {number} todayWithdrawn - 今日已提现金额
 * @param {Object} config - 配置
 * @returns {Object} 验证结果 { valid, error, remaining }
 */
function validateDailyLimit(amount, todayWithdrawn, config = DEFAULT_CONFIG) {
  const remaining = config.dailyLimit - todayWithdrawn;
  
  if (remaining <= 0) {
    return { 
      valid: false, 
      error: '今日提现额度已用完',
      remaining: 0
    };
  }

  if (amount > remaining) {
    return { 
      valid: false, 
      error: `今日剩余可提现额度为${remaining}元`,
      remaining
    };
  }

  return { 
    valid: true, 
    error: null,
    remaining: remaining - amount
  };
}

/**
 * 创建提现申请
 * @param {number} amount - 提现金额
 * @param {number} availableBalance - 可用余额
 * @param {Object} bankInfo - 银行卡信息
 * @param {number} todayWithdrawn - 今日已提现金额
 * @param {Object} config - 配置
 * @returns {Object} 申请结果
 */
function createWithdrawalRequest(amount, availableBalance, bankInfo, todayWithdrawn = 0, config = DEFAULT_CONFIG) {
  // 验证金额
  const amountValidation = validateWithdrawalAmount(amount, availableBalance, config);
  if (!amountValidation.valid) {
    return { success: false, error: amountValidation.error };
  }

  // 验证银行卡
  const bankValidation = validateBankInfo(bankInfo);
  if (!bankValidation.valid) {
    return { success: false, error: bankValidation.error };
  }

  // 验证每日限额
  const limitValidation = validateDailyLimit(amount, todayWithdrawn, config);
  if (!limitValidation.valid) {
    return { success: false, error: limitValidation.error };
  }

  // 创建申请
  return {
    success: true,
    request: {
      id: `WD${Date.now()}`,
      amount,
      bankInfo: {
        cardNumber: maskCardNumber(bankInfo.cardNumber),
        bankName: bankInfo.bankName,
        holderName: bankInfo.holderName
      },
      status: WithdrawalStatus.PENDING,
      createdAt: new Date().toISOString(),
      estimatedArrival: getEstimatedArrival(config.processingDays)
    },
    error: null
  };
}

/**
 * 掩码银行卡号
 * @param {string} cardNumber - 银行卡号
 * @returns {string} 掩码后的卡号
 */
function maskCardNumber(cardNumber) {
  if (!cardNumber || cardNumber.length < 8) {
    return cardNumber;
  }
  const first4 = cardNumber.slice(0, 4);
  const last4 = cardNumber.slice(-4);
  return `${first4}****${last4}`;
}

/**
 * 获取预计到账时间
 * @param {number} days - 处理天数
 * @returns {string} 预计到账日期
 */
function getEstimatedArrival(days) {
  const date = new Date();
  let businessDays = 0;
  
  while (businessDays < days) {
    date.setDate(date.getDate() + 1);
    const dayOfWeek = date.getDay();
    if (dayOfWeek !== 0 && dayOfWeek !== 6) {
      businessDays++;
    }
  }
  
  return date.toISOString().split('T')[0];
}

/**
 * 检查提现金额是否满足最低要求
 * @param {number} amount - 提现金额
 * @param {number} minAmount - 最低金额
 * @returns {boolean} 是否满足
 */
function meetsMinimumAmount(amount, minAmount = DEFAULT_CONFIG.minAmount) {
  return amount >= minAmount;
}

/**
 * 计算可提现金额
 * @param {number} availableBalance - 可用余额
 * @param {number} todayWithdrawn - 今日已提现
 * @param {Object} config - 配置
 * @returns {number} 可提现金额
 */
function calculateWithdrawableAmount(availableBalance, todayWithdrawn = 0, config = DEFAULT_CONFIG) {
  const dailyRemaining = Math.max(0, config.dailyLimit - todayWithdrawn);
  const maxAllowed = Math.min(availableBalance, config.maxAmount, dailyRemaining);
  return Math.max(0, maxAllowed);
}

module.exports = {
  WithdrawalStatus,
  DEFAULT_CONFIG,
  validateWithdrawalAmount,
  validateBankInfo,
  validateDailyLimit,
  createWithdrawalRequest,
  maskCardNumber,
  getEstimatedArrival,
  meetsMinimumAmount,
  calculateWithdrawableAmount
};
