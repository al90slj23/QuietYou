/**
 * 服务筛选逻辑模块
 * 用于测试 Property 4: Service Filter Consistency
 * Validates: Requirements 2.4
 */

/**
 * 筛选服务列表
 * @param {Array} services - 服务列表
 * @param {Object} filters - 筛选条件
 * @param {number} filters.categoryId - 分类ID (0表示全部)
 * @param {string} filters.price - 价格区间 (如 "200-300")
 * @param {string} filters.duration - 时长 (如 "60")
 * @param {string} filters.sort - 排序方式
 * @returns {Array} 筛选后的服务列表
 */
export function filterServices(services, filters = {}) {
  let filtered = [...services];

  // 分类筛选
  if (filters.categoryId && filters.categoryId > 0) {
    filtered = filtered.filter(s => s.categoryId === filters.categoryId);
  }

  // 价格筛选
  if (filters.price) {
    const [min, max] = filters.price.split('-').map(Number);
    if (!isNaN(min) && !isNaN(max)) {
      filtered = filtered.filter(s => s.price >= min && s.price <= max);
    }
  }

  // 时长筛选
  if (filters.duration) {
    const duration = parseInt(filters.duration);
    if (!isNaN(duration)) {
      filtered = filtered.filter(s => s.duration === duration);
    }
  }

  // 排序
  if (filters.sort) {
    switch (filters.sort) {
      case 'price_asc':
        filtered.sort((a, b) => a.price - b.price);
        break;
      case 'price_desc':
        filtered.sort((a, b) => b.price - a.price);
        break;
      case 'duration_asc':
        filtered.sort((a, b) => a.duration - b.duration);
        break;
      case 'duration_desc':
        filtered.sort((a, b) => b.duration - a.duration);
        break;
    }
  }

  return filtered;
}

/**
 * 验证服务是否匹配筛选条件
 * @param {Object} service - 服务对象
 * @param {Object} filters - 筛选条件
 * @returns {boolean} 是否匹配
 */
export function matchesFilters(service, filters = {}) {
  // 分类匹配
  if (filters.categoryId && filters.categoryId > 0) {
    if (service.categoryId !== filters.categoryId) {
      return false;
    }
  }

  // 价格匹配
  if (filters.price) {
    const [min, max] = filters.price.split('-').map(Number);
    if (!isNaN(min) && !isNaN(max)) {
      if (service.price < min || service.price > max) {
        return false;
      }
    }
  }

  // 时长匹配
  if (filters.duration) {
    const duration = parseInt(filters.duration);
    if (!isNaN(duration)) {
      if (service.duration !== duration) {
        return false;
      }
    }
  }

  return true;
}
