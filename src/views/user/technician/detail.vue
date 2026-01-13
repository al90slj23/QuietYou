<template>
  <div class="technician-detail-page">
    <!-- 顶部导航 -->
    <div class="nav-bar">
      <ChevronLeftIcon size="24px" class="back-btn" @click="router.back()" />
      <div class="nav-actions">
        <HeartIcon 
          :size="24" 
          :class="{ 'is-favorite': isFavorite }" 
          @click="toggleFavorite" 
        />
        <ShareIcon size="24px" @click="handleShare" />
      </div>
    </div>
    
    <!-- 技师头部 -->
    <div class="tech-header">
      <div class="tech-avatar-wrap">
        <div class="tech-avatar">
          <img v-if="tech.avatar" :src="tech.avatar" :alt="tech.name" />
          <UserIcon v-else size="40px" style="color: #bbb" />
        </div>
        <span class="status-badge" :class="tech.status">{{ statusText }}</span>
      </div>
      <div class="tech-info">
        <div class="tech-name-row">
          <span class="tech-name">{{ tech.name }}</span>
          <span class="tech-gender" :class="tech.gender === 1 ? 'male' : 'female'">
            {{ tech.gender === 1 ? '♂' : '♀' }}
          </span>
          <span class="tech-badge" v-if="tech.badge">{{ tech.badge }}</span>
        </div>
        <div class="tech-shop" @click="goToShop">
          <ShopIcon size="14px" />
          <span>{{ tech.shopName || '独立技师' }}</span>
          <ChevronRightIcon size="14px" v-if="tech.shopId" />
        </div>
        <div class="tech-tags">
          <span v-for="tag in tech.tags" :key="tag" class="tag">{{ tag }}</span>
        </div>
      </div>
    </div>
    
    <!-- 数据统计 -->
    <div class="stats-card">
      <div class="stat-item">
        <div class="stat-value">
          <StarFilledIcon size="16px" style="color: #f59e0b" />
          {{ tech.rating }}
        </div>
        <div class="stat-label">综合评分</div>
      </div>
      <div class="stat-divider"></div>
      <div class="stat-item">
        <div class="stat-value">{{ tech.orderCount }}</div>
        <div class="stat-label">服务单数</div>
      </div>
      <div class="stat-divider"></div>
      <div class="stat-item">
        <div class="stat-value">{{ tech.returnRate }}%</div>
        <div class="stat-label">回头率</div>
      </div>
      <div class="stat-divider"></div>
      <div class="stat-item">
        <div class="stat-value">{{ tech.years }}年</div>
        <div class="stat-label">从业经验</div>
      </div>
    </div>
    
    <!-- 多维度评分 -->
    <div class="card">
      <div class="card-header">
        <h3>服务评分</h3>
        <span class="sub-text">基于 {{ tech.reviewCount }} 条评价</span>
      </div>
      <div class="score-dimensions">
        <div class="score-item" v-for="dim in scoreDimensions" :key="dim.key">
          <span class="score-label">{{ dim.label }}</span>
          <div class="score-bar">
            <div class="score-fill" :style="{ width: (dim.score / 5 * 100) + '%' }"></div>
          </div>
          <span class="score-value">{{ dim.score.toFixed(1) }}</span>
        </div>
      </div>
    </div>
    
    <!-- 个人简介 -->
    <div class="card">
      <div class="card-header">
        <h3>个人简介</h3>
      </div>
      <div class="intro-content">{{ tech.intro }}</div>
      <div class="cert-list" v-if="tech.certifications?.length">
        <div class="cert-item" v-for="cert in tech.certifications" :key="cert">
          <CheckCircleIcon size="14px" style="color: #07c160" />
          <span>{{ cert }}</span>
        </div>
      </div>
    </div>
    
    <!-- 服务项目 -->
    <div class="card">
      <div class="card-header">
        <h3>服务项目</h3>
        <span class="sub-text">{{ tech.services?.length || 0 }}项</span>
      </div>
      <div class="service-list">
        <div 
          class="service-item" 
          v-for="service in tech.services" 
          :key="service.id"
          @click="selectService(service)"
        >
          <div class="service-main">
            <div class="service-name">{{ service.name }}</div>
            <div class="service-meta">
              <span>{{ service.duration }}分钟</span>
              <span v-if="service.sold">· {{ service.sold }}人已约</span>
            </div>
          </div>
          <div class="service-right">
            <div class="service-price">
              <em>¥</em>{{ service.price }}
            </div>
            <t-button size="small" variant="outline" theme="primary">预约</t-button>
          </div>
        </div>
      </div>
    </div>
    
    <!-- 用户评价 -->
    <div class="card">
      <div class="card-header">
        <h3>用户评价</h3>
        <span class="see-all" @click="showAllReviews = true">
          查看全部 {{ tech.reviewCount }} 条
          <ChevronRightIcon size="14px" />
        </span>
      </div>
      
      <!-- 评价标签筛选 -->
      <div class="review-tags">
        <span 
          class="review-tag" 
          :class="{ active: reviewFilter === '' }"
          @click="reviewFilter = ''"
        >全部</span>
        <span 
          class="review-tag" 
          :class="{ active: reviewFilter === 'good' }"
          @click="reviewFilter = 'good'"
        >好评 {{ reviewStats.good }}</span>
        <span 
          class="review-tag" 
          :class="{ active: reviewFilter === 'mid' }"
          @click="reviewFilter = 'mid'"
        >中评 {{ reviewStats.mid }}</span>
        <span 
          class="review-tag" 
          :class="{ active: reviewFilter === 'bad' }"
          @click="reviewFilter = 'bad'"
        >差评 {{ reviewStats.bad }}</span>
      </div>
      
      <!-- 评价列表 -->
      <div class="review-list">
        <div class="review-item" v-for="review in displayReviews" :key="review.id">
          <div class="review-header">
            <div class="review-avatar">
              <img v-if="review.avatar" :src="review.avatar" />
              <UserIcon v-else size="18px" style="color: #bbb" />
            </div>
            <div class="review-user">
              <div class="review-name">{{ review.userName }}</div>
              <div class="review-time">{{ review.createdAt }}</div>
            </div>
            <div class="review-score">
              <StarFilledIcon size="12px" style="color: #f59e0b" />
              {{ review.score.toFixed(1) }}
            </div>
          </div>
          
          <!-- 多维度评分 -->
          <div class="review-dimensions">
            <span>手法 {{ review.scores.skill }}</span>
            <span>态度 {{ review.scores.attitude }}</span>
            <span>准时 {{ review.scores.punctual }}</span>
          </div>
          
          <div class="review-content">{{ review.content }}</div>
          <div class="review-service">{{ review.serviceName }} · {{ review.serviceDate }}</div>
          
          <!-- 技师回复 -->
          <div class="review-reply" v-if="review.reply">
            <div class="reply-label">技师回复：</div>
            <div class="reply-content">{{ review.reply }}</div>
          </div>
        </div>
      </div>
      
      <t-empty v-if="displayReviews.length === 0" description="暂无评价" />
    </div>
    
    <!-- 底部操作栏 -->
    <div class="bottom-bar">
      <div class="bar-left">
        <div class="bar-action" @click="handleChat">
          <ChatIcon size="22px" />
          <span>咨询</span>
        </div>
        <div class="bar-action" @click="toggleFavorite">
          <HeartFilledIcon v-if="isFavorite" size="22px" style="color: #ff4d4f" />
          <HeartIcon v-else size="22px" />
          <span>收藏</span>
        </div>
      </div>
      <div class="bar-right">
        <div class="price-info">
          <span class="price-label">起步价</span>
          <span class="price-value"><em>¥</em>{{ tech.minPrice }}</span>
        </div>
        <t-button theme="primary" size="large" @click="goToBook">立即预约</t-button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { Button as TButton, Empty as TEmpty } from 'tdesign-mobile-vue'
import { 
  ChevronLeftIcon, ChevronRightIcon, HeartIcon, HeartFilledIcon, ShareIcon,
  UserIcon, ShopIcon, StarFilledIcon, CheckCircleIcon, ChatIcon
} from 'tdesign-icons-vue-next'

const router = useRouter()
const route = useRoute()

const isFavorite = ref(false)
const reviewFilter = ref('')
const showAllReviews = ref(false)

// 技师数据
const tech = ref({
  id: route.params.id || 1,
  name: '张师傅',
  gender: 1,
  avatar: '',
  shopId: 1,
  shopName: '康乐养生馆',
  rating: 4.9,
  orderCount: 328,
  returnRate: 45,
  years: 5,
  minPrice: 198,
  status: 'online',
  badge: '金牌',
  tags: ['推拿按摩', '肩颈调理', '中医理疗'],
  intro: '从事按摩推拿行业5年，擅长中式推拿、肩颈调理。曾在多家知名养生馆工作，积累了丰富的实践经验。手法专业，力度适中，深受顾客好评。',
  certifications: ['高级按摩师证书', '中医推拿师资格证', '平台认证技师'],
  reviewCount: 156,
  services: [
    { id: 1, name: '全身中式推拿', duration: 60, price: 298, sold: '1.2k' },
    { id: 2, name: '肩颈深度调理', duration: 45, price: 198, sold: '856' },
    { id: 3, name: '背部推拿放松', duration: 45, price: 218, sold: '623' },
    { id: 4, name: '头部舒缓按摩', duration: 30, price: 128, sold: '412' }
  ]
})

// 多维度评分
const scoreDimensions = ref([
  { key: 'skill', label: '手法专业', score: 4.9 },
  { key: 'attitude', label: '服务态度', score: 4.8 },
  { key: 'punctual', label: '准时到达', score: 4.9 },
  { key: 'communication', label: '沟通顺畅', score: 4.7 },
  { key: 'hygiene', label: '卫生整洁', score: 4.8 }
])

// 评价数据
const reviews = ref([
  { 
    id: 1, 
    userName: '用户***8', 
    avatar: '',
    score: 5.0,
    scores: { skill: 5, attitude: 5, punctual: 5 },
    content: '张师傅手法很专业，力度刚刚好，做完整个人都轻松了很多。特别是肩颈部位，困扰我很久的酸痛缓解了不少。', 
    serviceName: '全身中式推拿', 
    serviceDate: '2026-01-10',
    createdAt: '2026-01-10',
    reply: '感谢您的认可，期待下次为您服务！'
  },
  { 
    id: 2, 
    userName: '用户***2', 
    avatar: '',
    score: 4.8,
    scores: { skill: 5, attitude: 5, punctual: 4 },
    content: '肩颈调理效果很好，师傅很专业，会根据情况调整力度。', 
    serviceName: '肩颈深度调理', 
    serviceDate: '2026-01-09',
    createdAt: '2026-01-09',
    reply: ''
  },
  { 
    id: 3, 
    userName: '用户***5', 
    avatar: '',
    score: 4.5,
    scores: { skill: 4, attitude: 5, punctual: 5 },
    content: '服务态度很好，准时到达，整体体验不错。', 
    serviceName: '背部推拿放松', 
    serviceDate: '2026-01-08',
    createdAt: '2026-01-08',
    reply: ''
  }
])

// 评价统计
const reviewStats = computed(() => {
  const good = reviews.value.filter(r => r.score >= 4).length
  const mid = reviews.value.filter(r => r.score >= 3 && r.score < 4).length
  const bad = reviews.value.filter(r => r.score < 3).length
  return { good, mid, bad }
})

// 筛选后的评价
const displayReviews = computed(() => {
  if (!reviewFilter.value) return reviews.value.slice(0, 3)
  
  return reviews.value.filter(r => {
    if (reviewFilter.value === 'good') return r.score >= 4
    if (reviewFilter.value === 'mid') return r.score >= 3 && r.score < 4
    if (reviewFilter.value === 'bad') return r.score < 3
    return true
  })
})

// 状态文本
const statusText = computed(() => {
  const map = { online: '在线', busy: '服务中', offline: '离线' }
  return map[tech.value.status] || '离线'
})

const toggleFavorite = () => {
  isFavorite.value = !isFavorite.value
}

const handleShare = () => {
  // TODO: 分享功能
}

const handleChat = () => {
  // TODO: 咨询功能
}

const goToShop = () => {
  if (tech.value.shopId) {
    router.push(`/user/shop/detail/${tech.value.shopId}`)
  }
}

const selectService = (service) => {
  router.push({ 
    path: '/user/order/create', 
    query: { techId: tech.value.id, serviceId: service.id } 
  })
}

const goToBook = () => {
  router.push({ path: '/user/order/create', query: { techId: tech.value.id } })
}
</script>


<style lang="scss" scoped>
$primary: #07c160;

.technician-detail-page {
  background: #f5f5f5;
  min-height: 100vh;
  padding-bottom: 80px;
}

/* 导航栏 */
.nav-bar {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  height: 56px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0 16px;
  z-index: 100;
  background: transparent;
}

.back-btn {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  background: rgba(0, 0, 0, 0.3);
  display: flex;
  align-items: center;
  justify-content: center;
  color: #fff;
}

.nav-actions {
  display: flex;
  gap: 12px;
  
  > * {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: rgba(0, 0, 0, 0.3);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    
    &.is-favorite {
      color: #ff4d4f;
    }
  }
}

/* 技师头部 */
.tech-header {
  background: linear-gradient(135deg, $primary 0%, #10b981 100%);
  padding: 70px 16px 24px;
  display: flex;
  gap: 16px;
}

.tech-avatar-wrap {
  position: relative;
  flex-shrink: 0;
}

.tech-avatar {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  background: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
  border: 3px solid rgba(255, 255, 255, 0.3);
  
  img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }
}

.status-badge {
  position: absolute;
  bottom: -4px;
  left: 50%;
  transform: translateX(-50%);
  padding: 2px 10px;
  border-radius: 10px;
  font-size: 11px;
  color: #fff;
  white-space: nowrap;
  
  &.online { background: #22c55e; }
  &.busy { background: #f59e0b; }
  &.offline { background: #9ca3af; }
}

.tech-info {
  flex: 1;
  color: #fff;
}

.tech-name-row {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 8px;
}

.tech-name {
  font-size: 22px;
  font-weight: 600;
}

.tech-gender {
  font-size: 12px;
  padding: 2px 8px;
  border-radius: 4px;
  
  &.male { background: rgba(59, 130, 246, 0.3); }
  &.female { background: rgba(236, 72, 153, 0.3); }
}

.tech-badge {
  font-size: 11px;
  padding: 2px 8px;
  background: linear-gradient(135deg, #fbbf24, #f59e0b);
  border-radius: 4px;
}

.tech-shop {
  display: flex;
  align-items: center;
  gap: 4px;
  font-size: 13px;
  opacity: 0.9;
  margin-bottom: 10px;
}

.tech-tags {
  display: flex;
  flex-wrap: wrap;
  gap: 6px;
  
  .tag {
    font-size: 11px;
    padding: 3px 8px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 4px;
  }
}

/* 数据统计 */
.stats-card {
  display: flex;
  margin: -12px 16px 16px;
  padding: 16px;
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
}

.stat-item {
  flex: 1;
  text-align: center;
}

.stat-value {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 4px;
  font-size: 18px;
  font-weight: 600;
  color: #1a1a1a;
  margin-bottom: 4px;
}

.stat-label {
  font-size: 12px;
  color: #999;
}

.stat-divider {
  width: 1px;
  background: #f0f0f0;
  margin: 4px 0;
}

/* 卡片 */
.card {
  margin: 0 16px 16px;
  padding: 16px;
  background: #fff;
  border-radius: 12px;
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
  
  h3 {
    font-size: 16px;
    font-weight: 600;
    color: #1a1a1a;
  }
  
  .sub-text {
    font-size: 12px;
    color: #999;
  }
  
  .see-all {
    display: flex;
    align-items: center;
    font-size: 13px;
    color: #999;
  }
}

/* 多维度评分 */
.score-dimensions {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.score-item {
  display: flex;
  align-items: center;
  gap: 12px;
}

.score-label {
  width: 70px;
  font-size: 13px;
  color: #666;
}

.score-bar {
  flex: 1;
  height: 6px;
  background: #f0f0f0;
  border-radius: 3px;
  overflow: hidden;
}

.score-fill {
  height: 100%;
  background: linear-gradient(90deg, $primary, #10b981);
  border-radius: 3px;
}

.score-value {
  width: 32px;
  font-size: 13px;
  font-weight: 500;
  color: #f59e0b;
  text-align: right;
}

/* 简介 */
.intro-content {
  font-size: 14px;
  color: #666;
  line-height: 1.8;
  margin-bottom: 12px;
}

.cert-list {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
}

.cert-item {
  display: flex;
  align-items: center;
  gap: 4px;
  font-size: 12px;
  color: #666;
  padding: 4px 10px;
  background: rgba($primary, 0.08);
  border-radius: 4px;
}

/* 服务列表 */
.service-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.service-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px;
  background: #f8f9fa;
  border-radius: 10px;
}

.service-name {
  font-size: 15px;
  font-weight: 500;
  color: #1a1a1a;
  margin-bottom: 4px;
}

.service-meta {
  font-size: 12px;
  color: #999;
}

.service-right {
  display: flex;
  align-items: center;
  gap: 12px;
}

.service-price {
  font-size: 18px;
  font-weight: 600;
  color: #ff6b35;
  
  em {
    font-size: 12px;
    font-style: normal;
  }
}

/* 评价标签 */
.review-tags {
  display: flex;
  gap: 8px;
  margin-bottom: 16px;
  overflow-x: auto;
  
  &::-webkit-scrollbar { display: none; }
}

.review-tag {
  flex-shrink: 0;
  padding: 6px 14px;
  font-size: 13px;
  color: #666;
  background: #f5f5f5;
  border-radius: 16px;
  
  &.active {
    color: $primary;
    background: rgba($primary, 0.1);
  }
}

/* 评价列表 */
.review-list {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.review-item {
  padding-bottom: 16px;
  border-bottom: 1px solid #f0f0f0;
  
  &:last-child {
    padding-bottom: 0;
    border-bottom: none;
  }
}

.review-header {
  display: flex;
  align-items: center;
  margin-bottom: 10px;
}

.review-avatar {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  background: #f5f5f5;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
  margin-right: 10px;
  
  img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }
}

.review-user {
  flex: 1;
}

.review-name {
  font-size: 14px;
  color: #1a1a1a;
}

.review-time {
  font-size: 12px;
  color: #999;
}

.review-score {
  display: flex;
  align-items: center;
  gap: 4px;
  font-size: 14px;
  font-weight: 500;
  color: #f59e0b;
}

.review-dimensions {
  display: flex;
  gap: 16px;
  margin-bottom: 10px;
  font-size: 12px;
  color: #999;
}

.review-content {
  font-size: 14px;
  color: #333;
  line-height: 1.6;
  margin-bottom: 8px;
}

.review-service {
  font-size: 12px;
  color: #999;
  margin-bottom: 10px;
}

.review-reply {
  padding: 10px 12px;
  background: #f8f9fa;
  border-radius: 8px;
  
  .reply-label {
    font-size: 12px;
    color: $primary;
    margin-bottom: 4px;
  }
  
  .reply-content {
    font-size: 13px;
    color: #666;
  }
}

/* 底部操作栏 */
.bottom-bar {
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  height: 64px;
  background: #fff;
  border-top: 1px solid #f0f0f0;
  display: flex;
  align-items: center;
  padding: 0 16px;
  gap: 16px;
}

.bar-left {
  display: flex;
  gap: 20px;
}

.bar-action {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 2px;
  font-size: 11px;
  color: #666;
}

.bar-right {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: flex-end;
  gap: 16px;
}

.price-info {
  text-align: right;
}

.price-label {
  font-size: 11px;
  color: #999;
  display: block;
}

.price-value {
  font-size: 20px;
  font-weight: 600;
  color: #ff6b35;
  
  em {
    font-size: 12px;
    font-style: normal;
  }
}
</style>
