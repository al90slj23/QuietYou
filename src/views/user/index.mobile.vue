<template>
  <div class="user-mobile-app">
    <router-view v-slot="{ Component }">
      <keep-alive>
        <component :is="Component" />
      </keep-alive>
    </router-view>
    
    <!-- 底部导航栏 -->
    <t-tab-bar v-model="activeTab" @change="onTabChange" v-if="showTabBar">
      <t-tab-bar-item value="home">
        <template #icon><HomeIcon size="22px" /></template>
        首页
      </t-tab-bar-item>
      <t-tab-bar-item value="service">
        <template #icon><ServiceIcon size="22px" /></template>
        服务
      </t-tab-bar-item>
      <t-tab-bar-item value="order">
        <template #icon><FileIcon size="22px" /></template>
        订单
      </t-tab-bar-item>
      <t-tab-bar-item value="profile">
        <template #icon><UserIcon size="22px" /></template>
        我的
      </t-tab-bar-item>
    </t-tab-bar>
    
    <!-- 登录弹窗 -->
    <LoginDialog 
      v-model="showLogin" 
      type="user"
      @success="onLoginSuccess"
    />
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, provide } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { TabBar as TTabBar, TabBarItem as TTabBarItem } from 'tdesign-mobile-vue'
import { HomeIcon, ServiceIcon, FileIcon, UserIcon } from 'tdesign-icons-vue-next'
import LoginDialog from '@/components/common/LoginDialog.vue'

const router = useRouter()
const route = useRoute()

const activeTab = ref('home')
const showLogin = ref(false)
const isLoggedIn = ref(false)
const userInfo = ref(null)

// 检查登录状态
const checkLogin = () => {
  const token = localStorage.getItem('user_token')
  isLoggedIn.value = !!token
  if (token) {
    userInfo.value = {
      token,
      phone: localStorage.getItem('user_phone')
    }
  }
  return isLoggedIn.value
}

// 需要登录的页面
const needLoginPages = ['user-order-list', 'user-order-detail', 'user-profile']

// 检查是否需要登录
const checkNeedLogin = () => {
  if (needLoginPages.includes(route.name) && !checkLogin()) {
    showLogin.value = true
  }
}

onMounted(() => {
  checkLogin()
  checkNeedLogin()
})

watch(() => route.name, () => {
  checkNeedLogin()
})

const showTabBar = computed(() => {
  return route.meta.showTabBar === true
})

watch(() => route.name, (name) => {
  if (name === 'user-home') activeTab.value = 'home'
  else if (name === 'user-service-list') activeTab.value = 'service'
  else if (name === 'user-order-list') activeTab.value = 'order'
  else if (name === 'user-profile') activeTab.value = 'profile'
}, { immediate: true })

const onTabChange = (value) => {
  const routeMap = {
    home: '/user/home',
    service: '/user/service/list',
    order: '/user/order/list',
    profile: '/user/profile'
  }
  
  // 订单和我的需要登录
  if (['order', 'profile'].includes(value) && !checkLogin()) {
    showLogin.value = true
    return
  }
  
  router.push(routeMap[value])
}

const onLoginSuccess = (data) => {
  isLoggedIn.value = true
  userInfo.value = data
}

// 提供给子组件
provide('userAuth', {
  isLoggedIn,
  userInfo,
  showLogin: () => { showLogin.value = true },
  checkLogin,
  logout: () => {
    localStorage.removeItem('user_token')
    localStorage.removeItem('user_phone')
    isLoggedIn.value = false
    userInfo.value = null
  }
})
</script>

<style lang="scss" scoped>
.user-mobile-app {
  min-height: 100vh;
  background: #f5f5f5;
  padding-bottom: 50px;
}

:deep(.t-tab-bar) {
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  z-index: 100;
  background: #fff;
  box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.05);
}
</style>
