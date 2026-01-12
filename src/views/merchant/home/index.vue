<template>
  <div class="merchant-home">
    <!-- 店铺信息 -->
    <div class="shop-header">
      <div class="shop-info">
        <t-avatar :image="shopInfo.logo" size="56px" shape="round" />
        <div class="info">
          <div class="name">{{ shopInfo.name }}</div>
          <div class="status" :class="{ open: shopInfo.isOpen }">
            {{ shopInfo.isOpen ? '营业中' : '休息中' }}
          </div>
        </div>
      </div>
      <t-switch v-model="shopInfo.isOpen" @change="onStatusChange" />
    </div>

    <!-- 今日数据 -->
    <div class="today-stats">
      <div class="stat-item">
        <div class="value">{{ todayStats.orderCount }}</div>
        <div class="label">今日订单</div>
      </div>
      <div class="stat-item">
        <div class="value">¥{{ todayStats.income }}</div>
        <div class="label">今日收入</div>
      </div>
      <div class="stat-item">
        <div class="value">{{ todayStats.techOnline }}</div>
        <div class="label">在线技师</div>
      </div>
      <div class="stat-item">
        <div class="value">{{ todayStats.rating }}</div>
        <div class="label">店铺评分</div>
      </div>
    </div>

    <!-- 快捷操作 -->
    <div class="quick-actions">
      <div class="action-item" @click="goTo('/merchant/order/list')">
        <div class="icon"><FileIcon size="24px" /></div>
        <span>订单管理</span>
      </div>
      <div class="action-item" @click="goTo('/merchant/tech/list')">
        <div class="icon"><UserIcon size="24px" /></div>
        <span>技师管理</span>
      </div>
      <div class="action-item" @click="goTo('/merchant/tech/borrow')">
        <div class="icon"><SwapIcon size="24px" /></div>
        <span>借调技师</span>
      </div>
      <div class="action-item" @click="goTo('/merchant/service/list')">
        <div class="icon"><ServiceIcon size="24px" /></div>
        <span>服务项目</span>
      </div>
    </div>

    <!-- 待处理事项 -->
    <div class="section">
      <div class="section-header">
        <span class="title">待处理</span>
      </div>
      <div class="todo-list">
        <div class="todo-item" @click="goTo('/merchant/order/list')">
          <span class="label">待确认订单</span>
          <span class="count">{{ todoStats.pendingOrders }}</span>
        </div>
        <div class="todo-item" @click="goTo('/merchant/job/list')">
          <span class="label">待处理申请</span>
          <span class="count">{{ todoStats.pendingApply }}</span>
        </div>
        <div class="todo-item" @click="goTo('/merchant/income')">
          <span class="label">待结算金额</span>
          <span class="count">¥{{ todoStats.pendingSettle }}</span>
        </div>
      </div>
    </div>

    <!-- 近期订单 -->
    <div class="section">
      <div class="section-header">
        <span class="title">近期订单</span>
        <span class="more" @click="goTo('/merchant/order/list')">查看全部 ></span>
      </div>
      <div class="order-list">
        <div class="order-item" v-for="order in recentOrders" :key="order.id">
          <div class="order-info">
            <div class="service">{{ order.serviceName }}</div>
            <div class="meta">{{ order.techName }} · {{ order.time }}</div>
          </div>
          <div class="order-right">
            <div class="price">¥{{ order.price }}</div>
            <div class="status" :class="order.statusClass">{{ order.statusText }}</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { Avatar as TAvatar, Switch as TSwitch } from 'tdesign-mobile-vue'
import { FileIcon, UserIcon, SwapIcon, SettingIcon } from 'tdesign-icons-vue-next'

const ServiceIcon = SettingIcon
const router = useRouter()

const shopInfo = ref({
  logo: '',
  name: '悦享养生馆',
  isOpen: true
})

const todayStats = ref({
  orderCount: 12,
  income: 2580,
  techOnline: 5,
  rating: 4.8
})

const todoStats = ref({
  pendingOrders: 3,
  pendingApply: 2,
  pendingSettle: 1280
})

const recentOrders = ref([
  { id: 1, serviceName: '全身推拿 60分钟', techName: '张师傅', time: '14:00', price: 198, statusText: '进行中', statusClass: 'serving' },
  { id: 2, serviceName: '肩颈按摩 45分钟', techName: '李师傅', time: '15:30', price: 158, statusText: '待服务', statusClass: 'pending' },
  { id: 3, serviceName: '足底按摩 60分钟', techName: '王师傅', time: '11:00', price: 168, statusText: '已完成', statusClass: 'completed' }
])

const onStatusChange = (value) => {
  console.log('店铺状态:', value)
}

const goTo = (path) => {
  router.push(path)
}
</script>

<style lang="scss" scoped>
$primary: #07c160;

.merchant-home { padding-bottom: 20px; }

.shop-header {
  background: linear-gradient(135deg, $primary 0%, #10b981 100%);
  padding: 20px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.shop-info {
  display: flex;
  align-items: center;
  gap: 12px;
  color: #fff;
  
  .info {
    .name { font-size: 18px; font-weight: 600; margin-bottom: 4px; }
    .status {
      font-size: 12px;
      padding: 2px 8px;
      background: rgba(255,255,255,0.2);
      border-radius: 10px;
      display: inline-block;
      &.open { background: #fff; color: $primary; }
    }
  }
}

.today-stats {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  background: #fff;
  margin: -10px 15px 15px;
  border-radius: 12px;
  padding: 20px 10px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.08);
  
  .stat-item {
    text-align: center;
    .value { font-size: 20px; font-weight: 600; color: #1a1a1a; }
    .label { font-size: 12px; color: #999; margin-top: 4px; }
  }
}

.quick-actions {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  background: #fff;
  margin: 0 15px 15px;
  border-radius: 12px;
  padding: 20px 10px;
  
  .action-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    
    .icon {
      width: 48px;
      height: 48px;
      background: #f5f5f5;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: $primary;
    }
    span { font-size: 12px; color: #666; }
  }
}

.section {
  background: #fff;
  margin: 0 15px 15px;
  border-radius: 12px;
  padding: 15px;
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 15px;
  .title { font-size: 16px; font-weight: 600; color: #1a1a1a; }
  .more { font-size: 13px; color: #999; }
}

.todo-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.todo-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px;
  background: #f9f9f9;
  border-radius: 8px;
  
  .label { font-size: 14px; color: #666; }
  .count { font-size: 16px; font-weight: 600; color: #f44336; }
}

.order-list { display: flex; flex-direction: column; gap: 12px; }

.order-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 0;
  border-bottom: 1px solid #f5f5f5;
  &:last-child { border-bottom: none; }
  
  .order-info {
    .service { font-size: 15px; color: #1a1a1a; margin-bottom: 4px; }
    .meta { font-size: 12px; color: #999; }
  }
  
  .order-right {
    text-align: right;
    .price { font-size: 16px; font-weight: 600; color: #1a1a1a; margin-bottom: 4px; }
    .status {
      font-size: 12px;
      &.pending { color: #f57c00; }
      &.serving { color: #1976d2; }
      &.completed { color: $primary; }
    }
  }
}
</style>
