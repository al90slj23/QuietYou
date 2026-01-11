/**
 * Property 20: Order Status Transitions
 * 订单状态转换属性测试
 * Validates: Requirements 8.3, 8.6
 * 
 * 核心属性：
 * 1. 状态转换必须遵循预定义的有效路径
 * 2. 终态（已完成、已取消）不能再转换
 * 3. 服务流程必须按顺序执行
 * 4. 取消操作只在特定状态可用
 */

import { describe, it, expect } from 'vitest';
import * as fc from 'fast-check';
import {
  OrderStatus,
  validTransitions,
  isValidTransition,
  transitionStatus,
  getAvailableActions,
  canCancel,
  isTerminalStatus,
  isInServiceFlow,
  getStatusText,
  simulateServiceFlow
} from './lib/orderStatusTransition.js';

// 生成订单状态
const statusArb = fc.constantFrom(...Object.values(OrderStatus));

// 生成非终态状态
const nonTerminalStatusArb = fc.constantFrom(
  OrderStatus.PENDING_PAYMENT,
  OrderStatus.PENDING_ACCEPT,
  OrderStatus.ACCEPTED,
  OrderStatus.DEPARTED,
  OrderStatus.ARRIVED,
  OrderStatus.IN_PROGRESS
);

// 生成订单对象
const orderArb = fc.record({
  id: fc.uuid(),
  status: statusArb,
  statusHistory: fc.array(fc.record({
    from: statusArb,
    to: statusArb,
    timestamp: fc.date().map(d => d.toISOString())
  }), { minLength: 0, maxLength: 5 })
});

describe('Property 20: Order Status Transitions', () => {
  
  describe('状态转换有效性', () => {
    
    it('有效转换应该成功执行', () => {
      fc.assert(
        fc.property(orderArb, (order) => {
          const allowedTransitions = validTransitions[order.status] || [];
          
          if (allowedTransitions.length > 0) {
            const targetStatus = allowedTransitions[0];
            const result = transitionStatus(order, targetStatus);
            
            expect(result.success).toBe(true);
            expect(result.order.status).toBe(targetStatus);
            expect(result.error).toBeNull();
          }
        })
      );
    });

    it('无效转换应该失败', () => {
      fc.assert(
        fc.property(orderArb, statusArb, (order, targetStatus) => {
          const allowedTransitions = validTransitions[order.status] || [];
          
          // 如果目标状态不在允许列表中
          if (!allowedTransitions.includes(targetStatus)) {
            const result = transitionStatus(order, targetStatus);
            
            expect(result.success).toBe(false);
            expect(result.order.status).toBe(order.status); // 状态不变
            expect(result.error).toBeTruthy();
          }
        })
      );
    });

    it('isValidTransition 与 transitionStatus 结果一致', () => {
      fc.assert(
        fc.property(orderArb, statusArb, (order, targetStatus) => {
          const isValid = isValidTransition(order.status, targetStatus);
          const result = transitionStatus(order, targetStatus);
          
          expect(result.success).toBe(isValid);
        })
      );
    });
  });

  describe('终态不可变性', () => {
    
    it('已完成状态不能转换到任何其他状态', () => {
      fc.assert(
        fc.property(statusArb, (targetStatus) => {
          const completedOrder = { id: '1', status: OrderStatus.COMPLETED, statusHistory: [] };
          const result = transitionStatus(completedOrder, targetStatus);
          
          expect(result.success).toBe(false);
          expect(result.order.status).toBe(OrderStatus.COMPLETED);
        })
      );
    });

    it('已取消状态不能转换到任何其他状态', () => {
      fc.assert(
        fc.property(statusArb, (targetStatus) => {
          const cancelledOrder = { id: '1', status: OrderStatus.CANCELLED, statusHistory: [] };
          const result = transitionStatus(cancelledOrder, targetStatus);
          
          expect(result.success).toBe(false);
          expect(result.order.status).toBe(OrderStatus.CANCELLED);
        })
      );
    });

    it('isTerminalStatus 正确识别终态', () => {
      expect(isTerminalStatus(OrderStatus.COMPLETED)).toBe(true);
      expect(isTerminalStatus(OrderStatus.CANCELLED)).toBe(true);
      
      fc.assert(
        fc.property(nonTerminalStatusArb, (status) => {
          expect(isTerminalStatus(status)).toBe(false);
        })
      );
    });
  });

  describe('服务流程顺序', () => {
    
    it('服务流程必须按顺序执行：出发→到达→开始→完成', () => {
      const acceptedOrder = { 
        id: '1', 
        status: OrderStatus.ACCEPTED, 
        statusHistory: [] 
      };
      
      const history = simulateServiceFlow(acceptedOrder);
      
      // 所有转换都应该成功
      history.forEach(h => {
        expect(h.success).toBe(true);
      });
      
      // 验证顺序
      expect(history[0].to).toBe(OrderStatus.DEPARTED);
      expect(history[1].to).toBe(OrderStatus.ARRIVED);
      expect(history[2].to).toBe(OrderStatus.IN_PROGRESS);
      expect(history[3].to).toBe(OrderStatus.COMPLETED);
    });

    it('不能跳过服务流程中的步骤', () => {
      // 不能从已接单直接到服务中
      const order1 = { id: '1', status: OrderStatus.ACCEPTED, statusHistory: [] };
      expect(transitionStatus(order1, OrderStatus.IN_PROGRESS).success).toBe(false);
      
      // 不能从已出发直接到服务中
      const order2 = { id: '2', status: OrderStatus.DEPARTED, statusHistory: [] };
      expect(transitionStatus(order2, OrderStatus.IN_PROGRESS).success).toBe(false);
      
      // 不能从已到达直接到完成
      const order3 = { id: '3', status: OrderStatus.ARRIVED, statusHistory: [] };
      expect(transitionStatus(order3, OrderStatus.COMPLETED).success).toBe(false);
    });

    it('isInServiceFlow 正确识别服务流程状态', () => {
      expect(isInServiceFlow(OrderStatus.DEPARTED)).toBe(true);
      expect(isInServiceFlow(OrderStatus.ARRIVED)).toBe(true);
      expect(isInServiceFlow(OrderStatus.IN_PROGRESS)).toBe(true);
      
      expect(isInServiceFlow(OrderStatus.PENDING_PAYMENT)).toBe(false);
      expect(isInServiceFlow(OrderStatus.ACCEPTED)).toBe(false);
      expect(isInServiceFlow(OrderStatus.COMPLETED)).toBe(false);
    });
  });

  describe('取消操作可用性', () => {
    
    it('待支付、待接单、已接单状态可以取消', () => {
      expect(canCancel(OrderStatus.PENDING_PAYMENT)).toBe(true);
      expect(canCancel(OrderStatus.PENDING_ACCEPT)).toBe(true);
      expect(canCancel(OrderStatus.ACCEPTED)).toBe(true);
    });

    it('服务流程中的状态不能取消', () => {
      expect(canCancel(OrderStatus.DEPARTED)).toBe(false);
      expect(canCancel(OrderStatus.ARRIVED)).toBe(false);
      expect(canCancel(OrderStatus.IN_PROGRESS)).toBe(false);
    });

    it('终态不能取消', () => {
      expect(canCancel(OrderStatus.COMPLETED)).toBe(false);
      expect(canCancel(OrderStatus.CANCELLED)).toBe(false);
    });

    it('canCancel 与 validTransitions 一致', () => {
      fc.assert(
        fc.property(statusArb, (status) => {
          const canCancelStatus = canCancel(status);
          const transitions = validTransitions[status] || [];
          const hasCancel = transitions.includes(OrderStatus.CANCELLED);
          
          expect(canCancelStatus).toBe(hasCancel);
        })
      );
    });
  });

  describe('可用操作', () => {
    
    it('每个状态都有对应的可用操作', () => {
      fc.assert(
        fc.property(statusArb, (status) => {
          const actions = getAvailableActions(status);
          
          expect(Array.isArray(actions)).toBe(true);
        })
      );
    });

    it('终态没有可用操作', () => {
      expect(getAvailableActions(OrderStatus.COMPLETED)).toEqual([]);
      expect(getAvailableActions(OrderStatus.CANCELLED)).toEqual([]);
    });

    it('非终态至少有一个可用操作', () => {
      fc.assert(
        fc.property(nonTerminalStatusArb, (status) => {
          const actions = getAvailableActions(status);
          
          expect(actions.length).toBeGreaterThan(0);
        })
      );
    });
  });

  describe('状态历史记录', () => {
    
    it('成功转换会记录历史', () => {
      fc.assert(
        fc.property(orderArb, (order) => {
          const allowedTransitions = validTransitions[order.status] || [];
          
          if (allowedTransitions.length > 0) {
            const targetStatus = allowedTransitions[0];
            const originalHistoryLength = (order.statusHistory || []).length;
            const result = transitionStatus(order, targetStatus);
            
            expect(result.order.statusHistory.length).toBe(originalHistoryLength + 1);
            
            const lastRecord = result.order.statusHistory[result.order.statusHistory.length - 1];
            expect(lastRecord.from).toBe(order.status);
            expect(lastRecord.to).toBe(targetStatus);
          }
        })
      );
    });

    it('失败转换不会记录历史', () => {
      fc.assert(
        fc.property(orderArb, statusArb, (order, targetStatus) => {
          const allowedTransitions = validTransitions[order.status] || [];
          
          if (!allowedTransitions.includes(targetStatus)) {
            const originalHistoryLength = (order.statusHistory || []).length;
            const result = transitionStatus(order, targetStatus);
            
            expect((result.order.statusHistory || []).length).toBe(originalHistoryLength);
          }
        })
      );
    });
  });

  describe('状态显示文本', () => {
    
    it('所有状态都有显示文本', () => {
      fc.assert(
        fc.property(statusArb, (status) => {
          const text = getStatusText(status);
          
          expect(text).toBeTruthy();
          expect(typeof text).toBe('string');
          expect(text.length).toBeGreaterThan(0);
        })
      );
    });
  });
});
