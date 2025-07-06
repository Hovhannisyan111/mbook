<?php
session_start();
include '../backend/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'] ?? '';

function getBooksByStatus($pdo, $user_id, $status) {
    $stmt = $pdo->prepare("
        SELECT b.title, b.author, b.cover_image, b.audio_file, ub.progress, ub.last_position_seconds
        FROM user_books ub
        JOIN books b ON ub.book_id = b.id
        WHERE ub.user_id = :user_id AND ub.status = :status
        ORDER BY ub.updated_at DESC
    ");
    $stmt->execute(['user_id' => $user_id, 'status' => $status]);
    return $stmt->fetchAll();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>MBOOK - Profile</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/profile.css">
    <script src="js/main.js" defer></script>
</head>
<body>
    <div class="profile-page-container">
        <aside class="sidebar">
            <div class="sidebar-logo">
                <img src="../images/logo.png" alt="MBOOK Logo">
            </div>
            <nav class="sidebar-menu">
                <a href="home.html" class="sidebar-link">Home</a>
                <a href="profile.php" class="sidebar-link active">My Library</a>
                <a href="upload.html" class="sidebar-link">Upload Book</a>
                <!-- <a href="#" class="sidebar-link">News</a> -->
            </nav>
        </aside>
        <main class="profile-main">
            <header class="profile-header">
                <!-- <img src="../images/logo.png" alt="MBOOK Logo" class="logo-img"> -->
                <div class="profile-username" id="profileUsername">Username</div>
                <div class="header-buttons">
                    <button class="settings-btn" title="Settings">
                        <!-- <span class="settings-icon">&#9881;</span> -->
                        <img src="../images/settings_icon.png" alt="profile_icon">

                    </button>
                    <button class="logout-btn" title="Logout">
                        <img src="../images/logout_icon.png" alt="profile_icon">
                    </button>
                </div>
            </header>
            <section class="bookshelf-section">
                <div class="shelf-header">
                    <h2>Saved Books</h2>
                </div>
                <div class="bookshelf">
                    <div class="shelf-books">
                        <?php
                            $saved = getBooksByStatus($pdo, $user_id, 'saved');
                            foreach ($saved as $row) {
                                $title = addslashes($row['title']);
                                $author = addslashes($row['author']);
                                $cover = $row['cover_image'] ?: 'images/default_cover.jpg';
                                $audio = $row['audio_file'] ?? ''; // если используешь
                                $coverPath = '../' . ltrim($cover, '/'); // подгоняем путь

                                echo '<div class="shelf-book">';
                                echo '<img src="' . $coverPath . '" alt="Book Cover" onclick="playBook(\'' . $title . '\', \'' . $author . '\', \'' . $coverPath . '\', \'' . $audio . '\')">';
                                echo '</div>';
                            }
                        ?>
                    </div>
                    <div class="shelf-bar"></div>
                </div>
            </section>
            <section class="bookshelf-section">
                <div class="shelf-header">
                    <h2>Currently Reading</h2>
                </div>
                <div class="bookshelf">
                    <div class="shelf-books">
                    <?php
                        $reading = getBooksByStatus($pdo, $user_id, 'reading');
                        foreach ($reading as $row) {
                            $title = addslashes($row['title']);
                            $author = addslashes($row['author']);
                            $cover = $row['cover_image'] ?: 'images/default_cover.jpg';
                            $audio = $row['audio_file'] ?? ''; // если используешь
                            $coverPath = '../' . ltrim($cover, '/'); // подгоняем путь

                            echo '<div class="shelf-book">';
                            echo '<img src="' . $coverPath . '" alt="Book Cover" onclick="playBook(\'' . $title . '\', \'' . $author . '\', \'' . $coverPath . '\', \'' . $audio . '\')">';
                            echo '</div>';
                        }
                    ?>
                    </div>
                    <div class="shelf-bar"></div>
                </div>
            </section>
            <section class="bookshelf-section">
                <div class="shelf-header">
                    <h2>Finished Books</h2>
                </div>
                <div class="bookshelf">
                    <div class="shelf-books">
                    <?php
                        $finished = getBooksByStatus($pdo, $user_id, 'finished');
                        foreach ($finished as $row) {
                            $title = addslashes($row['title']);
                            $author = addslashes($row['author']);
                            $cover = $row['cover_image'] ?: 'images/default_cover.jpg';
                            $audio = $row['audio_file'] ?? ''; // если используешь
                            $coverPath = '../' . ltrim($cover, '/'); // подгоняем путь

                            echo '<div class="shelf-book">';
                            echo '<img src="' . $coverPath . '" alt="Book Cover" onclick="playBook(\'' . $title . '\', \'' . $author . '\', \'' . $coverPath . '\', \'' . $audio . '\')">';
                            echo '</div>';
                        }
                    ?>
                    </div>
                    <div class="shelf-bar"></div>
                </div>
            </section>
        </main>
    </div>
    <script>
        function playBook(title, author, cover, audioFile, bookId) {
    // Save book info to sessionStorage
    sessionStorage.setItem('currentBook', JSON.stringify({
        title: title,
        author: author,
        cover: cover,
        audioFile: audioFile,
        bookId: bookId
    }));

    // Save book with status = "saved"
    fetch('../backend/update_book_status.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `book_id=${encodeURIComponent(bookId)}&status=saved`
    }).finally(() => {
        // Redirect to book.html after save
        window.location.href = 'book.html';
    });
}

    </script>

</body>
</html> 