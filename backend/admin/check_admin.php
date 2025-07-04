<?php
session_start();
require_once '../db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['is_admin' => false]);
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    // Check if user is admin
    $stmt = $pdo->prepare("SELECT is_admin FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    if ($user && $user['is_admin']) {
        echo json_encode(['is_admin' => true]);
    } else {
        echo json_encode(['is_admin' => false]);
    }

} catch (PDOException $e) {
    echo json_encode(['is_admin' => false, 'error' => $e->getMessage()]);
}
?> 