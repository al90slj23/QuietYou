<?php
/**
 * ================================================================
 * 文件名: list.api.php
 * 创建时间: 2026-01-13
 * ================================================================
 *
 * 【文件职责】
 * 技师服务项目列表 API
 *
 * ================================================================
 */

require_once dirname(dirname(dirname(__FILE__))) . '/bootstrap.include.php';

// 验证技师登录
$tech = auth_technician();

// 获取技师已开通的服务
$myServices = db()->fetchAll(
    "SELECT ts.*, si.base_profile_name as service_name, si.base_profile_desc as service_desc,
            si.base_price_base as base_price, si.base_duration_minutes as duration,
            sc.base_profile_name as category_name
     FROM qy_technician_service_list ts
     LEFT JOIN qy_service_item_list si ON ts.base_service_id = si.id
     LEFT JOIN qy_service_category_list sc ON si.base_category_id = sc.id
     WHERE ts.base_technician_id = ?
     ORDER BY ts.base_time_created DESC",
    [$tech['id']]
);

// 获取所有可选服务（用于添加新服务）
$allServices = db()->fetchAll(
    "SELECT si.*, sc.base_profile_name as category_name
     FROM qy_service_item_list si
     LEFT JOIN qy_service_category_list sc ON si.base_category_id = sc.id
     WHERE si.base_status_active = 1
     ORDER BY sc.base_sort_order, si.base_sort_order"
);

// 格式化我的服务
$formattedMyServices = array_map(function($item) {
    return [
        'id' => $item['id'],
        'service_id' => $item['base_service_id'],
        'service_name' => $item['service_name'],
        'service_desc' => $item['service_desc'],
        'category_name' => $item['category_name'],
        'price' => (float)$item['base_price'],
        'base_price' => (float)$item['base_price'],
        'duration' => (int)$item['duration'],
        'is_active' => (bool)$item['base_status_active']
    ];
}, $myServices);

// 格式化所有服务
$formattedAllServices = array_map(function($item) {
    return [
        'id' => $item['id'],
        'name' => $item['base_profile_name'],
        'desc' => $item['base_profile_desc'],
        'category_name' => $item['category_name'],
        'base_price' => (float)$item['base_price_base'],
        'max_price' => (float)$item['base_price_max'],
        'duration' => (int)$item['base_duration_minutes']
    ];
}, $allServices);

Response::success([
    'my_services' => $formattedMyServices,
    'all_services' => $formattedAllServices
]);
