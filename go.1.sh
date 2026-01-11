#!/bin/bash
# 轻养到家 - GitHub + 服务器部署（双通道）
# ZERO 框架规范

source "$(dirname "$0")/go.lib.sh"
source "$(dirname "$0")/go.ai.sh"
source "$(dirname "$0")/go.git.sh"

log_info "开始 GitHub + 服务器部署..."

# 加载环境变量
load_env

# 部署配置
DEPLOY_HOST="8.133.195.39"
DEPLOY_PATH="/www/wwwroot/qy.im.sh.cn"
DEPLOY_USER="root"

# 记录开始时间
START_TIME=$(date +%s)

# ========== 阶段1: 显示变更 ==========
echo ""
log_info "📋 本次变更文件:"
git status --short
echo ""

# ========== 阶段2: Git 提交 ==========
log_info "📤 Git 提交..."

# Git add
git add .

# 生成 AI 提交摘要
log_info "🤖 正在生成 AI 提交摘要..."
AI_COMMIT_MSG=$(get_ai_commit_message)

# 交互式确认
COMMIT_MSG=$(confirm_commit_message "$AI_COMMIT_MSG")

echo ""
log_info "📌 最终提交信息: $COMMIT_MSG"
echo ""

# Git commit
if git commit -m "$COMMIT_MSG" 2>/dev/null; then
    log_success "Git 提交成功"
else
    log_warning "无新变更需要提交"
fi

# ========== 阶段3: Git 推送 ==========
log_info "📤 推送到 GitHub..."

if git push origin main 2>&1; then
    log_success "GitHub 推送成功"
else
    log_error "GitHub 推送失败"
fi

# ========== 阶段4: rsync 同步 ==========
log_info "📦 rsync 同步到服务器..."

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

# ========== 阶段5: 服务器操作 ==========
log_info "🔧 服务器操作..."

ssh ${DEPLOY_USER}@${DEPLOY_HOST} << 'EOF'
cd /www/wwwroot/qy.im.sh.cn
# 设置权限
chown -R www:www . 2>/dev/null || true
chmod -R 755 . 2>/dev/null || true
echo "服务器操作完成"
EOF

# ========== 完成 ==========
END_TIME=$(date +%s)
ELAPSED=$((END_TIME - START_TIME))

echo ""
log_success "=========================================="
log_success "✅ 部署完成！"
log_success "📍 地址: http://qy.im.sh.cn"
log_success "⏱️  耗时: ${ELAPSED}秒"
log_success "=========================================="
