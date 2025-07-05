<?php
require_once 'db.php';

echo "=== MBook3 Admin Status ===\n\n";

try {
    // Check admin_users table
    $stmt = $pdo->prepare("SELECT au.user_id, au.role, u.username, u.email 
                           FROM admin_users au 
                           JOIN users u ON au.user_id = u.id");
    $stmt->execute();
    $adminUsers = $stmt->fetchAll();
    
    echo "Admin Users (from admin_users table):\n";
    if (count($adminUsers) > 0) {
        foreach ($adminUsers as $admin) {
            echo "- {$admin['username']} ({$admin['email']}) - Role: {$admin['role']}\n";
        }
    } else {
        echo "- No admin users found\n";
    }
    
    echo "\nUsers with is_admin flag:\n";
    $stmt = $pdo->prepare("SELECT username, email, is_admin FROM users WHERE is_admin = TRUE");
    $stmt->execute();
    $adminFlagUsers = $stmt->fetchAll();
    
    if (count($adminFlagUsers) > 0) {
        foreach ($adminFlagUsers as $user) {
            echo "- {$user['username']} ({$user['email']}) - is_admin: " . ($user['is_admin'] ? 'TRUE' : 'FALSE') . "\n";
        }
    } else {
        echo "- No users with admin flag found\n";
    }
    
    echo "\nTotal users in database:\n";
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM users");
    $stmt->execute();
    $totalUsers = $stmt->fetch();
    echo "- {$totalUsers['count']} total users\n";
    
    echo "\nBooks in database:\n";
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM books");
    $stmt->execute();
    $totalBooks = $stmt->fetch();
    echo "- {$totalBooks['count']} total books\n";
    
    echo "\nPending submissions:\n";
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM pending_books WHERE status = 'pending'");
    $stmt->execute();
    $pendingSubmissions = $stmt->fetch();
    echo "- {$pendingSubmissions['count']} pending submissions\n";
    
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?> 