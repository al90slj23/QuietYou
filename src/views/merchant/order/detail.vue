<template>
  <div class="order-detail-page">
    <div class="status-bar" :class="order.statusClass">
      <div class="status-text">{{ order.statusText }}</div>
      <div class="status-desc">{{ order.statusDesc }}</div>
    </div>

    <div class="section">
      <div class="section-title">服务信息</div>
      <div class="info-item">
        <span class="label">服务项目</span>
        <span class="value">{{ order.serviceName }}</span>
      </div>
      <div class="info-item">
        <span class="label">预约时间</span>
        <span class="value">{{ order.scheduledTime }}</span>
      </div>
      <div class="info-item">
        <span class="label">服务地址</span>
        <span class="value">{{ order.address }}</span>
      </div>
    </div>

    <div class="section">
      <div class="section-title">人员信息</div>
      <div class="info-item">
        <span class="label">顾客</span>
        <span class="value">{{ order.customerName }} {{ order.customerPhone }}</span>
      </div>
      <div class="info-item">
        <span class="label">技师</span>
        <span class="value">{{ order.techName }}</span>
      </div>
    </div>

    <div class="section">
      <div class="section-title">费用明细</div>
      <div class="info-item">
        <span class="label">服务费</span>
        <span class="value">¥{{ order.servicePrice }}</span>
      </div>
      <div class="info-item">
        <span class="label">平台服务费</span>
        <span class="value">-¥{{ order.platformFee }}</span>
      </div>
      <div class="info-item total">
        <span class="label">实收金额</span>
        <span class="value">¥{{ order.actualIncome }}</span>
      </div>
    </div>

    <div class="action-bar" v-if="order.status === 1">
      <t-button variant="outline" @click="rejectOrder">拒绝订单</t-button>
      <t-button theme="primary" @click="confirmOrder">确认订单</t-button>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { Button as TButton, Toast } from 'tdesign-mobile-vue'

const route = useRoute()
const router = useRouter()

const order = ref({
  id: route.params.id,
  orderNo: 'QY202601130001',
  serviceName: '全身推拿 60分钟',
  scheduledTime: '2026-01-13 14:00',
  address: '成都市武侯区天府大道100号',
  customerName: '李女士',
  customerPhone: '138****8888',
  techName: '张师傅',
  servicePrice: 198,
  platformFee: 20,
  actualIncome: 178,
  status: 1,
  statusText: '待确认',
  statusDesc: '请尽快确认订单',
  statusClass: 'pending'
})

const confirmOrder = () => {
  Toast({ message: '已确认订单', theme: 'success' })
  router.back()
}

const rejectOrder = () => {
  Toast({ message: '已拒绝订单', theme: 'warning' })
  router.back()
}
</script>

<style lang="scss" scoped>
$primary: #07c160;

.order-detail-page { min-height: 100vh; background: #f5f5f5; padding-bottom: 100px; }

.status-bar {
  padding: 30px 20px;
  text-align: center;
  color: #fff;
  &.pending { background: linear-gradient(135deg, #f57c00 0%, #ff9800 100%); }
  &.serving { background: linear-gradient(135deg, #1976d2 0%, #2196f3 100%); }
  &.completed { background: linear-gradient(135deg, $primary 0%, #10b981 100%); }
  .status-text { font-size: 20px; font-weight: 600; margin-bottom: 8px; }
  .status-desc { font-size: 14px; opacity: 0.9; }
}

.section { background: #fff; margin: 15px; border-radius: 12px; padding: 15px; }
.section-title { font-size: 14px; color: #999; margin-bottom: 15px; }

.info-item {
  display: flex;
  justify-content: space-between;
  padding: 10px 0;
  border-bottom: 1px solid #f5f5f5;
  &:last-child { border-bottom: none; }
  .label { font-size: 14px; color: #666; }
  .value { font-size: 14px; color: #1a1a1a; }
  &.total {
    .label { font-weight: 600; }
    .value { font-size: 18px; font-weight: 600; color: #f44336; }
  }
}

.action-bar {
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  padding: 15px;
  background: #fff;
  display: flex;
  gap: 15px;
  box-shadow: 0 -2px 10px rgba(0,0,0,0.05);
  button { flex: 1; }
}
</style>
