/**
 * Property 4: Service Filter Consistency
 * Feature: qingyang-platform, Property 4: Service Filter Consistency
 * Validates: Requirements 2.4
 * 
 * For any combination of filters (price range, duration, service type),
 * all returned service items SHALL match ALL specified filter criteria.
 */

import { describe, it, expect } from 'vitest';
import fc from 'fast-check';
import { filterServices, matchesFilters } from './lib/serviceFilter.js';

// 服务对象生成器
const serviceArbitrary = fc.record({
  id: fc.integer({ min: 1, max: 10000 }),
  categoryId: fc.integer({ min: 1, max: 10 }),
  name: fc.string({ minLength: 1, maxLength: 50 }),
  desc: fc.string({ maxLength: 200 }),
  duration: fc.constantFrom(30, 45, 60, 90, 120),
  price: fc.integer({ min: 50, max: 1000 }),
  maxPrice: fc.integer({ min: 100, max: 1500 }),
  icon: fc.constantFrom('💆', '🧘', '🦶', '🌸', '💪', '🔥', '🏺'),
  tags: fc.array(fc.constantFrom('热门', '推荐', '新品'), { maxLength: 3 })
});

// 服务列表生成器
const servicesArbitrary = fc.array(serviceArbitrary, { minLength: 0, maxLength: 50 });

// 筛选条件生成器
const filtersArbitrary = fc.record({
  categoryId: fc.oneof(fc.constant(0), fc.integer({ min: 1, max: 10 })),
  price: fc.oneof(
    fc.constant(''),
    fc.constant('0-200'),
    fc.constant('200-300'),
    fc.constant('300-400'),
    fc.constant('400-9999')
  ),
  duration: fc.oneof(
    fc.constant(''),
    fc.constant('30'),
    fc.constant('45'),
    fc.constant('60'),
    fc.constant('90')
  ),
  sort: fc.oneof(
    fc.constant(''),
    fc.constant('price_asc'),
    fc.constant('price_desc'),
    fc.constant('duration_asc'),
    fc.constant('duration_desc')
  )
});

describe('Property 4: Service Filter Consistency', () => {
  /**
   * Property: All returned services match ALL filter criteria
   * For any combination of filters, every service in the result
   * must satisfy all specified filter conditions.
   */
  it('all returned services should match ALL specified filter criteria', () => {
    fc.assert(
      fc.property(
        servicesArbitrary,
        filtersArbitrary,
        (services, filters) => {
          const filtered = filterServices(services, filters);
          
          // Every service in the result must match all filters
          for (const service of filtered) {
            expect(matchesFilters(service, filters)).toBe(true);
          }
        }
      ),
      { numRuns: 100 }
    );
  });

  /**
   * Property: No matching services are excluded
   * For any service that matches all filters, it should be in the result.
   */
  it('no matching services should be excluded from results', () => {
    fc.assert(
      fc.property(
        servicesArbitrary,
        filtersArbitrary,
        (services, filters) => {
          const filtered = filterServices(services, filters);
          const filteredIds = new Set(filtered.map(s => s.id));
          
          // Every service that matches filters should be in the result
          for (const service of services) {
            if (matchesFilters(service, filters)) {
              expect(filteredIds.has(service.id)).toBe(true);
            }
          }
        }
      ),
      { numRuns: 100 }
    );
  });

  /**
   * Property: Category filter correctness
   * When categoryId > 0, all results must have that categoryId.
   */
  it('category filter should only return services from specified category', () => {
    fc.assert(
      fc.property(
        servicesArbitrary,
        fc.integer({ min: 1, max: 10 }),
        (services, categoryId) => {
          const filtered = filterServices(services, { categoryId });
          
          for (const service of filtered) {
            expect(service.categoryId).toBe(categoryId);
          }
        }
      ),
      { numRuns: 100 }
    );
  });

  /**
   * Property: Price filter correctness
   * All results must have price within the specified range.
   */
  it('price filter should only return services within price range', () => {
    fc.assert(
      fc.property(
        servicesArbitrary,
        fc.constantFrom('0-200', '200-300', '300-400', '400-9999'),
        (services, priceRange) => {
          const [min, max] = priceRange.split('-').map(Number);
          const filtered = filterServices(services, { price: priceRange });
          
          for (const service of filtered) {
            expect(service.price).toBeGreaterThanOrEqual(min);
            expect(service.price).toBeLessThanOrEqual(max);
          }
        }
      ),
      { numRuns: 100 }
    );
  });

  /**
   * Property: Duration filter correctness
   * All results must have the exact specified duration.
   */
  it('duration filter should only return services with exact duration', () => {
    fc.assert(
      fc.property(
        servicesArbitrary,
        fc.constantFrom('30', '45', '60', '90'),
        (services, durationStr) => {
          const duration = parseInt(durationStr);
          const filtered = filterServices(services, { duration: durationStr });
          
          for (const service of filtered) {
            expect(service.duration).toBe(duration);
          }
        }
      ),
      { numRuns: 100 }
    );
  });

  /**
   * Property: Combined filters are conjunctive (AND)
   * When multiple filters are applied, results must match ALL of them.
   */
  it('combined filters should be applied conjunctively (AND)', () => {
    fc.assert(
      fc.property(
        servicesArbitrary,
        fc.integer({ min: 1, max: 10 }),
        fc.constantFrom('0-200', '200-300', '300-400'),
        fc.constantFrom('30', '45', '60', '90'),
        (services, categoryId, priceRange, durationStr) => {
          const [minPrice, maxPrice] = priceRange.split('-').map(Number);
          const duration = parseInt(durationStr);
          
          const filtered = filterServices(services, {
            categoryId,
            price: priceRange,
            duration: durationStr
          });
          
          for (const service of filtered) {
            // Must match category
            expect(service.categoryId).toBe(categoryId);
            // Must match price range
            expect(service.price).toBeGreaterThanOrEqual(minPrice);
            expect(service.price).toBeLessThanOrEqual(maxPrice);
            // Must match duration
            expect(service.duration).toBe(duration);
          }
        }
      ),
      { numRuns: 100 }
    );
  });
});
