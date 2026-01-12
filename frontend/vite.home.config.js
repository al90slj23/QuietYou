import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import { resolve } from 'path'

export default defineConfig({
  plugins: [vue()],
  root: 'src/home',
  base: '/',
  resolve: {
    alias: {
      '@': resolve(__dirname, 'src/home'),
      '@common': resolve(__dirname, 'src/common')
    }
  },
  publicDir: 'public',
  build: {
    outDir: '../../../pages/home',
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
