<template>
  <div class="service-list-page">
    <!-- 服务项目列表 -->
    <div class="service-list">
      <div class="service-card" v-for="service in services" :key="service.id">
        <div class="service-info">
          <div class="name">{{ service.name }}</div>
          <div class="desc">{{ service.description }}</div>
          <div class="meta">
            <span class="duration">{{ service.duration }}分钟</span>
            <span class="price">¥{{ service.price }}</span>
          </div>
        </div>
        <div class="service-action">
          <t-switch v-model="service.enabled" @change="(val) => onToggle(service, val)" />
        </div>
      </div>
    </div>

    <!-- 空状态 -->
    <div class="empty" v-if="!services.length">
      <ServiceIcon size="48px" />
      <p>暂无服务项目</p>
    </div>

    <!-- 提示 -->
    <div class="tips">
      <InfoCircleIcon size="14px" />
      <span>开启的服务项目将展示给顾客，关闭后顾客无法预约该项目</span>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { Switch as TSwitch, Toast } from 'tdesign-mobile-vue'
import { ServiceIcon, InfoCircleIcon } from 'tdesign-icons-vue-next'

const services = ref([
  { id: 1, name: '全身推拿', description: '中式传统推拿，舒缓全身肌肉疲劳', duration: 60, price: 198, enabled: true },
  { id: 2, name: '肩颈按摩', description: '针对肩颈部位深度放松', duration: 45, price: 158, enabled: true },
  { id: 3, name: '足底按摩', description: '足部穴位按摩，促进血液循环', duration: 60, price: 168, enabled: true },
  { id: 4, name: '头部按摩', description: '缓解头痛、改善睡眠质量', duration: 30, price: 98, enabled: false },
  { id: 5, name: '背部刮痧', description: '传统刮痧疗法，祛湿排毒', duration: 45, price: 128, enabled: true }
])

const onToggle = (service, value) => {
  Toast({ message: value ? '已开启服务' : '已关闭服务', theme: 'success' })
}
</script>

<style lang="scss" scoped>
$primary: #07c160;

.service-list-page {
  min-height: 100vh;
  background: #f5f5f5;
  padding: 15px;
}

.service-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.service-card {
  background: #fff;
  border-radius: 12px;
  padding: 15px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.service-info {
  flex: 1;
  
  .name {
    font-size: 16px;
    font-weight: 500;
    color: #1a1a1a;
    margin-bottom: 6px;
  }
  
  .desc {
    font-size: 13px;
    color: #999;
    margin-bottom: 8px;
  }
  
  .meta {
    display: flex;
    gap: 15px;
    font-size: 13px;
    
    .duration { color: #666; }
    .price { color: #f44336; font-weight: 600; }
  }
}

.empty {
  text-align: center;
  padding: 60px 0;
  color: #999;
  p { margin-top: 12px; font-size: 14px; }
}

.tips {
  display: flex;
  align-items: flex-start;
  gap: 6px;
  font-size: 12px;
  color: #999;
  margin-top: 20px;
  padding: 0 5px;
}
</style>
