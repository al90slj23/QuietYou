<template>
  <div class="portal-mobile">
    <!-- 背景装饰 -->
    <div class="portal-bg">
      <div class="bg-glow"></div>
    </div>

    <!-- Logo 区域 -->
    <div class="portal-header" :class="{ 'animate-in': isLoaded }">
      <div class="logo-wrapper">
        <svg class="logo-icon" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
          <circle cx="24" cy="24" r="22" fill="url(#portalLogoGradientMobile)" />
          <path d="M24 14c-5.5 0-10 4.5-10 10s4.5 10 10 10 10-4.5 10-10-4.5-10-10-10zm0 16c-3.3 0-6-2.7-6-6s2.7-6 6-6 6 2.7 6 6-2.7 6-6 6z" fill="white" opacity="0.9"/>
          <path d="M24 18c-3.3 0-6 2.7-6 6h2c0-2.2 1.8-4 4-4s4 1.8 4 4h2c0-3.3-2.7-6-6-6z" fill="white"/>
          <circle cx="24" cy="24" r="2" fill="white"/>
          <defs>
            <linearGradient id="portalLogoGradientMobile" x1="4" y1="4" x2="44" y2="44">
              <stop offset="0%" stop-color="#07c160"/>
              <stop offset="100%" stop-color="#10b981"/>
            </linearGradient>
          </defs>
        </svg>
      </div>
      <h1>轻养到家</h1>
      <p class="slogan">专业上门养生推拿服务平台</p>
    </div>

    <!-- 导航卡片 -->
    <div class="portal-cards">
      <!-- 预约技师（主卡片） -->
      <router-link to="/user" class="portal-card card-primary" :class="{ 'animate-in': isLoaded }" style="--delay: 0.1s">
        <div class="card-inner">
          <div class="card-icon">
            <UserIcon size="28px" />
          </div>
          <div class="card-content">
            <h3>预约技师</h3>
            <p>我是顾客，预约服务、享受健康</p>
          </div>
        </div>
        <ChevronRightIcon class="card-arrow" size="20px" />
      </router-link>

      <!-- 第二行 -->
      <div class="portal-row">
        <!-- 我要入驻 -->
        <div class="portal-card card-secondary" :class="{ 'animate-in': isLoaded }" style="--delay: 0.2s" @click="showJoinPopup = true">
          <div class="card-inner">
            <div class="card-icon">
              <AddCircleIcon size="24px" />
            </div>
            <div class="card-content">
              <h3>我要入驻</h3>
              <p>技师 · 商家</p>
            </div>
          </div>
          <ChevronRightIcon class="card-arrow" size="18px" />
        </div>

        <!-- 了解我们 -->
        <router-link to="/home" class="portal-card card-tertiary" :class="{ 'animate-in': isLoaded }" style="--delay: 0.3s">
          <div class="card-inner">
            <div class="card-icon">
              <InfoCircleIcon size="22px" />
            </div>
            <div class="card-content">
              <h3>了解我们</h3>
              <p>品牌故事</p>
            </div>
          </div>
          <ChevronRightIcon class="card-arrow" size="18px" />
        </router-link>
      </div>
    </div>

    <!-- 底部信息 -->
    <div class="portal-footer" :class="{ 'animate-in': isLoaded }" style="--delay: 0.4s">
      <p>© 2026 轻养到家</p>
    </div>

    <!-- 入驻选择弹窗 -->
    <t-popup v-model="showJoinPopup" placement="bottom">
      <div class="join-popup">
        <div class="popup-header">
          <div class="popup-title">
            <h3>选择入驻类型</h3>
            <p>加入轻养到家，开启事业新篇章</p>
          </div>
          <div class="popup-close" @click="showJoinPopup = false">
            <CloseIcon size="20px" />
          </div>
        </div>
        <div class="join-options">
          <router-link to="/tech" class="join-option" @click="showJoinPopup = false">
            <div class="option-icon">
              <ToolsIcon size="28px" />
            </div>
            <div class="option-content">
              <h4>我是技师</h4>
              <p>接单服务、收入管理</p>
            </div>
            <ChevronRightIcon size="18px" class="option-arrow" />
          </router-link>
          <router-link to="/merchant" class="join-option" @click="showJoinPopup = false">
            <div class="option-icon">
              <ShopIcon size="28px" />
            </div>
            <div class="option-content">
              <h4>我是商家</h4>
              <p>入驻合作、团队管理</p>
            </div>
            <ChevronRightIcon size="18px" class="option-arrow" />
          </router-link>
        </div>
      </div>
    </t-popup>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { UserIcon, AddCircleIcon, InfoCircleIcon, ToolsIcon, ShopIcon, ChevronRightIcon, CloseIcon } from 'tdesign-icons-vue-next'

const showJoinPopup = ref(false)
const isLoaded = ref(false)

onMounted(() => {
  setTimeout(() => {
    isLoaded.value = true
  }, 100)
})
</script>

<style lang="scss" scoped>
$primary-color: #07c160;
$primary-dark: #06ae56;
$primary-light: #e8f8ef;
$primary-lighter: #f0fbf5;
$text-primary: #1a1a1a;
$text-secondary: #666666;
$text-tertiary: #999999;

.portal-mobile {
  min-height: 100vh;
  background: linear-gradient(180deg, #e8f8ef 0%, #f0fbf5 30%, #f8fdfb 60%, #fff 100%);
  padding: 80px 20px 40px;
  display: flex;
  flex-direction: column;
  position: relative;
  overflow: hidden;
}

.portal-bg {
  position: absolute;
  inset: 0;
  pointer-events: none;
}

.bg-glow {
  position: absolute;
  top: -100px;
  left: 50%;
  transform: translateX(-50%);
  width: 300px;
  height: 300px;
  background: radial-gradient(circle, rgba(7, 193, 96, 0.15) 0%, transparent 70%);
}

.portal-header {
  text-align: center;
  margin-bottom: 48px;
  position: relative;
  z-index: 1;
}

.logo-wrapper {
  margin-bottom: 20px;
  position: relative;
  display: inline-block;
  
  &::before {
    content: '';
    position: absolute;
    inset: -12px;
    background: radial-gradient(circle, rgba(7, 193, 96, 0.15) 0%, transparent 70%);
    border-radius: 50%;
  }
}

.logo-icon {
  width: 72px;
  height: 72px;
  position: relative;
  filter: drop-shadow(0 8px 24px rgba(7, 193, 96, 0.3));
}

h1 {
  font-size: 32px;
  font-weight: 700;
  color: $text-primary;
  letter-spacing: 4px;
  margin-bottom: 8px;
}

.slogan {
  font-size: 14px;
  color: $text-secondary;
  letter-spacing: 1px;
}

.portal-cards {
  display: flex;
  flex-direction: column;
  gap: 16px;
  flex: 1;
  position: relative;
  z-index: 1;
}

.portal-row {
  display: flex;
  gap: 12px;
}

.portal-card {
  background: #fff;
  border-radius: 20px;
  padding: 24px 20px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  text-decoration: none;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
  transition: all 0.3s ease;
  cursor: pointer;
  border: 1px solid rgba(7, 193, 96, 0.06);
  
  &:active {
    transform: scale(0.98);
    background: #fafffe;
  }
}

.card-inner {
  display: flex;
  align-items: center;
  gap: 16px;
  flex: 1;
  min-width: 0;
}

.card-icon {
  width: 52px;
  height: 52px;
  background: $primary-lighter;
  border-radius: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: $primary-color;
  flex-shrink: 0;
}

.card-content {
  flex: 1;
  min-width: 0;
  
  h3 {
    font-size: 17px;
    font-weight: 600;
    color: $text-primary;
    margin-bottom: 4px;
  }
  
  p {
    font-size: 13px;
    color: $text-secondary;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
}

.card-arrow {
  color: $text-tertiary;
  flex-shrink: 0;
  margin-left: 8px;
}

.card-primary {
  .card-icon {
    width: 56px;
    height: 56px;
  }
  
  .card-content h3 {
    font-size: 18px;
  }
}

.card-secondary {
  flex: 1.2;
  padding: 20px 16px;
  
  .card-icon {
    width: 44px;
    height: 44px;
    border-radius: 14px;
  }
  
  .card-content {
    h3 { font-size: 15px; }
    p { font-size: 12px; }
  }
}

.card-tertiary {
  flex: 1;
  padding: 20px 16px;
  
  .card-icon {
    width: 44px;
    height: 44px;
    border-radius: 14px;
  }
  
  .card-content {
    h3 { font-size: 15px; }
    p { font-size: 12px; }
  }
}

.portal-footer {
  text-align: center;
  margin-top: 32px;
  position: relative;
  z-index: 1;
  
  p {
    font-size: 12px;
    color: $text-tertiary;
  }
}

.portal-header,
.portal-card,
.portal-footer {
  opacity: 0;
  transform: translateY(24px);
  transition: opacity 0.5s cubic-bezier(0.4, 0, 0.2, 1), 
              transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
  transition-delay: var(--delay, 0s);
  
  &.animate-in {
    opacity: 1;
    transform: translateY(0);
  }
}

.join-popup {
  background: #fff;
  border-radius: 24px 24px 0 0;
  padding: 28px 20px 36px;
}

.popup-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 24px;
}

.popup-title {
  h3 {
    font-size: 20px;
    font-weight: 600;
    color: $text-primary;
    margin-bottom: 4px;
  }
  
  p {
    font-size: 13px;
    color: $text-secondary;
  }
}

.popup-close {
  width: 36px;
  height: 36px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: $text-tertiary;
  cursor: pointer;
  border-radius: 50%;
  transition: all 0.3s ease;
  
  &:active {
    background: #f5f5f5;
  }
}

.join-options {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.join-option {
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 20px;
  background: $primary-lighter;
  border-radius: 18px;
  text-decoration: none;
  border: 1px solid transparent;
  
  &:active {
    background: $primary-light;
    border-color: rgba(7, 193, 96, 0.15);
  }
}

.option-icon {
  width: 52px;
  height: 52px;
  background: #fff;
  border-radius: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: $primary-color;
  flex-shrink: 0;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
}

.option-content {
  flex: 1;
  
  h4 {
    font-size: 17px;
    font-weight: 600;
    color: $text-primary;
    margin-bottom: 2px;
  }
  
  p {
    font-size: 13px;
    color: $text-secondary;
  }
}

.option-arrow {
  color: $text-tertiary;
}
</style>
