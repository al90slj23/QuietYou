<?php
/**
 * ================================================================
 * 文件名: list.api.php
 * 创建时间: 2026-01-13
 * ================================================================
 *
 * 【文件职责】
 * 商户服务项目列表 API
 *
 * ================================================================
 */

require_once dirname(dirname(dirname(__FILE__))) . '/bootstrap.include.php';

// 验证商户登录
$merchant = auth_merchant();

// 获取所有服务分类和项目
$categories = db()->fetchAll(
    "SELECT * FROM qy_service_category_list WHERE base_status_active = 1 ORDER BY base_sort_order"
);

$services = db()->fetchAll(
    "SELECT * FROM qy_service_item_list WHERE base_status_active = 1 ORDER BY base_sort_order"
);

// 按分类组织
$result = [];
foreach ($categories as $category) {
    $categoryServices = array_filter($services, function($s) use ($category) {
        return $s['base_category_id'] == $category['id'];
    });
    
    $result[] = [
        'id' => $category['id'],
        'name' => $category['base_profile_name'],
        'icon' => $category['base_profile_icon'],
        'services' => array_map(function($s) {
            return [
                'id' => $s['id'],
                'name' => $s['base_profile_name'],
                'desc' => $s['base_profile_desc'],
                'icon' => $s['base_profile_icon'],
                'price_base' => (float)$s['base_price_base'],
                'price_max' => (float)$s['base_price_max'],
                'duration' => (int)$s['base_duration_minutes']
            ];
        }, array_values($categoryServices))
    ];
}

Response::success(['categories' => $result]);
