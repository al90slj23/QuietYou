<template>
  <div class="order-detail">
    <!-- 订单状态 -->
    <div class="status-card" :class="order.statusClass">
      <div class="status-text">{{ order.statusText }}</div>
      <div class="status-desc">{{ statusDesc }}</div>
    </div>

    <!-- 服务信息 -->
    <div class="card">
      <div class="card-title">服务信息</div>
      <div class="service-info">
        <div class="service-name">{{ order.serviceName }}</div>
        <div class="service-price">¥{{ order.price }}</div>
      </div>
      <div class="info-item">
        <span class="label">预约时间</span>
        <span class="value">{{ order.scheduledTime }}</span>
      </div>
      <div class="info-item">
        <span class="label">服务时长</span>
        <span class="value">{{ order.duration }}分钟</span>
      </div>
    </div>

    <!-- 客户信息 -->
    <div class="card">
      <div class="card-title">客户信息</div>
      <div class="info-item">
        <span class="label">联系人</span>
        <span class="value">{{ order.customerName }}</span>
      </div>
      <div class="info-item">
        <span class="label">联系电话</span>
        <span class="value clickable" @click="callPhone">{{ order.customerPhone }}</span>
      </div>
      <div class="info-item">
        <span class="label">服务地址</span>
        <span class="value">{{ order.address }}</span>
      </div>
      <div class="info-item" v-if="order.remark">
        <span class="label">备注</span>
        <span class="value">{{ order.remark }}</span>
      </div>
    </div>

    <!-- 订单信息 -->
    <div class="card">
      <div class="card-title">订单信息</div>
      <div class="info-item">
        <span class="label">订单编号</span>
        <span class="value">{{ order.orderNo }}</span>
      </div>
      <div class="info-item">
        <span class="label">下单时间</span>
        <span class="value">{{ order.createTime }}</span>
      </div>
      <div class="info-item">
        <span class="label">支付方式</span>
        <span class="value">{{ order.payMethod }}</span>
      </div>
    </div>

    <!-- 底部操作 -->
    <div class="bottom-actions" v-if="showActions">
      <t-button block variant="outline" v-if="order.status === 1" @click="rejectOrder">拒绝接单</t-button>
      <t-button block theme="primary" v-if="order.status === 1" @click="acceptOrder">确认接单</t-button>
      <t-button block theme="primary" v-if="order.status === 2" @click="startService">开始服务</t-button>
      <t-button block theme="primary" v-if="order.status === 4" @click="completeService">完成服务</t-button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRoute } from 'vue-router'
import { Button as TButton } from 'tdesign-mobile-vue'

const route = useRoute()
const orderId = route.params.id

// 模拟订单数据
const order = ref({
  id: orderId,
  orderNo: 'QY202601130001',
  serviceName: '全身推拿 60分钟',
  price: 198,
  duration: 60,
  scheduledTime: '2026-01-13 14:00',
  customerName: '李女士',
  customerPhone: '138****8888',
  address: '成都市武侯区天府大道100号 XX小区 3栋1单元1001',
  remark: '请准时到达',
  createTime: '2026-01-13 10:30',
  payMethod: '微信支付',
  status: 1,
  statusText: '待接单',
  statusClass: 'pending'
})

const statusDesc = computed(() => {
  const descMap = {
    1: '请尽快确认是否接单',
    2: '请按时到达服务地点',
    4: '服务进行中，请认真服务',
    5: '服务已完成，感谢您的付出'
  }
  return descMap[order.value.status] || ''
})

const showActions = computed(() => [1, 2, 4].includes(order.value.status))

const callPhone = () => {
  window.location.href = `tel:${order.value.customerPhone}`
}

const acceptOrder = () => {
  console.log('接单')
}

const rejectOrder = () => {
  console.log('拒绝')
}

const startService = () => {
  console.log('开始服务')
}

const completeService = () => {
  console.log('完成服务')
}
</script>

<style lang="scss" scoped>
$primary: #07c160;

.order-detail {
  min-height: 100vh;
  background: #f5f5f5;
  padding-bottom: 100px;
}

.status-card {
  padding: 30px 20px;
  text-align: center;
  color: #fff;
  
  &.pending { background: linear-gradient(135deg, #ff9800, #f57c00); }
  &.accepted { background: linear-gradient(135deg, #2196f3, #1976d2); }
  &.serving { background: linear-gradient(135deg, $primary, #10b981); }
  &.completed { background: linear-gradient(135deg, #4caf50, #388e3c); }
  
  .status-text { font-size: 24px; font-weight: 600; margin-bottom: 8px; }
  .status-desc { font-size: 14px; opacity: 0.9; }
}

.card {
  background: #fff;
  margin: 15px;
  border-radius: 12px;
  padding: 15px;
}

.card-title {
  font-size: 16px;
  font-weight: 600;
  color: #1a1a1a;
  margin-bottom: 15px;
  padding-bottom: 10px;
  border-bottom: 1px solid #f0f0f0;
}

.service-info {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 15px;
  
  .service-name { font-size: 16px; font-weight: 500; color: #1a1a1a; }
  .service-price { font-size: 20px; font-weight: 600; color: #f44336; }
}

.info-item {
  display: flex;
  justify-content: space-between;
  padding: 10px 0;
  border-bottom: 1px solid #f5f5f5;
  
  &:last-child { border-bottom: none; }
  
  .label { font-size: 14px; color: #999; }
  .value { font-size: 14px; color: #1a1a1a; }
  .clickable { color: $primary; }
}

.bottom-actions {
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  padding: 15px;
  background: #fff;
  display: flex;
  gap: 12px;
  box-shadow: 0 -2px 10px rgba(0,0,0,0.05);
}
</style>
