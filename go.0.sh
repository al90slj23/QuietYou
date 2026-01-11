#!/bin/bash
# 轻养到家 - 本地开发环境（全部启动）
# ZERO 框架规范

source "$(dirname "$0")/go.lib.sh"

log_info "启动本地开发环境..."

# 检查环境
check_node || exit 1
check_php || exit 1

# 加载环境变量
load_env

# 前端目录
FRONTEND_DIR="$(dirname "$0")/frontend"

# 检查前端依赖
if [ ! -d "$FRONTEND_DIR/node_modules" ]; then
    log_info "安装前端依赖..."
    npm install --prefix "$FRONTEND_DIR"
fi

# PHP 后端端口
PHP_PORT=${DEV_PORT:-8080}

log_info "启动所有开发服务器..."
echo ""
log_info "后端 API:    http://localhost:$PHP_PORT/api/"
log_info "用户端:      http://localhost:3001"
log_info "技师端:      http://localhost:3002 (待创建)"
log_info "商家端:      http://localhost:3003 (待创建)"
log_info "管理后台:    http://localhost:3004 (待创建)"
echo ""
log_info "按 Ctrl+C 停止所有服务器"
echo ""

# 后台启动 PHP 服务器
php -S localhost:$PHP_PORT -t . > /dev/null 2>&1 &
PHP_PID=$!
log_success "PHP 后端已启动 (PID: $PHP_PID)"

# 启动 Vue 用户端（前台运行）
npm run dev:user --prefix "$FRONTEND_DIR"

# 清理：当前台进程结束时，杀掉后台 PHP 进程
kill $PHP_PID 2>/dev/null
