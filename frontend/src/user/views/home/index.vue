<template>
  <div class="home-page">
    <!-- Banner 轮播 -->
    <t-swiper
      class="banner-swiper"
      :autoplay="true"
      :duration="4000"
      :navigation="{ type: 'dots' }"
    >
      <t-swiper-item v-for="(banner, index) in banners" :key="index">
        <div class="banner-item" :style="{ background: banner.bg }">
          <div class="banner-content">
            <h3>{{ banner.title }}</h3>
            <p>{{ banner.desc }}</p>
          </div>
        </div>
      </t-swiper-item>
    </t-swiper>

    <!-- 服务分类 -->
    <div class="card">
      <div class="card-title">服务分类</div>
      <t-grid :column="4" :border="false">
        <t-grid-item
          v-for="cat in categories"
          :key="cat.id"
          :text="cat.name"
          @click="goToService(cat.id)"
        >
          <template #image>
            <div class="category-icon" :style="{ background: cat.color }">
              <Icon :icon="cat.icon" width="24" height="24" />
            </div>
          </template>
        </t-grid-item>
      </t-grid>
    </div>

    <!-- 热门服务 -->
    <div class="card">
      <div class="card-title flex-between">
        <span>热门服务</span>
        <span class="more" @click="router.push('/service/list')">查看全部 &gt;</span>
      </div>
      <div class="service-list">
        <div
          v-for="service in hotServices"
          :key="service.id"
          class="service-item"
          @click="router.push(`/service/detail/${service.id}`)"
        >
          <div class="service-image">
            <Icon :icon="service.icon" width="32" height="32" />
          </div>
          <div class="service-info">
            <div class="service-name">{{ service.name }}</div>
            <div class="service-desc">{{ service.desc }}</div>
            <div class="service-meta">
              <span class="service-duration">{{ service.duration }}分钟</span>
              <span class="price">{{ service.price }}<small>起</small></span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- 推荐技师 -->
    <div class="card">
      <div class="card-title flex-between">
        <span>推荐技师</span>
        <span class="more" @click="router.push('/technician/list')">查看全部 &gt;</span>
      </div>
      <div class="tech-list">
        <div
          v-for="tech in recommendTechs"
          :key="tech.id"
          class="tech-card"
          @click="router.push(`/technician/detail/${tech.id}`)"
        >
          <div class="tech-avatar">
            <t-icon name="user" size="28px" />
          </div>
          <div class="tech-name">{{ tech.name }}</div>
          <div class="tech-rating">
            <t-icon name="star-filled" size="12px" /> {{ tech.rating }}
          </div>
          <div class="tech-orders">已服务{{ tech.orders }}单</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { Icon, addIcon } from '@iconify/vue'

// 手动定义需要的图标（避免加载整个图标集）
const icons = {
  'mdi:hand-back-right': {
    body: '<path fill="currentColor" d="M3 16v-3c0-1.1.9-2 2-2h1V5c0-1.1.9-2 2-2s2 .9 2 2v6h1V4c0-1.1.9-2 2-2s2 .9 2 2v7h1V6c0-1.1.9-2 2-2s2 .9 2 2v5h1V9c0-1.1.9-2 2-2s2 .9 2 2v10c0 2.21-1.79 4-4 4H8c-2.21 0-4-1.79-4-4v-3H3z"/>',
    width: 24, height: 24
  },
  'mdi:spa': {
    body: '<path fill="currentColor" d="M15.49 9.63c-.18-2.79-1.31-5.51-3.43-7.63a12.188 12.188 0 0 0-3.55 7.63c1.28.68 2.46 1.56 3.49 2.63c1.03-1.06 2.21-1.94 3.49-2.63zm-6.5 2.65c-.14-.1-.3-.19-.45-.29a9.15 9.15 0 0 0-.64 2.91c.33.17.64.35.93.56c.29-.21.6-.39.93-.56c-.07-.97-.23-1.94-.52-2.86c-.08.08-.17.16-.25.24zM12 15.45C9.85 12.17 6.18 10 2 10c0 5.32 3.36 9.82 8.03 11.49c.63.23 1.29.4 1.97.51c.68-.12 1.34-.29 1.97-.51C18.64 19.82 22 15.32 22 10c-4.18 0-7.85 2.17-10 5.45z"/>',
    width: 24, height: 24
  },
  'mdi:foot-print': {
    body: '<path fill="currentColor" d="M12 2C9.24 2 7 4.24 7 7c0 2.85 2.92 7.21 5 9.88c2.11-2.69 5-7 5-9.88c0-2.76-2.24-5-5-5zm0 7.5a2.5 2.5 0 0 1 0-5a2.5 2.5 0 0 1 0 5zM5 20.5c0 1.38 1.12 2.5 2.5 2.5S10 21.88 10 20.5S8.88 18 7.5 18S5 19.12 5 20.5zm9 0c0 1.38 1.12 2.5 2.5 2.5s2.5-1.12 2.5-2.5s-1.12-2.5-2.5-2.5s-2.5 1.12-2.5 2.5z"/>',
    width: 24, height: 24
  },
  'mdi:flower-tulip': {
    body: '<path fill="currentColor" d="M3 13c0-4.97 4.03-9 9-9c0 4.97-4.03 9-9 9zm9 9c0-4.97 4.03-9 9-9c0 4.97-4.03 9-9 9zm0-18c0 4.97-4.03 9-9 9c4.97 0 9 4.03 9 9c0-4.97 4.03-9 9-9c-4.97 0-9-4.03-9-9z"/>',
    width: 24, height: 24
  },
  'mdi:human-handsup': {
    body: '<path fill="currentColor" d="M5 1v8H2v2h3v13h2V11h2V9H7V1H5zm16 8h-3V1h-2v8h-2v2h2v13h2V11h3V9zM12 1c-1.1 0-2 .9-2 2s.9 2 2 2s2-.9 2-2s-.9-2-2-2zm1 5h-2c-1.1 0-2 .9-2 2v6h2v9h2v-9h2V8c0-1.1-.9-2-2-2z"/>',
    width: 24, height: 24
  },
  'mdi:leaf': {
    body: '<path fill="currentColor" d="M17 8C8 10 5.9 16.17 3.82 21.34l1.89.66l.95-2.3c.48.17.98.3 1.34.3C19 20 22 3 22 3c-1 2-8 2.25-13 3.25S2 11.5 2 13.5s1.75 3.75 1.75 3.75C7 8 17 8 17 8z"/>',
    width: 24, height: 24
  },
  'mdi:pot-outline': {
    body: '<path fill="currentColor" d="M19 19c0 1.1-.9 2-2 2H7c-1.1 0-2-.9-2-2v-7h14v7zM4 10v2h16v-2c0-1.1-.9-2-2-2h-1V6h-2v2h-2V6h-2v2H9V6H7v2H6c-1.1 0-2 .9-2 2zm17 2H3v-2c0-1.66 1.34-3 3-3h1V5h4v2h2V5h4v2h1c1.66 0 3 1.34 3 3v2zm-2 7c0 .55-.45 1-1 1H6c-.55 0-1-.45-1-1v-6h14v6z"/>',
    width: 24, height: 24
  },
  'mdi:dots-horizontal': {
    body: '<path fill="currentColor" d="M16 12a2 2 0 0 1 2-2a2 2 0 0 1 2 2a2 2 0 0 1-2 2a2 2 0 0 1-2-2m-6 0a2 2 0 0 1 2-2a2 2 0 0 1 2 2a2 2 0 0 1-2 2a2 2 0 0 1-2-2m-6 0a2 2 0 0 1 2-2a2 2 0 0 1 2 2a2 2 0 0 1-2 2a2 2 0 0 1-2-2z"/>',
    width: 24, height: 24
  }
}

// 注册图标
onMounted(() => {
  Object.entries(icons).forEach(([name, data]) => {
    addIcon(name, data)
  })
})

const router = useRouter()

// Banner 数据
const banners = ref([
  { title: '轻养调理，随叫随松', desc: '专业技师上门服务', bg: 'linear-gradient(135deg, #07c160 0%, #10b981 100%)' },
  { title: '新用户专享', desc: '首单立减30元', bg: 'linear-gradient(135deg, #576b95 0%, #6366f1 100%)' },
  { title: '品质保障', desc: '正规门店技师，安心服务', bg: 'linear-gradient(135deg, #f59e0b 0%, #f97316 100%)' }
])

// 服务分类
const categories = ref([
  { id: 1, name: '中式推拿', icon: 'mdi:hand-back-right', color: '#e8f8ef' },
  { id: 2, name: '泰式按摩', icon: 'mdi:spa', color: '#fef3c7' },
  { id: 3, name: '足疗保健', icon: 'mdi:foot-print', color: '#dbeafe' },
  { id: 4, name: '精油SPA', icon: 'mdi:flower-tulip', color: '#fce7f3' },
  { id: 5, name: '肩颈调理', icon: 'mdi:human-handsup', color: '#e0e7ff' },
  { id: 6, name: '艾灸理疗', icon: 'mdi:leaf', color: '#fee2e2' },
  { id: 7, name: '刮痧拔罐', icon: 'mdi:pot-outline', color: '#d1fae5' },
  { id: 8, name: '更多服务', icon: 'mdi:dots-horizontal', color: '#f3f4f6' }
])

// 热门服务
const hotServices = ref([
  { id: 1, name: '全身中式推拿', desc: '传统手法，疏通经络，缓解疲劳', duration: 60, price: 298, icon: 'mdi:hand-back-right' },
  { id: 2, name: '肩颈深度调理', desc: '针对久坐人群，专业手法放松肩颈', duration: 45, price: 198, icon: 'mdi:human-handsup' },
  { id: 3, name: '足底反射疗法', desc: '刺激足底穴位，促进血液循环', duration: 60, price: 168, icon: 'mdi:foot-print' }
])

// 推荐技师
const recommendTechs = ref([
  { id: 1, name: '张师傅', rating: 4.9, orders: 328 },
  { id: 2, name: '李师傅', rating: 4.8, orders: 256 },
  { id: 3, name: '王师傅', rating: 4.9, orders: 412 },
  { id: 4, name: '陈师傅', rating: 4.7, orders: 189 },
  { id: 5, name: '刘师傅', rating: 4.8, orders: 275 }
])

const goToService = (categoryId) => {
  router.push({ path: '/service/list', query: { categoryId } })
}
</script>

<style lang="scss" scoped>
.home-page {
  padding-bottom: 20px;
}

.banner-swiper {
  margin: 12px;
  border-radius: 8px;
  overflow: hidden;
}

.banner-item {
  height: 140px;
  display: flex;
  align-items: center;
  padding: 20px;
}

.banner-content {
  color: #fff;
  
  h3 {
    font-size: 20px;
    font-weight: 600;
    margin-bottom: 8px;
  }
  
  p {
    font-size: 14px;
    opacity: 0.9;
  }
}

.category-icon {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.flex-between {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.more {
  font-size: 12px;
  color: #999;
  font-weight: normal;
}

.service-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.service-item {
  display: flex;
  padding: 12px;
  background: #f5f5f5;
  border-radius: 8px;
}

.service-image {
  width: 80px;
  height: 80px;
  border-radius: 8px;
  background: linear-gradient(135deg, #e8f8ef 0%, #d1fae5 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.service-info {
  flex: 1;
  margin-left: 12px;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

.service-name {
  font-size: 15px;
  font-weight: 500;
}

.service-desc {
  font-size: 12px;
  color: #666;
}

.service-meta {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.service-duration {
  font-size: 12px;
  color: #999;
}

.tech-list {
  display: flex;
  gap: 12px;
  overflow-x: auto;
  padding-bottom: 4px;
  
  &::-webkit-scrollbar {
    display: none;
  }
}

.tech-card {
  flex-shrink: 0;
  width: 100px;
  padding: 12px;
  background: #f5f5f5;
  border-radius: 8px;
  text-align: center;
}

.tech-avatar {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  background: linear-gradient(135deg, #e8f8ef 0%, #d1fae5 100%);
  margin: 0 auto 8px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.tech-name {
  font-size: 14px;
  font-weight: 500;
  margin-bottom: 4px;
}

.tech-rating {
  font-size: 12px;
  color: #f59e0b;
}

.tech-orders {
  font-size: 11px;
  color: #999;
  margin-top: 4px;
}
</style>
