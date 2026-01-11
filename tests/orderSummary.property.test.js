/**
 * Property 10: Order Summary Completeness
 * Feature: qingyang-platform, Property 10: Order Summary Completeness
 * Validates: Requirements 4.4
 * 
 * For any order confirmation, the summary SHALL contain:
 * - service name
 * - service price
 * - technician name
 * - scheduled date/time
 * - service address
 * - total amount
 */

import { describe, it, expect } from 'vitest';
import fc from 'fast-check';
import {
  REQUIRED_FIELDS,
  validateOrderSummary,
  hasValidValue,
  generateOrderSummary,
  calculateTotalAmount,
  formatOrderSummary,
  formatPrice,
  formatDateTime,
  validatePriceConsistency
} from './lib/orderSummary.js';

// 日期字符串生成器 (YYYY-MM-DD 格式)
const dateArbitrary = fc.integer({ min: 0, max: 30 }).map(daysFromNow => {
  const date = new Date();
  date.setDate(date.getDate() + daysFromNow);
  return `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(date.getDate()).padStart(2, '0')}`;
});

// 时间字符串生成器 (HH:MM 格式)
const timeArbitrary = fc.integer({ min: 9, max: 20 }).chain(hour =>
  fc.integer({ min: 0, max: 1 }).map(halfHour => 
    `${String(hour).padStart(2, '0')}:${halfHour === 0 ? '00' : '30'}`
  )
);

// 非空字符串生成器（不包含纯空白字符串）
const nonEmptyStringArbitrary = (minLength, maxLength) => 
  fc.stringOf(fc.constantFrom(...'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789中文测试'), { minLength, maxLength })
    .filter(s => s.trim().length > 0);

// 有效订单数据生成器
const validOrderDataArbitrary = fc.record({
  serviceName: nonEmptyStringArbitrary(1, 50),
  price: fc.integer({ min: 50, max: 1000 }),
  techName: nonEmptyStringArbitrary(1, 20),
  date: dateArbitrary,
  time: timeArbitrary,
  address: nonEmptyStringArbitrary(5, 100),
  discount: fc.integer({ min: 0, max: 100 }),
  tip: fc.integer({ min: 0, max: 50 })
});

// 部分订单数据生成器（可能缺少字段）
const partialOrderDataArbitrary = fc.record({
  serviceName: fc.oneof(nonEmptyStringArbitrary(1, 50), fc.constant('')),
  price: fc.oneof(fc.integer({ min: 50, max: 1000 }), fc.constant(0), fc.constant(-1)),
  techName: fc.oneof(nonEmptyStringArbitrary(1, 20), fc.constant('')),
  date: fc.oneof(dateArbitrary, fc.constant('')),
  time: fc.oneof(timeArbitrary, fc.constant('')),
  address: fc.oneof(nonEmptyStringArbitrary(5, 100), fc.constant(''))
});

describe('Property 10: Order Summary Completeness', () => {
  /**
   * Property: Valid order data generates complete summary
   * For any valid order data, the generated summary should contain all required fields.
   */
  it('valid order data should generate complete summary', () => {
    fc.assert(
      fc.property(
        validOrderDataArbitrary,
        (orderData) => {
          const summary = generateOrderSummary(orderData);
          const validation = validateOrderSummary(summary);
          
          expect(validation.complete).toBe(true);
          expect(validation.missingFields).toHaveLength(0);
        }
      ),
      { numRuns: 100 }
    );
  });

  /**
   * Property: Summary contains all required fields
   * For any complete summary, all required fields must be present and valid.
   */
  it('complete summary should contain all required fields', () => {
    fc.assert(
      fc.property(
        validOrderDataArbitrary,
        (orderData) => {
          const summary = generateOrderSummary(orderData);
          
          // Check each required field
          expect(hasValidValue(summary.serviceName)).toBe(true);
          expect(hasValidValue(summary.servicePrice)).toBe(true);
          expect(hasValidValue(summary.techName)).toBe(true);
          expect(hasValidValue(summary.scheduleDate)).toBe(true);
          expect(hasValidValue(summary.scheduleTime)).toBe(true);
          expect(hasValidValue(summary.address)).toBe(true);
          expect(hasValidValue(summary.totalAmount)).toBe(true);
        }
      ),
      { numRuns: 100 }
    );
  });

  /**
   * Property: Total amount calculation is correct
   * For any order, total = price - discount + tip
   */
  it('total amount should be calculated correctly', () => {
    fc.assert(
      fc.property(
        fc.integer({ min: 100, max: 1000 }),
        fc.integer({ min: 0, max: 50 }),
        fc.integer({ min: 0, max: 50 }),
        (price, discount, tip) => {
          const orderData = { price, discount, tip };
          const total = calculateTotalAmount(orderData);
          const expected = Math.max(0, price - discount + tip);
          
          expect(total).toBe(expected);
        }
      ),
      { numRuns: 100 }
    );
  });

  /**
   * Property: Total amount is never negative
   * For any order, the total amount should be >= 0.
   */
  it('total amount should never be negative', () => {
    fc.assert(
      fc.property(
        fc.integer({ min: 0, max: 1000 }),
        fc.integer({ min: 0, max: 500 }),
        fc.integer({ min: 0, max: 100 }),
        (price, discount, tip) => {
          const orderData = { price, discount, tip };
          const total = calculateTotalAmount(orderData);
          
          expect(total).toBeGreaterThanOrEqual(0);
        }
      ),
      { numRuns: 100 }
    );
  });

  /**
   * Property: Price consistency between summary and order data
   * The summary's total amount should match the calculated total from order data.
   */
  it('summary total should match calculated total from order data', () => {
    fc.assert(
      fc.property(
        validOrderDataArbitrary,
        (orderData) => {
          const summary = generateOrderSummary(orderData);
          
          expect(validatePriceConsistency(summary, orderData)).toBe(true);
        }
      ),
      { numRuns: 100 }
    );
  });

  /**
   * Property: Incomplete data results in incomplete summary
   * If required fields are missing from order data, the summary should be marked incomplete.
   */
  it('missing required fields should result in incomplete summary', () => {
    fc.assert(
      fc.property(
        partialOrderDataArbitrary,
        (orderData) => {
          const summary = generateOrderSummary(orderData);
          const validation = validateOrderSummary(summary);
          
          // Count how many fields are actually missing/invalid based on hasValidValue logic
          let expectedMissing = 0;
          if (!hasValidValue(orderData.serviceName)) expectedMissing++;
          // price of 0 is valid according to hasValidValue, but negative is not
          if (!hasValidValue(orderData.price)) expectedMissing++;
          if (!hasValidValue(orderData.techName)) expectedMissing++;
          if (!hasValidValue(orderData.date)) expectedMissing++;
          if (!hasValidValue(orderData.time)) expectedMissing++;
          if (!hasValidValue(orderData.address)) expectedMissing++;
          
          // If any field is missing, summary should be incomplete
          if (expectedMissing > 0) {
            expect(validation.complete).toBe(false);
          }
        }
      ),
      { numRuns: 100 }
    );
  });

  /**
   * Property: Format price produces valid string
   * For any price, formatPrice should return a string starting with ¥.
   */
  it('formatPrice should produce valid price string', () => {
    fc.assert(
      fc.property(
        fc.integer({ min: 0, max: 10000 }),
        (price) => {
          const formatted = formatPrice(price);
          
          expect(formatted).toMatch(/^¥\d+\.\d{2}$/);
          expect(formatted.startsWith('¥')).toBe(true);
        }
      ),
      { numRuns: 100 }
    );
  });

  /**
   * Property: Format date time produces valid string
   * For any valid date and time, formatDateTime should produce a non-empty string.
   */
  it('formatDateTime should produce valid datetime string', () => {
    fc.assert(
      fc.property(
        dateArbitrary,
        timeArbitrary,
        (date, time) => {
          const formatted = formatDateTime(date, time);
          
          expect(formatted).not.toBe('');
          expect(formatted).toContain('月');
          expect(formatted).toContain('日');
          expect(formatted).toContain(':');
        }
      ),
      { numRuns: 100 }
    );
  });

  /**
   * Property: Null/undefined order data returns null summary
   * generateOrderSummary should handle null/undefined gracefully.
   */
  it('null order data should return null summary', () => {
    expect(generateOrderSummary(null)).toBeNull();
    expect(generateOrderSummary(undefined)).toBeNull();
  });

  /**
   * Property: hasValidValue correctly identifies invalid values
   */
  it('hasValidValue should correctly identify invalid values', () => {
    expect(hasValidValue(null)).toBe(false);
    expect(hasValidValue(undefined)).toBe(false);
    expect(hasValidValue('')).toBe(false);
    expect(hasValidValue('   ')).toBe(false);
    expect(hasValidValue(-1)).toBe(false);
    expect(hasValidValue(NaN)).toBe(false);
    
    expect(hasValidValue('test')).toBe(true);
    expect(hasValidValue(0)).toBe(true);
    expect(hasValidValue(100)).toBe(true);
  });
});
