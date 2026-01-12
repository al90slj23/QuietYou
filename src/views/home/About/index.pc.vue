<template>
  <div class="about">
    <!-- Hero Banner -->
    <section class="hero">
      <div class="hero-content">
        <p class="hero-subtitle">ABOUT US</p>
        <h1 class="hero-title">关于轻养到家</h1>
        <p class="hero-desc">用科技传递健康，让专业养生服务触手可及</p>
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
        <div class="section-header">
          <p class="section-subtitle">INTRODUCTION</p>
          <h2 class="section-title">公司简介</h2>
        </div>
        <div class="intro-content">
          <div class="intro-text">
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
        <div class="section-header">
          <p class="section-subtitle">WHAT WE DO</p>
          <h2 class="section-title">轻养到家是什么</h2>
        </div>
        <div class="feature-grid">
          <div class="feature-card" v-for="(feature, index) in features" :key="index">
            <div class="feature-icon">
              <component :is="feature.icon" size="36px" />
            </div>
            <h3 class="feature-title">{{ feature.title }}</h3>
            <p class="feature-desc">{{ feature.desc }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- 数据展示 -->
    <section class="section stats">
      <div class="container">
        <div class="section-header">
          <p class="section-subtitle">OUR ACHIEVEMENTS</p>
          <h2 class="section-title">专业服务</h2>
        </div>
        <div class="stats-grid">
          <div class="stat-card" v-for="(stat, index) in stats" :key="index">
            <div class="stat-number">{{ stat.value }}</div>
            <div class="stat-label">{{ stat.label }}</div>
            <div class="stat-line"></div>
          </div>
        </div>
      </div>
    </section>

    <!-- 企业价值观 -->
    <section class="section values">
      <div class="container">
        <div class="section-header">
          <p class="section-subtitle">OUR VALUES</p>
          <h2 class="section-title">企业价值观</h2>
        </div>
        <div class="values-grid">
          <div class="value-item" v-for="(value, index) in values" :key="index">
            <div class="value-number">0{{ index + 1 }}</div>
            <h3 class="value-title">{{ value.title }}</h3>
            <p class="value-desc">{{ value.desc }}</p>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { UserIcon, ShopIcon, SecuredIcon, HeartIcon } from 'tdesign-icons-vue-next'

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
  padding: 120px 40px 160px;
  
  &-content { position: relative; z-index: 1; }
  &-subtitle { font-size: 14px; letter-spacing: 4px; opacity: 0.9; margin-bottom: 20px; }
  &-title { font-size: 56px; font-weight: 700; margin-bottom: 24px; line-height: 1.2; }
  &-desc { font-size: 20px; opacity: 0.9; max-width: 600px; margin: 0 auto; }
  
  &-wave {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    svg { display: block; width: 100%; height: 80px; }
  }
}

.section {
  padding: 100px 0;
  
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
  margin-bottom: 60px;
}

.section-subtitle {
  font-size: 14px;
  color: $primary-color;
  letter-spacing: 3px;
  margin-bottom: 12px;
}

.section-title {
  font-size: 40px;
  font-weight: 600;
  color: $text-primary;
}

.intro-content {
  max-width: 900px;
  margin: 0 auto;
}

.intro-text {
  p {
    font-size: 17px;
    line-height: 2;
    color: $text-secondary;
    margin-bottom: 24px;
    text-align: justify;
    
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
  border-radius: 20px;
  padding: 48px 36px;
  text-align: center;
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.06);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  
  &:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
  }
}

.feature-icon {
  width: 80px;
  height: 80px;
  margin: 0 auto 24px;
  background: $primary-light;
  border-radius: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: $primary-color;
}

.feature-title {
  font-size: 22px;
  font-weight: 600;
  color: $text-primary;
  margin-bottom: 16px;
}

.feature-desc {
  font-size: 15px;
  color: $text-secondary;
  line-height: 1.7;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 32px;
}

.stat-card {
  background: #fff;
  border-radius: 20px;
  padding: 48px 24px;
  text-align: center;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
  position: relative;
  overflow: hidden;
}

.stat-number {
  font-size: 48px;
  font-weight: 700;
  color: $primary-color;
  margin-bottom: 12px;
}

.stat-label {
  font-size: 16px;
  color: $text-secondary;
}

.stat-line {
  position: absolute;
  bottom: 0;
  left: 50%;
  transform: translateX(-50%);
  width: 60px;
  height: 4px;
  background: $primary-color;
  border-radius: 2px;
}

.values-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 32px;
}

.value-item {
  text-align: center;
  padding: 40px 24px;
}

.value-number {
  font-size: 48px;
  font-weight: 700;
  color: $primary-light;
  margin-bottom: 16px;
  line-height: 1;
}

.value-title {
  font-size: 20px;
  font-weight: 600;
  color: $text-primary;
  margin-bottom: 12px;
}

.value-desc {
  font-size: 15px;
  color: $text-secondary;
  line-height: 1.7;
}
</style>
