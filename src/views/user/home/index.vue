<template>
  <div class="home-page">
    <!-- 顶部区域 -->
    <div class="header-area">
      <div class="header-top">
        <div class="location" @click="showLocationPicker = true">
          <LocationIcon size="16px" />
          <span>{{ currentCity }}</span>
          <ChevronDownIcon size="14px" />
        </div>
        <div class="header-actions">
          <div class="action-btn" @click="router.push('/user/message/list')">
            <NotificationIcon size="22px" />
            <span class="badge" v-if="unreadCount > 0">{{ unreadCount > 99 ? '99+' : unreadCount }}</span>
          </div>
        </div>
      </div>
      <div class="search-box" @click="router.push('/user/service/list')">
        <SearchIcon size="16px" />
        <span>搜索服务或技师</span>
      </div>
    </div>

    <!-- Banner -->
    <div class="banner-wrap">
      <t-swiper
        class="banner"
        :autoplay="true"
        :duration="4000"
        :navigation="{ type: 'dots' }"
      >
        <t-swiper-item v-for="(item, index) in banners" :key="index">
          <div class="banner-slide" :style="{ background: item.bg }">
            <div class="banner-content">
              <p class="banner-label">{{ item.label }}</p>
              <h2 class="banner-title">{{ item.title }}</h2>
              <p class="banner-desc">{{ item.desc }}</p>
            </div>
            <div class="banner-icon">
              <component :is="item.icon" :size="item.iconSize || '48px'" />
            </div>
          </div>
        </t-swiper-item>
      </t-swiper>
    </div>

    <!-- 快捷入口 -->
    <div class="quick-entry">
      <div class="entry-item" @click="goToService('home')">
        <div class="entry-icon" style="background: linear-gradient(135deg, #e8f5e9, #c8e6c9)">
          <HomeIcon size="22px" style="color: #43a047" />
        </div>
        <span>上门服务</span>
      </div>
      <div class="entry-item" @click="router.push('/user/shop/list')">
        <div class="entry-icon" style="background: linear-gradient(135deg, #e3f2fd, #bbdefb)">
          <ShopIcon size="22px" style="color: #1976d2" />
        </div>
        <span>到店消费</span>
      </div>
      <div class="entry-item" @click="router.push('/user/technician/list')">
        <div class="entry-icon" style="background: linear-gradient(135deg, #fff3e0, #ffe0b2)">
          <UserIcon size="22px" style="color: #f57c00" />
        </div>
        <span>找技师</span>
      </div>
      <div class="entry-item" @click="router.push('/user/order/list')">
        <div class="entry-icon" style="background: linear-gradient(135deg, #fce4ec, #f8bbd9)">
          <FileIcon size="22px" style="color: #c2185b" />
        </div>
        <span>我的订单</span>
      </div>
    </div>

    <!-- 服务分类 -->
    <div class="section">
      <div class="section-head">
        <h3>服务项目</h3>
        <span class="see-all" @click="router.push('/user/service/list')">
          全部 <ChevronRightIcon size="14px" />
        </span>
      </div>
      <div class="category-grid">
        <div 
          class="category-item" 
          v-for="cat in categories" 
          :key="cat.id"
          @click="goToService(cat.id)"
        >
          <div class="category-icon" :style="{ background: cat.bg }">
            <component :is="cat.icon" size="24px" :style="{ color: cat.color }" />
          </div>
          <span class="category-name">{{ cat.name }}</span>
          <span class="category-price">¥{{ cat.price }}起</span>
        </div>
      </div>
    </div>

    <!-- 附近技师 -->
    <div class="section">
      <div class="section-head">
        <h3>附近技师</h3>
        <span class="see-all" @click="router.push('/user/technician/list')">
          更多 <ChevronRightIcon size="14px" />
        </span>
      </div>
      <div class="tech-scroll">
        <div 
          class="tech-card"
          v-for="tech in nearbyTechs"
          :key="tech.id"
          @click="router.push(`/user/technician/detail/${tech.id}`)"
        >
          <div class="tech-avatar">
            <img v-if="tech.avatar" :src="tech.avatar" :alt="tech.name" />
            <UserIcon v-else size="28px" style="color: #bbb" />
            <span class="status-dot" :class="tech.status"></span>
          </div>
          <div class="tech-name">{{ tech.name }}</div>
          <div class="tech-rating">
            <StarFilledIcon size="12px" style="color: #f59e0b" />
            <span>{{ tech.rating }}</span>
          </div>
          <div class="tech-distance">{{ tech.distance }}km</div>
        </div>
      </div>
    </div>

    <!-- 附近店铺 -->
    <div class="section">
      <div class="section-head">
        <h3>附近店铺</h3>
        <span class="see-all" @click="router.push('/user/shop/list')">
          更多 <ChevronRightIcon size="14px" />
        </span>
      </div>
      <div class="shop-list">
        <div 
          class="shop-card"
          v-for="shop in nearbyShops"
          :key="shop.id"
          @click="router.push(`/user/shop/detail/${shop.id}`)"
        >
          <div class="shop-cover">
            <img v-if="shop.cover" :src="shop.cover" :alt="shop.name" />
            <ShopIcon v-else size="32px" style="color: #bbb" />
          </div>
          <div class="shop-info">
            <div class="shop-name">{{ shop.name }}</div>
            <div class="shop-meta">
              <span class="shop-rating">
                <StarFilledIcon size="12px" style="color: #f59e0b" />
                {{ shop.rating }}
              </span>
              <span class="shop-count">{{ shop.orderCount }}单</span>
            </div>
            <div class="shop-tags">
              <span v-for="tag in shop.tags" :key="tag" class="tag">{{ tag }}</span>
            </div>
            <div class="shop-footer">
              <span class="shop-distance">
                <LocationIcon size="12px" /> {{ shop.distance }}km
              </span>
              <span class="shop-price">¥{{ shop.avgPrice }}/人</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- 热门服务 -->
    <div class="section">
      <div class="section-head">
        <h3>热门服务</h3>
      </div>
      <div class="service-list">
        <div 
          class="service-card"
          v-for="service in hotServices"
          :key="service.id"
          @click="router.push(`/user/service/detail/${service.id}`)"
        >
          <div class="service-cover">
            <img v-if="service.cover" :src="service.cover" :alt="service.name" />
          </div>
          <div class="service-info">
            <div class="service-name">{{ service.name }}</div>
            <div class="service-desc">{{ service.desc }}</div>
            <div class="service-footer">
              <span class="service-price">
                <em>¥</em>{{ service.price }}
                <small>起</small>
              </span>
              <span class="service-sold">{{ service.sold }}人已约</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- 底部保障 -->
    <div class="footer-guarantee">
      <div class="guarantee-item">
        <SecuredIcon size="18px" style="color: #07c160" />
        <span>资质认证</span>
      </div>
      <div class="guarantee-item">
        <CheckCircleIcon size="18px" style="color: #07c160" />
        <span>服务保障</span>
      </div>
      <div class="guarantee-item">
        <WalletIcon size="18px" style="color: #07c160" />
        <span>退款无忧</span>
      </div>
    </div>

    <!-- 底部留白 -->
    <div class="footer-space"></div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { Swiper as TSwiper, SwiperItem as TSwiperItem } from 'tdesign-mobile-vue'
import { 
  LocationIcon, ChevronDownIcon, ChevronRightIcon, SearchIcon, NotificationIcon,
  HomeIcon, ShopIcon, UserIcon, FileIcon, StarFilledIcon,
  SecuredIcon, CheckCircleIcon, WalletIcon,
  HeartIcon, ServiceIcon, ToolsIcon, HelpCircleIcon
} from 'tdesign-icons-vue-next'

const router = useRouter()

const currentCity = ref('杭州')
const unreadCount = ref(3)
const showLocationPicker = ref(false)

const banners = ref([
  { 
    label: '品质生活', 
    title: '轻养到家', 
    desc: '专业技师 · 上门服务',
    bg: 'linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%)',
    icon: HeartIcon,
    iconSize: '56px'
  },
  { 
    label: '限时特惠', 
    title: '新客立减30', 
    desc: '首单专享优惠',
    bg: 'linear-gradient(135deg, #fff3e0 0%, #ffe0b2 100%)',
    icon: ServiceIcon,
    iconSize: '56px'
  }
])

const categories = ref([
  { id: 'tuina', name: '推拿按摩', price: 198, icon: HeartIcon, bg: 'linear-gradient(135deg, #e8f5e9, #c8e6c9)', color: '#43a047' },
  { id: 'zuliao', name: '足疗保健', price: 168, icon: ServiceIcon, bg: 'linear-gradient(135deg, #fce4ec, #f8bbd9)', color: '#c2185b' },
  { id: 'spa', name: '精油SPA', price: 298, icon: ToolsIcon, bg: 'linear-gradient(135deg, #e3f2fd, #bbdefb)', color: '#1976d2' },
  { id: 'liliao', name: '中医理疗', price: 258, icon: HelpCircleIcon, bg: 'linear-gradient(135deg, #fff3e0, #ffe0b2)', color: '#f57c00' }
])

const nearbyTechs = ref([
  { id: 1, name: '张师傅', rating: 4.9, distance: 0.8, status: 'online', avatar: '' },
  { id: 2, name: '李师傅', rating: 4.8, distance: 1.2, status: 'online', avatar: '' },
  { id: 3, name: '王师傅', rating: 4.9, distance: 1.5, status: 'busy', avatar: '' },
  { id: 4, name: '陈师傅', rating: 4.7, distance: 2.0, status: 'online', avatar: '' },
  { id: 5, name: '刘师傅', rating: 4.8, distance: 2.3, status: 'offline', avatar: '' }
])

const nearbyShops = ref([
  { id: 1, name: '康乐养生馆', rating: 4.8, orderCount: 2680, distance: 0.5, avgPrice: 198, tags: ['推拿', 'SPA'], cover: '' },
  { id: 2, name: '悦享SPA会所', rating: 4.9, orderCount: 1890, distance: 1.2, avgPrice: 288, tags: ['精油', '足疗'], cover: '' }
])

const hotServices = ref([
  { id: 1, name: '全身推拿', desc: '60分钟深度放松', price: 298, sold: '2.3k', cover: '' },
  { id: 2, name: '肩颈舒缓', desc: '45分钟精准调理', price: 198, sold: '1.8k', cover: '' },
  { id: 3, name: '足底按摩', desc: '60分钟足部护理', price: 168, sold: '1.5k', cover: '' }
])

const goToService = (type) => {
  if (type === 'home') {
    router.push({ path: '/user/service/list', query: { type: 'home' } })
  } else {
    router.push({ path: '/user/service/list', query: { category: type } })
  }
}
</script>


<style lang="scss" scoped>
$primary: #07c160;

.home-page {
  background: #f5f5f5;
  min-height: 100vh;
}

/* 顶部区域 */
.header-area {
  background: #fff;
  padding: 12px 16px 16px;
}

.header-top {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 12px;
}

.location {
  display: flex;
  align-items: center;
  gap: 4px;
  font-size: 15px;
  font-weight: 500;
  color: #1a1a1a;
}

.header-actions {
  .action-btn {
    position: relative;
    padding: 4px;
  }
  
  .badge {
    position: absolute;
    top: 0;
    right: 0;
    min-width: 16px;
    height: 16px;
    padding: 0 4px;
    font-size: 10px;
    line-height: 16px;
    text-align: center;
    color: #fff;
    background: #ff4d4f;
    border-radius: 8px;
  }
}

.search-box {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 10px 14px;
  background: #f5f5f5;
  border-radius: 20px;
  font-size: 14px;
  color: #999;
}

/* Banner */
.banner-wrap {
  padding: 0 16px 16px;
}

.banner {
  border-radius: 12px;
  overflow: hidden;
  height: 100px;
}

.banner-slide {
  height: 100px;
  padding: 16px 20px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.banner-content {
  flex: 1;
}

.banner-label {
  font-size: 11px;
  color: #666;
  margin-bottom: 4px;
}

.banner-title {
  font-size: 20px;
  font-weight: 600;
  color: #1a1a1a;
  margin-bottom: 4px;
}

.banner-desc {
  font-size: 12px;
  color: #666;
}

.banner-icon {
  opacity: 0.3;
}

/* 快捷入口 */
.quick-entry {
  display: flex;
  justify-content: space-around;
  padding: 16px;
  margin: 0 16px 16px;
  background: #fff;
  border-radius: 12px;
}

.entry-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
}

.entry-icon {
  width: 48px;
  height: 48px;
  border-radius: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.entry-item span {
  font-size: 12px;
  color: #666;
}

/* Section */
.section {
  padding: 0 16px;
  margin-bottom: 20px;
}

.section-head {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 12px;
  
  h3 {
    font-size: 17px;
    font-weight: 600;
    color: #1a1a1a;
  }
  
  .see-all {
    display: flex;
    align-items: center;
    font-size: 13px;
    color: #999;
  }
}

/* 服务分类 */
.category-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 12px;
  background: #fff;
  padding: 16px;
  border-radius: 12px;
}

.category-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 6px;
}

.category-icon {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.category-name {
  font-size: 13px;
  color: #1a1a1a;
  font-weight: 500;
}

.category-price {
  font-size: 11px;
  color: #999;
}

/* 技师横向滚动 */
.tech-scroll {
  display: flex;
  gap: 12px;
  overflow-x: auto;
  padding-bottom: 4px;
  
  &::-webkit-scrollbar {
    display: none;
  }
}

.tech-card {
  flex-shrink: 0;
  width: 80px;
  padding: 12px;
  background: #fff;
  border-radius: 12px;
  text-align: center;
}

.tech-avatar {
  width: 48px;
  height: 48px;
  margin: 0 auto 8px;
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
  width: 10px;
  height: 10px;
  border-radius: 50%;
  border: 2px solid #fff;
  
  &.online { background: #22c55e; }
  &.busy { background: #f59e0b; }
  &.offline { background: #d1d5db; }
}

.tech-name {
  font-size: 13px;
  font-weight: 500;
  color: #1a1a1a;
  margin-bottom: 4px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.tech-rating {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 2px;
  font-size: 12px;
  color: #666;
  margin-bottom: 2px;
}

.tech-distance {
  font-size: 11px;
  color: #999;
}

/* 店铺列表 */
.shop-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.shop-card {
  display: flex;
  gap: 12px;
  padding: 12px;
  background: #fff;
  border-radius: 12px;
}

.shop-cover {
  width: 80px;
  height: 80px;
  border-radius: 8px;
  background: #f5f5f5;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
  flex-shrink: 0;
  
  img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }
}

.shop-info {
  flex: 1;
  min-width: 0;
}

.shop-name {
  font-size: 15px;
  font-weight: 500;
  color: #1a1a1a;
  margin-bottom: 6px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.shop-meta {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 6px;
}

.shop-rating {
  display: flex;
  align-items: center;
  gap: 2px;
  font-size: 13px;
  color: #f59e0b;
}

.shop-count {
  font-size: 12px;
  color: #999;
}

.shop-tags {
  display: flex;
  gap: 6px;
  margin-bottom: 8px;
  
  .tag {
    font-size: 11px;
    padding: 2px 6px;
    background: rgba($primary, 0.1);
    color: $primary;
    border-radius: 4px;
  }
}

.shop-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.shop-distance {
  display: flex;
  align-items: center;
  gap: 2px;
  font-size: 12px;
  color: #999;
}

.shop-price {
  font-size: 13px;
  color: #ff6b35;
  font-weight: 500;
}

/* 服务列表 */
.service-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.service-card {
  display: flex;
  gap: 12px;
  padding: 12px;
  background: #fff;
  border-radius: 12px;
}

.service-cover {
  width: 100px;
  height: 80px;
  border-radius: 8px;
  background: linear-gradient(135deg, #e8f5e9, #c8e6c9);
  overflow: hidden;
  flex-shrink: 0;
  
  img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }
}

.service-info {
  flex: 1;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

.service-name {
  font-size: 15px;
  font-weight: 500;
  color: #1a1a1a;
}

.service-desc {
  font-size: 12px;
  color: #999;
}

.service-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.service-price {
  color: #ff6b35;
  
  em {
    font-size: 12px;
    font-style: normal;
  }
  
  font-size: 18px;
  font-weight: 600;
  
  small {
    font-size: 12px;
    font-weight: 400;
    color: #999;
    margin-left: 2px;
  }
}

.service-sold {
  font-size: 12px;
  color: #999;
}

/* 底部保障 */
.footer-guarantee {
  display: flex;
  justify-content: center;
  gap: 32px;
  padding: 24px 16px;
  background: #fff;
  margin: 0 16px;
  border-radius: 12px;
}

.guarantee-item {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 13px;
  color: #666;
}

.footer-space {
  height: 70px;
}
</style>
