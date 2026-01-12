<template>
  <div class="portal">
    <!-- 背景装饰 -->
    <div class="portal-bg">
      <div class="bg-circle bg-circle-1"></div>
      <div class="bg-circle bg-circle-2"></div>
      <div class="bg-circle bg-circle-3"></div>
      <div class="bg-glow"></div>
    </div>

    <!-- 主内容 -->
    <div class="portal-content">
      <!-- Logo 区域 -->
      <div class="portal-header" :class="{ 'animate-in': isLoaded }">
        <div class="logo">
          <div class="logo-icon-wrapper">
            <svg class="logo-icon" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
              <circle cx="24" cy="24" r="22" fill="url(#portalLogoGradient)" />
              <path d="M24 14c-5.5 0-10 4.5-10 10s4.5 10 10 10 10-4.5 10-10-4.5-10-10-10zm0 16c-3.3 0-6-2.7-6-6s2.7-6 6-6 6 2.7 6 6-2.7 6-6 6z" fill="white" opacity="0.9"/>
              <path d="M24 18c-3.3 0-6 2.7-6 6h2c0-2.2 1.8-4 4-4s4 1.8 4 4h2c0-3.3-2.7-6-6-6z" fill="white"/>
              <circle cx="24" cy="24" r="2" fill="white"/>
              <defs>
                <linearGradient id="portalLogoGradient" x1="4" y1="4" x2="44" y2="44">
                  <stop offset="0%" stop-color="#07c160"/>
                  <stop offset="100%" stop-color="#10b981"/>
                </linearGradient>
              </defs>
            </svg>
          </div>
          <div class="logo-text">
            <h1>轻养到家</h1>
            <p>QINGYANG DAOJIA</p>
          </div>
        </div>
        <p class="portal-slogan">专业上门养生推拿服务平台</p>
      </div>

      <!-- 导航卡片 - 统一垂直布局 -->
      <div class="portal-cards">
        <!-- 第一行：预约技师（整行） -->
        <router-link to="/user" class="portal-card card-primary" :class="{ 'animate-in': isLoaded }" style="--delay: 0.15s">
          <div class="card-inner">
            <div class="card-icon">
              <UserIcon size="40px" />
            </div>
            <div class="card-content">
              <h3>预约技师</h3>
              <p>我是顾客，预约专业服务、享受健康生活</p>
            </div>
          </div>
          <div class="card-arrow">
            <ChevronRightIcon size="24px" />
          </div>
          <div class="card-decoration"></div>
        </router-link>

        <!-- 第二行 -->
        <div class="portal-row">
          <!-- 我要入驻（2/3） -->
          <div class="portal-card card-secondary" :class="{ 'animate-in': isLoaded }" style="--delay: 0.25s" @click="showJoinDialog = true">
            <div class="card-inner">
              <div class="card-icon">
                <AddCircleIcon size="36px" />
              </div>
              <div class="card-content">
                <h3>我要入驻</h3>
                <p>技师入驻 · 商家合作</p>
              </div>
            </div>
            <div class="card-arrow">
              <ChevronRightIcon size="22px" />
            </div>
          </div>

          <!-- 了解轻养到家（1/3） -->
          <router-link to="/home" class="portal-card card-tertiary" :class="{ 'animate-in': isLoaded }" style="--delay: 0.35s">
            <div class="card-inner">
              <div class="card-icon">
                <InfoCircleIcon size="32px" />
              </div>
              <div class="card-content">
                <h3>了解我们</h3>
                <p>品牌故事</p>
              </div>
            </div>
            <div class="card-arrow">
              <ChevronRightIcon size="20px" />
            </div>
          </router-link>
        </div>
      </div>

      <!-- 底部信息 -->
      <div class="portal-footer" :class="{ 'animate-in': isLoaded }" style="--delay: 0.45s">
        <p>© 2026 轻养到家 · 让专业养生服务触手可及</p>
      </div>
    </div>

    <!-- 入驻选择弹窗 -->
    <t-dialog
      v-model:visible="showJoinDialog"
      :header="false"
      :footer="false"
      :close-on-overlay-click="true"
      width="480px"
      class="join-dialog"
    >
      <div class="join-modal">
        <div class="modal-header">
          <h2>选择入驻类型</h2>
          <p>加入轻养到家，开启事业新篇章</p>
        </div>
        <div class="join-options">
          <router-link to="/tech" class="join-option" @click="showJoinDialog = false">
            <div class="option-icon">
              <ToolsIcon size="36px" />
            </div>
            <div class="option-content">
              <h4>我是技师</h4>
              <p>接单服务、收入管理、技能提升</p>
            </div>
            <ChevronRightIcon size="22px" class="option-arrow" />
          </router-link>
          <router-link to="/merchant" class="join-option" @click="showJoinDialog = false">
            <div class="option-icon">
              <ShopIcon size="36px" />
            </div>
            <div class="option-content">
              <h4>我是商家</h4>
              <p>入驻合作、团队管理、业务拓展</p>
            </div>
            <ChevronRightIcon size="22px" class="option-arrow" />
          </router-link>
        </div>
      </div>
    </t-dialog>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { UserIcon, AddCircleIcon, InfoCircleIcon, ToolsIcon, ShopIcon, ChevronRightIcon } from 'tdesign-icons-vue-next'

const showJoinDialog = ref(false)
const isLoaded = ref(false)

onMounted(() => {
  // 延迟触发动画，确保 DOM 渲染完成
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

.portal {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(160deg, #f0fbf5 0%, #e8f8ef 30%, #f8fdfb 70%, #fff 100%);
  position: relative;
  overflow: hidden;
  padding: 60px 24px;
}

// 背景装饰
.portal-bg {
  position: absolute;
  inset: 0;
  overflow: hidden;
  pointer-events: none;
}

.bg-circle {
  position: absolute;
  border-radius: 50%;
  
  &-1 {
    width: 800px;
    height: 800px;
    top: -300px;
    right: -200px;
    background: radial-gradient(circle, rgba(7, 193, 96, 0.06) 0%, transparent 70%);
    animation: float 25s ease-in-out infinite;
  }
  
  &-2 {
    width: 600px;
    height: 600px;
    bottom: -200px;
    left: -150px;
    background: radial-gradient(circle, rgba(7, 193, 96, 0.05) 0%, transparent 70%);
    animation: float 20s ease-in-out infinite reverse;
  }
  
  &-3 {
    width: 300px;
    height: 300px;
    top: 50%;
    left: 5%;
    background: radial-gradient(circle, rgba(7, 193, 96, 0.04) 0%, transparent 70%);
    animation: float 15s ease-in-out infinite;
  }
}

.bg-glow {
  position: absolute;
  width: 100%;
  height: 100%;
  background: radial-gradient(ellipse at 50% 0%, rgba(7, 193, 96, 0.08) 0%, transparent 50%);
}

@keyframes float {
  0%, 100% { transform: translateY(0) scale(1); }
  50% { transform: translateY(-40px) scale(1.02); }
}

// 主内容
.portal-content {
  position: relative;
  z-index: 1;
  max-width: 720px;
  width: 100%;
}

// Logo 区域
.portal-header {
  text-align: center;
  margin-bottom: 64px;
}

.logo {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 24px;
  margin-bottom: 24px;
}

.logo-icon-wrapper {
  position: relative;
  
  &::before {
    content: '';
    position: absolute;
    inset: -8px;
    background: radial-gradient(circle, rgba(7, 193, 96, 0.2) 0%, transparent 70%);
    border-radius: 50%;
    animation: pulse 3s ease-in-out infinite;
  }
}

@keyframes pulse {
  0%, 100% { transform: scale(1); opacity: 1; }
  50% { transform: scale(1.1); opacity: 0.7; }
}

.logo-icon {
  width: 80px;
  height: 80px;
  position: relative;
  filter: drop-shadow(0 12px 32px rgba(7, 193, 96, 0.35));
}

.logo-text {
  text-align: left;
  
  h1 {
    font-size: 44px;
    font-weight: 700;
    color: $text-primary;
    letter-spacing: 6px;
    margin-bottom: 6px;
  }
  
  p {
    font-size: 13px;
    color: $primary-color;
    letter-spacing: 4px;
    font-weight: 500;
    text-transform: uppercase;
  }
}

.portal-slogan {
  font-size: 18px;
  color: $text-secondary;
  font-weight: 300;
  letter-spacing: 2px;
}

// 导航卡片
.portal-cards {
  display: flex;
  flex-direction: column;
  gap: 24px;
  margin-bottom: 64px;
}

.portal-row {
  display: flex;
  gap: 24px;
}

.portal-card {
  background: #fff;
  border-radius: 24px;
  padding: 36px 32px;
  text-decoration: none;
  position: relative;
  overflow: hidden;
  box-shadow: 0 4px 24px rgba(0, 0, 0, 0.04), 0 1px 2px rgba(0, 0, 0, 0.02);
  transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
  display: flex;
  align-items: center;
  justify-content: space-between;
  cursor: pointer;
  border: 1px solid rgba(7, 193, 96, 0.08);
  
  &:hover {
    transform: translateY(-8px);
    box-shadow: 0 24px 48px rgba(7, 193, 96, 0.12), 0 8px 16px rgba(0, 0, 0, 0.04);
    border-color: rgba(7, 193, 96, 0.2);
    
    .card-icon {
      transform: scale(1.1);
      background: linear-gradient(135deg, $primary-color 0%, $primary-dark 100%);
      color: #fff;
    }
    
    .card-arrow {
      transform: translateX(6px);
      color: $primary-color;
    }
    
    .card-decoration {
      transform: scale(1.5);
      opacity: 0.15;
    }
  }
}

.card-inner {
  display: flex;
  align-items: center;
  gap: 24px;
}

.card-icon {
  width: 72px;
  height: 72px;
  background: $primary-lighter;
  border-radius: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: $primary-color;
  flex-shrink: 0;
  transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
}

.card-content {
  h3 {
    font-size: 24px;
    font-weight: 600;
    color: $text-primary;
    margin-bottom: 8px;
    letter-spacing: 1px;
  }
  
  p {
    font-size: 15px;
    color: $text-secondary;
    line-height: 1.6;
  }
}

.card-arrow {
  color: $text-tertiary;
  transition: all 0.4s ease;
  flex-shrink: 0;
}

.card-decoration {
  position: absolute;
  right: -60px;
  bottom: -60px;
  width: 200px;
  height: 200px;
  background: radial-gradient(circle, $primary-color 0%, transparent 70%);
  border-radius: 50%;
  opacity: 0.08;
  transition: all 0.5s ease;
}

// 主卡片样式
.card-primary {
  .card-icon {
    width: 80px;
    height: 80px;
    border-radius: 24px;
  }
  
  .card-content h3 {
    font-size: 28px;
  }
}

// 次要卡片
.card-secondary {
  flex: 2;
  
  .card-icon {
    width: 68px;
    height: 68px;
  }
  
  .card-content h3 {
    font-size: 22px;
  }
}

// 第三卡片
.card-tertiary {
  flex: 1;
  
  .card-icon {
    width: 60px;
    height: 60px;
    border-radius: 18px;
  }
  
  .card-content {
    h3 {
      font-size: 20px;
      margin-bottom: 4px;
    }
    
    p {
      font-size: 14px;
    }
  }
}

// 底部
.portal-footer {
  text-align: center;
  
  p {
    font-size: 14px;
    color: $text-tertiary;
    letter-spacing: 1px;
  }
}

// 入场动画
.portal-header,
.portal-card,
.portal-footer {
  opacity: 0;
  transform: translateY(30px);
  transition: opacity 0.6s cubic-bezier(0.4, 0, 0.2, 1), 
              transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
  transition-delay: var(--delay, 0s);
  
  &.animate-in {
    opacity: 1;
    transform: translateY(0);
  }
}

// 弹窗样式
.join-modal {
  padding: 16px 8px;
}

.modal-header {
  text-align: center;
  margin-bottom: 32px;
  
  h2 {
    font-size: 26px;
    font-weight: 600;
    color: $text-primary;
    margin-bottom: 8px;
  }
  
  p {
    font-size: 15px;
    color: $text-secondary;
  }
}

.join-options {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.join-option {
  display: flex;
  align-items: center;
  gap: 20px;
  padding: 24px;
  background: $primary-lighter;
  border-radius: 20px;
  text-decoration: none;
  transition: all 0.4s ease;
  border: 1px solid transparent;
  
  &:hover {
    background: $primary-light;
    border-color: rgba(7, 193, 96, 0.2);
    transform: translateX(4px);
    
    .option-icon {
      background: linear-gradient(135deg, $primary-color 0%, $primary-dark 100%);
      color: #fff;
      transform: scale(1.05);
    }
    
    .option-arrow {
      transform: translateX(4px);
      color: $primary-color;
    }
  }
}

.option-icon {
  width: 64px;
  height: 64px;
  background: #fff;
  border-radius: 18px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: $primary-color;
  flex-shrink: 0;
  transition: all 0.4s ease;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
}

.option-content {
  flex: 1;
  
  h4 {
    font-size: 20px;
    font-weight: 600;
    color: $text-primary;
    margin-bottom: 4px;
  }
  
  p {
    font-size: 14px;
    color: $text-secondary;
  }
}

.option-arrow {
  color: $text-tertiary;
  transition: all 0.3s ease;
}
</style>
