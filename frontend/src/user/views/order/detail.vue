<template>
  <div class="order-detail-page">
    <t-navbar title="订单详情" left-arrow @left-click="router.back()" />
    
    <!-- 订单状态 -->
    <div class="status-section" :class="getStatusClass(order.status)">
      <t-icon :name="getStatusIcon(order.status)" size="32px" />
      <div class="status-info">
        <div class="status-text">{{ getStatusText(order.status) }}</div>
        <div class="status-desc">{{ getStatusDesc(order.status) }}</div>
      </div>
    </div>

    <!-- 服务信息 -->
    <div class="card">
      <div class="card-title">服务信息</div>
      <div class="info-item">
        <span class="info-label">服务项目</span>
        <span class="info-value">{{ order.serviceName }}</span>
      </div>
      <div class="info-item">
        <span class="info-label">服务技师</span>
        <span class="info-value">{{ order.techName }}</span>
      </div>
      <div class="info-item">
        <span class="info-label">服务时长</span>
        <span class="info-value">{{ order.duration }}分钟</span>
      </div>
      <div class="info-item">
        <span class="info-label">预约时间</span>
        <span class="info-value">{{ order.serviceTime }}</span>
      </div>
    </div>

    <!-- 服务地址 -->
    <div class="card">
      <div class="card-title">服务地址</div>
      <div class="address-info">
        <div class="address-name">{{ order.address.name }} {{ order.address.phone }}</div>
        <div class="address-detail">{{ order.address.detail }}</div>
      </div>
    </div>

    <!-- 订单信息 -->
    <div class="card">
      <div class="card-title">订单信息</div>
      <div class="info-item">
        <span class="info-label">订单编号</span>
        <span class="info-value">{{ order.orderNo }}</span>
      </div>
      <div class="info-item">
        <span class="info-label">下单时间</span>
        <span class="info-value">{{ order.createdAt }}</span>
      </div>
      <div class="info-item">
        <span class="info-label">支付方式</span>
        <span class="info-value">{{ order.payMethod || '未支付' }}</span>
      </div>
    </div>

    <!-- 费用明细 -->
    <div class="card">
      <div class="card-title">费用明细</div>
      <div class="info-item">
        <span class="info-label">服务费用</span>
        <span class="info-value">¥{{ order.serviceAmount }}</span>
      </div>
      <div class="info-item" v-if="order.discount > 0">
        <span class="info-label">优惠</span>
        <span class="info-value discount">-¥{{ order.discount }}</span>
      </div>
      <div class="info-item total">
        <span class="info-label">实付金额</span>
        <span class="info-value price">{{ order.amount }}</span>
      </div>
    </div>

    <!-- 底部操作 -->
    <div class="bottom-bar" v-if="order.status === 'pending'">
      <t-button variant="outline" @click="cancelOrder">取消订单</t-button>
      <t-button theme="primary" @click="payOrder">去支付</t-button>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { Toast, Dialog } from 'tdesign-mobile-vue'

const router = useRouter()
const route = useRoute()

const order = ref({
  id: 1,
  orderNo: 'QY202601120001',
  serviceName: '全身中式推拿',
  techName: '张师傅',
  duration: 60,
  serviceTime: '2026-01-12 14:00',
  status: 'pending',
  serviceAmount: 298,
  discount: 0,
  amount: 298,
  payMethod: '',
  createdAt: '2026-01-11 10:30',
  address: {
    name: '张先生',
    phone: '138****8888',
    detail: '上海市浦东新区陆家嘴环路1000号 恒生银行大厦 1801室'
  }
})

const getStatusText = (status) => {
  const map = {
    pending: '待支付',
    confirmed: '待服务',
    in_progress: '服务中',
    completed: '已完成',
    cancelled: '已取消'
  }
  return map[status] || status
}

const getStatusDesc = (status) => {
  const map = {
    pending: '请在30分钟内完成支付',
    confirmed: '技师将准时上门服务',
    in_progress: '技师正在为您服务',
    completed: '感谢您的使用，期待再次为您服务',
    cancelled: '订单已取消'
  }
  return map[status] || ''
}

const getStatusIcon = (status) => {
  const map = {
    pending: 'time',
    confirmed: 'check-circle',
    in_progress: 'service',
    completed: 'check-circle-filled',
    cancelled: 'close-circle'
  }
  return map[status] || 'info-circle'
}

const getStatusClass = (status) => {
  const map = {
    pending: 'warning',
    confirmed: 'primary',
    in_progress: 'primary',
    completed: 'success',
    cancelled: 'default'
  }
  return map[status] || 'default'
}

const cancelOrder = () => {
  Dialog.confirm({
    title: '取消订单',
    content: '确定要取消该订单吗？',
    confirmBtn: '确定',
    cancelBtn: '取消'
  }).then(() => {
    order.value.status = 'cancelled'
    Toast.success('订单已取消')
  })
}

const payOrder = () => {
  router.push(`/order/pay/${order.value.id}`)
}
</script>

<style lang="scss" scoped>
.order-detail-page {
  background: #f5f5f5;
  min-height: 100vh;
  padding-bottom: 80px;
}

.status-section {
  display: flex;
  align-items: center;
  padding: 20px 16px;
  color: #fff;
  
  &.warning { background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%); }
  &.primary { background: linear-gradient(135deg, #07c160 0%, #10b981 100%); }
  &.success { background: linear-gradient(135deg, #07c160 0%, #10b981 100%); }
  &.default { background: #999; }
}

.status-info {
  margin-left: 12px;
}

.status-text {
  font-size: 18px;
  font-weight: 600;
  margin-bottom: 4px;
}

.status-desc {
  font-size: 13px;
  opacity: 0.9;
}

.info-item {
  display: flex;
  justify-content: space-between;
  padding: 12px 0;
  border-bottom: 1px solid #f0f0f0;
  
  &:last-child {
    border-bottom: none;
  }
  
  &.total {
    padding-top: 16px;
    margin-top: 8px;
    border-top: 1px dashed #e5e5e5;
    border-bottom: none;
  }
}

.info-label {
  font-size: 14px;
  color: #666;
}

.info-value {
  font-size: 14px;
  color: #333;
  
  &.discount {
    color: #07c160;
  }
}

.address-name {
  font-size: 15px;
  font-weight: 500;
  margin-bottom: 4px;
}

.address-detail {
  font-size: 13px;
  color: #666;
  line-height: 1.5;
}

.bottom-bar {
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  display: flex;
  gap: 12px;
  padding: 12px 16px;
  background: #fff;
  border-top: 1px solid #e5e5e5;
  
  .t-button {
    flex: 1;
  }
}
</style>
