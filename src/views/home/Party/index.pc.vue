<template>
  <div class="party">
    <!-- Hero Banner -->
    <section class="hero">
      <div class="hero-bg">
        <div class="hero-circle hero-circle-1"></div>
        <div class="hero-circle hero-circle-2"></div>
      </div>
      <div class="hero-content">
        <p class="hero-subtitle animate-on-scroll">PARTY BUILDING</p>
        <h1 class="hero-title animate-on-scroll animate-delay-1">党建动态</h1>
        <p class="hero-desc animate-on-scroll animate-delay-2">坚持党的领导，践行社会责任</p>
      </div>
      <div class="hero-wave">
        <svg viewBox="0 0 1440 120" preserveAspectRatio="none">
          <path d="M0,60 C360,120 1080,0 1440,60 L1440,120 L0,120 Z" fill="#fff"/>
        </svg>
      </div>
    </section>

    <!-- 党建引领 -->
    <section class="section intro">
      <div class="container">
        <div class="section-header animate-on-scroll">
          <p class="section-subtitle">PARTY LEADERSHIP</p>
          <h2 class="section-title">党建引领</h2>
        </div>
        <div class="intro-content">
          <div class="intro-card animate-on-scroll animate-delay-1">
            <p>轻养到家始终坚持党的领导，以习近平新时代中国特色社会主义思想为指导，将党建工作与企业发展深度融合，充分发挥党组织的战斗堡垒作用和党员的先锋模范作用。</p>
            <p>我们积极响应国家号召，践行社会责任，在促进灵活就业、服务民生健康等方面贡献企业力量，努力实现经济效益与社会效益的统一。</p>
          </div>
        </div>
      </div>
    </section>

    <!-- 主题活动 -->
    <section class="section activities">
      <div class="container">
        <div class="section-header animate-on-scroll">
          <p class="section-subtitle">THEMED ACTIVITIES</p>
          <h2 class="section-title">主题活动</h2>
        </div>
        <div class="activities-grid">
          <div 
            class="activity-card animate-on-scroll breathe-hover" 
            v-for="(activity, index) in activities" 
            :key="index"
            :class="`animate-delay-${index + 1}`"
          >
            <div class="activity-icon">
              <component :is="activity.icon" size="32px" />
            </div>
            <h3>{{ activity.title }}</h3>
            <p>{{ activity.desc }}</p>
            <div class="activity-line"></div>
          </div>
        </div>
      </div>
    </section>

    <!-- 党建新闻 -->
    <section class="section news">
      <div class="container">
        <div class="section-header animate-on-scroll">
          <p class="section-subtitle">PARTY NEWS</p>
          <h2 class="section-title">党建新闻</h2>
        </div>
        <div class="news-grid">
          <article 
            class="news-card animate-on-scroll breathe-hover" 
            v-for="(item, index) in newsList" 
            :key="item.id"
            :class="`animate-delay-${index + 1}`"
          >
            <div class="news-cover">
              <img :src="item.image" :alt="item.title" loading="lazy" />
            </div>
            <div class="news-body">
              <h3 class="news-title">{{ item.title }}</h3>
              <p class="news-desc">{{ item.desc }}</p>
              <div class="news-footer">
                <span class="news-date">
                  <TimeIcon size="14px" />
                  {{ item.date }}
                </span>
                <span class="news-link">
                  阅读详情
                  <ChevronRightIcon size="16px" />
                </span>
              </div>
            </div>
            <div class="news-hover-line"></div>
          </article>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { FlagIcon, HeartIcon, UsergroupIcon, BookIcon, TimeIcon, ChevronRightIcon } from 'tdesign-icons-vue-next'
import { useScrollAnimation } from '@/composables/useScrollAnimation'

useScrollAnimation()

const activities = [
  { icon: BookIcon, title: '理论学习', desc: '定期组织党员学习党的理论知识和方针政策，提升政治素养' },
  { icon: HeartIcon, title: '志愿服务', desc: '开展社区服务、公益活动，用实际行动践行社会责任' },
  { icon: UsergroupIcon, title: '组织生活', desc: '规范开展"三会一课"等组织生活，增强组织凝聚力' },
  { icon: FlagIcon, title: '主题党日', desc: '每月开展主题党日活动，增强党性修养和使命担当' }
]

const newsList = [
  { id: 1, title: '轻养到家党支部成立大会顺利召开', desc: '轻养到家党支部正式成立，标志着公司党建工作迈入新阶段，将进一步加强党对企业的领导。', date: '2026-01-12', image: 'https://images.pexels.com/photos/7651937/pexels-photo-7651937.jpeg?auto=compress&cs=tinysrgb&w=400&h=300&fit=crop' },
  { id: 2, title: '学习贯彻党的二十大精神专题会议', desc: '公司组织全体党员深入学习党的二十大报告精神，深刻领会新时代党的使命任务。', date: '2026-01-08', image: 'https://images.pexels.com/photos/7652178/pexels-photo-7652178.jpeg?auto=compress&cs=tinysrgb&w=400&h=300&fit=crop' },
  { id: 3, title: '党员志愿服务进社区活动', desc: '公司党员积极参与社区志愿服务，为社区居民提供健康咨询和免费推拿服务。', date: '2026-01-05', image: 'https://images.pexels.com/photos/6646918/pexels-photo-6646918.jpeg?auto=compress&cs=tinysrgb&w=400&h=300&fit=crop' }
]
</script>

<style lang="scss" scoped>
$party-color: #c62828;
$party-light: #ffebee;
$text-primary: #1a1a1a;
$text-secondary: #666666;
$background-light: #f8f9fa;

.hero {
  position: relative;
  min-height: 60vh;
  background: linear-gradient(135deg, $party-color 0%, #b71c1c 100%);
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
  color: $party-color;
  letter-spacing: 4px;
  margin-bottom: 16px;
  font-weight: 500;
}

.section-title {
  font-size: 44px;
  font-weight: 600;
  color: $text-primary;
}

.intro-card {
  max-width: 900px;
  margin: 0 auto;
  background: $party-light;
  border-radius: 24px;
  padding: 56px;
  border-left: 5px solid $party-color;
  
  p {
    font-size: 17px;
    line-height: 2;
    color: $text-secondary;
    margin-bottom: 24px;
    
    &:last-child { margin-bottom: 0; }
  }
}

.activities-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 32px;
}

.activity-card {
  background: #fff;
  border-radius: 24px;
  padding: 48px 32px;
  text-align: center;
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.06);
  position: relative;
  overflow: hidden;
}

.activity-icon {
  width: 80px;
  height: 80px;
  margin: 0 auto 24px;
  background: $party-light;
  border-radius: 24px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: $party-color;
  transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
}

.activity-card:hover .activity-icon {
  background: $party-color;
  color: #fff;
  transform: scale(1.1) rotate(5deg);
}

.activity-card h3 {
  font-size: 22px;
  font-weight: 600;
  color: $text-primary;
  margin-bottom: 14px;
}

.activity-card p {
  font-size: 15px;
  color: $text-secondary;
  line-height: 1.7;
}

.activity-line {
  position: absolute;
  bottom: 0;
  left: 50%;
  transform: translateX(-50%);
  width: 0;
  height: 4px;
  background: $party-color;
  border-radius: 2px;
  transition: width 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.activity-card:hover .activity-line {
  width: 60px;
}

.news-grid {
  display: flex;
  flex-direction: column;
  gap: 28px;
}

.news-card {
  display: flex;
  background: #fff;
  border-radius: 24px;
  overflow: hidden;
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.06);
  cursor: pointer;
  position: relative;
}

.news-cover {
  width: 200px;
  min-height: 160px;
  position: relative;
  flex-shrink: 0;
  overflow: hidden;
  
  img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
  }
}

.news-card:hover .news-cover img {
  transform: scale(1.1);
}

.news-body {
  flex: 1;
  padding: 36px;
  display: flex;
  flex-direction: column;
}

.news-title {
  font-size: 22px;
  font-weight: 600;
  color: $text-primary;
  margin-bottom: 14px;
  line-height: 1.4;
}

.news-desc {
  font-size: 15px;
  color: $text-secondary;
  line-height: 1.7;
  margin-bottom: 24px;
}

.news-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: auto;
}

.news-date {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 14px;
  color: #999;
}

.news-link {
  display: flex;
  align-items: center;
  gap: 4px;
  font-size: 14px;
  color: $text-secondary;
  transition: color 0.3s;
}

.news-card:hover .news-link {
  color: $party-color;
}

.news-hover-line {
  position: absolute;
  bottom: 0;
  left: 0;
  width: 0;
  height: 4px;
  background: $party-color;
  transition: width 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.news-card:hover .news-hover-line {
  width: 100%;
}
</style>
