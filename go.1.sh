#!/bin/bash
# 轻养到家 - 本地开发环境
# ZERO 框架规范

source "$(dirname "$0")/go.lib.sh"

log_info "启动本地开发环境..."

# 检查环境
check_php || exit 1

# 加载环境变量
load_env

# 默认端口
PORT=${DEV_PORT:-8080}

# 启动 PHP 内置服务器
log_info "启动 PHP 开发服务器，端口: $PORT"
log_info "访问地址:"
log_info "  用户端: http://localhost:$PORT/pages/user/"
log_info "  技师端: http://localhost:$PORT/pages/tech/"
log_info "  商家端: http://localhost:$PORT/pages/shop/"
log_info "  管理后台: http://localhost:$PORT/pages/admin/"
log_info ""
log_info "按 Ctrl+C 停止服务器"

php -S localhost:$PORT -t .
