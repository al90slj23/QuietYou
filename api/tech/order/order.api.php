<?php
/**
 * 轻养到家 - 技师端订单 API
 * ZERO 框架规范 - 冒号语法
 * 
 * GET  /api/tech/order              订单列表
 * GET  /api/tech/order:123          订单详情
 * POST /api/tech/order:123:accept   接单
 * POST /api/tech/order:123:reject   拒单
 * POST /api/tech/order:123:depart   出发
 * POST /api/tech/order:123:arrive   到达
 * POST /api/tech/order:123:start    开始服务
 * POST /api/tech/order:123:complete 完成服务
 */

require_once dirname(dirname(dirname(__FILE__))) . '/bootstrap.include.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';
$id = $_GET['id'] ?? null;

// 需要登录
$auth = auth_require(TokenType::TECHNICIAN);

if ($id) {
    switch ($action) {
        case 'accept':
            if ($method !== 'POST') Response::badRequest('请求方法不允许');
            acceptOrder($auth, $id);
            break;
        case 'reject':
            if ($method !== 'POST') Response::badRequest('请求方法不允许');
            rejectOrder($auth, $id);
            break;
        case 'depart':
            if ($method !== 'POST') Response::badRequest('请求方法不允许');
            departOrder($auth, $id);
            break;
        case 'arrive':
            if ($method !== 'POST') Response::badRequest('请求方法不允许');
            arriveOrder($auth, $id);
            break;
        case 'start':
            if ($method !== 'POST') Response::badRequest('请求方法不允许');
            startOrder($auth, $id);
            break;
        case 'complete':
            if ($method !== 'POST') Response::badRequest('请求方法不允许');
            completeOrder($auth, $id);
            break;
        default:
            if ($method !== 'GET') Response::badRequest('请求方法不允许');
            getOrderDetail($auth, $id);
    }
} else {
    if ($method !== 'GET') Response::badRequest('请求方法不允许');
    getOrderList($auth);
}

/**
 * 获取订单列表
 */
function getOrderList($auth) {
    $status = input('status');
    $date = input('date'); // YYYY-MM-DD
    $page = max(1, (int)input('page', 1));
    $pageSize = min(50, max(1, (int)input('page_size', 20)));
    
    $where = ['base_technician_id = :tech_id'];
    $params = ['tech_id' => $auth['id']];
    
    if ($status !== null && $status !== '') {
        $where[] = 'base_status_order = :status';
        $params['status'] = $status;
    }
    
    if ($date) {
        $where[] = 'DATE(base_time_scheduled) = :date';
        $params['date'] = $date;
    }
    
    $whereClause = implode(' AND ', $where);
    
    $total = db()->fetch(
        "SELECT COUNT(*) as count FROM qy_order_list WHERE $whereClause",
        $params
    )['count'];
    
    $offset = ($page - 1) * $pageSize;
    $orders = db()->fetchAll(
        "SELECT o.id, o.base_order_no as order_no, o.base_service_name as service_name,
                o.base_time_scheduled as scheduled_time, o.base_duration_minutes as duration,
                o.base_price_total as total_amount, o.base_status_order as status,
                o.base_address_contact as contact_name, o.base_address_phone as contact_phone,
                o.base_address_detail as address, o.base_time_created as created_at,
                u.base_profile_nickname as user_name
         FROM qy_order_list o
         LEFT JOIN qy_user_list u ON o.base_user_id = u.id
         WHERE $whereClause
         ORDER BY o.base_time_scheduled ASC
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
        'SELECT o.*, u.base_profile_nickname as user_name, u.base_profile_avatar as user_avatar
         FROM qy_order_list o
         LEFT JOIN qy_user_list u ON o.base_user_id = u.id
         WHERE o.id = ? AND o.base_technician_id = ?',
        [$id, $auth['id']]
    );
    
    if (!$order) {
        Response::notFound('订单不存在');
    }
    
    Response::success([
        'id' => $order['id'],
        'order_no' => $order['base_order_no'],
        'service_name' => $order['base_service_name'],
        'scheduled_time' => $order['base_time_scheduled'],
        'duration' => $order['base_duration_minutes'],
        'user' => [
            'name' => $order['user_name'],
            'avatar' => $order['user_avatar']
        ],
        'address' => [
            'contact' => $order['base_address_contact'],
            'phone' => $order['base_address_phone'],
            'detail' => $order['base_address_detail'],
            'lat' => $order['base_address_lat'],
            'lng' => $order['base_address_lng']
        ],
        'price' => [
            'service' => $order['base_price_service'],
            'tip' => $order['base_price_tip'],
            'total' => $order['base_price_total']
        ],
        'status' => $order['base_status_order'],
        'remark' => $order['base_remark'],
        'started_at' => $order['base_time_started'],
        'completed_at' => $order['base_time_completed'],
        'created_at' => $order['base_time_created']
    ]);
}

/**
 * 接单
 */
function acceptOrder($auth, $id) {
    $order = getAndValidateOrder($auth, $id, [1]); // 待接单
    
    db()->update('qy_order_list', [
        'base_status_order' => 2 // 待服务
    ], 'id = :id', ['id' => $id]);
    
    // 更新技师状态为忙碌
    db()->update('qy_technician_list', [
        'base_status_online' => 2 // 忙碌
    ], 'id = :id', ['id' => $auth['id']]);
    
    Response::success(null, '接单成功');
}

/**
 * 拒单
 */
function rejectOrder($auth, $id) {
    $reason = input('reason', '技师拒绝');
    
    $order = getAndValidateOrder($auth, $id, [1]); // 待接单
    
    // 拒单后订单需要重新分配或取消
    db()->update('qy_order_list', [
        'base_technician_id' => 0,
        'base_status_order' => 1, // 保持待接单，等待重新分配
        'base_remark' => $order['base_remark'] . ' [技师拒绝: ' . $reason . ']'
    ], 'id = :id', ['id' => $id]);
    
    Response::success(null, '已拒绝订单');
}

/**
 * 出发
 */
function departOrder($auth, $id) {
    $order = getAndValidateOrder($auth, $id, [2]); // 待服务
    
    db()->update('qy_order_list', [
        'base_status_order' => 3 // 出发中
    ], 'id = :id', ['id' => $id]);
    
    Response::success(null, '已出发');
}

/**
 * 到达
 */
function arriveOrder($auth, $id) {
    $order = getAndValidateOrder($auth, $id, [3]); // 出发中
    
    db()->update('qy_order_list', [
        'base_status_order' => 4, // 服务中
        'base_time_started' => date('Y-m-d H:i:s')
    ], 'id = :id', ['id' => $id]);
    
    Response::success(null, '已到达，开始服务');
}

/**
 * 开始服务（如果到达和开始分开）
 */
function startOrder($auth, $id) {
    $order = getAndValidateOrder($auth, $id, [3, 4]); // 出发中或服务中
    
    db()->update('qy_order_list', [
        'base_status_order' => 4,
        'base_time_started' => date('Y-m-d H:i:s')
    ], 'id = :id', ['id' => $id]);
    
    Response::success(null, '服务已开始');
}

/**
 * 完成服务
 */
function completeOrder($auth, $id) {
    $order = getAndValidateOrder($auth, $id, [4]); // 服务中
    
    db()->update('qy_order_list', [
        'base_status_order' => 5, // 已完成
        'base_time_completed' => date('Y-m-d H:i:s')
    ], 'id = :id', ['id' => $id]);
    
    // 更新技师订单数
    db()->query(
        'UPDATE qy_technician_list SET base_stat_order_count = base_stat_order_count + 1 WHERE id = ?',
        [$auth['id']]
    );
    
    // 更新技师状态为在线
    db()->update('qy_technician_list', [
        'base_status_online' => 1
    ], 'id = :id', ['id' => $auth['id']]);
    
    // 计算收入并添加到待结算
    $income = calculateTechnicianIncome($order);
    addPendingIncome($auth['id'], $order['id'], $income);
    
    Response::success([
        'income' => $income
    ], '服务完成');
}

/**
 * 获取并验证订单
 */
function getAndValidateOrder($auth, $id, $allowedStatus) {
    $order = db()->fetch(
        'SELECT * FROM qy_order_list WHERE id = ? AND base_technician_id = ?',
        [$id, $auth['id']]
    );
    
    if (!$order) {
        Response::notFound('订单不存在');
    }
    
    if (!in_array($order['base_status_order'], $allowedStatus)) {
        Response::badRequest('当前订单状态不允许此操作');
    }
    
    return $order;
}

/**
 * 计算技师收入
 */
function calculateTechnicianIncome($order) {
    $total = $order['base_price_total'];
    $platformRate = 0.15; // 平台抽成 15%
    $shopRate = 0.30; // 商家分成 30%
    $techRate = 0.55; // 技师分成 55%
    
    return round($total * $techRate, 2);
}

/**
 * 添加待结算收入
 */
function addPendingIncome($techId, $orderId, $amount) {
    // 更新钱包待结算余额
    db()->query(
        'UPDATE qy_wallet_list SET base_balance_pending = base_balance_pending + ? WHERE base_owner_type = ? AND base_owner_id = ?',
        [$amount, 'technician', $techId]
    );
    
    // 添加交易记录
    $wallet = db()->fetch(
        'SELECT * FROM qy_wallet_list WHERE base_owner_type = ? AND base_owner_id = ?',
        ['technician', $techId]
    );
    
    db()->insert('qy_wallet_transaction_list', [
        'base_wallet_id' => $wallet['id'],
        'base_type' => 'income',
        'base_amount' => $amount,
        'base_balance_after' => $wallet['base_balance_available'],
        'base_order_id' => $orderId,
        'base_remark' => '订单收入（待结算）',
        'base_time_created' => date('Y-m-d H:i:s')
    ]);
}
