<template>
  <div class="about">
    <!-- Hero Banner -->
    <section class="hero">
      <div class="hero-bg">
        <div class="hero-circle hero-circle-1"></div>
        <div class="hero-circle hero-circle-2"></div>
      </div>
      <div class="hero-content">
        <p class="hero-subtitle animate-on-scroll">ABOUT US</p>
        <h1 class="hero-title animate-on-scroll animate-delay-1">关于轻养到家</h1>
        <p class="hero-desc animate-on-scroll animate-delay-2">用科技传递健康，让专业养生服务触手可及</p>
      </div>
      <div class="hero-wave">
        <svg viewBox="0 0 1440 120" preserveAspectRatio="none">
          <path d="M0,60 C360,120 1080,0 1440,60 L1440,120 L0,120 Z" fill="#fff"/>
        </svg>
      </div>
    </section>

    <!-- 公司简介 -->
    <section class="section intro">
      <div class="container">
        <div class="section-header animate-on-scroll">
          <p class="section-subtitle">INTRODUCTION</p>
          <h2 class="section-title">公司简介</h2>
        </div>
        <div class="intro-content">
          <div class="intro-image animate-on-scroll">
            <img src="https://images.pexels.com/photos/5473184/pexels-photo-5473184.jpeg?auto=compress&cs=tinysrgb&w=800&h=600&fit=crop" alt="专业养生服务" loading="lazy" />
          </div>
          <div class="intro-text animate-on-scroll animate-delay-1">
            <p>轻养到家是一家专注于上门养生推拿服务的O2O平台，致力于"让专业养生服务触手可及，为用户带来更健康、更舒适的生活体验"。</p>
            <p>我们秉承"用科技传递健康"的战略愿景，通过不断迭代的互联网系统、严格的服务培训及可追溯的服务品质，与众多专业技师一同为消费者提供便捷、安全、高效的健康服务。</p>
            <p>轻养到家始终以用户需求为中心，合规为先，注重技术研发与服务创新，不断提升服务体验。</p>
          </div>
        </div>
      </div>
    </section>

    <!-- 我们做什么 -->
    <section class="section what-we-do">
      <div class="container">
        <div class="section-header animate-on-scroll">
          <p class="section-subtitle">WHAT WE DO</p>
          <h2 class="section-title">轻养到家是什么</h2>
        </div>
        <div class="feature-grid">
          <div 
            class="feature-card animate-on-scroll breathe-hover" 
            v-for="(feature, index) in features" 
            :key="index"
            :class="`animate-delay-${index + 1}`"
          >
            <div class="feature-icon">
              <component :is="feature.icon" size="36px" />
            </div>
            <h3 class="feature-title">{{ feature.title }}</h3>
            <p class="feature-desc">{{ feature.desc }}</p>
            <div class="feature-line"></div>
          </div>
        </div>
      </div>
    </section>

    <!-- 数据展示 -->
    <section class="section stats">
      <div class="container">
        <div class="section-header animate-on-scroll">
          <p class="section-subtitle">OUR ACHIEVEMENTS</p>
          <h2 class="section-title">专业服务</h2>
        </div>
        <div class="stats-grid">
          <div 
            class="stat-card animate-on-scroll" 
            v-for="(stat, index) in stats" 
            :key="index"
            :class="`animate-delay-${index + 1}`"
          >
            <div class="stat-number">{{ stat.value }}</div>
            <div class="stat-label">{{ stat.label }}</div>
            <div class="stat-bar"></div>
          </div>
        </div>
      </div>
    </section>

    <!-- 企业价值观 -->
    <section class="section values">
      <div class="container">
        <div class="section-header animate-on-scroll">
          <p class="section-subtitle">OUR VALUES</p>
          <h2 class="section-title">企业价值观</h2>
        </div>
        <div class="values-grid">
          <div 
            class="value-item animate-on-scroll" 
            v-for="(value, index) in values" 
            :key="index"
            :class="`animate-delay-${index + 1}`"
          >
            <div class="value-number">0{{ index + 1 }}</div>
            <h3 class="value-title">{{ value.title }}</h3>
            <p class="value-desc">{{ value.desc }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- 核心理念 -->
    <section class="section belief">
      <div class="container">
        <div class="belief-card animate-on-scroll animate-scale">
          <div class="belief-quote">"</div>
          <p class="belief-text">只看手艺，不碰擦边</p>
          <p class="belief-desc">轻养到家始终坚守这一核心理念，专注于为用户提供专业、健康、安全的养生服务</p>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { UserIcon, ShopIcon, SecuredIcon } from 'tdesign-icons-vue-next'
import { useScrollAnimation } from '@/composables/useScrollAnimation'

useScrollAnimation()

const features = [
  { icon: UserIcon, title: '服务用户', desc: '提供O2O上门养生推拿，足不出户静享舒缓与健康' },
  { icon: ShopIcon, title: '服务商户', desc: '提供全方位的专业培训、供应链、接单、通勤和安全体系' },
  { icon: SecuredIcon, title: '合规运营', desc: '始终秉持合法经营理念，坚决抵制违规行为' }
]

const stats = [
  { value: '10万+', label: '服务用户' },
  { value: '1000+', label: '入驻商户' },
  { value: '20+', label: '覆盖城市' },
  { value: '99%', label: '好评率' }
]

const values = [
  { title: '用户至上', desc: '始终以用户需求为中心，提供超越期望的服务体验' },
  { title: '专业品质', desc: '严格的培训体系和服务标准，确保每一次服务的专业性' },
  { title: '科技驱动', desc: '持续技术创新，用科技提升服务效率和用户体验' },
  { title: '合规经营', desc: '坚守法律底线，打造健康安全的服务环境' }
]
</script>

<style lang="scss" scoped>
$primary-color: #07c160;
$primary-light: #e8f8ef;
$text-primary: #1a1a1a;
$text-secondary: #666666;
$background-light: #f8f9fa;

.hero {
  position: relative;
  min-height: 70vh;
  background: linear-gradient(135deg, $primary-color 0%, #10b981 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  text-align: center;
  color: #fff;
  padding: 140px 40px 180px;
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
    width: 500px;
    height: 500px;
    top: -150px;
    right: -100px;
    animation: float 8s ease-in-out infinite;
  }
  
  &-2 {
    width: 300px;
    height: 300px;
    bottom: -50px;
    left: -50px;
    animation: float 10s ease-in-out infinite reverse;
  }
}

@keyframes float {
  0%, 100% { transform: translateY(0) rotate(0deg); }
  50% { transform: translateY(-30px) rotate(5deg); }
}

.hero-content { position: relative; z-index: 1; }
.hero-subtitle { font-size: 14px; letter-spacing: 6px; opacity: 0.9; margin-bottom: 24px; }
.hero-title { font-size: 64px; font-weight: 700; margin-bottom: 24px; line-height: 1.1; }
.hero-desc { font-size: 22px; opacity: 0.9; max-width: 600px; margin: 0 auto; }

.hero-wave {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  svg { display: block; width: 100%; height: 100px; }
}

.section {
  padding: 120px 0;
  
  &:nth-child(odd) { background: #fff; }
  &:nth-child(even) { background: $background-light; }
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
}

.intro-content {
  display: grid;
  grid-template-columns: 1fr 1.2fr;
  gap: 60px;
  align-items: center;
  max-width: 1100px;
  margin: 0 auto;
}

.intro-image {
  border-radius: 24px;
  overflow: hidden;
  box-shadow: 0 20px 50px rgba(0, 0, 0, 0.12);
  
  img {
    width: 100%;
    height: auto;
    display: block;
    transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
  }
  
  &:hover img {
    transform: scale(1.05);
  }
}

.intro-text {
  background: $background-light;
  border-radius: 24px;
  padding: 48px;
  border-left: 4px solid $primary-color;
  
  p {
    font-size: 17px;
    line-height: 2;
    color: $text-secondary;
    margin-bottom: 24px;
    
    &:last-child { margin-bottom: 0; }
  }
}

.feature-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 32px;
}

.feature-card {
  background: #fff;
  border-radius: 24px;
  padding: 48px 36px;
  text-align: center;
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.06);
  position: relative;
  overflow: hidden;
}

.feature-icon {
  width: 88px;
  height: 88px;
  margin: 0 auto 28px;
  background: $primary-light;
  border-radius: 24px;
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

.feature-title {
  font-size: 24px;
  font-weight: 600;
  color: $text-primary;
  margin-bottom: 16px;
}

.feature-desc {
  font-size: 15px;
  color: $text-secondary;
  line-height: 1.7;
}

.feature-line {
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

.feature-card:hover .feature-line {
  width: 60px;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 32px;
}

.stat-card {
  background: #fff;
  border-radius: 24px;
  padding: 48px 24px;
  text-align: center;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
  position: relative;
  overflow: hidden;
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  
  &:hover {
    transform: translateY(-8px);
    background: $primary-light;
  }
}

.stat-number {
  font-size: 52px;
  font-weight: 700;
  color: $primary-color;
  margin-bottom: 12px;
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

.values-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 32px;
}

.value-item {
  text-align: center;
  padding: 40px 24px;
  border-radius: 24px;
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  
  &:hover {
    background: #fff;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
    transform: translateY(-8px);
  }
}

.value-number {
  font-size: 56px;
  font-weight: 700;
  color: $primary-light;
  margin-bottom: 16px;
  line-height: 1;
  transition: color 0.4s;
}

.value-item:hover .value-number {
  color: $primary-color;
}

.value-title {
  font-size: 22px;
  font-weight: 600;
  color: $text-primary;
  margin-bottom: 12px;
}

.value-desc {
  font-size: 15px;
  color: $text-secondary;
  line-height: 1.7;
}

.belief {
  padding: 100px 0;
}

.belief-card {
  max-width: 900px;
  margin: 0 auto;
  text-align: center;
  background: linear-gradient(135deg, $primary-color 0%, #10b981 100%);
  border-radius: 32px;
  padding: 80px 60px;
  color: #fff;
}

.belief-quote {
  font-size: 100px;
  font-family: Georgia, serif;
  line-height: 0.5;
  opacity: 0.3;
  margin-bottom: 24px;
}

.belief-text {
  font-size: 40px;
  font-weight: 700;
  margin-bottom: 24px;
}

.belief-desc {
  font-size: 18px;
  opacity: 0.9;
  line-height: 1.8;
  max-width: 600px;
  margin: 0 auto;
}
</style>
