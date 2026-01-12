import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import { resolve } from 'path'
import { readdirSync, readFileSync, writeFileSync } from 'fs'

// 生成 whitepaper 配置
function generateWhitepaperConfig() {
  const whitepaperDir = resolve(__dirname, 'public/whitepaper')
  const outputPath = resolve(__dirname, 'public/whitepaper/chapters.json')
  
  try {
    const files = readdirSync(whitepaperDir)
      .filter(f => f.endsWith('.md'))
      .sort()
    
    const chapters = files.map(file => {
      const filePath = resolve(whitepaperDir, file)
      const content = readFileSync(filePath, 'utf-8')
      
      // 从文件名提取序号（如 00.summary.md -> 0）
      const orderMatch = file.match(/^(\d+)\./)
      const order = orderMatch ? parseInt(orderMatch[1], 10) : 99
      
      // 提取第一个 H1 标题作为 title
      const titleMatch = content.match(/^#\s+(.+)$/m)
      const title = titleMatch ? titleMatch[1].trim() : file.replace('.md', '')
      
      // 从文件开头的 HTML 注释中提取 icon
      // 格式: <!-- icon: star -->
      const iconMatch = content.match(/^<!--\s*icon:\s*(\S+)\s*-->/m)
      const icon = iconMatch ? iconMatch[1] : 'info-circle'
      
      // 从文件名提取 id（去掉编号和扩展名）
      const id = file.replace(/^\d+\./, '').replace('.md', '')
      
      return {
        id,
        order,
        title,
        file,
        icon
      }
    })
    
    const config = {
      title: '轻养到家 - 商业计划白皮书',
      version: '1.0.0',
      lastUpdate: new Date().toISOString().split('T')[0],
      chapters
    }
    
    writeFileSync(outputPath, JSON.stringify(config, null, 2))
    console.log(`[whitepaper] Generated chapters.json with ${chapters.length} chapters`)
  } catch (e) {
    console.error('[whitepaper] Failed to generate config:', e)
  }
}

// Vite 插件：扫描 whitepaper 目录生成章节列表
function whitepaperPlugin() {
  return {
    name: 'whitepaper-scanner',
    buildStart() {
      generateWhitepaperConfig()
    },
    configureServer(server) {
      // 开发模式下监听 whitepaper 目录变化
      server.watcher.add(resolve(__dirname, 'public/whitepaper'))
      server.watcher.on('change', (path) => {
        if (path.includes('whitepaper') && path.endsWith('.md')) {
          generateWhitepaperConfig()
        }
      })
      server.watcher.on('add', (path) => {
        if (path.includes('whitepaper') && path.endsWith('.md')) {
          generateWhitepaperConfig()
        }
      })
      // 初始生成
      generateWhitepaperConfig()
    }
  }
}

export default defineConfig({
  plugins: [vue(), whitepaperPlugin()],
  base: '/',
  resolve: {
    alias: {
      '@': resolve(__dirname, 'src')
    }
  },
  css: {
    preprocessorOptions: {
      scss: {
        api: 'modern'
      }
    }
  },
  build: {
    outDir: 'pages',
    emptyOutDir: true
  },
  server: {
    port: 3000,
    proxy: {
      '/api': {
        target: 'http://qy.im.sh.cn',
        changeOrigin: true
      }
    }
  }
})
