#!/bin/bash
# ================================================================
# 文件名: go.ai.sh
# 中文名: AI API 调用库（DeepSeek / MoonShot）
# 创建时间: 2025-01-11
# 参考: YYSYYF 项目
# ================================================================
#
# 【使用方法】
# 1. 作为库引入：source go.ai.sh
# 2. 直接调用：./go.ai.sh "你的问题"
# 3. 管道输入：echo "你的问题" | ./go.ai.sh
#
# 【环境变量】
# APIKEY_MacOS_Code_DeepSeek - DeepSeek API 密钥（优先）
# APIKEY_MacOS_Code_MoonShot - MoonShot API 密钥（备选）
#
# 【函数列表】
# - call_ai_api(prompt, [system_prompt], [max_tokens]) - 通用 AI 调用
# - get_ai_commit_message() - 生成 Git 提交摘要
# ================================================================

# 如果作为库被 source，不执行主逻辑
[[ "${BASH_SOURCE[0]}" != "${0}" ]] && _GO_AI_SOURCED=true || _GO_AI_SOURCED=false

# 通用 AI API 调用函数
# 参数：$1=prompt, $2=system_prompt(可选), $3=max_tokens(可选，默认1200)
call_ai_api() {
    local PROMPT="$1"
    local SYSTEM_PROMPT="${2:-你是一个专业的AI助手。}"
    local MAX_TOKENS="${3:-1200}"

    if [ -z "$PROMPT" ]; then
        echo "错误：请提供 prompt" >&2
        return 1
    fi

    local API_KEY=""
    local API_URL=""
    local MODEL=""

    if [ -n "$APIKEY_MacOS_Code_DeepSeek" ]; then
        API_KEY="$APIKEY_MacOS_Code_DeepSeek"
        API_URL="https://api.deepseek.com/chat/completions"
        MODEL="deepseek-chat"
    elif [ -n "$APIKEY_MacOS_Code_MoonShot" ]; then
        API_KEY="$APIKEY_MacOS_Code_MoonShot"
        API_URL="https://api.moonshot.cn/v1/chat/completions"
        MODEL="moonshot-v1-8k"
    else
        echo "错误：未配置 AI API 密钥" >&2
        echo "请设置环境变量：APIKEY_MacOS_Code_DeepSeek 或 APIKEY_MacOS_Code_MoonShot" >&2
        return 1
    fi

    python3 -c "
import json
import urllib.request
import sys

prompt = '''${PROMPT}'''
system_prompt = '''${SYSTEM_PROMPT}'''

data = {
    'model': '${MODEL}',
    'messages': [
        {'role': 'system', 'content': system_prompt},
        {'role': 'user', 'content': prompt}
    ],
    'temperature': 0.3,
    'max_tokens': ${MAX_TOKENS}
}

req = urllib.request.Request(
    '${API_URL}',
    data=json.dumps(data).encode('utf-8'),
    headers={
        'Content-Type': 'application/json',
        'Authorization': 'Bearer ${API_KEY}'
    }
)

try:
    with urllib.request.urlopen(req, timeout=60) as response:
        result = json.loads(response.read().decode('utf-8'))
        print(result['choices'][0]['message']['content'].strip())
except urllib.error.HTTPError as e:
    print(f'HTTP错误: {e.code}', file=sys.stderr)
    sys.exit(1)
except urllib.error.URLError as e:
    print(f'网络错误: {e.reason}', file=sys.stderr)
    sys.exit(1)
except Exception as e:
    print(f'错误: {e}', file=sys.stderr)
    sys.exit(1)
" 2>/dev/null
}

# 生成 Git 提交摘要（专用函数）
get_ai_commit_message() {
    if [ -z "$(git status --porcelain)" ]; then
        echo "chore: 自动部署 $(date '+%Y-%m-%d %H:%M')"
        return
    fi

    local CHANGED_FILES=$(git diff --cached --name-status 2>/dev/null)
    if [ -z "$CHANGED_FILES" ]; then
        CHANGED_FILES=$(git diff --name-status 2>/dev/null)
    fi

    local DIFF_STAT=$(git diff --cached --stat 2>/dev/null)
    if [ -z "$DIFF_STAT" ]; then
        DIFF_STAT=$(git diff --stat 2>/dev/null)
    fi

    local PROMPT="你是一个专业的Git提交摘要生成专家。请根据以下变更信息，生成一个Conventional Commits格式提交摘要。

变更文件状态（A=新增, M=修改, D=删除）:
${CHANGED_FILES}

变更统计:
${DIFF_STAT}

生成要求:
1. 第一行: type: 简洁但准确的主标题
   - type必须从feat/fix/refactor/docs/style/chore中选择最合适的
   - 主标题要概括本次提交的核心内容

2. 第二行开始: 用'-'开头列出重要变更
   - 按功能模块分类
   - 包含具体的文件名或目录名
   - 简洁明了

3. 其他要求:
   - 中文输出
   - 总长度控制在500字符内"

    local SYSTEM_PROMPT="你是一个专业的Git提交摘要生成专家，擅长分析代码变更并生成详细准确的Conventional Commits格式提交信息。"

    local RESULT=$(call_ai_api_stream "$PROMPT" "$SYSTEM_PROMPT" 800)
    
    if [ -n "$RESULT" ]; then
        echo "$RESULT"
    else
        echo "chore: 自动部署 $(date '+%Y-%m-%d %H:%M')"
    fi
}

# 流式 AI API 调用（带实时显示）
call_ai_api_stream() {
    local PROMPT="$1"
    local SYSTEM_PROMPT="${2:-你是一个专业的AI助手。}"
    local MAX_TOKENS="${3:-1200}"

    if [ -z "$PROMPT" ]; then
        echo "错误：请提供 prompt" >&2
        return 1
    fi

    local API_KEY=""
    local API_URL=""
    local MODEL=""

    if [ -n "$APIKEY_MacOS_Code_DeepSeek" ]; then
        API_KEY="$APIKEY_MacOS_Code_DeepSeek"
        API_URL="https://api.deepseek.com/chat/completions"
        MODEL="deepseek-chat"
    elif [ -n "$APIKEY_MacOS_Code_MoonShot" ]; then
        API_KEY="$APIKEY_MacOS_Code_MoonShot"
        API_URL="https://api.moonshot.cn/v1/chat/completions"
        MODEL="moonshot-v1-8k"
    else
        echo "错误：未配置 AI API 密钥" >&2
        return 1
    fi

    AI_PROMPT="$PROMPT" \
    AI_SYSTEM_PROMPT="$SYSTEM_PROMPT" \
    AI_API_KEY="$API_KEY" \
    AI_API_URL="$API_URL" \
    AI_MODEL="$MODEL" \
    AI_MAX_TOKENS="$MAX_TOKENS" \
    _call_ai_api_stream_python
}


# 内部 Python 调用函数
_call_ai_api_stream_python() {
    python3 << 'PYTHON_SCRIPT'
import json
import urllib.request
import sys
import os
import unicodedata

prompt = os.environ.get('AI_PROMPT', '')
system_prompt = os.environ.get('AI_SYSTEM_PROMPT', '你是一个专业的AI助手。')
api_key = os.environ.get('AI_API_KEY', '')
api_url = os.environ.get('AI_API_URL', '')
model = os.environ.get('AI_MODEL', '')
max_tokens = int(os.environ.get('AI_MAX_TOKENS', '1200'))
display_height = 6

CYAN = '\033[36m'
YELLOW = '\033[33m'
GREEN = '\033[32m'
NC = '\033[0m'
HIDE_CURSOR = '\033[?25l'
SHOW_CURSOR = '\033[?25h'
CLEAR_LINE = '\033[K'

def get_display_width(s):
    width = 0
    for char in s:
        if unicodedata.east_asian_width(char) in ('F', 'W', 'A'):
            width += 2
        else:
            width += 1
    return width

def truncate_to_width(s, max_width):
    width = 0
    result = []
    for char in s:
        char_width = 2 if unicodedata.east_asian_width(char) in ('F', 'W', 'A') else 1
        if width + char_width > max_width:
            break
        result.append(char)
        width += char_width
    return ''.join(result)

def pad_to_width(s, target_width):
    current_width = get_display_width(s)
    return s + ' ' * max(0, target_width - current_width)

BOX_TOTAL_LINES = display_height + 4
WIDTH = 72
CONTENT_WIDTH = WIDTH - 4
first_draw = True

def draw_box(lines, title="🤖 AI 思考中 ..."):
    global first_draw
    
    if not first_draw:
        sys.stderr.write(f'\033[{BOX_TOTAL_LINES}A')
    first_draw = False
    
    title_padded = pad_to_width(truncate_to_width(title, CONTENT_WIDTH), CONTENT_WIDTH)
    sys.stderr.write(f'{CLEAR_LINE}{CYAN}┌{"─" * (WIDTH - 2)}┐{NC}\n')
    sys.stderr.write(f'{CLEAR_LINE}{CYAN}│{NC} {YELLOW}{title_padded}{NC} {CYAN}│{NC}\n')
    sys.stderr.write(f'{CLEAR_LINE}{CYAN}├{"─" * (WIDTH - 2)}┤{NC}\n')
    
    display_lines = lines[-display_height:] if len(lines) > display_height else lines
    for i in range(display_height):
        if i < len(display_lines):
            line_padded = pad_to_width(truncate_to_width(display_lines[i], CONTENT_WIDTH), CONTENT_WIDTH)
            sys.stderr.write(f'{CLEAR_LINE}{CYAN}│{NC} {line_padded} {CYAN}│{NC}\n')
        else:
            sys.stderr.write(f'{CLEAR_LINE}{CYAN}│{NC} {" " * CONTENT_WIDTH} {CYAN}│{NC}\n')
    
    sys.stderr.write(f'{CLEAR_LINE}{CYAN}└{"─" * (WIDTH - 2)}┘{NC}\n')
    sys.stderr.flush()

data = {
    'model': model,
    'messages': [
        {'role': 'system', 'content': system_prompt},
        {'role': 'user', 'content': prompt}
    ],
    'temperature': 0.3,
    'max_tokens': max_tokens,
    'stream': True
}

req = urllib.request.Request(
    api_url,
    data=json.dumps(data).encode('utf-8'),
    headers={
        'Content-Type': 'application/json',
        'Authorization': f'Bearer {api_key}',
        'Accept': 'text/event-stream'
    }
)

try:
    sys.stderr.write(HIDE_CURSOR)
    draw_box([], "🤖 AI 思考中 ...")
    
    full_content = ""
    lines = []
    current_line = ""
    
    with urllib.request.urlopen(req, timeout=120) as response:
        for raw_line in response:
            line = raw_line.decode('utf-8').strip()
            if not line or not line.startswith('data: '):
                continue
            
            json_str = line[6:]
            if json_str == '[DONE]':
                break
            
            try:
                chunk = json.loads(json_str)
                content = chunk.get('choices', [{}])[0].get('delta', {}).get('content', '')
                
                if content:
                    full_content += content
                    current_line += content
                    
                    if '\n' in current_line:
                        parts = current_line.split('\n')
                        lines.extend(parts[:-1])
                        current_line = parts[-1]
                    
                    draw_box(lines + ([current_line] if current_line else []), "🤖 AI 生成中 ...")
            except:
                continue
    
    sys.stderr.write(f'\033[{BOX_TOTAL_LINES}A')
    for _ in range(BOX_TOTAL_LINES):
        sys.stderr.write(f'{CLEAR_LINE}\n')
    sys.stderr.write(f'\033[{BOX_TOTAL_LINES}A')
    sys.stderr.write(f'{GREEN}✓ AI 生成完成{NC}\n{SHOW_CURSOR}')
    sys.stderr.flush()
    
    print(full_content.strip())
    
except Exception as e:
    sys.stderr.write(f'{SHOW_CURSOR}\n错误: {e}\n')
    sys.exit(1)
PYTHON_SCRIPT
}

# 如果直接执行（非 source），则作为命令行工具使用
if [ "$_GO_AI_SOURCED" = false ]; then
    SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
    [ -f "$SCRIPT_DIR/go.lib.sh" ] && source "$SCRIPT_DIR/go.lib.sh"

    if [ -n "$1" ]; then
        PROMPT="$*"
    elif [ ! -t 0 ]; then
        PROMPT=$(cat)
    else
        echo "用法: ./go.ai.sh \"你的问题\""
        echo "      echo \"你的问题\" | ./go.ai.sh"
        echo ""
        echo "环境变量:"
        echo "  APIKEY_MacOS_Code_DeepSeek - DeepSeek API 密钥（优先）"
        echo "  APIKEY_MacOS_Code_MoonShot - MoonShot API 密钥（备选）"
        exit 0
    fi

    call_ai_api "$PROMPT"
fi
