<template>
  <div class="merchant-mobile-app">
    <router-view />
    
    <!-- 底部导航 -->
    <t-tab-bar v-model="activeTab" @change="onTabChange" v-if="showTabBar">
      <t-tab-bar-item value="home">
        <template #icon><HomeIcon size="22px" /></template>
        工作台
      </t-tab-bar-item>
      <t-tab-bar-item value="order">
        <template #icon><FileIcon size="22px" /></template>
        订单
      </t-tab-bar-item>
      <t-tab-bar-item value="tech">
        <template #icon><UserIcon size="22px" /></template>
        技师
      </t-tab-bar-item>
      <t-tab-bar-item value="profile">
        <template #icon><ShopIcon size="22px" /></template>
        我的
      </t-tab-bar-item>
    </t-tab-bar>
    
    <!-- 登录弹窗 -->
    <LoginDialog 
      v-model="showLogin" 
      type="merchant"
      @success="onLoginSuccess"
    />
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, provide } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { TabBar as TTabBar, TabBarItem as TTabBarItem } from 'tdesign-mobile-vue'
import { HomeIcon, FileIcon, UserIcon, ShopIcon } from 'tdesign-icons-vue-next'
import LoginDialog from '@/components/common/LoginDialog.vue'

const route = useRoute()
const router = useRouter()

const activeTab = ref('home')
const showLogin = ref(false)
const isLoggedIn = ref(false)
const merchantInfo = ref(null)

// 检查登录状态
const checkLogin = () => {
  const token = localStorage.getItem('merchant_token')
  isLoggedIn.value = !!token
  if (token) {
    merchantInfo.value = {
      token,
      phone: localStorage.getItem('merchant_phone')
    }
  }
  return isLoggedIn.value
}

// 商户端所有页面都需要登录
onMounted(() => {
  if (!checkLogin()) {
    showLogin.value = true
  }
})

const showTabBar = computed(() => route.meta.showTabBar)

const tabRouteMap = {
  home: '/merchant/home',
  order: '/merchant/order/list',
  tech: '/merchant/tech/list',
  profile: '/merchant/profile'
}

const routeTabMap = {
  '/merchant/home': 'home',
  '/merchant/order/list': 'order',
  '/merchant/tech/list': 'tech',
  '/merchant/profile': 'profile'
}

watch(() => route.path, (path) => {
  if (routeTabMap[path]) activeTab.value = routeTabMap[path]
}, { immediate: true })

const onTabChange = (value) => {
  if (!checkLogin()) {
    showLogin.value = true
    return
  }
  router.push(tabRouteMap[value])
}

const onLoginSuccess = (data) => {
  isLoggedIn.value = true
  merchantInfo.value = data
}

// 提供给子组件
provide('merchantAuth', {
  isLoggedIn,
  merchantInfo,
  showLogin: () => { showLogin.value = true },
  checkLogin,
  logout: () => {
    localStorage.removeItem('merchant_token')
    localStorage.removeItem('merchant_phone')
    isLoggedIn.value = false
    merchantInfo.value = null
    showLogin.value = true
  }
})
</script>

<style lang="scss" scoped>
.merchant-mobile-app {
  min-height: 100vh;
  background: #f5f5f5;
  padding-bottom: 60px;
}

:deep(.t-tab-bar) {
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  z-index: 1000;
  background: #fff;
  box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.05);
}
</style>
