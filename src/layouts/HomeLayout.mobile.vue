<template>
  <div class="home-layout-mobile">
    <Header />
    <main class="main-content">
      <router-view />
    </main>
    
    <!-- 底部导航栏 -->
    <t-tab-bar v-model="activeTab" @change="onTabChange" class="tab-bar">
      <t-tab-bar-item value="home">
        <template #icon><HomeIcon /></template>
        首页
      </t-tab-bar-item>
      <t-tab-bar-item value="news">
        <template #icon><ArticleIcon /></template>
        资讯
      </t-tab-bar-item>
      <t-tab-bar-item value="download">
        <template #icon><DownloadIcon /></template>
        下载
      </t-tab-bar-item>
      <t-tab-bar-item value="about">
        <template #icon><InfoCircleIcon /></template>
        关于
      </t-tab-bar-item>
    </t-tab-bar>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { TabBar as TTabBar, TabBarItem as TTabBarItem } from 'tdesign-mobile-vue'
import { HomeIcon, ArticleIcon, DownloadIcon, InfoCircleIcon } from 'tdesign-icons-vue-next'
import Header from '../components/home/Header.vue'

const router = useRouter()
const route = useRoute()

const activeTab = ref('home')

// 路由名称到 tab 的映射
const routeToTab = {
  'home': 'home',
  'home-news': 'news',
  'home-download': 'download',
  'home-about': 'about'
}

watch(() => route.name, (name) => {
  if (routeToTab[name]) {
    activeTab.value = routeToTab[name]
  }
}, { immediate: true })

const onTabChange = (value) => {
  const tabToRoute = {
    home: '/home',
    news: '/home/news',
    download: '/home/download',
    about: '/home/about'
  }
  router.push(tabToRoute[value])
}
</script>

<style lang="scss" scoped>
.home-layout-mobile {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  padding-bottom: 50px; // TabBar 高度
}

.main-content {
  flex: 1;
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
