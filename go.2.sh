#!/bin/bash
# 轻养到家 - 部署脚本
# ZERO 框架规范

source "$(dirname "$0")/go.lib.sh"

log_info "开始部署..."

# 加载环境变量
load_env

# 部署目标
DEPLOY_HOST=${DEPLOY_HOST:-""}
DEPLOY_PATH=${DEPLOY_PATH:-"/var/www/quietyou"}
DEPLOY_USER=${DEPLOY_USER:-"www-data"}

if [ -z "$DEPLOY_HOST" ]; then
    log_error "请在 .env 文件中配置 DEPLOY_HOST"
    exit 1
fi

log_info "部署目标: $DEPLOY_USER@$DEPLOY_HOST:$DEPLOY_PATH"

# 同步文件
log_info "同步文件..."
rsync -avz --exclude='.git' \
    --exclude='node_modules' \
    --exclude='.env' \
    --exclude='*.log' \
    ./ "$DEPLOY_USER@$DEPLOY_HOST:$DEPLOY_PATH/"

log_success "部署完成!"
