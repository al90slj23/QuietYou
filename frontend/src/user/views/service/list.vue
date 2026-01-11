<template>
  <div class="service-list-page">
    <!-- 分类标签 -->
    <t-tabs v-model="activeCategory" @change="onCategoryChange">
      <t-tab-panel v-for="cat in categories" :key="cat.id" :value="cat.id" :label="cat.name" />
    </t-tabs>

    <!-- 筛选栏 -->
    <div class="filter-bar">
      <t-dropdown :options="priceOptions" @click="onPriceFilter">
        <t-button variant="text" size="small">
          价格 <t-icon name="caret-down-small" />
        </t-button>
      </t-dropdown>
      <t-dropdown :options="durationOptions" @click="onDurationFilter">
        <t-button variant="text" size="small">
          时长 <t-icon name="caret-down-small" />
        </t-button>
      </t-dropdown>
      <t-dropdown :options="sortOptions" @click="onSortFilter">
        <t-button variant="text" size="small">
          综合排序 <t-icon name="caret-down-small" />
        </t-button>
      </t-dropdown>
    </div>

    <!-- 服务列表 -->
    <div class="service-list">
      <div
        v-for="service in filteredServices"
        :key="service.id"
        class="service-card"
        @click="router.push(`/service/detail/${service.id}`)"
      >
        <div class="service-card-image">
          <t-icon :name="service.icon" size="40px" />
        </div>
        <div class="service-card-info">
          <div class="service-card-name">{{ service.name }}</div>
          <div class="service-card-desc">{{ service.desc }}</div>
          <div class="service-card-tags" v-if="service.tags.length">
            <t-tag v-for="tag in service.tags" :key="tag" size="small" variant="light" theme="primary">
              {{ tag }}
            </t-tag>
          </div>
          <div class="service-card-footer">
            <span class="service-card-duration">{{ service.duration }}分钟</span>
            <span class="price">{{ service.price }}<small>起</small></span>
          </div>
        </div>
      </div>
    </div>

    <!-- 空状态 -->
    <t-empty v-if="filteredServices.length === 0" description="暂无符合条件的服务">
      <t-button theme="primary" size="small" @click="resetFilters">重置筛选</t-button>
    </t-empty>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'

const router = useRouter()
const route = useRoute()

const activeCategory = ref(Number(route.query.categoryId) || 0)
const priceFilter = ref('')
const durationFilter = ref('')
const sortFilter = ref('')

const categories = ref([
  { id: 0, name: '全部' },
  { id: 1, name: '中式推拿' },
  { id: 2, name: '泰式按摩' },
  { id: 3, name: '足疗保健' },
  { id: 4, name: '精油SPA' },
  { id: 5, name: '肩颈调理' },
  { id: 6, name: '艾灸理疗' },
  { id: 7, name: '刮痧拔罐' }
])

const allServices = ref([
  { id: 1, categoryId: 1, name: '全身中式推拿', desc: '传统手法，疏通经络，缓解疲劳', duration: 60, price: 298, icon: 'gesture-press', tags: ['热门', '推荐'] },
  { id: 2, categoryId: 1, name: '背部推拿放松', desc: '针对背部肌肉紧张，深层放松', duration: 45, price: 198, icon: 'gesture-press', tags: ['新品'] },
  { id: 3, categoryId: 5, name: '肩颈深度调理', desc: '针对久坐人群，专业手法放松肩颈', duration: 45, price: 198, icon: 'heart', tags: ['热门'] },
  { id: 4, categoryId: 3, name: '足底反射疗法', desc: '刺激足底穴位，促进血液循环', duration: 60, price: 168, icon: 'service', tags: [] },
  { id: 5, categoryId: 3, name: '足部精油按摩', desc: '精油配合按摩，滋润足部肌肤', duration: 45, price: 148, icon: 'service', tags: ['新品'] },
  { id: 6, categoryId: 2, name: '泰式古法按摩', desc: '正宗泰式手法，拉伸放松全身', duration: 90, price: 398, icon: 'spa', tags: ['推荐'] },
  { id: 7, categoryId: 4, name: '精油香薰SPA', desc: '天然精油，舒缓身心，深度放松', duration: 90, price: 458, icon: 'flower', tags: ['热门'] },
  { id: 8, categoryId: 6, name: '艾灸温经调理', desc: '传统艾灸，温经散寒，调理体质', duration: 60, price: 268, icon: 'tips', tags: [] }
])

const priceOptions = [
  { content: '不限', value: '' },
  { content: '200以下', value: '0-200' },
  { content: '200-300', value: '200-300' },
  { content: '300-400', value: '300-400' },
  { content: '400以上', value: '400-9999' }
]

const durationOptions = [
  { content: '不限', value: '' },
  { content: '30分钟', value: '30' },
  { content: '45分钟', value: '45' },
  { content: '60分钟', value: '60' },
  { content: '90分钟', value: '90' }
]

const sortOptions = [
  { content: '综合排序', value: '' },
  { content: '价格从低到高', value: 'price_asc' },
  { content: '价格从高到低', value: 'price_desc' }
]

const filteredServices = computed(() => {
  let result = [...allServices.value]
  
  if (activeCategory.value > 0) {
    result = result.filter(s => s.categoryId === activeCategory.value)
  }
  
  if (priceFilter.value) {
    const [min, max] = priceFilter.value.split('-').map(Number)
    result = result.filter(s => s.price >= min && s.price <= max)
  }
  
  if (durationFilter.value) {
    result = result.filter(s => s.duration === Number(durationFilter.value))
  }
  
  if (sortFilter.value === 'price_asc') {
    result.sort((a, b) => a.price - b.price)
  } else if (sortFilter.value === 'price_desc') {
    result.sort((a, b) => b.price - a.price)
  }
  
  return result
})

const onCategoryChange = (val) => {
  activeCategory.value = val
}

const onPriceFilter = (data) => {
  priceFilter.value = data.value
}

const onDurationFilter = (data) => {
  durationFilter.value = data.value
}

const onSortFilter = (data) => {
  sortFilter.value = data.value
}

const resetFilters = () => {
  activeCategory.value = 0
  priceFilter.value = ''
  durationFilter.value = ''
  sortFilter.value = ''
}
</script>

<style lang="scss" scoped>
.service-list-page {
  background: #fff;
  min-height: 100vh;
}

.filter-bar {
  display: flex;
  padding: 8px 12px;
  border-bottom: 1px solid #e5e5e5;
  gap: 8px;
}

.service-list {
  padding: 12px;
}

.service-card {
  display: flex;
  background: #fff;
  border-radius: 8px;
  padding: 12px;
  margin-bottom: 12px;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.05);
}

.service-card-image {
  width: 100px;
  height: 100px;
  border-radius: 8px;
  background: linear-gradient(135deg, #e8f8ef 0%, #d1fae5 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.service-card-info {
  flex: 1;
  margin-left: 12px;
  display: flex;
  flex-direction: column;
}

.service-card-name {
  font-size: 16px;
  font-weight: 500;
  margin-bottom: 4px;
}

.service-card-desc {
  font-size: 12px;
  color: #666;
  margin-bottom: 8px;
}

.service-card-tags {
  display: flex;
  gap: 6px;
  margin-bottom: 8px;
}

.service-card-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: auto;
}

.service-card-duration {
  font-size: 12px;
  color: #999;
}
</style>
