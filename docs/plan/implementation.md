# 轻养到家 - 功能实现计划书

> 基于产品愿景和技师体系设计，规划 user/tech/merchant 三端功能实现

## 核心理念回顾

**平台定位**：多元化链接平台，连接商家、技师、顾客三方
**核心价值**：永远的第三方，兜住所有剩余资源
**盈利模式**：订单服务费（从美团/抖音分流），供应链增值服务

---

## 一、用户端 `/user`

### 1.1 现有功能（需优化）

| 模块 | 路由 | 状态 | 说明 |
|------|------|------|------|
| 首页 | `/user/home` | ✅ 已有 | 需优化展示逻辑 |
| 服务列表 | `/user/service/list` | ✅ 已有 | 需完善筛选 |
| 服务详情 | `/user/service/detail/:id` | ✅ 已有 | 需完善 |
| 技师列表 | `/user/technician/list` | ✅ 已有 | 需增加筛选维度 |
| 技师详情 | `/user/technician/detail/:id` | ✅ 已有 | 需展示评价体系 |
| 订单列表 | `/user/order/list` | ✅ 已有 | 需完善状态流转 |
| 订单详情 | `/user/order/detail/:id` | ✅ 已有 | 需完善 |
| 创建订单 | `/user/order/create` | ✅ 已有 | 需完善 |
| 确认订单 | `/user/order/confirm` | ✅ 已有 | 需完善 |
| 支付订单 | `/user/order/pay/:id` | ✅ 已有 | 需完善 |
| 个人中心 | `/user/profile` | ✅ 已有 | 需完善 |

### 1.2 新增功能

| 模块 | 路由 | 优先级 | 说明 |
|------|------|--------|------|
| 附近店铺 | `/user/shop/list` | P1 | 支持到店消费引流 |
| 店铺详情 | `/user/shop/detail/:id` | P1 | 店铺信息、技师列表、服务项目 |
| 评价技师 | `/user/order/review/:id` | P1 | 多维度评分（手法、态度、准时、沟通、卫生） |
| 收藏技师 | `/user/favorite/technician` | P2 | 常用技师快速预约 |
| 收藏店铺 | `/user/favorite/shop` | P2 | 常去店铺 |
| 优惠券 | `/user/coupon/list` | P2 | 平台让利机制 |
| 地址管理 | `/user/address/list` | P1 | 上门服务地址 |
| 消息通知 | `/user/message/list` | P2 | 订单状态、优惠推送 |

### 1.3 用户端核心流程

```
首页
  ├── 选择服务类型
  │     ├── 上门服务 → 选技师 → 选时间地址 → 下单支付
  │     └── 到店服务 → 选店铺 → 选技师 → 选时间 → 下单支付
  ├── 直接选技师
  │     └── 查看详情 → 选服务 → 下单
  └── 选择店铺
        └── 查看店铺 → 选技师/服务 → 下单
```

---

## 二、技师端 `/tech`

### 2.1 技师分类

| 类型 | 注册方式 | 归属 | 收益结算 |
|------|----------|------|----------|
| 店铺技师 | 店铺注册 | 归属店铺 | 结算到店铺账户 |
| 散技师（独立技师） | 个人注册 | 无归属 | 结算到个人账户 |

### 2.2 功能规划

| 模块 | 路由 | 优先级 | 说明 |
|------|------|--------|------|
| 首页/工作台 | `/tech/home` | P0 | 今日订单、收入统计、待处理事项 |
| 订单列表 | `/tech/order/list` | P0 | 待接单、进行中、已完成 |
| 订单详情 | `/tech/order/detail/:id` | P0 | 订单信息、顾客信息、导航 |
| 接单设置 | `/tech/setting/accept` | P1 | 接单开关、服务范围、可用时间 |
| 服务项目 | `/tech/service/list` | P1 | 我的服务项目、价格设置 |
| 收入明细 | `/tech/income/list` | P1 | 收入记录、提现 |
| 评价管理 | `/tech/review/list` | P1 | 顾客评价、店铺评价 |
| 个人资料 | `/tech/profile` | P1 | 基本信息、技能证书、照片 |
| 认证申请 | `/tech/certification` | P2 | 申请培训门店认证 |
| 店铺招聘 | `/tech/job/list` | P2 | 浏览店铺招聘信息 |
| 消息通知 | `/tech/message/list` | P2 | 新订单、评价、系统通知 |

### 2.3 技师端核心流程

```
工作台
  ├── 新订单提醒 → 查看详情 → 接单/拒单
  ├── 进行中订单 → 开始服务 → 完成服务
  ├── 收入管理 → 查看明细 → 申请提现
  └── 找工作 → 浏览招聘 → 申请/联系
```

### 2.4 技师状态流转

```
离线 ←→ 在线空闲 → 服务中 → 在线空闲
                ↓
            休息中（手动设置）
```

---

## 三、商户端 `/merchant`

### 3.1 功能规划

| 模块 | 路由 | 优先级 | 说明 |
|------|------|--------|------|
| 首页/工作台 | `/merchant/home` | P0 | 今日数据、待处理事项、快捷操作 |
| 订单管理 | `/merchant/order/list` | P0 | 店内订单、外派订单 |
| 订单详情 | `/merchant/order/detail/:id` | P0 | 订单信息、指派技师 |
| 技师管理 | `/merchant/technician/list` | P0 | 店内技师列表 |
| 技师详情 | `/merchant/technician/detail/:id` | P1 | 技师信息、排班、业绩 |
| 添加技师 | `/merchant/technician/add` | P1 | 注册店铺技师 |
| 技师排班 | `/merchant/schedule` | P1 | 技师排班表 |
| 服务项目 | `/merchant/service/list` | P1 | 店铺服务项目管理 |
| 收入统计 | `/merchant/income` | P1 | 收入明细、结算记录 |
| 技师池 | `/merchant/pool/list` | P2 | 浏览平台技师、借调 |
| 借调记录 | `/merchant/pool/borrow` | P2 | 借调技师记录 |
| 发布招聘 | `/merchant/job/list` | P2 | 发布招聘需求 |
| 店铺设置 | `/merchant/setting` | P1 | 店铺信息、营业时间 |
| 采购商城 | `/merchant/supply` | P3 | 消耗品采购 |
| 消息通知 | `/merchant/message/list` | P2 | 新订单、技师申请 |

### 3.2 商户端核心流程

```
工作台
  ├── 新订单 → 查看详情 → 指派技师 → 跟踪服务
  ├── 技师管理 → 添加/编辑技师 → 排班设置
  ├── 缺人时 → 技师池 → 筛选技师 → 发起借调
  ├── 招人时 → 发布招聘 → 查看申请 → 联系/录用
  └── 采购 → 浏览商品 → 下单
```

### 3.3 订单类型

| 类型 | 说明 | 收益归属 |
|------|------|----------|
| 到店订单 | 顾客到店消费 | 店铺 |
| 外派订单（本店技师） | 店内技师上门服务 | 店铺 |
| 借调订单（借入） | 借其他店技师到本店 | 本店付费给对方店铺 |
| 借调订单（借出） | 本店技师去其他店 | 对方店铺付费给本店 |

---

## 四、数据库设计要点

### 4.1 核心表

| 表名 | 说明 |
|------|------|
| `qy_user` | 用户（顾客） |
| `qy_technician` | 技师 |
| `qy_merchant` | 商户/店铺 |
| `qy_service` | 服务项目 |
| `qy_order` | 订单 |
| `qy_review` | 评价 |
| `qy_income` | 收入记录 |
| `qy_settlement` | 结算记录 |

### 4.2 关键字段设计

**技师表关键字段：**
- `type`: 类型（店铺技师/散技师）
- `merchant_id`: 归属店铺（散技师为空）
- `is_certified`: 是否认证
- `status`: 状态（在线/离线/服务中/休息）

**订单表关键字段：**
- `type`: 类型（上门/到店/借调）
- `merchant_id`: 店铺ID
- `technician_id`: 技师ID
- `source_merchant_id`: 技师来源店铺（借调时）
- `status`: 状态流转

---

## 五、实现优先级

### Phase 1：核心流程（P0）
1. 用户端：服务预约、技师选择、订单支付、评价
2. 技师端：接单、服务、收入查看
3. 商户端：订单管理、技师管理

### Phase 2：完善功能（P1）
1. 用户端：店铺列表、地址管理、收藏
2. 技师端：接单设置、服务项目管理、认证
3. 商户端：排班、服务项目、收入统计

### Phase 3：增值功能（P2）
1. 用户端：优惠券、消息通知
2. 技师端：找工作、消息
3. 商户端：技师池借调、招聘发布

### Phase 4：生态功能（P3）
1. 商户端：采购商城
2. 平台：数据分析、运营工具

---

## 六、技术要点

### 6.1 实时通信
- 新订单推送
- 技师状态同步
- 服务进度更新

### 6.2 地理位置
- 附近技师/店铺
- 上门服务导航
- 服务范围设置

### 6.3 支付结算
- 订单支付（微信/支付宝）
- T+N 结算机制
- 技师工资保障（资金留存）

### 6.4 评价体系
- 多维度评分
- 评价可迁移（跟技师走）
- 公开透明

---

## 七、实现进度

### 已完成（2026-01-13 更新）

#### 用户端 `/user` 根据白皮书调整
- ✅ 首页 (`/user/home`) - 快捷入口、服务分类、附近技师、附近店铺、热门服务
- ✅ 技师列表 (`/user/technician/list`) - 服务类型筛选、性别筛选、排序、更多筛选（评分、回头率、状态）
- ✅ 技师详情 (`/user/technician/detail`) - 多维度评分展示、评价筛选、收藏/分享、认证展示
- ✅ 店铺列表 (`/user/shop/list`) - 距离/评分/人气排序
- ✅ 店铺详情 (`/user/shop/detail`) - 店铺信息、服务项目、技师列表
- ✅ 评价页面 (`/user/order/review`) - 多维度评分输入（手法、态度、准时、沟通、卫生）

#### 响应式结构重构
- ✅ 创建 `src/components/common/MobileOnlyTip.vue` - PC端统一提示"请使用移动端访问"
- ✅ 创建 `src/components/common/LoginDialog.vue` - 统一登录弹窗组件（开发阶段随意输入）
- ✅ 重构 User 端入口：`src/views/user/index.vue` + `index.pc.vue` + `index.mobile.vue`
- ✅ 重构 Tech 端入口：`src/views/tech/index.vue` + `index.pc.vue` + `index.mobile.vue`
- ✅ 重构 Merchant 端入口：`src/views/merchant/index.vue` + `index.pc.vue` + `index.mobile.vue`
- ✅ 重构门户入口：`src/views/index.vue` + `index.pc.vue` + `index.mobile.vue`
- ✅ 删除旧 Layout 文件：`UserLayout.vue`、`TechLayout.vue`、`MerchantLayout.vue`
- ✅ 更新路由配置 `src/router/index.js`
- ✅ 修复构建错误：替换不存在的 TDesign 组件（EmptyIcon → InfoCircleIcon, Dropdown → ActionSheet）

#### Bug 修复（2026-01-13 更新）
- ✅ 修复 `src/views/index.vue` - `useMediaQuery` 调用方式错误
- ✅ 修复 `src/components/common/LoginDialog.vue` - TDesign Mobile Vue Popup 组件用法
- ✅ 修复 `src/views/index.mobile.vue` - 入驻选择弹窗

#### 数据库
- ✅ 更新技师表、订单表、评价表
- ✅ 新增借调、招聘、收藏、优惠券、消息等表

#### 技师端 `/tech`
- ✅ 全部页面已完成

#### 商户端 `/merchant`
- ✅ 全部页面已完成

#### 用户端增强
- ✅ 店铺、评价、收藏、优惠券、地址、消息等页面已完成

#### PHP API
- ✅ 技师端、商户端、用户端 API 已完成

### 待完成

#### 管理后台 `/admin` - ✅ 已完成（2026-01-13）
- ✅ 工作台 (`/admin`) - 统计卡片、今日数据、待处理事项、最近订单
- ✅ 用户管理 (`/admin/user/list`) - 用户列表、搜索、禁用/启用
- ✅ 技师管理 (`/admin/tech/list`) - 技师列表、认证审核
- ✅ 商户管理 (`/admin/merchant/list`) - 商户列表、禁用/启用
- ✅ 订单管理 (`/admin/order/list`, `/admin/order/refund`) - 订单列表、退款处理
- ✅ 服务管理 (`/admin/service/category`, `/admin/service/list`) - 服务分类、服务项目
- ✅ 财务管理 (`/admin/finance/income`, `/admin/finance/withdraw`) - 收入统计、提现审核
- ✅ 内容管理 (`/admin/content/banner`, `/admin/content/news`) - 轮播图、资讯管理
- ✅ 系统设置 (`/admin/system/config`, `/admin/system/admin`) - 基础配置、管理员

#### 后续优化
- [ ] 数据可视化图表
- [ ] 权限管理系统
- [ ] 操作日志
