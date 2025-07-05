<?php
require_once 'db.php';

try {
    // Create users table
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        email VARCHAR(100) NOT NULL UNIQUE,
        password_hash VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Create books table
    $pdo->exec("CREATE TABLE IF NOT EXISTS books (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        author VARCHAR(255) NOT NULL,
        cover_image VARCHAR(255),
        audio_file VARCHAR(255),
        category VARCHAR(100),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Create user_books table for tracking user's saved/reading books
    $pdo->exec("CREATE TABLE IF NOT EXISTS user_books (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        book_id INT NOT NULL,
        status ENUM('saved', 'reading', 'finished') DEFAULT 'saved',
        progress FLOAT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE,
        UNIQUE KEY unique_user_book (user_id, book_id)
    )");

    // Insert sample books (only if they don't exist)
    $sampleBooks = [
        ['Avetik Isahakyan', 'Avetik Isahakyan', 'images/isahakyan.jpg', 'isahakyan.wav', 'Literature'],
        ['First Zeitun Resistance', 'Author Name', 'images/zeytun.jpg', 'zeytun.wav', 'History'],
        ['The Battle of Avarayr', 'Yeghishe', 'images/avarayr.jpg', 'avarayr.wav', 'History']
    ];

    $stmt = $pdo->prepare("INSERT IGNORE INTO books (title, author, cover_image, audio_file, category) VALUES (?, ?, ?, ?, ?)");
    
    foreach ($sampleBooks as $book) {
        $stmt->execute($book);
    }

    echo "Database setup completed successfully!\n";
    echo "Tables created: users, books, user_books\n";
    echo "Sample books inserted.\n";

} catch (PDOException $e) {
    echo "Database setup failed: " . $e->getMessage() . "\n";
}
?> 