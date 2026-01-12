<template>
  <div class="history">
    <!-- Hero Banner -->
    <section class="hero">
      <div class="hero-content">
        <p class="hero-subtitle">DEVELOPMENT HISTORY</p>
        <h1 class="hero-title">发展历程</h1>
        <p class="hero-desc">见证轻养到家的成长足迹</p>
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
        <div class="timeline">
          <div 
            class="timeline-item" 
            v-for="(item, index) in timeline" 
            :key="item.year"
            :class="{ reverse: index % 2 === 1 }"
          >
            <div class="timeline-dot">
              <span class="dot-inner"></span>
            </div>
            <div class="timeline-content">
              <div class="timeline-year">{{ item.year }}</div>
              <div class="timeline-card">
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
        <div class="section-header">
          <p class="section-subtitle">FUTURE VISION</p>
          <h2 class="section-title">未来展望</h2>
        </div>
        <div class="vision-grid">
          <div class="vision-card" v-for="(vision, index) in visions" :key="index">
            <div class="vision-icon">
              <component :is="vision.icon" size="32px" />
            </div>
            <h3>{{ vision.title }}</h3>
            <p>{{ vision.desc }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- 核心理念 -->
    <section class="section belief">
      <div class="container">
        <div class="belief-card">
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
  padding: 120px 40px 160px;
  
  &-content { position: relative; z-index: 1; }
  &-subtitle { font-size: 14px; letter-spacing: 4px; opacity: 0.9; margin-bottom: 20px; }
  &-title { font-size: 56px; font-weight: 700; margin-bottom: 24px; line-height: 1.2; }
  &-desc { font-size: 20px; opacity: 0.9; }
  
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

.timeline {
  position: relative;
  max-width: 1000px;
  margin: 0 auto;
  padding: 40px 0;
  
  &::before {
    content: '';
    position: absolute;
    left: 50%;
    top: 0;
    bottom: 0;
    width: 3px;
    background: linear-gradient(to bottom, $primary-color, #10b981);
    transform: translateX(-50%);
    border-radius: 2px;
  }
}

.timeline-item {
  position: relative;
  display: flex;
  justify-content: flex-end;
  padding: 30px 0;
  
  &.reverse {
    justify-content: flex-start;
    
    .timeline-content {
      padding-left: calc(50% + 60px);
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
  top: 40px;
  width: 24px;
  height: 24px;
  background: #fff;
  border: 3px solid $primary-color;
  border-radius: 50%;
  transform: translateX(-50%);
  z-index: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  
  .dot-inner {
    width: 10px;
    height: 10px;
    background: $primary-color;
    border-radius: 50%;
  }
}

.timeline-content {
  width: 100%;
  padding-right: calc(50% + 60px);
  text-align: right;
}

.timeline-year {
  font-size: 48px;
  font-weight: 700;
  color: $primary-light;
  margin-bottom: 16px;
  line-height: 1;
}

.timeline-card {
  background: #fff;
  padding: 32px;
  border-radius: 20px;
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
  text-align: left;
}

.card-header {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 20px;
  
  h3 {
    font-size: 24px;
    font-weight: 600;
    color: $text-primary;
  }
}

.card-badge {
  padding: 4px 12px;
  background: $primary-light;
  color: $primary-color;
  font-size: 12px;
  font-weight: 500;
  border-radius: 12px;
}

.timeline-events {
  list-style: none;
  padding: 0;
  margin: 0;
  
  li {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 14px;
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

.vision-icon {
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

.vision-card h3 {
  font-size: 22px;
  font-weight: 600;
  color: $text-primary;
  margin-bottom: 12px;
}

.vision-card p {
  font-size: 15px;
  color: $text-secondary;
  line-height: 1.7;
}

.belief-card {
  max-width: 800px;
  margin: 0 auto;
  text-align: center;
  background: linear-gradient(135deg, $primary-color 0%, #10b981 100%);
  border-radius: 24px;
  padding: 60px 48px;
  color: #fff;
}

.belief-quote {
  font-size: 80px;
  font-family: Georgia, serif;
  line-height: 0.5;
  opacity: 0.3;
  margin-bottom: 20px;
}

.belief-text {
  font-size: 36px;
  font-weight: 700;
  margin-bottom: 20px;
}

.belief-desc {
  font-size: 16px;
  opacity: 0.9;
  line-height: 1.8;
  max-width: 500px;
  margin: 0 auto;
}
</style>
