<template>
  <div class="order-list-page">
    <!-- 订单状态标签 -->
    <t-tabs v-model="activeStatus" @change="onStatusChange">
      <t-tab-panel value="all" label="全部" />
      <t-tab-panel value="pending" label="待支付" />
      <t-tab-panel value="confirmed" label="待服务" />
      <t-tab-panel value="completed" label="已完成" />
      <t-tab-panel value="cancelled" label="已取消" />
    </t-tabs>

    <!-- 订单列表 -->
    <div class="order-list">
      <div
        v-for="order in filteredOrders"
        :key="order.id"
        class="order-card"
        @click="router.push(`/order/detail/${order.id}`)"
      >
        <div class="order-header">
          <span class="order-shop">{{ order.shopName }}</span>
          <span class="order-status" :class="getStatusClass(order.status)">
            {{ getStatusText(order.status) }}
          </span>
        </div>
        <div class="order-content">
          <div class="order-image">
            <t-icon :name="order.icon" size="32px" />
          </div>
          <div class="order-info">
            <div class="order-service">{{ order.serviceName }}</div>
            <div class="order-tech">技师：{{ order.techName }}</div>
            <div class="order-time">{{ order.serviceTime }}</div>
          </div>
          <div class="order-price">
            <span class="price">{{ order.amount }}</span>
          </div>
        </div>
        <div class="order-actions">
          <t-button v-if="order.status === 'pending'" size="small" theme="primary" @click.stop="payOrder(order)">
            去支付
          </t-button>
          <t-button v-if="order.status === 'pending'" size="small" variant="outline" @click.stop="cancelOrder(order)">
            取消订单
          </t-button>
          <t-button v-if="order.status === 'completed' && !order.reviewed" size="small" theme="primary" @click.stop="reviewOrder(order)">
            去评价
          </t-button>
          <t-button v-if="order.status === 'completed'" size="small" variant="outline" @click.stop="reorder(order)">
            再次预约
          </t-button>
        </div>
      </div>
    </div>

    <t-empty v-if="filteredOrders.length === 0" description="暂无订单" />
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { Toast, Dialog } from 'tdesign-mobile-vue'

const router = useRouter()

const activeStatus = ref('all')

const orders = ref([
  { id: 1, shopName: '康乐养生馆', serviceName: '全身中式推拿', techName: '张师傅', serviceTime: '2026-01-12 14:00', amount: 298, status: 'pending', icon: 'gesture-press', reviewed: false },
  { id: 2, shopName: '悦享SPA会所', serviceName: '精油香薰SPA', techName: '李师傅', serviceTime: '2026-01-10 16:00', amount: 458, status: 'confirmed', icon: 'flower', reviewed: false },
  { id: 3, shopName: '康乐养生馆', serviceName: '肩颈深度调理', techName: '张师傅', serviceTime: '2026-01-08 10:00', amount: 198, status: 'completed', icon: 'heart', reviewed: false },
  { id: 4, shopName: '舒心按摩店', serviceName: '足底反射疗法', techName: '陈师傅', serviceTime: '2026-01-05 15:00', amount: 168, status: 'completed', icon: 'service', reviewed: true },
  { id: 5, shopName: '康乐养生馆', serviceName: '背部推拿放松', techName: '王师傅', serviceTime: '2026-01-03 11:00', amount: 218, status: 'cancelled', icon: 'gesture-press', reviewed: false }
])

const filteredOrders = computed(() => {
  if (activeStatus.value === 'all') return orders.value
  return orders.value.filter(o => o.status === activeStatus.value)
})

const onStatusChange = (val) => {
  activeStatus.value = val
}

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

const payOrder = (order) => {
  router.push(`/order/pay/${order.id}`)
}

const cancelOrder = (order) => {
  Dialog.confirm({
    title: '取消订单',
    content: '确定要取消该订单吗？',
    confirmBtn: '确定',
    cancelBtn: '取消'
  }).then(() => {
    order.status = 'cancelled'
    Toast.success('订单已取消')
  })
}

const reviewOrder = (order) => {
  Toast.info('评价功能开发中')
}

const reorder = (order) => {
  router.push({ path: '/user/order/create', query: { serviceId: 1 } })
}
</script>

<style lang="scss" scoped>
.order-list-page {
  background: #f5f5f5;
  min-height: 100vh;
}

.order-list {
  padding: 12px;
}

.order-card {
  background: #fff;
  border-radius: 8px;
  padding: 12px;
  margin-bottom: 12px;
}

.order-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-bottom: 12px;
  border-bottom: 1px solid #f0f0f0;
  margin-bottom: 12px;
}

.order-shop {
  font-size: 14px;
  font-weight: 500;
}

.order-status {
  font-size: 12px;
  
  &.warning { color: #f59e0b; }
  &.primary { color: #07c160; }
  &.success { color: #07c160; }
  &.default { color: #999; }
}

.order-content {
  display: flex;
  align-items: center;
}

.order-image {
  width: 60px;
  height: 60px;
  border-radius: 8px;
  background: linear-gradient(135deg, #e8f8ef 0%, #d1fae5 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.order-info {
  flex: 1;
  margin-left: 12px;
}

.order-service {
  font-size: 15px;
  font-weight: 500;
  margin-bottom: 4px;
}

.order-tech, .order-time {
  font-size: 12px;
  color: #666;
}

.order-price {
  text-align: right;
}

.order-actions {
  display: flex;
  justify-content: flex-end;
  gap: 8px;
  margin-top: 12px;
  padding-top: 12px;
  border-top: 1px solid #f0f0f0;
}
</style>
