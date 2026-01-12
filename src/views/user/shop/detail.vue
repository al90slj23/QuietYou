<template>
  <div class="shop-detail-page">
    <!-- 店铺头部 -->
    <div class="shop-header">
      <div class="shop-cover">
        <img :src="shop.cover" :alt="shop.name" />
        <div class="overlay"></div>
        <div class="shop-basic">
          <t-avatar :image="shop.logo" size="64px" shape="round" />
          <div class="info">
            <div class="name">{{ shop.name }}</div>
            <div class="meta">
              <span><StarFilledIcon size="12px" /> {{ shop.rating }}</span>
              <span>{{ shop.orderCount }}单</span>
              <span>{{ shop.distance }}</span>
            </div>
          </div>
          <div class="favorite" @click="toggleFavorite">
            <HeartFilledIcon v-if="isFavorite" size="24px" class="active" />
            <HeartIcon v-else size="24px" />
          </div>
        </div>
      </div>
    </div>

    <!-- 店铺信息 -->
    <div class="section">
      <div class="info-item">
        <LocationIcon size="16px" />
        <span>{{ shop.address }}</span>
      </div>
      <div class="info-item">
        <TimeIcon size="16px" />
        <span>营业时间：{{ shop.businessHours }}</span>
      </div>
      <div class="info-item">
        <CallIcon size="16px" />
        <span>{{ shop.phone }}</span>
      </div>
    </div>

    <!-- 服务项目 -->
    <div class="section">
      <div class="section-title">服务项目</div>
      <div class="service-list">
        <div class="service-item" v-for="service in shop.services" :key="service.id" @click="goService(service.id)">
          <div class="service-info">
            <div class="name">{{ service.name }}</div>
            <div class="desc">{{ service.description }}</div>
            <div class="meta">{{ service.duration }}分钟 · {{ service.sold }}人已约</div>
          </div>
          <div class="service-right">
            <div class="price">¥{{ service.price }}</div>
            <t-button size="small" theme="primary">预约</t-button>
          </div>
        </div>
      </div>
    </div>

    <!-- 店铺技师 -->
    <div class="section">
      <div class="section-title">店铺技师</div>
      <div class="tech-list">
        <div class="tech-item" v-for="tech in shop.techs" :key="tech.id" @click="goTech(tech.id)">
          <t-avatar :image="tech.avatar" size="48px" />
          <div class="tech-info">
            <div class="name">
              {{ tech.name }}
              <span class="badge" v-if="tech.isCertified">已认证</span>
            </div>
            <div class="meta">
              <span><StarFilledIcon size="12px" /> {{ tech.rating }}</span>
              <span>{{ tech.orderCount }}单</span>
              <span>{{ tech.experience }}年经验</span>
            </div>
          </div>
          <ChevronRightIcon size="16px" class="arrow" />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { Avatar as TAvatar, Button as TButton, Toast } from 'tdesign-mobile-vue'
import { StarFilledIcon, LocationIcon, TimeIcon, CallIcon, HeartIcon, HeartFilledIcon, ChevronRightIcon } from 'tdesign-icons-vue-next'

const route = useRoute()
const router = useRouter()
const isFavorite = ref(false)

const shop = ref({
  id: route.params.id,
  name: '悦享养生馆',
  logo: '',
  cover: '',
  rating: 4.9,
  orderCount: 1280,
  distance: '1.2km',
  address: '成都市武侯区天府大道100号',
  businessHours: '10:00-22:00',
  phone: '028-88888888',
  services: [
    { id: 1, name: '全身推拿', description: '中式传统推拿，舒缓全身肌肉疲劳', duration: 60, price: 198, sold: 520 },
    { id: 2, name: '肩颈按摩', description: '针对肩颈部位深度放松', duration: 45, price: 158, sold: 380 },
    { id: 3, name: '足底按摩', description: '足部穴位按摩，促进血液循环', duration: 60, price: 168, sold: 290 }
  ],
  techs: [
    { id: 1, avatar: '', name: '张师傅', isCertified: true, rating: 4.9, orderCount: 328, experience: 5 },
    { id: 2, avatar: '', name: '李师傅', isCertified: true, rating: 4.8, orderCount: 256, experience: 3 },
    { id: 3, avatar: '', name: '王师傅', isCertified: false, rating: 4.7, orderCount: 186, experience: 2 }
  ]
})

const toggleFavorite = () => {
  isFavorite.value = !isFavorite.value
  Toast({ message: isFavorite.value ? '已收藏' : '已取消收藏', theme: 'success' })
}

const goService = (id) => router.push(`/user/service/detail/${id}`)
const goTech = (id) => router.push(`/user/technician/detail/${id}`)
</script>

<style lang="scss" scoped>
$primary: #07c160;

.shop-detail-page { min-height: 100vh; background: #f5f5f5; padding-bottom: 30px; }

.shop-header {
  .shop-cover {
    height: 180px;
    background: linear-gradient(135deg, $primary 0%, #10b981 100%);
    position: relative;
    
    img { width: 100%; height: 100%; object-fit: cover; }
    .overlay { position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.3); }
  }
  
  .shop-basic {
    position: absolute;
    bottom: -30px;
    left: 15px;
    right: 15px;
    display: flex;
    align-items: flex-end;
    gap: 12px;
    
    .info {
      flex: 1;
      color: #fff;
      .name { font-size: 20px; font-weight: 600; margin-bottom: 6px; }
      .meta { display: flex; gap: 12px; font-size: 12px; opacity: 0.9; }
    }
    
    .favorite { color: #fff; &.active { color: #f44336; } }
  }
}

.section {
  background: #fff;
  margin: 45px 15px 15px;
  border-radius: 12px;
  padding: 15px;
  
  &:not(:first-of-type) { margin-top: 15px; }
}

.section-title { font-size: 16px; font-weight: 600; color: #1a1a1a; margin-bottom: 15px; }

.info-item {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 14px;
  color: #666;
  padding: 8px 0;
  border-bottom: 1px solid #f5f5f5;
  &:last-child { border-bottom: none; }
}

.service-list { display: flex; flex-direction: column; gap: 15px; }

.service-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-bottom: 15px;
  border-bottom: 1px solid #f5f5f5;
  &:last-child { border-bottom: none; padding-bottom: 0; }
  
  .service-info {
    flex: 1;
    .name { font-size: 15px; font-weight: 500; color: #1a1a1a; margin-bottom: 4px; }
    .desc { font-size: 12px; color: #999; margin-bottom: 4px; }
    .meta { font-size: 12px; color: #999; }
  }
  
  .service-right {
    text-align: right;
    .price { font-size: 18px; font-weight: 600; color: #f44336; margin-bottom: 8px; }
  }
}

.tech-list { display: flex; flex-direction: column; }

.tech-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px 0;
  border-bottom: 1px solid #f5f5f5;
  &:last-child { border-bottom: none; }
  
  .tech-info {
    flex: 1;
    .name {
      font-size: 15px;
      font-weight: 500;
      color: #1a1a1a;
      display: flex;
      align-items: center;
      gap: 8px;
      margin-bottom: 4px;
      .badge { font-size: 10px; padding: 2px 6px; background: #e8f5e9; color: $primary; border-radius: 4px; }
    }
    .meta { display: flex; gap: 12px; font-size: 12px; color: #999; }
  }
  
  .arrow { color: #ccc; }
}
</style>
