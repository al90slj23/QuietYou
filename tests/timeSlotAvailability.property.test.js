/**
 * Property 9: Time Slot Availability
 * Feature: qingyang-platform, Property 9: Time Slot Availability
 * Validates: Requirements 4.2
 * 
 * For any technician's available time slots displayed to user, each slot SHALL:
 * 1. Fall within the technician's scheduled availability
 * 2. Not conflict with any existing accepted orders
 * 3. Be in the future (not in the past)
 */

import { describe, it, expect } from 'vitest';
import fc from 'fast-check';
import {
  isWithinSchedule,
  hasOrderConflict,
  isInFuture,
  validateTimeSlot,
  getAvailableSlots,
  timeToMinutes,
  minutesToTime
} from './lib/timeSlotAvailability.js';

// 时间字符串生成器 (HH:MM 格式)
const timeArbitrary = fc.integer({ min: 0, max: 23 }).chain(hour =>
  fc.integer({ min: 0, max: 1 }).map(halfHour => 
    `${String(hour).padStart(2, '0')}:${halfHour === 0 ? '00' : '30'}`
  )
);

// 日期字符串生成器 (YYYY-MM-DD 格式)
const dateArbitrary = fc.integer({ min: 0, max: 6 }).map(daysFromNow => {
  const date = new Date();
  date.setDate(date.getDate() + daysFromNow);
  return `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(date.getDate()).padStart(2, '0')}`;
});

// 时段对象生成器
const slotArbitrary = fc.record({
  date: dateArbitrary,
  time: timeArbitrary,
  duration: fc.constantFrom(30, 45, 60, 90, 120)
});

// 排班对象生成器
const scheduleEntryArbitrary = fc.record({
  date: dateArbitrary,
  startTime: fc.constantFrom('09:00', '10:00', '11:00'),
  endTime: fc.constantFrom('18:00', '19:00', '20:00', '21:00')
});

// 订单对象生成器
const orderArbitrary = fc.record({
  date: dateArbitrary,
  time: timeArbitrary,
  duration: fc.constantFrom(30, 45, 60, 90)
});

describe('Property 9: Time Slot Availability', () => {
  /**
   * Property: Available slots must be within schedule
   * For any slot marked as available, it must fall within the technician's scheduled availability.
   */
  it('available slots should fall within technician schedule', () => {
    fc.assert(
      fc.property(
        dateArbitrary,
        fc.array(scheduleEntryArbitrary, { minLength: 1, maxLength: 5 }),
        fc.array(orderArbitrary, { minLength: 0, maxLength: 10 }),
        (date, schedule, orders) => {
          const currentTime = new Date();
          currentTime.setHours(0, 0, 0, 0); // Start of day for testing
          
          const availableSlots = getAvailableSlots(date, {
            schedule,
            orders,
            currentTime,
            minAdvanceMinutes: 0, // Disable advance time check for this test
            slotInterval: 30,
            serviceDuration: 60
          });

          // Every available slot must be within schedule
          for (const slot of availableSlots) {
            if (slot.available) {
              expect(isWithinSchedule(slot, schedule)).toBe(true);
            }
          }
        }
      ),
      { numRuns: 100 }
    );
  });

  /**
   * Property: Available slots must not conflict with existing orders
   * For any slot marked as available, it must not overlap with any accepted order.
   */
  it('available slots should not conflict with existing orders', () => {
    fc.assert(
      fc.property(
        dateArbitrary,
        fc.array(scheduleEntryArbitrary, { minLength: 1, maxLength: 3 }),
        fc.array(orderArbitrary, { minLength: 1, maxLength: 5 }),
        (date, schedule, orders) => {
          const currentTime = new Date();
          currentTime.setHours(0, 0, 0, 0);
          
          const availableSlots = getAvailableSlots(date, {
            schedule,
            orders,
            currentTime,
            minAdvanceMinutes: 0,
            slotInterval: 30,
            serviceDuration: 60
          });

          // Every available slot must not conflict with orders
          for (const slot of availableSlots) {
            if (slot.available) {
              expect(hasOrderConflict(slot, orders)).toBe(false);
            }
          }
        }
      ),
      { numRuns: 100 }
    );
  });

  /**
   * Property: Available slots must be in the future
   * For any slot marked as available, it must be at least minAdvanceMinutes in the future.
   */
  it('available slots should be in the future', () => {
    fc.assert(
      fc.property(
        fc.integer({ min: 0, max: 6 }),
        fc.array(scheduleEntryArbitrary, { minLength: 1, maxLength: 3 }),
        fc.integer({ min: 30, max: 180 }),
        (daysFromNow, schedule, minAdvanceMinutes) => {
          const currentTime = new Date();
          const date = new Date(currentTime);
          date.setDate(date.getDate() + daysFromNow);
          const dateStr = `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(date.getDate()).padStart(2, '0')}`;
          
          const availableSlots = getAvailableSlots(dateStr, {
            schedule,
            orders: [],
            currentTime,
            minAdvanceMinutes,
            slotInterval: 30,
            serviceDuration: 60
          });

          // Every available slot must be in the future
          for (const slot of availableSlots) {
            if (slot.available) {
              expect(isInFuture(slot, currentTime, minAdvanceMinutes)).toBe(true);
            }
          }
        }
      ),
      { numRuns: 100 }
    );
  });

  /**
   * Property: Unavailable slots have valid reasons
   * For any slot marked as unavailable, it must have a valid reason.
   */
  it('unavailable slots should have valid reasons', () => {
    fc.assert(
      fc.property(
        dateArbitrary,
        fc.array(scheduleEntryArbitrary, { minLength: 1, maxLength: 3 }),
        fc.array(orderArbitrary, { minLength: 0, maxLength: 5 }),
        (date, schedule, orders) => {
          const currentTime = new Date();
          
          const availableSlots = getAvailableSlots(date, {
            schedule,
            orders,
            currentTime,
            minAdvanceMinutes: 120,
            slotInterval: 30,
            serviceDuration: 60
          });

          const validReasons = ['not_in_schedule', 'order_conflict', 'in_past', null];

          for (const slot of availableSlots) {
            if (!slot.available) {
              expect(validReasons).toContain(slot.reason);
              expect(slot.reason).not.toBeNull();
            }
          }
        }
      ),
      { numRuns: 100 }
    );
  });

  /**
   * Property: Time conversion round-trip
   * Converting time to minutes and back should preserve the original value.
   */
  it('time conversion should be reversible', () => {
    fc.assert(
      fc.property(
        timeArbitrary,
        (time) => {
          const minutes = timeToMinutes(time);
          const converted = minutesToTime(minutes);
          expect(converted).toBe(time);
        }
      ),
      { numRuns: 100 }
    );
  });

  /**
   * Property: Order conflict detection is symmetric
   * If slot A conflicts with order B, then a slot at order B's time conflicts with slot A.
   */
  it('order conflict detection should be consistent', () => {
    fc.assert(
      fc.property(
        slotArbitrary,
        orderArbitrary,
        (slot, order) => {
          // Make them on the same date for meaningful test
          const testSlot = { ...slot, date: order.date };
          const testOrder = { ...order };
          
          const slotConflictsWithOrder = hasOrderConflict(testSlot, [testOrder]);
          
          // Create a slot at the order's position
          const reverseSlot = { 
            date: testOrder.date, 
            time: testOrder.time, 
            duration: testOrder.duration 
          };
          const reverseOrder = { 
            date: testSlot.date, 
            time: testSlot.time, 
            duration: testSlot.duration 
          };
          
          const orderConflictsWithSlot = hasOrderConflict(reverseSlot, [reverseOrder]);
          
          // Conflict detection should be symmetric
          expect(slotConflictsWithOrder).toBe(orderConflictsWithSlot);
        }
      ),
      { numRuns: 100 }
    );
  });

  /**
   * Property: Empty schedule means no available slots
   * If there's no schedule for a date, no slots should be available.
   */
  it('empty schedule should result in no available slots', () => {
    fc.assert(
      fc.property(
        dateArbitrary,
        (date) => {
          const currentTime = new Date();
          
          const availableSlots = getAvailableSlots(date, {
            schedule: [], // Empty schedule
            orders: [],
            currentTime,
            minAdvanceMinutes: 0,
            slotInterval: 30,
            serviceDuration: 60
          });

          expect(availableSlots.length).toBe(0);
        }
      ),
      { numRuns: 100 }
    );
  });
});
