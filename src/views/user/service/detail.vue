<template>
  <div class="service-detail-page">
    <t-navbar title="服务详情" left-arrow @left-click="router.back()" />
    
    <div class="service-header">
      <div class="service-image">
        <t-icon :name="service.icon" size="60px" />
      </div>
      <div class="service-info">
        <h1 class="service-name">{{ service.name }}</h1>
        <p class="service-desc">{{ service.desc }}</p>
        <div class="service-meta">
          <span class="service-duration">{{ service.duration }}分钟</span>
          <span class="price">{{ service.price }}<small>起</small></span>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="card-title">服务介绍</div>
      <div class="service-intro">{{ service.intro }}</div>
    </div>

    <div class="card">
      <div class="card-title">可选技师</div>
      <div class="tech-list">
        <div
          v-for="tech in technicians"
          :key="tech.id"
          class="tech-item"
          @click="router.push(`/technician/detail/${tech.id}`)"
        >
          <div class="tech-avatar">
            <t-icon name="user" size="24px" />
          </div>
          <div class="tech-info">
            <div class="tech-name">{{ tech.name }}</div>
            <div class="tech-rating">
              <t-icon name="star-filled" size="12px" /> {{ tech.rating }}
            </div>
          </div>
          <t-icon name="chevron-right" size="20px" color="#999" />
        </div>
      </div>
    </div>

    <div class="bottom-bar">
      <t-button theme="primary" block @click="goToBook">立即预约</t-button>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter, useRoute } from 'vue-router'

const router = useRouter()
const route = useRoute()

const service = ref({
  id: 1,
  name: '全身中式推拿',
  desc: '传统手法，疏通经络，缓解疲劳',
  duration: 60,
  price: 298,
  icon: 'gesture-press',
  intro: '中式推拿是中国传统医学的重要组成部分，通过专业手法作用于人体经络穴位，达到疏通经络、调和气血、缓解疲劳的效果。我们的技师均经过专业培训，手法娴熟，力度适中。'
})

const technicians = ref([
  { id: 1, name: '张师傅', rating: 4.9 },
  { id: 2, name: '李师傅', rating: 4.8 },
  { id: 3, name: '王师傅', rating: 4.9 }
])

const goToBook = () => {
  router.push({ path: '/user/order/create', query: { serviceId: service.value.id } })
}
</script>

<style lang="scss" scoped>
.service-detail-page {
  padding-bottom: 80px;
}

.service-header {
  background: linear-gradient(135deg, #07c160 0%, #10b981 100%);
  padding: 20px;
  display: flex;
  align-items: center;
}

.service-image {
  width: 100px;
  height: 100px;
  border-radius: 12px;
  background: rgba(255, 255, 255, 0.2);
  display: flex;
  align-items: center;
  justify-content: center;
  color: #fff;
  flex-shrink: 0;
}

.service-info {
  flex: 1;
  margin-left: 16px;
  color: #fff;
}

.service-name {
  font-size: 20px;
  font-weight: 600;
  margin-bottom: 8px;
}

.service-desc {
  font-size: 14px;
  opacity: 0.9;
  margin-bottom: 12px;
}

.service-meta {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.service-duration {
  font-size: 13px;
  opacity: 0.8;
}

.price {
  font-size: 24px;
  font-weight: 600;
  color: #fff;
  
  &::before {
    content: '¥';
    font-size: 14px;
  }
}

.service-intro {
  font-size: 14px;
  color: #666;
  line-height: 1.8;
}

.tech-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.tech-item {
  display: flex;
  align-items: center;
  padding: 12px;
  background: #f5f5f5;
  border-radius: 8px;
}

.tech-avatar {
  width: 48px;
  height: 48px;
  border-radius: 50%;
  background: linear-gradient(135deg, #e8f8ef 0%, #d1fae5 100%);
  display: flex;
  align-items: center;
  justify-content: center;
}

.tech-info {
  flex: 1;
  margin-left: 12px;
}

.tech-name {
  font-size: 15px;
  font-weight: 500;
  margin-bottom: 4px;
}

.tech-rating {
  font-size: 12px;
  color: #f59e0b;
}

.bottom-bar {
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  padding: 12px 16px;
  background: #fff;
  border-top: 1px solid #e5e5e5;
}
</style>
