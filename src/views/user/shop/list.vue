<template>
  <div class="shop-list-page">
    <!-- 筛选栏 -->
    <div class="filter-bar">
      <div class="filter-item" :class="{ active: sortBy === 'distance' }" @click="sortBy = 'distance'">距离最近</div>
      <div class="filter-item" :class="{ active: sortBy === 'rating' }" @click="sortBy = 'rating'">评分最高</div>
      <div class="filter-item" :class="{ active: sortBy === 'orders' }" @click="sortBy = 'orders'">人气最旺</div>
    </div>

    <!-- 店铺列表 -->
    <div class="shop-list">
      <div class="shop-card" v-for="shop in shops" :key="shop.id" @click="goDetail(shop.id)">
        <div class="shop-cover">
          <img :src="shop.cover" :alt="shop.name" />
          <div class="distance">{{ shop.distance }}</div>
        </div>
        <div class="shop-info">
          <div class="shop-name">{{ shop.name }}</div>
          <div class="shop-meta">
            <span class="rating"><StarFilledIcon size="12px" /> {{ shop.rating }}</span>
            <span>{{ shop.orderCount }}单</span>
            <span>{{ shop.techCount }}位技师</span>
          </div>
          <div class="shop-tags">
            <span class="tag" v-for="tag in shop.tags" :key="tag">{{ tag }}</span>
          </div>
          <div class="shop-address">
            <LocationIcon size="12px" />
            <span>{{ shop.address }}</span>
          </div>
        </div>
      </div>
    </div>

    <div class="empty" v-if="!shops.length">
      <ShopIcon size="48px" />
      <p>附近暂无店铺</p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { StarFilledIcon, LocationIcon, ShopIcon } from 'tdesign-icons-vue-next'

const router = useRouter()
const sortBy = ref('distance')

const shops = ref([
  { id: 1, name: '悦享养生馆', cover: '', distance: '1.2km', rating: 4.9, orderCount: 1280, techCount: 8, tags: ['推拿', '足疗', 'SPA'], address: '武侯区天府大道100号' },
  { id: 2, name: '康乐足道', cover: '', distance: '2.5km', rating: 4.8, orderCount: 860, techCount: 6, tags: ['足疗', '刮痧'], address: '高新区软件园B区' },
  { id: 3, name: '舒心堂', cover: '', distance: '3.8km', rating: 4.7, orderCount: 520, techCount: 5, tags: ['推拿', '理疗'], address: '锦江区春熙路' }
])

const goDetail = (id) => router.push(`/user/shop/detail/${id}`)
</script>

<style lang="scss" scoped>
$primary: #07c160;

.shop-list-page { min-height: 100vh; background: #f5f5f5; }

.filter-bar {
  display: flex;
  background: #fff;
  padding: 12px 15px;
  gap: 20px;
  position: sticky;
  top: 0;
  z-index: 10;
}

.filter-item {
  font-size: 14px;
  color: #666;
  cursor: pointer;
  padding: 4px 0;
  border-bottom: 2px solid transparent;
  &.active { color: $primary; border-color: $primary; font-weight: 500; }
}

.shop-list { padding: 15px; display: flex; flex-direction: column; gap: 12px; }

.shop-card {
  background: #fff;
  border-radius: 12px;
  overflow: hidden;
  display: flex;
  gap: 12px;
  padding: 12px;
}

.shop-cover {
  width: 100px;
  height: 100px;
  border-radius: 8px;
  background: #f5f5f5;
  position: relative;
  overflow: hidden;
  flex-shrink: 0;
  
  img { width: 100%; height: 100%; object-fit: cover; }
  .distance {
    position: absolute;
    bottom: 6px;
    right: 6px;
    font-size: 10px;
    padding: 2px 6px;
    background: rgba(0,0,0,0.6);
    color: #fff;
    border-radius: 4px;
  }
}

.shop-info {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.shop-name { font-size: 16px; font-weight: 500; color: #1a1a1a; }

.shop-meta {
  display: flex;
  gap: 12px;
  font-size: 12px;
  color: #999;
  .rating { color: #f59e0b; display: flex; align-items: center; gap: 2px; }
}

.shop-tags {
  display: flex;
  gap: 6px;
  .tag { font-size: 10px; padding: 2px 6px; background: #f0fff4; color: $primary; border-radius: 4px; }
}

.shop-address {
  display: flex;
  align-items: center;
  gap: 4px;
  font-size: 12px;
  color: #999;
}

.empty { text-align: center; padding: 60px 0; color: #999; p { margin-top: 12px; font-size: 14px; } }
</style>
