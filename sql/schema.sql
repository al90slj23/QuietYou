-- ================================================================
-- 轻养到家 - 数据库表结构
-- ZERO 框架规范
-- 系统前缀：qy_（轻养）
-- ================================================================
--
-- 【表注释格式】中文名称|功能说明
-- 【字段注释格式】序号|中文字段分类|中文字段名|详细说明|是否显示
--
-- ================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- 1. 用户表 qy_user_list
-- ----------------------------
DROP TABLE IF EXISTS `qy_user_list`;
CREATE TABLE `qy_user_list` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '0|系统|主键|自增主键|0',
    `base_auth_phone` VARCHAR(20) NOT NULL DEFAULT '' COMMENT '1|认证|手机号|用于登录的手机号码|1',
    `base_auth_openid` VARCHAR(64) NOT NULL DEFAULT '' COMMENT '2|认证|微信OpenID|微信登录唯一标识|0',
    `base_profile_nickname` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '3|资料|昵称|用户显示名称|1',
    `base_profile_avatar` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '4|资料|头像|用户头像URL|1',
    `base_profile_gender` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '5|资料|性别|0=未知, 1=男, 2=女|1',
    `base_status_active` TINYINT UNSIGNED NOT NULL DEFAULT 1 COMMENT '6|状态|账号状态|0=禁用, 1=正常|1',
    `base_time_created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '7|时间|创建时间|账号创建时间|0',
    `base_time_updated` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '8|时间|更新时间|最后更新时间|0',
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_phone` (`base_auth_phone`),
    KEY `idx_openid` (`base_auth_openid`),
    KEY `idx_status` (`base_status_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='用户主表|存储C端用户基础信息和认证信息';

-- ----------------------------
-- 2. 技师表 qy_technician_list
-- ----------------------------
DROP TABLE IF EXISTS `qy_technician_list`;
CREATE TABLE `qy_technician_list` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '0|系统|主键|自增主键|0',
    `base_auth_phone` VARCHAR(20) NOT NULL DEFAULT '' COMMENT '1|认证|手机号|技师登录手机号|1',
    `base_auth_user_id` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '2|认证|关联用户ID|关联qy_user_list.id|0',
    `base_type` TINYINT UNSIGNED NOT NULL DEFAULT 1 COMMENT '3|类型|技师类型|1=店铺技师, 2=散技师(独立)|1',
    `base_profile_realname` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '4|资料|真实姓名|身份证姓名|1',
    `base_profile_avatar` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '5|资料|头像|技师展示头像|1',
    `base_profile_gender` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '6|资料|性别|0=未知, 1=男, 2=女|1',
    `base_profile_intro` TEXT COMMENT '7|资料|个人简介|技师自我介绍|1',
    `base_profile_photos` JSON COMMENT '8|资料|照片集|技师展示照片JSON数组|0',
    `base_profile_experience_years` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '9|资料|从业年限|从业经验年数|1',
    `base_cert_idcard_front` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '10|证件|身份证正面|身份证正面照片URL|0',
    `base_cert_idcard_back` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '11|证件|身份证背面|身份证背面照片URL|0',
    `base_cert_health` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '12|证件|健康证|健康证照片URL|0',
    `base_cert_professional` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '13|证件|专业证书|专业资质证书URL|0',
    `base_shop_id` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '14|归属|商家ID|所属商家qy_shop_list.id, 散技师为0|1',
    `base_shop_commission_rate` DECIMAL(5,2) NOT NULL DEFAULT 0.00 COMMENT '15|归属|分成比例|与商家的分成比例|0',
    `base_is_certified` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '16|认证|平台认证|0=未认证, 1=已认证(培训门店认证)|1',
    `base_stat_order_count` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '17|统计|完成订单数|累计完成订单数量|1',
    `base_stat_rating_avg` DECIMAL(3,2) NOT NULL DEFAULT 0.00 COMMENT '18|统计|平均评分|用户评分平均值1-5|1',
    `base_stat_rating_count` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '19|统计|评价数|累计收到评价数量|1',
    `base_stat_repeat_rate` DECIMAL(5,2) NOT NULL DEFAULT 0.00 COMMENT '20|统计|复购率|顾客复购百分比|1',
    `base_status_verify` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '21|状态|认证状态|0=待审核, 1=已通过, 2=已拒绝|1',
    `base_status_online` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '22|状态|在线状态|0=离线, 1=在线空闲, 2=服务中, 3=休息中|1',
    `base_status_active` TINYINT UNSIGNED NOT NULL DEFAULT 1 COMMENT '23|状态|账号状态|0=禁用, 1=正常|1',
    `base_setting_accept_order` TINYINT UNSIGNED NOT NULL DEFAULT 1 COMMENT '24|设置|接单开关|0=暂停接单, 1=正常接单|1',
    `base_setting_service_range` INT UNSIGNED NOT NULL DEFAULT 10 COMMENT '25|设置|服务范围|上门服务范围公里数|1',
    `base_location_lat` DECIMAL(10,6) NOT NULL DEFAULT 0.000000 COMMENT '26|位置|当前纬度|实时位置纬度|0',
    `base_location_lng` DECIMAL(10,6) NOT NULL DEFAULT 0.000000 COMMENT '27|位置|当前经度|实时位置经度|0',
    `base_time_created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '28|时间|创建时间|账号创建时间|0',
    `base_time_updated` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '29|时间|更新时间|最后更新时间|0',
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_phone` (`base_auth_phone`),
    KEY `idx_type` (`base_type`),
    KEY `idx_shop` (`base_shop_id`),
    KEY `idx_certified` (`base_is_certified`),
    KEY `idx_status_verify` (`base_status_verify`),
    KEY `idx_status_online` (`base_status_online`),
    KEY `idx_status_active` (`base_status_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='技师主表|存储技师基础信息、资质证件和服务状态';

-- ----------------------------
-- 3. 商家表 qy_shop_list
-- ----------------------------
DROP TABLE IF EXISTS `qy_shop_list`;
CREATE TABLE `qy_shop_list` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '0|系统|主键|自增主键|0',
    `base_auth_phone` VARCHAR(20) NOT NULL DEFAULT '' COMMENT '1|认证|联系电话|商家登录手机号|1',
    `base_profile_name` VARCHAR(100) NOT NULL DEFAULT '' COMMENT '2|资料|店铺名称|商家展示名称|1',
    `base_profile_logo` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '3|资料|店铺Logo|商家Logo图片URL|1',
    `base_profile_intro` TEXT COMMENT '4|资料|店铺简介|商家介绍说明|1',
    `base_profile_address` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '5|资料|店铺地址|商家实际地址|1',
    `base_cert_license` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '6|证件|营业执照|营业执照照片URL|0',
    `base_cert_permit` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '7|证件|卫生许可证|卫生许可证照片URL|0',
    `base_contact_name` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '8|联系|联系人姓名|商家负责人姓名|1',
    `base_stat_technician_count` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '9|统计|技师数量|旗下技师总数|1',
    `base_stat_order_count` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '10|统计|订单数量|累计订单总数|1',
    `base_commission_rate` DECIMAL(5,2) NOT NULL DEFAULT 10.00 COMMENT '11|财务|平台抽成|平台抽成比例百分比|0',
    `base_status_verify` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '12|状态|认证状态|0=待审核, 1=已通过, 2=已拒绝|1',
    `base_status_active` TINYINT UNSIGNED NOT NULL DEFAULT 1 COMMENT '13|状态|账号状态|0=禁用, 1=正常|1',
    `base_time_created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '14|时间|创建时间|账号创建时间|0',
    `base_time_updated` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '15|时间|更新时间|最后更新时间|0',
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_phone` (`base_auth_phone`),
    KEY `idx_status_verify` (`base_status_verify`),
    KEY `idx_status_active` (`base_status_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='商家主表|存储入驻商家基础信息和资质证件';


-- ----------------------------
-- 4. 服务分类表 qy_service_category_list
-- ----------------------------
DROP TABLE IF EXISTS `qy_service_category_list`;
CREATE TABLE `qy_service_category_list` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '0|系统|主键|自增主键|0',
    `base_profile_name` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '1|资料|分类名称|服务分类显示名称|1',
    `base_profile_icon` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '2|资料|图标|分类图标URL或图标名|1',
    `base_profile_desc` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '3|资料|描述|分类简要描述|1',
    `base_sort_order` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '4|排序|排序权重|数值越小越靠前|0',
    `base_status_active` TINYINT UNSIGNED NOT NULL DEFAULT 1 COMMENT '5|状态|启用状态|0=禁用, 1=启用|1',
    `base_time_created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '6|时间|创建时间|记录创建时间|0',
    `base_time_updated` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '7|时间|更新时间|最后更新时间|0',
    PRIMARY KEY (`id`),
    KEY `idx_sort` (`base_sort_order`),
    KEY `idx_status` (`base_status_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='服务分类表|存储服务项目的分类信息';

-- ----------------------------
-- 5. 服务项目表 qy_service_item_list
-- ----------------------------
DROP TABLE IF EXISTS `qy_service_item_list`;
CREATE TABLE `qy_service_item_list` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '0|系统|主键|自增主键|0',
    `base_category_id` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '1|归属|分类ID|所属分类qy_service_category_list.id|1',
    `base_profile_name` VARCHAR(100) NOT NULL DEFAULT '' COMMENT '2|资料|服务名称|服务项目显示名称|1',
    `base_profile_icon` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '3|资料|图标|服务图标URL|1',
    `base_profile_desc` TEXT COMMENT '4|资料|服务描述|服务详细介绍|1',
    `base_profile_process` TEXT COMMENT '5|资料|服务流程|服务步骤说明|0',
    `base_profile_notice` TEXT COMMENT '6|资料|注意事项|服务注意事项|0',
    `base_price_base` DECIMAL(10,2) NOT NULL DEFAULT 0.00 COMMENT '7|价格|基础价格|服务起步价格|1',
    `base_price_max` DECIMAL(10,2) NOT NULL DEFAULT 0.00 COMMENT '8|价格|最高价格|服务最高价格|1',
    `base_duration_minutes` INT UNSIGNED NOT NULL DEFAULT 60 COMMENT '9|时长|服务时长|服务时长分钟数|1',
    `base_sort_order` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '10|排序|排序权重|数值越小越靠前|0',
    `base_status_active` TINYINT UNSIGNED NOT NULL DEFAULT 1 COMMENT '11|状态|启用状态|0=禁用, 1=启用|1',
    `base_time_created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '12|时间|创建时间|记录创建时间|0',
    `base_time_updated` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '13|时间|更新时间|最后更新时间|0',
    PRIMARY KEY (`id`),
    KEY `idx_category` (`base_category_id`),
    KEY `idx_sort` (`base_sort_order`),
    KEY `idx_status` (`base_status_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='服务项目表|存储具体服务项目信息和定价';

-- ----------------------------
-- 6. 订单表 qy_order_list
-- ----------------------------
DROP TABLE IF EXISTS `qy_order_list`;
CREATE TABLE `qy_order_list` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '0|系统|主键|自增主键|0',
    `base_order_no` VARCHAR(32) NOT NULL DEFAULT '' COMMENT '1|核心|订单号|唯一订单编号|1',
    `base_order_type` TINYINT UNSIGNED NOT NULL DEFAULT 1 COMMENT '2|核心|订单类型|1=上门服务, 2=到店服务, 3=借调服务|1',
    `base_user_id` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '3|关联|用户ID|下单用户qy_user_list.id|1',
    `base_technician_id` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '4|关联|技师ID|服务技师qy_technician_list.id|1',
    `base_shop_id` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '5|关联|商家ID|服务店铺qy_shop_list.id|0',
    `base_source_shop_id` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '6|关联|来源店铺ID|技师所属店铺(借调时)|0',
    `base_service_id` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '7|关联|服务ID|服务项目qy_service_item_list.id|1',
    `base_service_name` VARCHAR(100) NOT NULL DEFAULT '' COMMENT '6|服务|服务名称|下单时服务名称快照|1',
    `base_address_contact` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '7|地址|联系人|服务地址联系人|1',
    `base_address_phone` VARCHAR(20) NOT NULL DEFAULT '' COMMENT '8|地址|联系电话|服务地址联系电话|1',
    `base_address_detail` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '9|地址|详细地址|服务地址详细信息|1',
    `base_address_lat` DECIMAL(10,6) NOT NULL DEFAULT 0.000000 COMMENT '10|地址|纬度|服务地址纬度|0',
    `base_address_lng` DECIMAL(10,6) NOT NULL DEFAULT 0.000000 COMMENT '11|地址|经度|服务地址经度|0',
    `base_time_scheduled` DATETIME DEFAULT NULL COMMENT '12|时间|预约时间|用户预约的服务时间|1',
    `base_time_started` DATETIME DEFAULT NULL COMMENT '13|时间|开始时间|实际服务开始时间|0',
    `base_time_completed` DATETIME DEFAULT NULL COMMENT '14|时间|完成时间|实际服务完成时间|0',
    `base_duration_minutes` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '15|时长|服务时长|实际服务时长分钟数|1',
    `base_price_service` DECIMAL(10,2) NOT NULL DEFAULT 0.00 COMMENT '16|价格|服务费|服务项目费用|1',
    `base_price_tip` DECIMAL(10,2) NOT NULL DEFAULT 0.00 COMMENT '17|价格|小费|用户打赏小费|1',
    `base_price_total` DECIMAL(10,2) NOT NULL DEFAULT 0.00 COMMENT '18|价格|总金额|订单总金额|1',
    `base_pay_method` VARCHAR(20) NOT NULL DEFAULT '' COMMENT '19|支付|支付方式|wechat=微信, alipay=支付宝|1',
    `base_pay_time` DATETIME DEFAULT NULL COMMENT '20|支付|支付时间|实际支付完成时间|0',
    `base_pay_transaction_id` VARCHAR(64) NOT NULL DEFAULT '' COMMENT '21|支付|支付流水号|第三方支付流水号|0',
    `base_status_order` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '22|状态|订单状态|0=待支付, 1=已支付, 2=已接单, 3=出发中, 4=服务中, 5=已完成, 6=已取消, 7=已退款|1',
    `base_status_pay` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '23|状态|支付状态|0=未支付, 1=已支付, 2=已退款|1',
    `base_cancel_reason` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '24|取消|取消原因|订单取消原因说明|0',
    `base_cancel_time` DATETIME DEFAULT NULL COMMENT '25|取消|取消时间|订单取消时间|0',
    `base_remark` VARCHAR(500) NOT NULL DEFAULT '' COMMENT '26|备注|用户备注|用户下单备注信息|1',
    `base_time_created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '27|时间|创建时间|订单创建时间|0',
    `base_time_updated` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '28|时间|更新时间|最后更新时间|0',
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_order_no` (`base_order_no`),
    KEY `idx_user` (`base_user_id`),
    KEY `idx_technician` (`base_technician_id`),
    KEY `idx_shop` (`base_shop_id`),
    KEY `idx_status_order` (`base_status_order`),
    KEY `idx_status_pay` (`base_status_pay`),
    KEY `idx_time_scheduled` (`base_time_scheduled`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='订单主表|存储用户下单信息、服务地址和支付状态';

-- ----------------------------
-- 7. 评价表 qy_review_list
-- ----------------------------
DROP TABLE IF EXISTS `qy_review_list`;
CREATE TABLE `qy_review_list` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '0|系统|主键|自增主键|0',
    `base_order_id` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '1|关联|订单ID|关联订单qy_order_list.id|1',
    `base_user_id` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '2|关联|用户ID|评价用户qy_user_list.id|1',
    `base_technician_id` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '3|关联|技师ID|被评技师qy_technician_list.id|1',
    `base_shop_id` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '4|关联|店铺ID|被评店铺qy_shop_list.id|0',
    `base_reviewer_type` TINYINT UNSIGNED NOT NULL DEFAULT 1 COMMENT '5|评价|评价者类型|1=顾客, 2=店铺主|1',
    `base_rating_overall` TINYINT UNSIGNED NOT NULL DEFAULT 5 COMMENT '6|评分|综合评分|总体评分1-5星|1',
    `base_rating_skill` TINYINT UNSIGNED NOT NULL DEFAULT 5 COMMENT '7|评分|手法专业度|技术评分1-5星|1',
    `base_rating_attitude` TINYINT UNSIGNED NOT NULL DEFAULT 5 COMMENT '8|评分|服务态度|态度评分1-5星|1',
    `base_rating_punctual` TINYINT UNSIGNED NOT NULL DEFAULT 5 COMMENT '9|评分|准时守约|守时评分1-5星|1',
    `base_rating_communication` TINYINT UNSIGNED NOT NULL DEFAULT 5 COMMENT '10|评分|沟通能力|沟通评分1-5星|1',
    `base_rating_hygiene` TINYINT UNSIGNED NOT NULL DEFAULT 5 COMMENT '11|评分|卫生习惯|卫生评分1-5星|1',
    `base_content` TEXT COMMENT '12|评价|评价内容|用户评价文字内容|1',
    `base_photos` JSON COMMENT '13|评价|评价图片|评价图片URL数组|0',
    `base_tags` JSON COMMENT '14|评价|评价标签|快捷评价标签数组|0',
    `base_reply` TEXT COMMENT '15|回复|技师回复|技师回复内容|1',
    `base_reply_time` DATETIME DEFAULT NULL COMMENT '16|回复|回复时间|技师回复时间|0',
    `base_is_anonymous` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '17|设置|匿名评价|0=实名, 1=匿名|0',
    `base_status_visible` TINYINT UNSIGNED NOT NULL DEFAULT 1 COMMENT '18|状态|显示状态|0=隐藏, 1=显示|0',
    `base_time_created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '19|时间|创建时间|评价创建时间|0',
    `base_time_updated` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '20|时间|更新时间|最后更新时间|0',
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_order` (`base_order_id`),
    KEY `idx_user` (`base_user_id`),
    KEY `idx_technician` (`base_technician_id`),
    KEY `idx_shop` (`base_shop_id`),
    KEY `idx_rating` (`base_rating_overall`),
    KEY `idx_visible` (`base_status_visible`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='评价主表|存储多维度评价，支持顾客和店铺主评价';


-- ----------------------------
-- 8. 技师排班表 qy_technician_schedule_list
-- ----------------------------
DROP TABLE IF EXISTS `qy_technician_schedule_list`;
CREATE TABLE `qy_technician_schedule_list` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '0|系统|主键|自增主键|0',
    `base_technician_id` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '1|关联|技师ID|关联技师qy_technician_list.id|1',
    `base_date` DATE NOT NULL COMMENT '2|排班|日期|排班日期|1',
    `base_time_start` TIME NOT NULL COMMENT '3|排班|开始时间|当日可服务开始时间|1',
    `base_time_end` TIME NOT NULL COMMENT '4|排班|结束时间|当日可服务结束时间|1',
    `base_status` TINYINT UNSIGNED NOT NULL DEFAULT 1 COMMENT '5|状态|排班状态|0=不可用, 1=可用|1',
    `base_time_created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '6|时间|创建时间|记录创建时间|0',
    `base_time_updated` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '7|时间|更新时间|最后更新时间|0',
    PRIMARY KEY (`id`),
    KEY `idx_technician` (`base_technician_id`),
    KEY `idx_date` (`base_date`),
    KEY `idx_technician_date` (`base_technician_id`, `base_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='技师排班表|存储技师每日可服务时间段';

-- ----------------------------
-- 9. 钱包表 qy_wallet_list
-- ----------------------------
DROP TABLE IF EXISTS `qy_wallet_list`;
CREATE TABLE `qy_wallet_list` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '0|系统|主键|自增主键|0',
    `base_owner_type` VARCHAR(20) NOT NULL DEFAULT '' COMMENT '1|归属|所有者类型|technician=技师, shop=商家|1',
    `base_owner_id` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '2|归属|所有者ID|技师或商家的ID|1',
    `base_balance_available` DECIMAL(12,2) NOT NULL DEFAULT 0.00 COMMENT '3|余额|可用余额|可提现金额|1',
    `base_balance_pending` DECIMAL(12,2) NOT NULL DEFAULT 0.00 COMMENT '4|余额|待结算余额|未到账金额|1',
    `base_balance_total` DECIMAL(12,2) NOT NULL DEFAULT 0.00 COMMENT '5|余额|累计收入|历史总收入|1',
    `base_time_created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '6|时间|创建时间|钱包创建时间|0',
    `base_time_updated` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '7|时间|更新时间|最后更新时间|0',
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_owner` (`base_owner_type`, `base_owner_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='钱包主表|存储技师和商家的钱包余额信息';

-- ----------------------------
-- 10. 钱包流水表 qy_wallet_transaction_list
-- ----------------------------
DROP TABLE IF EXISTS `qy_wallet_transaction_list`;
CREATE TABLE `qy_wallet_transaction_list` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '0|系统|主键|自增主键|0',
    `base_wallet_id` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '1|关联|钱包ID|关联钱包qy_wallet_list.id|1',
    `base_type` VARCHAR(20) NOT NULL DEFAULT '' COMMENT '2|类型|交易类型|income=收入, withdraw=提现, refund=退款|1',
    `base_amount` DECIMAL(12,2) NOT NULL DEFAULT 0.00 COMMENT '3|金额|交易金额|本次交易金额|1',
    `base_balance_after` DECIMAL(12,2) NOT NULL DEFAULT 0.00 COMMENT '4|金额|交易后余额|交易后可用余额|1',
    `base_order_id` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '5|关联|订单ID|关联订单qy_order_list.id|0',
    `base_remark` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '6|备注|交易备注|交易说明信息|1',
    `base_status` TINYINT UNSIGNED NOT NULL DEFAULT 1 COMMENT '7|状态|交易状态|0=处理中, 1=成功, 2=失败|1',
    `base_time_created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '8|时间|创建时间|交易创建时间|0',
    `base_time_updated` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '9|时间|更新时间|最后更新时间|0',
    PRIMARY KEY (`id`),
    KEY `idx_wallet` (`base_wallet_id`),
    KEY `idx_type` (`base_type`),
    KEY `idx_order` (`base_order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='钱包流水表|记录钱包收支明细和提现记录';

-- ----------------------------
-- 11. 用户地址表 qy_user_address_list
-- ----------------------------
DROP TABLE IF EXISTS `qy_user_address_list`;
CREATE TABLE `qy_user_address_list` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '0|系统|主键|自增主键|0',
    `base_user_id` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '1|关联|用户ID|关联用户qy_user_list.id|1',
    `base_contact_name` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '2|联系|联系人|收货联系人姓名|1',
    `base_contact_phone` VARCHAR(20) NOT NULL DEFAULT '' COMMENT '3|联系|联系电话|收货联系电话|1',
    `base_address_province` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '4|地址|省份|省级行政区|1',
    `base_address_city` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '5|地址|城市|市级行政区|1',
    `base_address_district` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '6|地址|区县|区县级行政区|1',
    `base_address_detail` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '7|地址|详细地址|街道门牌号等|1',
    `base_address_lat` DECIMAL(10,6) NOT NULL DEFAULT 0.000000 COMMENT '8|地址|纬度|地址纬度坐标|0',
    `base_address_lng` DECIMAL(10,6) NOT NULL DEFAULT 0.000000 COMMENT '9|地址|经度|地址经度坐标|0',
    `base_is_default` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '10|标记|默认地址|0=否, 1=是|1',
    `base_time_created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '11|时间|创建时间|地址创建时间|0',
    `base_time_updated` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '12|时间|更新时间|最后更新时间|0',
    PRIMARY KEY (`id`),
    KEY `idx_user` (`base_user_id`),
    KEY `idx_default` (`base_user_id`, `base_is_default`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='用户地址表|存储用户常用服务地址信息';

-- ----------------------------
-- 12. 技师服务关联表 qy_technician_service_list
-- ----------------------------
DROP TABLE IF EXISTS `qy_technician_service_list`;
CREATE TABLE `qy_technician_service_list` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '0|系统|主键|自增主键|0',
    `base_technician_id` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '1|关联|技师ID|关联技师qy_technician_list.id|1',
    `base_service_id` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '2|关联|服务ID|关联服务qy_service_item_list.id|1',
    `base_price` DECIMAL(10,2) NOT NULL DEFAULT 0.00 COMMENT '3|价格|技师定价|技师自定义服务价格|1',
    `base_status_active` TINYINT UNSIGNED NOT NULL DEFAULT 1 COMMENT '4|状态|启用状态|0=禁用, 1=启用|1',
    `base_time_created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '5|时间|创建时间|关联创建时间|0',
    `base_time_updated` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '6|时间|更新时间|最后更新时间|0',
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_technician_service` (`base_technician_id`, `base_service_id`),
    KEY `idx_service` (`base_service_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='技师服务关联表|存储技师可提供的服务项目和定价';

-- ----------------------------
-- 13. 管理员表 qy_admin_list
-- ----------------------------
DROP TABLE IF EXISTS `qy_admin_list`;
CREATE TABLE `qy_admin_list` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '0|系统|主键|自增主键|0',
    `base_auth_username` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '1|认证|用户名|管理员登录用户名|1',
    `base_auth_password` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '2|认证|密码|加密后的登录密码|0',
    `base_profile_name` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '3|资料|姓名|管理员真实姓名|1',
    `base_profile_avatar` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '4|资料|头像|管理员头像URL|1',
    `base_role` VARCHAR(20) NOT NULL DEFAULT 'admin' COMMENT '5|权限|角色|super=超级管理员, admin=普通管理员|1',
    `base_status_active` TINYINT UNSIGNED NOT NULL DEFAULT 1 COMMENT '6|状态|账号状态|0=禁用, 1=正常|1',
    `base_time_last_login` DATETIME DEFAULT NULL COMMENT '7|时间|最后登录|最后登录时间|0',
    `base_time_created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '8|时间|创建时间|账号创建时间|0',
    `base_time_updated` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '9|时间|更新时间|最后更新时间|0',
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_username` (`base_auth_username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='管理员主表|存储后台管理员账号信息';

-- ----------------------------
-- 14. 系统配置表 qy_config_list
-- ----------------------------
DROP TABLE IF EXISTS `qy_config_list`;
CREATE TABLE `qy_config_list` (
    `base_key` VARCHAR(100) NOT NULL COMMENT '1|核心|配置键|配置项唯一标识|1',
    `base_value` TEXT COMMENT '2|核心|配置值|配置项的值|1',
    `base_desc` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '3|信息|描述|配置项用途说明|1',
    `base_time_created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '4|时间|创建时间|配置创建时间|0',
    `base_time_updated` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '5|时间|更新时间|最后更新时间|0',
    PRIMARY KEY (`base_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='系统配置表|存储全局系统配置键值对';

-- ----------------------------
-- 15. 借调记录表 qy_borrow_list
-- ----------------------------
DROP TABLE IF EXISTS `qy_borrow_list`;
CREATE TABLE `qy_borrow_list` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '0|系统|主键|自增主键|0',
    `base_technician_id` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '1|关联|技师ID|被借调技师qy_technician_list.id|1',
    `base_from_shop_id` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '2|关联|来源店铺|技师所属店铺qy_shop_list.id|1',
    `base_to_shop_id` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '3|关联|目标店铺|借入店铺qy_shop_list.id|1',
    `base_order_id` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '4|关联|订单ID|关联订单qy_order_list.id|0',
    `base_fee_total` DECIMAL(10,2) NOT NULL DEFAULT 0.00 COMMENT '5|费用|总费用|借调总费用|1',
    `base_fee_technician` DECIMAL(10,2) NOT NULL DEFAULT 0.00 COMMENT '6|费用|技师费用|技师应得部分|1',
    `base_fee_from_shop` DECIMAL(10,2) NOT NULL DEFAULT 0.00 COMMENT '7|费用|来源店铺费用|来源店铺应得部分|1',
    `base_fee_platform` DECIMAL(10,2) NOT NULL DEFAULT 0.00 COMMENT '8|费用|平台费用|平台抽成部分|1',
    `base_status` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '9|状态|借调状态|0=待确认, 1=已确认, 2=进行中, 3=已完成, 4=已取消|1',
    `base_time_start` DATETIME DEFAULT NULL COMMENT '10|时间|开始时间|借调开始时间|1',
    `base_time_end` DATETIME DEFAULT NULL COMMENT '11|时间|结束时间|借调结束时间|1',
    `base_remark` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '12|备注|备注|借调备注说明|0',
    `base_time_created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '13|时间|创建时间|记录创建时间|0',
    `base_time_updated` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '14|时间|更新时间|最后更新时间|0',
    PRIMARY KEY (`id`),
    KEY `idx_technician` (`base_technician_id`),
    KEY `idx_from_shop` (`base_from_shop_id`),
    KEY `idx_to_shop` (`base_to_shop_id`),
    KEY `idx_order` (`base_order_id`),
    KEY `idx_status` (`base_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='借调记录表|记录技师在店铺间的借调信息';

-- ----------------------------
-- 16. 招聘信息表 qy_job_list
-- ----------------------------
DROP TABLE IF EXISTS `qy_job_list`;
CREATE TABLE `qy_job_list` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '0|系统|主键|自增主键|0',
    `base_shop_id` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '1|关联|店铺ID|发布店铺qy_shop_list.id|1',
    `base_title` VARCHAR(100) NOT NULL DEFAULT '' COMMENT '2|信息|职位标题|招聘标题|1',
    `base_type` TINYINT UNSIGNED NOT NULL DEFAULT 1 COMMENT '3|信息|招聘类型|1=长期聘用, 2=短期借调, 3=兼职|1',
    `base_description` TEXT COMMENT '4|信息|职位描述|详细要求说明|1',
    `base_salary_min` DECIMAL(10,2) NOT NULL DEFAULT 0.00 COMMENT '5|薪资|最低薪资|薪资范围下限|1',
    `base_salary_max` DECIMAL(10,2) NOT NULL DEFAULT 0.00 COMMENT '6|薪资|最高薪资|薪资范围上限|1',
    `base_salary_type` TINYINT UNSIGNED NOT NULL DEFAULT 1 COMMENT '7|薪资|薪资类型|1=月薪, 2=日薪, 3=时薪, 4=单次|1',
    `base_require_gender` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '8|要求|性别要求|0=不限, 1=男, 2=女|1',
    `base_require_experience` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '9|要求|经验要求|最低从业年限|1',
    `base_require_certified` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '10|要求|认证要求|0=不限, 1=需认证|1',
    `base_count` INT UNSIGNED NOT NULL DEFAULT 1 COMMENT '11|数量|招聘人数|需要招聘的人数|1',
    `base_status` TINYINT UNSIGNED NOT NULL DEFAULT 1 COMMENT '12|状态|招聘状态|0=已关闭, 1=招聘中|1',
    `base_time_expire` DATETIME DEFAULT NULL COMMENT '13|时间|过期时间|招聘截止时间|1',
    `base_time_created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '14|时间|创建时间|发布时间|0',
    `base_time_updated` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '15|时间|更新时间|最后更新时间|0',
    PRIMARY KEY (`id`),
    KEY `idx_shop` (`base_shop_id`),
    KEY `idx_type` (`base_type`),
    KEY `idx_status` (`base_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='招聘信息表|店铺发布的招聘需求';

-- ----------------------------
-- 17. 招聘申请表 qy_job_apply_list
-- ----------------------------
DROP TABLE IF EXISTS `qy_job_apply_list`;
CREATE TABLE `qy_job_apply_list` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '0|系统|主键|自增主键|0',
    `base_job_id` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '1|关联|招聘ID|关联招聘qy_job_list.id|1',
    `base_technician_id` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '2|关联|技师ID|申请技师qy_technician_list.id|1',
    `base_message` TEXT COMMENT '3|信息|申请留言|技师申请留言|1',
    `base_status` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '4|状态|申请状态|0=待处理, 1=已通过, 2=已拒绝, 3=已撤回|1',
    `base_reply` TEXT COMMENT '5|回复|店铺回复|店铺回复内容|0',
    `base_time_created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '6|时间|创建时间|申请时间|0',
    `base_time_updated` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '7|时间|更新时间|最后更新时间|0',
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_job_technician` (`base_job_id`, `base_technician_id`),
    KEY `idx_technician` (`base_technician_id`),
    KEY `idx_status` (`base_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='招聘申请表|技师对招聘的申请记录';

-- ----------------------------
-- 18. 用户收藏表 qy_user_favorite_list
-- ----------------------------
DROP TABLE IF EXISTS `qy_user_favorite_list`;
CREATE TABLE `qy_user_favorite_list` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '0|系统|主键|自增主键|0',
    `base_user_id` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '1|关联|用户ID|收藏用户qy_user_list.id|1',
    `base_target_type` VARCHAR(20) NOT NULL DEFAULT '' COMMENT '2|目标|收藏类型|technician=技师, shop=店铺|1',
    `base_target_id` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '3|目标|目标ID|技师或店铺的ID|1',
    `base_time_created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '4|时间|创建时间|收藏时间|0',
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_user_target` (`base_user_id`, `base_target_type`, `base_target_id`),
    KEY `idx_target` (`base_target_type`, `base_target_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='用户收藏表|用户收藏的技师和店铺';

-- ----------------------------
-- 19. 优惠券表 qy_coupon_list
-- ----------------------------
DROP TABLE IF EXISTS `qy_coupon_list`;
CREATE TABLE `qy_coupon_list` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '0|系统|主键|自增主键|0',
    `base_name` VARCHAR(100) NOT NULL DEFAULT '' COMMENT '1|信息|优惠券名称|优惠券显示名称|1',
    `base_type` TINYINT UNSIGNED NOT NULL DEFAULT 1 COMMENT '2|信息|优惠类型|1=满减, 2=折扣, 3=立减|1',
    `base_value` DECIMAL(10,2) NOT NULL DEFAULT 0.00 COMMENT '3|信息|优惠值|满减金额或折扣比例|1',
    `base_min_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00 COMMENT '4|条件|最低消费|使用门槛金额|1',
    `base_total_count` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '5|数量|发放总量|优惠券总数量|1',
    `base_used_count` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '6|数量|已使用数|已使用数量|1',
    `base_time_start` DATETIME DEFAULT NULL COMMENT '7|时间|开始时间|有效期开始|1',
    `base_time_end` DATETIME DEFAULT NULL COMMENT '8|时间|结束时间|有效期结束|1',
    `base_status` TINYINT UNSIGNED NOT NULL DEFAULT 1 COMMENT '9|状态|状态|0=禁用, 1=启用|1',
    `base_time_created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '10|时间|创建时间|创建时间|0',
    `base_time_updated` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '11|时间|更新时间|最后更新时间|0',
    PRIMARY KEY (`id`),
    KEY `idx_status` (`base_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='优惠券表|平台优惠券配置';

-- ----------------------------
-- 20. 用户优惠券表 qy_user_coupon_list
-- ----------------------------
DROP TABLE IF EXISTS `qy_user_coupon_list`;
CREATE TABLE `qy_user_coupon_list` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '0|系统|主键|自增主键|0',
    `base_user_id` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '1|关联|用户ID|持有用户qy_user_list.id|1',
    `base_coupon_id` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '2|关联|优惠券ID|关联qy_coupon_list.id|1',
    `base_order_id` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '3|关联|订单ID|使用订单qy_order_list.id|0',
    `base_status` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '4|状态|状态|0=未使用, 1=已使用, 2=已过期|1',
    `base_time_used` DATETIME DEFAULT NULL COMMENT '5|时间|使用时间|优惠券使用时间|0',
    `base_time_expire` DATETIME DEFAULT NULL COMMENT '6|时间|过期时间|优惠券过期时间|1',
    `base_time_created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '7|时间|创建时间|领取时间|0',
    PRIMARY KEY (`id`),
    KEY `idx_user` (`base_user_id`),
    KEY `idx_coupon` (`base_coupon_id`),
    KEY `idx_status` (`base_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='用户优惠券表|用户持有的优惠券';

-- ----------------------------
-- 21. 消息通知表 qy_message_list
-- ----------------------------
DROP TABLE IF EXISTS `qy_message_list`;
CREATE TABLE `qy_message_list` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '0|系统|主键|自增主键|0',
    `base_receiver_type` VARCHAR(20) NOT NULL DEFAULT '' COMMENT '1|接收|接收者类型|user=用户, technician=技师, shop=商户|1',
    `base_receiver_id` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '2|接收|接收者ID|接收者的ID|1',
    `base_type` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '3|消息|消息类型|order=订单, review=评价, system=系统|1',
    `base_title` VARCHAR(100) NOT NULL DEFAULT '' COMMENT '4|消息|消息标题|通知标题|1',
    `base_content` TEXT COMMENT '5|消息|消息内容|通知详细内容|1',
    `base_extra` JSON COMMENT '6|消息|附加数据|关联数据JSON|0',
    `base_is_read` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '7|状态|已读状态|0=未读, 1=已读|1',
    `base_time_read` DATETIME DEFAULT NULL COMMENT '8|时间|阅读时间|消息阅读时间|0',
    `base_time_created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '9|时间|创建时间|消息创建时间|0',
    PRIMARY KEY (`id`),
    KEY `idx_receiver` (`base_receiver_type`, `base_receiver_id`),
    KEY `idx_type` (`base_type`),
    KEY `idx_read` (`base_is_read`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='消息通知表|系统消息和通知';

SET FOREIGN_KEY_CHECKS = 1;
