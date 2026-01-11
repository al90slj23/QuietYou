#!/bin/bash
# 轻养到家 - 仅 rsync 同步到服务器
# ZERO 框架规范

source "$(dirname "$0")/go.lib.sh"

log_info "开始 rsync 同步..."

# 部署配置（从环境变量读取）
DEPLOY_HOST="${DEPLOY_HOST:-}"
DEPLOY_PATH="${DEPLOY_PATH:-/www/wwwroot/qy.im.sh.cn}"
DEPLOY_USER="${DEPLOY_USER:-root}"

if [ -z "$DEPLOY_HOST" ]; then
    log_error "请设置 DEPLOY_HOST 环境变量"
    exit 1
fi

# 记录开始时间
START_TIME=$(date +%s)

# rsync 同步
log_info "📦 同步文件到服务器..."

rsync -avz --progress \
    --exclude 'node_modules' \
    --exclude '.git' \
    --exclude '.env' \
    --exclude '.DS_Store' \
    --exclude 'frontend/node_modules' \
    --exclude 'frontend/dist' \
    ./ ${DEPLOY_USER}@${DEPLOY_HOST}:${DEPLOY_PATH}/

if [ $? -eq 0 ]; then
    log_success "rsync 同步成功"
else
    log_error "rsync 同步失败"
    exit 1
fi

# 服务器操作
log_info "🔧 服务器操作..."

ssh ${DEPLOY_USER}@${DEPLOY_HOST} << 'EOF'
cd /www/wwwroot/qy.im.sh.cn
chown -R www:www . 2>/dev/null || true
chmod -R 755 . 2>/dev/null || true
echo "完成"
EOF

# 完成
END_TIME=$(date +%s)
ELAPSED=$((END_TIME - START_TIME))

echo ""
log_success "✅ 同步完成！耗时: ${ELAPSED}秒"
log_success "📍 地址: http://qy.im.sh.cn"
