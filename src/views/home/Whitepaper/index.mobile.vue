<template>
  <div class="whitepaper-mobile">
    <!-- 顶部导航 -->
    <header class="header">
      <router-link to="/home" class="back-btn">
        <ChevronLeftIcon size="24px" />
      </router-link>
      <h1 class="title">商业计划白皮书</h1>
      <button class="menu-btn" @click="showMenu = true">
        <ViewListIcon size="24px" />
      </button>
    </header>
    
    <!-- 主内容区 -->
    <main class="main-content">
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
    </main>
    
    <!-- 底部信息 -->
    <footer class="footer">
      <p>版本 {{ config.version }} · 更新于 {{ config.lastUpdate }}</p>
    </footer>
    
    <!-- 目录抽屉 -->
    <t-popup 
      :visible="showMenu" 
      placement="left" 
      :close-on-overlay-click="true"
      @visible-change="showMenu = $event"
    >
      <div class="menu-drawer">
        <div class="drawer-header">
          <h2>目录</h2>
          <button class="close-btn" @click="showMenu = false">
            <CloseIcon size="24px" />
          </button>
        </div>
        <nav class="drawer-nav">
          <a 
            v-for="chapter in config.chapters" 
            :key="chapter.id"
            :href="`#${chapter.id}`"
            class="nav-item"
            :class="{ active: activeChapter === chapter.id }"
            @click.prevent="goToChapter(chapter.id)"
          >
            <component :is="getIcon(chapter.icon)" size="20px" />
            <span>{{ chapter.order }}. {{ chapter.title }}</span>
          </a>
        </nav>
      </div>
    </t-popup>
    
    <!-- 浮动目录按钮 -->
    <button class="fab" @click="showMenu = true" v-show="scrolled">
      <ViewListIcon size="24px" />
    </button>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, nextTick } from 'vue'
import { Popup as TPopup } from 'tdesign-mobile-vue'
import { useWhitepaper, renderMindmaps } from './Whitepaper.logic'
import { 
  ChevronLeftIcon, ViewListIcon, CloseIcon,
  InfoCircleIcon, ChartIcon, WalletIcon, StarIcon, 
  AppIcon, CodeIcon, MapIcon, UsergroupIcon, LinkIcon, FilePasteIcon
} from 'tdesign-icons-vue-next'

const { config, chapters, loading, activeChapter, loadContent, markmapTransformer } = useWhitepaper()

const showMenu = ref(false)
const scrolled = ref(false)

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

const goToChapter = (id) => {
  showMenu.value = false
  setTimeout(() => {
    const element = document.getElementById(id)
    if (element) {
      element.scrollIntoView({ behavior: 'smooth', block: 'start' })
      activeChapter.value = id
    }
  }, 300)
}

const handleScroll = () => {
  const scrollTop = document.documentElement.scrollTop || document.body.scrollTop
  scrolled.value = scrollTop > 200
  
  for (const chapter of chapters.value) {
    const element = document.getElementById(chapter.id)
    if (element) {
      const rect = element.getBoundingClientRect()
      if (rect.top <= 100 && rect.bottom > 100) {
        activeChapter.value = chapter.id
        break
      }
    }
  }
}

onMounted(async () => {
  await loadContent()
  await nextTick()
  renderMindmaps(markmapTransformer, true, '👆 拖动移动 · 双指缩放 · 点击展开')
  window.addEventListener('scroll', handleScroll)
})

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
})
</script>

<style lang="scss" scoped>
@use './Whitepaper.style.scss' as *;

.whitepaper-mobile {
  min-height: 100vh;
  background: #fff;
  padding-bottom: 60px;
}

.header {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  height: 56px;
  background: #fff;
  border-bottom: 1px solid #eee;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 12px;
  z-index: 100;
}

.back-btn, .menu-btn {
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: none;
  border: none;
  color: #666;
  cursor: pointer;
}

.title {
  font-size: 17px;
  font-weight: 600;
  color: #1a1a1a;
}

.main-content {
  padding: 72px 16px 20px;
}

.loading {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  height: 300px;
  color: #999;
  
  .loading-spinner {
    width: 32px;
    height: 32px;
    border: 3px solid #f0f0f0;
    border-top-color: $primary;
    border-radius: 50%;
    animation: spin 1s linear infinite;
  }
  
  p { margin-top: 12px; font-size: 14px; }
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.chapter {
  margin-bottom: 48px;
}

.markdown-body {
  @include markdown-body(15px);
  
  :deep(h1) { font-size: 24px; margin-bottom: 16px; padding-bottom: 12px; }
  :deep(h2) { font-size: 20px; margin: 32px 0 16px; }
  :deep(h3) { font-size: 17px; margin: 24px 0 12px; }
  :deep(p) { margin-bottom: 12px; }
  :deep(ul), :deep(ol) { margin-bottom: 12px; padding-left: 20px; }
  :deep(li) { margin-bottom: 6px; }
  :deep(table) { 
    width: 100%;
    border-collapse: collapse;
    margin: 16px 0; 
    font-size: 13px; 
    display: block; 
    overflow-x: auto; 
  }
  :deep(th), :deep(td) { 
    border: 1px solid #e0e0e0;
    text-align: left;
    padding: 10px 12px; 
    white-space: nowrap; 
  }
  :deep(th) {
    background: #f8f9fa;
    font-weight: 600;
  }
  :deep(tr:hover td) {
    background: #fafafa;
  }
  :deep(code) { padding: 2px 4px; font-size: 13px; }
  :deep(pre) { padding: 16px; margin: 16px 0; font-size: 12px; }
  :deep(blockquote) { padding: 12px 16px; margin: 16px 0; font-size: 14px; }
  
  :deep() { @include mermaid-styles; }
  :deep() { @include mindmap-styles(28px, 11px); }
  
  :deep(.mindmap-tip) { bottom: 8px; left: 8px; padding: 4px 8px; }
  :deep(.mindmap-toolbar) { bottom: 8px; right: 8px; padding: 4px; }
}

.footer {
  text-align: center;
  padding: 20px;
  color: #999;
  font-size: 12px;
}

.menu-drawer {
  width: 280px;
  height: 100vh;
  background: #fff;
  display: flex;
  flex-direction: column;
}

.drawer-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 16px 20px;
  border-bottom: 1px solid #eee;
  
  h2 { font-size: 18px; font-weight: 600; color: #1a1a1a; }
  
  .close-btn {
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: none;
    border: none;
    color: #999;
    cursor: pointer;
  }
}

.drawer-nav {
  flex: 1;
  overflow-y: auto;
  padding: 12px 0;
}

.nav-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 14px 20px;
  color: #666;
  text-decoration: none;
  font-size: 15px;
  transition: all 0.2s;
  
  &:active { background: #f5f5f5; }
  
  &.active {
    color: $primary;
    background: rgba($primary, 0.08);
    font-weight: 500;
  }
}

.fab {
  position: fixed;
  bottom: 80px;
  right: 20px;
  width: 48px;
  height: 48px;
  border-radius: 50%;
  background: $primary;
  color: #fff;
  border: none;
  box-shadow: 0 4px 12px rgba($primary, 0.4);
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  z-index: 50;
  transition: transform 0.2s;
  
  &:active { transform: scale(0.95); }
}
</style>
