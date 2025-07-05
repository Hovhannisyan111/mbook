<?php
require_once 'db.php';

try {
    // Get all approved books from the books table
    $stmt = $pdo->prepare("
        SELECT id, title, author, cover_image, audio_file, category 
        FROM books 
        ORDER BY id DESC
    ");
    $stmt->execute();
    $books = $stmt->fetchAll();
    
    echo json_encode(['success' => true, 'books' => $books]);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?> 