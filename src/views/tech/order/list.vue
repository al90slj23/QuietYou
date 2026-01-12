<template>
  <div class="order-list-page">
    <!-- 顶部标签页 -->
    <t-tabs v-model="activeTab" @change="onTabChange">
      <t-tab-panel value="all" label="全部" />
      <t-tab-panel value="pending" label="待接单" />
      <t-tab-panel value="accepted" label="已接单" />
      <t-tab-panel value="serving" label="服务中" />
      <t-tab-panel value="completed" label="已完成" />
    </t-tabs>

    <!-- 订单列表 -->
    <div class="order-list">
      <div class="order-card" v-for="order in filteredOrders" :key="order.id" @click="goDetail(order.id)">
        <div class="order-header">
          <span class="order-no">{{ order.orderNo }}</span>
          <span class="status" :class="order.statusClass">{{ order.statusText }}</span>
        </div>
        <div class="order-content">
          <div class="service-name">{{ order.serviceName }}</div>
          <div class="info-row">
            <UserIcon size="14px" />
            <span>{{ order.customerName }}</span>
            <span class="time">{{ order.scheduledTime }}</span>
          </div>
          <div class="info-row">
            <LocationIcon size="14px" />
            <span>{{ order.address }}</span>
          </div>
        </div>
        <div class="order-footer">
          <span class="price">¥{{ order.price }}</span>
          <div class="actions">
            <t-button size="small" variant="outline" v-if="order.status === 1" @click.stop="rejectOrder(order)">拒绝</t-button>
            <t-button size="small" theme="primary" v-if="order.status === 1" @click.stop="acceptOrder(order)">接单</t-button>
            <t-button size="small" theme="primary" v-if="order.status === 2" @click.stop="startService(order)">开始服务</t-button>
            <t-button size="small" theme="primary" v-if="order.status === 4" @click.stop="completeService(order)">完成服务</t-button>
          </div>
        </div>
      </div>
    </div>

    <!-- 空状态 -->
    <div class="empty" v-if="!filteredOrders.length">
      <FileIcon size="48px" />
      <p>暂无订单</p>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { Tabs as TTabs, TabPanel as TTabPanel, Button as TButton } from 'tdesign-mobile-vue'
import { UserIcon, LocationIcon, FileIcon } from 'tdesign-icons-vue-next'

const router = useRouter()
const activeTab = ref('all')

// 模拟订单数据
const orders = ref([
  { id: 1, orderNo: 'QY202601130001', serviceName: '全身推拿 60分钟', customerName: '李女士', scheduledTime: '今天 14:00', address: '成都市武侯区天府大道100号', price: 198, status: 1, statusText: '待接单', statusClass: 'pending' },
  { id: 2, orderNo: 'QY202601130002', serviceName: '肩颈按摩 45分钟', customerName: '王先生', scheduledTime: '今天 16:30', address: '成都市高新区软件园B区', price: 158, status: 2, statusText: '已接单', statusClass: 'accepted' },
  { id: 3, orderNo: 'QY202601120003', serviceName: '足底按摩 60分钟', customerName: '张先生', scheduledTime: '昨天 20:00', address: '成都市锦江区春熙路', price: 168, status: 5, statusText: '已完成', statusClass: 'completed' }
])

const filteredOrders = computed(() => {
  if (activeTab.value === 'all') return orders.value
  const statusMap = { pending: 1, accepted: 2, serving: 4, completed: 5 }
  return orders.value.filter(o => o.status === statusMap[activeTab.value])
})

const onTabChange = (value) => {
  activeTab.value = value
}

const goDetail = (id) => {
  router.push(`/tech/order/detail/${id}`)
}

const acceptOrder = (order) => {
  console.log('接单:', order.id)
}

const rejectOrder = (order) => {
  console.log('拒绝:', order.id)
}

const startService = (order) => {
  console.log('开始服务:', order.id)
}

const completeService = (order) => {
  console.log('完成服务:', order.id)
}
</script>

<style lang="scss" scoped>
$primary: #07c160;

.order-list-page {
  min-height: 100vh;
  background: #f5f5f5;
}

.order-list {
  padding: 15px;
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.order-card {
  background: #fff;
  border-radius: 12px;
  padding: 15px;
}

.order-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 12px;
  
  .order-no { font-size: 12px; color: #999; }
  .status {
    font-size: 12px;
    padding: 2px 8px;
    border-radius: 4px;
    &.pending { background: #fff3e0; color: #f57c00; }
    &.accepted { background: #e3f2fd; color: #1976d2; }
    &.completed { background: #e8f5e9; color: #388e3c; }
  }
}

.order-content {
  .service-name {
    font-size: 16px;
    font-weight: 500;
    color: #1a1a1a;
    margin-bottom: 10px;
  }
  .info-row {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    color: #666;
    margin-bottom: 6px;
    .time { margin-left: auto; color: $primary; }
  }
}

.order-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 15px;
  padding-top: 15px;
  border-top: 1px solid #f0f0f0;
  
  .price { font-size: 20px; font-weight: 600; color: #f44336; }
  .actions { display: flex; gap: 8px; }
}

.empty {
  text-align: center;
  padding: 60px 0;
  color: #999;
  p { margin-top: 12px; font-size: 14px; }
}
</style>
