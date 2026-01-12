#!/bin/bash
# ================================================================
# 文件名: go.1.sh
# 中文名: 选项 1 - GitHub + 服务器部署
# 创建时间: 2025-01-12
# ================================================================
#
# 【文件职责】
# 完整部署流程：前端构建 → Git 提交 → GitHub 推送 → rsync 同步
#
# 【部署流程】
# 1. 构建前端项目
# 2. 显示变更文件
# 3. AI 生成提交信息
# 4. Git 提交 + 推送
# 5. rsync 同步到服务器
# 6. 服务器权限设置
#
# ================================================================

source "$SCRIPT_DIR/go.ai.sh"
source "$SCRIPT_DIR/go.git.sh"

step "开始 GitHub + 服务器部署..."

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
# 阶段1: 前端构建
# ============================================================
step "构建前端项目..."

cd "$SCRIPT_DIR"
if [ ! -d "node_modules" ]; then
    npm install
fi
npm run build
if [ $? -ne 0 ]; then
    error "前端构建失败"
    exit 1
fi
success "前端构建完成"

# ============================================================
# 阶段2: 显示变更
# ============================================================
echo ""
step "📋 本次变更文件:"
git status --short
echo ""

# ============================================================
# 阶段3: Git 提交
# ============================================================
step "📤 Git 提交..."

git add .

# 生成 AI 提交摘要
info "🤖 正在生成 AI 提交摘要..."
AI_COMMIT_MSG=$(get_ai_commit_message)

# 交互式确认
COMMIT_MSG=$(confirm_commit_message "$AI_COMMIT_MSG")

echo ""
info "📌 最终提交信息: $COMMIT_MSG"
echo ""

# Git commit
if git commit -m "$COMMIT_MSG" 2>/dev/null; then
    success "Git 提交成功"
else
    warn "无新变更需要提交"
fi

# ============================================================
# 阶段4: Git 推送
# ============================================================
step "📤 推送到 GitHub..."

if git push origin main 2>&1; then
    success "GitHub 推送成功"
else
    error "GitHub 推送失败"
fi

# ============================================================
# 阶段5: rsync 同步
# ============================================================
step "📦 rsync 同步到服务器..."

# 构建 rsync 命令
RSYNC_OPTS="-avz --progress --delete"
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
# 阶段6: 服务器操作
# ============================================================
step "🔧 服务器操作..."

ssh -T ${DEPLOY_USER}@${DEPLOY_HOST} << 'EOF'
cd /www/wwwroot/qy.im.sh.cn
chown -R www:www . 2>/dev/null || true
chmod -R 755 . 2>/dev/null || true
echo "服务器操作完成"
EOF

# ============================================================
# 完成
# ============================================================
echo ""
success "=========================================="
success "✅ 部署完成！"
success "📍 地址: http://qy.im.sh.cn"
success "=========================================="
