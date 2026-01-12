<?php
/**
 * 轻养到家 - 用户端技师 API
 * ZERO 框架规范 - 冒号语法
 * 
 * GET /api/user/technician              技师列表
 * GET /api/user/technician:123          技师详情
 * GET /api/user/technician:123:reviews  技师评价
 */

require_once dirname(dirname(dirname(__FILE__))) . '/bootstrap.include.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';
$id = $_GET['id'] ?? null;

if ($method !== 'GET') {
    Response::badRequest('请求方法不允许');
}

if ($id) {
    if ($action === 'reviews') {
        getTechnicianReviews($id);
    } else {
        getTechnicianDetail($id);
    }
} else {
    getTechnicianList();
}

/**
 * 获取技师列表
 */
function getTechnicianList() {
    $serviceId = input('service_id');
    $gender = input('gender');
    $minRating = input('min_rating');
    $maxDistance = input('max_distance');
    $minPrice = input('min_price');
    $maxPrice = input('max_price');
    $sortBy = input('sort_by', 'distance'); // distance, rating, price
    $userLat = input('lat');
    $userLng = input('lng');
    $page = max(1, (int)input('page', 1));
    $pageSize = min(50, max(1, (int)input('page_size', 20)));
    
    $where = ['t.base_status_verify = 1', 't.base_status_active = 1', 't.base_status_online = 1'];
    $params = [];
    
    if ($gender !== null && $gender !== '') {
        $where[] = 't.base_profile_gender = :gender';
        $params['gender'] = $gender;
    }
    
    if ($minRating !== null) {
        $where[] = 't.base_stat_rating_avg >= :min_rating';
        $params['min_rating'] = $minRating;
    }
    
    $whereClause = implode(' AND ', $where);
    
    // 距离计算（如果提供了用户位置）
    $distanceSelect = '0 as distance';
    $distanceOrder = '';
    if ($userLat && $userLng) {
        $distanceSelect = "(6371 * acos(cos(radians(:user_lat)) * cos(radians(t.base_location_lat)) * 
                          cos(radians(t.base_location_lng) - radians(:user_lng)) + 
                          sin(radians(:user_lat2)) * sin(radians(t.base_location_lat)))) as distance";
        $params['user_lat'] = $userLat;
        $params['user_lng'] = $userLng;
        $params['user_lat2'] = $userLat;
        
        if ($maxDistance !== null) {
            $where[] = "distance <= :max_distance";
            $params['max_distance'] = $maxDistance;
        }
    }
    
    // 排序
    switch ($sortBy) {
        case 'rating':
            $orderBy = 't.base_stat_rating_avg DESC';
            break;
        case 'price':
            $orderBy = 't.id ASC'; // TODO: 按价格排序需要关联服务价格
            break;
        case 'distance':
        default:
            $orderBy = $userLat ? 'distance ASC' : 't.id ASC';
            break;
    }
    
    // 获取总数
    $total = db()->fetch(
        "SELECT COUNT(*) as count FROM qy_technician_list t WHERE $whereClause",
        $params
    )['count'];
    
    // 获取列表
    $offset = ($page - 1) * $pageSize;
    $technicians = db()->fetchAll(
        "SELECT t.id, t.base_profile_realname as name, t.base_profile_avatar as avatar,
                t.base_profile_gender as gender, t.base_profile_intro as intro,
                t.base_stat_rating_avg as rating, t.base_stat_order_count as order_count,
                t.base_status_online as online_status, s.base_profile_name as shop_name,
                $distanceSelect
         FROM qy_technician_list t
         LEFT JOIN qy_shop_list s ON t.base_shop_id = s.id
         WHERE $whereClause
         ORDER BY $orderBy
         LIMIT $offset, $pageSize",
        $params
    );
    
    // 格式化距离
    foreach ($technicians as &$tech) {
        $tech['distance'] = round($tech['distance'], 2);
        $tech['rating'] = round($tech['rating'], 2);
    }
    
    Response::paginate($technicians, $total, $page, $pageSize);
}

/**
 * 获取技师详情
 */
function getTechnicianDetail($id) {
    $tech = db()->fetch(
        'SELECT t.id, t.base_profile_realname as name, t.base_profile_avatar as avatar,
                t.base_profile_gender as gender, t.base_profile_intro as intro,
                t.base_profile_photos as photos, t.base_stat_rating_avg as rating,
                t.base_stat_order_count as order_count, t.base_stat_rating_count as rating_count,
                t.base_status_online as online_status, t.base_shop_id as shop_id,
                s.base_profile_name as shop_name, s.base_profile_address as shop_address
         FROM qy_technician_list t
         LEFT JOIN qy_shop_list s ON t.base_shop_id = s.id
         WHERE t.id = ? AND t.base_status_verify = 1 AND t.base_status_active = 1',
        [$id]
    );
    
    if (!$tech) {
        Response::notFound('技师不存在');
    }
    
    // 解析照片 JSON
    $tech['photos'] = json_decode($tech['photos'], true) ?: [];
    $tech['rating'] = round($tech['rating'], 2);
    
    // 获取技师服务项目
    $services = db()->fetchAll(
        'SELECT s.id, s.base_profile_name as name, ts.base_price as price,
                s.base_duration_minutes as duration
         FROM qy_technician_service_list ts
         JOIN qy_service_item_list s ON ts.base_service_id = s.id
         WHERE ts.base_technician_id = ? AND ts.base_status_active = 1 AND s.base_status_active = 1',
        [$id]
    );
    
    $tech['services'] = $services;
    
    // 获取最近评价
    $reviews = db()->fetchAll(
        'SELECT r.id, r.base_rating as rating, r.base_content as content,
                r.base_photos as photos, r.base_time_created as created_at,
                u.base_profile_nickname as user_name, u.base_profile_avatar as user_avatar
         FROM qy_review_list r
         JOIN qy_user_list u ON r.base_user_id = u.id
         WHERE r.base_technician_id = ? AND r.base_status_visible = 1
         ORDER BY r.base_time_created DESC
         LIMIT 5',
        [$id]
    );
    
    foreach ($reviews as &$review) {
        $review['photos'] = json_decode($review['photos'], true) ?: [];
    }
    
    $tech['recent_reviews'] = $reviews;
    
    Response::success($tech);
}

/**
 * 获取技师评价列表
 */
function getTechnicianReviews($id) {
    $page = max(1, (int)input('page', 1));
    $pageSize = min(50, max(1, (int)input('page_size', 20)));
    
    // 验证技师存在
    $tech = db()->fetch(
        'SELECT id FROM qy_technician_list WHERE id = ? AND base_status_verify = 1',
        [$id]
    );
    
    if (!$tech) {
        Response::notFound('技师不存在');
    }
    
    // 获取总数
    $total = db()->fetch(
        'SELECT COUNT(*) as count FROM qy_review_list WHERE base_technician_id = ? AND base_status_visible = 1',
        [$id]
    )['count'];
    
    // 获取评价列表
    $offset = ($page - 1) * $pageSize;
    $reviews = db()->fetchAll(
        'SELECT r.id, r.base_rating as rating, r.base_content as content,
                r.base_photos as photos, r.base_reply as reply, r.base_reply_time as reply_time,
                r.base_time_created as created_at,
                u.base_profile_nickname as user_name, u.base_profile_avatar as user_avatar,
                s.base_profile_name as service_name
         FROM qy_review_list r
         JOIN qy_user_list u ON r.base_user_id = u.id
         LEFT JOIN qy_order_list o ON r.base_order_id = o.id
         LEFT JOIN qy_service_item_list s ON o.base_service_id = s.id
         WHERE r.base_technician_id = ? AND r.base_status_visible = 1
         ORDER BY r.base_time_created DESC
         LIMIT ?, ?',
        [$id, $offset, $pageSize]
    );
    
    foreach ($reviews as &$review) {
        $review['photos'] = json_decode($review['photos'], true) ?: [];
    }
    
    Response::paginate($reviews, $total, $page, $pageSize);
}
