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
import { ref, onMounted, onUnmounted, computed, nextTick } from 'vue'
import { marked } from 'marked'
import mermaid from 'mermaid'
import { Transformer } from 'markmap-lib'
import { Markmap } from 'markmap-view'

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
  InfoCircleIcon, 
  ChartIcon, 
  WalletIcon, 
  StarIcon, 
  AppIcon, 
  CodeIcon, 
  MapIcon, 
  UsergroupIcon, 
  LinkIcon,
  ChevronLeftIcon,
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
const activeChapter = ref('')
const activeHeading = ref('')
const currentHeadings = ref([])

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
      let markdown = await response.text()
      // 移除原有的提示文案（现在提示在脑图窗口内部）
      markdown = markdown.replace(/💡 下方思维导图支持拖拽、缩放，可自由探索\n?/g, '')
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
      const { svg } = await mermaid.render(`mermaid-pc-${Date.now()}-${i}`, code)
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

// 计算思维导图高度（根据内容复杂度）
const calculateMindmapHeight = (markdown) => {
  const lines = markdown.split('\n').filter(line => line.trim())
  const nodeCount = lines.length
  
  // 增加高度，为底部提示和按钮留出空间（+60px）
  // 简单脑图（少于10个节点）：320px
  // 中等脑图（10-20个节点）：400px
  // 复杂脑图（20-35个节点）：480px
  // 超大脑图（35+节点）：580px
  
  if (nodeCount <= 8) return 320
  if (nodeCount <= 15) return 400
  if (nodeCount <= 25) return 480
  if (nodeCount <= 35) return 560
  return 620
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
      
      // 创建 SVG 容器（预留底部 50px 给提示和按钮）
      const container = document.createElement('div')
      container.className = 'mindmap-container'
      container.style.cssText = `width: 100%; height: ${height}px; background: #fafafa; border-radius: 12px; overflow: hidden; position: relative;`
      
      // SVG 高度减去底部预留空间
      const svgHeight = height - 50
      const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg')
      svg.style.cssText = `width: 100%; height: ${svgHeight}px;`
      container.appendChild(svg)
      
      // 创建左下角提示
      const tip = document.createElement('div')
      tip.className = 'mindmap-tip'
      tip.innerHTML = '🖱️ 拖拽移动 · 滚轮缩放 · 点击节点展开/收缩'
      container.appendChild(tip)
      
      // 创建工具栏
      const toolbar = document.createElement('div')
      toolbar.className = 'mindmap-toolbar'
      toolbar.innerHTML = `
        <button class="mindmap-btn" data-action="expandAll" title="展开所有">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M4 4h6M4 4v6M20 4h-6M20 4v6M4 20h6M4 20v-6M20 20h-6M20 20v-6"/>
          </svg>
        </button>
        <button class="mindmap-btn" data-action="collapseAll" title="收缩所有">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M4 14h6v6M20 14h-6v6M4 10h6V4M20 10h-6V4"/>
          </svg>
        </button>
        <button class="mindmap-btn" data-action="zoomIn" title="放大">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35M11 8v6M8 11h6"/>
          </svg>
        </button>
        <button class="mindmap-btn" data-action="zoomOut" title="缩小">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
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
          // 根据层级设置颜色
          const colors = ['#07c160', '#1890ff', '#722ed1', '#fa8c16', '#eb2f96']
          return colors[node.state.depth % colors.length]
        },
        paddingX: 16,
        spacingHorizontal: 80,
        spacingVertical: 8,
        duration: 500,
        zoom: true,
        pan: true,
        scrollForPan: false  // 禁用滚轮平移
      }, root)
      
      // 绑定工具栏事件
      toolbar.addEventListener('click', (e) => {
        const btn = e.target.closest('.mindmap-btn')
        if (!btn) return
        
        const action = btn.dataset.action
        if (action === 'zoomIn') {
          mm.rescale(1.25)
        } else if (action === 'zoomOut') {
          mm.rescale(0.8)
        } else if (action === 'expandAll') {
          // 展开所有节点
          const expandNode = (node) => {
            if (node.payload) node.payload.fold = 0
            if (node.children) node.children.forEach(expandNode)
          }
          expandNode(root)
          mm.setData(root)
          setTimeout(() => mm.fit(), 100)
        } else if (action === 'collapseAll') {
          // 收缩所有节点（只保留第一层展开）
          const collapseNode = (node, depth = 0) => {
            if (depth >= 1 && node.children && node.children.length > 0) {
              if (!node.payload) node.payload = {}
              node.payload.fold = 1
            }
            if (node.children) node.children.forEach(child => collapseNode(child, depth + 1))
          }
          collapseNode(root)
          mm.setData(root)
          setTimeout(() => mm.fit(), 100)
        }
      })
      
      // 自定义滚轮事件：滚轮控制缩放
      svg.addEventListener('wheel', (e) => {
        e.preventDefault()
        e.stopPropagation()
        
        // 根据滚轮方向缩放
        const scaleFactor = e.deltaY > 0 ? 0.9 : 1.1
        mm.rescale(scaleFactor)
      }, { passive: false, capture: true })
      
    } catch (e) {
      console.error('思维导图渲染失败:', e)
      block.innerHTML = `<div class="mindmap-error"><pre>${markdown}</pre><p>思维导图渲染失败</p></div>`
    }
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
  
  :deep(.mermaid-diagram) {
    margin: 24px 0;
    padding: 20px;
    background: #fafafa;
    border-radius: 12px;
    overflow-x: auto;
    
    svg {
      max-width: 100%;
      height: auto;
    }
  }
  
  :deep(.mermaid-placeholder) {
    margin: 24px 0;
    padding: 20px;
    background: #1a1a1a;
    color: #f0f0f0;
    border-radius: 12px;
    font-family: monospace;
    font-size: 13px;
    white-space: pre-wrap;
  }
  
  :deep(.mermaid-error) {
    margin: 24px 0;
    padding: 20px;
    background: #fff5f5;
    border: 1px solid #ffccc7;
    border-radius: 12px;
    
    pre {
      background: #1a1a1a;
      color: #f0f0f0;
      padding: 16px;
      border-radius: 8px;
      margin-bottom: 12px;
    }
    
    .error-tip {
      color: #ff4d4f;
      font-size: 13px;
      margin: 0;
    }
  }
  
  :deep(.mindmap-wrapper) {
    margin: 24px 0;
  }
  
  :deep(.mindmap-container) {
    border: 1px solid #e8e8e8;
    cursor: grab;
    position: relative;
    
    &:active {
      cursor: grabbing;
    }
    
    svg {
      display: block;
    }
  }
  
  :deep(.mindmap-tip) {
    position: absolute;
    bottom: 12px;
    left: 12px;
    font-size: 12px;
    color: #999;
    background: rgba(255, 255, 255, 0.9);
    padding: 6px 10px;
    border-radius: 6px;
    z-index: 10;
    pointer-events: none;
  }
  
  :deep(.mindmap-toolbar) {
    position: absolute;
    bottom: 12px;
    right: 12px;
    display: flex;
    gap: 4px;
    background: rgba(255, 255, 255, 0.95);
    padding: 6px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    z-index: 10;
  }
  
  :deep(.mindmap-btn) {
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid #e8e8e8;
    background: #fff;
    border-radius: 6px;
    cursor: pointer;
    color: #666;
    transition: all 0.2s;
    
    &:hover {
      background: #f5f5f5;
      color: $primary;
      border-color: $primary;
    }
    
    &:active {
      transform: scale(0.95);
    }
  }
  
  :deep(.mindmap-loading) {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 200px;
    color: #999;
    font-size: 14px;
  }
  
  :deep(.mindmap-error) {
    margin: 24px 0;
    padding: 20px;
    background: #fff5f5;
    border: 1px solid #ffccc7;
    border-radius: 12px;
    
    pre {
      background: #1a1a1a;
      color: #f0f0f0;
      padding: 16px;
      border-radius: 8px;
      margin-bottom: 12px;
      white-space: pre-wrap;
    }
    
    p {
      color: #ff4d4f;
      font-size: 13px;
      margin: 0;
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
