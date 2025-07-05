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
$submission_id = $_GET['id'] ?? null;

if (!$submission_id) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Submission ID required']);
    exit;
}

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

    // Get submission details with user information
    $stmt = $pdo->prepare("
        SELECT pb.*, u.username 
        FROM pending_books pb 
        JOIN users u ON pb.user_id = u.id 
        WHERE pb.id = ?
    ");
    
    $stmt->execute([$submission_id]);
    $submission = $stmt->fetch();

    if (!$submission) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Submission not found']);
        exit;
    }

    echo json_encode([
        'success' => true,
        'submission' => $submission
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?> 