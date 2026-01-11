/**
 * 订单分组逻辑模块
 * 用于测试 Property 12: Order List Grouping
 * Validates: Requirements 5.1
 */

/**
 * 订单状态枚举
 */
export const ORDER_STATUS = {
  PENDING_PAYMENT: 0,  // 待支付
  PAID: 1,             // 已支付/待接单
  ACCEPTED: 2,         // 已接单/待服务
  DEPARTING: 3,        // 技师出发中
  IN_SERVICE: 4,       // 服务中
  COMPLETED: 5,        // 已完成
  CANCELLED: 6,        // 已取消
  REFUNDED: 7          // 已退款
};

/**
 * 状态分组映射
 */
export const STATUS_GROUPS = {
  pending_payment: [ORDER_STATUS.PENDING_PAYMENT],
  upcoming: [ORDER_STATUS.PAID, ORDER_STATUS.ACCEPTED],
  in_progress: [ORDER_STATUS.DEPARTING, ORDER_STATUS.IN_SERVICE],
  completed: [ORDER_STATUS.COMPLETED],
  cancelled: [ORDER_STATUS.CANCELLED, ORDER_STATUS.REFUNDED]
};

/**
 * 获取订单所属分组
 * @param {number} status - 订单状态
 * @returns {string|null} 分组名称
 */
export function getOrderGroup(status) {
  for (const [groupName, statuses] of Object.entries(STATUS_GROUPS)) {
    if (statuses.includes(status)) {
      return groupName;
    }
  }
  return null;
}

/**
 * 按状态分组订单
 * @param {Array} orders - 订单列表
 * @returns {Object} 分组后的订单 { groupName: [orders] }
 */
export function groupOrdersByStatus(orders) {
  const groups = {
    pending_payment: [],
    upcoming: [],
    in_progress: [],
    completed: [],
    cancelled: []
  };

  if (!orders || !Array.isArray(orders)) {
    return groups;
  }

  for (const order of orders) {
    const group = getOrderGroup(order.status);
    if (group && groups[group]) {
      groups[group].push(order);
    }
  }

  return groups;
}

/**
 * 筛选指定状态的订单
 * @param {Array} orders - 订单列表
 * @param {string} groupName - 分组名称 ('all' 表示全部)
 * @returns {Array} 筛选后的订单
 */
export function filterOrdersByGroup(orders, groupName) {
  if (!orders || !Array.isArray(orders)) {
    return [];
  }

  if (groupName === 'all') {
    return [...orders];
  }

  const statuses = STATUS_GROUPS[groupName];
  if (!statuses) {
    return [];
  }

  return orders.filter(order => statuses.includes(order.status));
}

/**
 * 验证订单只属于一个分组
 * @param {Object} order - 订单对象
 * @returns {boolean} 是否只属于一个分组
 */
export function orderBelongsToExactlyOneGroup(order) {
  if (!order || typeof order.status !== 'number') {
    return false;
  }

  let groupCount = 0;
  for (const statuses of Object.values(STATUS_GROUPS)) {
    if (statuses.includes(order.status)) {
      groupCount++;
    }
  }

  return groupCount === 1;
}

/**
 * 验证分组结果的正确性
 * @param {Array} orders - 原始订单列表
 * @param {Object} groups - 分组结果
 * @returns {Object} { valid, errors }
 */
export function validateGrouping(orders, groups) {
  const errors = [];

  if (!orders || !groups) {
    return { valid: false, errors: ['Invalid input'] };
  }

  // 检查每个订单是否在正确的分组中
  for (const order of orders) {
    const expectedGroup = getOrderGroup(order.status);
    if (!expectedGroup) {
      errors.push(`Order ${order.orderNo} has invalid status ${order.status}`);
      continue;
    }

    const groupOrders = groups[expectedGroup] || [];
    const found = groupOrders.some(o => o.orderNo === order.orderNo);
    
    if (!found) {
      errors.push(`Order ${order.orderNo} not found in expected group ${expectedGroup}`);
    }
  }

  // 检查分组中的订单是否都有正确的状态
  for (const [groupName, groupOrders] of Object.entries(groups)) {
    const validStatuses = STATUS_GROUPS[groupName];
    if (!validStatuses) continue;

    for (const order of groupOrders) {
      if (!validStatuses.includes(order.status)) {
        errors.push(`Order ${order.orderNo} with status ${order.status} should not be in group ${groupName}`);
      }
    }
  }

  // 检查订单总数是否一致
  const totalInGroups = Object.values(groups).reduce((sum, g) => sum + g.length, 0);
  if (totalInGroups !== orders.length) {
    errors.push(`Total orders mismatch: ${totalInGroups} in groups vs ${orders.length} original`);
  }

  return { valid: errors.length === 0, errors };
}

/**
 * 获取所有有效的订单状态
 * @returns {Array} 有效状态列表
 */
export function getAllValidStatuses() {
  return Object.values(ORDER_STATUS);
}

/**
 * 检查状态是否有效
 * @param {number} status - 状态值
 * @returns {boolean} 是否有效
 */
export function isValidStatus(status) {
  return getAllValidStatuses().includes(status);
}
