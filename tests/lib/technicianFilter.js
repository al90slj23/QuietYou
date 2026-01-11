/**
 * 技师筛选和排序逻辑模块
 * 用于测试 Property 5: Technician Distance Sorting
 * Validates: Requirements 3.1
 */

/**
 * 筛选技师列表
 * @param {Array} technicians - 技师列表
 * @param {Object} filters - 筛选条件
 * @returns {Array} 筛选后的技师列表
 */
export function filterTechnicians(technicians, filters = {}) {
  let filtered = [...technicians];

  // 性别筛选
  if (filters.gender) {
    const gender = parseInt(filters.gender);
    if (!isNaN(gender)) {
      filtered = filtered.filter(t => t.gender === gender);
    }
  }

  // 评分筛选
  if (filters.rating) {
    const minRating = parseFloat(filters.rating);
    if (!isNaN(minRating)) {
      filtered = filtered.filter(t => t.rating >= minRating);
    }
  }

  // 距离筛选
  if (filters.distance) {
    const maxDistance = parseFloat(filters.distance);
    if (!isNaN(maxDistance)) {
      filtered = filtered.filter(t => t.distance <= maxDistance);
    }
  }

  return filtered;
}

/**
 * 按距离排序技师列表（升序）
 * @param {Array} technicians - 技师列表
 * @returns {Array} 排序后的技师列表
 */
export function sortByDistance(technicians) {
  return [...technicians].sort((a, b) => a.distance - b.distance);
}

/**
 * 按评分排序技师列表（降序）
 * @param {Array} technicians - 技师列表
 * @returns {Array} 排序后的技师列表
 */
export function sortByRating(technicians) {
  return [...technicians].sort((a, b) => b.rating - a.rating);
}

/**
 * 按价格排序技师列表
 * @param {Array} technicians - 技师列表
 * @param {string} order - 排序方向 'asc' | 'desc'
 * @returns {Array} 排序后的技师列表
 */
export function sortByPrice(technicians, order = 'asc') {
  return [...technicians].sort((a, b) => 
    order === 'asc' ? a.price - b.price : b.price - a.price
  );
}

/**
 * 综合排序（默认按距离）
 * @param {Array} technicians - 技师列表
 * @param {string} sortType - 排序类型
 * @returns {Array} 排序后的技师列表
 */
export function sortTechnicians(technicians, sortType = '') {
  switch (sortType) {
    case 'distance_asc':
      return sortByDistance(technicians);
    case 'rating_desc':
      return sortByRating(technicians);
    case 'price_asc':
      return sortByPrice(technicians, 'asc');
    case 'price_desc':
      return sortByPrice(technicians, 'desc');
    default:
      // 综合排序：默认按距离
      return sortByDistance(technicians);
  }
}

/**
 * 验证列表是否按距离升序排列
 * @param {Array} technicians - 技师列表
 * @returns {boolean} 是否有序
 */
export function isDistanceSorted(technicians) {
  for (let i = 0; i < technicians.length - 1; i++) {
    if (technicians[i].distance > technicians[i + 1].distance) {
      return false;
    }
  }
  return true;
}

/**
 * 验证列表是否按评分降序排列
 * @param {Array} technicians - 技师列表
 * @returns {boolean} 是否有序
 */
export function isRatingSorted(technicians) {
  for (let i = 0; i < technicians.length - 1; i++) {
    if (technicians[i].rating < technicians[i + 1].rating) {
      return false;
    }
  }
  return true;
}
