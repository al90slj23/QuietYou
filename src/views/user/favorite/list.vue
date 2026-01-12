<template>
  <div class="favorite-page">
    <t-tabs v-model="activeTab">
      <t-tab-panel value="tech" label="技师" />
      <t-tab-panel value="shop" label="店铺" />
    </t-tabs>

    <!-- 技师收藏 -->
    <div class="list" v-if="activeTab === 'tech'">
      <div class="tech-card" v-for="tech in favoriteTechs" :key="tech.id" @click="goTech(tech.id)">
        <t-avatar :image="tech.avatar" size="56px" />
        <div class="info">
          <div class="name">
            {{ tech.name }}
            <span class="badge" v-if="tech.isCertified">已认证</span>
          </div>
          <div class="meta">
            <span><StarFilledIcon size="12px" /> {{ tech.rating }}</span>
            <span>{{ tech.orderCount }}单</span>
            <span>{{ tech.experience }}年经验</span>
          </div>
          <div class="skills">{{ tech.skills }}</div>
        </div>
        <t-button size="small" theme="primary" @click.stop="bookTech(tech)">预约</t-button>
      </div>
      <div class="empty" v-if="!favoriteTechs.length">
        <HeartIcon size="48px" />
        <p>暂无收藏的技师</p>
      </div>
    </div>

    <!-- 店铺收藏 -->
    <div class="list" v-if="activeTab === 'shop'">
      <div class="shop-card" v-for="shop in favoriteShops" :key="shop.id" @click="goShop(shop.id)">
        <div class="shop-cover">
          <img :src="shop.cover" :alt="shop.name" />
        </div>
        <div class="info">
          <div class="name">{{ shop.name }}</div>
          <div class="meta">
            <span><StarFilledIcon size="12px" /> {{ shop.rating }}</span>
            <span>{{ shop.distance }}</span>
          </div>
          <div class="address">
            <LocationIcon size="12px" />
            <span>{{ shop.address }}</span>
          </div>
        </div>
      </div>
      <div class="empty" v-if="!favoriteShops.length">
        <HeartIcon size="48px" />
        <p>暂无收藏的店铺</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { Tabs as TTabs, TabPanel as TTabPanel, Avatar as TAvatar, Button as TButton } from 'tdesign-mobile-vue'
import { StarFilledIcon, LocationIcon, HeartIcon } from 'tdesign-icons-vue-next'

const router = useRouter()
const activeTab = ref('tech')

const favoriteTechs = ref([
  { id: 1, avatar: '', name: '张师傅', isCertified: true, rating: 4.9, orderCount: 328, experience: 5, skills: '推拿、足疗、刮痧' },
  { id: 2, avatar: '', name: '李师傅', isCertified: true, rating: 4.8, orderCount: 256, experience: 3, skills: '肩颈、头疗' }
])

const favoriteShops = ref([
  { id: 1, cover: '', name: '悦享养生馆', rating: 4.9, distance: '1.2km', address: '武侯区天府大道100号' },
  { id: 2, cover: '', name: '康乐足道', rating: 4.8, distance: '2.5km', address: '高新区软件园B区' }
])

const goTech = (id) => router.push(`/user/technician/detail/${id}`)
const goShop = (id) => router.push(`/user/shop/detail/${id}`)
const bookTech = (tech) => router.push(`/user/order/create?techId=${tech.id}`)
</script>

<style lang="scss" scoped>
$primary: #07c160;

.favorite-page { min-height: 100vh; background: #f5f5f5; }

.list { padding: 15px; display: flex; flex-direction: column; gap: 12px; }

.tech-card {
  background: #fff;
  border-radius: 12px;
  padding: 15px;
  display: flex;
  align-items: center;
  gap: 12px;
  
  .info {
    flex: 1;
    .name {
      font-size: 16px;
      font-weight: 500;
      color: #1a1a1a;
      display: flex;
      align-items: center;
      gap: 8px;
      margin-bottom: 6px;
      .badge { font-size: 10px; padding: 2px 6px; background: #e8f5e9; color: $primary; border-radius: 4px; }
    }
    .meta { display: flex; gap: 12px; font-size: 12px; color: #999; margin-bottom: 4px; }
    .skills { font-size: 12px; color: #666; }
  }
}

.shop-card {
  background: #fff;
  border-radius: 12px;
  padding: 12px;
  display: flex;
  gap: 12px;
  
  .shop-cover {
    width: 80px;
    height: 80px;
    border-radius: 8px;
    background: #f5f5f5;
    overflow: hidden;
    flex-shrink: 0;
    img { width: 100%; height: 100%; object-fit: cover; }
  }
  
  .info {
    flex: 1;
    .name { font-size: 16px; font-weight: 500; color: #1a1a1a; margin-bottom: 6px; }
    .meta { display: flex; gap: 12px; font-size: 12px; color: #999; margin-bottom: 6px; }
    .address { display: flex; align-items: center; gap: 4px; font-size: 12px; color: #999; }
  }
}

.empty { text-align: center; padding: 60px 0; color: #999; p { margin-top: 12px; font-size: 14px; } }
</style>
