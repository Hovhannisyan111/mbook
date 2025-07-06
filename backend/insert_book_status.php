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
$status = 'saved';

$stmt = $pdo->prepare("INSERT INTO user_books (user_id, book_id, status, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())");
$stmt->execute([$user_id, $book_id, $status]);

