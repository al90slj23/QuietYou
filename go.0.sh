#!/bin/bash
# ================================================================
# 文件名: go.0.sh
# 中文名: 选项 0 - 本地开发环境
# 创建时间: 2025-01-12
# ================================================================
#
# 【文件职责】
# 启动本地开发环境，包括 PHP 后端和 Vue 前端
#
# 【启动服务】
# - PHP 后端 API: http://localhost:8080/api/
# - Vue 用户端: http://localhost:3001
#
# ================================================================

step "启动本地开发环境..."

# 检查环境
check_node || exit 1
check_php || exit 1

# 加载环境变量
load_env

# 前端目录
FRONTEND_DIR="$SCRIPT_DIR/frontend"

# 检查前端依赖
if [ ! -d "$FRONTEND_DIR/node_modules" ]; then
    step "安装前端依赖..."
    npm install --prefix "$FRONTEND_DIR"
fi

# PHP 后端端口
PHP_PORT=${DEV_PORT:-8080}

step "启动所有开发服务器..."
echo ""
info "后端 API:    http://localhost:$PHP_PORT/api/"
info "用户端:      http://localhost:3001"
info "技师端:      http://localhost:3002 (待创建)"
info "商家端:      http://localhost:3003 (待创建)"
info "管理后台:    http://localhost:3004 (待创建)"
echo ""
info "按 Ctrl+C 停止所有服务器"
echo ""

# 后台启动 PHP 服务器
php -S localhost:$PHP_PORT -t . > /dev/null 2>&1 &
PHP_PID=$!
success "PHP 后端已启动 (PID: $PHP_PID)"

# 启动 Vue 用户端（前台运行）
npm run dev:user --prefix "$FRONTEND_DIR"

# 清理：当前台进程结束时，杀掉后台 PHP 进程
kill $PHP_PID 2>/dev/null
