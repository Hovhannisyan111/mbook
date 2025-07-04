<?php
require_once 'backend/db.php';

// Set your username here
$username = 'amigo1'; // Change this to your username

try {
    // Check if user exists
    $stmt = $pdo->prepare("SELECT id, username FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if (!$user) {
        echo "User '$username' not found. Please create the user first or check the username.\n";
        exit;
    }

    // Make user admin
    $stmt = $pdo->prepare("UPDATE users SET is_admin = 1 WHERE username = ?");
    if ($stmt->execute([$username])) {
        echo "User '$username' is now an admin!\n";
        echo "You can now:\n";
        echo "1. Upload books directly to the home page\n";
        echo "2. Access the admin panel at: public/admin.html\n";
        echo "3. Manage books at: public/admin_books.html\n";
    } else {
        echo "Failed to make user admin.\n";
    }

} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}
?> 