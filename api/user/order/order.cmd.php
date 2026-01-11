<?php
/**
 * 轻养到家 - 用户端订单 API
 * ZERO 框架规范 - 冒号语法
 * 
 * POST /api/user/order              创建订单
 * GET  /api/user/order              订单列表
 * GET  /api/user/order:123          订单详情
 * POST /api/user/order:123:cancel   取消订单
 * POST /api/user/order:123:pay      支付订单
 */

require_once dirname(dirname(dirname(__FILE__))) . '/bootstrap.inc.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';
$id = $_GET['id'] ?? null;

// 需要登录
$auth = auth_require(TokenType::USER);

if ($id) {
    switch ($action) {
        case 'cancel':
            if ($method !== 'POST') Response::badRequest('请求方法不允许');
            cancelOrder($auth, $id);
            break;
        case 'pay':
            if ($method !== 'POST') Response::badRequest('请求方法不允许');
            payOrder($auth, $id);
            break;
        default:
            if ($method !== 'GET') Response::badRequest('请求方法不允许');
            getOrderDetail($auth, $id);
    }
} else {
    if ($method === 'POST') {
        createOrder($auth);
    } elseif ($method === 'GET') {
        getOrderList($auth);
    } else {
        Response::badRequest('请求方法不允许');
    }
}

/**
 * 创建订单
 */
function createOrder($auth) {
    validate_required(['technician_id', 'service_id', 'scheduled_time', 'address_id']);
    
    $technicianId = input('technician_id');
    $serviceId = input('service_id');
    $scheduledTime = input('scheduled_time');
    $addressId = input('address_id');
    $remark = input('remark', '');
    
    // 验证技师
    $tech = db()->fetch(
        'SELECT t.*, s.id as shop_id FROM qy_technician_list t
         LEFT JOIN qy_shop_list s ON t.base_shop_id = s.id
         WHERE t.id = ? AND t.base_status_verify = 1 AND t.base_status_active = 1',
        [$technicianId]
    );
    
    if (!$tech) {
        Response::badRequest('技师不存在或不可用');
    }
    
    // 验证服务
    $service = db()->fetch(
        'SELECT * FROM qy_service_item_list WHERE id = ? AND base_status_active = 1',
        [$serviceId]
    );
    
    if (!$service) {
        Response::badRequest('服务不存在');
    }
    
    // 获取技师服务价格
    $techService = db()->fetch(
        'SELECT base_price as price FROM qy_technician_service_list 
         WHERE base_technician_id = ? AND base_service_id = ? AND base_status_active = 1',
        [$technicianId, $serviceId]
    );
    
    $price = $techService ? $techService['price'] : $service['base_price_base'];
    
    // 验证地址
    $address = db()->fetch(
        'SELECT * FROM qy_user_address_list WHERE id = ? AND base_user_id = ?',
        [$addressId, $auth['id']]
    );
    
    if (!$address) {
        Response::badRequest('地址不存在');
    }
    
    // 验证预约时间
    $scheduledDateTime = strtotime($scheduledTime);
    if ($scheduledDateTime < time() + 3600) { // 至少提前1小时
        Response::badRequest('预约时间至少需要提前1小时');
    }
    
    // TODO: 验证时段可用性
    
    // 创建订单
    $orderNo = 'QY' . generate_order_no();
    
    $orderId = db()->insert('qy_order_list', [
        'base_order_no' => $orderNo,
        'base_user_id' => $auth['id'],
        'base_technician_id' => $technicianId,
        'base_shop_id' => $tech['shop_id'],
        'base_service_id' => $serviceId,
        'base_service_name' => $service['base_profile_name'],
        'base_address_contact' => $address['base_contact_name'],
        'base_address_phone' => $address['base_contact_phone'],
        'base_address_detail' => $address['base_address_detail'],
        'base_address_lat' => $address['base_address_lat'],
        'base_address_lng' => $address['base_address_lng'],
        'base_time_scheduled' => $scheduledTime,
        'base_duration_minutes' => $service['base_duration_minutes'],
        'base_price_service' => $price,
        'base_price_tip' => 0,
        'base_price_total' => $price,
        'base_status_order' => 0, // 待支付
        'base_status_pay' => 0,
        'base_remark' => $remark,
        'base_time_created' => date('Y-m-d H:i:s')
    ]);
    
    Response::success([
        'order_id' => $orderId,
        'order_no' => $orderNo,
        'total_amount' => $price
    ], '订单创建成功');
}

/**
 * 获取订单列表
 */
function getOrderList($auth) {
    $status = input('status'); // 0待支付 1待接单 2待服务 3出发中 4服务中 5已完成 6已取消 7已退款
    $page = max(1, (int)input('page', 1));
    $pageSize = min(50, max(1, (int)input('page_size', 20)));
    
    $where = ['base_user_id = :user_id'];
    $params = ['user_id' => $auth['id']];
    
    if ($status !== null && $status !== '') {
        $where[] = 'base_status_order = :status';
        $params['status'] = $status;
    }
    
    $whereClause = implode(' AND ', $where);
    
    // 获取总数
    $total = db()->fetch(
        "SELECT COUNT(*) as count FROM qy_order_list WHERE $whereClause",
        $params
    )['count'];
    
    // 获取列表
    $offset = ($page - 1) * $pageSize;
    $orders = db()->fetchAll(
        "SELECT o.id, o.base_order_no as order_no, o.base_service_name as service_name,
                o.base_time_scheduled as scheduled_time, o.base_price_total as total_amount,
                o.base_status_order as status, o.base_time_created as created_at,
                t.base_profile_realname as technician_name, t.base_profile_avatar as technician_avatar
         FROM qy_order_list o
         LEFT JOIN qy_technician_list t ON o.base_technician_id = t.id
         WHERE $whereClause
         ORDER BY o.base_time_created DESC
         LIMIT $offset, $pageSize",
        $params
    );
    
    Response::paginate($orders, $total, $page, $pageSize);
}

/**
 * 获取订单详情
 */
function getOrderDetail($auth, $id) {
    $order = db()->fetch(
        'SELECT o.*, t.base_profile_realname as technician_name, 
                t.base_profile_avatar as technician_avatar, t.base_profile_gender as technician_gender,
                t.base_stat_rating_avg as technician_rating, t.base_location_lat as tech_lat,
                t.base_location_lng as tech_lng, s.base_profile_name as shop_name
         FROM qy_order_list o
         LEFT JOIN qy_technician_list t ON o.base_technician_id = t.id
         LEFT JOIN qy_shop_list s ON o.base_shop_id = s.id
         WHERE o.id = ? AND o.base_user_id = ?',
        [$id, $auth['id']]
    );
    
    if (!$order) {
        Response::notFound('订单不存在');
    }
    
    // 检查是否已评价
    $review = db()->fetch(
        'SELECT id FROM qy_review_list WHERE base_order_id = ?',
        [$id]
    );
    
    Response::success([
        'id' => $order['id'],
        'order_no' => $order['base_order_no'],
        'service_name' => $order['base_service_name'],
        'scheduled_time' => $order['base_time_scheduled'],
        'duration' => $order['base_duration_minutes'],
        'address' => [
            'contact' => $order['base_address_contact'],
            'phone' => $order['base_address_phone'],
            'detail' => $order['base_address_detail'],
            'lat' => $order['base_address_lat'],
            'lng' => $order['base_address_lng']
        ],
        'technician' => [
            'id' => $order['base_technician_id'],
            'name' => $order['technician_name'],
            'avatar' => $order['technician_avatar'],
            'gender' => $order['technician_gender'],
            'rating' => round($order['technician_rating'], 2),
            'lat' => $order['tech_lat'],
            'lng' => $order['tech_lng']
        ],
        'shop_name' => $order['shop_name'],
        'price' => [
            'service' => $order['base_price_service'],
            'tip' => $order['base_price_tip'],
            'total' => $order['base_price_total']
        ],
        'status' => $order['base_status_order'],
        'pay_status' => $order['base_status_pay'],
        'pay_method' => $order['base_pay_method'],
        'pay_time' => $order['base_pay_time'],
        'remark' => $order['base_remark'],
        'cancel_reason' => $order['base_cancel_reason'],
        'cancel_time' => $order['base_cancel_time'],
        'started_at' => $order['base_time_started'],
        'completed_at' => $order['base_time_completed'],
        'created_at' => $order['base_time_created'],
        'has_review' => $review ? true : false
    ]);
}

/**
 * 取消订单
 */
function cancelOrder($auth, $id) {
    $reason = input('reason', '用户取消');
    
    $order = db()->fetch(
        'SELECT * FROM qy_order_list WHERE id = ? AND base_user_id = ?',
        [$id, $auth['id']]
    );
    
    if (!$order) {
        Response::notFound('订单不存在');
    }
    
    // 只有待支付、待接单、待服务状态可以取消
    if (!in_array($order['base_status_order'], [0, 1, 2])) {
        Response::badRequest('当前订单状态不可取消');
    }
    
    db()->update('qy_order_list', [
        'base_status_order' => 6, // 已取消
        'base_cancel_reason' => $reason,
        'base_cancel_time' => date('Y-m-d H:i:s')
    ], 'id = :id', ['id' => $id]);
    
    // TODO: 如果已支付，需要退款处理
    
    Response::success(null, '订单已取消');
}

/**
 * 支付订单（模拟）
 */
function payOrder($auth, $id) {
    $payMethod = input('pay_method', 'wechat'); // wechat, alipay
    
    $order = db()->fetch(
        'SELECT * FROM qy_order_list WHERE id = ? AND base_user_id = ?',
        [$id, $auth['id']]
    );
    
    if (!$order) {
        Response::notFound('订单不存在');
    }
    
    if ($order['base_status_order'] != 0) {
        Response::badRequest('订单状态不正确');
    }
    
    // 模拟支付成功
    $transactionId = 'PAY' . date('YmdHis') . mt_rand(100000, 999999);
    
    db()->update('qy_order_list', [
        'base_status_order' => 1, // 待接单
        'base_status_pay' => 1,
        'base_pay_method' => $payMethod,
        'base_pay_time' => date('Y-m-d H:i:s'),
        'base_pay_transaction_id' => $transactionId
    ], 'id = :id', ['id' => $id]);
    
    // TODO: 通知技师
    
    Response::success([
        'transaction_id' => $transactionId
    ], '支付成功');
}
