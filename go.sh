#!/bin/bash
# ================================================================
# 文件名: go.sh
# 中文名: 轻养到家 - 统一入口脚本
# 创建时间: 2025-01-12
# ================================================================
#
# 【文件职责】
# 统一入口脚本，负责加载库文件、显示菜单、调度子脚本
#
# 【拆分结构】
# go.sh          - 主入口（本文件）
# go.lib.sh      - 通用库：颜色定义、工具函数
# go.0.sh        - 选项 0: 本地开发服务器
# go.1.sh        - 选项 1: GitHub + 服务器部署
# go.2.sh        - 选项 2: 仅 rsync 同步
# go.3.sh        - 选项 3: 运行测试
# go.4.sh        - 选项 4: 数据库操作
#
# 【使用方法】
# ./go.sh        # 交互式菜单
# ./go.sh 0      # 直接执行选项0
#
# ================================================================

# 获取脚本所在目录
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

# 加载库文件
if [ -f "$SCRIPT_DIR/go.lib.sh" ]; then
    source "$SCRIPT_DIR/go.lib.sh"
else
    echo "❌ 错误：找不到 go.lib.sh"
    exit 1
fi

# 显示标题
show_header() {
    echo -e "${BLUE}================================${NC}"
    echo -e "${BLUE}    轻养到家（QuietYou）${NC}"
    echo -e "${BLUE}================================${NC}"
    echo ""
}

# 显示菜单
show_menu() {
    echo -e "${YELLOW}请选择操作：${NC}"
    echo "  0. 启动本地开发服务器"
    echo "  1. GitHub + 服务器部署"
    echo "  2. 仅 rsync 同步到服务器"
    echo "  3. 运行测试"
    echo "  4. 数据库操作"
    echo ""
}

# 执行命令
run_command() {
    local choice="$1"
    
    # 记录开始时间
    START_TIME=$(date +%s)
    export START_TIME
    
    # 检查对应的子脚本是否存在
    local SUB_SCRIPT="$SCRIPT_DIR/go.${choice}.sh"
    if [ -f "$SUB_SCRIPT" ]; then
        source "$SUB_SCRIPT"
        # 显示耗时
        show_elapsed_time "$START_TIME"
    else
        error "无效选择：${choice}"
        echo ""
        echo -e "${YELLOW}💡 可用的子脚本：${NC}"
        ls -1 "$SCRIPT_DIR"/go.*.sh 2>/dev/null | grep -v "go.lib.sh" | grep -v "go.ai.sh" | grep -v "go.git.sh" | while read f; do
            basename "$f"
        done
        exit 1
    fi
}

# 主入口
show_header

if [ -n "$1" ]; then
    # 有参数时，直接执行
    step "执行选项: $1"
    echo ""
    run_command "$1"
else
    # 无参数时，交互式选择
    show_menu
    read -t 15 -p "请输入选择 (15秒后自动选择0): " choice
    
    if [ -z "$choice" ]; then
        choice=0
        echo ""
        step "⏱️  自动选择：启动本地开发服务器"
    fi
    echo ""
    run_command "$choice"
fi
