import { createRouter, createWebHistory } from 'vue-router'

// 导航门户
const portalRoutes = [
  { path: '/', component: () => import('../views/index.vue'), meta: { title: '轻养到家' } }
]

// 官网路由
const homeRoutes = [
  { 
    path: '/home', 
    component: () => import('../layouts/HomeLayout.vue'),
    children: [
      { path: '', name: 'home', component: () => import('../views/home/Home/index.vue'), meta: { title: '首页' } },
      { path: 'about', name: 'home-about', component: () => import('../views/home/About/index.vue'), meta: { title: '关于我们' } },
      { path: 'recruit', name: 'home-recruit', component: () => import('../views/home/Recruit/index.vue'), meta: { title: '商户合作' } },
      { path: 'news', name: 'home-news', component: () => import('../views/home/News/index.vue'), meta: { title: '轻养资讯' } },
      { path: 'party', name: 'home-party', component: () => import('../views/home/Party/index.vue'), meta: { title: '党建动态' } },
      { path: 'history', name: 'home-history', component: () => import('../views/home/History/index.vue'), meta: { title: '发展历程' } },
      { path: 'download', name: 'home-download', component: () => import('../views/home/Download/index.vue'), meta: { title: '预约渠道' } },
      { path: 'contact', name: 'home-contact', component: () => import('../views/home/Contact/index.vue'), meta: { title: '联系我们' } },
      { path: 'privacy', name: 'home-privacy', component: () => import('../views/home/Privacy/index.vue'), meta: { title: '隐私政策' } },
      { path: 'terms', name: 'home-terms', component: () => import('../views/home/Terms/index.vue'), meta: { title: '服务条款' } }
    ]
  }
]

// 用户端路由 - 使用 index.vue 作为入口（自动检测设备）
const userRoutes = [
  {
    path: '/user',
    component: () => import('../views/user/index.vue'),
    children: [
      { path: '', redirect: '/user/home' },
      { path: 'home', name: 'user-home', component: () => import('../views/user/home/index.vue'), meta: { title: '首页', showTabBar: true } },
      { path: 'service/list', name: 'user-service-list', component: () => import('../views/user/service/list.vue'), meta: { title: '服务列表', showTabBar: true } },
      { path: 'service/detail/:id', name: 'user-service-detail', component: () => import('../views/user/service/detail.vue'), meta: { title: '服务详情' } },
      { path: 'technician/list', name: 'user-technician-list', component: () => import('../views/user/technician/list.vue'), meta: { title: '技师列表' } },
      { path: 'technician/detail/:id', name: 'user-technician-detail', component: () => import('../views/user/technician/detail.vue'), meta: { title: '技师详情' } },
      { path: 'shop/list', name: 'user-shop-list', component: () => import('../views/user/shop/list.vue'), meta: { title: '附近店铺' } },
      { path: 'shop/detail/:id', name: 'user-shop-detail', component: () => import('../views/user/shop/detail.vue'), meta: { title: '店铺详情' } },
      { path: 'order/list', name: 'user-order-list', component: () => import('../views/user/order/list.vue'), meta: { title: '我的订单', showTabBar: true } },
      { path: 'order/detail/:id', name: 'user-order-detail', component: () => import('../views/user/order/detail.vue'), meta: { title: '订单详情' } },
      { path: 'order/create', name: 'user-order-create', component: () => import('../views/user/order/create.vue'), meta: { title: '预约服务' } },
      { path: 'order/confirm', name: 'user-order-confirm', component: () => import('../views/user/order/confirm.vue'), meta: { title: '确认订单' } },
      { path: 'order/pay/:id', name: 'user-order-pay', component: () => import('../views/user/order/pay.vue'), meta: { title: '支付订单' } },
      { path: 'order/review/:id', name: 'user-order-review', component: () => import('../views/user/order/review.vue'), meta: { title: '评价服务' } },
      { path: 'favorite/list', name: 'user-favorite-list', component: () => import('../views/user/favorite/list.vue'), meta: { title: '我的收藏' } },
      { path: 'coupon/list', name: 'user-coupon-list', component: () => import('../views/user/coupon/list.vue'), meta: { title: '优惠券' } },
      { path: 'address/list', name: 'user-address-list', component: () => import('../views/user/address/list.vue'), meta: { title: '地址管理' } },
      { path: 'address/edit/:id?', name: 'user-address-edit', component: () => import('../views/user/address/edit.vue'), meta: { title: '编辑地址' } },
      { path: 'message/list', name: 'user-message-list', component: () => import('../views/user/message/list.vue'), meta: { title: '消息通知' } },
      { path: 'profile', name: 'user-profile', component: () => import('../views/user/profile/index.vue'), meta: { title: '我的', showTabBar: true } },
      { path: 'profile/edit', name: 'user-profile-edit', component: () => import('../views/user/profile/edit.vue'), meta: { title: '编辑资料' } }
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

// 商户端路由 - 使用 index.vue 作为入口（自动检测设备）
const merchantRoutes = [
  {
    path: '/merchant',
    component: () => import('../views/merchant/index.vue'),
    children: [
      { path: '', redirect: '/merchant/home' },
      { path: 'home', name: 'merchant-home', component: () => import('../views/merchant/home/index.vue'), meta: { title: '工作台', showTabBar: true } },
      { path: 'order/list', name: 'merchant-order-list', component: () => import('../views/merchant/order/list.vue'), meta: { title: '订单管理', showTabBar: true } },
      { path: 'order/detail/:id', name: 'merchant-order-detail', component: () => import('../views/merchant/order/detail.vue'), meta: { title: '订单详情' } },
      { path: 'tech/list', name: 'merchant-tech-list', component: () => import('../views/merchant/tech/list.vue'), meta: { title: '技师管理', showTabBar: true } },
      { path: 'tech/add', name: 'merchant-tech-add', component: () => import('../views/merchant/tech/add.vue'), meta: { title: '添加技师' } },
      { path: 'tech/borrow', name: 'merchant-tech-borrow', component: () => import('../views/merchant/tech/borrow.vue'), meta: { title: '借调技师' } },
      { path: 'service/list', name: 'merchant-service-list', component: () => import('../views/merchant/service/list.vue'), meta: { title: '服务项目' } },
      { path: 'service/edit/:id?', name: 'merchant-service-edit', component: () => import('../views/merchant/service/edit.vue'), meta: { title: '编辑服务' } },
      { path: 'income', name: 'merchant-income', component: () => import('../views/merchant/income/index.vue'), meta: { title: '收入明细' } },
      { path: 'income/withdraw', name: 'merchant-income-withdraw', component: () => import('../views/merchant/income/withdraw.vue'), meta: { title: '申请提现' } },
      { path: 'job/list', name: 'merchant-job-list', component: () => import('../views/merchant/job/list.vue'), meta: { title: '招聘管理' } },
      { path: 'job/edit/:id?', name: 'merchant-job-edit', component: () => import('../views/merchant/job/edit.vue'), meta: { title: '编辑招聘' } },
      { path: 'profile', name: 'merchant-profile', component: () => import('../views/merchant/profile/index.vue'), meta: { title: '我的', showTabBar: true } },
      { path: 'profile/edit', name: 'merchant-profile-edit', component: () => import('../views/merchant/profile/edit.vue'), meta: { title: '店铺信息' } },
      { path: 'setting', name: 'merchant-setting', component: () => import('../views/merchant/setting/index.vue'), meta: { title: '店铺设置' } }
    ]
  }
]

// 技师端路由 - 使用 index.vue 作为入口（自动检测设备）
const techRoutes = [
  {
    path: '/tech',
    component: () => import('../views/tech/index.vue'),
    children: [
      { path: '', redirect: '/tech/home' },
      { path: 'home', name: 'tech-home', component: () => import('../views/tech/home/index.vue'), meta: { title: '工作台', showTabBar: true } },
      { path: 'order/list', name: 'tech-order-list', component: () => import('../views/tech/order/list.vue'), meta: { title: '订单列表', showTabBar: true } },
      { path: 'order/detail/:id', name: 'tech-order-detail', component: () => import('../views/tech/order/detail.vue'), meta: { title: '订单详情' } },
      { path: 'income', name: 'tech-income', component: () => import('../views/tech/income/index.vue'), meta: { title: '收入明细', showTabBar: true } },
      { path: 'income/withdraw', name: 'tech-income-withdraw', component: () => import('../views/tech/income/withdraw.vue'), meta: { title: '申请提现' } },
      { path: 'service/list', name: 'tech-service-list', component: () => import('../views/tech/service/list.vue'), meta: { title: '服务项目' } },
      { path: 'review/list', name: 'tech-review-list', component: () => import('../views/tech/review/list.vue'), meta: { title: '我的评价' } },
      { path: 'setting/accept', name: 'tech-setting-accept', component: () => import('../views/tech/setting/accept.vue'), meta: { title: '接单设置' } },
      { path: 'job/list', name: 'tech-job-list', component: () => import('../views/tech/job/list.vue'), meta: { title: '店铺招聘' } },
      { path: 'profile', name: 'tech-profile', component: () => import('../views/tech/profile/index.vue'), meta: { title: '我的', showTabBar: true } },
      { path: 'profile/edit', name: 'tech-profile-edit', component: () => import('../views/tech/profile/edit.vue'), meta: { title: '编辑资料' } },
      { path: 'certification', name: 'tech-certification', component: () => import('../views/tech/certification/index.vue'), meta: { title: '认证申请' } }
    ]
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes: [
    ...portalRoutes,
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
