/**
 * Property 12: Order List Grouping
 * Feature: qingyang-platform, Property 12: Order List Grouping
 * Validates: Requirements 5.1
 * 
 * For any order in the order list, it SHALL appear in exactly one status group,
 * and the group SHALL match the order's actual status.
 */

import { describe, it, expect } from 'vitest';
import fc from 'fast-check';
import {
  ORDER_STATUS,
  STATUS_GROUPS,
  getOrderGroup,
  groupOrdersByStatus,
  filterOrdersByGroup,
  orderBelongsToExactlyOneGroup,
  validateGrouping,
  getAllValidStatuses,
  isValidStatus
} from './lib/orderGrouping.js';

// 订单状态生成器
const statusArbitrary = fc.constantFrom(
  ORDER_STATUS.PENDING_PAYMENT,
  ORDER_STATUS.PAID,
  ORDER_STATUS.ACCEPTED,
  ORDER_STATUS.DEPARTING,
  ORDER_STATUS.IN_SERVICE,
  ORDER_STATUS.COMPLETED,
  ORDER_STATUS.CANCELLED,
  ORDER_STATUS.REFUNDED
);

// 订单对象生成器
const orderArbitrary = fc.record({
  orderNo: fc.stringOf(fc.constantFrom(...'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), { minLength: 10, maxLength: 20 }),
  status: statusArbitrary,
  serviceName: fc.stringOf(fc.constantFrom(...'abcdefghijklmnopqrstuvwxyz中文服务'), { minLength: 2, maxLength: 20 }),
  price: fc.integer({ min: 50, max: 1000 }),
  techName: fc.stringOf(fc.constantFrom(...'张李王赵师傅'), { minLength: 2, maxLength: 6 })
});

// 订单列表生成器
const ordersArbitrary = fc.array(orderArbitrary, { minLength: 0, maxLength: 50 });

describe('Property 12: Order List Grouping', () => {
  /**
   * Property: Each order belongs to exactly one group
   * For any order with a valid status, it should belong to exactly one status group.
   */
  it('each order should belong to exactly one status group', () => {
    fc.assert(
      fc.property(
        orderArbitrary,
        (order) => {
          expect(orderBelongsToExactlyOneGroup(order)).toBe(true);
        }
      ),
      { numRuns: 100 }
    );
  });

  /**
   * Property: Order appears in correct group
   * For any order, it should appear in the group that matches its status.
   */
  it('order should appear in the group matching its status', () => {
    fc.assert(
      fc.property(
        orderArbitrary,
        (order) => {
          const group = getOrderGroup(order.status);
          const validStatuses = STATUS_GROUPS[group];
          
          expect(validStatuses).toContain(order.status);
        }
      ),
      { numRuns: 100 }
    );
  });

  /**
   * Property: Grouping preserves all orders
   * For any list of orders, grouping should preserve all orders (no loss, no duplication).
   */
  it('grouping should preserve all orders without loss or duplication', () => {
    fc.assert(
      fc.property(
        ordersArbitrary,
        (orders) => {
          const groups = groupOrdersByStatus(orders);
          const totalInGroups = Object.values(groups).reduce((sum, g) => sum + g.length, 0);
          
          expect(totalInGroups).toBe(orders.length);
        }
      ),
      { numRuns: 100 }
    );
  });

  /**
   * Property: Each order in a group has correct status
   * For any group, all orders in that group should have a status that belongs to that group.
   */
  it('all orders in a group should have status belonging to that group', () => {
    fc.assert(
      fc.property(
        ordersArbitrary,
        (orders) => {
          const groups = groupOrdersByStatus(orders);
          
          for (const [groupName, groupOrders] of Object.entries(groups)) {
            const validStatuses = STATUS_GROUPS[groupName];
            
            for (const order of groupOrders) {
              expect(validStatuses).toContain(order.status);
            }
          }
        }
      ),
      { numRuns: 100 }
    );
  });

  /**
   * Property: Filtering by group returns correct orders
   * For any group filter, all returned orders should have status in that group.
   */
  it('filtering by group should return only orders with matching status', () => {
    fc.assert(
      fc.property(
        ordersArbitrary,
        fc.constantFrom('pending_payment', 'upcoming', 'in_progress', 'completed', 'cancelled'),
        (orders, groupName) => {
          const filtered = filterOrdersByGroup(orders, groupName);
          const validStatuses = STATUS_GROUPS[groupName];
          
          for (const order of filtered) {
            expect(validStatuses).toContain(order.status);
          }
        }
      ),
      { numRuns: 100 }
    );
  });

  /**
   * Property: Filter 'all' returns all orders
   * Filtering with 'all' should return all orders.
   */
  it('filtering with all should return all orders', () => {
    fc.assert(
      fc.property(
        ordersArbitrary,
        (orders) => {
          const filtered = filterOrdersByGroup(orders, 'all');
          
          expect(filtered.length).toBe(orders.length);
        }
      ),
      { numRuns: 100 }
    );
  });

  /**
   * Property: Grouping validation passes for correct grouping
   * For any list of orders, the grouping result should pass validation.
   */
  it('grouping result should pass validation', () => {
    fc.assert(
      fc.property(
        ordersArbitrary,
        (orders) => {
          const groups = groupOrdersByStatus(orders);
          const validation = validateGrouping(orders, groups);
          
          expect(validation.valid).toBe(true);
          expect(validation.errors).toHaveLength(0);
        }
      ),
      { numRuns: 100 }
    );
  });

  /**
   * Property: All valid statuses are covered by groups
   * Every valid order status should belong to exactly one group.
   */
  it('all valid statuses should be covered by exactly one group', () => {
    const allStatuses = getAllValidStatuses();
    
    for (const status of allStatuses) {
      let groupCount = 0;
      for (const statuses of Object.values(STATUS_GROUPS)) {
        if (statuses.includes(status)) {
          groupCount++;
        }
      }
      expect(groupCount).toBe(1);
    }
  });

  /**
   * Property: isValidStatus correctly identifies valid statuses
   */
  it('isValidStatus should correctly identify valid statuses', () => {
    fc.assert(
      fc.property(
        statusArbitrary,
        (status) => {
          expect(isValidStatus(status)).toBe(true);
        }
      ),
      { numRuns: 100 }
    );
  });

  /**
   * Property: Invalid statuses are rejected
   */
  it('invalid statuses should not be valid', () => {
    fc.assert(
      fc.property(
        fc.integer({ min: 100, max: 1000 }),
        (invalidStatus) => {
          expect(isValidStatus(invalidStatus)).toBe(false);
        }
      ),
      { numRuns: 100 }
    );
  });
});
