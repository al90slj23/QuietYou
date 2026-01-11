/**
 * Property 16: Average Rating Calculation
 * Feature: qingyang-platform, Property 16: Average Rating Calculation
 * Validates: Requirements 6.4
 * 
 * For any technician, after a new review is submitted, the average rating
 * SHALL equal: sum(all_ratings) / count(all_ratings), rounded to 2 decimal places.
 */

import { describe, it, expect } from 'vitest';
import fc from 'fast-check';
import {
  calculateAverageRating,
  addRatingAndCalculate,
  isValidRating,
  calculateRatingDistribution,
  calculateGoodRatePercentage,
  extractRatingsFromReviews,
  validateAverageCalculation,
  validateIncrementalCalculation
} from './lib/ratingCalculation.js';

// 有效评分生成器 (1-5)
const validRatingArbitrary = fc.integer({ min: 1, max: 5 });

// 评分列表生成器
const ratingsArbitrary = fc.array(validRatingArbitrary, { minLength: 0, maxLength: 100 });

// 非空评分列表生成器
const nonEmptyRatingsArbitrary = fc.array(validRatingArbitrary, { minLength: 1, maxLength: 100 });

// 评价对象生成器
const reviewArbitrary = fc.record({
  id: fc.integer({ min: 1, max: 10000 }),
  rating: validRatingArbitrary,
  content: fc.string({ maxLength: 500 }),
  createdAt: fc.date()
});

// 评价列表生成器
const reviewsArbitrary = fc.array(reviewArbitrary, { minLength: 0, maxLength: 50 });

describe('Property 16: Average Rating Calculation', () => {
  /**
   * Property: Average equals sum/count
   * For any list of ratings, average = sum(ratings) / count(ratings)
   */
  it('average should equal sum divided by count', () => {
    fc.assert(
      fc.property(
        nonEmptyRatingsArbitrary,
        (ratings) => {
          const average = calculateAverageRating(ratings);
          const sum = ratings.reduce((acc, r) => acc + r, 0);
          const expectedAverage = Math.round((sum / ratings.length) * 100) / 100;
          
          expect(average).toBe(expectedAverage);
        }
      ),
      { numRuns: 100 }
    );
  });

  /**
   * Property: Average is within valid range
   * For any non-empty list of valid ratings, average should be between 1 and 5.
   */
  it('average should be between 1 and 5 for valid ratings', () => {
    fc.assert(
      fc.property(
        nonEmptyRatingsArbitrary,
        (ratings) => {
          const average = calculateAverageRating(ratings);
          
          expect(average).toBeGreaterThanOrEqual(1);
          expect(average).toBeLessThanOrEqual(5);
        }
      ),
      { numRuns: 100 }
    );
  });

  /**
   * Property: Empty ratings return 0
   * For an empty list of ratings, average should be 0.
   */
  it('empty ratings should return 0', () => {
    expect(calculateAverageRating([])).toBe(0);
    expect(calculateAverageRating(null)).toBe(0);
    expect(calculateAverageRating(undefined)).toBe(0);
  });

  /**
   * Property: Incremental calculation matches full calculation
   * Adding a new rating incrementally should produce the same result as
   * calculating from scratch with all ratings.
   */
  it('incremental calculation should match full calculation', () => {
    fc.assert(
      fc.property(
        nonEmptyRatingsArbitrary,
        validRatingArbitrary,
        (ratings, newRating) => {
          const currentAverage = calculateAverageRating(ratings);
          const currentCount = ratings.length;
          
          const { average: incrementalAverage } = addRatingAndCalculate(
            currentAverage, 
            currentCount, 
            newRating
          );
          
          const allRatings = [...ratings, newRating];
          const fullAverage = calculateAverageRating(allRatings);
          
          // Allow small floating point differences (0.02 to account for rounding)
          expect(Math.abs(incrementalAverage - fullAverage)).toBeLessThan(0.02);
        }
      ),
      { numRuns: 100 }
    );
  });

  /**
   * Property: Rating distribution sums to total count
   * The sum of all distribution counts should equal the total number of valid ratings.
   */
  it('rating distribution should sum to total count', () => {
    fc.assert(
      fc.property(
        ratingsArbitrary,
        (ratings) => {
          const distribution = calculateRatingDistribution(ratings);
          const distributionSum = Object.values(distribution).reduce((a, b) => a + b, 0);
          const validCount = ratings.filter(r => isValidRating(r)).length;
          
          expect(distributionSum).toBe(validCount);
        }
      ),
      { numRuns: 100 }
    );
  });

  /**
   * Property: Good rate is between 0 and 100
   * For any list of ratings, good rate percentage should be between 0 and 100.
   */
  it('good rate should be between 0 and 100', () => {
    fc.assert(
      fc.property(
        ratingsArbitrary,
        (ratings) => {
          const goodRate = calculateGoodRatePercentage(ratings);
          
          expect(goodRate).toBeGreaterThanOrEqual(0);
          expect(goodRate).toBeLessThanOrEqual(100);
        }
      ),
      { numRuns: 100 }
    );
  });

  /**
   * Property: All 5-star ratings = 100% good rate
   * If all ratings are 4 or 5, good rate should be 100%.
   */
  it('all high ratings should result in 100% good rate', () => {
    fc.assert(
      fc.property(
        fc.array(fc.integer({ min: 4, max: 5 }), { minLength: 1, maxLength: 50 }),
        (ratings) => {
          const goodRate = calculateGoodRatePercentage(ratings);
          
          expect(goodRate).toBe(100);
        }
      ),
      { numRuns: 100 }
    );
  });

  /**
   * Property: All low ratings = 0% good rate
   * If all ratings are 1, 2, or 3, good rate should be 0%.
   */
  it('all low ratings should result in 0% good rate', () => {
    fc.assert(
      fc.property(
        fc.array(fc.integer({ min: 1, max: 3 }), { minLength: 1, maxLength: 50 }),
        (ratings) => {
          const goodRate = calculateGoodRatePercentage(ratings);
          
          expect(goodRate).toBe(0);
        }
      ),
      { numRuns: 100 }
    );
  });

  /**
   * Property: Extract ratings from reviews preserves count
   * Extracting ratings from reviews should preserve the count of valid reviews.
   */
  it('extracting ratings should preserve count', () => {
    fc.assert(
      fc.property(
        reviewsArbitrary,
        (reviews) => {
          const ratings = extractRatingsFromReviews(reviews);
          
          expect(ratings.length).toBe(reviews.length);
        }
      ),
      { numRuns: 100 }
    );
  });

  /**
   * Property: isValidRating correctly validates ratings
   */
  it('isValidRating should correctly validate ratings', () => {
    // Valid ratings
    for (let i = 1; i <= 5; i++) {
      expect(isValidRating(i)).toBe(true);
    }
    
    // Invalid ratings
    expect(isValidRating(0)).toBe(false);
    expect(isValidRating(6)).toBe(false);
    expect(isValidRating(-1)).toBe(false);
    expect(isValidRating(3.5)).toBe(false);
    expect(isValidRating(null)).toBe(false);
    expect(isValidRating(undefined)).toBe(false);
    expect(isValidRating('5')).toBe(false);
  });

  /**
   * Property: Validation functions work correctly
   */
  it('validation functions should work correctly', () => {
    fc.assert(
      fc.property(
        nonEmptyRatingsArbitrary,
        (ratings) => {
          const average = calculateAverageRating(ratings);
          
          expect(validateAverageCalculation(ratings, average)).toBe(true);
        }
      ),
      { numRuns: 100 }
    );
  });

  /**
   * Property: Single rating equals itself
   * For a single rating, the average should equal that rating.
   */
  it('single rating average should equal the rating itself', () => {
    fc.assert(
      fc.property(
        validRatingArbitrary,
        (rating) => {
          const average = calculateAverageRating([rating]);
          
          expect(average).toBe(rating);
        }
      ),
      { numRuns: 100 }
    );
  });
});
