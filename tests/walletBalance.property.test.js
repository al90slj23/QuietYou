/**
 * Property 23: Wallet Balance Consistency
 * 钱包余额一致性属性测试
 * Validates: Requirements 10.1, 10.4
 * 
 * 核心属性：
 * 1. 余额不能为负数
 * 2. 可用余额 + 待结算 + 已提现 = 累计收入
 * 3. 提现金额不能超过可用余额
 * 4. 结算金额不能超过待结算余额
 */

import { describe, it, expect } from 'vitest';
import * as fc from 'fast-check';
import {
  TransactionType,
  createWallet,
  addOrderIncome,
  settleBalance,
  requestWithdrawal,
  refundWithdrawal,
  deductRefund,
  validateBalanceConsistency,
  getWalletSummary
} from './lib/walletBalance.js';

// 生成正数金额
const positiveAmountArb = fc.integer({ min: 1, max: 1000000 })
  .map(n => n / 100); // 0.01 到 10000

// 生成钱包初始状态
const walletArb = fc.record({
  availableBalance: fc.integer({ min: 0, max: 500000 }).map(n => n / 100),
  pendingBalance: fc.integer({ min: 0, max: 500000 }).map(n => n / 100),
  totalEarnings: fc.integer({ min: 0, max: 2000000 }).map(n => n / 100)
}).map(w => createWallet(w.availableBalance, w.pendingBalance, 
  Math.max(w.totalEarnings, w.availableBalance + w.pendingBalance)));

describe('Property 23: Wallet Balance Consistency', () => {
  
  describe('余额非负性', () => {
    
    it('创建钱包时余额不能为负', () => {
      fc.assert(
        fc.property(
          fc.integer({ min: -100000, max: 100000 }).map(n => n / 100),
          fc.integer({ min: -100000, max: 100000 }).map(n => n / 100),
          fc.integer({ min: -100000, max: 100000 }).map(n => n / 100),
          (available, pending, total) => {
            const wallet = createWallet(available, pending, total);
            
            expect(wallet.availableBalance).toBeGreaterThanOrEqual(0);
            expect(wallet.pendingBalance).toBeGreaterThanOrEqual(0);
            expect(wallet.totalEarnings).toBeGreaterThanOrEqual(0);
          }
        )
      );
    });

    it('订单收入后余额保持非负', () => {
      fc.assert(
        fc.property(walletArb, positiveAmountArb, fc.uuid(), (wallet, amount, orderId) => {
          const result = addOrderIncome(wallet, amount, orderId);
          
          if (!result.error) {
            expect(result.availableBalance).toBeGreaterThanOrEqual(0);
            expect(result.pendingBalance).toBeGreaterThanOrEqual(0);
            expect(result.totalEarnings).toBeGreaterThanOrEqual(0);
          }
        })
      );
    });
  });

  describe('订单收入', () => {
    
    it('订单收入增加待结算余额', () => {
      fc.assert(
        fc.property(walletArb, positiveAmountArb, fc.uuid(), (wallet, amount, orderId) => {
          const result = addOrderIncome(wallet, amount, orderId);
          
          expect(result.error).toBeNull();
          expect(result.pendingBalance).toBeCloseTo(wallet.pendingBalance + amount, 2);
          expect(result.availableBalance).toBeCloseTo(wallet.availableBalance, 2);
        })
      );
    });

    it('订单收入增加累计收入', () => {
      fc.assert(
        fc.property(walletArb, positiveAmountArb, fc.uuid(), (wallet, amount, orderId) => {
          const result = addOrderIncome(wallet, amount, orderId);
          
          expect(result.totalEarnings).toBeCloseTo(wallet.totalEarnings + amount, 2);
        })
      );
    });

    it('零或负金额订单收入被拒绝', () => {
      fc.assert(
        fc.property(
          walletArb,
          fc.integer({ min: -100000, max: 0 }).map(n => n / 100),
          fc.uuid(),
          (wallet, amount, orderId) => {
            const result = addOrderIncome(wallet, amount, orderId);
            
            expect(result.error).toBeTruthy();
            expect(result.pendingBalance).toBe(wallet.pendingBalance);
          }
        )
      );
    });
  });

  describe('余额结算', () => {
    
    it('结算将待结算转为可用', () => {
      fc.assert(
        fc.property(walletArb, (wallet) => {
          // 确保有待结算余额
          fc.pre(wallet.pendingBalance > 0);
          
          const settleAmount = wallet.pendingBalance * 0.5; // 结算一半
          const result = settleBalance(wallet, settleAmount);
          
          expect(result.error).toBeNull();
          expect(result.availableBalance).toBeCloseTo(wallet.availableBalance + settleAmount, 2);
          expect(result.pendingBalance).toBeCloseTo(wallet.pendingBalance - settleAmount, 2);
        })
      );
    });

    it('结算不改变累计收入', () => {
      fc.assert(
        fc.property(walletArb, (wallet) => {
          fc.pre(wallet.pendingBalance > 0);
          
          const settleAmount = wallet.pendingBalance;
          const result = settleBalance(wallet, settleAmount);
          
          expect(result.totalEarnings).toBeCloseTo(wallet.totalEarnings, 2);
        })
      );
    });

    it('结算金额不能超过待结算余额', () => {
      fc.assert(
        fc.property(walletArb, positiveAmountArb, (wallet, extraAmount) => {
          const overAmount = wallet.pendingBalance + extraAmount + 1;
          const result = settleBalance(wallet, overAmount);
          
          expect(result.error).toBeTruthy();
          expect(result.pendingBalance).toBe(wallet.pendingBalance);
        })
      );
    });
  });

  describe('提现验证', () => {
    
    it('有效提现减少可用余额', () => {
      fc.assert(
        fc.property(walletArb, (wallet) => {
          fc.pre(wallet.availableBalance >= 50);
          
          const withdrawAmount = Math.min(wallet.availableBalance, 100);
          const result = requestWithdrawal(wallet, withdrawAmount, 50);
          
          expect(result.error).toBeNull();
          expect(result.availableBalance).toBeCloseTo(wallet.availableBalance - withdrawAmount, 2);
        })
      );
    });

    it('提现不影响待结算和累计收入', () => {
      fc.assert(
        fc.property(walletArb, (wallet) => {
          fc.pre(wallet.availableBalance >= 50);
          
          const withdrawAmount = 50;
          const result = requestWithdrawal(wallet, withdrawAmount, 50);
          
          expect(result.pendingBalance).toBeCloseTo(wallet.pendingBalance, 2);
          expect(result.totalEarnings).toBeCloseTo(wallet.totalEarnings, 2);
        })
      );
    });

    it('提现金额不能超过可用余额', () => {
      fc.assert(
        fc.property(walletArb, positiveAmountArb, (wallet, extraAmount) => {
          const overAmount = wallet.availableBalance + extraAmount + 1;
          const result = requestWithdrawal(wallet, overAmount, 50);
          
          expect(result.error).toBeTruthy();
          expect(result.availableBalance).toBe(wallet.availableBalance);
        })
      );
    });
  });

  describe('提现失败退回', () => {
    
    it('提现失败退回增加可用余额', () => {
      fc.assert(
        fc.property(walletArb, positiveAmountArb, (wallet, amount) => {
          const result = refundWithdrawal(wallet, amount);
          
          expect(result.error).toBeNull();
          expect(result.availableBalance).toBeCloseTo(wallet.availableBalance + amount, 2);
        })
      );
    });
  });

  describe('退款扣除', () => {
    
    it('退款优先从待结算扣除', () => {
      fc.assert(
        fc.property(walletArb, fc.uuid(), (wallet) => {
          fc.pre(wallet.pendingBalance > 0);
          
          const refundAmount = Math.min(wallet.pendingBalance * 0.5, wallet.totalEarnings);
          fc.pre(refundAmount > 0);
          
          const result = deductRefund(wallet, refundAmount, 'order-1');
          
          if (!result.error) {
            // 待结算应该减少
            expect(result.pendingBalance).toBeLessThanOrEqual(wallet.pendingBalance);
          }
        })
      );
    });

    it('退款减少累计收入', () => {
      fc.assert(
        fc.property(walletArb, fc.uuid(), (wallet) => {
          const totalBalance = wallet.availableBalance + wallet.pendingBalance;
          fc.pre(totalBalance > 0);
          
          const refundAmount = Math.min(totalBalance * 0.5, wallet.totalEarnings);
          fc.pre(refundAmount > 0);
          
          const result = deductRefund(wallet, refundAmount, 'order-1');
          
          if (!result.error) {
            expect(result.totalEarnings).toBeCloseTo(wallet.totalEarnings - refundAmount, 2);
          }
        })
      );
    });
  });

  describe('交易记录', () => {
    
    it('每次操作都记录交易', () => {
      fc.assert(
        fc.property(walletArb, positiveAmountArb, fc.uuid(), (wallet, amount, orderId) => {
          const originalCount = wallet.transactions.length;
          const result = addOrderIncome(wallet, amount, orderId);
          
          expect(result.transactions.length).toBe(originalCount + 1);
        })
      );
    });

    it('交易记录包含正确的类型', () => {
      fc.assert(
        fc.property(walletArb, positiveAmountArb, fc.uuid(), (wallet, amount, orderId) => {
          const result = addOrderIncome(wallet, amount, orderId);
          const lastTx = result.transactions[result.transactions.length - 1];
          
          expect(lastTx.type).toBe(TransactionType.ORDER_INCOME);
          expect(lastTx.amount).toBe(amount);
        })
      );
    });
  });

  describe('钱包摘要', () => {
    
    it('摘要包含所有必要字段', () => {
      fc.assert(
        fc.property(walletArb, (wallet) => {
          const summary = getWalletSummary(wallet);
          
          expect(summary).toHaveProperty('availableBalance');
          expect(summary).toHaveProperty('pendingBalance');
          expect(summary).toHaveProperty('totalEarnings');
          expect(summary).toHaveProperty('transactionCount');
        })
      );
    });

    it('摘要数值与钱包一致', () => {
      fc.assert(
        fc.property(walletArb, (wallet) => {
          const summary = getWalletSummary(wallet);
          
          expect(summary.availableBalance).toBe(wallet.availableBalance);
          expect(summary.pendingBalance).toBe(wallet.pendingBalance);
          expect(summary.totalEarnings).toBe(wallet.totalEarnings);
          expect(summary.transactionCount).toBe(wallet.transactions.length);
        })
      );
    });
  });
});
