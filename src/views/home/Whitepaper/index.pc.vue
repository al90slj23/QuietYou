<template>
  <div class="whitepaper-pc">
    <!-- 侧边栏 -->
    <aside class="sidebar">
      <div class="sidebar-header">
        <router-link to="/home" class="logo">
          <span class="logo-text">轻养到家</span>
        </router-link>
        <h2 class="doc-title">商业计划白皮书</h2>
        <p class="doc-version">v{{ config.version }}</p>
      </div>
      
      <nav class="sidebar-nav">
        <a 
          v-for="chapter in config.chapters" 
          :key="chapter.id"
          :href="`#${chapter.id}`"
          class="nav-item"
          :class="{ active: activeChapter === chapter.id }"
          @click.prevent="scrollToChapter(chapter.id)"
        >
          <component :is="getIcon(chapter.icon)" size="18px" />
          <span>{{ chapter.title }}</span>
        </a>
      </nav>
      
      <div class="sidebar-footer">
        <p>最后更新：{{ config.lastUpdate }}</p>
        <router-link to="/home" class="back-link">
          <ChevronLeftIcon size="16px" />
          返回官网
        </router-link>
      </div>
    </aside>
    
    <!-- 主内容区 -->
    <main class="main-content">
      <div class="content-wrapper">
        <!-- 加载状态 -->
        <div v-if="loading" class="loading">
          <div class="loading-spinner"></div>
          <p>加载中...</p>
        </div>
        
        <!-- 文档内容 -->
        <template v-else>
          <article 
            v-for="chapter in chapters" 
            :key="chapter.id"
            :id="chapter.id"
            class="chapter"
          >
            <div class="markdown-body" v-html="chapter.content"></div>
          </article>
        </template>
      </div>
      
      <!-- 右侧目录 -->
      <aside class="toc">
        <h4>本页目录</h4>
        <div class="toc-list">
          <a 
            v-for="heading in currentHeadings" 
            :key="heading.id"
            :href="`#${heading.id}`"
            class="toc-item"
            :class="[`level-${heading.level}`, { active: activeHeading === heading.id }]"
            @click.prevent="scrollToHeading(heading.id)"
          >
            {{ heading.text }}
          </a>
        </div>
      </aside>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue'
import { marked } from 'marked'
import { 
  InfoCircleIcon, 
  ChartIcon, 
  WalletIcon, 
  StarIcon, 
  AppIcon, 
  CodeIcon, 
  MapIcon, 
  UsergroupIcon, 
  LinkIcon,
  ChevronLeftIcon
} from 'tdesign-icons-vue-next'

const config = ref({
  title: '',
  version: '1.0.0',
  lastUpdate: '',
  chapters: []
})

const chapters = ref([])
const loading = ref(true)
const activeChapter = ref('')
const activeHeading = ref('')
const currentHeadings = ref([])

// 图标映射
const iconMap = {
  'info-circle': InfoCircleIcon,
  'chart-line': ChartIcon,
  'money-circle': WalletIcon,
  'star': StarIcon,
  'app': AppIcon,
  'code': CodeIcon,
  'map-route': MapIcon,
  'usergroup': UsergroupIcon,
  'link': LinkIcon
}

const getIcon = (name) => iconMap[name] || InfoCircleIcon

// 加载配置和内容
const loadContent = async () => {
  try {
    // 加载配置
    const configResponse = await fetch('/whitepaper/config.json')
    config.value = await configResponse.json()
    
    // 加载所有章节内容
    const loadedChapters = []
    for (const chapter of config.value.chapters) {
      const response = await fetch(`/whitepaper/${chapter.file}`)
      const markdown = await response.text()
      const content = marked(markdown)
      loadedChapters.push({
        ...chapter,
        content
      })
    }
    chapters.value = loadedChapters
    
    // 设置默认激活章节
    if (chapters.value.length > 0) {
      activeChapter.value = chapters.value[0].id
      extractHeadings(chapters.value[0].content)
    }
  } catch (error) {
    console.error('加载白皮书内容失败:', error)
  } finally {
    loading.value = false
  }
}

// 提取标题
const extractHeadings = (html) => {
  const parser = new DOMParser()
  const doc = parser.parseFromString(html, 'text/html')
  const headings = []
  
  doc.querySelectorAll('h2, h3').forEach((el, index) => {
    const id = el.textContent.replace(/\s+/g, '-').toLowerCase()
    headings.push({
      id,
      text: el.textContent,
      level: parseInt(el.tagName[1])
    })
  })
  
  currentHeadings.value = headings
}

// 滚动到章节
const scrollToChapter = (id) => {
  const element = document.getElementById(id)
  if (element) {
    element.scrollIntoView({ behavior: 'smooth', block: 'start' })
    activeChapter.value = id
    
    // 更新当前章节的标题
    const chapter = chapters.value.find(c => c.id === id)
    if (chapter) {
      extractHeadings(chapter.content)
    }
  }
}

// 滚动到标题
const scrollToHeading = (id) => {
  const element = document.getElementById(id)
  if (element) {
    element.scrollIntoView({ behavior: 'smooth', block: 'start' })
    activeHeading.value = id
  }
}

// 监听滚动
const handleScroll = () => {
  const scrollTop = document.documentElement.scrollTop || document.body.scrollTop
  
  // 更新激活章节
  for (const chapter of chapters.value) {
    const element = document.getElementById(chapter.id)
    if (element) {
      const rect = element.getBoundingClientRect()
      if (rect.top <= 100 && rect.bottom > 100) {
        if (activeChapter.value !== chapter.id) {
          activeChapter.value = chapter.id
          extractHeadings(chapter.content)
        }
        break
      }
    }
  }
}

onMounted(() => {
  loadContent()
  window.addEventListener('scroll', handleScroll)
})

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
})
</script>

<style lang="scss" scoped>
$primary: #07c160;
$sidebar-width: 280px;
$toc-width: 200px;

.whitepaper-pc {
  display: flex;
  min-height: 100vh;
  background: #fff;
}

.sidebar {
  width: $sidebar-width;
  background: #fafafa;
  border-right: 1px solid #eee;
  position: fixed;
  top: 0;
  left: 0;
  bottom: 0;
  display: flex;
  flex-direction: column;
  z-index: 100;
}

.sidebar-header {
  padding: 24px 20px;
  border-bottom: 1px solid #eee;
  
  .logo {
    text-decoration: none;
    display: block;
    margin-bottom: 16px;
  }
  
  .logo-text {
    font-size: 20px;
    font-weight: 700;
    color: $primary;
  }
  
  .doc-title {
    font-size: 16px;
    font-weight: 600;
    color: #1a1a1a;
    margin-bottom: 4px;
  }
  
  .doc-version {
    font-size: 12px;
    color: #999;
  }
}

.sidebar-nav {
  flex: 1;
  overflow-y: auto;
  padding: 16px 0;
}

.nav-item {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 12px 20px;
  color: #666;
  text-decoration: none;
  font-size: 14px;
  transition: all 0.2s;
  border-left: 3px solid transparent;
  
  &:hover {
    background: #f0f0f0;
    color: #1a1a1a;
  }
  
  &.active {
    background: rgba($primary, 0.08);
    color: $primary;
    border-left-color: $primary;
    font-weight: 500;
  }
}

.sidebar-footer {
  padding: 16px 20px;
  border-top: 1px solid #eee;
  font-size: 12px;
  color: #999;
  
  .back-link {
    display: flex;
    align-items: center;
    gap: 4px;
    color: $primary;
    text-decoration: none;
    margin-top: 12px;
    font-size: 13px;
    
    &:hover {
      text-decoration: underline;
    }
  }
}

.main-content {
  flex: 1;
  margin-left: $sidebar-width;
  display: flex;
}

.content-wrapper {
  flex: 1;
  max-width: 900px;
  padding: 40px 60px;
  margin: 0 auto;
}

.loading {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  height: 400px;
  color: #999;
  
  .loading-spinner {
    width: 40px;
    height: 40px;
    border: 3px solid #f0f0f0;
    border-top-color: $primary;
    border-radius: 50%;
    animation: spin 1s linear infinite;
  }
  
  p {
    margin-top: 16px;
    font-size: 14px;
  }
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.chapter {
  margin-bottom: 80px;
  
  &:last-child {
    margin-bottom: 40px;
  }
}

.markdown-body {
  font-size: 16px;
  line-height: 1.8;
  color: #1a1a1a;
  
  :deep(h1) {
    font-size: 32px;
    font-weight: 700;
    margin-bottom: 24px;
    padding-bottom: 16px;
    border-bottom: 2px solid $primary;
    color: #1a1a1a;
  }
  
  :deep(h2) {
    font-size: 24px;
    font-weight: 600;
    margin: 48px 0 20px;
    color: #1a1a1a;
  }
  
  :deep(h3) {
    font-size: 18px;
    font-weight: 600;
    margin: 32px 0 16px;
    color: #333;
  }
  
  :deep(p) {
    margin-bottom: 16px;
  }
  
  :deep(ul), :deep(ol) {
    margin-bottom: 16px;
    padding-left: 24px;
  }
  
  :deep(li) {
    margin-bottom: 8px;
  }
  
  :deep(table) {
    width: 100%;
    border-collapse: collapse;
    margin: 24px 0;
    font-size: 14px;
  }
  
  :deep(th), :deep(td) {
    padding: 12px 16px;
    border: 1px solid #e0e0e0;
    text-align: left;
  }
  
  :deep(th) {
    background: #f8f9fa;
    font-weight: 600;
  }
  
  :deep(tr:hover td) {
    background: #fafafa;
  }
  
  :deep(code) {
    background: #f5f5f5;
    padding: 2px 6px;
    border-radius: 4px;
    font-family: 'Monaco', 'Menlo', monospace;
    font-size: 14px;
  }
  
  :deep(pre) {
    background: #1a1a1a;
    color: #f0f0f0;
    padding: 20px;
    border-radius: 8px;
    overflow-x: auto;
    margin: 24px 0;
    
    code {
      background: none;
      padding: 0;
      color: inherit;
    }
  }
  
  :deep(blockquote) {
    border-left: 4px solid $primary;
    padding: 16px 20px;
    margin: 24px 0;
    background: rgba($primary, 0.05);
    color: #666;
    
    p {
      margin: 0;
    }
  }
  
  :deep(strong) {
    font-weight: 600;
    color: #1a1a1a;
  }
  
  :deep(a) {
    color: $primary;
    text-decoration: none;
    
    &:hover {
      text-decoration: underline;
    }
  }
}

.toc {
  width: $toc-width;
  padding: 40px 20px;
  position: sticky;
  top: 0;
  height: fit-content;
  max-height: 100vh;
  overflow-y: auto;
  
  h4 {
    font-size: 12px;
    font-weight: 600;
    color: #999;
    text-transform: uppercase;
    margin-bottom: 16px;
  }
}

.toc-list {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.toc-item {
  font-size: 13px;
  color: #666;
  text-decoration: none;
  line-height: 1.4;
  transition: color 0.2s;
  
  &:hover {
    color: $primary;
  }
  
  &.active {
    color: $primary;
    font-weight: 500;
  }
  
  &.level-3 {
    padding-left: 12px;
    font-size: 12px;
  }
}
</style>
