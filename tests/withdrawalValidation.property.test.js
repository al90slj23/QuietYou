/**
 * Property 24: Withdrawal Minimum Validation
 * 提现验证属性测试
 * Validates: Requirements 10.3
 * 
 * 核心属性：
 * 1. 提现金额必须大于等于最低金额（50元）
 * 2. 提现金额不能超过可用余额
 * 3. 提现金额不能超过单笔最高限额
 * 4. 提现金额不能超过每日限额
 */

import { describe, it, expect } from 'vitest';
import * as fc from 'fast-check';
import {
  WithdrawalStatus,
  DEFAULT_CONFIG,
  validateWithdrawalAmount,
  validateBankInfo,
  validateDailyLimit,
  createWithdrawalRequest,
  maskCardNumber,
  meetsMinimumAmount,
  calculateWithdrawableAmount
} from './lib/withdrawalValidation.js';

// 生成金额（分为单位，转换为元）
const amountArb = fc.integer({ min: 1, max: 10000000 }).map(n => n / 100);

// 生成有效银行卡信息
const validBankInfoArb = fc.record({
  cardNumber: fc.stringOf(fc.constantFrom('0', '1', '2', '3', '4', '5', '6', '7', '8', '9'), { minLength: 16, maxLength: 19 }),
  bankName: fc.constantFrom('工商银行', '建设银行', '农业银行', '中国银行', '招商银行'),
  holderName: fc.string({ minLength: 2, maxLength: 10 })
});

// 生成无效银行卡信息
const invalidBankInfoArb = fc.oneof(
  fc.constant(null),
  fc.constant(undefined),
  fc.record({
    cardNumber: fc.string({ minLength: 0, maxLength: 15 }),
    bankName: fc.constant(''),
    holderName: fc.constant('')
  })
);

describe('Property 24: Withdrawal Minimum Validation', () => {
  
  describe('最低金额验证', () => {
    
    it('低于最低金额的提现被拒绝', () => {
      fc.assert(
        fc.property(
          fc.integer({ min: 1, max: 4999 }).map(n => n / 100), // 0.01 到 49.99
          fc.integer({ min: 10000, max: 100000 }).map(n => n / 100), // 足够的余额
          (amount, balance) => {
            const result = validateWithdrawalAmount(amount, balance);
            
            expect(result.valid).toBe(false);
            expect(result.error).toContain('最低');
          }
        )
      );
    });

    it('等于最低金额的提现被接受', () => {
      fc.assert(
        fc.property(
          fc.integer({ min: 10000, max: 100000 }).map(n => n / 100),
          (balance) => {
            const result = validateWithdrawalAmount(DEFAULT_CONFIG.minAmount, balance);
            
            expect(result.valid).toBe(true);
            expect(result.error).toBeNull();
          }
        )
      );
    });

    it('高于最低金额的提现被接受（在余额范围内）', () => {
      fc.assert(
        fc.property(
          fc.integer({ min: 5000, max: 100000 }).map(n => n / 100), // 50 到 1000
          (amount) => {
            const balance = amount + 100; // 确保余额足够
            const result = validateWithdrawalAmount(amount, balance);
            
            expect(result.valid).toBe(true);
          }
        )
      );
    });

    it('meetsMinimumAmount 正确判断', () => {
      fc.assert(
        fc.property(amountArb, (amount) => {
          const meets = meetsMinimumAmount(amount);
          
          expect(meets).toBe(amount >= DEFAULT_CONFIG.minAmount);
        })
      );
    });
  });

  describe('余额验证', () => {
    
    it('提现金额超过余额被拒绝', () => {
      fc.assert(
        fc.property(
          fc.integer({ min: 5000, max: 100000 }).map(n => n / 100),
          fc.integer({ min: 1, max: 100 }).map(n => n / 100),
          (amount, extra) => {
            const balance = amount - extra; // 余额不足
            fc.pre(balance > 0);
            
            const result = validateWithdrawalAmount(amount, balance);
            
            expect(result.valid).toBe(false);
            expect(result.error).toContain('余额');
          }
        )
      );
    });

    it('提现金额等于余额被接受', () => {
      fc.assert(
        fc.property(
          fc.integer({ min: 5000, max: 100000 }).map(n => n / 100),
          (amount) => {
            const result = validateWithdrawalAmount(amount, amount);
            
            expect(result.valid).toBe(true);
          }
        )
      );
    });
  });

  describe('单笔限额验证', () => {
    
    it('超过单笔最高限额被拒绝', () => {
      fc.assert(
        fc.property(
          fc.integer({ min: 1, max: 10000 }).map(n => n / 100),
          (extra) => {
            const amount = DEFAULT_CONFIG.maxAmount + extra;
            const balance = amount + 1000;
            
            const result = validateWithdrawalAmount(amount, balance);
            
            expect(result.valid).toBe(false);
            expect(result.error).toContain('最高');
          }
        )
      );
    });
  });

  describe('每日限额验证', () => {
    
    it('超过每日限额被拒绝', () => {
      fc.assert(
        fc.property(
          fc.integer({ min: 5000, max: 10000 }).map(n => n / 100),
          (amount) => {
            const todayWithdrawn = DEFAULT_CONFIG.dailyLimit; // 已用完
            
            const result = validateDailyLimit(amount, todayWithdrawn);
            
            expect(result.valid).toBe(false);
            expect(result.remaining).toBe(0);
          }
        )
      );
    });

    it('在每日限额内被接受', () => {
      fc.assert(
        fc.property(
          fc.integer({ min: 5000, max: 10000 }).map(n => n / 100),
          (amount) => {
            const todayWithdrawn = 0;
            
            const result = validateDailyLimit(amount, todayWithdrawn);
            
            expect(result.valid).toBe(true);
            expect(result.remaining).toBe(DEFAULT_CONFIG.dailyLimit - amount);
          }
        )
      );
    });

    it('剩余额度正确计算', () => {
      fc.assert(
        fc.property(
          fc.integer({ min: 0, max: DEFAULT_CONFIG.dailyLimit * 100 }).map(n => n / 100),
          fc.integer({ min: 5000, max: 10000 }).map(n => n / 100),
          (todayWithdrawn, amount) => {
            const result = validateDailyLimit(amount, todayWithdrawn);
            
            if (result.valid) {
              expect(result.remaining).toBe(DEFAULT_CONFIG.dailyLimit - todayWithdrawn - amount);
            }
          }
        )
      );
    });
  });

  describe('银行卡验证', () => {
    
    it('有效银行卡信息被接受', () => {
      fc.assert(
        fc.property(validBankInfoArb, (bankInfo) => {
          const result = validateBankInfo(bankInfo);
          
          expect(result.valid).toBe(true);
          expect(result.error).toBeNull();
        })
      );
    });

    it('无效银行卡信息被拒绝', () => {
      fc.assert(
        fc.property(invalidBankInfoArb, (bankInfo) => {
          const result = validateBankInfo(bankInfo);
          
          expect(result.valid).toBe(false);
          expect(result.error).toBeTruthy();
        })
      );
    });
  });

  describe('完整提现申请', () => {
    
    it('有效申请成功创建', () => {
      fc.assert(
        fc.property(
          fc.integer({ min: 5000, max: 100000 }).map(n => n / 100),
          validBankInfoArb,
          (amount, bankInfo) => {
            const balance = amount + 1000;
            
            const result = createWithdrawalRequest(amount, balance, bankInfo, 0);
            
            expect(result.success).toBe(true);
            expect(result.request).toBeTruthy();
            expect(result.request.status).toBe(WithdrawalStatus.PENDING);
            expect(result.request.amount).toBe(amount);
          }
        )
      );
    });

    it('无效金额申请失败', () => {
      fc.assert(
        fc.property(
          fc.integer({ min: 1, max: 4999 }).map(n => n / 100),
          validBankInfoArb,
          (amount, bankInfo) => {
            const balance = 10000;
            
            const result = createWithdrawalRequest(amount, balance, bankInfo, 0);
            
            expect(result.success).toBe(false);
            expect(result.error).toBeTruthy();
          }
        )
      );
    });

    it('无效银行卡申请失败', () => {
      fc.assert(
        fc.property(
          fc.integer({ min: 5000, max: 100000 }).map(n => n / 100),
          invalidBankInfoArb,
          (amount, bankInfo) => {
            const balance = amount + 1000;
            
            const result = createWithdrawalRequest(amount, balance, bankInfo, 0);
            
            expect(result.success).toBe(false);
            expect(result.error).toBeTruthy();
          }
        )
      );
    });
  });

  describe('银行卡号掩码', () => {
    
    it('正确掩码银行卡号', () => {
      fc.assert(
        fc.property(
          fc.stringOf(fc.constantFrom('0', '1', '2', '3', '4', '5', '6', '7', '8', '9'), { minLength: 16, maxLength: 19 }),
          (cardNumber) => {
            const masked = maskCardNumber(cardNumber);
            
            expect(masked.startsWith(cardNumber.slice(0, 4))).toBe(true);
            expect(masked.endsWith(cardNumber.slice(-4))).toBe(true);
            expect(masked).toContain('****');
          }
        )
      );
    });

    it('短卡号不掩码', () => {
      fc.assert(
        fc.property(
          fc.string({ minLength: 0, maxLength: 7 }),
          (cardNumber) => {
            const masked = maskCardNumber(cardNumber);
            
            expect(masked).toBe(cardNumber);
          }
        )
      );
    });
  });

  describe('可提现金额计算', () => {
    
    it('可提现金额不超过可用余额', () => {
      fc.assert(
        fc.property(
          fc.integer({ min: 0, max: 1000000 }).map(n => n / 100),
          fc.integer({ min: 0, max: 1000000 }).map(n => n / 100),
          (balance, todayWithdrawn) => {
            const withdrawable = calculateWithdrawableAmount(balance, todayWithdrawn);
            
            expect(withdrawable).toBeLessThanOrEqual(balance);
          }
        )
      );
    });

    it('可提现金额不超过单笔限额', () => {
      fc.assert(
        fc.property(
          fc.integer({ min: 0, max: 10000000 }).map(n => n / 100),
          fc.integer({ min: 0, max: 1000000 }).map(n => n / 100),
          (balance, todayWithdrawn) => {
            const withdrawable = calculateWithdrawableAmount(balance, todayWithdrawn);
            
            expect(withdrawable).toBeLessThanOrEqual(DEFAULT_CONFIG.maxAmount);
          }
        )
      );
    });

    it('可提现金额不超过每日剩余限额', () => {
      fc.assert(
        fc.property(
          fc.integer({ min: 0, max: 10000000 }).map(n => n / 100),
          fc.integer({ min: 0, max: 1000000 }).map(n => n / 100),
          (balance, todayWithdrawn) => {
            const withdrawable = calculateWithdrawableAmount(balance, todayWithdrawn);
            const dailyRemaining = Math.max(0, DEFAULT_CONFIG.dailyLimit - todayWithdrawn);
            
            expect(withdrawable).toBeLessThanOrEqual(dailyRemaining);
          }
        )
      );
    });

    it('可提现金额非负', () => {
      fc.assert(
        fc.property(
          fc.integer({ min: 0, max: 10000000 }).map(n => n / 100),
          fc.integer({ min: 0, max: 10000000 }).map(n => n / 100),
          (balance, todayWithdrawn) => {
            const withdrawable = calculateWithdrawableAmount(balance, todayWithdrawn);
            
            expect(withdrawable).toBeGreaterThanOrEqual(0);
          }
        )
      );
    });
  });
});
