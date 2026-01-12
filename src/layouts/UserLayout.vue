<template>
  <div class="user-layout">
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
  router.push(routeMap[value])
}
</script>

<style lang="scss" scoped>
.user-layout {
  min-height: 100vh;
  background: #f5f5f5;
  padding-bottom: 50px;
}
</style>
