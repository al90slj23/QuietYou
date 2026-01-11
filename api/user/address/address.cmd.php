<?php
/**
 * 轻养到家 - 用户端地址 API
 * ZERO 框架规范 - 冒号语法
 * 
 * GET    /api/user/address           地址列表
 * POST   /api/user/address           添加地址
 * PUT    /api/user/address:123       更新地址
 * DELETE /api/user/address:123       删除地址
 */

require_once dirname(dirname(dirname(__FILE__))) . '/bootstrap.inc.php';

$method = $_SERVER['REQUEST_METHOD'];
$id = $_GET['id'] ?? null;

// 需要登录
$auth = auth_require(TokenType::USER);

if ($id) {
    switch ($method) {
        case 'GET':
            getAddressDetail($auth, $id);
            break;
        case 'PUT':
            updateAddress($auth, $id);
            break;
        case 'DELETE':
            deleteAddress($auth, $id);
            break;
        default:
            Response::badRequest('请求方法不允许');
    }
} else {
    switch ($method) {
        case 'GET':
            getAddressList($auth);
            break;
        case 'POST':
            createAddress($auth);
            break;
        default:
            Response::badRequest('请求方法不允许');
    }
}

/**
 * 获取地址列表
 */
function getAddressList($auth) {
    $addresses = db()->fetchAll(
        'SELECT id, base_contact_name as contact_name, base_contact_phone as contact_phone,
                base_address_province as province, base_address_city as city,
                base_address_district as district, base_address_detail as detail,
                base_address_lat as lat, base_address_lng as lng, base_is_default as is_default
         FROM qy_user_address_list 
         WHERE base_user_id = ?
         ORDER BY base_is_default DESC, base_time_created DESC',
        [$auth['id']]
    );
    
    Response::success($addresses);
}

/**
 * 获取地址详情
 */
function getAddressDetail($auth, $id) {
    $address = db()->fetch(
        'SELECT id, base_contact_name as contact_name, base_contact_phone as contact_phone,
                base_address_province as province, base_address_city as city,
                base_address_district as district, base_address_detail as detail,
                base_address_lat as lat, base_address_lng as lng, base_is_default as is_default
         FROM qy_user_address_list 
         WHERE id = ? AND base_user_id = ?',
        [$id, $auth['id']]
    );
    
    if (!$address) {
        Response::notFound('地址不存在');
    }
    
    Response::success($address);
}

/**
 * 添加地址
 */
function createAddress($auth) {
    validate_required(['contact_name', 'contact_phone', 'detail']);
    
    $contactName = input('contact_name');
    $contactPhone = input('contact_phone');
    $province = input('province', '');
    $city = input('city', '');
    $district = input('district', '');
    $detail = input('detail');
    $lat = input('lat', 0);
    $lng = input('lng', 0);
    $isDefault = input('is_default', 0);
    
    if (!validate_phone($contactPhone)) {
        Response::badRequest('联系电话格式不正确');
    }
    
    // 如果设为默认，先取消其他默认
    if ($isDefault) {
        db()->update('qy_user_address_list', 
            ['base_is_default' => 0], 
            'base_user_id = :user_id', 
            ['user_id' => $auth['id']]
        );
    }
    
    // 如果是第一个地址，自动设为默认
    $count = db()->fetch(
        'SELECT COUNT(*) as count FROM qy_user_address_list WHERE base_user_id = ?',
        [$auth['id']]
    )['count'];
    
    if ($count == 0) {
        $isDefault = 1;
    }
    
    $addressId = db()->insert('qy_user_address_list', [
        'base_user_id' => $auth['id'],
        'base_contact_name' => $contactName,
        'base_contact_phone' => $contactPhone,
        'base_address_province' => $province,
        'base_address_city' => $city,
        'base_address_district' => $district,
        'base_address_detail' => $detail,
        'base_address_lat' => $lat,
        'base_address_lng' => $lng,
        'base_is_default' => $isDefault,
        'base_time_created' => date('Y-m-d H:i:s')
    ]);
    
    Response::success([
        'address_id' => $addressId
    ], '地址添加成功');
}

/**
 * 更新地址
 */
function updateAddress($auth, $id) {
    $address = db()->fetch(
        'SELECT * FROM qy_user_address_list WHERE id = ? AND base_user_id = ?',
        [$id, $auth['id']]
    );
    
    if (!$address) {
        Response::notFound('地址不存在');
    }
    
    $data = [];
    
    if (input('contact_name') !== null) {
        $data['base_contact_name'] = input('contact_name');
    }
    if (input('contact_phone') !== null) {
        $phone = input('contact_phone');
        if (!validate_phone($phone)) {
            Response::badRequest('联系电话格式不正确');
        }
        $data['base_contact_phone'] = $phone;
    }
    if (input('province') !== null) {
        $data['base_address_province'] = input('province');
    }
    if (input('city') !== null) {
        $data['base_address_city'] = input('city');
    }
    if (input('district') !== null) {
        $data['base_address_district'] = input('district');
    }
    if (input('detail') !== null) {
        $data['base_address_detail'] = input('detail');
    }
    if (input('lat') !== null) {
        $data['base_address_lat'] = input('lat');
    }
    if (input('lng') !== null) {
        $data['base_address_lng'] = input('lng');
    }
    if (input('is_default') !== null) {
        $isDefault = (int)input('is_default');
        if ($isDefault) {
            // 取消其他默认
            db()->update('qy_user_address_list', 
                ['base_is_default' => 0], 
                'base_user_id = :user_id', 
                ['user_id' => $auth['id']]
            );
        }
        $data['base_is_default'] = $isDefault;
    }
    
    if (empty($data)) {
        Response::badRequest('没有要更新的数据');
    }
    
    db()->update('qy_user_address_list', $data, 'id = :id', ['id' => $id]);
    
    Response::success(null, '地址更新成功');
}

/**
 * 删除地址
 */
function deleteAddress($auth, $id) {
    $address = db()->fetch(
        'SELECT * FROM qy_user_address_list WHERE id = ? AND base_user_id = ?',
        [$id, $auth['id']]
    );
    
    if (!$address) {
        Response::notFound('地址不存在');
    }
    
    db()->delete('qy_user_address_list', 'id = ?', [$id]);
    
    // 如果删除的是默认地址，将第一个地址设为默认
    if ($address['base_is_default']) {
        $first = db()->fetch(
            'SELECT id FROM qy_user_address_list WHERE base_user_id = ? ORDER BY base_time_created DESC LIMIT 1',
            [$auth['id']]
        );
        if ($first) {
            db()->update('qy_user_address_list', ['base_is_default' => 1], 'id = :id', ['id' => $first['id']]);
        }
    }
    
    Response::success(null, '地址删除成功');
}
