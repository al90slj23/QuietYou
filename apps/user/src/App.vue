<template>
  <div class="app">
    <router-view v-slot="{ Component }">
      <keep-alive>
        <component :is="Component" />
      </keep-alive>
    </router-view>
    
    <!-- 底部导航栏 -->
    <t-tab-bar v-model="activeTab" @change="onTabChange" v-if="showTabBar">
      <t-tab-bar-item value="home">
        <template #icon><t-icon name="home" /></template>
        首页
      </t-tab-bar-item>
      <t-tab-bar-item value="service">
        <template #icon><t-icon name="service" /></template>
        服务
      </t-tab-bar-item>
      <t-tab-bar-item value="order">
        <template #icon><t-icon name="file-paste" /></template>
        订单
      </t-tab-bar-item>
      <t-tab-bar-item value="profile">
        <template #icon><t-icon name="user" /></template>
        我的
      </t-tab-bar-item>
    </t-tab-bar>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { useRouter, useRoute } from 'vue-router'

const router = useRouter()
const route = useRoute()

const activeTab = ref('home')

// 需要显示 TabBar 的页面
const tabBarRoutes = ['home', 'service-list', 'order-list', 'profile']

const showTabBar = computed(() => {
  return tabBarRoutes.includes(route.name)
})

// 监听路由变化，更新 activeTab
watch(() => route.name, (name) => {
  if (name === 'home') activeTab.value = 'home'
  else if (name === 'service-list') activeTab.value = 'service'
  else if (name === 'order-list') activeTab.value = 'order'
  else if (name === 'profile') activeTab.value = 'profile'
})

const onTabChange = (value) => {
  const routeMap = {
    home: '/home',
    service: '/service/list',
    order: '/order/list',
    profile: '/profile'
  }
  router.push(routeMap[value])
}
</script>

<style lang="scss">
.app {
  min-height: 100vh;
  background: #f5f5f5;
  padding-bottom: 50px;
}
</style>
