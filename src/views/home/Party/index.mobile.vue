<template>
  <div class="party-mobile">
    <section class="banner">
      <h1>党建动态</h1>
      <p>PARTY BUILDING</p>
    </section>

    <section class="intro">
      <h2>党建引领</h2>
      <p>轻养到家始终坚持党的领导，将党建工作与企业发展深度融合，充分发挥党组织的战斗堡垒作用。</p>
    </section>

    <section class="news">
      <h2>党建新闻</h2>
      <div class="news-list">
        <div class="news-item" v-for="item in newsList" :key="item.id">
          <div class="icon"><FlagIcon size="24px" /></div>
          <div class="info">
            <h3>{{ item.title }}</h3>
            <p>{{ item.desc }}</p>
            <span class="date">{{ item.date }}</span>
          </div>
        </div>
      </div>
    </section>

    <section class="activities">
      <h2>主题活动</h2>
      <div class="activity-list">
        <div class="activity-item" v-for="activity in activities" :key="activity.title">
          <div class="icon"><component :is="activity.icon" size="28px" /></div>
          <div class="info">
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
  { id: 1, title: '轻养到家党支部成立大会顺利召开', desc: '公司党建工作迈入新阶段...', date: '2026-01-12' },
  { id: 2, title: '学习贯彻党的二十大精神专题会议', desc: '深入学习党的二十大报告精神...', date: '2026-01-08' },
  { id: 3, title: '党员志愿服务进社区活动', desc: '为社区居民提供健康咨询...', date: '2026-01-05' }
]

const activities = [
  { icon: BookIcon, title: '理论学习', desc: '定期组织党员学习' },
  { icon: HeartIcon, title: '志愿服务', desc: '开展社区服务活动' },
  { icon: UsergroupIcon, title: '组织生活', desc: '规范开展三会一课' },
  { icon: FlagIcon, title: '主题党日', desc: '每月开展主题活动' }
]
</script>

<style lang="scss" scoped>
$party-color: #d32f2f;

.banner { background: linear-gradient(135deg, $party-color 0%, #c62828 100%); color: #fff; text-align: center; padding: 80px 20px 40px; h1 { font-size: 24px; font-weight: 700; margin-bottom: 8px; } p { font-size: 12px; opacity: 0.8; } }

.intro { padding: 20px; h2 { font-size: 16px; font-weight: 600; color: #333; margin-bottom: 10px; } p { font-size: 14px; color: #555; line-height: 1.8; } }

.news { padding: 20px; background: #f9fafb; h2 { font-size: 16px; font-weight: 600; color: #333; margin-bottom: 12px; } }
.news-list { display: flex; flex-direction: column; gap: 10px; }
.news-item { display: flex; background: #fff; border-radius: 10px; padding: 12px; .icon { width: 44px; height: 44px; background: linear-gradient(135deg, #ffebee 0%, #ffcdd2 100%); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: $party-color; flex-shrink: 0; } .info { flex: 1; margin-left: 12px; h3 { font-size: 14px; font-weight: 600; color: #333; margin-bottom: 4px; display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden; } p { font-size: 12px; color: #666; margin-bottom: 4px; display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden; } .date { font-size: 11px; color: #999; } } }

.activities { padding: 20px; h2 { font-size: 16px; font-weight: 600; color: #333; margin-bottom: 12px; } }
.activity-list { display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; }
.activity-item { background: #fff; border: 1px solid #eee; border-radius: 10px; padding: 14px; text-align: center; .icon { color: $party-color; margin-bottom: 8px; } .info { h3 { font-size: 14px; font-weight: 600; color: #333; margin-bottom: 4px; } p { font-size: 12px; color: #666; } } }
</style>
