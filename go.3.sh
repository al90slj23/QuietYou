#!/bin/bash
# 轻养到家 - 测试脚本
# ZERO 框架规范

source "$(dirname "$0")/go.lib.sh"

log_info "运行测试..."

# 检查环境
check_php || exit 1

# 运行 PHP 单元测试
if [ -d "tests" ]; then
    log_info "运行 PHP 单元测试..."
    if check_command phpunit; then
        phpunit --configuration phpunit.xml
    else
        log_warning "PHPUnit 未安装，跳过 PHP 测试"
    fi
fi

# 运行前端测试
if [ -f "package.json" ]; then
    log_info "运行前端测试..."
    if check_command npm; then
        npm test
    else
        log_warning "npm 未安装，跳过前端测试"
    fi
fi

log_success "测试完成!"
