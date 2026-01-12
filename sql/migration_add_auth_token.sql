-- ================================================================
-- 轻养到家 - 数据库迁移脚本
-- 添加认证 Token 字段
-- 创建时间: 2026-01-13
-- ================================================================

-- 用户表添加 token 字段
ALTER TABLE `qy_user_list` 
ADD COLUMN `base_auth_token` VARCHAR(128) NOT NULL DEFAULT '' COMMENT '认证Token|登录凭证' AFTER `base_auth_openid`,
ADD COLUMN `base_auth_expire_at` DATETIME DEFAULT NULL COMMENT 'Token过期时间' AFTER `base_auth_token`,
ADD COLUMN `base_status` VARCHAR(20) NOT NULL DEFAULT 'active' COMMENT '账号状态|active=正常, disabled=禁用' AFTER `base_status_active`,
ADD COLUMN `is_deleted` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '软删除标记|0=正常, 1=已删除' AFTER `base_time_updated`,
ADD INDEX `idx_token` (`base_auth_token`);

-- 技师表添加 token 字段
ALTER TABLE `qy_technician_list` 
ADD COLUMN `base_auth_token` VARCHAR(128) NOT NULL DEFAULT '' COMMENT '认证Token|登录凭证' AFTER `base_auth_user_id`,
ADD COLUMN `base_auth_expire_at` DATETIME DEFAULT NULL COMMENT 'Token过期时间' AFTER `base_auth_token`,
ADD COLUMN `base_status` VARCHAR(20) NOT NULL DEFAULT 'active' COMMENT '账号状态|active=正常, disabled=禁用' AFTER `base_status_active`,
ADD COLUMN `is_deleted` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '软删除标记|0=正常, 1=已删除' AFTER `base_time_updated`,
ADD INDEX `idx_token` (`base_auth_token`);

-- 商家表添加 token 字段
ALTER TABLE `qy_shop_list` 
ADD COLUMN `base_auth_token` VARCHAR(128) NOT NULL DEFAULT '' COMMENT '认证Token|登录凭证' AFTER `base_auth_phone`,
ADD COLUMN `base_auth_expire_at` DATETIME DEFAULT NULL COMMENT 'Token过期时间' AFTER `base_auth_token`,
ADD COLUMN `base_status` VARCHAR(20) NOT NULL DEFAULT 'active' COMMENT '账号状态|active=正常, disabled=禁用' AFTER `base_status_active`,
ADD COLUMN `is_deleted` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '软删除标记|0=正常, 1=已删除' AFTER `base_time_updated`,
ADD INDEX `idx_token` (`base_auth_token`);
