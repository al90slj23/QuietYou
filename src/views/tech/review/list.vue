<template>
  <div class="review-list-page">
    <!-- 评价统计 -->
    <div class="stats-card">
      <div class="overall">
        <div class="score">{{ stats.overall }}</div>
        <div class="stars">
          <StarFilledIcon v-for="i in 5" :key="i" size="16px" :class="{ active: i <= Math.round(stats.overall) }" />
        </div>
        <div class="count">共{{ stats.total }}条评价</div>
      </div>
      <div class="dimensions">
        <div class="dim-item">
          <span class="label">手法技术</span>
          <div class="bar"><div class="fill" :style="{ width: stats.skill * 20 + '%' }"></div></div>
          <span class="value">{{ stats.skill }}</span>
        </div>
        <div class="dim-item">
          <span class="label">服务态度</span>
          <div class="bar"><div class="fill" :style="{ width: stats.attitude * 20 + '%' }"></div></div>
          <span class="value">{{ stats.attitude }}</span>
        </div>
        <div class="dim-item">
          <span class="label">准时守约</span>
          <div class="bar"><div class="fill" :style="{ width: stats.punctual * 20 + '%' }"></div></div>
          <span class="value">{{ stats.punctual }}</span>
        </div>
        <div class="dim-item">
          <span class="label">沟通能力</span>
          <div class="bar"><div class="fill" :style="{ width: stats.communication * 20 + '%' }"></div></div>
          <span class="value">{{ stats.communication }}</span>
        </div>
        <div class="dim-item">
          <span class="label">卫生整洁</span>
          <div class="bar"><div class="fill" :style="{ width: stats.hygiene * 20 + '%' }"></div></div>
          <span class="value">{{ stats.hygiene }}</span>
        </div>
      </div>
    </div>

    <!-- 评价列表 -->
    <div class="review-list">
      <div class="review-card" v-for="review in reviews" :key="review.id">
        <div class="review-header">
          <t-avatar :image="review.avatar" size="36px" />
          <div class="user-info">
            <div class="name">{{ review.userName }}</div>
            <div class="time">{{ review.time }}</div>
          </div>
          <div class="rating">
            <StarFilledIcon size="14px" class="star" />
            <span>{{ review.rating }}</span>
          </div>
        </div>
        <div class="review-content">{{ review.content }}</div>
        <div class="review-service">{{ review.serviceName }}</div>
      </div>
    </div>

    <!-- 空状态 -->
    <div class="empty" v-if="!reviews.length">
      <StarIcon size="48px" />
      <p>暂无评价</p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { Avatar as TAvatar } from 'tdesign-mobile-vue'
import { StarFilledIcon, StarIcon } from 'tdesign-icons-vue-next'

const stats = ref({
  overall: 4.9,
  total: 128,
  skill: 4.9,
  attitude: 4.8,
  punctual: 5.0,
  communication: 4.7,
  hygiene: 4.9
})

const reviews = ref([
  { id: 1, avatar: '', userName: '李**', time: '2026-01-13', rating: 5.0, content: '手法非常专业，力度刚刚好，按完整个人都轻松了很多，下次还会预约！', serviceName: '全身推拿 60分钟' },
  { id: 2, avatar: '', userName: '王**', time: '2026-01-12', rating: 4.8, content: '准时到达，服务态度很好，技术也不错，推荐！', serviceName: '肩颈按摩 45分钟' },
  { id: 3, avatar: '', userName: '张**', time: '2026-01-11', rating: 5.0, content: '足底按摩很舒服，师傅很专业，会继续支持', serviceName: '足底按摩 60分钟' },
  { id: 4, avatar: '', userName: '陈**', time: '2026-01-10', rating: 4.5, content: '整体不错，就是时间稍微短了一点', serviceName: '头部按摩 30分钟' }
])
</script>

<style lang="scss" scoped>
$primary: #07c160;

.review-list-page {
  min-height: 100vh;
  background: #f5f5f5;
  padding: 15px;
}

.stats-card {
  background: #fff;
  border-radius: 12px;
  padding: 20px;
  margin-bottom: 15px;
  display: flex;
  gap: 30px;
}

.overall {
  text-align: center;
  padding-right: 30px;
  border-right: 1px solid #f0f0f0;
  
  .score {
    font-size: 40px;
    font-weight: 700;
    color: #1a1a1a;
  }
  
  .stars {
    display: flex;
    gap: 2px;
    justify-content: center;
    margin: 8px 0;
    color: #ddd;
    .active { color: #ffc107; }
  }
  
  .count {
    font-size: 12px;
    color: #999;
  }
}

.dimensions {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.dim-item {
  display: flex;
  align-items: center;
  gap: 8px;
  
  .label {
    font-size: 12px;
    color: #666;
    width: 60px;
  }
  
  .bar {
    flex: 1;
    height: 6px;
    background: #f0f0f0;
    border-radius: 3px;
    overflow: hidden;
    
    .fill {
      height: 100%;
      background: $primary;
      border-radius: 3px;
    }
  }
  
  .value {
    font-size: 12px;
    color: #1a1a1a;
    font-weight: 500;
    width: 24px;
    text-align: right;
  }
}

.review-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.review-card {
  background: #fff;
  border-radius: 12px;
  padding: 15px;
}

.review-header {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 12px;
  
  .user-info {
    flex: 1;
    .name { font-size: 14px; color: #1a1a1a; font-weight: 500; }
    .time { font-size: 12px; color: #999; margin-top: 2px; }
  }
  
  .rating {
    display: flex;
    align-items: center;
    gap: 4px;
    .star { color: #ffc107; }
    span { font-size: 14px; font-weight: 600; color: #1a1a1a; }
  }
}

.review-content {
  font-size: 14px;
  color: #333;
  line-height: 1.6;
  margin-bottom: 10px;
}

.review-service {
  font-size: 12px;
  color: #999;
  padding: 6px 10px;
  background: #f5f5f5;
  border-radius: 4px;
  display: inline-block;
}

.empty {
  text-align: center;
  padding: 60px 0;
  color: #999;
  p { margin-top: 12px; font-size: 14px; }
}
</style>
