# 轻养到家（QuietYou）

> 轻养调理，随叫随松

## 产品定位

轻养到家是一个**多元化链接平台**，连接商家、技师、顾客三方。

我们不是要取代传统门店，而是作为**永远的第三方**，兜住所有剩余的店铺资源、客户资源和技师资源。

### 核心价值

| 角色 | 痛点 | 我们的价值 |
|------|------|------------|
| 商家 | 人员冗余/不足、获客成本高 | 闲时技师接线上单、忙时借调技师、平台引流 |
| 技师 | 收入不稳定、评价不可迁移、工资纠纷 | 多渠道接单、评价跟人走、平台资金保障 |
| 顾客 | 价格不透明、选择有限 | 比美团/抖音更便宜、公开透明的评分体系 |

详见 [商业计划白皮书](public/whitepaper/)

## 项目结构

```
QuietYou/
├── src/                    # 前端源码（Vue 3）
│   ├── views/
│   │   ├── home/          # 官网（PC + 移动端响应式）
│   │   ├── user/          # 用户端（仅移动端）
│   │   ├── tech/          # 技师端（仅移动端）
│   │   ├── merchant/      # 商户端（仅移动端）
│   │   └── admin/         # 管理后台（仅 PC）
│   ├── layouts/           # 布局组件
│   ├── components/        # 公共组件
│   ├── composables/       # 组合式函数
│   ├── router/            # 路由配置
│   └── styles/            # 全局样式
├── api/                    # 后端 API（PHP）
│   ├── user/              # 用户端 API
│   ├── tech/              # 技师端 API
│   ├── merchant/          # 商户端 API
│   └── admin/             # 管理后台 API
├── public/                 # 静态资源
│   └── whitepaper/        # 商业计划白皮书（Markdown）
├── pages/                  # 前端构建产物
├── sql/                    # 数据库脚本
├── docs/standards/         # 开发规范
├── tests/                  # 测试文件
└── go.*.sh                # Shell 脚本
```

## 快速开始

### 环境要求

- Node.js >= 18
- PHP >= 7.4
- MySQL >= 5.7

### 配置环境

```bash
cp .env.example .env
vim .env
```

### 启动开发

```bash
./go.sh      # 交互式菜单，15秒后自动启动开发环境
./go.sh 0    # 直接启动本地开发
```

### 访问地址

| 板块 | 本地 | 线上 |
|------|------|------|
| 导航门户 | http://localhost:3000/ | https://qy.im.sh.cn/ |
| 官网首页 | http://localhost:3000/home/ | https://qy.im.sh.cn/home/ |
| 白皮书 | http://localhost:3000/home/whitepaper/ | https://qy.im.sh.cn/home/whitepaper/ |
| 用户端 | http://localhost:3000/user/ | https://qy.im.sh.cn/user/ |
| 技师端 | http://localhost:3000/tech/ | https://qy.im.sh.cn/tech/ |
| 商户端 | http://localhost:3000/merchant/ | https://qy.im.sh.cn/merchant/ |
| 管理后台 | http://localhost:3000/admin/ | https://qy.im.sh.cn/admin/ |

## 命令说明

```bash
./go.sh 0    # 启动本地开发环境
./go.sh 1    # GitHub + 服务器部署
./go.sh 2    # 仅 rsync 同步到服务器
./go.sh 3    # 运行测试
./go.sh 4    # 数据库操作
```

## 技术栈

| 类别 | 技术 |
|------|------|
| 框架 | ZERO 框架 |
| 前端 | Vue 3 + Vue Router + Pinia |
| UI 组件 | TDesign Vue Next (PC) / TDesign Mobile Vue (移动端) |
| 图标 | TDesign Icons Vue Next |
| 构建 | Vite 5 |
| 样式 | SCSS |
| 后端 | PHP |
| 数据库 | MySQL |

## 文档

- [商业计划白皮书](public/whitepaper/) - 产品愿景、市场分析、商业模式、技术架构等
- [开发计划](docs/plan/) - 功能实现进度、待办事项
- [开发规范](docs/standards/) - 架构、前后端、质量规范

## License

MIT
