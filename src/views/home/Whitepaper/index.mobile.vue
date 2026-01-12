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
            <span>{{ chapter.title }}</span>
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
import { marked } from 'marked'
import mermaid from 'mermaid'
import { Transformer } from 'markmap-lib'
import { Markmap } from 'markmap-view'
import { Popup as TPopup } from 'tdesign-mobile-vue'

// 初始化 Mermaid
mermaid.initialize({
  startOnLoad: false,
  theme: 'default',
  securityLevel: 'loose',
  flowchart: {
    useMaxWidth: true,
    htmlLabels: true,
    curve: 'basis'
  }
})

// 初始化 markmap transformer
const markmapTransformer = new Transformer()

// 自定义 marked renderer 处理 mermaid 和 mindmap 代码块
const renderer = new marked.Renderer()
const originalCodeRenderer = renderer.code.bind(renderer)
renderer.code = function(code, language) {
  // 兼容新旧版本 marked API
  const codeText = typeof code === 'object' ? code.text : code
  const lang = typeof code === 'object' ? code.lang : language
  
  if (lang === 'mermaid') {
    return `<div class="mermaid-placeholder" data-mermaid="${encodeURIComponent(codeText)}">${codeText}</div>`
  }
  
  if (lang === 'mindmap') {
    return `<div class="mindmap-placeholder" data-mindmap="${encodeURIComponent(codeText)}"><div class="mindmap-loading">思维导图加载中...</div></div>`
  }
  
  return originalCodeRenderer(code, language)
}
marked.setOptions({ renderer })
import { 
  ChevronLeftIcon, 
  ViewListIcon, 
  CloseIcon,
  InfoCircleIcon, 
  ChartIcon, 
  WalletIcon, 
  StarIcon, 
  AppIcon, 
  CodeIcon, 
  MapIcon, 
  UsergroupIcon, 
  LinkIcon,
  FilePasteIcon
} from 'tdesign-icons-vue-next'

const config = ref({
  title: '',
  version: '1.0.0',
  lastUpdate: '',
  chapters: []
})

const chapters = ref([])
const loading = ref(true)
const showMenu = ref(false)
const activeChapter = ref('')
const scrolled = ref(false)

// 图标映射
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
    }
  } catch (error) {
    console.error('加载白皮书内容失败:', error)
  } finally {
    loading.value = false
    // 等待 DOM 更新后再渲染图表
    await nextTick()
    renderMermaid()
    renderMindmaps()
  }
}

// 渲染 Mermaid 图表
const renderMermaid = async () => {
  const mermaidBlocks = document.querySelectorAll('.mermaid-placeholder')
  for (let i = 0; i < mermaidBlocks.length; i++) {
    const block = mermaidBlocks[i]
    const code = decodeURIComponent(block.getAttribute('data-mermaid'))
    
    try {
      const { svg } = await mermaid.render(`mermaid-mobile-${Date.now()}-${i}`, code)
      const div = document.createElement('div')
      div.className = 'mermaid-diagram'
      div.innerHTML = svg
      block.replaceWith(div)
    } catch (e) {
      console.error('Mermaid 渲染失败:', e, code)
      // 渲染失败时显示原始代码
      block.className = 'mermaid-error'
      block.innerHTML = `<pre><code>${code}</code></pre><p class="error-tip">图表渲染失败</p>`
    }
  }
}

// 计算思维导图高度（根据内容复杂度）- 移动端
const calculateMindmapHeight = (markdown) => {
  const lines = markdown.split('\n').filter(line => line.trim())
  const nodeCount = lines.length
  
  // 移动端高度稍小
  // 简单脑图（少于10个节点）：220px
  // 中等脑图（10-20个节点）：280px
  // 复杂脑图（20-35个节点）：350px
  // 超大脑图（35+节点）：420px
  
  if (nodeCount <= 8) return 220
  if (nodeCount <= 15) return 280
  if (nodeCount <= 25) return 340
  if (nodeCount <= 35) return 400
  return 450
}

// 渲染思维导图
const renderMindmaps = async () => {
  const mindmapBlocks = document.querySelectorAll('.mindmap-placeholder')
  
  for (let i = 0; i < mindmapBlocks.length; i++) {
    const block = mindmapBlocks[i]
    const markdown = decodeURIComponent(block.getAttribute('data-mindmap'))
    
    try {
      // 计算动态高度
      const height = calculateMindmapHeight(markdown)
      
      // 创建外层包装
      const wrapper = document.createElement('div')
      wrapper.className = 'mindmap-wrapper'
      
      // 创建 SVG 容器
      const container = document.createElement('div')
      container.className = 'mindmap-container'
      container.style.cssText = `width: 100%; height: ${height}px; background: #fafafa; border-radius: 8px; overflow: hidden; position: relative;`
      
      const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg')
      svg.style.cssText = 'width: 100%; height: 100%;'
      container.appendChild(svg)
      
      // 创建工具栏
      const toolbar = document.createElement('div')
      toolbar.className = 'mindmap-toolbar'
      toolbar.innerHTML = `
        <button class="mindmap-btn" data-action="fit" title="显示全部">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M15 3h6v6M9 21H3v-6M21 3l-7 7M3 21l7-7"/>
          </svg>
        </button>
        <button class="mindmap-btn" data-action="zoomIn" title="放大">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35M11 8v6M8 11h6"/>
          </svg>
        </button>
        <button class="mindmap-btn" data-action="zoomOut" title="缩小">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35M8 11h6"/>
          </svg>
        </button>
      `
      container.appendChild(toolbar)
      
      wrapper.appendChild(container)
      block.replaceWith(wrapper)
      
      // 转换 markdown 为 markmap 数据
      const { root } = markmapTransformer.transform(markdown)
      
      // 渲染思维导图
      const mm = Markmap.create(svg, {
        autoFit: true,
        color: (node) => {
          const colors = ['#07c160', '#1890ff', '#722ed1', '#fa8c16', '#eb2f96']
          return colors[node.state.depth % colors.length]
        },
        paddingX: 12,
        spacingHorizontal: 60,
        spacingVertical: 6,
        duration: 500,
        zoom: true,
        pan: true
      }, root)
      
      // 绑定工具栏事件
      toolbar.addEventListener('click', (e) => {
        const btn = e.target.closest('.mindmap-btn')
        if (!btn) return
        
        const action = btn.dataset.action
        if (action === 'fit') {
          mm.fit()
        } else if (action === 'zoomIn') {
          mm.rescale(1.25)
        } else if (action === 'zoomOut') {
          mm.rescale(0.8)
        }
      })
      
    } catch (e) {
      console.error('思维导图渲染失败:', e)
      block.innerHTML = `<div class="mindmap-error"><pre>${markdown}</pre><p>思维导图渲染失败</p></div>`
    }
  }
}

// 跳转到章节
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

// 监听滚动
const handleScroll = () => {
  const scrollTop = document.documentElement.scrollTop || document.body.scrollTop
  scrolled.value = scrollTop > 200
  
  // 更新激活章节
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
  
  p {
    margin-top: 12px;
    font-size: 14px;
  }
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.chapter {
  margin-bottom: 48px;
}

.markdown-body {
  font-size: 15px;
  line-height: 1.8;
  color: #1a1a1a;
  
  :deep(h1) {
    font-size: 24px;
    font-weight: 700;
    margin-bottom: 16px;
    padding-bottom: 12px;
    border-bottom: 2px solid $primary;
  }
  
  :deep(h2) {
    font-size: 20px;
    font-weight: 600;
    margin: 32px 0 16px;
  }
  
  :deep(h3) {
    font-size: 17px;
    font-weight: 600;
    margin: 24px 0 12px;
  }
  
  :deep(p) {
    margin-bottom: 12px;
  }
  
  :deep(ul), :deep(ol) {
    margin-bottom: 12px;
    padding-left: 20px;
  }
  
  :deep(li) {
    margin-bottom: 6px;
  }
  
  :deep(table) {
    width: 100%;
    border-collapse: collapse;
    margin: 16px 0;
    font-size: 13px;
    display: block;
    overflow-x: auto;
  }
  
  :deep(th), :deep(td) {
    padding: 10px 12px;
    border: 1px solid #e0e0e0;
    text-align: left;
    white-space: nowrap;
  }
  
  :deep(th) {
    background: #f8f9fa;
    font-weight: 600;
  }
  
  :deep(code) {
    background: #f5f5f5;
    padding: 2px 4px;
    border-radius: 4px;
    font-family: monospace;
    font-size: 13px;
  }
  
  :deep(pre) {
    background: #1a1a1a;
    color: #f0f0f0;
    padding: 16px;
    border-radius: 8px;
    overflow-x: auto;
    margin: 16px 0;
    font-size: 12px;
    
    code {
      background: none;
      padding: 0;
      color: inherit;
    }
  }
  
  :deep(blockquote) {
    border-left: 3px solid $primary;
    padding: 12px 16px;
    margin: 16px 0;
    background: rgba($primary, 0.05);
    color: #666;
    font-size: 14px;
    
    p {
      margin: 0;
    }
  }
  
  :deep(strong) {
    font-weight: 600;
  }
  
  :deep(a) {
    color: $primary;
    text-decoration: none;
  }
  
  :deep(.mermaid-diagram) {
    margin: 16px 0;
    padding: 12px;
    background: #fafafa;
    border-radius: 8px;
    overflow-x: auto;
    
    svg {
      max-width: 100%;
      height: auto;
    }
  }
  
  :deep(.mermaid-placeholder) {
    margin: 16px 0;
    padding: 16px;
    background: #1a1a1a;
    color: #f0f0f0;
    border-radius: 8px;
    font-family: monospace;
    font-size: 12px;
    white-space: pre-wrap;
  }
  
  :deep(.mermaid-error) {
    margin: 16px 0;
    padding: 16px;
    background: #fff5f5;
    border: 1px solid #ffccc7;
    border-radius: 8px;
    
    pre {
      background: #1a1a1a;
      color: #f0f0f0;
      padding: 12px;
      border-radius: 6px;
      margin-bottom: 8px;
      font-size: 11px;
    }
    
    .error-tip {
      color: #ff4d4f;
      font-size: 12px;
      margin: 0;
    }
  }
  
  :deep(.mindmap-wrapper) {
    margin: 16px 0;
  }
  
  :deep(.mindmap-container) {
    border: 1px solid #e8e8e8;
    cursor: grab;
    position: relative;
    
    &:active {
      cursor: grabbing;
    }
  }
  
  :deep(.mindmap-toolbar) {
    position: absolute;
    bottom: 8px;
    right: 8px;
    display: flex;
    gap: 4px;
    background: rgba(255, 255, 255, 0.95);
    padding: 4px;
    border-radius: 6px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    z-index: 10;
  }
  
  :deep(.mindmap-btn) {
    width: 28px;
    height: 28px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid #e8e8e8;
    background: #fff;
    border-radius: 4px;
    cursor: pointer;
    color: #666;
    transition: all 0.2s;
    
    &:active {
      background: #f5f5f5;
      color: $primary;
    }
  }
  
  :deep(.mindmap-loading) {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 150px;
    color: #999;
    font-size: 13px;
  }
  
  :deep(.mindmap-error) {
    margin: 16px 0;
    padding: 16px;
    background: #fff5f5;
    border: 1px solid #ffccc7;
    border-radius: 8px;
    
    pre {
      background: #1a1a1a;
      color: #f0f0f0;
      padding: 12px;
      border-radius: 6px;
      margin-bottom: 8px;
      font-size: 11px;
      white-space: pre-wrap;
    }
    
    p {
      color: #ff4d4f;
      font-size: 12px;
      margin: 0;
    }
  }
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
  
  h2 {
    font-size: 18px;
    font-weight: 600;
    color: #1a1a1a;
  }
  
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
  
  &:active {
    background: #f5f5f5;
  }
  
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
  
  &:active {
    transform: scale(0.95);
  }
}
</style>
