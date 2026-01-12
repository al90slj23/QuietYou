import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import { resolve } from 'path'

export default defineConfig({
  plugins: [vue()],
  base: '/user/',
  resolve: {
    alias: {
      '@': resolve(__dirname, 'src'),
      '@shared': resolve(__dirname, '../../packages/shared'),
      '@common': resolve(__dirname, '../../packages/shared/common')
    }
  },
  build: {
    outDir: '../../pages/user',
    emptyOutDir: true
  },
  server: {
    port: 3001,
    proxy: {
      '/api': {
        target: 'http://qy.im.sh.cn',
        changeOrigin: true
      }
    }
  }
})
