/**
 * Whitepaper 共享逻辑
 * 提供 PC 和移动端共用的数据加载、渲染逻辑
 */
import { ref, computed, nextTick } from 'vue'
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

export function useWhitepaper() {
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
  const isScrolling = ref(false) // 防止点击滚动时触发 scroll 事件更新状态

  // 计算属性：带有子标题的章节列表
  const chaptersWithHeadings = computed(() => {
    return chapters.value.map(chapter => {
      const parser = new DOMParser()
      const doc = parser.parseFromString(chapter.content, 'text/html')
      const headings = []
      
      doc.querySelectorAll('h2').forEach((el) => {
        const text = el.textContent.trim()
        const id = text.replace(/\s+/g, '-').toLowerCase()
        headings.push({ id, text })
      })
      
      return { ...chapter, headings }
    })
  })

  // 加载配置和内容
  const loadContent = async () => {
    try {
      const configResponse = await fetch('/whitepaper/chapters.json')
      config.value = await configResponse.json()
      
      const loadedChapters = []
      for (const chapter of config.value.chapters) {
        const response = await fetch(`/whitepaper/${chapter.file}`)
        let markdown = await response.text()
        markdown = markdown.replace(/💡 下方思维导图支持拖拽、缩放，可自由探索\n?/g, '')
        const content = marked(markdown)
        loadedChapters.push({ ...chapter, content })
      }
      chapters.value = loadedChapters
      
      if (chapters.value.length > 0) {
        activeChapter.value = chapters.value[0].id
        extractHeadings(chapters.value[0].content)
      }
    } catch (error) {
      console.error('加载白皮书内容失败:', error)
    } finally {
      loading.value = false
      await nextTick()
      renderMermaid()
    }
  }

  // 渲染 Mermaid 图表
  const renderMermaid = async () => {
    const mermaidBlocks = document.querySelectorAll('.mermaid-placeholder')
    for (let i = 0; i < mermaidBlocks.length; i++) {
      const block = mermaidBlocks[i]
      const code = decodeURIComponent(block.getAttribute('data-mermaid'))
      
      try {
        const { svg } = await mermaid.render(`mermaid-${Date.now()}-${i}`, code)
        const div = document.createElement('div')
        div.className = 'mermaid-diagram'
        div.innerHTML = svg
        block.replaceWith(div)
      } catch (e) {
        console.error('Mermaid 渲染失败:', e)
        block.className = 'mermaid-error'
        block.innerHTML = `<pre><code>${code}</code></pre><p class="error-tip">图表渲染失败</p>`
      }
    }
  }

  // 提取标题
  const extractHeadings = (html) => {
    const parser = new DOMParser()
    const doc = parser.parseFromString(html, 'text/html')
    const headings = []
    
    doc.querySelectorAll('h2, h3').forEach((el) => {
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
    // 先更新状态，立即显示子目录
    activeChapter.value = id
    const chapter = chapters.value.find(c => c.id === id)
    if (chapter) {
      extractHeadings(chapter.content)
    }
    
    // 标记正在滚动，防止 scroll 事件覆盖状态
    isScrolling.value = true
    
    // 滚动到目标位置
    const element = document.getElementById(id)
    if (element) {
      element.scrollIntoView({ behavior: 'smooth', block: 'start' })
    }
    
    // 滚动完成后恢复 scroll 监听
    setTimeout(() => {
      isScrolling.value = false
    }, 800)
  }

  // 滚动到标题
  const scrollToHeading = (id) => {
    activeHeading.value = id
    
    const element = document.getElementById(id)
    if (element) {
      element.scrollIntoView({ behavior: 'smooth', block: 'start' })
    }
  }

  return {
    config,
    chapters,
    loading,
    activeChapter,
    activeHeading,
    currentHeadings,
    chaptersWithHeadings,
    isScrolling,
    loadContent,
    extractHeadings,
    scrollToChapter,
    scrollToHeading,
    markmapTransformer
  }
}

// 计算思维导图高度
export function calculateMindmapHeight(markdown, isMobile = false) {
  const lines = markdown.split('\n').filter(line => line.trim())
  const nodeCount = lines.length
  
  if (isMobile) {
    if (nodeCount <= 8) return 280
    if (nodeCount <= 15) return 350
    if (nodeCount <= 25) return 420
    if (nodeCount <= 35) return 480
    return 520
  }
  
  if (nodeCount <= 8) return 320
  if (nodeCount <= 15) return 400
  if (nodeCount <= 25) return 480
  if (nodeCount <= 35) return 560
  return 620
}

// 渲染思维导图
export function renderMindmaps(markmapTransformer, isMobile = false, tipText = '') {
  const mindmapBlocks = document.querySelectorAll('.mindmap-placeholder')
  
  mindmapBlocks.forEach((block) => {
    const markdown = decodeURIComponent(block.getAttribute('data-mindmap'))
    
    try {
      const height = calculateMindmapHeight(markdown, isMobile)
      const svgHeight = height - (isMobile ? 45 : 50)
      
      const wrapper = document.createElement('div')
      wrapper.className = 'mindmap-wrapper'
      
      const container = document.createElement('div')
      container.className = 'mindmap-container'
      container.style.cssText = `width: 100%; height: ${height}px; background: #fafafa; border-radius: ${isMobile ? 8 : 12}px; overflow: hidden; position: relative;`
      
      const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg')
      svg.style.cssText = `width: 100%; height: ${svgHeight}px;`
      container.appendChild(svg)
      
      // 提示
      const tip = document.createElement('div')
      tip.className = 'mindmap-tip'
      tip.innerHTML = tipText
      container.appendChild(tip)
      
      // 工具栏
      const iconSize = isMobile ? 14 : 16
      const toolbar = document.createElement('div')
      toolbar.className = 'mindmap-toolbar'
      toolbar.innerHTML = `
        <button class="mindmap-btn" data-action="expandAll" title="展开所有">
          <svg width="${iconSize}" height="${iconSize}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M4 4h6M4 4v6M20 4h-6M20 4v6M4 20h6M4 20v-6M20 20h-6M20 20v-6"/>
          </svg>
        </button>
        <button class="mindmap-btn" data-action="collapseAll" title="收缩所有">
          <svg width="${iconSize}" height="${iconSize}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M4 14h6v6M20 14h-6v6M4 10h6V4M20 10h-6V4"/>
          </svg>
        </button>
        <button class="mindmap-btn" data-action="zoomIn" title="放大">
          <svg width="${iconSize}" height="${iconSize}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35M11 8v6M8 11h6"/>
          </svg>
        </button>
        <button class="mindmap-btn" data-action="zoomOut" title="缩小">
          <svg width="${iconSize}" height="${iconSize}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35M8 11h6"/>
          </svg>
        </button>
      `
      container.appendChild(toolbar)
      
      wrapper.appendChild(container)
      block.replaceWith(wrapper)
      
      const { root } = markmapTransformer.transform(markdown)
      
      const mm = Markmap.create(svg, {
        autoFit: true,
        color: (node) => {
          const colors = ['#07c160', '#1890ff', '#722ed1', '#fa8c16', '#eb2f96']
          return colors[node.state.depth % colors.length]
        },
        paddingX: isMobile ? 12 : 16,
        spacingHorizontal: isMobile ? 60 : 80,
        spacingVertical: isMobile ? 6 : 8,
        duration: 500,
        zoom: true,
        pan: true,
        scrollForPan: false
      }, root)
      
      // 工具栏事件
      toolbar.addEventListener('click', (e) => {
        const btn = e.target.closest('.mindmap-btn')
        if (!btn) return
        
        const action = btn.dataset.action
        if (action === 'zoomIn') {
          mm.rescale(1.25)
        } else if (action === 'zoomOut') {
          mm.rescale(0.8)
        } else if (action === 'expandAll') {
          const expandNode = (node) => {
            if (node.payload) node.payload.fold = 0
            if (node.children) node.children.forEach(expandNode)
          }
          expandNode(root)
          mm.setData(root)
          setTimeout(() => mm.fit(), 100)
        } else if (action === 'collapseAll') {
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
      
      // 滚轮缩放
      svg.addEventListener('wheel', (e) => {
        e.preventDefault()
        e.stopPropagation()
        const scaleFactor = e.deltaY > 0 ? 0.9 : 1.1
        mm.rescale(scaleFactor)
      }, { passive: false, capture: true })
      
    } catch (e) {
      console.error('思维导图渲染失败:', e)
      block.innerHTML = `<div class="mindmap-error"><pre>${markdown}</pre><p>思维导图渲染失败</p></div>`
    }
  })
}
