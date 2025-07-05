<?php
require_once 'db.php';

$category = $_GET['category'] ?? '';

try {
    if ($category && $category !== 'all') {
        // Get books by specific category
        $stmt = $pdo->prepare("
            SELECT id, title, author, cover_image, audio_file, category 
            FROM books 
            WHERE category = ?
            ORDER BY id DESC
        ");
        $stmt->execute([$category]);
    } else {
        // Get all books
        $stmt = $pdo->prepare("
            SELECT id, title, author, cover_image, audio_file, category 
            FROM books 
            ORDER BY id DESC
        ");
        $stmt->execute();
    }
    
    $books = $stmt->fetchAll();
    echo json_encode(['success' => true, 'books' => $books, 'category' => $category]);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?> 