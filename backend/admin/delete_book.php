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

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
        $book_id = $input['book_id'] ?? null;

        if (!$book_id) {
            echo json_encode(['success' => false, 'message' => 'Book ID is required']);
            exit;
        }

        // Get book info before deleting (for file cleanup)
        $stmt = $pdo->prepare("SELECT audio_file, cover_image FROM books WHERE id = ?");
        $stmt->execute([$book_id]);
        $book = $stmt->fetch();

        if (!$book) {
            echo json_encode(['success' => false, 'message' => 'Book not found']);
            exit;
        }

        // Delete from database
        $stmt = $pdo->prepare("DELETE FROM books WHERE id = ?");
        if ($stmt->execute([$book_id])) {
            // Clean up files
            if ($book['audio_file']) {
                $audio_path = '../../' . $book['audio_file'];
                if (file_exists($audio_path)) {
                    unlink($audio_path);
                }
            }
            
            if ($book['cover_image']) {
                $cover_path = '../../' . $book['cover_image'];
                if (file_exists($cover_path)) {
                    unlink($cover_path);
                }
            }

            echo json_encode(['success' => true, 'message' => 'Book deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete book']);
        }

    } else {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    }

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?> 