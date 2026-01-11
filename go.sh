#!/bin/bash
# 轻养到家 - 统一入口脚本
# ZERO 框架规范

set -e

# 加载公共函数库
source "$(dirname "$0")/go.lib.sh"

# 显示帮助信息
show_help() {
    echo "轻养到家（QuietYou）- 项目管理脚本"
    echo ""
    echo "用法: ./go.sh <命令>"
    echo ""
    echo "命令:"
    echo "  0, local    启动本地开发环境（前端+后端）"
    echo "  1, deploy   GitHub + 服务器部署（双通道）"
    echo "  2, sync     仅 rsync 同步到服务器"
    echo "  3, test     运行测试"
    echo "  4, db       数据库操作"
    echo "  help        显示帮助信息"
    echo ""
}

# 主入口
case "${1:-help}" in
    0|local)
        source "$(dirname "$0")/go.0.sh"
        ;;
    1|deploy)
        source "$(dirname "$0")/go.1.sh"
        ;;
    2|sync)
        source "$(dirname "$0")/go.2.sh"
        ;;
    3|test)
        source "$(dirname "$0")/go.3.sh"
        ;;
    4|db)
        source "$(dirname "$0")/go.4.sh"
        ;;
    help|--help|-h)
        show_help
        ;;
    *)
        echo "未知命令: $1"
        show_help
        exit 1
        ;;
esac
