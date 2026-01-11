/**
 * 订单摘要逻辑模块
 * 用于测试 Property 10: Order Summary Completeness
 * Validates: Requirements 4.4
 */

/**
 * 必需的订单摘要字段
 */
export const REQUIRED_FIELDS = [
  'serviceName',
  'servicePrice',
  'techName',
  'scheduleDate',
  'scheduleTime',
  'address',
  'totalAmount'
];

/**
 * 验证订单摘要是否完整
 * @param {Object} summary - 订单摘要对象
 * @returns {Object} { complete, missingFields }
 */
export function validateOrderSummary(summary) {
  if (!summary || typeof summary !== 'object') {
    return { complete: false, missingFields: REQUIRED_FIELDS };
  }

  const missingFields = [];

  for (const field of REQUIRED_FIELDS) {
    if (!hasValidValue(summary[field])) {
      missingFields.push(field);
    }
  }

  return {
    complete: missingFields.length === 0,
    missingFields
  };
}

/**
 * 检查值是否有效（非空、非undefined、非null）
 * @param {any} value - 要检查的值
 * @returns {boolean} 是否有效
 */
export function hasValidValue(value) {
  if (value === undefined || value === null) {
    return false;
  }
  
  if (typeof value === 'string' && value.trim() === '') {
    return false;
  }
  
  if (typeof value === 'number' && (isNaN(value) || value < 0)) {
    return false;
  }
  
  return true;
}

/**
 * 从订单数据生成订单摘要
 * @param {Object} orderData - 原始订单数据
 * @returns {Object} 订单摘要
 */
export function generateOrderSummary(orderData) {
  if (!orderData) {
    return null;
  }

  return {
    serviceName: orderData.serviceName || '',
    servicePrice: orderData.price || 0,
    techName: orderData.techName || '',
    scheduleDate: orderData.date || '',
    scheduleTime: orderData.time || '',
    address: orderData.address || '',
    totalAmount: calculateTotalAmount(orderData)
  };
}

/**
 * 计算订单总金额
 * @param {Object} orderData - 订单数据
 * @returns {number} 总金额
 */
export function calculateTotalAmount(orderData) {
  if (!orderData) {
    return 0;
  }

  const servicePrice = parseFloat(orderData.price) || 0;
  const discount = parseFloat(orderData.discount) || 0;
  const tip = parseFloat(orderData.tip) || 0;

  return Math.max(0, servicePrice - discount + tip);
}

/**
 * 格式化订单摘要用于显示
 * @param {Object} summary - 订单摘要
 * @returns {Object} 格式化后的摘要
 */
export function formatOrderSummary(summary) {
  if (!summary) {
    return null;
  }

  return {
    serviceName: summary.serviceName,
    servicePrice: formatPrice(summary.servicePrice),
    techName: summary.techName,
    scheduleDateTime: formatDateTime(summary.scheduleDate, summary.scheduleTime),
    address: summary.address,
    totalAmount: formatPrice(summary.totalAmount)
  };
}

/**
 * 格式化价格
 * @param {number} price - 价格
 * @returns {string} 格式化后的价格
 */
export function formatPrice(price) {
  if (typeof price !== 'number' || isNaN(price)) {
    return '¥0.00';
  }
  return '¥' + price.toFixed(2);
}

/**
 * 格式化日期时间
 * @param {string} date - 日期 YYYY-MM-DD
 * @param {string} time - 时间 HH:MM
 * @returns {string} 格式化后的日期时间
 */
export function formatDateTime(date, time) {
  if (!date || !time) {
    return '';
  }
  
  const [year, month, day] = date.split('-');
  return `${month}月${day}日 ${time}`;
}

/**
 * 验证订单摘要中的价格一致性
 * @param {Object} summary - 订单摘要
 * @param {Object} orderData - 原始订单数据
 * @returns {boolean} 价格是否一致
 */
export function validatePriceConsistency(summary, orderData) {
  if (!summary || !orderData) {
    return false;
  }

  const expectedTotal = calculateTotalAmount(orderData);
  return summary.totalAmount === expectedTotal;
}
