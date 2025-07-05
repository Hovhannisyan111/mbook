<?php
session_start();
require_once '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$email || !$password) {
        echo json_encode(['success' => false, 'message' => 'Please provide email and password']);
        exit;
    }

    try {
        // Check if user exists and is admin
        $stmt = $pdo->prepare("SELECT u.id, u.username, u.password_hash, u.is_admin, a.role 
                               FROM users u 
                               LEFT JOIN admin_users a ON u.id = a.user_id 
                               WHERE u.email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            // Check if user is admin (either in admin_users table or has is_admin flag)
            if ($user['role'] || $user['is_admin']) {
                // Admin login successful
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['is_admin'] = true;
                $_SESSION['admin_role'] = $user['role'] ?: 'admin';
                
                echo json_encode(['success' => true, 'message' => 'Admin login successful']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Access denied. Admin privileges required.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid email or password']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}
?> 