<?php
/**
 * ================================================================
 * 文件名: list.api.php
 * 创建时间: 2026-01-13
 * ================================================================
 *
 * 【文件职责】
 * 用户地址列表 API
 *
 * ================================================================
 */

require_once dirname(dirname(dirname(__FILE__))) . '/bootstrap.include.php';

// 验证用户登录
$user = auth_customer();

// 查询地址列表
$list = db()->fetchAll(
    "SELECT * FROM qy_user_address_list 
     WHERE base_user_id = ?
     ORDER BY base_is_default DESC, base_time_created DESC",
    [$user['id']]
);

$formattedList = array_map(function($item) {
    return [
        'id' => $item['id'],
        'contact_name' => $item['base_contact_name'],
        'contact_phone' => $item['base_contact_phone'],
        'province' => $item['base_address_province'],
        'city' => $item['base_address_city'],
        'district' => $item['base_address_district'],
        'detail' => $item['base_address_detail'],
        'lat' => (float)$item['base_address_lat'],
        'lng' => (float)$item['base_address_lng'],
        'is_default' => (bool)$item['base_is_default']
    ];
}, $list);

Response::success(['list' => $formattedList]);
