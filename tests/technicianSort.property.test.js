/**
 * Property 5: Technician Distance Sorting
 * Feature: qingyang-platform, Property 5: Technician Distance Sorting
 * Validates: Requirements 3.1
 * 
 * For any technician list sorted by distance, the list SHALL be in ascending
 * order of distance from user's location, where distance[i] <= distance[i+1]
 * for all consecutive pairs.
 */

import { describe, it, expect } from 'vitest';
import fc from 'fast-check';
import { 
  sortByDistance, 
  sortByRating, 
  sortByPrice,
  sortTechnicians,
  isDistanceSorted,
  isRatingSorted,
  filterTechnicians
} from './lib/technicianFilter.js';

// 技师对象生成器
const technicianArbitrary = fc.record({
  id: fc.integer({ min: 1, max: 10000 }),
  name: fc.string({ minLength: 1, maxLength: 20 }),
  gender: fc.constantFrom(1, 2),
  avatar: fc.constantFrom('👨', '👩'),
  shopName: fc.string({ minLength: 1, maxLength: 50 }),
  rating: fc.float({ min: Math.fround(1), max: Math.fround(5), noNaN: true }),
  ratingCount: fc.integer({ min: 0, max: 1000 }),
  orderCount: fc.integer({ min: 0, max: 5000 }),
  distance: fc.float({ min: Math.fround(0.1), max: Math.fround(50), noNaN: true }),
  price: fc.integer({ min: 100, max: 1000 }),
  status: fc.constantFrom(0, 1, 2)
});

// 技师列表生成器
const techniciansArbitrary = fc.array(technicianArbitrary, { minLength: 0, maxLength: 50 });

describe('Property 5: Technician Distance Sorting', () => {
  /**
   * Property: Distance sorting produces ascending order
   * For any technician list, sorting by distance should produce a list
   * where each technician's distance is <= the next technician's distance.
   */
  it('should sort technicians by distance in ascending order', () => {
    fc.assert(
      fc.property(
        techniciansArbitrary,
        (technicians) => {
          const sorted = sortByDistance(technicians);
          
          // Verify ascending order
          for (let i = 0; i < sorted.length - 1; i++) {
            expect(sorted[i].distance).toBeLessThanOrEqual(sorted[i + 1].distance);
          }
        }
      ),
      { numRuns: 100 }
    );
  });

  /**
   * Property: Distance sorting preserves all elements
   * Sorting should not add or remove any technicians.
   */
  it('should preserve all technicians after distance sorting', () => {
    fc.assert(
      fc.property(
        techniciansArbitrary,
        (technicians) => {
          const sorted = sortByDistance(technicians);
          
          // Same length
          expect(sorted.length).toBe(technicians.length);
          
          // Same elements (by id)
          const originalIds = new Set(technicians.map(t => t.id));
          const sortedIds = new Set(sorted.map(t => t.id));
          expect(sortedIds).toEqual(originalIds);
        }
      ),
      { numRuns: 100 }
    );
  });

  /**
   * Property: isDistanceSorted correctly validates sorted lists
   * After sorting by distance, isDistanceSorted should return true.
   */
  it('isDistanceSorted should return true for distance-sorted lists', () => {
    fc.assert(
      fc.property(
        techniciansArbitrary,
        (technicians) => {
          const sorted = sortByDistance(technicians);
          expect(isDistanceSorted(sorted)).toBe(true);
        }
      ),
      { numRuns: 100 }
    );
  });

  /**
   * Property: Rating sorting produces descending order
   * For any technician list, sorting by rating should produce a list
   * where each technician's rating is >= the next technician's rating.
   */
  it('should sort technicians by rating in descending order', () => {
    fc.assert(
      fc.property(
        techniciansArbitrary,
        (technicians) => {
          const sorted = sortByRating(technicians);
          
          // Verify descending order
          for (let i = 0; i < sorted.length - 1; i++) {
            expect(sorted[i].rating).toBeGreaterThanOrEqual(sorted[i + 1].rating);
          }
        }
      ),
      { numRuns: 100 }
    );
  });

  /**
   * Property: Price sorting (ascending) produces correct order
   */
  it('should sort technicians by price in ascending order', () => {
    fc.assert(
      fc.property(
        techniciansArbitrary,
        (technicians) => {
          const sorted = sortByPrice(technicians, 'asc');
          
          for (let i = 0; i < sorted.length - 1; i++) {
            expect(sorted[i].price).toBeLessThanOrEqual(sorted[i + 1].price);
          }
        }
      ),
      { numRuns: 100 }
    );
  });

  /**
   * Property: Price sorting (descending) produces correct order
   */
  it('should sort technicians by price in descending order', () => {
    fc.assert(
      fc.property(
        techniciansArbitrary,
        (technicians) => {
          const sorted = sortByPrice(technicians, 'desc');
          
          for (let i = 0; i < sorted.length - 1; i++) {
            expect(sorted[i].price).toBeGreaterThanOrEqual(sorted[i + 1].price);
          }
        }
      ),
      { numRuns: 100 }
    );
  });

  /**
   * Property: Default sort (综合排序) uses distance
   * When no sort type is specified, should default to distance sorting.
   */
  it('default sort should use distance sorting', () => {
    fc.assert(
      fc.property(
        techniciansArbitrary,
        (technicians) => {
          const defaultSorted = sortTechnicians(technicians, '');
          const distanceSorted = sortByDistance(technicians);
          
          // Should produce same order
          expect(defaultSorted.map(t => t.id)).toEqual(distanceSorted.map(t => t.id));
        }
      ),
      { numRuns: 100 }
    );
  });

  /**
   * Property: Sorting is idempotent
   * Sorting an already sorted list should produce the same result.
   */
  it('sorting should be idempotent', () => {
    fc.assert(
      fc.property(
        techniciansArbitrary,
        (technicians) => {
          const sorted1 = sortByDistance(technicians);
          const sorted2 = sortByDistance(sorted1);
          
          expect(sorted1.map(t => t.id)).toEqual(sorted2.map(t => t.id));
        }
      ),
      { numRuns: 100 }
    );
  });
});
