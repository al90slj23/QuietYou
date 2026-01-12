<template>
  <div class="tech-mobile-app">
    <router-view v-slot="{ Component }">
      <keep-alive>
        <component :is="Component" />
      </keep-alive>
    </router-view>
    
    <!-- 底部导航栏 -->
    <t-tab-bar v-model="activeTab" @change="onTabChange" v-if="showTabBar" class="tab-bar">
      <t-tab-bar-item value="home">
        <template #icon><HomeIcon size="22px" /></template>
        工作台
      </t-tab-bar-item>
      <t-tab-bar-item value="order">
        <template #icon><FileIcon size="22px" /></template>
        订单
      </t-tab-bar-item>
      <t-tab-bar-item value="income">
        <template #icon><WalletIcon size="22px" /></template>
        收入
      </t-tab-bar-item>
      <t-tab-bar-item value="profile">
        <template #icon><UserIcon size="22px" /></template>
        我的
      </t-tab-bar-item>
    </t-tab-bar>
    
    <!-- 登录弹窗 -->
    <LoginDialog 
      v-model="showLogin" 
      type="tech"
      @success="onLoginSuccess"
    />
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, provide } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { TabBar as TTabBar, TabBarItem as TTabBarItem } from 'tdesign-mobile-vue'
import { HomeIcon, FileIcon, WalletIcon, UserIcon } from 'tdesign-icons-vue-next'
import LoginDialog from '@/components/common/LoginDialog.vue'

const router = useRouter()
const route = useRoute()

const activeTab = ref('home')
const showLogin = ref(false)
const isLoggedIn = ref(false)
const techInfo = ref(null)

// 检查登录状态
const checkLogin = () => {
  const token = localStorage.getItem('tech_token')
  isLoggedIn.value = !!token
  if (token) {
    techInfo.value = {
      token,
      phone: localStorage.getItem('tech_phone')
    }
  }
  return isLoggedIn.value
}

// 技师端所有页面都需要登录
onMounted(() => {
  if (!checkLogin()) {
    showLogin.value = true
  }
})

const showTabBar = computed(() => route.meta.showTabBar === true)

const routeToTab = {
  'tech-home': 'home',
  'tech-order-list': 'order',
  'tech-income': 'income',
  'tech-profile': 'profile'
}

watch(() => route.name, (name) => {
  if (routeToTab[name]) {
    activeTab.value = routeToTab[name]
  }
}, { immediate: true })

const onTabChange = (value) => {
  if (!checkLogin()) {
    showLogin.value = true
    return
  }
  
  const tabToRoute = {
    home: '/tech/home',
    order: '/tech/order/list',
    income: '/tech/income',
    profile: '/tech/profile'
  }
  router.push(tabToRoute[value])
}

const onLoginSuccess = (data) => {
  isLoggedIn.value = true
  techInfo.value = data
}

// 提供给子组件
provide('techAuth', {
  isLoggedIn,
  techInfo,
  showLogin: () => { showLogin.value = true },
  checkLogin,
  logout: () => {
    localStorage.removeItem('tech_token')
    localStorage.removeItem('tech_phone')
    isLoggedIn.value = false
    techInfo.value = null
    showLogin.value = true
  }
})
</script>

<style lang="scss" scoped>
.tech-mobile-app {
  min-height: 100vh;
  background: #f5f5f5;
  padding-bottom: 50px;
}

.tab-bar {
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  z-index: 1000;
  background: #fff;
  box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.05);
}
</style>
