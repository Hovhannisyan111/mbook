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

    // Get POST data
    $input = json_decode(file_get_contents('php://input'), true);
    $submission_id = $input['submission_id'] ?? null;
    $action = $input['action'] ?? null;
    $comment = $input['comment'] ?? '';

    if (!$submission_id || !$action) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
        exit;
    }

    if (!in_array($action, ['approve', 'reject'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        exit;
    }

    // Get submission details
    $stmt = $pdo->prepare("SELECT * FROM pending_books WHERE id = ? AND status = 'pending'");
    $stmt->execute([$submission_id]);
    $submission = $stmt->fetch();

    if (!$submission) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Submission not found or already reviewed']);
        exit;
    }

    // Update submission status
    $new_status = ($action === 'approve') ? 'approved' : 'rejected';
    
    $stmt = $pdo->prepare("
        UPDATE pending_books 
        SET status = ?, admin_comment = ?, admin_id = ?, reviewed_at = CURRENT_TIMESTAMP 
        WHERE id = ?
    ");
    
    if ($stmt->execute([$new_status, $comment, $user_id, $submission_id])) {
        
        // If approved, add to main books table
        if ($action === 'approve') {
            $stmt = $pdo->prepare("
                INSERT INTO books (title, author, cover_image, audio_file, category) 
                VALUES (?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $submission['title'],
                $submission['author'],
                $submission['cover_image'],
                $submission['audio_file'],
                $submission['category']
            ]);
        }
        
        echo json_encode(['success' => true, 'message' => 'Submission ' . $action . 'd successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update submission']);
    }

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?> 