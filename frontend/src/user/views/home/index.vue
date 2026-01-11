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
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { Icon } from '@iconify/vue'

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
