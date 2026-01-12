<template>
  <div class="history">
    <!-- Hero Banner -->
    <section class="hero">
      <div class="hero-bg">
        <div class="hero-circle hero-circle-1"></div>
        <div class="hero-circle hero-circle-2"></div>
      </div>
      <div class="hero-content">
        <p class="hero-subtitle animate-on-scroll">DEVELOPMENT HISTORY</p>
        <h1 class="hero-title animate-on-scroll animate-delay-1">发展历程</h1>
        <p class="hero-desc animate-on-scroll animate-delay-2">见证轻养到家的成长足迹</p>
      </div>
      <div class="hero-wave">
        <svg viewBox="0 0 1440 120" preserveAspectRatio="none">
          <path d="M0,60 C360,120 1080,0 1440,60 L1440,120 L0,120 Z" fill="#fff"/>
        </svg>
      </div>
    </section>

    <!-- 时间线 -->
    <section class="section timeline-section">
      <div class="container">
        <div class="timeline-header">
          <div class="timeline-intro animate-on-scroll">
            <p class="section-subtitle">OUR JOURNEY</p>
            <h2 class="section-title">发展历程</h2>
            <p class="timeline-desc">从创立到成长，每一步都凝聚着我们对健康服务的执着追求</p>
          </div>
          <div class="timeline-image animate-on-scroll animate-delay-1">
            <img src="https://images.pexels.com/photos/6187650/pexels-photo-6187650.jpeg?auto=compress&cs=tinysrgb&w=600&h=400&fit=crop" alt="专业养生服务" loading="lazy" />
          </div>
        </div>
        <div class="timeline">
          <div 
            class="timeline-item animate-on-scroll" 
            v-for="(item, index) in timeline" 
            :key="item.year"
            :class="[{ reverse: index % 2 === 1 }, `animate-delay-${index + 1}`]"
          >
            <div class="timeline-dot">
              <span class="dot-inner"></span>
            </div>
            <div class="timeline-content">
              <div class="timeline-year">{{ item.year }}</div>
              <div class="timeline-card breathe-hover">
                <div class="card-header">
                  <h3>{{ item.title }}</h3>
                  <span class="card-badge" v-if="item.badge">{{ item.badge }}</span>
                </div>
                <ul class="timeline-events">
                  <li v-for="event in item.events" :key="event">
                    <CheckCircleIcon size="16px" />
                    <span>{{ event }}</span>
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- 未来展望 -->
    <section class="section vision">
      <div class="container">
        <div class="section-header animate-on-scroll">
          <p class="section-subtitle">FUTURE VISION</p>
          <h2 class="section-title">未来展望</h2>
        </div>
        <div class="vision-grid">
          <div 
            class="vision-card animate-on-scroll breathe-hover" 
            v-for="(vision, index) in visions" 
            :key="index"
            :class="`animate-delay-${index + 1}`"
          >
            <div class="vision-icon">
              <component :is="vision.icon" size="32px" />
            </div>
            <h3>{{ vision.title }}</h3>
            <p>{{ vision.desc }}</p>
            <div class="vision-line"></div>
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
import { CheckCircleIcon, RocketIcon, MapIcon, StarIcon } from 'tdesign-icons-vue-next'
import { useScrollAnimation } from '@/composables/useScrollAnimation'

useScrollAnimation()

const timeline = [
  { 
    year: '2026', 
    title: '扬帆起航', 
    badge: '当前',
    events: [
      '轻养到家平台正式上线',
      '首批认证技师入驻平台',
      '开放首批合作城市',
      '建立技师培训认证体系'
    ] 
  },
  { 
    year: '2027', 
    title: '稳步发展',
    badge: '规划中',
    events: [
      '计划覆盖20+城市',
      '深化商户合作模式',
      '完善多维度评分体系',
      '推出技师职业发展通道'
    ] 
  },
  { 
    year: '2028', 
    title: '全面拓展',
    badge: '规划中',
    events: [
      '计划覆盖50+城市',
      '建立区域培训中心',
      '打造行业服务标准',
      '构建完整生态体系'
    ] 
  }
]

const visions = [
  { icon: MapIcon, title: '全国覆盖', desc: '三年内覆盖全国50+城市，让更多用户享受专业服务' },
  { icon: StarIcon, title: '行业标杆', desc: '建立完善的技师认证培训体系，打造行业服务标准' },
  { icon: RocketIcon, title: '持续创新', desc: '通过技术创新和服务升级，成为行业领先平台' }
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
  min-height: 60vh;
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
    width: 400px;
    height: 400px;
    top: -100px;
    right: -50px;
    animation: float 8s ease-in-out infinite;
  }
  
  &-2 {
    width: 250px;
    height: 250px;
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
.hero-desc { font-size: 22px; opacity: 0.9; }

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

.timeline-header {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 60px;
  align-items: center;
  margin-bottom: 80px;
}

.timeline-intro {
  .section-subtitle { text-align: left; }
  .section-title { text-align: left; margin-bottom: 20px; }
}

.timeline-desc {
  font-size: 17px;
  color: $text-secondary;
  line-height: 1.8;
}

.timeline-image {
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

.timeline {
  position: relative;
  max-width: 1000px;
  margin: 0 auto;
  padding: 60px 0;
  
  &::before {
    content: '';
    position: absolute;
    left: 50%;
    top: 0;
    bottom: 0;
    width: 4px;
    background: linear-gradient(to bottom, $primary-color, #10b981);
    transform: translateX(-50%);
    border-radius: 2px;
  }
}

.timeline-item {
  position: relative;
  display: flex;
  justify-content: flex-end;
  padding: 40px 0;
  
  &.reverse {
    justify-content: flex-start;
    
    .timeline-content {
      padding-left: calc(50% + 70px);
      padding-right: 0;
      text-align: left;
    }
    
    .timeline-year {
      text-align: left;
    }
    
    .timeline-card {
      text-align: left;
    }
  }
}

.timeline-dot {
  position: absolute;
  left: 50%;
  top: 50px;
  width: 28px;
  height: 28px;
  background: #fff;
  border: 4px solid $primary-color;
  border-radius: 50%;
  transform: translateX(-50%);
  z-index: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  
  .dot-inner {
    width: 12px;
    height: 12px;
    background: $primary-color;
    border-radius: 50%;
    transition: transform 0.4s;
  }
}

.timeline-item:hover .timeline-dot {
  transform: translateX(-50%) scale(1.2);
  
  .dot-inner {
    transform: scale(1.2);
  }
}

.timeline-content {
  width: 100%;
  padding-right: calc(50% + 70px);
  text-align: right;
}

.timeline-year {
  font-size: 56px;
  font-weight: 700;
  color: $primary-light;
  margin-bottom: 20px;
  line-height: 1;
  transition: color 0.4s;
}

.timeline-item:hover .timeline-year {
  color: $primary-color;
}

.timeline-card {
  background: #fff;
  padding: 36px;
  border-radius: 24px;
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
  text-align: left;
}

.card-header {
  display: flex;
  align-items: center;
  gap: 14px;
  margin-bottom: 24px;
  
  h3 {
    font-size: 26px;
    font-weight: 600;
    color: $text-primary;
  }
}

.card-badge {
  padding: 6px 16px;
  background: $primary-light;
  color: $primary-color;
  font-size: 13px;
  font-weight: 600;
  border-radius: 16px;
}

.timeline-events {
  list-style: none;
  padding: 0;
  margin: 0;
  
  li {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 16px;
    font-size: 15px;
    color: $text-secondary;
    line-height: 1.5;
    
    svg {
      color: $primary-color;
      flex-shrink: 0;
    }
    
    &:last-child {
      margin-bottom: 0;
    }
  }
}

.vision-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 32px;
}

.vision-card {
  background: #fff;
  border-radius: 24px;
  padding: 56px 40px;
  text-align: center;
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.06);
  position: relative;
  overflow: hidden;
}

.vision-icon {
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

.vision-card:hover .vision-icon {
  background: $primary-color;
  color: #fff;
  transform: scale(1.1) rotate(5deg);
}

.vision-card h3 {
  font-size: 24px;
  font-weight: 600;
  color: $text-primary;
  margin-bottom: 14px;
}

.vision-card p {
  font-size: 15px;
  color: $text-secondary;
  line-height: 1.7;
}

.vision-line {
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

.vision-card:hover .vision-line {
  width: 60px;
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
  margin-bottom: 28px;
}

.belief-text {
  font-size: 44px;
  font-weight: 700;
  margin-bottom: 28px;
}

.belief-desc {
  font-size: 18px;
  opacity: 0.9;
  line-height: 1.8;
  max-width: 600px;
  margin: 0 auto;
}
</style>
