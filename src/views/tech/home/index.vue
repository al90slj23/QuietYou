<template>
  <div class="tech-home">
    <!-- 顶部状态栏 -->
    <div class="status-bar">
      <div class="user-info">
        <t-avatar :image="techInfo.avatar" size="48px" />
        <div class="info">
          <div class="name">{{ techInfo.name }}</div>
          <div class="badge" :class="{ certified: techInfo.isCertified }">
            {{ techInfo.isCertified ? '已认证' : '未认证' }}
          </div>
        </div>
      </div>
      <div class="status-switch">
        <span>{{ statusText }}</span>
        <t-switch v-model="isOnline" @change="onStatusChange" />
      </div>
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
        <div class="value">{{ todayStats.serviceHours }}h</div>
        <div class="label">服务时长</div>
      </div>
      <div class="stat-item">
        <div class="value">{{ todayStats.rating }}</div>
        <div class="label">今日评分</div>
      </div>
    </div>

    <!-- 快捷操作 -->
    <div class="quick-actions">
      <div class="action-item" @click="goTo('/tech/order/list')">
        <div class="icon"><FileIcon size="24px" /></div>
        <span>全部订单</span>
      </div>
      <div class="action-item" @click="goTo('/tech/service/list')">
        <div class="icon"><ServiceIcon size="24px" /></div>
        <span>服务项目</span>
      </div>
      <div class="action-item" @click="goTo('/tech/setting/accept')">
        <div class="icon"><SettingIcon size="24px" /></div>
        <span>接单设置</span>
      </div>
      <div class="action-item" @click="goTo('/tech/review/list')">
        <div class="icon"><StarIcon size="24px" /></div>
        <span>我的评价</span>
      </div>
    </div>

    <!-- 待处理订单 -->
    <div class="section">
      <div class="section-header">
        <span class="title">待处理订单</span>
        <span class="more" @click="goTo('/tech/order/list')">查看全部 ></span>
      </div>
      <div class="order-list" v-if="pendingOrders.length">
        <div class="order-card" v-for="order in pendingOrders" :key="order.id" @click="goTo(`/tech/order/detail/${order.id}`)">
          <div class="order-header">
            <span class="order-no">{{ order.orderNo }}</span>
            <span class="status" :class="order.statusClass">{{ order.statusText }}</span>
          </div>
          <div class="order-content">
            <div class="service-name">{{ order.serviceName }}</div>
            <div class="customer-info">
              <UserIcon size="14px" />
              <span>{{ order.customerName }}</span>
              <span class="time">{{ order.scheduledTime }}</span>
            </div>
            <div class="address">
              <LocationIcon size="14px" />
              <span>{{ order.address }}</span>
            </div>
          </div>
          <div class="order-footer">
            <span class="price">¥{{ order.price }}</span>
            <t-button size="small" theme="primary" v-if="order.status === 1">接单</t-button>
            <t-button size="small" theme="primary" v-else-if="order.status === 2">开始服务</t-button>
          </div>
        </div>
      </div>
      <div class="empty" v-else>
        <InfoCircleIcon size="48px" />
        <p>暂无待处理订单</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { Avatar as TAvatar, Switch as TSwitch, Button as TButton } from 'tdesign-mobile-vue'
import { FileIcon, ServiceIcon, SettingIcon, StarIcon, UserIcon, LocationIcon, InfoCircleIcon } from 'tdesign-icons-vue-next'

const router = useRouter()

// 技师信息（模拟数据）
const techInfo = ref({
  avatar: '',
  name: '张师傅',
  isCertified: true
})

// 在线状态
const isOnline = ref(true)
const statusText = computed(() => isOnline.value ? '接单中' : '休息中')

const onStatusChange = (value) => {
  // TODO: 调用API更新状态
  console.log('状态切换:', value)
}

// 今日统计
const todayStats = ref({
  orderCount: 3,
  income: 580,
  serviceHours: 4.5,
  rating: 4.9
})

// 待处理订单（模拟数据）
const pendingOrders = ref([
  {
    id: 1,
    orderNo: 'QY202601130001',
    serviceName: '全身推拿 60分钟',
    customerName: '李女士',
    scheduledTime: '今天 14:00',
    address: '成都市武侯区天府大道100号',
    price: 198,
    status: 1,
    statusText: '待接单',
    statusClass: 'pending'
  },
  {
    id: 2,
    orderNo: 'QY202601130002',
    serviceName: '肩颈按摩 45分钟',
    customerName: '王先生',
    scheduledTime: '今天 16:30',
    address: '成都市高新区软件园B区',
    price: 158,
    status: 2,
    statusText: '已接单',
    statusClass: 'accepted'
  }
])

const goTo = (path) => {
  router.push(path)
}
</script>

<style lang="scss" scoped>
$primary: #07c160;

.tech-home {
  padding-bottom: 20px;
}

.status-bar {
  background: linear-gradient(135deg, $primary 0%, #10b981 100%);
  padding: 20px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  color: #fff;
}

.user-info {
  display: flex;
  align-items: center;
  gap: 12px;
  
  .info {
    .name {
      font-size: 18px;
      font-weight: 600;
      margin-bottom: 4px;
    }
    .badge {
      display: inline-block;
      padding: 2px 8px;
      background: rgba(255,255,255,0.2);
      border-radius: 10px;
      font-size: 11px;
      
      &.certified {
        background: #fff;
        color: $primary;
      }
    }
  }
}

.status-switch {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 14px;
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
    
    .value {
      font-size: 20px;
      font-weight: 600;
      color: #1a1a1a;
    }
    .label {
      font-size: 12px;
      color: #999;
      margin-top: 4px;
    }
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
    
    span {
      font-size: 12px;
      color: #666;
    }
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
  
  .title {
    font-size: 16px;
    font-weight: 600;
    color: #1a1a1a;
  }
  
  .more {
    font-size: 13px;
    color: #999;
  }
}

.order-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.order-card {
  background: #f9f9f9;
  border-radius: 10px;
  padding: 12px;
}

.order-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 10px;
  
  .order-no {
    font-size: 12px;
    color: #999;
  }
  
  .status {
    font-size: 12px;
    padding: 2px 8px;
    border-radius: 4px;
    
    &.pending { background: #fff3e0; color: #f57c00; }
    &.accepted { background: #e3f2fd; color: #1976d2; }
  }
}

.order-content {
  .service-name {
    font-size: 15px;
    font-weight: 500;
    color: #1a1a1a;
    margin-bottom: 8px;
  }
  
  .customer-info, .address {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    color: #666;
    margin-bottom: 4px;
    
    .time {
      margin-left: auto;
      color: $primary;
    }
  }
}

.order-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 12px;
  padding-top: 12px;
  border-top: 1px solid #eee;
  
  .price {
    font-size: 18px;
    font-weight: 600;
    color: #f44336;
  }
}

.empty {
  text-align: center;
  padding: 40px 0;
  color: #999;
  
  p {
    margin-top: 10px;
    font-size: 14px;
  }
}
</style>
