<template>
  <div class="home-page">
    <!-- 顶部区域 -->
    <div class="header-area">
      <div class="header-top">
        <div class="location">
          <t-icon name="location" size="14px" />
          <span>杭州</span>
          <t-icon name="chevron-down" size="12px" />
        </div>
        <div class="header-actions">
          <t-icon name="notification" size="22px" color="#333" />
        </div>
      </div>
      <div class="search-box" @click="router.push('/user/service/list')">
        <t-icon name="search" size="16px" color="#bbb" />
        <span>搜索服务项目</span>
      </div>
    </div>

    <!-- Banner -->
    <div class="banner-wrap">
      <t-swiper
        class="banner"
        :autoplay="true"
        :duration="4000"
        :navigation="{ type: 'dots' }"
      >
        <t-swiper-item v-for="(item, i) in banners" :key="i">
          <div class="banner-slide" :style="{ backgroundImage: `url(${item.img})` }">
            <div class="banner-overlay"></div>
            <div class="banner-content">
              <p class="banner-label">{{ item.label }}</p>
              <h2 class="banner-title">{{ item.title }}</h2>
              <p class="banner-desc">{{ item.desc }}</p>
            </div>
          </div>
        </t-swiper-item>
      </t-swiper>
    </div>

    <!-- 服务分类 -->
    <div class="category-section">
      <div class="category-list">
        <div 
          class="category-item" 
          v-for="cat in categories" 
          :key="cat.id"
          @click="goToService(cat.id)"
        >
          <div class="category-icon" :style="{ background: cat.bg }">
            <Icon :icon="cat.icon" width="20" :style="{ color: cat.color }" />
          </div>
          <span class="category-name">{{ cat.name }}</span>
        </div>
      </div>
    </div>

    <!-- 精选推荐 -->
    <div class="section">
      <div class="section-head">
        <h3>精选推荐</h3>
        <span class="see-all" @click="router.push('/user/service/list')">查看全部</span>
      </div>
      <div class="recommend-list">
        <div 
          class="recommend-card"
          v-for="item in recommends"
          :key="item.id"
          @click="router.push(`/user/service/detail/${item.id}`)"
        >
          <div class="card-cover">
            <img :src="item.img" :alt="item.name" />
          </div>
          <div class="card-info">
            <div class="card-name">{{ item.name }}</div>
            <div class="card-meta">{{ item.duration }}min · {{ item.sold }}人已约</div>
            <div class="card-price">
              <span class="price-num">¥{{ item.price }}</span>
              <span class="price-unit">起</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- 优选技师 -->
    <div class="section">
      <div class="section-head">
        <h3>优选技师</h3>
        <span class="see-all" @click="router.push('/user/technician/list')">查看全部</span>
      </div>
      <div class="tech-list">
        <div 
          class="tech-item"
          v-for="tech in techs"
          :key="tech.id"
          @click="router.push(`/user/technician/detail/${tech.id}`)"
        >
          <div class="tech-avatar">
            <img :src="tech.avatar" :alt="tech.name" />
            <span class="status-dot" v-if="tech.online"></span>
          </div>
          <div class="tech-content">
            <div class="tech-row">
              <span class="tech-name">{{ tech.name }}</span>
              <span class="tech-badge" v-if="tech.badge">{{ tech.badge }}</span>
            </div>
            <div class="tech-stats">
              <span><t-icon name="star-filled" size="11px" color="#f59e0b" /> {{ tech.rating }}</span>
              <span>{{ tech.orders }}单</span>
              <span>{{ tech.exp }}年经验</span>
            </div>
          </div>
          <t-icon name="chevron-right" size="16px" color="#ddd" />
        </div>
      </div>
    </div>

    <!-- 底部保障 -->
    <div class="footer-guarantee">
      <div class="guarantee-item">
        <t-icon name="secured" size="16px" color="#07c160" />
        <span>资质认证</span>
      </div>
      <div class="guarantee-item">
        <t-icon name="check-circle" size="16px" color="#07c160" />
        <span>服务保障</span>
      </div>
      <div class="guarantee-item">
        <t-icon name="money-circle" size="16px" color="#07c160" />
        <span>退款无忧</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()

const banners = ref([
  { label: '品质生活', title: '轻养到家', desc: '专业技师 · 上门服务', img: './images/banner-massage-1.jpg' },
  { label: '限时特惠', title: '新客立减30', desc: '首单专享优惠', img: './images/banner-massage-2.jpg' },
  { label: '身心放松', title: '专业SPA', desc: '让疲惫一扫而空', img: './images/banner-wellness.jpg' }
])

const categories = ref([
  { id: 1, name: '推拿', icon: 'mdi:hand-back-right', bg: '#f0f5ff', color: '#4f6ef7' },
  { id: 2, name: '足疗', icon: 'mdi:foot-print', bg: '#fff0f6', color: '#eb4d9c' },
  { id: 3, name: 'SPA', icon: 'mdi:spa', bg: '#e6fffb', color: '#13c2c2' },
  { id: 4, name: '理疗', icon: 'mdi:heart-pulse', bg: '#fff7e6', color: '#fa8c16' },
  { id: 5, name: '更多', icon: 'mdi:dots-horizontal', bg: '#f5f5f5', color: '#999' }
])

const recommends = ref([
  { id: 1, name: '全身推拿', duration: 60, price: 298, sold: '2.3k', img: './images/service-massage.jpg' },
  { id: 2, name: '肩颈舒缓', duration: 45, price: 198, sold: '1.8k', img: './images/service-neck.jpg' },
  { id: 3, name: '足底按摩', duration: 60, price: 168, sold: '1.5k', img: './images/service-foot.jpg' },
  { id: 4, name: '精油SPA', duration: 90, price: 458, sold: '986', img: './images/service-spa.jpg' }
])

const techs = ref([
  { id: 1, name: '张师傅', rating: 4.9, orders: 328, exp: 5, online: true, badge: '金牌', avatar: './images/avatar-tech-1.jpg' },
  { id: 2, name: '李师傅', rating: 4.8, orders: 256, exp: 3, online: true, avatar: './images/avatar-tech-2.jpg' },
  { id: 3, name: '王师傅', rating: 4.9, orders: 412, exp: 6, online: false, badge: '金牌', avatar: './images/avatar-tech-3.jpg' }
])

const goToService = (id) => {
  router.push({ path: '/user/service/list', query: { categoryId: id } })
}
</script>

<style lang="scss" scoped>
.home-page {
  background: #fafafa;
  min-height: 100vh;
  padding-bottom: 100px;
}

/* 顶部 */
.header-area {
  background: #fff;
  padding: 12px 20px 16px;
}

.header-top {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 14px;
}

.location {
  display: flex;
  align-items: center;
  gap: 4px;
  font-size: 14px;
  color: #333;
  font-weight: 500;
}

.search-box {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 12px 16px;
  background: #f7f7f7;
  border-radius: 24px;
  font-size: 14px;
  color: #bbb;
}

/* Banner */
.banner-wrap {
  padding: 0 20px 16px;
}

.banner {
  border-radius: 12px;
  overflow: hidden;
  height: 120px;
}

.banner-slide {
  height: 120px;
  padding: 20px;
  display: flex;
  align-items: center;
  background-size: cover;
  background-position: center;
  position: relative;
}

.banner-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(90deg, rgba(0,0,0,0.6) 0%, rgba(0,0,0,0.2) 100%);
}

.banner-content {
  color: #fff;
  position: relative;
  z-index: 1;
}

.banner-label {
  font-size: 10px;
  opacity: 0.6;
  margin-bottom: 4px;
  letter-spacing: 1px;
}

.banner-title {
  font-size: 20px;
  font-weight: 600;
  margin-bottom: 4px;
}

.banner-desc {
  font-size: 12px;
  opacity: 0.75;
}

/* 分类 */
.category-section {
  padding: 0 20px 16px;
}

.category-list {
  display: flex;
  justify-content: space-between;
  background: #fff;
  padding: 16px 12px;
  border-radius: 12px;
}

.category-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
}

.category-icon {
  width: 40px;
  height: 40px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.category-name {
  font-size: 11px;
  color: #666;
}

/* Section */
.section {
  padding: 0 20px;
  margin-bottom: 20px;
}

.section-head {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 12px;
  
  h3 {
    font-size: 16px;
    font-weight: 600;
    color: #1a1a1a;
  }
  
  .see-all {
    font-size: 12px;
    color: #999;
  }
}

/* 推荐列表 */
.recommend-list {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 12px;
}

.recommend-card {
  background: #fff;
  border-radius: 10px;
  overflow: hidden;
}

.card-cover {
  height: 80px;
  overflow: hidden;
  
  img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }
}

.card-info {
  padding: 10px 12px 12px;
}

.card-name {
  font-size: 14px;
  font-weight: 500;
  color: #1a1a1a;
  margin-bottom: 4px;
}

.card-meta {
  font-size: 11px;
  color: #999;
  margin-bottom: 8px;
}

.card-price {
  .price-num {
    font-size: 16px;
    font-weight: 600;
    color: #ff6b35;
  }
  .price-unit {
    font-size: 11px;
    color: #999;
    margin-left: 2px;
  }
}

/* 技师列表 */
.tech-list {
  background: #fff;
  border-radius: 12px;
  overflow: hidden;
}

.tech-item {
  display: flex;
  align-items: center;
  padding: 14px;
  border-bottom: 1px solid #f5f5f5;
  
  &:last-child {
    border-bottom: none;
  }
}

.tech-avatar {
  width: 44px;
  height: 44px;
  border-radius: 50%;
  background: #f5f5f5;
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
  margin-right: 12px;
  overflow: hidden;
  
  img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }
}

.status-dot {
  position: absolute;
  bottom: 2px;
  right: 2px;
  width: 8px;
  height: 8px;
  background: #22c55e;
  border: 2px solid #fff;
  border-radius: 50%;
}

.tech-content {
  flex: 1;
}

.tech-row {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 6px;
}

.tech-name {
  font-size: 15px;
  font-weight: 500;
  color: #1a1a1a;
}

.tech-badge {
  font-size: 10px;
  padding: 2px 6px;
  background: linear-gradient(135deg, #fbbf24, #f59e0b);
  color: #fff;
  border-radius: 4px;
}

.tech-stats {
  display: flex;
  gap: 12px;
  font-size: 12px;
  color: #999;
  
  span {
    display: flex;
    align-items: center;
    gap: 2px;
  }
}

/* 底部保障 */
.footer-guarantee {
  display: flex;
  justify-content: center;
  gap: 32px;
  padding: 24px 20px;
}

.guarantee-item {
  display: flex;
  align-items: center;
  gap: 4px;
  font-size: 12px;
  color: #666;
}
</style>
