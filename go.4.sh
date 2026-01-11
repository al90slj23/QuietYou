#!/bin/bash
# 轻养到家 - 数据库操作脚本
# ZERO 框架规范

source "$(dirname "$0")/go.lib.sh"

# 加载环境变量
load_env

# 数据库配置
DB_HOST=${DB_HOST:-"localhost"}
DB_PORT=${DB_PORT:-"3306"}
DB_NAME=${DB_NAME:-"quietyou"}
DB_USER=${DB_USER:-"root"}
DB_PASS=${DB_PASS:-""}

show_db_help() {
    echo "数据库操作命令:"
    echo ""
    echo "用法: ./go.sh db <子命令>"
    echo ""
    echo "子命令:"
    echo "  init      初始化数据库（创建表）"
    echo "  seed      填充测试数据"
    echo "  reset     重置数据库（删除并重建）"
    echo "  backup    备份数据库"
    echo "  restore   恢复数据库"
    echo ""
}

# 执行 SQL 文件
exec_sql_file() {
    local file=$1
    if [ -f "$file" ]; then
        log_info "执行 SQL 文件: $file"
        mysql -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" < "$file"
    else
        log_error "SQL 文件不存在: $file"
        return 1
    fi
}

case "${2:-help}" in
    init)
        log_info "初始化数据库..."
        check_mysql || exit 1
        exec_sql_file "sql/schema.sql"
        log_success "数据库初始化完成!"
        ;;
    seed)
        log_info "填充测试数据..."
        check_mysql || exit 1
        exec_sql_file "sql/seed.sql"
        log_success "测试数据填充完成!"
        ;;
    reset)
        log_warning "即将重置数据库，所有数据将被删除!"
        read -p "确认继续? (y/N) " confirm
        if [ "$confirm" = "y" ] || [ "$confirm" = "Y" ]; then
            check_mysql || exit 1
            exec_sql_file "sql/schema.sql"
            log_success "数据库重置完成!"
        else
            log_info "操作已取消"
        fi
        ;;
    backup)
        log_info "备份数据库..."
        check_mysql || exit 1
        BACKUP_FILE="backup/db_$(date +%Y%m%d_%H%M%S).sql"
        mkdir -p backup
        mysqldump -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" > "$BACKUP_FILE"
        log_success "数据库已备份到: $BACKUP_FILE"
        ;;
    restore)
        if [ -z "$3" ]; then
            log_error "请指定备份文件路径"
            exit 1
        fi
        log_info "恢复数据库..."
        check_mysql || exit 1
        exec_sql_file "$3"
        log_success "数据库恢复完成!"
        ;;
    help|--help|-h|*)
        show_db_help
        ;;
esac
