<?php
$host = 'localhost';
$db   = 'MBook'; 
$user = 'root'; 
$pass = '';

try {
    // Create connection using MySQLi
    $mysqli = new mysqli($host, $user, $pass, $db);
    
    // Check connection
    if ($mysqli->connect_error) {
        throw new Exception("Connection failed: " . $mysqli->connect_error);
    }
    
    echo "✅ Database connection successful!\n";
    
    // Create users table
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        email VARCHAR(100) NOT NULL UNIQUE,
        password_hash VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    if ($mysqli->query($sql) === TRUE) {
        echo "✅ Users table created successfully\n";
    } else {
        echo "❌ Error creating users table: " . $mysqli->error . "\n";
    }

    // Create books table
    $sql = "CREATE TABLE IF NOT EXISTS books (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        author VARCHAR(255) NOT NULL,
        cover_image VARCHAR(255),
        audio_file VARCHAR(255),
        category VARCHAR(100),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    if ($mysqli->query($sql) === TRUE) {
        echo "✅ Books table created successfully\n";
    } else {
        echo "❌ Error creating books table: " . $mysqli->error . "\n";
    }

    // Create user_books table
    $sql = "CREATE TABLE IF NOT EXISTS user_books (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        book_id INT NOT NULL,
        status ENUM('saved', 'reading', 'finished') DEFAULT 'saved',
        progress FLOAT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE,
        UNIQUE KEY unique_user_book (user_id, book_id)
    )";
    
    if ($mysqli->query($sql) === TRUE) {
        echo "✅ User_books table created successfully\n";
    } else {
        echo "❌ Error creating user_books table: " . $mysqli->error . "\n";
    }

    // Insert sample books
    $sampleBooks = [
        ['Avetik Isahakyan', 'Avetik Isahakyan', '../images/isahakyan.jpg', '../isahakyan.wav', 'Literature'],
        ['First Zeitun Resistance', 'Author Name', '../images/zeytun.jpg', '../zeytun.wav', 'History'],
        ['The Battle of Avarayr', 'Yeghishe', '../images/avarayr.jpg', '../avarayr.wav', 'History']
    ];

    $stmt = $mysqli->prepare("INSERT IGNORE INTO books (title, author, cover_image, audio_file, category) VALUES (?, ?, ?, ?, ?)");
    
    foreach ($sampleBooks as $book) {
        $stmt->bind_param("sssss", $book[0], $book[1], $book[2], $book[3], $book[4]);
        if ($stmt->execute()) {
            echo "✅ Sample book inserted: " . $book[0] . "\n";
        } else {
            echo "❌ Error inserting book: " . $book[0] . " - " . $stmt->error . "\n";
        }
    }
    
    $stmt->close();
    $mysqli->close();
    
    echo "\n🎉 Database setup completed successfully!\n";
    echo "Tables created: users, books, user_books\n";
    echo "Sample books inserted.\n";

} catch (Exception $e) {
    echo "❌ Database setup failed: " . $e->getMessage() . "\n";
}
?> 