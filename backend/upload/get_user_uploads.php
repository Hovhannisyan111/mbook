<?php
session_start();
require_once '../db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Please login']);
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    $stmt = $pdo->prepare("
        SELECT id, title, author, description, category, status, admin_comment, submitted_at, reviewed_at
        FROM pending_books 
        WHERE user_id = ? 
        ORDER BY submitted_at DESC
    ");
    
    $stmt->execute([$user_id]);
    $uploads = $stmt->fetchAll();

    echo json_encode([
        'success' => true,
        'uploads' => $uploads
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?> 