#!/bin/bash
# 轻养到家 - 公共函数库
# ZERO 框架规范

# 颜色定义
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# 日志函数
log_info() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

log_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

log_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

log_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# 检查命令是否存在
check_command() {
    if ! command -v "$1" &> /dev/null; then
        log_error "命令 '$1' 未找到，请先安装"
        return 1
    fi
    return 0
}

# 检查 PHP 环境
check_php() {
    check_command php || return 1
    log_info "PHP 版本: $(php -v | head -n 1)"
}

# 检查 Node.js 环境
check_node() {
    check_command node || return 1
    check_command npm || return 1
    log_info "Node.js 版本: $(node -v)"
    log_info "npm 版本: $(npm -v)"
}

# 检查 MySQL 环境
check_mysql() {
    check_command mysql || return 1
    log_info "MySQL 客户端已就绪"
}

# 加载环境变量
load_env() {
    if [ -f ".env" ]; then
        export $(cat .env | grep -v '^#' | xargs)
        log_info "环境变量已加载"
    else
        log_warning ".env 文件不存在，使用默认配置"
    fi
}

# 项目根目录
PROJECT_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
