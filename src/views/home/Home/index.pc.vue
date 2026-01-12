<template>
  <div class="home">
    <!-- Hero Section -->
    <section class="hero">
      <div class="hero-bg">
        <div class="hero-circle hero-circle-1"></div>
        <div class="hero-circle hero-circle-2"></div>
        <div class="hero-circle hero-circle-3"></div>
      </div>
      <div class="container">
        <div class="hero-content">
          <p class="hero-badge animate-on-scroll">专业 · 安全 · 便捷</p>
          <h1 class="hero-title animate-on-scroll animate-delay-1">轻养到家</h1>
          <p class="hero-subtitle animate-on-scroll animate-delay-2">专业上门养生推拿服务平台</p>
          <p class="hero-desc animate-on-scroll animate-delay-3">
            我们致力于"让专业养生服务触手可及"，为用户带来更健康、更舒适的生活体验。
          </p>
          <div class="hero-actions animate-on-scroll animate-delay-4">
            <a href="#download" class="btn-hero-primary">
              立即预约
              <span class="btn-arrow">→</span>
            </a>
            <router-link to="/home/about" class="btn-hero-outline">了解更多</router-link>
          </div>
        </div>
      </div>
      <div class="hero-download" id="download">
        <div class="download-card animate-on-scroll animate-delay-5">
          <div class="download-icon float-animation"><MobileIcon size="32px" /></div>
          <span>微信小程序</span>
          <p>扫码预约服务</p>
        </div>
        <div class="download-card animate-on-scroll animate-delay-6">
          <div class="download-icon float-animation"><LogoWechatStrokeIcon size="32px" /></div>
          <span>微信公众号</span>
          <p>关注获取资讯</p>
        </div>
      </div>
      <div class="hero-wave">
        <svg viewBox="0 0 1440 120" preserveAspectRatio="none">
          <path d="M0,60 C360,120 1080,0 1440,60 L1440,120 L0,120 Z" fill="#fff"/>
        </svg>
      </div>
    </section>

    <!-- Features Section -->
    <section class="features section">
      <div class="container">
        <div class="section-header animate-on-scroll">
          <p class="section-subtitle">WHY CHOOSE US</p>
          <h2 class="section-title">为什么选择轻养到家</h2>
          <p class="section-desc">专业服务，品质保障</p>
        </div>
        <div class="features-grid">
          <div 
            class="feature-card animate-on-scroll breathe-hover" 
            v-for="(feature, index) in features" 
            :key="feature.title"
            :class="`animate-delay-${index + 1}`"
          >
            <div class="feature-icon-wrapper">
              <div class="feature-icon">
                <component :is="feature.icon" size="36px" />
              </div>
            </div>
            <h3>{{ feature.title }}</h3>
            <p>{{ feature.desc }}</p>
            <div class="feature-hover-line"></div>
          </div>
        </div>
      </div>
    </section>

    <!-- Stats Section -->
    <section class="stats section">
      <div class="container">
        <div class="stats-content">
          <div class="stats-intro animate-on-scroll animate-slide-left">
            <p class="section-subtitle">OUR ACHIEVEMENTS</p>
            <h2 class="section-title">数据见证实力</h2>
            <p class="stats-desc">
              轻养到家自上线以来，已服务数十万用户，覆盖全国多个城市，
              获得用户的一致好评与信赖。
            </p>
          </div>
          <div class="stats-grid">
            <div 
              class="stat-card animate-on-scroll" 
              v-for="(stat, index) in stats" 
              :key="stat.label"
              :class="`animate-delay-${index + 1}`"
            >
              <div class="stat-number">{{ stat.value }}</div>
              <div class="stat-label">{{ stat.label }}</div>
              <div class="stat-bar"></div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="cta section">
      <div class="container">
        <div class="cta-card animate-on-scroll animate-scale">
          <div class="cta-content">
            <h2>开启您的健康之旅</h2>
            <p>专业技师，上门服务，让健康触手可及</p>
          </div>
          <div class="cta-actions">
            <router-link to="/user" class="cta-btn">
              立即体验
              <span>→</span>
            </router-link>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { CertificateIcon, SecuredIcon, TimeIcon, StarIcon, MobileIcon, LogoWechatStrokeIcon } from 'tdesign-icons-vue-next'
import { useScrollAnimation } from '@/composables/useScrollAnimation'

useScrollAnimation()

const features = [
  { icon: CertificateIcon, title: '专业认证', desc: '所有技师均经过严格培训和资质认证，持证上岗，专业可靠' },
  { icon: SecuredIcon, title: '安全保障', desc: '全程服务可追溯，平台担保交易，让您放心享受每一次服务' },
  { icon: TimeIcon, title: '随叫随到', desc: '24小时在线预约，快速响应，专业技师准时上门服务' },
  { icon: StarIcon, title: '品质服务', desc: '标准化服务流程，专业手法技艺，效果显著有保障' }
]

const stats = [
  { value: '10万+', label: '服务用户' },
  { value: '1000+', label: '认证技师' },
  { value: '20+', label: '覆盖城市' },
  { value: '99%', label: '好评率' }
]
</script>

<style lang="scss" scoped>
$primary-color: #07c160;
$primary-light: #e8f8ef;
$primary-dark: #06a050;
$text-primary: #1a1a1a;
$text-secondary: #666666;
$background-light: #f8f9fa;

.hero {
  position: relative;
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  color: #fff;
  text-align: center;
  padding: 120px 0 160px;
  background: linear-gradient(135deg, $primary-color 0%, #10b981 50%, #059669 100%);
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
  background: rgba(255, 255, 255, 0.06);
  
  &-1 {
    width: 600px;
    height: 600px;
    top: -200px;
    right: -100px;
    animation: float 8s ease-in-out infinite;
  }
  
  &-2 {
    width: 400px;
    height: 400px;
    bottom: -100px;
    left: -100px;
    animation: float 10s ease-in-out infinite reverse;
  }
  
  &-3 {
    width: 200px;
    height: 200px;
    top: 40%;
    left: 10%;
    animation: float 6s ease-in-out infinite;
  }
}

@keyframes float {
  0%, 100% { transform: translateY(0) rotate(0deg); }
  50% { transform: translateY(-30px) rotate(5deg); }
}

.hero-content {
  position: relative;
  z-index: 1;
  max-width: 800px;
  margin: 0 auto;
  padding: 0 20px;
}

.hero-badge {
  display: inline-block;
  padding: 10px 24px;
  background: rgba(255, 255, 255, 0.15);
  border-radius: 30px;
  font-size: 14px;
  letter-spacing: 2px;
  margin-bottom: 32px;
  backdrop-filter: blur(10px);
}

.hero-title {
  font-size: 72px;
  font-weight: 700;
  margin-bottom: 20px;
  letter-spacing: 4px;
}

.hero-subtitle {
  font-size: 26px;
  opacity: 0.95;
  margin-bottom: 24px;
  font-weight: 300;
}

.hero-desc {
  font-size: 18px;
  line-height: 1.8;
  opacity: 0.9;
  margin-bottom: 48px;
  max-width: 600px;
  margin-left: auto;
  margin-right: auto;
}

.hero-actions {
  display: flex;
  gap: 20px;
  justify-content: center;
}

.btn-hero-primary {
  display: inline-flex;
  align-items: center;
  gap: 12px;
  padding: 18px 40px;
  background: #fff;
  color: $primary-color;
  font-size: 17px;
  font-weight: 600;
  border-radius: 50px;
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  
  .btn-arrow {
    transition: transform 0.3s;
  }
  
  &:hover {
    transform: translateY(-4px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
    
    .btn-arrow {
      transform: translateX(6px);
    }
  }
}

.btn-hero-outline {
  display: inline-flex;
  align-items: center;
  padding: 18px 40px;
  background: transparent;
  color: #fff;
  font-size: 17px;
  font-weight: 500;
  border: 2px solid rgba(255, 255, 255, 0.5);
  border-radius: 50px;
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  
  &:hover {
    background: #fff;
    color: $primary-color;
    border-color: #fff;
    transform: translateY(-4px);
  }
}

.hero-download {
  position: relative;
  z-index: 1;
  display: flex;
  gap: 32px;
  margin-top: 80px;
}

.download-card {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 12px;
  padding: 28px 36px;
  background: rgba(255, 255, 255, 0.1);
  border-radius: 20px;
  backdrop-filter: blur(10px);
  cursor: pointer;
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  
  &:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: translateY(-8px);
  }
  
  .download-icon {
    width: 72px;
    height: 72px;
    background: rgba(255, 255, 255, 0.15);
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  
  span {
    font-size: 16px;
    font-weight: 500;
  }
  
  p {
    font-size: 13px;
    opacity: 0.7;
  }
}

.hero-wave {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  
  svg {
    display: block;
    width: 100%;
    height: 100px;
  }
}

.section {
  padding: 120px 0;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 40px;
}

.section-header {
  text-align: center;
  margin-bottom: 80px;
}

.section-subtitle {
  font-size: 14px;
  color: $primary-color;
  letter-spacing: 4px;
  margin-bottom: 16px;
  font-weight: 500;
}

.section-title {
  font-size: 44px;
  font-weight: 600;
  color: $text-primary;
  margin-bottom: 16px;
}

.section-desc {
  font-size: 18px;
  color: $text-secondary;
}

.features {
  background: $background-light;
}

.features-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 32px;
}

.feature-card {
  background: #fff;
  padding: 48px 32px;
  border-radius: 24px;
  text-align: center;
  position: relative;
  overflow: hidden;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
}

.feature-icon-wrapper {
  margin-bottom: 28px;
}

.feature-icon {
  width: 88px;
  height: 88px;
  margin: 0 auto;
  background: $primary-light;
  border-radius: 28px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: $primary-color;
  transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
}

.feature-card:hover .feature-icon {
  background: $primary-color;
  color: #fff;
  transform: scale(1.1) rotate(5deg);
}

.feature-card h3 {
  font-size: 22px;
  font-weight: 600;
  color: $text-primary;
  margin-bottom: 14px;
}

.feature-card p {
  font-size: 15px;
  color: $text-secondary;
  line-height: 1.7;
}

.feature-hover-line {
  position: absolute;
  bottom: 0;
  left: 50%;
  transform: translateX(-50%);
  width: 0;
  height: 4px;
  background: $primary-color;
  border-radius: 2px;
  transition: width 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.feature-card:hover .feature-hover-line {
  width: 60px;
}

.stats {
  background: #fff;
}

.stats-content {
  display: grid;
  grid-template-columns: 1fr 1.5fr;
  gap: 80px;
  align-items: center;
}

.stats-intro {
  .section-subtitle { text-align: left; }
  .section-title { text-align: left; font-size: 40px; }
}

.stats-desc {
  font-size: 17px;
  color: $text-secondary;
  line-height: 1.9;
  margin-top: 24px;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 32px;
}

.stat-card {
  background: $background-light;
  border-radius: 24px;
  padding: 40px 32px;
  text-align: center;
  position: relative;
  overflow: hidden;
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  
  &:hover {
    background: $primary-light;
    transform: translateY(-8px);
  }
}

.stat-number {
  font-size: 48px;
  font-weight: 700;
  color: $primary-color;
  margin-bottom: 10px;
}

.stat-label {
  font-size: 16px;
  color: $text-secondary;
}

.stat-bar {
  position: absolute;
  bottom: 0;
  left: 0;
  width: 100%;
  height: 4px;
  background: linear-gradient(90deg, $primary-color, #10b981);
  transform: scaleX(0);
  transform-origin: left;
  transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
}

.stat-card:hover .stat-bar {
  transform: scaleX(1);
}

.cta {
  background: $background-light;
  padding: 100px 0;
}

.cta-card {
  background: linear-gradient(135deg, $primary-color 0%, #10b981 100%);
  border-radius: 32px;
  padding: 80px 60px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  color: #fff;
}

.cta-content {
  h2 {
    font-size: 36px;
    font-weight: 600;
    margin-bottom: 16px;
  }
  
  p {
    font-size: 18px;
    opacity: 0.9;
  }
}

.cta-btn {
  display: inline-flex;
  align-items: center;
  gap: 12px;
  padding: 20px 48px;
  background: #fff;
  color: $primary-color;
  font-size: 18px;
  font-weight: 600;
  border-radius: 50px;
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  
  span {
    transition: transform 0.3s;
  }
  
  &:hover {
    transform: translateY(-4px) scale(1.02);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
    
    span {
      transform: translateX(6px);
    }
  }
}
</style>
