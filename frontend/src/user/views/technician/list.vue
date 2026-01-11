<template>
  <div class="technician-list-page">
    <t-navbar title="技师列表" left-arrow @left-click="router.back()" />
    
    <!-- 筛选栏 -->
    <div class="filter-bar">
      <t-dropdown :options="genderOptions" @click="onGenderFilter">
        <t-button variant="text" size="small">
          {{ genderLabel }} <t-icon name="caret-down-small" />
        </t-button>
      </t-dropdown>
      <t-dropdown :options="ratingOptions" @click="onRatingFilter">
        <t-button variant="text" size="small">
          {{ ratingLabel }} <t-icon name="caret-down-small" />
        </t-button>
      </t-dropdown>
      <t-dropdown :options="sortOptions" @click="onSortFilter">
        <t-button variant="text" size="small">
          {{ sortLabel }} <t-icon name="caret-down-small" />
        </t-button>
      </t-dropdown>
    </div>

    <!-- 技师列表 -->
    <div class="technician-list">
      <div
        v-for="tech in filteredTechnicians"
        :key="tech.id"
        class="technician-card"
        @click="router.push(`/technician/detail/${tech.id}`)"
      >
        <div class="technician-avatar">
          <t-icon name="user" size="36px" />
          <span class="technician-status" :class="getStatusClass(tech.status)"></span>
        </div>
        <div class="technician-info">
          <div class="technician-header">
            <span class="technician-name">{{ tech.name }}</span>
            <span class="technician-gender" :class="tech.gender === 1 ? 'male' : 'female'">
              {{ tech.gender === 1 ? '♂' : '♀' }}
            </span>
          </div>
          <div class="technician-shop">{{ tech.shopName }}</div>
          <div class="technician-stats">
            <span class="technician-rating">
              <t-icon name="star-filled" size="12px" /> {{ tech.rating }}
            </span>
            <span class="technician-orders">已服务{{ tech.orderCount }}单</span>
          </div>
          <div class="technician-footer">
            <span class="technician-distance">
              <t-icon name="location" size="12px" /> {{ tech.distance }}km
            </span>
            <span class="price">{{ tech.price }}<small>起</small></span>
          </div>
        </div>
      </div>
    </div>

    <t-empty v-if="filteredTechnicians.length === 0" description="暂无符合条件的技师">
      <t-button theme="primary" size="small" @click="resetFilters">重置筛选</t-button>
    </t-empty>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()

const genderFilter = ref('')
const ratingFilter = ref('')
const sortFilter = ref('')

const allTechnicians = ref([
  { id: 1, name: '张师傅', gender: 1, shopName: '康乐养生馆', rating: 4.9, orderCount: 328, distance: 1.2, price: 198, status: 1 },
  { id: 2, name: '李师傅', gender: 2, shopName: '悦享SPA会所', rating: 4.8, orderCount: 256, distance: 2.5, price: 228, status: 1 },
  { id: 3, name: '王师傅', gender: 1, shopName: '康乐养生馆', rating: 4.9, orderCount: 412, distance: 0.8, price: 268, status: 2 },
  { id: 4, name: '陈师傅', gender: 2, shopName: '舒心按摩店', rating: 4.7, orderCount: 189, distance: 3.8, price: 188, status: 1 },
  { id: 5, name: '刘师傅', gender: 1, shopName: '悦享SPA会所', rating: 4.8, orderCount: 275, distance: 1.5, price: 238, status: 0 }
])

const genderOptions = [
  { content: '不限', value: '' },
  { content: '男技师', value: '1' },
  { content: '女技师', value: '2' }
]

const ratingOptions = [
  { content: '不限', value: '' },
  { content: '4.5分以上', value: '4.5' },
  { content: '4.7分以上', value: '4.7' },
  { content: '4.9分以上', value: '4.9' }
]

const sortOptions = [
  { content: '综合排序', value: '' },
  { content: '距离最近', value: 'distance_asc' },
  { content: '评分最高', value: 'rating_desc' },
  { content: '价格最低', value: 'price_asc' }
]

const genderLabel = computed(() => {
  const opt = genderOptions.find(o => o.value === genderFilter.value)
  return opt?.content || '性别'
})

const ratingLabel = computed(() => {
  const opt = ratingOptions.find(o => o.value === ratingFilter.value)
  return opt?.content || '评分'
})

const sortLabel = computed(() => {
  const opt = sortOptions.find(o => o.value === sortFilter.value)
  return opt?.content || '综合排序'
})

const filteredTechnicians = computed(() => {
  let result = [...allTechnicians.value]
  
  if (genderFilter.value) {
    result = result.filter(t => t.gender === Number(genderFilter.value))
  }
  
  if (ratingFilter.value) {
    result = result.filter(t => t.rating >= Number(ratingFilter.value))
  }
  
  switch (sortFilter.value) {
    case 'distance_asc':
      result.sort((a, b) => a.distance - b.distance)
      break
    case 'rating_desc':
      result.sort((a, b) => b.rating - a.rating)
      break
    case 'price_asc':
      result.sort((a, b) => a.price - b.price)
      break
  }
  
  return result
})

const getStatusClass = (status) => {
  return { 1: 'online', 2: 'busy', 0: 'offline' }[status] || 'offline'
}

const onGenderFilter = (data) => { genderFilter.value = data.value }
const onRatingFilter = (data) => { ratingFilter.value = data.value }
const onSortFilter = (data) => { sortFilter.value = data.value }

const resetFilters = () => {
  genderFilter.value = ''
  ratingFilter.value = ''
  sortFilter.value = ''
}
</script>

<style lang="scss" scoped>
.technician-list-page {
  background: #f5f5f5;
  min-height: 100vh;
}

.filter-bar {
  display: flex;
  padding: 8px 12px;
  background: #fff;
  border-bottom: 1px solid #e5e5e5;
  gap: 8px;
}

.technician-list {
  padding: 12px;
}

.technician-card {
  display: flex;
  background: #fff;
  border-radius: 8px;
  padding: 12px;
  margin-bottom: 12px;
}

.technician-avatar {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  background: linear-gradient(135deg, #e8f8ef 0%, #d1fae5 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  position: relative;
}

.technician-status {
  position: absolute;
  bottom: 2px;
  right: 2px;
  width: 16px;
  height: 16px;
  border-radius: 50%;
  border: 2px solid #fff;
  
  &.online { background: #07c160; }
  &.busy { background: #f59e0b; }
  &.offline { background: #999; }
}

.technician-info {
  flex: 1;
  margin-left: 12px;
}

.technician-header {
  display: flex;
  align-items: center;
  margin-bottom: 4px;
}

.technician-name {
  font-size: 16px;
  font-weight: 500;
  margin-right: 8px;
}

.technician-gender {
  font-size: 12px;
  padding: 1px 6px;
  border-radius: 2px;
  
  &.male { color: #3b82f6; background: #dbeafe; }
  &.female { color: #ec4899; background: #fce7f3; }
}

.technician-shop {
  font-size: 12px;
  color: #666;
  margin-bottom: 6px;
}

.technician-stats {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 6px;
}

.technician-rating {
  font-size: 13px;
  color: #f59e0b;
}

.technician-orders {
  font-size: 12px;
  color: #999;
}

.technician-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.technician-distance {
  font-size: 12px;
  color: #999;
}
</style>
