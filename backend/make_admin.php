<?php
require_once 'db.php';

// This script makes a user an admin
// Usage: /opt/lampp/bin/php backend/make_admin.php

try {
    // Get the first user and make them admin
    $stmt = $pdo->prepare("SELECT id, username, email FROM users ORDER BY id LIMIT 1");
    $stmt->execute();
    $user = $stmt->fetch();

    if ($user) {
        // Make user admin
        $update_stmt = $pdo->prepare("UPDATE users SET is_admin = TRUE WHERE id = ?");
        $update_stmt->execute([$user['id']]);
        
        echo "✅ User '{$user['username']}' (ID: {$user['id']}) is now an admin!\n";
        echo "Email: {$user['email']}\n";
        echo "You can now access the admin panel at: http://localhost/MBook3/public/admin.html\n";
    } else {
        echo "❌ No users found in the database.\n";
        echo "Please register a user first at: http://localhost/MBook3/public/register.html\n";
    }

} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?> 