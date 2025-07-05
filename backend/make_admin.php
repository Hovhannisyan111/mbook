<?php
require_once 'db.php';

// This script makes a user an admin
// Usage: /opt/lampp/bin/php backend/make_admin.php

try {
    // Get the first user (assuming it's the test user)
    $stmt = $pdo->prepare("SELECT id, username FROM users ORDER BY id ASC LIMIT 1");
    $stmt->execute();
    $user = $stmt->fetch();

    if ($user) {
        // Make this user an admin in both tables
        $updateStmt = $pdo->prepare("UPDATE users SET is_admin = TRUE WHERE id = ?");
        $updateStmt->execute([$user['id']]);
        
        // Also add to admin_users table
        $adminStmt = $pdo->prepare("INSERT IGNORE INTO admin_users (user_id, role) VALUES (?, 'admin')");
        $adminStmt->execute([$user['id']]);
        
        echo "✅ User '{$user['username']}' (ID: {$user['id']}) is now an admin!\n";
        echo "Admin access granted in both users and admin_users tables.\n";
        echo "You can now access the admin panel at: http://localhost/mbook/public/admin.html\n";
        echo "Or use admin login at: http://localhost/mbook/public/admin_login.html\n";
    } else {
        echo "❌ No users found in the database.\n";
        echo "Please register a user first at: http://localhost/mbook/public/register.html\n";
    }

} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?> 