<?php
session_start();
require_once '../db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Please login to upload audiobooks']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $title = trim($_POST['title'] ?? '');
    $author = trim($_POST['author'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $description = trim($_POST['description'] ?? '');

    // Validate required fields
    if (!$title || !$author || !$category) {
        echo json_encode(['success' => false, 'message' => 'Please fill in all required fields']);
        exit;
    }

    // Check if audio file was uploaded
    if (!isset($_FILES['audioFile']) || $_FILES['audioFile']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['success' => false, 'message' => 'Please select an audio file']);
        exit;
    }

    $audio_file = $_FILES['audioFile'];
    $cover_image = $_FILES['coverImage'] ?? null;

    // Validate audio file
    $allowed_audio_types = [
        'audio/mp3', 'audio/mpeg', 'audio/wav', 'audio/x-wav', 
        'audio/m4a', 'audio/x-m4a', 'audio/mp4', 'audio/x-mp4'
    ];
    $allowed_audio_extensions = ['.mp3', '.wav', '.m4a'];
    
    // Check MIME type first
    $mime_type_ok = in_array($audio_file['type'], $allowed_audio_types);
    
    // If MIME type check fails, check file extension
    $extension_ok = false;
    if (!$mime_type_ok) {
        $file_extension = strtolower('.' . pathinfo($audio_file['name'], PATHINFO_EXTENSION));
        $extension_ok = in_array($file_extension, $allowed_audio_extensions);
    }
    
    if (!$mime_type_ok && !$extension_ok) {
        echo json_encode(['success' => false, 'message' => 'Invalid audio file type. Please use MP3, WAV, or M4A']);
        exit;
    }

    // Check file size (100MB limit)
    if ($audio_file['size'] > 100 * 1024 * 1024) {
        echo json_encode(['success' => false, 'message' => 'Audio file size must be less than 100MB']);
        exit;
    }

    // Create uploads directory if it doesn't exist
    $uploads_dir = '../../uploads/';
    $audio_dir = $uploads_dir . 'audio/';
    $image_dir = $uploads_dir . 'images/';

    if (!file_exists($uploads_dir)) {
        mkdir($uploads_dir, 0755, true);
    }
    if (!file_exists($audio_dir)) {
        mkdir($audio_dir, 0755, true);
    }
    if (!file_exists($image_dir)) {
        mkdir($image_dir, 0755, true);
    }

    // Generate unique filename for audio
    $audio_extension = pathinfo($audio_file['name'], PATHINFO_EXTENSION);
    $audio_filename = uniqid() . '_' . time() . '.' . $audio_extension;
    $audio_path = $audio_dir . $audio_filename;

    // Move audio file
    if (!move_uploaded_file($audio_file['tmp_name'], $audio_path)) {
        // Add debugging information
        $error_info = [
            'tmp_name' => $audio_file['tmp_name'],
            'target_path' => $audio_path,
            'file_exists' => file_exists($audio_file['tmp_name']),
            'is_uploaded_file' => is_uploaded_file($audio_file['tmp_name']),
            'permissions' => [
                'tmp_readable' => is_readable($audio_file['tmp_name']),
                'target_dir_writable' => is_writable(dirname($audio_path)),
                'target_dir_exists' => is_dir(dirname($audio_path))
            ],
            'php_errors' => error_get_last()
        ];
        
        error_log('Upload debug info: ' . json_encode($error_info));
        echo json_encode(['success' => false, 'message' => 'Failed to upload audio file. Debug: ' . json_encode($error_info)]);
        exit;
    }

    // Handle cover image if provided
    $cover_path = null;
    if ($cover_image && $cover_image['error'] === UPLOAD_ERR_OK) {
        $allowed_image_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        
        if (!in_array($cover_image['type'], $allowed_image_types)) {
            echo json_encode(['success' => false, 'message' => 'Invalid image file type']);
            exit;
        }

        if ($cover_image['size'] > 5 * 1024 * 1024) {
            echo json_encode(['success' => false, 'message' => 'Image file size must be less than 5MB']);
            exit;
        }

        $image_extension = pathinfo($cover_image['name'], PATHINFO_EXTENSION);
        $image_filename = uniqid() . '_' . time() . '.' . $image_extension;
        $cover_path = $image_dir . $image_filename;

        if (!move_uploaded_file($cover_image['tmp_name'], $cover_path)) {
            echo json_encode(['success' => false, 'message' => 'Failed to upload cover image']);
            exit;
        }
    }

    try {
        // Check if user is admin
        $stmt = $pdo->prepare("SELECT is_admin FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();

        if ($user && $user['is_admin']) {
            // Admin upload - add directly to books table
            $stmt = $pdo->prepare("
                INSERT INTO books (title, author, description, category, audio_file, cover_image) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");

            $audio_db_path = 'uploads/audio/' . $audio_filename;
            $cover_db_path = $cover_path ? 'uploads/images/' . basename($cover_path) : null;

            if ($stmt->execute([$title, $author, $description, $category, $audio_db_path, $cover_db_path])) {
                echo json_encode(['success' => true, 'message' => 'Audiobook added directly to home page!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to add audiobook']);
            }
        } else {
            // Regular user upload - add to pending_books table
            $stmt = $pdo->prepare("
                INSERT INTO pending_books (user_id, title, author, description, category, audio_file, cover_image) 
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");

            $audio_db_path = 'uploads/audio/' . $audio_filename;
            $cover_db_path = $cover_path ? 'uploads/images/' . basename($cover_path) : null;

            if ($stmt->execute([$user_id, $title, $author, $description, $category, $audio_db_path, $cover_db_path])) {
                echo json_encode(['success' => true, 'message' => 'Audiobook submitted for review! You will be notified once it\'s approved.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to save audiobook information']);
            }
        }

    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}
?> 