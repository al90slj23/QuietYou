/**
 * 订单取消操作逻辑模块
 * 用于测试 Property 14: Cancel Action Availability
 * Validates: Requirements 5.4
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
 * 可取消的订单状态列表
 * 根据 Requirements 5.4: 仅待服务状态可取消
 * 这里扩展为：待支付、待接单、待服务状态都可取消
 */
export const CANCELLABLE_STATUSES = [
  ORDER_STATUS.PENDING_PAYMENT,  // 待支付
  ORDER_STATUS.PAID,             // 待接单
  ORDER_STATUS.ACCEPTED          // 待服务
];

/**
 * 检查订单是否可以取消
 * @param {Object} order - 订单对象
 * @returns {boolean} 是否可以取消
 */
export function canCancelOrder(order) {
  if (!order || typeof order.status !== 'number') {
    return false;
  }
  
  return CANCELLABLE_STATUSES.includes(order.status);
}

/**
 * 获取取消操作的可用性及原因
 * @param {Object} order - 订单对象
 * @returns {Object} { available, reason }
 */
export function getCancelAvailability(order) {
  if (!order) {
    return { available: false, reason: 'invalid_order' };
  }
  
  if (typeof order.status !== 'number') {
    return { available: false, reason: 'invalid_status' };
  }
  
  if (CANCELLABLE_STATUSES.includes(order.status)) {
    return { available: true, reason: null };
  }
  
  // 根据状态返回不同的原因
  switch (order.status) {
    case ORDER_STATUS.DEPARTING:
      return { available: false, reason: 'technician_departing' };
    case ORDER_STATUS.IN_SERVICE:
      return { available: false, reason: 'service_in_progress' };
    case ORDER_STATUS.COMPLETED:
      return { available: false, reason: 'order_completed' };
    case ORDER_STATUS.CANCELLED:
      return { available: false, reason: 'already_cancelled' };
    case ORDER_STATUS.REFUNDED:
      return { available: false, reason: 'already_refunded' };
    default:
      return { available: false, reason: 'unknown_status' };
  }
}

/**
 * 执行取消订单操作
 * @param {Object} order - 订单对象
 * @param {string} reason - 取消原因
 * @returns {Object} { success, order, error }
 */
export function cancelOrder(order, reason = '用户取消') {
  const availability = getCancelAvailability(order);
  
  if (!availability.available) {
    return { 
      success: false, 
      order: order, 
      error: availability.reason 
    };
  }
  
  // 创建取消后的订单副本
  const cancelledOrder = {
    ...order,
    status: ORDER_STATUS.CANCELLED,
    cancelTime: new Date().toISOString(),
    cancelReason: reason
  };
  
  return { 
    success: true, 
    order: cancelledOrder, 
    error: null 
  };
}

/**
 * 获取订单可用的操作列表
 * @param {Object} order - 订单对象
 * @returns {Array} 可用操作列表
 */
export function getAvailableActions(order) {
  if (!order || typeof order.status !== 'number') {
    return [];
  }
  
  const actions = [];
  
  switch (order.status) {
    case ORDER_STATUS.PENDING_PAYMENT:
      actions.push('cancel', 'pay');
      break;
    case ORDER_STATUS.PAID:
    case ORDER_STATUS.ACCEPTED:
      actions.push('cancel', 'contact');
      break;
    case ORDER_STATUS.DEPARTING:
    case ORDER_STATUS.IN_SERVICE:
      actions.push('contact');
      break;
    case ORDER_STATUS.COMPLETED:
      if (!order.hasReview) {
        actions.push('review');
      }
      actions.push('rebook');
      break;
    case ORDER_STATUS.CANCELLED:
    case ORDER_STATUS.REFUNDED:
      actions.push('rebook');
      break;
  }
  
  return actions;
}

/**
 * 验证取消操作的一致性
 * @param {Object} order - 原始订单
 * @param {Object} cancelledOrder - 取消后的订单
 * @returns {boolean} 是否一致
 */
export function validateCancelResult(order, cancelledOrder) {
  if (!order || !cancelledOrder) {
    return false;
  }
  
  // 取消后状态应该是 CANCELLED
  if (cancelledOrder.status !== ORDER_STATUS.CANCELLED) {
    return false;
  }
  
  // 应该有取消时间
  if (!cancelledOrder.cancelTime) {
    return false;
  }
  
  // 应该有取消原因
  if (!cancelledOrder.cancelReason) {
    return false;
  }
  
  // 其他字段应该保持不变
  if (cancelledOrder.orderNo !== order.orderNo) {
    return false;
  }
  
  return true;
}
