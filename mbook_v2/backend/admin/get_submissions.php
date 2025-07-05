<?php
session_start();
require_once '../db.php';

// Check if user is admin
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Please login']);
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    // Check admin status
    $stmt = $pdo->prepare("SELECT is_admin FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    if (!$user || !$user['is_admin']) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Admin access required']);
        exit;
    }

    $status_filter = $_GET['status'] ?? 'all';
    
    // Build query based on status filter
    $where_clause = '';
    $params = [];
    
    if ($status_filter !== 'all') {
        $where_clause = 'WHERE pb.status = ?';
        $params[] = $status_filter;
    }

    // Get submissions with user information
    $sql = "
        SELECT pb.*, u.username 
        FROM pending_books pb 
        JOIN users u ON pb.user_id = u.id 
        $where_clause 
        ORDER BY pb.submitted_at DESC
    ";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $submissions = $stmt->fetchAll();

    // Get statistics
    $stats_sql = "
        SELECT 
            SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
            SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved,
            SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected
        FROM pending_books
    ";
    
    $stats_stmt = $pdo->query($stats_sql);
    $stats = $stats_stmt->fetch();

    echo json_encode([
        'success' => true,
        'submissions' => $submissions,
        'stats' => $stats
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?> 