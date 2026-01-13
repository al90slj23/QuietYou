<template>
  <div class="technician-list-page">
    <t-navbar title="技师列表" left-arrow @left-click="router.back()" />
    
    <!-- 筛选栏 -->
    <div class="filter-bar">
      <div class="filter-item" @click="showServiceFilter = true">
        <span>{{ serviceLabel }}</span>
        <ChevronDownIcon size="14px" />
      </div>
      <div class="filter-item" @click="showGenderFilter = true">
        <span>{{ genderLabel }}</span>
        <ChevronDownIcon size="14px" />
      </div>
      <div class="filter-item" @click="showSortFilter = true">
        <span>{{ sortLabel }}</span>
        <ChevronDownIcon size="14px" />
      </div>
      <div class="filter-item" :class="{ active: showMoreFilter }" @click="showMoreFilter = !showMoreFilter">
        <FilterIcon size="16px" />
        <span>筛选</span>
      </div>
    </div>
    
    <!-- 更多筛选 -->
    <div class="more-filter" v-show="showMoreFilter">
      <div class="filter-group">
        <div class="filter-label">评分</div>
        <div class="filter-tags">
          <span 
            v-for="opt in ratingOptions" 
            :key="opt.value"
            class="filter-tag"
            :class="{ active: ratingFilter === opt.value }"
            @click="ratingFilter = opt.value"
          >{{ opt.label }}</span>
        </div>
      </div>
      <div class="filter-group">
        <div class="filter-label">回头率</div>
        <div class="filter-tags">
          <span 
            v-for="opt in returnRateOptions" 
            :key="opt.value"
            class="filter-tag"
            :class="{ active: returnRateFilter === opt.value }"
            @click="returnRateFilter = opt.value"
          >{{ opt.label }}</span>
        </div>
      </div>
      <div class="filter-group">
        <div class="filter-label">状态</div>
        <div class="filter-tags">
          <span 
            v-for="opt in statusOptions" 
            :key="opt.value"
            class="filter-tag"
            :class="{ active: statusFilter === opt.value }"
            @click="statusFilter = opt.value"
          >{{ opt.label }}</span>
        </div>
      </div>
      <div class="filter-actions">
        <t-button variant="outline" size="small" @click="resetFilters">重置</t-button>
        <t-button theme="primary" size="small" @click="showMoreFilter = false">确定</t-button>
      </div>
    </div>

    <!-- 技师列表 -->
    <div class="technician-list">
      <div
        v-for="tech in filteredTechnicians"
        :key="tech.id"
        class="technician-card"
        @click="router.push(`/user/technician/detail/${tech.id}`)"
      >
        <div class="tech-left">
          <div class="tech-avatar">
            <img v-if="tech.avatar" :src="tech.avatar" :alt="tech.name" />
            <UserIcon v-else size="32px" style="color: #bbb" />
            <span class="status-dot" :class="getStatusClass(tech.status)"></span>
          </div>
        </div>
        <div class="tech-main">
          <div class="tech-header">
            <span class="tech-name">{{ tech.name }}</span>
            <span class="tech-gender" :class="tech.gender === 1 ? 'male' : 'female'">
              {{ tech.gender === 1 ? '♂' : '♀' }}
            </span>
            <span class="tech-badge" v-if="tech.badge">{{ tech.badge }}</span>
          </div>
          <div class="tech-shop">{{ tech.shopName || '独立技师' }}</div>
          <div class="tech-services">
            <span v-for="service in tech.services.slice(0, 3)" :key="service" class="service-tag">
              {{ service }}
            </span>
          </div>
          <div class="tech-stats">
            <span class="stat-item">
              <StarFilledIcon size="12px" style="color: #f59e0b" />
              {{ tech.rating }}
            </span>
            <span class="stat-item">{{ tech.orderCount }}单</span>
            <span class="stat-item">回头率{{ tech.returnRate }}%</span>
          </div>
        </div>
        <div class="tech-right">
          <div class="tech-distance">
            <LocationIcon size="12px" />
            {{ tech.distance }}km
          </div>
          <div class="tech-price">
            <em>¥</em>{{ tech.price }}<small>起</small>
          </div>
        </div>
      </div>
      
      <t-empty v-if="filteredTechnicians.length === 0" description="暂无符合条件的技师">
        <t-button theme="primary" size="small" @click="resetFilters">重置筛选</t-button>
      </t-empty>
    </div>
    
    <!-- 服务类型筛选 -->
    <t-popup 
      :visible="showServiceFilter" 
      placement="bottom"
      @visible-change="showServiceFilter = $event"
    >
      <div class="filter-popup">
        <div class="popup-header">
          <span>服务类型</span>
          <CloseIcon size="20px" @click="showServiceFilter = false" />
        </div>
        <div class="popup-options">
          <div 
            v-for="opt in serviceOptions" 
            :key="opt.value"
            class="popup-option"
            :class="{ active: serviceFilter === opt.value }"
            @click="serviceFilter = opt.value; showServiceFilter = false"
          >
            {{ opt.label }}
            <CheckIcon v-if="serviceFilter === opt.value" size="18px" style="color: #07c160" />
          </div>
        </div>
      </div>
    </t-popup>
    
    <!-- 性别筛选 -->
    <t-popup 
      :visible="showGenderFilter" 
      placement="bottom"
      @visible-change="showGenderFilter = $event"
    >
      <div class="filter-popup">
        <div class="popup-header">
          <span>技师性别</span>
          <CloseIcon size="20px" @click="showGenderFilter = false" />
        </div>
        <div class="popup-options">
          <div 
            v-for="opt in genderOptions" 
            :key="opt.value"
            class="popup-option"
            :class="{ active: genderFilter === opt.value }"
            @click="genderFilter = opt.value; showGenderFilter = false"
          >
            {{ opt.label }}
            <CheckIcon v-if="genderFilter === opt.value" size="18px" style="color: #07c160" />
          </div>
        </div>
      </div>
    </t-popup>
    
    <!-- 排序筛选 -->
    <t-popup 
      :visible="showSortFilter" 
      placement="bottom"
      @visible-change="showSortFilter = $event"
    >
      <div class="filter-popup">
        <div class="popup-header">
          <span>排序方式</span>
          <CloseIcon size="20px" @click="showSortFilter = false" />
        </div>
        <div class="popup-options">
          <div 
            v-for="opt in sortOptions" 
            :key="opt.value"
            class="popup-option"
            :class="{ active: sortFilter === opt.value }"
            @click="sortFilter = opt.value; showSortFilter = false"
          >
            {{ opt.label }}
            <CheckIcon v-if="sortFilter === opt.value" size="18px" style="color: #07c160" />
          </div>
        </div>
      </div>
    </t-popup>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { Navbar as TNavbar, Popup as TPopup, Button as TButton, Empty as TEmpty } from 'tdesign-mobile-vue'
import { 
  ChevronDownIcon, FilterIcon, UserIcon, LocationIcon, StarFilledIcon,
  CloseIcon, CheckIcon
} from 'tdesign-icons-vue-next'

const router = useRouter()
const route = useRoute()

// 筛选弹窗状态
const showServiceFilter = ref(false)
const showGenderFilter = ref(false)
const showSortFilter = ref(false)
const showMoreFilter = ref(false)

// 筛选条件
const serviceFilter = ref(route.query.category || '')
const genderFilter = ref('')
const sortFilter = ref('')
const ratingFilter = ref('')
const returnRateFilter = ref('')
const statusFilter = ref('')

// 筛选选项
const serviceOptions = [
  { label: '全部服务', value: '' },
  { label: '推拿按摩', value: 'tuina' },
  { label: '足疗保健', value: 'zuliao' },
  { label: '精油SPA', value: 'spa' },
  { label: '中医理疗', value: 'liliao' }
]

const genderOptions = [
  { label: '不限', value: '' },
  { label: '男技师', value: '1' },
  { label: '女技师', value: '2' }
]

const sortOptions = [
  { label: '综合排序', value: '' },
  { label: '距离最近', value: 'distance' },
  { label: '评分最高', value: 'rating' },
  { label: '单量最多', value: 'orders' },
  { label: '价格最低', value: 'price' }
]

const ratingOptions = [
  { label: '不限', value: '' },
  { label: '4.5分以上', value: '4.5' },
  { label: '4.8分以上', value: '4.8' }
]

const returnRateOptions = [
  { label: '不限', value: '' },
  { label: '30%以上', value: '30' },
  { label: '50%以上', value: '50' }
]

const statusOptions = [
  { label: '不限', value: '' },
  { label: '在线', value: 'online' },
  { label: '空闲', value: 'free' }
]

// 标签显示
const serviceLabel = computed(() => serviceOptions.find(o => o.value === serviceFilter.value)?.label || '服务')
const genderLabel = computed(() => genderOptions.find(o => o.value === genderFilter.value)?.label || '性别')
const sortLabel = computed(() => sortOptions.find(o => o.value === sortFilter.value)?.label || '排序')

// 模拟数据
const allTechnicians = ref([
  { id: 1, name: '张师傅', gender: 1, shopName: '康乐养生馆', rating: 4.9, orderCount: 328, returnRate: 45, distance: 1.2, price: 198, status: 'online', badge: '金牌', services: ['推拿按摩', '足疗'], avatar: '' },
  { id: 2, name: '李师傅', gender: 2, shopName: '悦享SPA会所', rating: 4.8, orderCount: 256, returnRate: 38, distance: 2.5, price: 228, status: 'online', services: ['精油SPA', '推拿按摩'], avatar: '' },
  { id: 3, name: '王师傅', gender: 1, shopName: '康乐养生馆', rating: 4.9, orderCount: 412, returnRate: 52, distance: 0.8, price: 268, status: 'busy', badge: '金牌', services: ['中医理疗', '推拿按摩'], avatar: '' },
  { id: 4, name: '陈师傅', gender: 2, shopName: '', rating: 4.7, orderCount: 189, returnRate: 35, distance: 3.8, price: 188, status: 'online', services: ['足疗', '推拿按摩'], avatar: '' },
  { id: 5, name: '刘师傅', gender: 1, shopName: '悦享SPA会所', rating: 4.8, orderCount: 275, returnRate: 42, distance: 1.5, price: 238, status: 'offline', services: ['精油SPA'], avatar: '' }
])

// 筛选后的技师列表
const filteredTechnicians = computed(() => {
  let result = [...allTechnicians.value]
  
  // 服务类型筛选
  if (serviceFilter.value) {
    const serviceMap = { tuina: '推拿按摩', zuliao: '足疗', spa: '精油SPA', liliao: '中医理疗' }
    const serviceName = serviceMap[serviceFilter.value]
    if (serviceName) {
      result = result.filter(t => t.services.includes(serviceName))
    }
  }
  
  // 性别筛选
  if (genderFilter.value) {
    result = result.filter(t => t.gender === Number(genderFilter.value))
  }
  
  // 评分筛选
  if (ratingFilter.value) {
    result = result.filter(t => t.rating >= Number(ratingFilter.value))
  }
  
  // 回头率筛选
  if (returnRateFilter.value) {
    result = result.filter(t => t.returnRate >= Number(returnRateFilter.value))
  }
  
  // 状态筛选
  if (statusFilter.value === 'online') {
    result = result.filter(t => t.status === 'online' || t.status === 'busy')
  } else if (statusFilter.value === 'free') {
    result = result.filter(t => t.status === 'online')
  }
  
  // 排序
  switch (sortFilter.value) {
    case 'distance':
      result.sort((a, b) => a.distance - b.distance)
      break
    case 'rating':
      result.sort((a, b) => b.rating - a.rating)
      break
    case 'orders':
      result.sort((a, b) => b.orderCount - a.orderCount)
      break
    case 'price':
      result.sort((a, b) => a.price - b.price)
      break
  }
  
  return result
})

const getStatusClass = (status) => {
  return { online: 'online', busy: 'busy', offline: 'offline' }[status] || 'offline'
}

const resetFilters = () => {
  serviceFilter.value = ''
  genderFilter.value = ''
  sortFilter.value = ''
  ratingFilter.value = ''
  returnRateFilter.value = ''
  statusFilter.value = ''
}
</script>

<style lang="scss" scoped>
$primary: #07c160;

.technician-list-page {
  background: #f5f5f5;
  min-height: 100vh;
}

/* 筛选栏 */
.filter-bar {
  display: flex;
  padding: 10px 16px;
  background: #fff;
  border-bottom: 1px solid #f0f0f0;
  gap: 8px;
}

.filter-item {
  display: flex;
  align-items: center;
  gap: 4px;
  padding: 6px 12px;
  font-size: 13px;
  color: #666;
  background: #f5f5f5;
  border-radius: 16px;
  
  &.active {
    color: $primary;
    background: rgba($primary, 0.1);
  }
}

/* 更多筛选 */
.more-filter {
  padding: 16px;
  background: #fff;
  border-bottom: 1px solid #f0f0f0;
}

.filter-group {
  margin-bottom: 16px;
}

.filter-label {
  font-size: 13px;
  color: #999;
  margin-bottom: 10px;
}

.filter-tags {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
}

.filter-tag {
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

.filter-actions {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  margin-top: 16px;
  padding-top: 16px;
  border-top: 1px solid #f0f0f0;
}

/* 技师列表 */
.technician-list {
  padding: 12px 16px;
}

.technician-card {
  display: flex;
  gap: 12px;
  padding: 14px;
  background: #fff;
  border-radius: 12px;
  margin-bottom: 12px;
}

.tech-left {
  flex-shrink: 0;
}

.tech-avatar {
  width: 64px;
  height: 64px;
  border-radius: 50%;
  background: #f5f5f5;
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
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
  width: 12px;
  height: 12px;
  border-radius: 50%;
  border: 2px solid #fff;
  
  &.online { background: #22c55e; }
  &.busy { background: #f59e0b; }
  &.offline { background: #d1d5db; }
}

.tech-main {
  flex: 1;
  min-width: 0;
}

.tech-header {
  display: flex;
  align-items: center;
  gap: 6px;
  margin-bottom: 4px;
}

.tech-name {
  font-size: 16px;
  font-weight: 500;
  color: #1a1a1a;
}

.tech-gender {
  font-size: 11px;
  padding: 1px 5px;
  border-radius: 3px;
  
  &.male { color: #3b82f6; background: #dbeafe; }
  &.female { color: #ec4899; background: #fce7f3; }
}

.tech-badge {
  font-size: 10px;
  padding: 2px 6px;
  background: linear-gradient(135deg, #fbbf24, #f59e0b);
  color: #fff;
  border-radius: 4px;
}

.tech-shop {
  font-size: 12px;
  color: #999;
  margin-bottom: 6px;
}

.tech-services {
  display: flex;
  gap: 6px;
  margin-bottom: 8px;
  
  .service-tag {
    font-size: 11px;
    padding: 2px 6px;
    background: #f5f5f5;
    color: #666;
    border-radius: 4px;
  }
}

.tech-stats {
  display: flex;
  gap: 12px;
  
  .stat-item {
    display: flex;
    align-items: center;
    gap: 2px;
    font-size: 12px;
    color: #666;
  }
}

.tech-right {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  justify-content: space-between;
}

.tech-distance {
  display: flex;
  align-items: center;
  gap: 2px;
  font-size: 12px;
  color: #999;
}

.tech-price {
  color: #ff6b35;
  
  em {
    font-size: 12px;
    font-style: normal;
  }
  
  font-size: 18px;
  font-weight: 600;
  
  small {
    font-size: 11px;
    font-weight: 400;
    color: #999;
  }
}

/* 筛选弹窗 */
.filter-popup {
  background: #fff;
  border-radius: 16px 16px 0 0;
  max-height: 60vh;
  overflow-y: auto;
}

.popup-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 16px;
  border-bottom: 1px solid #f0f0f0;
  font-size: 16px;
  font-weight: 500;
  color: #1a1a1a;
}

.popup-options {
  padding: 8px 0;
}

.popup-option {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 14px 16px;
  font-size: 15px;
  color: #333;
  
  &.active {
    color: $primary;
  }
}
</style>
