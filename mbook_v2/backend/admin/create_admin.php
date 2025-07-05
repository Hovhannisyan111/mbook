<?php
require_once '../db.php';

// Create admin user
$admin_email = 'admin@mbook.com';
$admin_password = 'admin123'; // You can change this password
$admin_username = 'admin';

try {
    // Check if admin already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$admin_email]);
    
    if ($stmt->fetch()) {
        echo "Admin user already exists!\n";
        echo "Email: $admin_email\n";
        echo "Password: $admin_password\n";
    } else {
        // Create new admin user
        $hashed_password = password_hash($admin_password, PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash, is_admin) VALUES (?, ?, ?, 1)");
        $stmt->execute([$admin_username, $admin_email, $hashed_password]);
        
        echo "✅ Admin user created successfully!\n";
        echo "Email: $admin_email\n";
        echo "Password: $admin_password\n";
        echo "You can now login at: http://localhost/MBook3/public/admin_login.html\n";
    }
    
} catch (PDOException $e) {
    echo "❌ Error creating admin user: " . $e->getMessage() . "\n";
}
?> 