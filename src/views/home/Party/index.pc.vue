<template>
  <div class="party">
    <section class="page-banner party-banner">
      <div class="container">
        <h1>党建动态</h1>
        <p class="subtitle">PARTY BUILDING</p>
      </div>
    </section>

    <section class="intro section">
      <div class="container">
        <div class="section-title">
          <h2>党建引领</h2>
          <p class="subtitle">PARTY LEADERSHIP</p>
        </div>
        <div class="intro-content">
          <p>轻养到家始终坚持党的领导，以习近平新时代中国特色社会主义思想为指导，将党建工作与企业发展深度融合，充分发挥党组织的战斗堡垒作用和党员的先锋模范作用。</p>
          <p>我们积极响应国家号召，践行社会责任，在促进灵活就业、服务民生健康等方面贡献企业力量，努力实现经济效益与社会效益的统一。</p>
        </div>
      </div>
    </section>

    <section class="news-section section" style="background: #f9fafb;">
      <div class="container">
        <div class="section-title">
          <h2>党建新闻</h2>
          <p class="subtitle">PARTY NEWS</p>
        </div>
        <div class="news-list">
          <div class="news-item" v-for="item in newsList" :key="item.id">
            <div class="news-cover"><FlagIcon size="32px" /></div>
            <div class="news-content">
              <h3 class="news-title">{{ item.title }}</h3>
              <p class="news-desc">{{ item.desc }}</p>
              <div class="news-meta">
                <span class="news-date">{{ item.date }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="activities section">
      <div class="container">
        <div class="section-title">
          <h2>主题活动</h2>
          <p class="subtitle">THEMED ACTIVITIES</p>
        </div>
        <div class="activities-grid">
          <div class="activity-card" v-for="activity in activities" :key="activity.title">
            <div class="activity-icon"><component :is="activity.icon" size="40px" /></div>
            <h3>{{ activity.title }}</h3>
            <p>{{ activity.desc }}</p>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { FlagIcon, HeartIcon, UsergroupIcon, BookIcon } from 'tdesign-icons-vue-next'

const newsList = [
  { id: 1, title: '轻养到家党支部成立大会顺利召开', desc: '轻养到家党支部正式成立，标志着公司党建工作迈入新阶段...', date: '2026-01-12' },
  { id: 2, title: '学习贯彻党的二十大精神专题会议', desc: '公司组织全体党员深入学习党的二十大报告精神...', date: '2026-01-08' },
  { id: 3, title: '党员志愿服务进社区活动', desc: '公司党员积极参与社区志愿服务，为社区居民提供健康咨询...', date: '2026-01-05' }
]

const activities = [
  { icon: BookIcon, title: '理论学习', desc: '定期组织党员学习党的理论知识和方针政策' },
  { icon: HeartIcon, title: '志愿服务', desc: '开展社区服务、公益活动，践行社会责任' },
  { icon: UsergroupIcon, title: '组织生活', desc: '规范开展"三会一课"等组织生活' },
  { icon: FlagIcon, title: '主题党日', desc: '每月开展主题党日活动，增强党性修养' }
]
</script>

<style lang="scss" scoped>
$primary-color: #07c160;
$party-color: #d32f2f;

.party-banner { background: linear-gradient(135deg, $party-color 0%, #c62828 100%) !important; }
.page-banner { color: #fff; text-align: center; padding: 140px 0 80px; h1 { font-size: 42px; font-weight: 700; margin-bottom: 10px; } .subtitle { font-size: 14px; opacity: 0.8; letter-spacing: 3px; } }

.intro-content { max-width: 900px; margin: 0 auto; p { font-size: 16px; line-height: 2; color: #555; margin-bottom: 20px; text-align: justify; &:last-child { margin-bottom: 0; } } }

.news-list { display: flex; flex-direction: column; gap: 20px; }
.news-item { display: flex; background: #fff; border: 1px solid #eee; border-radius: 12px; padding: 20px; transition: all 0.3s; cursor: pointer; &:hover { box-shadow: 0 4px 20px rgba(0,0,0,0.08); } }
.news-cover { width: 80px; height: 80px; background: linear-gradient(135deg, #ffebee 0%, #ffcdd2 100%); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: $party-color; flex-shrink: 0; }
.news-content { flex: 1; margin-left: 20px; display: flex; flex-direction: column; }
.news-title { font-size: 18px; font-weight: 600; color: #333; margin-bottom: 8px; }
.news-desc { font-size: 14px; color: #666; line-height: 1.6; margin-bottom: 12px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
.news-meta { margin-top: auto; }
.news-date { font-size: 13px; color: #999; }

.activities-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 30px; }
.activity-card { background: #fff; border: 1px solid #eee; padding: 30px; border-radius: 12px; text-align: center; transition: all 0.3s; &:hover { box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1); transform: translateY(-5px); } .activity-icon { color: $party-color; margin-bottom: 15px; } h3 { font-size: 18px; font-weight: 600; margin-bottom: 10px; color: #333; } p { font-size: 14px; color: #666; line-height: 1.6; } }
</style>
