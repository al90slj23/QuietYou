# 轻养到家（QuietYou）

> 轻养调理，随叫随松

上门按摩/推拿服务平台，采用"平台 + 合作门店 + 灵活技师"的轻资产模式。

## 项目结构

```
QuietYou/
├── api/                    # 后端 API（PHP）
│   ├── user/              # 用户端 API
│   ├── tech/              # 技师端 API
│   ├── shop/              # 商家端 API
│   └── admin/             # 管理后台 API
├── pages/                  # 前端页面
│   ├── user/              # 用户端（H5）
│   ├── tech/              # 技师端（H5）
│   ├── shop/              # 商家端（H5）
│   └── admin/             # 管理后台（PC）
├── assets/                 # 静态资源
│   ├── images/
│   ├── icons/
│   └── fonts/
├── sql/                    # 数据库脚本
├── go.sh                   # 统一入口脚本
└── README.md
```

## 快速开始

### 1. 环境要求

- PHP >= 7.4
- MySQL >= 5.7
- Node.js >= 16（可选，用于前端开发）

### 2. 配置环境

```bash
# 复制环境配置
cp .env.example .env

# 编辑配置文件
vim .env
```

### 3. 初始化数据库

```bash
./go.sh db init
```

### 4. 启动开发服务器

```bash
./go.sh dev
```

### 本地开发地址

- 用户端: http://localhost:8080/pages/user/
- 技师端: http://localhost:8080/pages/tech/
- 商家端: http://localhost:8080/pages/shop/
- 管理后台: http://localhost:8080/pages/admin/

### 线上地址

- 用户端: https://qy.im.sh.cn/pages/user/
- 技师端: https://qy.im.sh.cn/pages/tech/
- 商家端: https://qy.im.sh.cn/pages/shop/
- 管理后台: https://qy.im.sh.cn/pages/admin/

## 命令说明

```bash
./go.sh dev      # 启动本地开发环境
./go.sh deploy   # 部署到服务器
./go.sh test     # 运行测试
./go.sh db init  # 初始化数据库
./go.sh db seed  # 填充测试数据
./go.sh db reset # 重置数据库
```

## 技术栈

- **框架**: ZERO 框架
- **前端-移动端**: TDesign Mobile + Vue3
- **前端-管理后台**: TDesign Vue Next
- **后端**: PHP
- **数据库**: MySQL

## License

MIT
