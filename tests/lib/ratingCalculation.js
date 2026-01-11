/**
 * 评分计算逻辑模块
 * 用于测试 Property 16: Average Rating Calculation
 * Validates: Requirements 6.4
 */

/**
 * 计算平均评分
 * @param {Array} ratings - 评分列表 (1-5)
 * @returns {number} 平均评分，保留2位小数
 */
export function calculateAverageRating(ratings) {
  if (!ratings || !Array.isArray(ratings) || ratings.length === 0) {
    return 0;
  }

  // 过滤有效评分 (1-5)
  const validRatings = ratings.filter(r => 
    typeof r === 'number' && r >= 1 && r <= 5
  );

  if (validRatings.length === 0) {
    return 0;
  }

  const sum = validRatings.reduce((acc, r) => acc + r, 0);
  const average = sum / validRatings.length;
  
  // 保留2位小数
  return Math.round(average * 100) / 100;
}

/**
 * 添加新评分后计算新的平均评分
 * @param {number} currentAverage - 当前平均评分
 * @param {number} currentCount - 当前评价数量
 * @param {number} newRating - 新评分
 * @returns {Object} { average, count }
 */
export function addRatingAndCalculate(currentAverage, currentCount, newRating) {
  if (typeof newRating !== 'number' || newRating < 1 || newRating > 5) {
    return { average: currentAverage, count: currentCount };
  }

  const currentSum = currentAverage * currentCount;
  const newSum = currentSum + newRating;
  const newCount = currentCount + 1;
  const newAverage = Math.round((newSum / newCount) * 100) / 100;

  return { average: newAverage, count: newCount };
}

/**
 * 验证评分是否有效
 * @param {number} rating - 评分
 * @returns {boolean} 是否有效
 */
export function isValidRating(rating) {
  return typeof rating === 'number' && 
         Number.isInteger(rating) && 
         rating >= 1 && 
         rating <= 5;
}

/**
 * 计算评分分布
 * @param {Array} ratings - 评分列表
 * @returns {Object} 评分分布 { 1: count, 2: count, ... }
 */
export function calculateRatingDistribution(ratings) {
  const distribution = { 1: 0, 2: 0, 3: 0, 4: 0, 5: 0 };

  if (!ratings || !Array.isArray(ratings)) {
    return distribution;
  }

  for (const rating of ratings) {
    if (isValidRating(rating)) {
      distribution[rating]++;
    }
  }

  return distribution;
}

/**
 * 计算好评率 (4星及以上)
 * @param {Array} ratings - 评分列表
 * @returns {number} 好评率 (0-100)
 */
export function calculateGoodRatePercentage(ratings) {
  if (!ratings || !Array.isArray(ratings) || ratings.length === 0) {
    return 100; // 无评价时默认100%
  }

  const validRatings = ratings.filter(r => isValidRating(r));
  if (validRatings.length === 0) {
    return 100;
  }

  const goodRatings = validRatings.filter(r => r >= 4);
  return Math.round((goodRatings.length / validRatings.length) * 100);
}

/**
 * 从评价列表中提取评分
 * @param {Array} reviews - 评价列表
 * @returns {Array} 评分列表
 */
export function extractRatingsFromReviews(reviews) {
  if (!reviews || !Array.isArray(reviews)) {
    return [];
  }

  return reviews
    .filter(r => r && typeof r.rating === 'number')
    .map(r => r.rating);
}

/**
 * 验证平均评分计算的正确性
 * @param {Array} ratings - 评分列表
 * @param {number} calculatedAverage - 计算得到的平均评分
 * @returns {boolean} 是否正确
 */
export function validateAverageCalculation(ratings, calculatedAverage) {
  const expectedAverage = calculateAverageRating(ratings);
  
  // 允许浮点数精度误差
  return Math.abs(expectedAverage - calculatedAverage) < 0.01;
}

/**
 * 验证增量计算的正确性
 * @param {Array} originalRatings - 原始评分列表
 * @param {number} newRating - 新评分
 * @param {number} calculatedAverage - 增量计算得到的平均评分
 * @returns {boolean} 是否正确
 */
export function validateIncrementalCalculation(originalRatings, newRating, calculatedAverage) {
  const allRatings = [...originalRatings, newRating];
  const expectedAverage = calculateAverageRating(allRatings);
  
  return Math.abs(expectedAverage - calculatedAverage) < 0.01;
}
