<template>
  <div class="news">
    <section class="page-banner">
      <div class="container">
        <h1>轻养资讯</h1>
        <p class="subtitle">NEWS & ARTICLES</p>
      </div>
    </section>

    <section class="news-section section">
      <div class="container">
        <div class="tabs">
          <button v-for="tab in tabs" :key="tab.key" :class="['tab', { active: activeTab === tab.key }]" @click="activeTab = tab.key">
            {{ tab.name }}
          </button>
        </div>

        <div class="news-list">
          <div class="news-item" v-for="item in filteredNews" :key="item.id">
            <div class="news-cover"><ArticleIcon size="32px" /></div>
            <div class="news-content">
              <h3 class="news-title">{{ item.title }}</h3>
              <p class="news-desc">{{ item.desc }}</p>
              <div class="news-meta">
                <span class="news-date">{{ item.date }}</span>
                <span class="news-tag">{{ item.tag }}</span>
              </div>
            </div>
          </div>
        </div>

        <div class="empty" v-if="filteredNews.length === 0">
          <p>暂无相关资讯</p>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { ArticleIcon } from 'tdesign-icons-vue-next'

const activeTab = ref('all')
const tabs = [
  { key: 'all', name: '全部' },
  { key: 'news', name: '新闻动态' },
  { key: 'welfare', name: '公益活动' },
  { key: 'technician', name: '出彩手艺人' }
]

const newsList = ref([
  { id: 1, title: '轻养到家正式上线，开启健康服务新篇章', desc: '轻养到家平台正式上线，致力于为用户提供专业、便捷的上门养生推拿服务...', date: '2026-01-10', tag: '新闻动态', type: 'news' },
  { id: 2, title: '首批认证技师培训圆满结束', desc: '经过为期一周的专业培训，首批30名技师顺利通过认证考核...', date: '2026-01-08', tag: '新闻动态', type: 'news' },
  { id: 3, title: '轻养公益行：关爱社区老人健康', desc: '轻养到家携手社区志愿者，为社区老人提供免费推拿服务...', date: '2026-01-05', tag: '公益活动', type: 'welfare' }
])

const filteredNews = computed(() => {
  if (activeTab.value === 'all') return newsList.value
  return newsList.value.filter(n => n.type === activeTab.value)
})
</script>

<style lang="scss" scoped>
$primary-color: #07c160;
.page-banner { background: linear-gradient(135deg, $primary-color 0%, #10b981 100%); color: #fff; text-align: center; padding: 140px 0 80px; h1 { font-size: 42px; font-weight: 700; margin-bottom: 10px; } .subtitle { font-size: 14px; opacity: 0.8; letter-spacing: 3px; } }
.tabs { display: flex; gap: 12px; margin-bottom: 30px; flex-wrap: wrap; }
.tab { padding: 10px 24px; border: 1px solid #ddd; border-radius: 20px; background: #fff; font-size: 14px; cursor: pointer; transition: all 0.3s; &:hover { border-color: $primary-color; color: $primary-color; } &.active { background: $primary-color; border-color: $primary-color; color: #fff; } }
.news-list { display: flex; flex-direction: column; gap: 20px; }
.news-item { display: flex; background: #fff; border: 1px solid #eee; border-radius: 12px; padding: 20px; transition: all 0.3s; cursor: pointer; &:hover { box-shadow: 0 4px 20px rgba(0,0,0,0.08); } }
.news-cover { width: 120px; height: 90px; background: linear-gradient(135deg, #e8f8ef 0%, #d1fae5 100%); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: $primary-color; flex-shrink: 0; }
.news-content { flex: 1; margin-left: 20px; display: flex; flex-direction: column; }
.news-title { font-size: 18px; font-weight: 600; color: #333; margin-bottom: 8px; }
.news-desc { font-size: 14px; color: #666; line-height: 1.6; margin-bottom: 12px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
.news-meta { display: flex; gap: 16px; margin-top: auto; }
.news-date { font-size: 13px; color: #999; }
.news-tag { font-size: 12px; padding: 2px 8px; background: #f0f0f0; border-radius: 4px; color: #666; }
.empty { text-align: center; padding: 60px 0; color: #999; }
</style>
