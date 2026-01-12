<template>
  <div class="profile-page">
    <div class="shop-card">
      <div class="shop-info">
        <t-avatar :image="shopInfo.logo" size="72px" shape="round" />
        <div class="info">
          <div class="name">{{ shopInfo.name }}</div>
          <div class="address">{{ shopInfo.address }}</div>
        </div>
      </div>
      <div class="edit-btn" @click="goEdit">
        <EditIcon size="18px" />
      </div>
    </div>

    <div class="stats-card">
      <div class="stat-item">
        <div class="value">{{ stats.totalOrders }}</div>
        <div class="label">累计订单</div>
      </div>
      <div class="stat-item">
        <div class="value">{{ stats.rating }}</div>
        <div class="label">店铺评分</div>
      </div>
      <div class="stat-item">
        <div class="value">{{ stats.techCount }}</div>
        <div class="label">技师数量</div>
      </div>
      <div class="stat-item">
        <div class="value">{{ stats.totalIncome }}</div>
        <div class="label">累计收入</div>
      </div>
    </div>

    <div class="menu-section">
      <div class="menu-item" @click="goTo('/merchant/income')">
        <div class="left"><WalletIcon size="20px" /><span>收入明细</span></div>
        <ChevronRightIcon size="20px" class="arrow" />
      </div>
      <div class="menu-item" @click="goTo('/merchant/service/list')">
        <div class="left"><SettingIcon size="20px" /><span>服务项目</span></div>
        <ChevronRightIcon size="20px" class="arrow" />
      </div>
      <div class="menu-item" @click="goTo('/merchant/job/list')">
        <div class="left"><UserIcon size="20px" /><span>招聘管理</span></div>
        <ChevronRightIcon size="20px" class="arrow" />
      </div>
      <div class="menu-item" @click="goTo('/merchant/setting')">
        <div class="left"><SettingIcon size="20px" /><span>店铺设置</span></div>
        <ChevronRightIcon size="20px" class="arrow" />
      </div>
    </div>

    <div class="menu-section">
      <div class="menu-item" @click="showHelp">
        <div class="left"><HelpCircleIcon size="20px" /><span>帮助中心</span></div>
        <ChevronRightIcon size="20px" class="arrow" />
      </div>
      <div class="menu-item" @click="showAbout">
        <div class="left"><InfoCircleIcon size="20px" /><span>关于我们</span></div>
        <ChevronRightIcon size="20px" class="arrow" />
      </div>
    </div>

    <div class="logout-section">
      <t-button block variant="outline" @click="logout">退出登录</t-button>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { Avatar as TAvatar, Button as TButton, Toast } from 'tdesign-mobile-vue'
import { EditIcon, ChevronRightIcon, WalletIcon, SettingIcon, UserIcon, HelpCircleIcon, InfoCircleIcon } from 'tdesign-icons-vue-next'

const router = useRouter()

const shopInfo = ref({
  logo: '',
  name: '悦享养生馆',
  address: '成都市武侯区天府大道100号'
})

const stats = ref({
  totalOrders: 1280,
  rating: 4.8,
  techCount: 8,
  totalIncome: '68.9k'
})

const goTo = (path) => router.push(path)
const goEdit = () => router.push('/merchant/profile/edit')
const showHelp = () => Toast({ message: '帮助中心开发中', theme: 'warning' })
const showAbout = () => Toast({ message: '关于我们开发中', theme: 'warning' })
const logout = () => { Toast({ message: '已退出登录', theme: 'success' }); router.push('/') }
</script>

<style lang="scss" scoped>
$primary: #07c160;

.profile-page { min-height: 100vh; background: #f5f5f5; padding-bottom: 30px; }

.shop-card {
  background: linear-gradient(135deg, $primary 0%, #10b981 100%);
  padding: 30px 20px;
  display: flex;
  align-items: center;
  gap: 15px;
}

.shop-info {
  display: flex;
  align-items: center;
  gap: 15px;
  flex: 1;
  color: #fff;
  .info {
    .name { font-size: 20px; font-weight: 600; margin-bottom: 6px; }
    .address { font-size: 13px; opacity: 0.9; }
  }
}

.edit-btn {
  width: 36px;
  height: 36px;
  background: rgba(255,255,255,0.2);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #fff;
  cursor: pointer;
}

.stats-card {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  background: #fff;
  margin: -15px 15px 15px;
  border-radius: 12px;
  padding: 20px 10px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.08);
  
  .stat-item {
    text-align: center;
    .value { font-size: 20px; font-weight: 600; color: #1a1a1a; }
    .label { font-size: 12px; color: #999; margin-top: 4px; }
  }
}

.menu-section { background: #fff; margin: 0 15px 15px; border-radius: 12px; overflow: hidden; }

.menu-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 15px;
  border-bottom: 1px solid #f5f5f5;
  cursor: pointer;
  &:last-child { border-bottom: none; }
  .left { display: flex; align-items: center; gap: 12px; color: #1a1a1a; span { font-size: 15px; } }
  .arrow { color: #ccc; }
}

.logout-section { padding: 0 15px; margin-top: 30px; }
</style>
