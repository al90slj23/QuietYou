/**
 * 技师在线状态可见性逻辑
 * Property 21: Technician Online Status Visibility
 * Validates: Requirements 9.3, 9.4
 */

/**
 * 技师状态枚举
 */
const TechnicianStatus = {
  ONLINE: 'online',    // 在线，可接单
  OFFLINE: 'offline',  // 离线，不可接单
  BUSY: 'busy'         // 忙碌，有进行中订单
};

/**
 * 判断技师是否应该在搜索结果中显示
 * @param {Object} technician - 技师对象
 * @param {string} technician.status - 技师状态
 * @param {boolean} technician.isActive - 账号是否激活
 * @param {boolean} technician.isCertified - 是否已认证
 * @returns {boolean} 是否应该显示在搜索结果中
 */
function shouldShowInSearchResults(technician) {
  // 账号未激活或未认证的技师不显示
  if (!technician.isActive || !technician.isCertified) {
    return false;
  }
  
  // 只有在线状态的技师才显示在搜索结果中
  // 离线和忙碌状态都不显示
  return technician.status === TechnicianStatus.ONLINE;
}

/**
 * 判断技师是否可以接收新订单
 * @param {Object} technician - 技师对象
 * @returns {boolean} 是否可以接收新订单
 */
function canReceiveNewOrders(technician) {
  // 必须是激活且认证的账号
  if (!technician.isActive || !technician.isCertified) {
    return false;
  }
  
  // 只有在线状态可以接收新订单
  return technician.status === TechnicianStatus.ONLINE;
}

/**
 * 切换技师在线状态
 * @param {Object} technician - 技师对象
 * @param {boolean} goOnline - 是否上线
 * @returns {Object} 更新后的技师对象
 */
function toggleOnlineStatus(technician, goOnline) {
  // 如果技师正在服务中（忙碌状态），不能直接下线
  if (technician.status === TechnicianStatus.BUSY && !goOnline) {
    return {
      ...technician,
      error: '有进行中的订单，无法下线'
    };
  }
  
  // 如果账号未激活或未认证，不能上线
  if (goOnline && (!technician.isActive || !technician.isCertified)) {
    return {
      ...technician,
      error: '账号未激活或未认证，无法上线'
    };
  }
  
  return {
    ...technician,
    status: goOnline ? TechnicianStatus.ONLINE : TechnicianStatus.OFFLINE,
    error: null
  };
}

/**
 * 当技师接受订单时更新状态
 * @param {Object} technician - 技师对象
 * @returns {Object} 更新后的技师对象
 */
function onOrderAccepted(technician) {
  // 接单后变为忙碌状态
  return {
    ...technician,
    status: TechnicianStatus.BUSY
  };
}

/**
 * 当技师完成订单时更新状态
 * @param {Object} technician - 技师对象
 * @param {boolean} hasMoreOrders - 是否还有其他进行中的订单
 * @returns {Object} 更新后的技师对象
 */
function onOrderCompleted(technician, hasMoreOrders) {
  // 如果还有其他订单，保持忙碌状态
  if (hasMoreOrders) {
    return technician;
  }
  
  // 否则恢复在线状态（假设技师之前是在线的）
  return {
    ...technician,
    status: TechnicianStatus.ONLINE
  };
}

/**
 * 过滤搜索结果中的技师列表
 * @param {Array} technicians - 技师列表
 * @returns {Array} 过滤后的技师列表（只包含在线技师）
 */
function filterVisibleTechnicians(technicians) {
  return technicians.filter(shouldShowInSearchResults);
}

/**
 * 获取技师状态显示文本
 * @param {string} status - 技师状态
 * @returns {string} 状态显示文本
 */
function getStatusDisplayText(status) {
  const statusTexts = {
    [TechnicianStatus.ONLINE]: '在线',
    [TechnicianStatus.OFFLINE]: '离线',
    [TechnicianStatus.BUSY]: '服务中'
  };
  return statusTexts[status] || '未知';
}

module.exports = {
  TechnicianStatus,
  shouldShowInSearchResults,
  canReceiveNewOrders,
  toggleOnlineStatus,
  onOrderAccepted,
  onOrderCompleted,
  filterVisibleTechnicians,
  getStatusDisplayText
};
