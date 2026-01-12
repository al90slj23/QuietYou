<template>
  <div class="news">
    <!-- Hero Banner -->
    <section class="hero">
      <div class="hero-bg">
        <div class="hero-circle hero-circle-1"></div>
        <div class="hero-circle hero-circle-2"></div>
      </div>
      <div class="hero-content">
        <p class="hero-subtitle animate-on-scroll">NEWS & ARTICLES</p>
        <h1 class="hero-title animate-on-scroll animate-delay-1">轻养资讯</h1>
        <p class="hero-desc animate-on-scroll animate-delay-2">了解轻养到家的最新动态</p>
      </div>
      <div class="hero-wave">
        <svg viewBox="0 0 1440 120" preserveAspectRatio="none">
          <path d="M0,60 C360,120 1080,0 1440,60 L1440,120 L0,120 Z" fill="#fff"/>
        </svg>
      </div>
    </section>

    <!-- 资讯列表 -->
    <section class="section news-section">
      <div class="container">
        <!-- 分类标签 -->
        <div class="tabs-wrapper animate-on-scroll">
          <div class="tabs">
            <button 
              v-for="tab in tabs" 
              :key="tab.key" 
              :class="['tab', { active: activeTab === tab.key }]" 
              @click="activeTab = tab.key"
            >
              {{ tab.name }}
            </button>
          </div>
        </div>

        <!-- 资讯网格 -->
        <div class="news-grid" v-if="filteredNews.length > 0">
          <article 
            class="news-card animate-on-scroll breathe-hover" 
            v-for="(item, index) in filteredNews" 
            :key="item.id"
            :class="`animate-delay-${(index % 4) + 1}`"
          >
            <div class="news-cover">
              <img :src="item.image" :alt="item.title" loading="lazy" />
              <div class="news-cover-overlay"></div>
            </div>
            <div class="news-body">
              <div class="news-tag">{{ item.tag }}</div>
              <h3 class="news-title">{{ item.title }}</h3>
              <p class="news-desc">{{ item.desc }}</p>
              <div class="news-footer">
                <span class="news-date">
                  <TimeIcon size="14px" />
                  {{ item.date }}
                </span>
                <span class="news-link">
                  阅读更多
                  <ChevronRightIcon size="16px" />
                </span>
              </div>
            </div>
            <div class="news-hover-line"></div>
          </article>
        </div>

        <!-- 空状态 -->
        <div class="empty animate-on-scroll" v-else>
          <div class="empty-icon">
            <ArticleIcon size="48px" />
          </div>
          <p>暂无相关资讯</p>
        </div>

        <!-- 加载更多 -->
        <div class="load-more animate-on-scroll" v-if="filteredNews.length > 0">
          <button class="btn-load-more">
            查看更多资讯
            <ChevronDownIcon size="20px" />
          </button>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { ArticleIcon, TimeIcon, ChevronRightIcon, ChevronDownIcon } from 'tdesign-icons-vue-next'
import { useScrollAnimation } from '@/composables/useScrollAnimation'

useScrollAnimation()

const activeTab = ref('all')
const tabs = [
  { key: 'all', name: '全部资讯' },
  { key: 'news', name: '新闻动态' },
  { key: 'welfare', name: '公益活动' },
  { key: 'technician', name: '出彩手艺人' }
]

const newsList = ref([
  { 
    id: 1, 
    title: '轻养到家正式上线，开启健康服务新篇章', 
    desc: '轻养到家平台正式上线，致力于为用户提供专业、便捷的上门养生推拿服务，让健康触手可及。', 
    date: '2026-01-10', 
    tag: '新闻动态', 
    type: 'news',
    image: 'https://images.pexels.com/photos/7652178/pexels-photo-7652178.jpeg?auto=compress&cs=tinysrgb&w=600&h=400&fit=crop'
  },
  { 
    id: 2, 
    title: '首批认证技师培训圆满结束', 
    desc: '经过为期一周的专业培训，首批30名技师顺利通过认证考核，即将为用户提供专业服务。', 
    date: '2026-01-08', 
    tag: '新闻动态', 
    type: 'news',
    image: 'https://images.pexels.com/photos/6076101/pexels-photo-6076101.jpeg?auto=compress&cs=tinysrgb&w=600&h=400&fit=crop'
  },
  { 
    id: 3, 
    title: '轻养公益行：关爱社区老人健康', 
    desc: '轻养到家携手社区志愿者，为社区老人提供免费推拿服务，传递温暖与关爱。', 
    date: '2026-01-05', 
    tag: '公益活动', 
    type: 'welfare',
    image: 'https://images.pexels.com/photos/34073814/pexels-photo-34073814.jpeg?auto=compress&cs=tinysrgb&w=600&h=400&fit=crop'
  },
  { 
    id: 4, 
    title: '技师风采：用双手传递健康的力量', 
    desc: '走进轻养到家优秀技师的故事，了解他们如何用专业技能为用户带来健康与舒适。', 
    date: '2026-01-03', 
    tag: '出彩手艺人', 
    type: 'technician',
    image: 'https://images.pexels.com/photos/5473184/pexels-photo-5473184.jpeg?auto=compress&cs=tinysrgb&w=600&h=400&fit=crop'
  },
  { 
    id: 5, 
    title: '轻养到家与多家企业达成战略合作', 
    desc: '轻养到家与多家知名企业签署战略合作协议，共同推动健康服务行业发展。', 
    date: '2026-01-01', 
    tag: '新闻动态', 
    type: 'news',
    image: 'https://images.pexels.com/photos/7652246/pexels-photo-7652246.jpeg?auto=compress&cs=tinysrgb&w=600&h=400&fit=crop'
  },
  { 
    id: 6, 
    title: '新年公益：为环卫工人送温暖', 
    desc: '新年伊始，轻养到家组织技师团队为城市环卫工人提供免费推拿服务。', 
    date: '2025-12-28', 
    tag: '公益活动', 
    type: 'welfare',
    image: 'https://images.pexels.com/photos/33296777/pexels-photo-33296777.jpeg?auto=compress&cs=tinysrgb&w=600&h=400&fit=crop'
  }
])

const filteredNews = computed(() => {
  if (activeTab.value === 'all') return newsList.value
  return newsList.value.filter(n => n.type === activeTab.value)
})
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
  background: #fff;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 40px;
}

.tabs-wrapper {
  display: flex;
  justify-content: center;
  margin-bottom: 80px;
}

.tabs {
  display: inline-flex;
  gap: 12px;
  background: $background-light;
  padding: 10px;
  border-radius: 40px;
}

.tab {
  padding: 14px 32px;
  border: none;
  border-radius: 30px;
  background: transparent;
  font-size: 15px;
  color: $text-secondary;
  cursor: pointer;
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  
  &:hover {
    color: $primary-color;
  }
  
  &.active {
    background: $primary-color;
    color: #fff;
    box-shadow: 0 8px 20px rgba(7, 193, 96, 0.3);
  }
}

.news-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 32px;
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
  width: 280px;
  min-height: 240px;
  position: relative;
  flex-shrink: 0;
  overflow: hidden;
  
  img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
  }
  
  &-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(7, 193, 96, 0.1) 0%, rgba(16, 185, 129, 0.2) 100%);
    opacity: 0;
    transition: opacity 0.4s;
  }
}

.news-card:hover .news-cover {
  img {
    transform: scale(1.1);
  }
  
  &-overlay {
    opacity: 1;
  }
}

.news-body {
  flex: 1;
  padding: 36px;
  display: flex;
  flex-direction: column;
}

.news-tag {
  display: inline-block;
  padding: 6px 14px;
  background: $primary-light;
  color: $primary-color;
  font-size: 12px;
  font-weight: 500;
  border-radius: 16px;
  margin-bottom: 18px;
  align-self: flex-start;
}

.news-title {
  font-size: 22px;
  font-weight: 600;
  color: $text-primary;
  margin-bottom: 14px;
  line-height: 1.4;
  transition: color 0.3s;
}

.news-card:hover .news-title {
  color: $primary-color;
}

.news-desc {
  font-size: 15px;
  color: $text-secondary;
  line-height: 1.7;
  margin-bottom: 24px;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
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
  color: $primary-color;
}

.news-hover-line {
  position: absolute;
  bottom: 0;
  left: 0;
  width: 0;
  height: 4px;
  background: $primary-color;
  transition: width 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.news-card:hover .news-hover-line {
  width: 100%;
}

.empty {
  text-align: center;
  padding: 100px 0;
  
  &-icon {
    width: 120px;
    height: 120px;
    margin: 0 auto 28px;
    background: $background-light;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #ccc;
  }
  
  p {
    font-size: 18px;
    color: #999;
  }
}

.load-more {
  text-align: center;
  margin-top: 80px;
}

.btn-load-more {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  padding: 18px 48px;
  background: transparent;
  border: 2px solid $primary-color;
  border-radius: 40px;
  font-size: 16px;
  font-weight: 500;
  color: $primary-color;
  cursor: pointer;
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  
  &:hover {
    background: $primary-color;
    color: #fff;
    transform: translateY(-4px);
    box-shadow: 0 12px 30px rgba(7, 193, 96, 0.3);
  }
}
</style>
