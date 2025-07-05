<?php
require_once 'db.php';

try {
    // Define all possible categories
    $allCategories = [
        'Literature',
        'History', 
        'Biography',
        'Self-Help',
        'Religion',
        'Mystery'
    ];
    
    // Get categories that actually have books
    $stmt = $pdo->prepare("
        SELECT DISTINCT category 
        FROM books 
        WHERE category IS NOT NULL AND category != '' 
        ORDER BY category
    ");
    $stmt->execute();
    $existingCategories = $stmt->fetchAll();
    $existingCategoryNames = array_column($existingCategories, 'category');
    
    // Return all categories, but mark which ones have books
    $categoriesWithStatus = [];
    foreach ($allCategories as $category) {
        $categoriesWithStatus[] = [
            'name' => $category,
            'hasBooks' => in_array($category, $existingCategoryNames)
        ];
    }
    
    echo json_encode([
        'success' => true, 
        'categories' => $allCategories,
        'categoriesWithStatus' => $categoriesWithStatus
    ]);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?> 