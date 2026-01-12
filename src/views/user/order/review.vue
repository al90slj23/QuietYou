<template>
  <div class="review-page">
    <!-- 订单信息 -->
    <div class="order-info">
      <div class="service-name">{{ order.serviceName }}</div>
      <div class="tech-info">
        <t-avatar :image="order.techAvatar" size="40px" />
        <span>{{ order.techName }}</span>
      </div>
    </div>

    <!-- 评分区域 -->
    <div class="section">
      <div class="section-title">服务评分</div>
      <div class="rating-list">
        <div class="rating-item">
          <span class="label">手法技术</span>
          <div class="stars">
            <StarFilledIcon 
              v-for="i in 5" 
              :key="i" 
              size="24px" 
              :class="{ active: ratings.skill >= i }"
              @click="ratings.skill = i"
            />
          </div>
        </div>
        <div class="rating-item">
          <span class="label">服务态度</span>
          <div class="stars">
            <StarFilledIcon 
              v-for="i in 5" 
              :key="i" 
              size="24px" 
              :class="{ active: ratings.attitude >= i }"
              @click="ratings.attitude = i"
            />
          </div>
        </div>
        <div class="rating-item">
          <span class="label">准时守约</span>
          <div class="stars">
            <StarFilledIcon 
              v-for="i in 5" 
              :key="i" 
              size="24px" 
              :class="{ active: ratings.punctual >= i }"
              @click="ratings.punctual = i"
            />
          </div>
        </div>
        <div class="rating-item">
          <span class="label">沟通能力</span>
          <div class="stars">
            <StarFilledIcon 
              v-for="i in 5" 
              :key="i" 
              size="24px" 
              :class="{ active: ratings.communication >= i }"
              @click="ratings.communication = i"
            />
          </div>
        </div>
        <div class="rating-item">
          <span class="label">卫生整洁</span>
          <div class="stars">
            <StarFilledIcon 
              v-for="i in 5" 
              :key="i" 
              size="24px" 
              :class="{ active: ratings.hygiene >= i }"
              @click="ratings.hygiene = i"
            />
          </div>
        </div>
      </div>
    </div>

    <!-- 评价内容 -->
    <div class="section">
      <div class="section-title">评价内容</div>
      <textarea v-model="content" placeholder="分享您的服务体验，帮助其他用户做出选择" rows="5"></textarea>
      <div class="char-count">{{ content.length }}/200</div>
    </div>

    <!-- 快捷标签 -->
    <div class="section">
      <div class="section-title">快捷评价</div>
      <div class="quick-tags">
        <span 
          class="tag" 
          v-for="tag in quickTags" 
          :key="tag"
          :class="{ active: selectedTags.includes(tag) }"
          @click="toggleTag(tag)"
        >{{ tag }}</span>
      </div>
    </div>

    <!-- 提交按钮 -->
    <div class="submit-area">
      <t-button block theme="primary" size="large" @click="submitReview">提交评价</t-button>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { Avatar as TAvatar, Button as TButton, Toast } from 'tdesign-mobile-vue'
import { StarFilledIcon } from 'tdesign-icons-vue-next'

const route = useRoute()
const router = useRouter()

const order = ref({
  id: route.params.id,
  serviceName: '全身推拿 60分钟',
  techName: '张师傅',
  techAvatar: ''
})

const ratings = ref({
  skill: 5,
  attitude: 5,
  punctual: 5,
  communication: 5,
  hygiene: 5
})

const content = ref('')
const selectedTags = ref([])

const quickTags = ['手法专业', '态度很好', '准时到达', '环境整洁', '物超所值', '下次还约']

const toggleTag = (tag) => {
  const index = selectedTags.value.indexOf(tag)
  if (index > -1) {
    selectedTags.value.splice(index, 1)
  } else {
    selectedTags.value.push(tag)
  }
}

const submitReview = () => {
  Toast({ message: '评价提交成功', theme: 'success' })
  setTimeout(() => router.back(), 1500)
}
</script>

<style lang="scss" scoped>
$primary: #07c160;

.review-page { min-height: 100vh; background: #f5f5f5; padding: 15px; padding-bottom: 100px; }

.order-info {
  background: #fff;
  border-radius: 12px;
  padding: 15px;
  margin-bottom: 15px;
  
  .service-name { font-size: 16px; font-weight: 500; color: #1a1a1a; margin-bottom: 12px; }
  .tech-info { display: flex; align-items: center; gap: 10px; font-size: 14px; color: #666; }
}

.section { background: #fff; border-radius: 12px; padding: 15px; margin-bottom: 15px; }
.section-title { font-size: 14px; color: #999; margin-bottom: 15px; }

.rating-list { display: flex; flex-direction: column; gap: 15px; }

.rating-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  
  .label { font-size: 15px; color: #1a1a1a; }
  .stars {
    display: flex;
    gap: 8px;
    color: #ddd;
    .active { color: #ffc107; }
  }
}

textarea {
  width: 100%;
  border: none;
  font-size: 14px;
  color: #1a1a1a;
  resize: none;
  outline: none;
  line-height: 1.6;
  &::placeholder { color: #ccc; }
}

.char-count { text-align: right; font-size: 12px; color: #999; margin-top: 8px; }

.quick-tags {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  
  .tag {
    font-size: 13px;
    padding: 8px 16px;
    background: #f5f5f5;
    color: #666;
    border-radius: 20px;
    cursor: pointer;
    border: 1px solid transparent;
    transition: all 0.2s;
    
    &.active {
      background: #f0fff4;
      color: $primary;
      border-color: $primary;
    }
  }
}

.submit-area { margin-top: 30px; }
</style>
