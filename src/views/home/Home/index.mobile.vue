<template>
  <div class="home-mobile">
    <!-- Hero Section -->
    <section class="hero">
      <div class="hero-bg">
        <div class="hero-circle hero-circle-1"></div>
        <div class="hero-circle hero-circle-2"></div>
      </div>
      <div class="hero-content">
        <p class="hero-badge animate-on-scroll">专业 · 安全 · 便捷</p>
        <h1 class="animate-on-scroll animate-delay-1">轻养到家</h1>
        <p class="subtitle animate-on-scroll animate-delay-2">专业上门养生推拿服务平台</p>
        <p class="desc animate-on-scroll animate-delay-3">让专业养生服务触手可及，为用户带来更健康、更舒适的生活体验。</p>
        <div class="actions animate-on-scroll animate-delay-4">
          <a href="#download" class="btn btn-primary">
            立即预约
            <span class="btn-arrow">→</span>
          </a>
          <router-link to="/home/about" class="btn btn-outline">了解更多</router-link>
        </div>
      </div>
      <div class="download-area" id="download">
        <div class="download-item animate-on-scroll animate-delay-5">
          <div class="icon float-animation"><MobileIcon size="24px" /></div>
          <span>小程序</span>
          <p>扫码预约</p>
        </div>
        <div class="download-item animate-on-scroll animate-delay-6">
          <div class="icon float-animation"><LogoWechatStrokeIcon size="24px" /></div>
          <span>公众号</span>
          <p>关注资讯</p>
        </div>
      </div>
      <div class="hero-wave">
        <svg viewBox="0 0 375 40" preserveAspectRatio="none">
          <path d="M0,20 Q93.75,40 187.5,20 T375,20 L375,40 L0,40 Z" fill="#fff"/>
        </svg>
      </div>
    </section>

    <!-- Features Section -->
    <section class="features">
      <div class="section-header animate-on-scroll">
        <p class="section-tag">WHY CHOOSE US</p>
        <h2>为什么选择我们</h2>
      </div>
      <div class="feature-list">
        <div 
          class="feature-item animate-on-scroll breathe-hover" 
          v-for="(f, index) in features" 
          :key="f.title"
          :class="`animate-delay-${index + 1}`"
        >
          <div class="icon-wrapper">
            <div class="icon"><component :is="f.icon" size="28px" /></div>
          </div>
          <div class="info">
            <h3>{{ f.title }}</h3>
            <p>{{ f.desc }}</p>
          </div>
          <div class="hover-indicator"></div>
        </div>
      </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
      <div class="section-header animate-on-scroll">
        <p class="section-tag light">OUR ACHIEVEMENTS</p>
        <h2>数据见证实力</h2>
      </div>
      <div class="stats-grid">
        <div 
          class="stat-item animate-on-scroll" 
          v-for="(s, index) in stats" 
          :key="s.label"
          :class="`animate-delay-${index + 1}`"
        >
          <div class="value">{{ s.value }}</div>
          <div class="label">{{ s.label }}</div>
          <div class="stat-bar"></div>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="cta animate-on-scroll animate-scale">
      <div class="cta-content">
        <h3>开启健康之旅</h3>
        <p>专业技师，上门服务</p>
      </div>
      <router-link to="/user" class="cta-btn">
        立即体验
        <span>→</span>
      </router-link>
    </section>
  </div>
</template>

<script setup>
import { CertificateIcon, SecuredIcon, TimeIcon, StarIcon, MobileIcon, LogoWechatStrokeIcon } from 'tdesign-icons-vue-next'
import { useScrollAnimation } from '@/composables/useScrollAnimation'

useScrollAnimation()

const features = [
  { icon: CertificateIcon, title: '专业认证', desc: '所有技师持证上岗，专业可靠' },
  { icon: SecuredIcon, title: '安全保障', desc: '平台担保交易，服务可追溯' },
  { icon: TimeIcon, title: '随叫随到', desc: '24小时在线，快速响应' },
  { icon: StarIcon, title: '品质服务', desc: '标准化流程，效果有保障' }
]

const stats = [
  { value: '10万+', label: '服务用户' },
  { value: '1000+', label: '认证技师' },
  { value: '20+', label: '覆盖城市' },
  { value: '99%', label: '好评率' }
]
</script>

<style lang="scss" scoped>
$primary: #07c160;
$primary-light: #e8f8ef;
$text-primary: #1a1a1a;
$text-secondary: #666;
$bg-light: #f8f9fa;

.home-mobile {
  padding-bottom: 70px; // TabBar 高度
}

// ========== Hero Section ==========
.hero {
  position: relative;
  background: linear-gradient(135deg, $primary 0%, #10b981 100%);
  color: #fff;
  padding: 70px 24px 60px;
  text-align: center;
  overflow: hidden;
}

.hero-bg {
  position: absolute;
  inset: 0;
  overflow: hidden;
}

.hero-circle {
  position: absolute;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.08);
  
  &-1 {
    width: 200px;
    height: 200px;
    top: -60px;
    right: -40px;
    animation: float 6s ease-in-out infinite;
  }
  
  &-2 {
    width: 120px;
    height: 120px;
    bottom: 40px;
    left: -30px;
    animation: float 8s ease-in-out infinite reverse;
  }
}

@keyframes float {
  0%, 100% { transform: translateY(0) rotate(0deg); }
  50% { transform: translateY(-15px) rotate(3deg); }
}

.hero-content {
  position: relative;
  z-index: 1;
}

.hero-badge {
  display: inline-block;
  padding: 8px 18px;
  background: rgba(255, 255, 255, 0.15);
  border-radius: 20px;
  font-size: 12px;
  letter-spacing: 2px;
  margin-bottom: 20px;
  backdrop-filter: blur(10px);
}

.hero-content h1 {
  font-size: 36px;
  font-weight: 700;
  margin-bottom: 12px;
  letter-spacing: 2px;
}

.subtitle {
  font-size: 16px;
  opacity: 0.95;
  margin-bottom: 16px;
  font-weight: 300;
}

.desc {
  font-size: 14px;
  opacity: 0.9;
  line-height: 1.7;
  margin-bottom: 28px;
  max-width: 300px;
  margin-left: auto;
  margin-right: auto;
}

.actions {
  display: flex;
  gap: 12px;
  justify-content: center;
  
  .btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 14px 28px;
    font-size: 15px;
    font-weight: 500;
    border-radius: 30px;
    transition: all 0.3s ease;
    
    &-primary {
      background: #fff;
      color: $primary;
      
      .btn-arrow {
        transition: transform 0.3s;
      }
      
      &:active {
        transform: scale(0.96);
        
        .btn-arrow {
          transform: translateX(4px);
        }
      }
    }
    
    &-outline {
      background: transparent;
      color: #fff;
      border: 1.5px solid rgba(255, 255, 255, 0.6);
      
      &:active {
        background: rgba(255, 255, 255, 0.1);
      }
    }
  }
}

.download-area {
  display: flex;
  justify-content: center;
  gap: 24px;
  margin-top: 36px;
  position: relative;
  z-index: 1;
}

.download-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
  padding: 20px 24px;
  background: rgba(255, 255, 255, 0.12);
  border-radius: 16px;
  backdrop-filter: blur(10px);
  transition: all 0.3s ease;
  
  &:active {
    transform: scale(0.96);
    background: rgba(255, 255, 255, 0.18);
  }
  
  .icon {
    width: 52px;
    height: 52px;
    background: rgba(255, 255, 255, 0.15);
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  
  span {
    font-size: 14px;
    font-weight: 500;
  }
  
  p {
    font-size: 11px;
    opacity: 0.7;
  }
}

.float-animation {
  animation: iconFloat 3s ease-in-out infinite;
}

@keyframes iconFloat {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-4px); }
}

.hero-wave {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  
  svg {
    display: block;
    width: 100%;
    height: 30px;
  }
}

// ========== Section Common ==========
.section-header {
  text-align: center;
  margin-bottom: 24px;
}

.section-tag {
  font-size: 11px;
  color: $primary;
  letter-spacing: 2px;
  margin-bottom: 8px;
  font-weight: 500;
  
  &.light {
    color: rgba(255, 255, 255, 0.8);
  }
}

.section-header h2 {
  font-size: 22px;
  font-weight: 600;
  color: $text-primary;
}

// ========== Features Section ==========
.features {
  padding: 40px 20px;
  background: #fff;
}

.feature-list {
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.feature-item {
  display: flex;
  align-items: center;
  gap: 16px;
  background: $bg-light;
  padding: 20px;
  border-radius: 16px;
  position: relative;
  overflow: hidden;
  transition: all 0.3s ease;
  
  &:active {
    transform: scale(0.98);
    background: $primary-light;
  }
}

.icon-wrapper {
  flex-shrink: 0;
}

.feature-item .icon {
  width: 56px;
  height: 56px;
  background: $primary-light;
  border-radius: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: $primary;
  transition: all 0.4s ease;
}

.feature-item:active .icon {
  background: $primary;
  color: #fff;
  transform: scale(1.05);
}

.feature-item .info {
  flex: 1;
  
  h3 {
    font-size: 16px;
    font-weight: 600;
    color: $text-primary;
    margin-bottom: 4px;
  }
  
  p {
    font-size: 13px;
    color: $text-secondary;
    line-height: 1.5;
  }
}

.hover-indicator {
  position: absolute;
  bottom: 0;
  left: 0;
  width: 0;
  height: 3px;
  background: $primary;
  border-radius: 2px;
  transition: width 0.4s ease;
}

.feature-item:active .hover-indicator {
  width: 100%;
}

// ========== Stats Section ==========
.stats-section {
  padding: 40px 20px;
  background: linear-gradient(135deg, $primary 0%, #10b981 100%);
  
  .section-header h2 {
    color: #fff;
  }
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 14px;
}

.stat-item {
  background: rgba(255, 255, 255, 0.12);
  backdrop-filter: blur(10px);
  border-radius: 16px;
  padding: 24px 16px;
  text-align: center;
  color: #fff;
  position: relative;
  overflow: hidden;
  transition: all 0.3s ease;
  
  &:active {
    transform: scale(0.96);
    background: rgba(255, 255, 255, 0.2);
  }
}

.stat-item .value {
  font-size: 32px;
  font-weight: 700;
  margin-bottom: 6px;
}

.stat-item .label {
  font-size: 13px;
  opacity: 0.9;
}

.stat-bar {
  position: absolute;
  bottom: 0;
  left: 0;
  width: 100%;
  height: 3px;
  background: rgba(255, 255, 255, 0.4);
  transform: scaleX(0);
  transform-origin: left;
  transition: transform 0.5s ease;
}

.stat-item:active .stat-bar {
  transform: scaleX(1);
}

// ========== CTA Section ==========
.cta {
  margin: 20px;
  padding: 28px 24px;
  background: linear-gradient(135deg, $primary 0%, #10b981 100%);
  border-radius: 20px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  color: #fff;
}

.cta-content {
  h3 {
    font-size: 18px;
    font-weight: 600;
    margin-bottom: 6px;
  }
  
  p {
    font-size: 13px;
    opacity: 0.9;
  }
}

.cta-btn {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 12px 24px;
  background: #fff;
  color: $primary;
  font-size: 14px;
  font-weight: 600;
  border-radius: 25px;
  transition: all 0.3s ease;
  
  span {
    transition: transform 0.3s;
  }
  
  &:active {
    transform: scale(0.96);
    
    span {
      transform: translateX(4px);
    }
  }
}

// ========== Animations ==========
.animate-on-scroll {
  opacity: 0;
  transform: translateY(30px);
  transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
  
  &.animated {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-scale {
  transform: scale(0.9);
  
  &.animated {
    transform: scale(1);
  }
}

@for $i from 1 through 6 {
  .animate-delay-#{$i} {
    transition-delay: #{$i * 0.1}s;
  }
}

.breathe-hover {
  &.animated {
    animation: breathe 3s ease-in-out infinite;
    animation-delay: 1s;
  }
}

@keyframes breathe {
  0%, 100% { box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05); }
  50% { box-shadow: 0 8px 25px rgba(7, 193, 96, 0.12); }
}
</style>
