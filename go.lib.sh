#!/bin/bash
# ================================================================
# 文件名: go.lib.sh
# 中文名: 通用库 - 颜色定义和工具函数
# 创建时间: 2025-01-12
# ================================================================
#
# 【文件职责】
# 提供项目脚本的公共函数：颜色定义、输出函数、检查函数、服务管理
#
# 【主要函数】
# - success/error/warn/info/step: 带颜色的输出函数
# - check_command/check_port/kill_port: 环境检查函数
# - load_env: 加载环境变量
# - show_elapsed_time: 显示耗时
#
# ================================================================

# ============================================================
# 颜色定义
# ============================================================
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
CYAN='\033[1;36m'
MAGENTA='\033[1;35m'
NC='\033[0m' # No Color

# ============================================================
# 输出函数
# ============================================================

# 成功消息
success() {
    echo -e "${GREEN}✅ $1${NC}"
}

# 错误消息
error() {
    echo -e "${RED}❌ $1${NC}"
}

# 警告消息
warn() {
    echo -e "${YELLOW}⚠️  $1${NC}"
}

# 信息消息
info() {
    echo -e "${BLUE}ℹ️  $1${NC}"
}

# 步骤提示
step() {
    echo -e "${CYAN}📌 $1${NC}"
}

# 兼容旧函数名（过渡期）
log_info() { info "$1"; }
log_success() { success "$1"; }
log_warning() { warn "$1"; }
log_error() { error "$1"; }

# ============================================================
# 检查函数
# ============================================================

# 获取脚本所在目录
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$SCRIPT_DIR"

# 检查命令是否存在
check_command() {
    local cmd=$1
    local install_hint=$2
    if ! command -v "$cmd" &> /dev/null; then
        error "需要 $cmd，但未安装"
        if [ -n "$install_hint" ]; then
            info "安装方法: $install_hint"
        fi
        return 1
    fi
    return 0
}

# 检查端口是否被占用
check_port() {
    local port=$1
    if lsof -Pi :$port -sTCP:LISTEN -t >/dev/null 2>&1; then
        return 0  # 端口被占用
    else
        return 1  # 端口空闲
    fi
}

# 杀死占用端口的进程
kill_port() {
    local port=$1
    if check_port $port; then
        warn "端口 $port 被占用，正在释放..."
        lsof -ti:$port | xargs kill -9 2>/dev/null
        sleep 1
        success "端口 $port 已释放"
    fi
}

# 检查 PHP 环境
check_php() {
    check_command php || return 1
    info "PHP 版本: $(php -v | head -n 1)"
}

# 检查 Node.js 环境
check_node() {
    check_command node || return 1
    check_command npm || return 1
    info "Node.js 版本: $(node -v)"
    info "npm 版本: $(npm -v)"
}

# ============================================================
# 环境变量
# ============================================================

# 加载环境变量
load_env() {
    if [ -f "$PROJECT_ROOT/.env" ]; then
        export $(cat "$PROJECT_ROOT/.env" | grep -v '^#' | xargs)
        info "环境变量已加载"
    else
        warn ".env 文件不存在，使用默认配置"
    fi
}

# ============================================================
# 耗时计算
# ============================================================

# 计算并显示耗时
show_elapsed_time() {
    local start_time=$1
    local end_time=$(date +%s)
    local elapsed=$((end_time - start_time))
    local minutes=$((elapsed / 60))
    local seconds=$((elapsed % 60))
    echo ""
    echo -e "${CYAN}⏱️  总耗时: ${minutes}分${seconds}秒${NC}"
}

# ============================================================
# 确认函数
# ============================================================

# 确认操作
confirm() {
    local message=${1:-"确认继续？"}
    read -p "$message (y/n): " answer
    case $answer in
        [Yy]* ) return 0;;
        * ) return 1;;
    esac
}


# ============================================================
# 部署排除规则
# ============================================================

DEPLOY_IGNORE=".deployignore"

# 构建 rsync 排除参数（返回文件路径供 --exclude-from 使用）
get_deploy_ignore_file() {
    if [ -f "$PROJECT_ROOT/$DEPLOY_IGNORE" ]; then
        echo "$PROJECT_ROOT/$DEPLOY_IGNORE"
    else
        echo ""
    fi
}

# 获取排除文件路径
DEPLOY_IGNORE_FILE=$(get_deploy_ignore_file)
