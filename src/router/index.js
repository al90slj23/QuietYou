import { createRouter, createWebHistory } from 'vue-router'

// 官网路由
const homeRoutes = [
  { 
    path: '/', 
    component: () => import('../layouts/HomeLayout.vue'),
    children: [
      { path: '', name: 'home', component: () => import('../views/home/Home.vue'), meta: { title: '首页' } },
      { path: 'about', name: 'home-about', component: () => import('../views/home/About.vue'), meta: { title: '关于我们' } },
      { path: 'download', name: 'home-download', component: () => import('../views/home/Download.vue'), meta: { title: '预约渠道' } },
      { path: 'contact', name: 'home-contact', component: () => import('../views/home/Contact.vue'), meta: { title: '联系我们' } },
      { path: 'privacy', name: 'home-privacy', component: () => import('../views/home/Privacy.vue'), meta: { title: '隐私政策' } },
      { path: 'terms', name: 'home-terms', component: () => import('../views/home/Terms.vue'), meta: { title: '服务条款' } }
    ]
  },
  // /home/ 也能访问官网
  { 
    path: '/home', 
    component: () => import('../layouts/HomeLayout.vue'),
    children: [
      { path: '', component: () => import('../views/home/Home.vue'), meta: { title: '首页' } },
      { path: 'about', component: () => import('../views/home/About.vue'), meta: { title: '关于我们' } },
      { path: 'download', component: () => import('../views/home/Download.vue'), meta: { title: '预约渠道' } },
      { path: 'contact', component: () => import('../views/home/Contact.vue'), meta: { title: '联系我们' } },
      { path: 'privacy', component: () => import('../views/home/Privacy.vue'), meta: { title: '隐私政策' } },
      { path: 'terms', component: () => import('../views/home/Terms.vue'), meta: { title: '服务条款' } }
    ]
  }
]

// 用户端路由
const userRoutes = [
  {
    path: '/user',
    component: () => import('../layouts/UserLayout.vue'),
    children: [
      { path: '', redirect: '/user/home' },
      { path: 'home', name: 'user-home', component: () => import('../views/user/home/index.vue'), meta: { title: '首页', showTabBar: true } },
      { path: 'service/list', name: 'user-service-list', component: () => import('../views/user/service/list.vue'), meta: { title: '服务列表', showTabBar: true } },
      { path: 'service/detail/:id', name: 'user-service-detail', component: () => import('../views/user/service/detail.vue'), meta: { title: '服务详情' } },
      { path: 'technician/list', name: 'user-technician-list', component: () => import('../views/user/technician/list.vue'), meta: { title: '技师列表' } },
      { path: 'technician/detail/:id', name: 'user-technician-detail', component: () => import('../views/user/technician/detail.vue'), meta: { title: '技师详情' } },
      { path: 'order/list', name: 'user-order-list', component: () => import('../views/user/order/list.vue'), meta: { title: '我的订单', showTabBar: true } },
      { path: 'order/detail/:id', name: 'user-order-detail', component: () => import('../views/user/order/detail.vue'), meta: { title: '订单详情' } },
      { path: 'order/create', name: 'user-order-create', component: () => import('../views/user/order/create.vue'), meta: { title: '预约服务' } },
      { path: 'order/confirm', name: 'user-order-confirm', component: () => import('../views/user/order/confirm.vue'), meta: { title: '确认订单' } },
      { path: 'order/pay/:id', name: 'user-order-pay', component: () => import('../views/user/order/pay.vue'), meta: { title: '支付订单' } },
      { path: 'profile', name: 'user-profile', component: () => import('../views/user/profile/index.vue'), meta: { title: '我的', showTabBar: true } }
    ]
  }
]

// 管理后台路由 (待开发)
const adminRoutes = [
  {
    path: '/admin',
    component: () => import('../layouts/AdminLayout.vue'),
    children: [
      { path: '', name: 'admin-home', component: () => import('../views/admin/index.vue'), meta: { title: '管理后台' } }
    ]
  }
]

// 商户端路由 (待开发)
const merchantRoutes = [
  {
    path: '/merchant',
    component: () => import('../layouts/MerchantLayout.vue'),
    children: [
      { path: '', name: 'merchant-home', component: () => import('../views/merchant/index.vue'), meta: { title: '商户端' } }
    ]
  }
]

// 技师端路由 (待开发)
const techRoutes = [
  {
    path: '/tech',
    component: () => import('../layouts/TechLayout.vue'),
    children: [
      { path: '', name: 'tech-home', component: () => import('../views/tech/index.vue'), meta: { title: '技师端' } }
    ]
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes: [
    ...homeRoutes,
    ...userRoutes,
    ...adminRoutes,
    ...merchantRoutes,
    ...techRoutes
  ]
})

router.beforeEach((to, from, next) => {
  document.title = to.meta.title ? `${to.meta.title} - 轻养到家` : '轻养到家'
  next()
})

export default router
