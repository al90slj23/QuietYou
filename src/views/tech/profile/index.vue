<template>
  <div class="profile-page">
    <!-- 用户信息卡片 -->
    <div class="profile-card">
      <div class="avatar-section">
        <t-avatar :image="userInfo.avatar" size="72px" />
        <div class="badge" :class="{ certified: userInfo.isCertified }">
          {{ userInfo.isCertified ? '已认证' : '未认证' }}
        </div>
      </div>
      <div class="info-section">
        <div class="name">{{ userInfo.name }}</div>
        <div class="type">{{ userInfo.type }}</div>
        <div class="shop" v-if="userInfo.shopName">
          <ShopIcon size="14px" />
          <span>{{ userInfo.shopName }}</span>
        </div>
      </div>
      <div class="edit-btn" @click="goEdit">
        <EditIcon size="18px" />
      </div>
    </div>

    <!-- 数据统计 -->
    <div class="stats-card">
      <div class="stat-item">
        <div class="value">{{ stats.totalOrders }}</div>
        <div class="label">累计订单</div>
      </div>
      <div class="stat-item">
        <div class="value">{{ stats.rating }}</div>
        <div class="label">综合评分</div>
      </div>
      <div class="stat-item">
        <div class="value">{{ stats.repeatRate }}%</div>
        <div class="label">回头率</div>
      </div>
      <div class="stat-item">
        <div class="value">{{ stats.serviceHours }}h</div>
        <div class="label">服务时长</div>
      </div>
    </div>

    <!-- 功能菜单 -->
    <div class="menu-section">
      <div class="menu-item" @click="goTo('/tech/service/list')">
        <div class="left">
          <ServiceIcon size="20px" />
          <span>服务项目</span>
        </div>
        <ChevronRightIcon size="20px" class="arrow" />
      </div>
      <div class="menu-item" @click="goTo('/tech/review/list')">
        <div class="left">
          <StarIcon size="20px" />
          <span>我的评价</span>
        </div>
        <div class="right">
          <span class="badge-num">{{ stats.reviewCount }}</span>
          <ChevronRightIcon size="20px" class="arrow" />
        </div>
      </div>
      <div class="menu-item" @click="goTo('/tech/setting/accept')">
        <div class="left">
          <SettingIcon size="20px" />
          <span>接单设置</span>
        </div>
        <ChevronRightIcon size="20px" class="arrow" />
      </div>
      <div class="menu-item" @click="goTo('/tech/job/list')">
        <div class="left">
          <WorkIcon size="20px" />
          <span>店铺招聘</span>
        </div>
        <div class="right">
          <span class="badge-dot"></span>
          <ChevronRightIcon size="20px" class="arrow" />
        </div>
      </div>
    </div>

    <div class="menu-section">
      <div class="menu-item" @click="goTo('/tech/certification')">
        <div class="left">
          <CertificateIcon size="20px" />
          <span>认证管理</span>
        </div>
        <div class="right">
          <span class="status" :class="{ success: userInfo.isCertified }">
            {{ userInfo.isCertified ? '已认证' : '去认证' }}
          </span>
          <ChevronRightIcon size="20px" class="arrow" />
        </div>
      </div>
      <div class="menu-item" @click="showHelp">
        <div class="left">
          <HelpIcon size="20px" />
          <span>帮助中心</span>
        </div>
        <ChevronRightIcon size="20px" class="arrow" />
      </div>
      <div class="menu-item" @click="showAbout">
        <div class="left">
          <InfoIcon size="20px" />
          <span>关于我们</span>
        </div>
        <ChevronRightIcon size="20px" class="arrow" />
      </div>
    </div>

    <!-- 退出登录 -->
    <div class="logout-section">
      <t-button block variant="outline" @click="logout">退出登录</t-button>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { Avatar as TAvatar, Button as TButton, Toast } from 'tdesign-mobile-vue'
import { 
  EditIcon, ChevronRightIcon, StarIcon, SettingIcon,
  HelpCircleIcon, InfoCircleIcon, ShopIcon
} from 'tdesign-icons-vue-next'

// 图标别名
const ServiceIcon = SettingIcon
const WorkIcon = ShopIcon
const CertificateIcon = StarIcon
const HelpIcon = HelpCircleIcon
const InfoIcon = InfoCircleIcon

const router = useRouter()

const userInfo = ref({
  avatar: '',
  name: '张师傅',
  type: '店铺技师',
  shopName: '悦享养生馆',
  isCertified: true
})

const stats = ref({
  totalOrders: 328,
  rating: 4.9,
  repeatRate: 68,
  serviceHours: 1280,
  reviewCount: 128
})

const goTo = (path) => {
  router.push(path)
}

const goEdit = () => {
  router.push('/tech/profile/edit')
}

const showHelp = () => {
  Toast({ message: '帮助中心开发中', theme: 'warning' })
}

const showAbout = () => {
  Toast({ message: '关于我们开发中', theme: 'warning' })
}

const logout = () => {
  Toast({ message: '已退出登录', theme: 'success' })
  router.push('/')
}
</script>

<style lang="scss" scoped>
$primary: #07c160;

.profile-page {
  min-height: 100vh;
  background: #f5f5f5;
  padding-bottom: 30px;
}

.profile-card {
  background: linear-gradient(135deg, $primary 0%, #10b981 100%);
  padding: 30px 20px;
  display: flex;
  align-items: center;
  gap: 15px;
  position: relative;
}

.avatar-section {
  position: relative;
  
  .badge {
    position: absolute;
    bottom: -4px;
    left: 50%;
    transform: translateX(-50%);
    font-size: 10px;
    padding: 2px 8px;
    background: rgba(255,255,255,0.3);
    color: #fff;
    border-radius: 10px;
    white-space: nowrap;
    
    &.certified {
      background: #fff;
      color: $primary;
    }
  }
}

.info-section {
  flex: 1;
  color: #fff;
  
  .name {
    font-size: 20px;
    font-weight: 600;
    margin-bottom: 4px;
  }
  
  .type {
    font-size: 13px;
    opacity: 0.9;
    margin-bottom: 6px;
  }
  
  .shop {
    display: flex;
    align-items: center;
    gap: 4px;
    font-size: 12px;
    opacity: 0.8;
  }
}

.edit-btn {
  width: 36px;
  height: 36px;
  background: rgba(255,255,255,0.2);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #fff;
  cursor: pointer;
}

.stats-card {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  background: #fff;
  margin: -15px 15px 15px;
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

.menu-section {
  background: #fff;
  margin: 0 15px 15px;
  border-radius: 12px;
  overflow: hidden;
}

.menu-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 15px;
  border-bottom: 1px solid #f5f5f5;
  cursor: pointer;
  
  &:last-child { border-bottom: none; }
  
  .left {
    display: flex;
    align-items: center;
    gap: 12px;
    color: #1a1a1a;
    
    span { font-size: 15px; }
  }
  
  .right {
    display: flex;
    align-items: center;
    gap: 8px;
  }
  
  .arrow { color: #ccc; }
  
  .badge-num {
    font-size: 12px;
    color: #999;
  }
  
  .badge-dot {
    width: 8px;
    height: 8px;
    background: #f44336;
    border-radius: 50%;
  }
  
  .status {
    font-size: 13px;
    color: #f57c00;
    
    &.success { color: $primary; }
  }
}

.logout-section {
  padding: 0 15px;
  margin-top: 30px;
}
</style>
