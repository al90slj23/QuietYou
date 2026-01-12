<?php
/**
 * 轻养到家 - 技师端排班 API
 * ZERO 框架规范 - 冒号语法
 * 
 * GET    /api/tech/schedule           排班列表
 * POST   /api/tech/schedule           设置排班
 * DELETE /api/tech/schedule:123       删除排班
 * POST   /api/tech/status:online      上线
 * POST   /api/tech/status:offline     下线
 * POST   /api/tech/status:location    更新位置
 */

require_once dirname(dirname(dirname(__FILE__))) . '/bootstrap.include.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';
$id = $_GET['id'] ?? null;

// 需要登录
$auth = auth_require(TokenType::TECHNICIAN);

// 状态相关接口
if (strpos($action, 'status:') === 0) {
    $statusAction = substr($action, 7);
    switch ($statusAction) {
        case 'online':
            if ($method !== 'POST') Response::badRequest('请求方法不允许');
            goOnline($auth);
            break;
        case 'offline':
            if ($method !== 'POST') Response::badRequest('请求方法不允许');
            goOffline($auth);
            break;
        case 'location':
            if ($method !== 'POST') Response::badRequest('请求方法不允许');
            updateLocation($auth);
            break;
        default:
            Response::notFound('接口不存在');
    }
    exit;
}

// 排班相关接口
if ($id) {
    if ($method === 'DELETE') {
        deleteSchedule($auth, $id);
    } else {
        Response::badRequest('请求方法不允许');
    }
} else {
    if ($method === 'GET') {
        getScheduleList($auth);
    } elseif ($method === 'POST') {
        setSchedule($auth);
    } else {
        Response::badRequest('请求方法不允许');
    }
}

/**
 * 获取排班列表
 */
function getScheduleList($auth) {
    $startDate = input('start_date', date('Y-m-d'));
    $endDate = input('end_date', date('Y-m-d', strtotime('+7 days')));
    
    $schedules = db()->fetchAll(
        'SELECT id, base_date as date, base_time_start as time_start, 
                base_time_end as time_end, base_status as status
         FROM qy_technician_schedule_list
         WHERE base_technician_id = ? AND base_date BETWEEN ? AND ?
         ORDER BY base_date ASC, base_time_start ASC',
        [$auth['id'], $startDate, $endDate]
    );
    
    Response::success($schedules);
}

/**
 * 设置排班
 */
function setSchedule($auth) {
    validate_required(['date', 'time_start', 'time_end']);
    
    $date = input('date');
    $timeStart = input('time_start');
    $timeEnd = input('time_end');
    
    // 验证日期
    if (strtotime($date) < strtotime(date('Y-m-d'))) {
        Response::badRequest('不能设置过去的日期');
    }
    
    // 验证时间
    if ($timeStart >= $timeEnd) {
        Response::badRequest('结束时间必须大于开始时间');
    }
    
    // 检查时间段是否至少2小时
    $startMinutes = strtotime($timeStart) - strtotime('00:00:00');
    $endMinutes = strtotime($timeEnd) - strtotime('00:00:00');
    if (($endMinutes - $startMinutes) < 7200) { // 2小时 = 7200秒
        Response::badRequest('时间段至少需要2小时');
    }
    
    // 检查是否有冲突
    $conflict = db()->fetch(
        'SELECT id FROM qy_technician_schedule_list 
         WHERE base_technician_id = ? AND base_date = ?
         AND ((base_time_start < ? AND base_time_end > ?) OR (base_time_start < ? AND base_time_end > ?))',
        [$auth['id'], $date, $timeEnd, $timeStart, $timeStart, $timeStart]
    );
    
    if ($conflict) {
        Response::badRequest('该时间段与已有排班冲突');
    }
    
    $scheduleId = db()->insert('qy_technician_schedule_list', [
        'base_technician_id' => $auth['id'],
        'base_date' => $date,
        'base_time_start' => $timeStart,
        'base_time_end' => $timeEnd,
        'base_status' => 1
    ]);
    
    Response::success([
        'schedule_id' => $scheduleId
    ], '排班设置成功');
}

/**
 * 删除排班
 */
function deleteSchedule($auth, $id) {
    $schedule = db()->fetch(
        'SELECT * FROM qy_technician_schedule_list WHERE id = ? AND base_technician_id = ?',
        [$id, $auth['id']]
    );
    
    if (!$schedule) {
        Response::notFound('排班不存在');
    }
    
    // 检查是否有该时段的订单
    $hasOrder = db()->fetch(
        'SELECT id FROM qy_order_list 
         WHERE base_technician_id = ? AND DATE(base_time_scheduled) = ?
         AND base_status_order IN (1, 2, 3, 4)',
        [$auth['id'], $schedule['base_date']]
    );
    
    if ($hasOrder) {
        Response::badRequest('该时段有未完成的订单，无法删除');
    }
    
    db()->delete('qy_technician_schedule_list', 'id = ?', [$id]);
    
    Response::success(null, '排班已删除');
}

/**
 * 上线
 */
function goOnline($auth) {
    // 检查是否有今日排班
    $hasSchedule = db()->fetch(
        'SELECT id FROM qy_technician_schedule_list 
         WHERE base_technician_id = ? AND base_date = ? AND base_status = 1',
        [$auth['id'], date('Y-m-d')]
    );
    
    // 即使没有排班也允许上线（灵活模式）
    
    db()->update('qy_technician_list', [
        'base_status_online' => 1
    ], 'id = :id', ['id' => $auth['id']]);
    
    Response::success(null, '已上线');
}

/**
 * 下线
 */
function goOffline($auth) {
    // 检查是否有进行中的订单
    $activeOrder = db()->fetch(
        'SELECT id FROM qy_order_list 
         WHERE base_technician_id = ? AND base_status_order IN (2, 3, 4)',
        [$auth['id']]
    );
    
    if ($activeOrder) {
        Response::badRequest('您有进行中的订单，无法下线');
    }
    
    db()->update('qy_technician_list', [
        'base_status_online' => 0
    ], 'id = :id', ['id' => $auth['id']]);
    
    Response::success(null, '已下线');
}

/**
 * 更新位置
 */
function updateLocation($auth) {
    validate_required(['lat', 'lng']);
    
    $lat = input('lat');
    $lng = input('lng');
    
    db()->update('qy_technician_list', [
        'base_location_lat' => $lat,
        'base_location_lng' => $lng
    ], 'id = :id', ['id' => $auth['id']]);
    
    Response::success(null, '位置已更新');
}
