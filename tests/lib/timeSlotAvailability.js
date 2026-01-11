/**
 * 时段可用性逻辑模块
 * 用于测试 Property 9: Time Slot Availability
 * Validates: Requirements 4.2
 */

/**
 * 检查时段是否在技师排班范围内
 * @param {Object} slot - 时段对象 { date, time }
 * @param {Array} schedule - 技师排班列表 [{ date, startTime, endTime }]
 * @returns {boolean} 是否在排班范围内
 */
export function isWithinSchedule(slot, schedule) {
  if (!slot || !schedule || schedule.length === 0) {
    return false;
  }

  const slotDate = slot.date;
  const slotTime = slot.time;

  for (const sch of schedule) {
    if (sch.date === slotDate) {
      if (slotTime >= sch.startTime && slotTime < sch.endTime) {
        return true;
      }
    }
  }

  return false;
}

/**
 * 检查时段是否与已有订单冲突
 * @param {Object} slot - 时段对象 { date, time, duration }
 * @param {Array} orders - 已接受的订单列表 [{ date, time, duration }]
 * @returns {boolean} 是否有冲突
 */
export function hasOrderConflict(slot, orders) {
  if (!slot || !orders || orders.length === 0) {
    return false;
  }

  const slotStart = timeToMinutes(slot.time);
  const slotEnd = slotStart + (slot.duration || 60);

  for (const order of orders) {
    if (order.date !== slot.date) {
      continue;
    }

    const orderStart = timeToMinutes(order.time);
    const orderEnd = orderStart + (order.duration || 60);

    // 检查时间重叠
    if (slotStart < orderEnd && slotEnd > orderStart) {
      return true;
    }
  }

  return false;
}

/**
 * 检查时段是否在未来
 * @param {Object} slot - 时段对象 { date, time }
 * @param {Date} currentTime - 当前时间
 * @param {number} minAdvanceMinutes - 最小提前预约时间（分钟）
 * @returns {boolean} 是否在未来
 */
export function isInFuture(slot, currentTime, minAdvanceMinutes = 120) {
  if (!slot || !currentTime) {
    return false;
  }

  const slotDateTime = new Date(`${slot.date}T${slot.time}:00`);
  const minBookingTime = new Date(currentTime.getTime() + minAdvanceMinutes * 60 * 1000);

  return slotDateTime >= minBookingTime;
}

/**
 * 验证时段是否可用
 * @param {Object} slot - 时段对象 { date, time, duration }
 * @param {Object} context - 上下文 { schedule, orders, currentTime, minAdvanceMinutes }
 * @returns {Object} { available, reason }
 */
export function validateTimeSlot(slot, context) {
  const { schedule, orders, currentTime, minAdvanceMinutes = 120 } = context;

  // 检查是否在排班范围内
  if (!isWithinSchedule(slot, schedule)) {
    return { available: false, reason: 'not_in_schedule' };
  }

  // 检查是否与已有订单冲突
  if (hasOrderConflict(slot, orders)) {
    return { available: false, reason: 'order_conflict' };
  }

  // 检查是否在未来
  if (!isInFuture(slot, currentTime, minAdvanceMinutes)) {
    return { available: false, reason: 'in_past' };
  }

  return { available: true, reason: null };
}

/**
 * 获取可用时段列表
 * @param {string} date - 日期 YYYY-MM-DD
 * @param {Object} context - 上下文 { schedule, orders, currentTime, minAdvanceMinutes, slotInterval }
 * @returns {Array} 可用时段列表
 */
export function getAvailableSlots(date, context) {
  const { schedule, orders, currentTime, minAdvanceMinutes = 120, slotInterval = 30, serviceDuration = 60 } = context;

  const availableSlots = [];

  // 找到该日期的排班
  const daySchedules = schedule.filter(s => s.date === date);
  if (daySchedules.length === 0) {
    return availableSlots;
  }

  for (const sch of daySchedules) {
    let currentMinutes = timeToMinutes(sch.startTime);
    const endMinutes = timeToMinutes(sch.endTime);

    while (currentMinutes + serviceDuration <= endMinutes) {
      const time = minutesToTime(currentMinutes);
      const slot = { date, time, duration: serviceDuration };

      const validation = validateTimeSlot(slot, { schedule, orders, currentTime, minAdvanceMinutes });
      
      availableSlots.push({
        ...slot,
        available: validation.available,
        reason: validation.reason
      });

      currentMinutes += slotInterval;
    }
  }

  return availableSlots;
}

/**
 * 时间字符串转分钟数
 * @param {string} time - 时间字符串 HH:MM
 * @returns {number} 分钟数
 */
export function timeToMinutes(time) {
  if (!time || typeof time !== 'string') {
    return 0;
  }
  const [hours, minutes] = time.split(':').map(Number);
  return hours * 60 + minutes;
}

/**
 * 分钟数转时间字符串
 * @param {number} minutes - 分钟数
 * @returns {string} 时间字符串 HH:MM
 */
export function minutesToTime(minutes) {
  const hours = Math.floor(minutes / 60);
  const mins = minutes % 60;
  return `${String(hours).padStart(2, '0')}:${String(mins).padStart(2, '0')}`;
}
