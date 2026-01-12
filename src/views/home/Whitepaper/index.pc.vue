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
        <div v-for="chapter in chaptersWithHeadings" :key="chapter.id" class="nav-group">
          <a 
            :href="`#${chapter.id}`"
            class="nav-item"
            :class="{ active: activeChapter === chapter.id }"
            @click.prevent="scrollToChapter(chapter.id)"
          >
            <component :is="getIcon(chapter.icon)" size="18px" />
            <span>{{ chapter.order }}. {{ chapter.title }}</span>
          </a>
          <div 
            v-show="chapter.headings.length > 0" 
            class="nav-sublist"
            :class="{ expanded: activeChapter === chapter.id }"
          >
            <a 
              v-for="heading in chapter.headings" 
              :key="heading.id"
              :href="`#${heading.id}`"
              class="nav-subitem"
              :class="{ active: activeHeading === heading.id }"
              @click.prevent="scrollToHeading(heading.id)"
            >
              {{ heading.text }}
            </a>
          </div>
        </div>
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
        <div v-if="loading" class="loading">
          <div class="loading-spinner"></div>
          <p>加载中...</p>
        </div>
        
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
import { onMounted, onUnmounted, nextTick } from 'vue'
import { useWhitepaper, renderMindmaps } from './Whitepaper.logic'
import { 
  InfoCircleIcon, ChartIcon, WalletIcon, StarIcon, AppIcon, 
  CodeIcon, MapIcon, UsergroupIcon, LinkIcon, ChevronLeftIcon, FilePasteIcon
} from 'tdesign-icons-vue-next'

const {
  config, chapters, loading, activeChapter, activeHeading,
  currentHeadings, chaptersWithHeadings, isScrolling, loadContent,
  extractHeadings, scrollToChapter, scrollToHeading, markmapTransformer
} = useWhitepaper()

const iconMap = {
  'file-paste': FilePasteIcon,
  'info-circle': InfoCircleIcon,
  'chart-line': ChartIcon,
  'wallet': WalletIcon,
  'star': StarIcon,
  'app': AppIcon,
  'code': CodeIcon,
  'map-route': MapIcon,
  'usergroup': UsergroupIcon,
  'link': LinkIcon
}

const getIcon = (name) => iconMap[name] || InfoCircleIcon

const handleScroll = () => {
  // 如果是点击触发的滚动，不更新状态
  if (isScrolling.value) return
  
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

onMounted(async () => {
  await loadContent()
  await nextTick()
  renderMindmaps(markmapTransformer, false, '🖱️ 拖拽移动 · 滚轮缩放 · 点击节点展开/收缩')
  window.addEventListener('scroll', handleScroll)
})

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
})
</script>

<style lang="scss" scoped>
@use './Whitepaper.style.scss' as *;

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

.nav-group {
  margin-bottom: 2px;
}

.nav-sublist {
  background: rgba($primary, 0.03);
  overflow: hidden;
  max-height: 0;
  opacity: 0;
  transition: max-height 0.3s ease, opacity 0.2s ease, padding 0.3s ease;
  
  &.expanded {
    max-height: 500px;
    opacity: 1;
    padding: 4px 0 8px;
  }
}

.nav-subitem {
  display: block;
  padding: 8px 20px 8px 48px;
  color: #888;
  text-decoration: none;
  font-size: 13px;
  transition: all 0.2s;
  
  &:hover {
    color: #1a1a1a;
    background: rgba($primary, 0.05);
  }
  
  &.active {
    color: $primary;
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
  
  p { margin-top: 16px; font-size: 14px; }
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.chapter {
  margin-bottom: 80px;
  &:last-child { margin-bottom: 40px; }
}

.markdown-body {
  @include markdown-body(16px);
  
  :deep(h1) { font-size: 32px; margin-bottom: 24px; }
  :deep(h2) { font-size: 24px; margin: 48px 0 20px; }
  :deep(h3) { font-size: 18px; margin: 32px 0 16px; }
  :deep(p) { margin-bottom: 16px; }
  :deep(ul), :deep(ol) { margin-bottom: 16px; }
  :deep(li) { margin-bottom: 8px; }
  :deep(table) { 
    width: 100%;
    border-collapse: collapse;
    margin: 24px 0; 
    font-size: 14px; 
  }
  :deep(th), :deep(td) { 
    border: 1px solid #e0e0e0;
    text-align: left;
    padding: 12px 16px; 
  }
  :deep(th) {
    background: #f8f9fa;
    font-weight: 600;
  }
  :deep(tr:hover td) {
    background: #fafafa;
  }
  :deep(code) { padding: 2px 6px; font-size: 14px; }
  :deep(pre) { padding: 20px; margin: 24px 0; }
  :deep(blockquote) { padding: 16px 20px; margin: 24px 0; }
  
  :deep() { @include mermaid-styles; }
  :deep() { @include mindmap-styles(32px, 12px); }
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
  
  &:hover { color: $primary; }
  &.active { color: $primary; font-weight: 500; }
  &.level-3 { padding-left: 12px; font-size: 12px; }
}
</style>
