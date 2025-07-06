<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Not authorized']);
    exit;
}

$user_id = $_SESSION['user_id'];
$book_id = $_POST['book_id'] ?? null;
$status = $_POST['status'] ?? null;

if (!$book_id || !$status) {
    echo json_encode(['success' => false, 'message' => 'Missing data']);
    exit;
}

try {
    // Проверим, есть ли уже запись
    $stmt = $pdo->prepare("SELECT id FROM user_books WHERE user_id = ? AND book_id = ?");
    $stmt->execute([$user_id, $book_id]);
    $existing = $stmt->fetch();

    if ($existing) {
        // Обновим статус
        $stmt = $pdo->prepare("UPDATE user_books SET status = ?, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$status, $existing['id']]);
    } else {
        // Вставим новую запись
        $stmt = $pdo->prepare("INSERT INTO user_books (user_id, book_id, status, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())");
        $stmt->execute([$user_id, $book_id, $status]);
    }

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error']);
}
