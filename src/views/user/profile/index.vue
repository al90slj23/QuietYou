<template>
  <div class="profile-page">
    <!-- 用户信息 -->
    <div class="user-header" @click="goEdit">
      <t-avatar :image="user.avatar" size="64px" />
      <div class="user-info">
        <div class="user-name">{{ user.name || '未登录' }}</div>
        <div class="user-phone">{{ user.phone || '点击登录' }}</div>
      </div>
      <ChevronRightIcon size="20px" class="arrow" />
    </div>

    <!-- 订单入口 -->
    <div class="section">
      <div class="section-header">
        <span class="title">我的订单</span>
        <span class="more" @click="goTo('/user/order/list')">全部订单 ></span>
      </div>
      <div class="order-grid">
        <div class="order-item" @click="goTo('/user/order/list?status=pending')">
          <div class="icon-wrap">
            <WalletIcon size="24px" />
            <span class="badge" v-if="orderCounts.pending">{{ orderCounts.pending }}</span>
          </div>
          <span>待支付</span>
        </div>
        <div class="order-item" @click="goTo('/user/order/list?status=confirmed')">
          <div class="icon-wrap">
            <TimeIcon size="24px" />
            <span class="badge" v-if="orderCounts.confirmed">{{ orderCounts.confirmed }}</span>
          </div>
          <span>待服务</span>
        </div>
        <div class="order-item" @click="goTo('/user/order/list?status=completed')">
          <div class="icon-wrap">
            <ChatIcon size="24px" />
            <span class="badge" v-if="orderCounts.toReview">{{ orderCounts.toReview }}</span>
          </div>
          <span>待评价</span>
        </div>
        <div class="order-item" @click="goTo('/user/order/list?status=refund')">
          <div class="icon-wrap">
            <ServiceIcon size="24px" />
          </div>
          <span>售后</span>
        </div>
      </div>
    </div>

    <!-- 功能菜单 -->
    <div class="section menu-section">
      <div class="menu-item" @click="goTo('/user/favorite/list')">
        <div class="left"><HeartIcon size="20px" /><span>我的收藏</span></div>
        <ChevronRightIcon size="20px" class="arrow" />
      </div>
      <div class="menu-item" @click="goTo('/user/coupon/list')">
        <div class="left"><DiscountIcon size="20px" /><span>优惠券</span></div>
        <div class="right">
          <span class="badge-text" v-if="couponCount">{{ couponCount }}张可用</span>
          <ChevronRightIcon size="20px" class="arrow" />
        </div>
      </div>
      <div class="menu-item" @click="goTo('/user/address/list')">
        <div class="left"><LocationIcon size="20px" /><span>地址管理</span></div>
        <ChevronRightIcon size="20px" class="arrow" />
      </div>
      <div class="menu-item" @click="goTo('/user/message/list')">
        <div class="left"><NotificationIcon size="20px" /><span>消息通知</span></div>
        <div class="right">
          <span class="badge-dot" v-if="unreadCount"></span>
          <ChevronRightIcon size="20px" class="arrow" />
        </div>
      </div>
    </div>

    <div class="section menu-section">
      <div class="menu-item" @click="goTo('/user/shop/list')">
        <div class="left"><ShopIcon size="20px" /><span>附近店铺</span></div>
        <ChevronRightIcon size="20px" class="arrow" />
      </div>
      <div class="menu-item" @click="showHelp">
        <div class="left"><HelpCircleIcon size="20px" /><span>帮助中心</span></div>
        <ChevronRightIcon size="20px" class="arrow" />
      </div>
      <div class="menu-item" @click="showAbout">
        <div class="left"><InfoCircleIcon size="20px" /><span>关于我们</span></div>
        <ChevronRightIcon size="20px" class="arrow" />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { Avatar as TAvatar, Toast } from 'tdesign-mobile-vue'
import { 
  ChevronRightIcon, WalletIcon, TimeIcon, ChatIcon, ServiceIcon,
  HeartIcon, DiscountIcon, LocationIcon, NotificationIcon,
  ShopIcon, HelpCircleIcon, InfoCircleIcon
} from 'tdesign-icons-vue-next'

const router = useRouter()

const user = ref({
  avatar: '',
  name: '张先生',
  phone: '138****8888'
})

const orderCounts = ref({
  pending: 1,
  confirmed: 1,
  toReview: 2
})

const couponCount = ref(3)
const unreadCount = ref(2)

const goTo = (path) => router.push(path)
const goEdit = () => router.push('/user/profile/edit')
const showHelp = () => Toast({ message: '帮助中心开发中', theme: 'warning' })
const showAbout = () => router.push('/home/about')
</script>

<style lang="scss" scoped>
$primary: #07c160;

.profile-page { min-height: 100vh; background: #f5f5f5; padding-bottom: 30px; }

.user-header {
  background: linear-gradient(135deg, $primary 0%, #10b981 100%);
  padding: 30px 20px;
  display: flex;
  align-items: center;
  gap: 15px;
  color: #fff;
  
  .user-info {
    flex: 1;
    .user-name { font-size: 20px; font-weight: 600; margin-bottom: 4px; }
    .user-phone { font-size: 14px; opacity: 0.9; }
  }
  
  .arrow { opacity: 0.8; }
}

.section {
  background: #fff;
  margin: 15px;
  border-radius: 12px;
  padding: 15px;
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 15px;
  .title { font-size: 16px; font-weight: 600; color: #1a1a1a; }
  .more { font-size: 12px; color: #999; }
}

.order-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 10px;
}

.order-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
  cursor: pointer;
  
  .icon-wrap {
    position: relative;
    width: 44px;
    height: 44px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #666;
  }
  
  .badge {
    position: absolute;
    top: -4px;
    right: -4px;
    min-width: 16px;
    height: 16px;
    padding: 0 4px;
    font-size: 10px;
    line-height: 16px;
    text-align: center;
    color: #fff;
    background: #f44336;
    border-radius: 8px;
  }
  
  span:not(.badge) { font-size: 12px; color: #666; }
}

.menu-section { padding: 5px 15px; }

.menu-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 15px 0;
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
  
  .badge-text { font-size: 12px; color: #f44336; }
  
  .badge-dot {
    width: 8px;
    height: 8px;
    background: #f44336;
    border-radius: 50%;
  }
}
</style>
