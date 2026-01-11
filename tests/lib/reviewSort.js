/**
 * 评价排序逻辑模块
 * 用于测试 Property 17: Review Sorting
 * Validates: Requirements 6.5
 */

/**
 * 按时间降序排序评价列表（最新的在前）
 * @param {Array} reviews - 评价列表
 * @returns {Array} 排序后的评价列表
 */
export function sortReviewsByTime(reviews) {
  return [...reviews].sort((a, b) => {
    const timeA = new Date(a.createdAt).getTime();
    const timeB = new Date(b.createdAt).getTime();
    return timeB - timeA; // 降序：最新的在前
  });
}

/**
 * 验证评价列表是否按时间降序排列
 * @param {Array} reviews - 评价列表
 * @returns {boolean} 是否有序
 */
export function isTimeSortedDescending(reviews) {
  for (let i = 0; i < reviews.length - 1; i++) {
    const timeA = new Date(reviews[i].createdAt).getTime();
    const timeB = new Date(reviews[i + 1].createdAt).getTime();
    if (timeA < timeB) {
      return false;
    }
  }
  return true;
}

/**
 * 筛选指定技师的评价
 * @param {Array} reviews - 所有评价
 * @param {number} techId - 技师ID
 * @returns {Array} 该技师的评价列表
 */
export function filterReviewsByTechnician(reviews, techId) {
  return reviews.filter(r => r.techId === techId);
}

/**
 * 获取技师的评价列表（按时间降序）
 * @param {Array} reviews - 所有评价
 * @param {number} techId - 技师ID
 * @returns {Array} 排序后的评价列表
 */
export function getTechnicianReviews(reviews, techId) {
  const filtered = filterReviewsByTechnician(reviews, techId);
  return sortReviewsByTime(filtered);
}
