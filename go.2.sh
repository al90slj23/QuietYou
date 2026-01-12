#!/bin/bash
# ================================================================
# 文件名: go.2.sh
# 中文名: 选项 2 - 仅 rsync 同步
# 创建时间: 2025-01-12
# ================================================================
#
# 【文件职责】
# 仅执行 rsync 同步到服务器，不构建、不提交 Git
#
# ================================================================

step "开始 rsync 同步..."

# 加载环境变量
load_env

# 部署配置（从环境变量读取）
DEPLOY_HOST="${DEPLOY_HOST:-}"
DEPLOY_PATH="${DEPLOY_PATH:-/www/wwwroot/qy.im.sh.cn}"
DEPLOY_USER="${DEPLOY_USER:-root}"

if [ -z "$DEPLOY_HOST" ]; then
    error "请设置 DEPLOY_HOST 环境变量"
    exit 1
fi

# ============================================================
# rsync 同步
# ============================================================
step "📦 同步文件到服务器..."

# 构建 rsync 命令
RSYNC_OPTS="-avz --progress"
if [ -n "$DEPLOY_IGNORE_FILE" ]; then
    RSYNC_OPTS="$RSYNC_OPTS --exclude-from=$DEPLOY_IGNORE_FILE"
fi

info "执行: rsync $RSYNC_OPTS ./ ${DEPLOY_USER}@${DEPLOY_HOST}:${DEPLOY_PATH}/"
rsync $RSYNC_OPTS ./ ${DEPLOY_USER}@${DEPLOY_HOST}:${DEPLOY_PATH}/
RSYNC_EXIT=$?

if [ $RSYNC_EXIT -eq 0 ]; then
    success "rsync 同步成功"
elif [ $RSYNC_EXIT -eq 23 ]; then
    warn "rsync 部分文件传输警告 (code 23)，通常是权限问题，继续执行..."
else
    error "rsync 同步失败 (exit code: $RSYNC_EXIT)"
    exit 1
fi

# ============================================================
# 服务器操作
# ============================================================
step "🔧 服务器操作..."

ssh ${DEPLOY_USER}@${DEPLOY_HOST} << 'EOF'
cd /www/wwwroot/qy.im.sh.cn
chown -R www:www . 2>/dev/null || true
chmod -R 755 . 2>/dev/null || true
echo "完成"
EOF

# ============================================================
# 完成
# ============================================================
echo ""
success "✅ 同步完成！"
success "📍 地址: http://qy.im.sh.cn"
