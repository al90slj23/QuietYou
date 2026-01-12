<?php
/**
 * 轻养到家 - 用户端评价 API
 * ZERO 框架规范 - 冒号语法
 * 
 * POST /api/user/review              提交评价
 */

require_once dirname(dirname(dirname(__FILE__))) . '/bootstrap.include.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method !== 'POST') {
    Response::badRequest('请求方法不允许');
}

// 需要登录
$auth = auth_require(TokenType::USER);

submitReview($auth);

/**
 * 提交评价
 */
function submitReview($auth) {
    validate_required(['order_id', 'rating']);
    
    $orderId = input('order_id');
    $rating = (int)input('rating');
    $content = input('content', '');
    $photos = input('photos', []);
    
    // 验证评分
    if ($rating < 1 || $rating > 5) {
        Response::badRequest('评分必须在1-5之间');
    }
    
    // 验证订单
    $order = db()->fetch(
        'SELECT * FROM qy_order_list WHERE id = ? AND base_user_id = ?',
        [$orderId, $auth['id']]
    );
    
    if (!$order) {
        Response::notFound('订单不存在');
    }
    
    // 只有已完成的订单可以评价
    if ($order['base_status_order'] != 5) {
        Response::badRequest('只有已完成的订单可以评价');
    }
    
    // 检查是否已评价
    $existingReview = db()->fetch(
        'SELECT id FROM qy_review_list WHERE base_order_id = ?',
        [$orderId]
    );
    
    if ($existingReview) {
        Response::badRequest('该订单已评价');
    }
    
    // 创建评价
    $reviewId = db()->insert('qy_review_list', [
        'base_order_id' => $orderId,
        'base_user_id' => $auth['id'],
        'base_technician_id' => $order['base_technician_id'],
        'base_rating' => $rating,
        'base_content' => $content,
        'base_photos' => json_encode($photos),
        'base_status_visible' => 1,
        'base_time_created' => date('Y-m-d H:i:s')
    ]);
    
    // 更新技师评分
    updateTechnicianRating($order['base_technician_id']);
    
    Response::success([
        'review_id' => $reviewId
    ], '评价成功');
}

/**
 * 更新技师平均评分
 */
function updateTechnicianRating($technicianId) {
    $stats = db()->fetch(
        'SELECT AVG(base_rating) as avg_rating, COUNT(*) as count 
         FROM qy_review_list 
         WHERE base_technician_id = ? AND base_status_visible = 1',
        [$technicianId]
    );
    
    db()->update('qy_technician_list', [
        'base_stat_rating_avg' => round($stats['avg_rating'], 2),
        'base_stat_rating_count' => $stats['count']
    ], 'id = :id', ['id' => $technicianId]);
}
