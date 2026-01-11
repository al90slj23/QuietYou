import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import { resolve } from 'path'

export default defineConfig({
  plugins: [vue()],
  root: 'src/user',
  base: '/user/',
  resolve: {
    alias: {
      '@': resolve(__dirname, 'src/user'),
      '@common': resolve(__dirname, 'src/common')
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
