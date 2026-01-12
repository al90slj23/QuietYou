<?php
/**
 * 轻养到家 - 用户端服务 API
 * ZERO 框架规范 - 冒号语法
 * 
 * GET /api/user/service/category       服务分类列表
 * GET /api/user/service/item           服务项目列表
 * GET /api/user/service/item:123       服务详情
 */

require_once dirname(dirname(dirname(__FILE__))) . '/bootstrap.include.php';

$method = $_SERVER['REQUEST_METHOD'];
$path = $_GET['path'] ?? '';

if ($method !== 'GET') {
    Response::badRequest('请求方法不允许');
}

// 解析路径
$parts = explode('/', trim($path, '/'));
$resource = $parts[0] ?? '';

switch ($resource) {
    case 'category':
        getCategoryList();
        break;
        
    case 'item':
        $id = $parts[1] ?? null;
        if ($id) {
            getServiceDetail($id);
        } else {
            getServiceList();
        }
        break;
        
    default:
        Response::notFound('接口不存在');
}

/**
 * 获取服务分类列表
 */
function getCategoryList() {
    $categories = db()->fetchAll(
        'SELECT id, base_profile_name as name, base_profile_icon as icon, 
                base_profile_desc as description, base_sort_order as sort_order
         FROM qy_service_category_list 
         WHERE base_status_active = 1 
         ORDER BY base_sort_order ASC'
    );
    
    Response::success($categories);
}

/**
 * 获取服务项目列表
 */
function getServiceList() {
    $categoryId = input('category_id');
    $minPrice = input('min_price');
    $maxPrice = input('max_price');
    $minDuration = input('min_duration');
    $maxDuration = input('max_duration');
    $page = max(1, (int)input('page', 1));
    $pageSize = min(50, max(1, (int)input('page_size', 20)));
    
    $where = ['s.base_status_active = 1'];
    $params = [];
    
    if ($categoryId) {
        $where[] = 's.base_category_id = :category_id';
        $params['category_id'] = $categoryId;
    }
    
    if ($minPrice !== null) {
        $where[] = 's.base_price_base >= :min_price';
        $params['min_price'] = $minPrice;
    }
    
    if ($maxPrice !== null) {
        $where[] = 's.base_price_base <= :max_price';
        $params['max_price'] = $maxPrice;
    }
    
    if ($minDuration !== null) {
        $where[] = 's.base_duration_minutes >= :min_duration';
        $params['min_duration'] = $minDuration;
    }
    
    if ($maxDuration !== null) {
        $where[] = 's.base_duration_minutes <= :max_duration';
        $params['max_duration'] = $maxDuration;
    }
    
    $whereClause = implode(' AND ', $where);
    
    // 获取总数
    $total = db()->fetch(
        "SELECT COUNT(*) as count FROM qy_service_item_list s WHERE $whereClause",
        $params
    )['count'];
    
    // 获取列表
    $offset = ($page - 1) * $pageSize;
    $services = db()->fetchAll(
        "SELECT s.id, s.base_profile_name as name, s.base_profile_icon as icon,
                s.base_profile_desc as description, s.base_price_base as price_min,
                s.base_price_max as price_max, s.base_duration_minutes as duration,
                c.base_profile_name as category_name
         FROM qy_service_item_list s
         LEFT JOIN qy_service_category_list c ON s.base_category_id = c.id
         WHERE $whereClause
         ORDER BY s.base_sort_order ASC
         LIMIT $offset, $pageSize",
        $params
    );
    
    Response::paginate($services, $total, $page, $pageSize);
}

/**
 * 获取服务详情
 */
function getServiceDetail($id) {
    $service = db()->fetch(
        'SELECT s.id, s.base_profile_name as name, s.base_profile_icon as icon,
                s.base_profile_desc as description, s.base_profile_process as process,
                s.base_profile_notice as notice, s.base_price_base as price_min,
                s.base_price_max as price_max, s.base_duration_minutes as duration,
                s.base_category_id as category_id, c.base_profile_name as category_name
         FROM qy_service_item_list s
         LEFT JOIN qy_service_category_list c ON s.base_category_id = c.id
         WHERE s.id = ? AND s.base_status_active = 1',
        [$id]
    );
    
    if (!$service) {
        Response::notFound('服务不存在');
    }
    
    Response::success($service);
}
