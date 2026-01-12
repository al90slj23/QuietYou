<template>
  <div class="news-mobile">
    <section class="banner">
      <h1>轻养资讯</h1>
      <p>NEWS & ARTICLES</p>
    </section>

    <section class="content">
      <div class="tabs">
        <button v-for="tab in tabs" :key="tab.key" :class="['tab', { active: activeTab === tab.key }]" @click="activeTab = tab.key">
          {{ tab.name }}
        </button>
      </div>

      <div class="news-list">
        <div class="news-item" v-for="item in filteredNews" :key="item.id">
          <div class="cover"><ArticleIcon size="24px" /></div>
          <div class="info">
            <h3>{{ item.title }}</h3>
            <p>{{ item.desc }}</p>
            <div class="meta">
              <span>{{ item.date }}</span>
              <span class="tag">{{ item.tag }}</span>
            </div>
          </div>
        </div>
      </div>

      <div class="empty" v-if="filteredNews.length === 0">暂无相关资讯</div>
    </section>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { ArticleIcon } from 'tdesign-icons-vue-next'

const activeTab = ref('all')
const tabs = [
  { key: 'all', name: '全部' },
  { key: 'news', name: '新闻' },
  { key: 'welfare', name: '公益' }
]

const newsList = ref([
  { id: 1, title: '轻养到家正式上线', desc: '轻养到家平台正式上线，致力于为用户提供专业服务...', date: '2026-01-10', tag: '新闻', type: 'news' },
  { id: 2, title: '首批认证技师培训结束', desc: '首批30名技师顺利通过认证考核...', date: '2026-01-08', tag: '新闻', type: 'news' },
  { id: 3, title: '轻养公益行', desc: '为社区老人提供免费推拿服务...', date: '2026-01-05', tag: '公益', type: 'welfare' }
])

const filteredNews = computed(() => {
  if (activeTab.value === 'all') return newsList.value
  return newsList.value.filter(n => n.type === activeTab.value)
})
</script>

<style lang="scss" scoped>
$primary: #07c160;

.banner { background: linear-gradient(135deg, $primary 0%, #10b981 100%); color: #fff; text-align: center; padding: 80px 20px 40px; h1 { font-size: 24px; font-weight: 700; margin-bottom: 8px; } p { font-size: 12px; opacity: 0.8; } }

.content { padding: 20px; }
.tabs { display: flex; gap: 8px; margin-bottom: 16px; overflow-x: auto; }
.tab { padding: 8px 16px; border: 1px solid #ddd; border-radius: 16px; background: #fff; font-size: 13px; white-space: nowrap; &.active { background: $primary; border-color: $primary; color: #fff; } }

.news-list { display: flex; flex-direction: column; gap: 12px; }
.news-item { display: flex; background: #fff; border: 1px solid #eee; border-radius: 10px; padding: 12px; }
.cover { width: 60px; height: 60px; background: linear-gradient(135deg, #e8f8ef 0%, #d1fae5 100%); border-radius: 6px; display: flex; align-items: center; justify-content: center; color: $primary; flex-shrink: 0; }
.info { flex: 1; margin-left: 12px; h3 { font-size: 14px; font-weight: 600; color: #333; margin-bottom: 4px; display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden; } p { font-size: 12px; color: #666; line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; margin-bottom: 6px; } }
.meta { display: flex; gap: 8px; font-size: 11px; color: #999; .tag { padding: 1px 6px; background: #f0f0f0; border-radius: 3px; } }
.empty { text-align: center; padding: 40px 0; color: #999; font-size: 14px; }
</style>
