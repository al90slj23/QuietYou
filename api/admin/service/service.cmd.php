<?php
/**
 * 轻养到家 - 管理后台服务管理 API
 * ZERO 框架规范 - 冒号语法
 * 
 * GET    /api/admin/service/category       分类列表
 * POST   /api/admin/service/category       添加分类
 * PUT    /api/admin/service/category:123   更新分类
 * DELETE /api/admin/service/category:123   删除分类
 * GET    /api/admin/service/item           项目列表
 * POST   /api/admin/service/item           添加项目
 * PUT    /api/admin/service/item:123       更新项目
 * DELETE /api/admin/service/item:123       删除项目
 */

require_once dirname(dirname(dirname(__FILE__))) . '/bootstrap.inc.php';

$method = $_SERVER['REQUEST_METHOD'];
$path = $_GET['path'] ?? '';
$id = $_GET['id'] ?? null;

// 需要管理员登录
$auth = auth_require(TokenType::ADMIN);

// 解析路径
$parts = explode('/', trim($path, '/'));
$resource = $parts[0] ?? '';

switch ($resource) {
    case 'category':
        handleCategory($method, $id);
        break;
    case 'item':
        handleItem($method, $id);
        break;
    default:
        Response::notFound('接口不存在');
}

/**
 * 处理分类
 */
function handleCategory($method, $id) {
    if ($id) {
        switch ($method) {
            case 'PUT':
                updateCategory($id);
                break;
            case 'DELETE':
                deleteCategory($id);
                break;
            default:
                Response::badRequest('请求方法不允许');
        }
    } else {
        switch ($method) {
            case 'GET':
                getCategoryList();
                break;
            case 'POST':
                createCategory();
                break;
            default:
                Response::badRequest('请求方法不允许');
        }
    }
}

/**
 * 获取分类列表
 */
function getCategoryList() {
    $categories = db()->fetchAll(
        'SELECT c.id, c.base_profile_name as name, c.base_profile_icon as icon,
                c.base_profile_desc as description, c.base_sort_order as sort_order,
                c.base_status_active as status,
                (SELECT COUNT(*) FROM qy_service_item_list WHERE base_category_id = c.id) as item_count
         FROM qy_service_category_list c
         ORDER BY c.base_sort_order ASC'
    );
    
    Response::success($categories);
}

/**
 * 创建分类
 */
function createCategory() {
    validate_required(['name', 'icon']);
    
    $name = input('name');
    $icon = input('icon');
    $desc = input('description', '');
    $sortOrder = (int)input('sort_order', 0);
    
    $categoryId = db()->insert('qy_service_category_list', [
        'base_profile_name' => $name,
        'base_profile_icon' => $icon,
        'base_profile_desc' => $desc,
        'base_sort_order' => $sortOrder,
        'base_status_active' => 1
    ]);
    
    Response::success(['category_id' => $categoryId], '分类创建成功');
}

/**
 * 更新分类
 */
function updateCategory($id) {
    $category = db()->fetch('SELECT id FROM qy_service_category_list WHERE id = ?', [$id]);
    if (!$category) {
        Response::notFound('分类不存在');
    }
    
    $data = [];
    if (input('name') !== null) $data['base_profile_name'] = input('name');
    if (input('icon') !== null) $data['base_profile_icon'] = input('icon');
    if (input('description') !== null) $data['base_profile_desc'] = input('description');
    if (input('sort_order') !== null) $data['base_sort_order'] = (int)input('sort_order');
    if (input('status') !== null) $data['base_status_active'] = (int)input('status');
    
    if (empty($data)) {
        Response::badRequest('没有要更新的数据');
    }
    
    db()->update('qy_service_category_list', $data, 'id = :id', ['id' => $id]);
    
    Response::success(null, '分类更新成功');
}

/**
 * 删除分类
 */
function deleteCategory($id) {
    $category = db()->fetch('SELECT id FROM qy_service_category_list WHERE id = ?', [$id]);
    if (!$category) {
        Response::notFound('分类不存在');
    }
    
    // 检查是否有服务项目
    $itemCount = db()->fetch(
        'SELECT COUNT(*) as count FROM qy_service_item_list WHERE base_category_id = ?',
        [$id]
    )['count'];
    
    if ($itemCount > 0) {
        Response::badRequest('该分类下有服务项目，无法删除');
    }
    
    db()->delete('qy_service_category_list', 'id = ?', [$id]);
    
    Response::success(null, '分类删除成功');
}

/**
 * 处理服务项目
 */
function handleItem($method, $id) {
    if ($id) {
        switch ($method) {
            case 'PUT':
                updateItem($id);
                break;
            case 'DELETE':
                deleteItem($id);
                break;
            default:
                Response::badRequest('请求方法不允许');
        }
    } else {
        switch ($method) {
            case 'GET':
                getItemList();
                break;
            case 'POST':
                createItem();
                break;
            default:
                Response::badRequest('请求方法不允许');
        }
    }
}

/**
 * 获取服务项目列表
 */
function getItemList() {
    $categoryId = input('category_id');
    $status = input('status');
    $page = max(1, (int)input('page', 1));
    $pageSize = min(50, max(1, (int)input('page_size', 20)));
    
    $where = ['1=1'];
    $params = [];
    
    if ($categoryId) {
        $where[] = 's.base_category_id = :category_id';
        $params['category_id'] = $categoryId;
    }
    
    if ($status !== null && $status !== '') {
        $where[] = 's.base_status_active = :status';
        $params['status'] = $status;
    }
    
    $whereClause = implode(' AND ', $where);
    
    $total = db()->fetch(
        "SELECT COUNT(*) as count FROM qy_service_item_list s WHERE $whereClause",
        $params
    )['count'];
    
    $offset = ($page - 1) * $pageSize;
    $items = db()->fetchAll(
        "SELECT s.id, s.base_profile_name as name, s.base_profile_icon as icon,
                s.base_price_base as price_min, s.base_price_max as price_max,
                s.base_duration_minutes as duration, s.base_sort_order as sort_order,
                s.base_status_active as status, c.base_profile_name as category_name
         FROM qy_service_item_list s
         LEFT JOIN qy_service_category_list c ON s.base_category_id = c.id
         WHERE $whereClause
         ORDER BY s.base_sort_order ASC
         LIMIT $offset, $pageSize",
        $params
    );
    
    Response::paginate($items, $total, $page, $pageSize);
}

/**
 * 创建服务项目
 */
function createItem() {
    validate_required(['category_id', 'name', 'price', 'duration']);
    
    $itemId = db()->insert('qy_service_item_list', [
        'base_category_id' => input('category_id'),
        'base_profile_name' => input('name'),
        'base_profile_icon' => input('icon', ''),
        'base_profile_desc' => input('description', ''),
        'base_profile_process' => input('process', ''),
        'base_profile_notice' => input('notice', ''),
        'base_price_base' => input('price'),
        'base_price_max' => input('price_max', input('price')),
        'base_duration_minutes' => input('duration'),
        'base_sort_order' => (int)input('sort_order', 0),
        'base_status_active' => 1
    ]);
    
    Response::success(['item_id' => $itemId], '服务项目创建成功');
}

/**
 * 更新服务项目
 */
function updateItem($id) {
    $item = db()->fetch('SELECT id FROM qy_service_item_list WHERE id = ?', [$id]);
    if (!$item) {
        Response::notFound('服务项目不存在');
    }
    
    $data = [];
    if (input('category_id') !== null) $data['base_category_id'] = input('category_id');
    if (input('name') !== null) $data['base_profile_name'] = input('name');
    if (input('icon') !== null) $data['base_profile_icon'] = input('icon');
    if (input('description') !== null) $data['base_profile_desc'] = input('description');
    if (input('process') !== null) $data['base_profile_process'] = input('process');
    if (input('notice') !== null) $data['base_profile_notice'] = input('notice');
    if (input('price') !== null) $data['base_price_base'] = input('price');
    if (input('price_max') !== null) $data['base_price_max'] = input('price_max');
    if (input('duration') !== null) $data['base_duration_minutes'] = input('duration');
    if (input('sort_order') !== null) $data['base_sort_order'] = (int)input('sort_order');
    if (input('status') !== null) $data['base_status_active'] = (int)input('status');
    
    if (empty($data)) {
        Response::badRequest('没有要更新的数据');
    }
    
    db()->update('qy_service_item_list', $data, 'id = :id', ['id' => $id]);
    
    Response::success(null, '服务项目更新成功');
}

/**
 * 删除服务项目
 */
function deleteItem($id) {
    $item = db()->fetch('SELECT id FROM qy_service_item_list WHERE id = ?', [$id]);
    if (!$item) {
        Response::notFound('服务项目不存在');
    }
    
    db()->delete('qy_service_item_list', 'id = ?', [$id]);
    
    Response::success(null, '服务项目删除成功');
}
