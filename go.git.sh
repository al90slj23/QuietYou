#!/bin/bash
# ================================================================
# 文件名: go.git.sh
# 中文名: GitHub 提交函数库
# 创建时间: 2025-01-11
# 依赖: go.ai.sh (AI API 调用)
# ================================================================

# 加载 AI 库（如果尚未加载）
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
if ! type call_ai_api &>/dev/null; then
    source "$SCRIPT_DIR/go.ai.sh"
fi

# 交互式确认提交摘要（交互输出到 stderr，结果输出到 stdout）
confirm_commit_message() {
    local CURRENT_MSG="$1"

    while true; do
        printf "\n" >&2
        printf "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}\n" >&2
        printf "${GREEN}📌 AI 生成的提交摘要:${NC}\n" >&2
        printf "${YELLOW}   %s${NC}\n" "$CURRENT_MSG" >&2
        printf "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}\n" >&2
        printf "\n" >&2
        printf "${YELLOW}请选择操作:${NC}\n" >&2
        printf "  ${GREEN}1${NC} - 确认使用此摘要 (默认, 10秒后自动确认)\n" >&2
        printf "  ${YELLOW}2${NC} - 自定义输入摘要\n" >&2
        printf "  ${BLUE}0${NC} - 重新生成 AI 摘要\n" >&2
        printf "\n" >&2
        
        read -t 10 -p "请输入选择 (1/2/0) [默认1]: " confirm_choice
        local read_status=$?
        
        if [ $read_status -gt 128 ] || [ -z "$confirm_choice" ]; then
            confirm_choice="1"
            printf "\n${GREEN}⏱️  自动确认使用此摘要${NC}\n" >&2
        fi

        case $confirm_choice in
            1)
                COMMIT_MSG="$CURRENT_MSG"
                break
                ;;
            2)
                printf "\n" >&2
                read -p "请输入自定义提交摘要: " CUSTOM_MSG
                if [ -n "$CUSTOM_MSG" ]; then
                    COMMIT_MSG="$CUSTOM_MSG"
                    break
                else
                    printf "${RED}摘要不能为空，请重新选择${NC}\n" >&2
                fi
                ;;
            0)
                printf "\n" >&2
                printf "${BLUE}🤖 重新生成 AI 提交摘要...${NC}\n" >&2
                CURRENT_MSG=$(get_ai_commit_message)
                ;;
            *)
                printf "${RED}无效选择，请输入 1、2 或 0${NC}\n" >&2
                ;;
        esac
    done

    echo "$COMMIT_MSG"
}

# 提交到 GitHub
commit_to_github() {
    echo -e "${BLUE}📤 提交到 GitHub...${NC}"

    # 显示变更
    echo ""
    echo -e "${YELLOW}📋 本次变更文件:${NC}"
    git status --short
    echo ""

    # 先 git add
    git add .

    # 生成 AI 提交摘要
    echo -e "${BLUE}🤖 正在生成 AI 提交摘要...${NC}"
    AI_COMMIT_MSG=$(get_ai_commit_message)

    # 交互式确认
    COMMIT_MSG=$(confirm_commit_message "$AI_COMMIT_MSG")

    echo ""
    echo -e "${GREEN}📌 最终提交信息: ${YELLOW}$COMMIT_MSG${NC}"
    echo ""

    git commit -m "$COMMIT_MSG" || echo "没有新的更改需要提交"
    git push origin main

    echo -e "${GREEN}✅ GitHub 推送完成${NC}"
}
