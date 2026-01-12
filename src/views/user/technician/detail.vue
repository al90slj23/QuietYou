<template>
  <div class="technician-detail-page">
    <!-- 技师头部 -->
    <div class="tech-header">
      <t-icon name="chevron-left" size="24px" color="#fff" class="back-btn" @click="router.back()" />
      <div class="tech-avatar-section">
        <div class="tech-avatar">
          <t-icon name="user" size="40px" />
        </div>
        <div class="tech-status" :class="getStatusClass(tech.status)">{{ tech.statusText }}</div>
      </div>
      <div class="tech-basic-info">
        <div class="tech-name-row">
          <span class="tech-name">{{ tech.name }}</span>
          <span class="tech-gender">{{ tech.gender === 1 ? '♂' : '♀' }}</span>
        </div>
        <div class="tech-shop">{{ tech.shopName }}</div>
        <div class="tech-stats">
          <div class="stat-item">
            <span class="stat-value">{{ tech.rating }}</span>
            <span class="stat-label">评分</span>
          </div>
          <div class="stat-divider"></div>
          <div class="stat-item">
            <span class="stat-value">{{ tech.orderCount }}</span>
            <span class="stat-label">服务</span>
          </div>
          <div class="stat-divider"></div>
          <div class="stat-item">
            <span class="stat-value">{{ tech.years }}年</span>
            <span class="stat-label">经验</span>
          </div>
        </div>
      </div>
    </div>

    <!-- 照片集 -->
    <div class="card">
      <div class="card-title">照片集</div>
      <div class="photo-gallery">
        <div v-for="(photo, index) in tech.photos" :key="index" class="photo-item">
          <t-icon :name="photo" size="48px" />
        </div>
      </div>
    </div>

    <!-- 个人简介 -->
    <div class="card">
      <div class="card-title">个人简介</div>
      <div class="tech-intro">{{ tech.intro }}</div>
    </div>

    <!-- 服务项目 -->
    <div class="card">
      <div class="card-title">服务项目</div>
      <div class="service-items">
        <div
          v-for="service in tech.services"
          :key="service.id"
          class="service-item"
          @click="goToBook(service.id)"
        >
          <div class="service-item-info">
            <div class="service-item-name">{{ service.name }}</div>
            <div class="service-item-duration">{{ service.duration }}分钟</div>
          </div>
          <div class="price">{{ service.price }}</div>
        </div>
      </div>
    </div>

    <!-- 用户评价 -->
    <div class="card">
      <div class="card-title flex-between">
        <span>用户评价 ({{ reviews.length }})</span>
        <span class="good-rate">好评率 {{ goodRate }}%</span>
      </div>
      <div class="review-list">
        <div v-for="review in reviews" :key="review.id" class="review-item">
          <div class="review-header">
            <div class="review-avatar">
              <t-icon name="user" size="18px" />
            </div>
            <div class="review-user-info">
              <div class="review-user-name">{{ review.userName }}</div>
              <div class="review-time">{{ review.createdAt }}</div>
            </div>
            <div class="review-rating">
              <t-icon v-for="i in review.rating" :key="i" name="star-filled" size="12px" />
            </div>
          </div>
          <div class="review-content">{{ review.content }}</div>
          <div class="review-service">服务项目：{{ review.serviceName }}</div>
        </div>
      </div>
      <t-empty v-if="reviews.length === 0" description="暂无评价" />
    </div>

    <!-- 底部操作栏 -->
    <div class="bottom-bar">
      <div class="price-info">
        <span class="price-label">起步价</span>
        <span class="price-value">¥{{ tech.price }}</span>
      </div>
      <t-button theme="primary" size="large" @click="goToBook()">立即预约</t-button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'

const router = useRouter()
const route = useRoute()

const tech = ref({
  id: 1,
  name: '张师傅',
  gender: 1,
  shopName: '康乐养生馆',
  rating: 4.9,
  orderCount: 328,
  years: 5,
  price: 198,
  status: 1,
  statusText: '在线',
  intro: '从事按摩推拿行业5年，擅长中式推拿、肩颈调理。曾在多家知名养生馆工作，积累了丰富的实践经验。手法专业，力度适中，深受顾客好评。',
  photos: ['gesture-press', 'spa', 'heart'],
  services: [
    { id: 1, name: '全身中式推拿', duration: 60, price: 298 },
    { id: 2, name: '肩颈深度调理', duration: 45, price: 198 },
    { id: 3, name: '背部推拿放松', duration: 45, price: 218 }
  ]
})

const reviews = ref([
  { id: 1, userName: '用户***8', rating: 5, content: '张师傅手法很专业，力度刚刚好，做完整个人都轻松了很多。', serviceName: '全身中式推拿', createdAt: '2026-01-10' },
  { id: 2, userName: '用户***2', rating: 5, content: '肩颈调理效果很好，困扰我很久的肩膀酸痛缓解了不少。', serviceName: '肩颈深度调理', createdAt: '2026-01-09' },
  { id: 3, userName: '用户***5', rating: 4, content: '服务态度很好，准时到达，整体体验不错。', serviceName: '背部推拿放松', createdAt: '2026-01-08' }
])

const goodRate = computed(() => {
  const good = reviews.value.filter(r => r.rating >= 4).length
  return reviews.value.length > 0 ? Math.round(good / reviews.value.length * 100) : 100
})

const getStatusClass = (status) => {
  return { 1: 'online', 2: 'busy', 0: 'offline' }[status] || 'offline'
}

const goToBook = (serviceId) => {
  const query = { techId: tech.value.id }
  if (serviceId) query.serviceId = serviceId
  router.push({ path: '/user/order/create', query })
}
</script>

<style lang="scss" scoped>
.technician-detail-page {
  background: #f5f5f5;
  min-height: 100vh;
  padding-bottom: 80px;
}

.tech-header {
  background: linear-gradient(135deg, #07c160 0%, #10b981 100%);
  padding: 20px 16px;
  display: flex;
  align-items: center;
  position: relative;
}

.back-btn {
  position: absolute;
  top: 16px;
  left: 16px;
}

.tech-avatar-section {
  position: relative;
  margin-right: 16px;
  margin-left: 24px;
}

.tech-avatar {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  background: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 3px solid rgba(255, 255, 255, 0.3);
}

.tech-status {
  position: absolute;
  bottom: -4px;
  left: 50%;
  transform: translateX(-50%);
  padding: 2px 8px;
  border-radius: 10px;
  font-size: 10px;
  color: #fff;
  white-space: nowrap;
  
  &.online { background: #07c160; }
  &.busy { background: #f59e0b; }
  &.offline { background: #999; }
}

.tech-basic-info {
  flex: 1;
  color: #fff;
}

.tech-name-row {
  display: flex;
  align-items: center;
  margin-bottom: 4px;
}

.tech-name {
  font-size: 20px;
  font-weight: 600;
  margin-right: 8px;
}

.tech-gender {
  font-size: 14px;
  padding: 2px 8px;
  border-radius: 4px;
  background: rgba(255, 255, 255, 0.2);
}

.tech-shop {
  font-size: 13px;
  opacity: 0.9;
  margin-bottom: 12px;
}

.tech-stats {
  display: flex;
  align-items: center;
}

.stat-item {
  text-align: center;
}

.stat-value {
  display: block;
  font-size: 18px;
  font-weight: 600;
}

.stat-label {
  font-size: 11px;
  opacity: 0.8;
}

.stat-divider {
  width: 1px;
  height: 24px;
  background: rgba(255, 255, 255, 0.3);
  margin: 0 20px;
}

.photo-gallery {
  display: flex;
  gap: 8px;
  overflow-x: auto;
  
  &::-webkit-scrollbar { display: none; }
}

.photo-item {
  flex-shrink: 0;
  width: 100px;
  height: 100px;
  border-radius: 8px;
  background: linear-gradient(135deg, #e8f8ef 0%, #d1fae5 100%);
  display: flex;
  align-items: center;
  justify-content: center;
}

.tech-intro {
  font-size: 14px;
  color: #666;
  line-height: 1.8;
}

.service-items {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.service-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 12px;
  background: #f5f5f5;
  border-radius: 8px;
}

.service-item-name {
  font-size: 15px;
  font-weight: 500;
  margin-bottom: 4px;
}

.service-item-duration {
  font-size: 12px;
  color: #999;
}

.flex-between {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.good-rate {
  font-size: 12px;
  color: #999;
  font-weight: normal;
}

.review-list {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.review-item {
  padding-bottom: 16px;
  border-bottom: 1px solid #e5e5e5;
  
  &:last-child {
    padding-bottom: 0;
    border-bottom: none;
  }
}

.review-header {
  display: flex;
  align-items: center;
  margin-bottom: 8px;
}

.review-avatar {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  background: #f5f5f5;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 10px;
}

.review-user-info {
  flex: 1;
}

.review-user-name {
  font-size: 14px;
}

.review-time {
  font-size: 12px;
  color: #999;
}

.review-rating {
  color: #f59e0b;
}

.review-content {
  font-size: 14px;
  color: #666;
  line-height: 1.6;
  margin-bottom: 8px;
}

.review-service {
  font-size: 12px;
  color: #999;
}

.bottom-bar {
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  height: 60px;
  background: #fff;
  border-top: 1px solid #e5e5e5;
  display: flex;
  align-items: center;
  padding: 0 16px;
}

.price-info {
  flex: 1;
}

.price-label {
  font-size: 12px;
  color: #999;
  display: block;
}

.price-value {
  font-size: 20px;
  font-weight: 600;
  color: #ef4444;
}
</style>
