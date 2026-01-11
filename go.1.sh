#!/bin/bash
# 轻养到家 - GitHub + 服务器部署（双通道）
# ZERO 框架规范

source "$(dirname "$0")/go.lib.sh"
source "$(dirname "$0")/go.ai.sh"
source "$(dirname "$0")/go.git.sh"

log_info "开始 GitHub + 服务器部署..."

# 加载环境变量
load_env

# 部署配置（从环境变量读取，或使用默认值）
DEPLOY_HOST="${DEPLOY_HOST:-}"
DEPLOY_PATH="${DEPLOY_PATH:-/www/wwwroot/qy.im.sh.cn}"
DEPLOY_USER="${DEPLOY_USER:-root}"

if [ -z "$DEPLOY_HOST" ]; then
    log_error "请设置 DEPLOY_HOST 环境变量"
    exit 1
fi

# 记录开始时间
START_TIME=$(date +%s)

# ========== 阶段1: 前端构建 ==========
log_info "📦 构建前端项目..."

cd frontend

# 安装依赖（如果需要）
if [ ! -d "node_modules" ]; then
    log_info "安装前端依赖..."
    npm install
fi

# 构建用户端
log_info "构建用户端 (Vue)..."
npm run build:user
if [ $? -ne 0 ]; then
    log_error "用户端构建失败"
    exit 1
fi
log_success "用户端构建完成"

cd ..

# ========== 阶段2: 显示变更 ==========
echo ""
log_info "📋 本次变更文件:"
git status --short
echo ""

# ========== 阶段3: Git 提交 ==========
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

# ========== 阶段4: Git 推送 ==========
log_info "📤 推送到 GitHub..."

if git push origin main 2>&1; then
    log_success "GitHub 推送成功"
else
    log_error "GitHub 推送失败"
fi

# ========== 阶段5: rsync 同步 ==========
log_info "📦 rsync 同步到服务器..."

rsync -avz --progress --delete \
    --exclude 'node_modules' \
    --exclude '.git' \
    --exclude '.env' \
    --exclude '.DS_Store' \
    --exclude 'frontend/node_modules' \
    --exclude 'frontend/src' \
    --exclude '.kiro' \
    --exclude '.vscode' \
    --exclude 'tests' \
    ./ ${DEPLOY_USER}@${DEPLOY_HOST}:${DEPLOY_PATH}/

if [ $? -eq 0 ]; then
    log_success "rsync 同步成功"
else
    log_error "rsync 同步失败"
    exit 1
fi

# ========== 阶段6: 服务器操作 ==========
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
