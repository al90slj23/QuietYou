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

详见 [产品愿景文档](docs/product/01.vision.md)

## 项目结构

```
QuietYou/
├── src/                    # 前端源码（Vue3 单应用）
│   ├── views/
│   │   ├── home/          # 官网
│   │   ├── user/          # 用户端
│   │   ├── admin/         # 管理后台
│   │   ├── merchant/      # 商户端
│   │   └── tech/          # 技师端
│   ├── layouts/           # 布局组件
│   ├── components/        # 公共组件
│   ├── router/            # 路由配置
│   └── styles/            # 全局样式
├── api/                    # 后端 API（PHP）
│   ├── user/              # 用户端 API
│   ├── tech/              # 技师端 API
│   ├── merchant/          # 商户端 API
│   └── admin/             # 管理后台 API
├── pages/                  # 前端构建产物
├── sql/                    # 数据库脚本
├── go.sh                   # 统一入口脚本
└── README.md
```

## 快速开始

### 1. 环境要求

- PHP >= 7.4
- MySQL >= 5.7
- Node.js >= 16

### 2. 配置环境

```bash
cp .env.example .env
vim .env
```

### 3. 启动开发服务器

```bash
./go.sh 0
# 或直接
./go.sh
```

### 访问地址

本地开发和线上地址结构一致：

| 板块 | 本地 | 线上 |
|------|------|------|
| 官网 | http://localhost:3000/ | https://qy.im.sh.cn/ |
| 用户端 | http://localhost:3000/user/ | https://qy.im.sh.cn/user/ |
| 管理后台 | http://localhost:3000/admin/ | https://qy.im.sh.cn/admin/ |
| 商户端 | http://localhost:3000/merchant/ | https://qy.im.sh.cn/merchant/ |
| 技师端 | http://localhost:3000/tech/ | https://qy.im.sh.cn/tech/ |

## 命令说明

```bash
./go.sh 0    # 启动本地开发环境
./go.sh 1    # GitHub + 服务器部署
./go.sh 2    # 仅 rsync 同步到服务器
./go.sh 3    # 运行测试
./go.sh 4    # 数据库操作
```

## 技术栈

- **框架**: ZERO 框架
- **前端**: Vue3 + Vue Router + Pinia
- **UI 组件**: TDesign Mobile Vue
- **图标**: TDesign Icons + Iconify
- **后端**: PHP
- **数据库**: MySQL

## License

MIT
