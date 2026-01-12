<template>
  <div class="service-list-page">
    <div class="top-action">
      <t-button block theme="primary" @click="addService">添加服务项目</t-button>
    </div>

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
        <div class="service-actions">
          <t-switch v-model="service.enabled" size="small" />
          <t-button size="small" variant="text" @click="editService(service)">编辑</t-button>
          <t-button size="small" variant="text" theme="danger" @click="deleteService(service)">删除</t-button>
        </div>
      </div>
    </div>

    <div class="empty" v-if="!services.length">
      <SettingIcon size="48px" />
      <p>暂无服务项目</p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { Button as TButton, Switch as TSwitch, Toast } from 'tdesign-mobile-vue'
import { SettingIcon } from 'tdesign-icons-vue-next'

const router = useRouter()

const services = ref([
  { id: 1, name: '全身推拿', description: '中式传统推拿，舒缓全身肌肉疲劳', duration: 60, price: 198, enabled: true },
  { id: 2, name: '肩颈按摩', description: '针对肩颈部位深度放松', duration: 45, price: 158, enabled: true },
  { id: 3, name: '足底按摩', description: '足部穴位按摩，促进血液循环', duration: 60, price: 168, enabled: true },
  { id: 4, name: '头部按摩', description: '缓解头痛、改善睡眠质量', duration: 30, price: 98, enabled: false }
])

const addService = () => router.push('/merchant/service/edit')
const editService = (service) => router.push(`/merchant/service/edit/${service.id}`)
const deleteService = (service) => Toast({ message: '确认删除该服务？', theme: 'warning' })
</script>

<style lang="scss" scoped>
$primary: #07c160;

.service-list-page { min-height: 100vh; background: #f5f5f5; padding: 15px; }
.top-action { margin-bottom: 15px; }
.service-list { display: flex; flex-direction: column; gap: 12px; }

.service-card {
  background: #fff;
  border-radius: 12px;
  padding: 15px;
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
}

.service-info {
  flex: 1;
  .name { font-size: 16px; font-weight: 500; color: #1a1a1a; margin-bottom: 6px; }
  .desc { font-size: 13px; color: #999; margin-bottom: 8px; }
  .meta {
    display: flex;
    gap: 15px;
    font-size: 13px;
    .duration { color: #666; }
    .price { color: #f44336; font-weight: 600; }
  }
}

.service-actions {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  gap: 8px;
}

.empty { text-align: center; padding: 60px 0; color: #999; p { margin-top: 12px; font-size: 14px; } }
</style>
