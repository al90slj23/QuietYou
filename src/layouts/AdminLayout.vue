<template>
  <div class="admin-layout">
    <!-- 侧边栏 -->
    <aside class="sidebar" :class="{ collapsed: isCollapsed }">
      <div class="logo">
        <img src="/favicon.svg" alt="轻养到家" />
        <span v-if="!isCollapsed">轻养到家</span>
      </div>
      <t-menu
        :value="activeMenu"
        :collapsed="isCollapsed"
        theme="dark"
        @change="handleMenuChange"
      >
        <t-menu-item value="dashboard">
          <template #icon><DashboardIcon /></template>
          工作台
        </t-menu-item>
        <t-submenu value="user" title="用户管理">
          <template #icon><UserIcon /></template>
          <t-menu-item value="user-list">用户列表</t-menu-item>
        </t-submenu>
        <t-submenu value="tech" title="技师管理">
          <template #icon><UserBusinessIcon /></template>
          <t-menu-item value="tech-list">技师列表</t-menu-item>
          <t-menu-item value="tech-certification">认证审核</t-menu-item>
        </t-submenu>
        <t-submenu value="merchant" title="商户管理">
          <template #icon><ShopIcon /></template>
          <t-menu-item value="merchant-list">商户列表</t-menu-item>
        </t-submenu>
        <t-submenu value="order" title="订单管理">
          <template #icon><FileIcon /></template>
          <t-menu-item value="order-list">订单列表</t-menu-item>
          <t-menu-item value="order-refund">退款处理</t-menu-item>
        </t-submenu>
        <t-submenu value="service" title="服务管理">
          <template #icon><LayersIcon /></template>
          <t-menu-item value="service-category">服务分类</t-menu-item>
          <t-menu-item value="service-list">服务项目</t-menu-item>
        </t-submenu>
        <t-submenu value="finance" title="财务管理">
          <template #icon><WalletIcon /></template>
          <t-menu-item value="finance-income">收入统计</t-menu-item>
          <t-menu-item value="finance-withdraw">提现审核</t-menu-item>
        </t-submenu>
        <t-submenu value="content" title="内容管理">
          <template #icon><EditIcon /></template>
          <t-menu-item value="content-banner">轮播图</t-menu-item>
          <t-menu-item value="content-news">资讯管理</t-menu-item>
        </t-submenu>
        <t-submenu value="system" title="系统设置">
          <template #icon><SettingIcon /></template>
          <t-menu-item value="system-config">基础配置</t-menu-item>
          <t-menu-item value="system-admin">管理员</t-menu-item>
        </t-submenu>
      </t-menu>
    </aside>

    <!-- 主内容区 -->
    <div class="main-container">
      <!-- 顶部栏 -->
      <header class="header">
        <div class="header-left">
          <ViewListIcon 
            class="collapse-btn" 
            @click="isCollapsed = !isCollapsed" 
          />
          <t-breadcrumb>
            <t-breadcrumb-item>首页</t-breadcrumb-item>
            <t-breadcrumb-item v-if="currentBreadcrumb">{{ currentBreadcrumb }}</t-breadcrumb-item>
          </t-breadcrumb>
        </div>
        <div class="header-right">
          <t-dropdown :options="userOptions" @click="handleUserAction">
            <t-button variant="text">
              <template #icon><UserCircleIcon /></template>
              管理员
              <ChevronDownIcon />
            </t-button>
          </t-dropdown>
        </div>
      </header>

      <!-- 页面内容 -->
      <main class="content">
        <router-view />
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { 
  Menu as TMenu, MenuItem as TMenuItem, Submenu as TSubmenu,
  Breadcrumb as TBreadcrumb, BreadcrumbItem as TBreadcrumbItem,
  Dropdown as TDropdown, Button as TButton
} from 'tdesign-vue-next'
import {
  DashboardIcon, UserIcon, UserBusinessIcon, ShopIcon, FileIcon,
  LayersIcon, WalletIcon, EditIcon, SettingIcon, ViewListIcon,
  UserCircleIcon, ChevronDownIcon
} from 'tdesign-icons-vue-next'

const router = useRouter()
const route = useRoute()
const isCollapsed = ref(false)

const menuMap = {
  'dashboard': { path: '/admin', title: '工作台' },
  'user-list': { path: '/admin/user/list', title: '用户列表' },
  'tech-list': { path: '/admin/tech/list', title: '技师列表' },
  'tech-certification': { path: '/admin/tech/certification', title: '认证审核' },
  'merchant-list': { path: '/admin/merchant/list', title: '商户列表' },
  'order-list': { path: '/admin/order/list', title: '订单列表' },
  'order-refund': { path: '/admin/order/refund', title: '退款处理' },
  'service-category': { path: '/admin/service/category', title: '服务分类' },
  'service-list': { path: '/admin/service/list', title: '服务项目' },
  'finance-income': { path: '/admin/finance/income', title: '收入统计' },
  'finance-withdraw': { path: '/admin/finance/withdraw', title: '提现审核' },
  'content-banner': { path: '/admin/content/banner', title: '轮播图' },
  'content-news': { path: '/admin/content/news', title: '资讯管理' },
  'system-config': { path: '/admin/system/config', title: '基础配置' },
  'system-admin': { path: '/admin/system/admin', title: '管理员' }
}

const activeMenu = computed(() => {
  const path = route.path
  for (const [key, value] of Object.entries(menuMap)) {
    if (value.path === path) return key
  }
  return 'dashboard'
})

const currentBreadcrumb = computed(() => {
  return menuMap[activeMenu.value]?.title || ''
})

const handleMenuChange = (value) => {
  const target = menuMap[value]
  if (target) router.push(target.path)
}

const userOptions = [
  { content: '个人设置', value: 'profile' },
  { content: '退出登录', value: 'logout' }
]

const handleUserAction = (data) => {
  if (data.value === 'logout') {
    router.push('/home')
  }
}
</script>

<style lang="scss" scoped>
$primary: #07c160;
$sidebar-width: 220px;
$sidebar-collapsed-width: 64px;
$header-height: 56px;

.admin-layout {
  display: flex;
  min-height: 100vh;
  background: #f0f2f5;
}

.sidebar {
  width: $sidebar-width;
  background: #001529;
  transition: width 0.2s;
  flex-shrink: 0;
  
  &.collapsed {
    width: $sidebar-collapsed-width;
    .logo span { display: none; }
  }
}

.logo {
  height: 56px;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
  
  img { width: 32px; height: 32px; }
  span { color: #fff; font-size: 16px; font-weight: 600; white-space: nowrap; }
}

.main-container {
  flex: 1;
  display: flex;
  flex-direction: column;
  min-width: 0;
}

.header {
  height: $header-height;
  background: #fff;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 24px;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.08);
}

.header-left {
  display: flex;
  align-items: center;
  gap: 16px;
}

.collapse-btn {
  font-size: 20px;
  cursor: pointer;
  color: #666;
  &:hover { color: $primary; }
}

.header-right {
  display: flex;
  align-items: center;
  gap: 16px;
}

.content {
  flex: 1;
  padding: 24px;
  overflow: auto;
}
</style>
