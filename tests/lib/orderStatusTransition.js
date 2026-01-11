/**
 * 订单状态转换逻辑
 * Property 20: Order Status Transitions
 * Validates: Requirements 8.3, 8.6
 */

/**
 * 订单状态枚举
 */
const OrderStatus = {
  PENDING_PAYMENT: 'pending_payment',  // 待支付
  PENDING_ACCEPT: 'pending_accept',    // 待接单
  ACCEPTED: 'accepted',                // 已接单/待服务
  DEPARTED: 'departed',                // 已出发
  ARRIVED: 'arrived',                  // 已到达
  IN_PROGRESS: 'in_progress',          // 服务中
  COMPLETED: 'completed',              // 已完成
  CANCELLED: 'cancelled'               // 已取消
};

/**
 * 有效的状态转换映射
 * key: 当前状态, value: 可转换到的状态数组
 */
const validTransitions = {
  [OrderStatus.PENDING_PAYMENT]: [OrderStatus.PENDING_ACCEPT, OrderStatus.CANCELLED],
  [OrderStatus.PENDING_ACCEPT]: [OrderStatus.ACCEPTED, OrderStatus.CANCELLED],
  [OrderStatus.ACCEPTED]: [OrderStatus.DEPARTED, OrderStatus.CANCELLED],
  [OrderStatus.DEPARTED]: [OrderStatus.ARRIVED],
  [OrderStatus.ARRIVED]: [OrderStatus.IN_PROGRESS],
  [OrderStatus.IN_PROGRESS]: [OrderStatus.COMPLETED],
  [OrderStatus.COMPLETED]: [],  // 终态
  [OrderStatus.CANCELLED]: []   // 终态
};

/**
 * 检查状态转换是否有效
 * @param {string} fromStatus - 当前状态
 * @param {string} toStatus - 目标状态
 * @returns {boolean} 是否有效
 */
function isValidTransition(fromStatus, toStatus) {
  const allowedTransitions = validTransitions[fromStatus];
  if (!allowedTransitions) {
    return false;
  }
  return allowedTransitions.includes(toStatus);
}

/**
 * 执行状态转换
 * @param {Object} order - 订单对象
 * @param {string} newStatus - 目标状态
 * @returns {Object} 转换结果 { success, order, error }
 */
function transitionStatus(order, newStatus) {
  if (!isValidTransition(order.status, newStatus)) {
    return {
      success: false,
      order: order,
      error: `无法从 ${order.status} 转换到 ${newStatus}`
    };
  }

  const updatedOrder = {
    ...order,
    status: newStatus,
    statusHistory: [
      ...(order.statusHistory || []),
      {
        from: order.status,
        to: newStatus,
        timestamp: new Date().toISOString()
      }
    ]
  };

  // 特殊状态处理
  if (newStatus === OrderStatus.IN_PROGRESS) {
    updatedOrder.serviceStartTime = new Date().toISOString();
  }
  
  if (newStatus === OrderStatus.COMPLETED) {
    updatedOrder.serviceEndTime = new Date().toISOString();
  }

  return {
    success: true,
    order: updatedOrder,
    error: null
  };
}

/**
 * 获取订单可执行的下一步操作
 * @param {string} status - 当前状态
 * @returns {Array} 可执行的操作列表
 */
function getAvailableActions(status) {
  const actionMap = {
    [OrderStatus.PENDING_PAYMENT]: ['pay', 'cancel'],
    [OrderStatus.PENDING_ACCEPT]: ['accept', 'reject'],
    [OrderStatus.ACCEPTED]: ['depart', 'cancel'],
    [OrderStatus.DEPARTED]: ['arrive'],
    [OrderStatus.ARRIVED]: ['start_service'],
    [OrderStatus.IN_PROGRESS]: ['complete'],
    [OrderStatus.COMPLETED]: [],
    [OrderStatus.CANCELLED]: []
  };
  
  return actionMap[status] || [];
}

/**
 * 检查订单是否可以取消
 * @param {string} status - 当前状态
 * @returns {boolean} 是否可取消
 */
function canCancel(status) {
  // 只有待支付、待接单、已接单状态可以取消
  const cancellableStatuses = [
    OrderStatus.PENDING_PAYMENT,
    OrderStatus.PENDING_ACCEPT,
    OrderStatus.ACCEPTED
  ];
  return cancellableStatuses.includes(status);
}

/**
 * 检查订单是否处于终态
 * @param {string} status - 当前状态
 * @returns {boolean} 是否为终态
 */
function isTerminalStatus(status) {
  return status === OrderStatus.COMPLETED || status === OrderStatus.CANCELLED;
}

/**
 * 检查订单是否处于服务流程中
 * @param {string} status - 当前状态
 * @returns {boolean} 是否在服务流程中
 */
function isInServiceFlow(status) {
  const serviceFlowStatuses = [
    OrderStatus.DEPARTED,
    OrderStatus.ARRIVED,
    OrderStatus.IN_PROGRESS
  ];
  return serviceFlowStatuses.includes(status);
}

/**
 * 获取状态显示文本
 * @param {string} status - 状态
 * @returns {string} 显示文本
 */
function getStatusText(status) {
  const textMap = {
    [OrderStatus.PENDING_PAYMENT]: '待支付',
    [OrderStatus.PENDING_ACCEPT]: '待接单',
    [OrderStatus.ACCEPTED]: '待服务',
    [OrderStatus.DEPARTED]: '前往中',
    [OrderStatus.ARRIVED]: '已到达',
    [OrderStatus.IN_PROGRESS]: '服务中',
    [OrderStatus.COMPLETED]: '已完成',
    [OrderStatus.CANCELLED]: '已取消'
  };
  return textMap[status] || '未知状态';
}

/**
 * 模拟完整的服务流程
 * @param {Object} order - 初始订单（已接单状态）
 * @returns {Array} 状态转换历史
 */
function simulateServiceFlow(order) {
  const flow = [OrderStatus.DEPARTED, OrderStatus.ARRIVED, OrderStatus.IN_PROGRESS, OrderStatus.COMPLETED];
  const history = [];
  let currentOrder = { ...order };

  for (const nextStatus of flow) {
    const result = transitionStatus(currentOrder, nextStatus);
    history.push({
      from: currentOrder.status,
      to: nextStatus,
      success: result.success
    });
    
    if (result.success) {
      currentOrder = result.order;
    } else {
      break;
    }
  }

  return history;
}

module.exports = {
  OrderStatus,
  validTransitions,
  isValidTransition,
  transitionStatus,
  getAvailableActions,
  canCancel,
  isTerminalStatus,
  isInServiceFlow,
  getStatusText,
  simulateServiceFlow
};
