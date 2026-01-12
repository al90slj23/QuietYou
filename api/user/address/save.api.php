<?php
/**
 * ================================================================
 * 文件名: save.api.php
 * 创建时间: 2026-01-13
 * ================================================================
 *
 * 【文件职责】
 * 用户保存地址 API
 *
 * ================================================================
 */

require_once dirname(dirname(dirname(__FILE__))) . '/bootstrap.include.php';

// 验证用户登录
$user = auth_customer();

// 验证必填参数
validate_required(['contact_name', 'contact_phone', 'address_detail']);

$id = input('id');
$contactName = trim(input('contact_name'));
$contactPhone = input('contact_phone');
$province = input('province', '');
$city = input('city', '');
$district = input('district', '');
$addressDetail = trim(input('address_detail'));
$lat = (float)input('lat', 0);
$lng = (float)input('lng', 0);
$isDefault = (int)input('is_default', 0);

if (!validate_phone($contactPhone)) {
    Response::badRequest('联系电话格式不正确');
}

if (mb_strlen($contactName) < 2 || mb_strlen($contactName) > 20) {
    Response::badRequest('联系人姓名长度应在2-20字之间');
}

// 如果设为默认，先取消其他默认
if ($isDefault) {
    db()->execute(
        "UPDATE qy_user_address_list SET base_is_default = 0 WHERE base_user_id = ?",
        [$user['id']]
    );
}

if ($id) {
    // 更新
    $existing = db()->fetch(
        "SELECT * FROM qy_user_address_list WHERE id = ? AND base_user_id = ?",
        [$id, $user['id']]
    );
    
    if (!$existing) {
        Response::notFound('地址不存在');
    }
    
    db()->execute(
        "UPDATE qy_user_address_list SET 
         base_contact_name = ?, base_contact_phone = ?,
         base_address_province = ?, base_address_city = ?, base_address_district = ?,
         base_address_detail = ?, base_address_lat = ?, base_address_lng = ?,
         base_is_default = ?
         WHERE id = ?",
        [$contactName, $contactPhone, $province, $city, $district, 
         $addressDetail, $lat, $lng, $isDefault, $id]
    );
} else {
    // 新增
    db()->execute(
        "INSERT INTO qy_user_address_list 
         (base_user_id, base_contact_name, base_contact_phone,
          base_address_province, base_address_city, base_address_district,
          base_address_detail, base_address_lat, base_address_lng, base_is_default)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
        [$user['id'], $contactName, $contactPhone, $province, $city, $district,
         $addressDetail, $lat, $lng, $isDefault]
    );
    $id = db()->lastInsertId();
}

Response::success(['id' => $id], '保存成功');
