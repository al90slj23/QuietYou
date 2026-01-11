import { createRouter, createWebHistory } from 'vue-router'

const routes = [
  {
    path: '/',
    redirect: '/home'
  },
  {
    path: '/home',
    name: 'home',
    component: () => import('../views/home/index.vue'),
    meta: { title: '首页' }
  },
  {
    path: '/service/list',
    name: 'service-list',
    component: () => import('../views/service/list.vue'),
    meta: { title: '服务列表' }
  },
  {
    path: '/service/detail/:id',
    name: 'service-detail',
    component: () => import('../views/service/detail.vue'),
    meta: { title: '服务详情' }
  },
  {
    path: '/technician/list',
    name: 'technician-list',
    component: () => import('../views/technician/list.vue'),
    meta: { title: '技师列表' }
  },
  {
    path: '/technician/detail/:id',
    name: 'technician-detail',
    component: () => import('../views/technician/detail.vue'),
    meta: { title: '技师详情' }
  },
  {
    path: '/order/list',
    name: 'order-list',
    component: () => import('../views/order/list.vue'),
    meta: { title: '我的订单' }
  },
  {
    path: '/order/detail/:id',
    name: 'order-detail',
    component: () => import('../views/order/detail.vue'),
    meta: { title: '订单详情' }
  },
  {
    path: '/order/create',
    name: 'order-create',
    component: () => import('../views/order/create.vue'),
    meta: { title: '预约服务' }
  },
  {
    path: '/order/confirm',
    name: 'order-confirm',
    component: () => import('../views/order/confirm.vue'),
    meta: { title: '确认订单' }
  },
  {
    path: '/order/pay/:id',
    name: 'order-pay',
    component: () => import('../views/order/pay.vue'),
    meta: { title: '支付订单' }
  },
  {
    path: '/profile',
    name: 'profile',
    component: () => import('../views/profile/index.vue'),
    meta: { title: '我的' }
  }
]

const router = createRouter({
  history: createWebHistory('/user/'),
  routes
})

// 路由守卫 - 设置页面标题
router.beforeEach((to, from, next) => {
  document.title = to.meta.title ? `${to.meta.title} - 轻养到家` : '轻养到家'
  next()
})

export default router
