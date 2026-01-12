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
# - Vue 官网: http://localhost:3000
# - Vue 用户端: http://localhost:3001
#
# ================================================================

step "启动本地开发环境..."

# 检查环境
check_node || exit 1
check_php || exit 1

# 加载环境变量
load_env

# 应用目录
APPS_DIR="$SCRIPT_DIR/apps"

# 检查 home 应用依赖
if [ ! -d "$APPS_DIR/home/node_modules" ]; then
    step "安装官网依赖..."
    npm install --prefix "$APPS_DIR/home"
fi

# 检查 user 应用依赖
if [ ! -d "$APPS_DIR/user/node_modules" ]; then
    step "安装用户端依赖..."
    npm install --prefix "$APPS_DIR/user"
fi

# PHP 后端端口
PHP_PORT=${DEV_PORT:-8080}

step "启动所有开发服务器..."
echo ""
info "后端 API:    http://localhost:$PHP_PORT/api/"
info "官网:        http://localhost:3000"
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

# 后台启动官网
npm run dev --prefix "$APPS_DIR/home" > /dev/null 2>&1 &
HOME_PID=$!
success "官网已启动 (PID: $HOME_PID)"

# 启动用户端（前台运行）
npm run dev --prefix "$APPS_DIR/user"

# 清理：当前台进程结束时，杀掉后台进程
kill $PHP_PID 2>/dev/null
kill $HOME_PID 2>/dev/null
