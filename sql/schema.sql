-- 轻养到家 - 数据库表结构
-- ZERO 框架规范
-- 系统前缀：qy_（轻养）

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- 1. 用户表 qy_user_list
-- ----------------------------
DROP TABLE IF EXISTS `qy_user_list`;
CREATE TABLE `qy_user_list` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
    `base_auth_phone` VARCHAR(20) NOT NULL DEFAULT '' COMMENT '手机号',
    `base_auth_openid` VARCHAR(64) NOT NULL DEFAULT '' COMMENT '微信 OpenID',
    `base_profile_nickname` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '昵称',
    `base_profile_avatar` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '头像 URL',
    `base_profile_gender` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '性别 0未知 1男 2女',
    `base_status_active` TINYINT UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态 1正常 0禁用',
    `base_time_created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    `base_time_updated` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_phone` (`base_auth_phone`),
    KEY `idx_openid` (`base_auth_openid`),
    KEY `idx_status` (`base_status_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='用户表';

-- ----------------------------
-- 2. 技师表 qy_technician_list
-- ----------------------------
DROP TABLE IF EXISTS `qy_technician_list`;
CREATE TABLE `qy_technician_list` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
    `base_auth_phone` VARCHAR(20) NOT NULL DEFAULT '' COMMENT '手机号',
    `base_auth_user_id` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '关联用户ID',
    `base_profile_realname` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '真实姓名',
    `base_profile_avatar` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '头像',
    `base_profile_gender` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '性别',
    `base_profile_intro` TEXT COMMENT '个人简介',
    `base_profile_photos` JSON COMMENT '照片集',
    `base_cert_idcard_front` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '身份证正面',
    `base_cert_idcard_back` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '身份证背面',
    `base_cert_health` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '健康证',
    `base_cert_professional` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '专业证书',
    `base_shop_id` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '所属商家ID',
    `base_shop_commission_rate` DECIMAL(5,2) NOT NULL DEFAULT 0.00 COMMENT '与商家分成比例',
    `base_stat_order_count` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '完成订单数',
    `base_stat_rating_avg` DECIMAL(3,2) NOT NULL DEFAULT 0.00 COMMENT '平均评分',
    `base_stat_rating_count` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '评价数',
    `base_status_verify` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '认证状态 0待审 1通过 2拒绝',
    `base_status_online` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '在线状态 0离线 1在线 2忙碌',
    `base_status_active` TINYINT UNSIGNED NOT NULL DEFAULT 1 COMMENT '账号状态',
    `base_location_lat` DECIMAL(10,6) NOT NULL DEFAULT 0.000000 COMMENT '当前纬度',
    `base_location_lng` DECIMAL(10,6) NOT NULL DEFAULT 0.000000 COMMENT '当前经度',
    `base_time_created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    `base_time_updated` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_phone` (`base_auth_phone`),
    KEY `idx_shop` (`base_shop_id`),
    KEY `idx_status_verify` (`base_status_verify`),
    KEY `idx_status_online` (`base_status_online`),
    KEY `idx_status_active` (`base_status_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='技师表';

-- ----------------------------
-- 3. 商家表 qy_shop_list
-- ----------------------------
DROP TABLE IF EXISTS `qy_shop_list`;
CREATE TABLE `qy_shop_list` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
    `base_auth_phone` VARCHAR(20) NOT NULL DEFAULT '' COMMENT '联系电话',
    `base_profile_name` VARCHAR(100) NOT NULL DEFAULT '' COMMENT '店铺名称',
    `base_profile_logo` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '店铺 Logo',
    `base_profile_intro` TEXT COMMENT '店铺简介',
    `base_profile_address` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '店铺地址',
    `base_cert_license` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '营业执照',
    `base_cert_permit` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '卫生许可证',
    `base_contact_name` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '联系人姓名',
    `base_stat_technician_count` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '技师数量',
    `base_stat_order_count` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '总订单数',
    `base_commission_rate` DECIMAL(5,2) NOT NULL DEFAULT 10.00 COMMENT '平台抽成比例',
    `base_status_verify` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '认证状态',
    `base_status_active` TINYINT UNSIGNED NOT NULL DEFAULT 1 COMMENT '账号状态',
    `base_time_created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    `base_time_updated` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_phone` (`base_auth_phone`),
    KEY `idx_status_verify` (`base_status_verify`),
    KEY `idx_status_active` (`base_status_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='商家表';

-- ----------------------------
-- 4. 服务分类表 qy_service_category_list
-- ----------------------------
DROP TABLE IF EXISTS `qy_service_category_list`;
CREATE TABLE `qy_service_category_list` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
    `base_profile_name` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '分类名称',
    `base_profile_icon` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '图标',
    `base_profile_desc` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '描述',
    `base_sort_order` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
    `base_status_active` TINYINT UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态',
    `base_time_created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    `base_time_updated` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
    PRIMARY KEY (`id`),
    KEY `idx_sort` (`base_sort_order`),
    KEY `idx_status` (`base_status_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='服务分类表';

-- ----------------------------
-- 5. 服务项目表 qy_service_item_list
-- ----------------------------
DROP TABLE IF EXISTS `qy_service_item_list`;
CREATE TABLE `qy_service_item_list` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
    `base_category_id` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '分类ID',
    `base_profile_name` VARCHAR(100) NOT NULL DEFAULT '' COMMENT '服务名称',
    `base_profile_icon` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '图标',
    `base_profile_desc` TEXT COMMENT '服务描述',
    `base_profile_process` TEXT COMMENT '服务流程',
    `base_profile_notice` TEXT COMMENT '注意事项',
    `base_price_base` DECIMAL(10,2) NOT NULL DEFAULT 0.00 COMMENT '基础价格',
    `base_price_max` DECIMAL(10,2) NOT NULL DEFAULT 0.00 COMMENT '最高价格',
    `base_duration_minutes` INT UNSIGNED NOT NULL DEFAULT 60 COMMENT '服务时长（分钟）',
    `base_sort_order` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
    `base_status_active` TINYINT UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态',
    `base_time_created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    `base_time_updated` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
    PRIMARY KEY (`id`),
    KEY `idx_category` (`base_category_id`),
    KEY `idx_sort` (`base_sort_order`),
    KEY `idx_status` (`base_status_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='服务项目表';

-- ----------------------------
-- 6. 订单表 qy_order_list
-- ----------------------------
DROP TABLE IF EXISTS `qy_order_list`;
CREATE TABLE `qy_order_list` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
    `base_order_no` VARCHAR(32) NOT NULL DEFAULT '' COMMENT '订单号',
    `base_user_id` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户ID',
    `base_technician_id` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '技师ID',
    `base_shop_id` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '商家ID',
    `base_service_id` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '服务项目ID',
    `base_service_name` VARCHAR(100) NOT NULL DEFAULT '' COMMENT '服务名称',
    `base_address_contact` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '联系人',
    `base_address_phone` VARCHAR(20) NOT NULL DEFAULT '' COMMENT '联系电话',
    `base_address_detail` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '详细地址',
    `base_address_lat` DECIMAL(10,6) NOT NULL DEFAULT 0.000000 COMMENT '纬度',
    `base_address_lng` DECIMAL(10,6) NOT NULL DEFAULT 0.000000 COMMENT '经度',
    `base_time_scheduled` DATETIME DEFAULT NULL COMMENT '预约时间',
    `base_time_started` DATETIME DEFAULT NULL COMMENT '服务开始时间',
    `base_time_completed` DATETIME DEFAULT NULL COMMENT '服务完成时间',
    `base_duration_minutes` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '服务时长',
    `base_price_service` DECIMAL(10,2) NOT NULL DEFAULT 0.00 COMMENT '服务费',
    `base_price_tip` DECIMAL(10,2) NOT NULL DEFAULT 0.00 COMMENT '小费',
    `base_price_total` DECIMAL(10,2) NOT NULL DEFAULT 0.00 COMMENT '总金额',
    `base_pay_method` VARCHAR(20) NOT NULL DEFAULT '' COMMENT '支付方式',
    `base_pay_time` DATETIME DEFAULT NULL COMMENT '支付时间',
    `base_pay_transaction_id` VARCHAR(64) NOT NULL DEFAULT '' COMMENT '支付流水号',
    `base_status_order` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '订单状态 0待支付 1已支付 2已接单 3出发中 4服务中 5已完成 6已取消 7已退款',
    `base_status_pay` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '支付状态 0未支付 1已支付 2已退款',
    `base_cancel_reason` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '取消原因',
    `base_cancel_time` DATETIME DEFAULT NULL COMMENT '取消时间',
    `base_remark` VARCHAR(500) NOT NULL DEFAULT '' COMMENT '用户备注',
    `base_time_created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    `base_time_updated` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_order_no` (`base_order_no`),
    KEY `idx_user` (`base_user_id`),
    KEY `idx_technician` (`base_technician_id`),
    KEY `idx_shop` (`base_shop_id`),
    KEY `idx_status_order` (`base_status_order`),
    KEY `idx_status_pay` (`base_status_pay`),
    KEY `idx_time_scheduled` (`base_time_scheduled`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='订单表';


-- ----------------------------
-- 7. 评价表 qy_review_list
-- ----------------------------
DROP TABLE IF EXISTS `qy_review_list`;
CREATE TABLE `qy_review_list` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
    `base_order_id` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '订单ID',
    `base_user_id` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户ID',
    `base_technician_id` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '技师ID',
    `base_rating` TINYINT UNSIGNED NOT NULL DEFAULT 5 COMMENT '评分 1-5',
    `base_content` TEXT COMMENT '评价内容',
    `base_photos` JSON COMMENT '评价图片',
    `base_reply` TEXT COMMENT '技师回复',
    `base_reply_time` DATETIME DEFAULT NULL COMMENT '回复时间',
    `base_status_visible` TINYINT UNSIGNED NOT NULL DEFAULT 1 COMMENT '是否显示',
    `base_time_created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    `base_time_updated` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_order` (`base_order_id`),
    KEY `idx_user` (`base_user_id`),
    KEY `idx_technician` (`base_technician_id`),
    KEY `idx_rating` (`base_rating`),
    KEY `idx_visible` (`base_status_visible`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='评价表';

-- ----------------------------
-- 8. 技师排班表 qy_technician_schedule_list
-- ----------------------------
DROP TABLE IF EXISTS `qy_technician_schedule_list`;
CREATE TABLE `qy_technician_schedule_list` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
    `base_technician_id` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '技师ID',
    `base_date` DATE NOT NULL COMMENT '日期',
    `base_time_start` TIME NOT NULL COMMENT '开始时间',
    `base_time_end` TIME NOT NULL COMMENT '结束时间',
    `base_status` TINYINT UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态 1可用 0不可用',
    `base_time_created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    `base_time_updated` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
    PRIMARY KEY (`id`),
    KEY `idx_technician` (`base_technician_id`),
    KEY `idx_date` (`base_date`),
    KEY `idx_technician_date` (`base_technician_id`, `base_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='技师排班表';

-- ----------------------------
-- 9. 钱包表 qy_wallet_list
-- ----------------------------
DROP TABLE IF EXISTS `qy_wallet_list`;
CREATE TABLE `qy_wallet_list` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
    `base_owner_type` VARCHAR(20) NOT NULL DEFAULT '' COMMENT '所有者类型 technician/shop',
    `base_owner_id` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '所有者ID',
    `base_balance_available` DECIMAL(12,2) NOT NULL DEFAULT 0.00 COMMENT '可用余额',
    `base_balance_pending` DECIMAL(12,2) NOT NULL DEFAULT 0.00 COMMENT '待结算余额',
    `base_balance_total` DECIMAL(12,2) NOT NULL DEFAULT 0.00 COMMENT '累计收入',
    `base_time_created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    `base_time_updated` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_owner` (`base_owner_type`, `base_owner_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='钱包表';

-- ----------------------------
-- 10. 钱包流水表 qy_wallet_transaction_list
-- ----------------------------
DROP TABLE IF EXISTS `qy_wallet_transaction_list`;
CREATE TABLE `qy_wallet_transaction_list` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
    `base_wallet_id` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '钱包ID',
    `base_type` VARCHAR(20) NOT NULL DEFAULT '' COMMENT '类型 income/withdraw/refund',
    `base_amount` DECIMAL(12,2) NOT NULL DEFAULT 0.00 COMMENT '金额',
    `base_balance_after` DECIMAL(12,2) NOT NULL DEFAULT 0.00 COMMENT '交易后余额',
    `base_order_id` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '关联订单ID',
    `base_remark` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '备注',
    `base_status` TINYINT UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态 0处理中 1成功 2失败',
    `base_time_created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    `base_time_updated` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
    PRIMARY KEY (`id`),
    KEY `idx_wallet` (`base_wallet_id`),
    KEY `idx_type` (`base_type`),
    KEY `idx_order` (`base_order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='钱包流水表';

-- ----------------------------
-- 11. 用户地址表 qy_user_address_list
-- ----------------------------
DROP TABLE IF EXISTS `qy_user_address_list`;
CREATE TABLE `qy_user_address_list` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
    `base_user_id` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户ID',
    `base_contact_name` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '联系人',
    `base_contact_phone` VARCHAR(20) NOT NULL DEFAULT '' COMMENT '联系电话',
    `base_address_province` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '省',
    `base_address_city` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '市',
    `base_address_district` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '区',
    `base_address_detail` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '详细地址',
    `base_address_lat` DECIMAL(10,6) NOT NULL DEFAULT 0.000000 COMMENT '纬度',
    `base_address_lng` DECIMAL(10,6) NOT NULL DEFAULT 0.000000 COMMENT '经度',
    `base_is_default` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否默认',
    `base_time_created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    `base_time_updated` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
    PRIMARY KEY (`id`),
    KEY `idx_user` (`base_user_id`),
    KEY `idx_default` (`base_user_id`, `base_is_default`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='用户地址表';

-- ----------------------------
-- 12. 技师服务关联表 qy_technician_service_list
-- ----------------------------
DROP TABLE IF EXISTS `qy_technician_service_list`;
CREATE TABLE `qy_technician_service_list` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
    `base_technician_id` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '技师ID',
    `base_service_id` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '服务项目ID',
    `base_price` DECIMAL(10,2) NOT NULL DEFAULT 0.00 COMMENT '技师定价',
    `base_status_active` TINYINT UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态',
    `base_time_created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    `base_time_updated` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_technician_service` (`base_technician_id`, `base_service_id`),
    KEY `idx_service` (`base_service_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='技师服务关联表';

-- ----------------------------
-- 13. 管理员表 qy_admin_list
-- ----------------------------
DROP TABLE IF EXISTS `qy_admin_list`;
CREATE TABLE `qy_admin_list` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
    `base_auth_username` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '用户名',
    `base_auth_password` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '密码哈希',
    `base_profile_name` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '姓名',
    `base_profile_avatar` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '头像',
    `base_role` VARCHAR(20) NOT NULL DEFAULT 'admin' COMMENT '角色',
    `base_status_active` TINYINT UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态',
    `base_time_last_login` DATETIME DEFAULT NULL COMMENT '最后登录时间',
    `base_time_created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    `base_time_updated` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_username` (`base_auth_username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='管理员表';

-- ----------------------------
-- 14. 系统配置表 qy_config_list
-- ----------------------------
DROP TABLE IF EXISTS `qy_config_list`;
CREATE TABLE `qy_config_list` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
    `base_key` VARCHAR(100) NOT NULL DEFAULT '' COMMENT '配置键',
    `base_value` TEXT COMMENT '配置值',
    `base_desc` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '描述',
    `base_time_created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    `base_time_updated` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_key` (`base_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='系统配置表';

SET FOREIGN_KEY_CHECKS = 1;
