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
# - Vue 前端: http://localhost:3000
#
# ================================================================

step "启动本地开发环境..."

# 检查环境
check_node || exit 1
check_php || exit 1

# 加载环境变量
load_env

# 端口配置
VUE_PORT=3000
PHP_PORT=${DEV_PORT:-8080}

# 清理占用端口的进程
kill_port() {
    local port=$1
    local pid=$(lsof -ti:$port 2>/dev/null)
    if [ -n "$pid" ]; then
        warn "端口 $port 被占用 (PID: $pid)，正在清理..."
        kill -9 $pid 2>/dev/null
        sleep 1
        success "端口 $port 已释放"
    fi
}

# 清理 Vue 和 PHP 端口
kill_port $VUE_PORT
kill_port $PHP_PORT

# 检查根目录依赖
if [ ! -d "$SCRIPT_DIR/node_modules" ]; then
    step "安装前端依赖..."
    npm install
fi

step "启动所有开发服务器..."
echo ""
info "后端 API:    http://localhost:$PHP_PORT/api/"
info "前端:        http://localhost:$VUE_PORT"
echo ""
info "访问路径:"
info "  /           官网首页"
info "  /home/      官网"
info "  /user/      用户端"
info "  /admin/     管理后台"
info "  /merchant/  商户端"
info "  /tech/      技师端"
echo ""
info "按 Ctrl+C 停止所有服务器"
echo ""

# 后台启动 PHP 服务器
php -S localhost:$PHP_PORT -t . > /dev/null 2>&1 &
PHP_PID=$!
success "PHP 后端已启动 (PID: $PHP_PID)"

# 启动 Vue 开发服务器（前台运行）
npm run dev

# 清理：当前台进程结束时，杀掉后台进程
kill $PHP_PID 2>/dev/null
