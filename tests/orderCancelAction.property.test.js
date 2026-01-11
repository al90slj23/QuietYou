/**
 * Property 14: Cancel Action Availability
 * Feature: qingyang-platform, Property 14: Cancel Action Availability
 * Validates: Requirements 5.4
 * 
 * For any order, the cancel action SHALL be available IF AND ONLY IF
 * the order status is "upcoming" (status codes 0, 1, or 2).
 */

import { describe, it, expect } from 'vitest';
import fc from 'fast-check';
import {
  ORDER_STATUS,
  CANCELLABLE_STATUSES,
  canCancelOrder,
  getCancelAvailability,
  cancelOrder,
  getAvailableActions,
  validateCancelResult
} from './lib/orderCancelAction.js';

// 可取消状态生成器
const cancellableStatusArbitrary = fc.constantFrom(
  ORDER_STATUS.PENDING_PAYMENT,
  ORDER_STATUS.PAID,
  ORDER_STATUS.ACCEPTED
);

// 不可取消状态生成器
const nonCancellableStatusArbitrary = fc.constantFrom(
  ORDER_STATUS.DEPARTING,
  ORDER_STATUS.IN_SERVICE,
  ORDER_STATUS.COMPLETED,
  ORDER_STATUS.CANCELLED,
  ORDER_STATUS.REFUNDED
);

// 所有状态生成器
const allStatusArbitrary = fc.constantFrom(
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
const orderArbitrary = (statusArb) => fc.record({
  orderNo: fc.stringOf(fc.constantFrom(...'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), { minLength: 10, maxLength: 20 }),
  status: statusArb,
  serviceName: fc.constant('测试服务'),
  price: fc.integer({ min: 50, max: 1000 }),
  hasReview: fc.boolean()
});

describe('Property 14: Cancel Action Availability', () => {
  /**
   * Property: Cancel is available for cancellable statuses
   * For any order with status 0, 1, or 2, cancel action should be available.
   */
  it('cancel should be available for pending/paid/accepted orders', () => {
    fc.assert(
      fc.property(
        orderArbitrary(cancellableStatusArbitrary),
        (order) => {
          expect(canCancelOrder(order)).toBe(true);
          
          const availability = getCancelAvailability(order);
          expect(availability.available).toBe(true);
          expect(availability.reason).toBeNull();
        }
      ),
      { numRuns: 100 }
    );
  });

  /**
   * Property: Cancel is NOT available for non-cancellable statuses
   * For any order with status 3, 4, 5, 6, or 7, cancel action should NOT be available.
   */
  it('cancel should NOT be available for departing/serving/completed/cancelled orders', () => {
    fc.assert(
      fc.property(
        orderArbitrary(nonCancellableStatusArbitrary),
        (order) => {
          expect(canCancelOrder(order)).toBe(false);
          
          const availability = getCancelAvailability(order);
          expect(availability.available).toBe(false);
          expect(availability.reason).not.toBeNull();
        }
      ),
      { numRuns: 100 }
    );
  });

  /**
   * Property: Cancel action appears in actions list IFF order is cancellable
   * The 'cancel' action should be in the available actions list if and only if
   * the order status is cancellable.
   */
  it('cancel action should appear in actions list iff order is cancellable', () => {
    fc.assert(
      fc.property(
        orderArbitrary(allStatusArbitrary),
        (order) => {
          const actions = getAvailableActions(order);
          const hasCancelAction = actions.includes('cancel');
          const isCancellable = canCancelOrder(order);
          
          expect(hasCancelAction).toBe(isCancellable);
        }
      ),
      { numRuns: 100 }
    );
  });

  /**
   * Property: Successful cancel changes status to CANCELLED
   * For any cancellable order, cancelling it should change status to CANCELLED.
   */
  it('successful cancel should change status to CANCELLED', () => {
    fc.assert(
      fc.property(
        orderArbitrary(cancellableStatusArbitrary),
        fc.string({ minLength: 1, maxLength: 50 }),
        (order, reason) => {
          const result = cancelOrder(order, reason);
          
          expect(result.success).toBe(true);
          expect(result.order.status).toBe(ORDER_STATUS.CANCELLED);
          expect(result.error).toBeNull();
        }
      ),
      { numRuns: 100 }
    );
  });

  /**
   * Property: Failed cancel preserves original order
   * For any non-cancellable order, attempting to cancel should fail and preserve the order.
   */
  it('failed cancel should preserve original order status', () => {
    fc.assert(
      fc.property(
        orderArbitrary(nonCancellableStatusArbitrary),
        (order) => {
          const originalStatus = order.status;
          const result = cancelOrder(order);
          
          expect(result.success).toBe(false);
          expect(result.order.status).toBe(originalStatus);
          expect(result.error).not.toBeNull();
        }
      ),
      { numRuns: 100 }
    );
  });

  /**
   * Property: Cancel result validation passes for successful cancels
   * For any successful cancel, the result should pass validation.
   */
  it('successful cancel result should pass validation', () => {
    fc.assert(
      fc.property(
        orderArbitrary(cancellableStatusArbitrary),
        (order) => {
          const result = cancelOrder(order);
          
          if (result.success) {
            expect(validateCancelResult(order, result.order)).toBe(true);
          }
        }
      ),
      { numRuns: 100 }
    );
  });

  /**
   * Property: Cancelled order has cancel time and reason
   * For any successfully cancelled order, it should have cancelTime and cancelReason.
   */
  it('cancelled order should have cancelTime and cancelReason', () => {
    fc.assert(
      fc.property(
        orderArbitrary(cancellableStatusArbitrary),
        fc.string({ minLength: 1, maxLength: 50 }),
        (order, reason) => {
          const result = cancelOrder(order, reason);
          
          if (result.success) {
            expect(result.order.cancelTime).toBeDefined();
            expect(result.order.cancelReason).toBe(reason);
          }
        }
      ),
      { numRuns: 100 }
    );
  });

  /**
   * Property: Order number is preserved after cancel
   * The order number should remain the same after cancellation.
   */
  it('order number should be preserved after cancel', () => {
    fc.assert(
      fc.property(
        orderArbitrary(cancellableStatusArbitrary),
        (order) => {
          const result = cancelOrder(order);
          
          expect(result.order.orderNo).toBe(order.orderNo);
        }
      ),
      { numRuns: 100 }
    );
  });

  /**
   * Property: Invalid orders cannot be cancelled
   * Null or undefined orders should not be cancellable.
   */
  it('invalid orders should not be cancellable', () => {
    expect(canCancelOrder(null)).toBe(false);
    expect(canCancelOrder(undefined)).toBe(false);
    expect(canCancelOrder({})).toBe(false);
    expect(canCancelOrder({ status: 'invalid' })).toBe(false);
  });

  /**
   * Property: getCancelAvailability returns valid reasons for non-cancellable orders
   */
  it('getCancelAvailability should return valid reasons', () => {
    const validReasons = [
      'technician_departing',
      'service_in_progress', 
      'order_completed',
      'already_cancelled',
      'already_refunded',
      'invalid_order',
      'invalid_status',
      'unknown_status'
    ];

    fc.assert(
      fc.property(
        orderArbitrary(nonCancellableStatusArbitrary),
        (order) => {
          const availability = getCancelAvailability(order);
          
          expect(validReasons).toContain(availability.reason);
        }
      ),
      { numRuns: 100 }
    );
  });
});
