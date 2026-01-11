/**
 * Property 17: Review Sorting
 * Feature: qingyang-platform, Property 17: Review Sorting
 * Validates: Requirements 6.5
 * 
 * For any technician's review list, reviews SHALL be sorted by creation time
 * in descending order (newest first).
 */

import { describe, it, expect } from 'vitest';
import fc from 'fast-check';
import { 
  sortReviewsByTime, 
  isTimeSortedDescending,
  filterReviewsByTechnician,
  getTechnicianReviews
} from './lib/reviewSort.js';

// 生成有效的日期时间字符串
const dateTimeArbitrary = fc.date({
  min: new Date('2024-01-01'),
  max: new Date('2026-12-31')
}).map(d => d.toISOString().slice(0, 16).replace('T', ' '));

// 评价对象生成器
const reviewArbitrary = fc.record({
  id: fc.integer({ min: 1, max: 100000 }),
  techId: fc.integer({ min: 1, max: 100 }),
  userId: fc.integer({ min: 1, max: 10000 }),
  userName: fc.string({ minLength: 1, maxLength: 20 }),
  avatar: fc.constantFrom('😊', '😄', '🙂', '😀', '🤗'),
  rating: fc.integer({ min: 1, max: 5 }),
  content: fc.string({ minLength: 1, maxLength: 500 }),
  photos: fc.array(fc.constant('📷'), { maxLength: 3 }),
  serviceName: fc.string({ minLength: 1, maxLength: 50 }),
  createdAt: dateTimeArbitrary
});

// 评价列表生成器
const reviewsArbitrary = fc.array(reviewArbitrary, { minLength: 0, maxLength: 50 });

describe('Property 17: Review Sorting', () => {
  /**
   * Property: Reviews are sorted by time in descending order (newest first)
   * For any review list, after sorting, each review's createdAt should be
   * >= the next review's createdAt.
   */
  it('should sort reviews by time in descending order (newest first)', () => {
    fc.assert(
      fc.property(
        reviewsArbitrary,
        (reviews) => {
          const sorted = sortReviewsByTime(reviews);
          
          // Verify descending order (newest first)
          for (let i = 0; i < sorted.length - 1; i++) {
            const timeA = new Date(sorted[i].createdAt).getTime();
            const timeB = new Date(sorted[i + 1].createdAt).getTime();
            expect(timeA).toBeGreaterThanOrEqual(timeB);
          }
        }
      ),
      { numRuns: 100 }
    );
  });

  /**
   * Property: Sorting preserves all reviews
   * Sorting should not add or remove any reviews.
   */
  it('should preserve all reviews after sorting', () => {
    fc.assert(
      fc.property(
        reviewsArbitrary,
        (reviews) => {
          const sorted = sortReviewsByTime(reviews);
          
          // Same length
          expect(sorted.length).toBe(reviews.length);
          
          // Same elements (by id)
          const originalIds = new Set(reviews.map(r => r.id));
          const sortedIds = new Set(sorted.map(r => r.id));
          expect(sortedIds).toEqual(originalIds);
        }
      ),
      { numRuns: 100 }
    );
  });

  /**
   * Property: isTimeSortedDescending correctly validates sorted lists
   * After sorting by time, isTimeSortedDescending should return true.
   */
  it('isTimeSortedDescending should return true for time-sorted lists', () => {
    fc.assert(
      fc.property(
        reviewsArbitrary,
        (reviews) => {
          const sorted = sortReviewsByTime(reviews);
          expect(isTimeSortedDescending(sorted)).toBe(true);
        }
      ),
      { numRuns: 100 }
    );
  });

  /**
   * Property: Filtering by technician only returns that technician's reviews
   * All reviews in the filtered list should have the specified techId.
   */
  it('should only return reviews for the specified technician', () => {
    fc.assert(
      fc.property(
        reviewsArbitrary,
        fc.integer({ min: 1, max: 100 }),
        (reviews, techId) => {
          const filtered = filterReviewsByTechnician(reviews, techId);
          
          for (const review of filtered) {
            expect(review.techId).toBe(techId);
          }
        }
      ),
      { numRuns: 100 }
    );
  });

  /**
   * Property: No matching reviews are excluded when filtering
   * All reviews with the specified techId should be in the filtered result.
   */
  it('should not exclude any matching reviews when filtering', () => {
    fc.assert(
      fc.property(
        reviewsArbitrary,
        fc.integer({ min: 1, max: 100 }),
        (reviews, techId) => {
          const filtered = filterReviewsByTechnician(reviews, techId);
          const filteredIds = new Set(filtered.map(r => r.id));
          
          for (const review of reviews) {
            if (review.techId === techId) {
              expect(filteredIds.has(review.id)).toBe(true);
            }
          }
        }
      ),
      { numRuns: 100 }
    );
  });

  /**
   * Property: getTechnicianReviews returns filtered and sorted reviews
   * The result should only contain reviews for the specified technician,
   * sorted by time in descending order.
   */
  it('getTechnicianReviews should return filtered and sorted reviews', () => {
    fc.assert(
      fc.property(
        reviewsArbitrary,
        fc.integer({ min: 1, max: 100 }),
        (reviews, techId) => {
          const result = getTechnicianReviews(reviews, techId);
          
          // All reviews should be for the specified technician
          for (const review of result) {
            expect(review.techId).toBe(techId);
          }
          
          // Reviews should be sorted by time (descending)
          expect(isTimeSortedDescending(result)).toBe(true);
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
        reviewsArbitrary,
        (reviews) => {
          const sorted1 = sortReviewsByTime(reviews);
          const sorted2 = sortReviewsByTime(sorted1);
          
          expect(sorted1.map(r => r.id)).toEqual(sorted2.map(r => r.id));
        }
      ),
      { numRuns: 100 }
    );
  });

  /**
   * Property: Empty list remains empty after sorting
   */
  it('empty list should remain empty after sorting', () => {
    const sorted = sortReviewsByTime([]);
    expect(sorted).toEqual([]);
    expect(isTimeSortedDescending(sorted)).toBe(true);
  });

  /**
   * Property: Single element list is always sorted
   */
  it('single element list should always be sorted', () => {
    fc.assert(
      fc.property(
        reviewArbitrary,
        (review) => {
          const sorted = sortReviewsByTime([review]);
          expect(sorted.length).toBe(1);
          expect(sorted[0].id).toBe(review.id);
          expect(isTimeSortedDescending(sorted)).toBe(true);
        }
      ),
      { numRuns: 100 }
    );
  });
});
