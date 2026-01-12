import { createApp } from 'vue'
import { createRouter, createWebHashHistory } from 'vue-router'
import App from './App.vue'
import './styles/main.scss'

const routes = [
  { path: '/', redirect: '/home' },
  { path: '/home', component: () => import('./views/Home.vue'), meta: { title: '首页' } },
  { path: '/about', component: () => import('./views/About.vue'), meta: { title: '关于我们' } },
  { path: '/download', component: () => import('./views/Download.vue'), meta: { title: '预约渠道' } },
  { path: '/contact', component: () => import('./views/Contact.vue'), meta: { title: '联系我们' } },
  { path: '/privacy', component: () => import('./views/Privacy.vue'), meta: { title: '隐私政策' } },
  { path: '/terms', component: () => import('./views/Terms.vue'), meta: { title: '服务条款' } }
]

const router = createRouter({
  history: createWebHashHistory(),
  routes
})

router.beforeEach((to, from, next) => {
  document.title = `${to.meta.title || '轻养到家'} - 轻养到家`
  next()
})

const app = createApp(App)
app.use(router)
app.mount('#app')
