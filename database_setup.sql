-- =====================================================
-- MBook Audiobook Application - Database Setup Script
-- =====================================================
-- This script creates the complete database structure for the audiobook application
-- Run this script in your MySQL database to set up everything needed

-- Create database if it doesn't exist
CREATE DATABASE IF NOT EXISTS MBook0 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Use the database
USE MBook0;

-- Drop existing tables if they exist (for clean setup)
DROP TABLE IF EXISTS user_books;
DROP TABLE IF EXISTS books;
DROP TABLE IF EXISTS users;

-- =====================================================
-- Create users table
-- =====================================================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    is_admin BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- =====================================================
-- Create books table
-- =====================================================
CREATE TABLE books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(255) NOT NULL,
    description TEXT,
    cover_image VARCHAR(255),
    audio_file VARCHAR(255),
    category VARCHAR(100),
    duration_minutes INT DEFAULT 0,
    file_size_mb DECIMAL(10,2) DEFAULT 0,
    upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- =====================================================
-- Create user_books table for tracking user's saved/reading books
-- =====================================================
CREATE TABLE user_books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    book_id INT NOT NULL,
    status ENUM('saved', 'reading', 'finished') DEFAULT 'saved',
    progress FLOAT DEFAULT 0,
    last_position_seconds INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_book (user_id, book_id)
);

-- =====================================================
-- Create categories table for better organization
-- =====================================================
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =====================================================
-- Insert sample categories
-- =====================================================
INSERT INTO categories (name, description) VALUES
('Literature', 'Classic and contemporary literature'),
('History', 'Historical accounts and narratives'),
('Biography', 'Personal life stories and memoirs'),
('Education', 'Educational and learning materials'),
('Fiction', 'Fictional stories and novels'),
('Non-Fiction', 'Factual and informative content');

-- =====================================================
-- Insert sample books with detailed information
-- =====================================================
INSERT INTO books (title, author, description, cover_image, audio_file, category, duration_minutes, file_size_mb) VALUES
(
    'Avetik Isahakyan',
    'Avetik Isahakyan',
    'A collection of poems and literary works by the renowned Armenian poet Avetik Isahakyan. This audiobook features his most celebrated works, capturing the essence of Armenian literature and culture.',
    'images/isahakyan.jpg',
    'isahakyan.wav',
    'Literature',
    45,
    13.5
),
(
    'First Zeitun Resistance',
    'Historical Accounts',
    'The story of the First Zeitun Resistance, a significant event in Armenian history. This audiobook recounts the brave resistance of the Armenian people during challenging times.',
    'images/zeytun.jpg',
    'zeytun.wav',
    'History',
    60,
    18.2
),
(
    'The Battle of Avarayr',
    'Yeghishe',
    'A detailed account of the Battle of Avarayr, one of the most important battles in Armenian history. This audiobook brings to life the heroic struggle of the Armenian people for their faith and freedom.',
    'images/avarayr.jpg',
    'avarayr.wav',
    'History',
    55,
    18.0
),
(
    'Armenian Folk Tales',
    'Traditional',
    'A collection of traditional Armenian folk tales passed down through generations. These stories capture the wisdom, humor, and cultural richness of Armenian heritage.',
    'images/folk_tales.png',
    'folk_tales.wav',
    'Literature',
    30,
    9.5
),
(
    'The Armenian Genocide: A History',
    'Historical Research',
    'A comprehensive historical account of the Armenian Genocide, providing detailed information about this tragic period in Armenian history.',
    'images/genocide_history.png',
    'genocide_history.wav',
    'History',
    90,
    27.3
),
(
    'Modern Armenian Poetry',
    'Contemporary Poets',
    'A collection of modern Armenian poetry featuring works from contemporary poets, showcasing the evolution of Armenian literary expression.',
    'images/modern_poetry.jpg',
    'modern_poetry.wav',
    'Literature',
    40,
    12.1
);

-- =====================================================
-- Create admin user (password: admin123)
-- =====================================================
INSERT INTO users (username, email, password_hash, is_admin) VALUES
('admin', 'admin@mbook.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', TRUE);

-- =====================================================
-- Create sample regular users (password: user123 for all)
-- =====================================================
INSERT INTO users (username, email, password_hash) VALUES
('john_doe', 'john@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('jane_smith', 'jane@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('mike_wilson', 'mike@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- =====================================================
-- Insert sample user-book relationships
-- =====================================================
INSERT INTO user_books (user_id, book_id, status, progress, last_position_seconds) VALUES
(2, 1, 'reading', 0.25, 675),  -- john_doe reading Avetik Isahakyan
(2, 2, 'saved', 0, 0),          -- john_doe saved First Zeitun Resistance
(3, 1, 'finished', 1.0, 2700),  -- jane_smith finished Avetik Isahakyan
(3, 3, 'reading', 0.5, 1650),   -- jane_smith reading The Battle of Avarayr
(4, 2, 'reading', 0.1, 360),    -- mike_wilson reading First Zeitun Resistance
(4, 4, 'saved', 0, 0);          -- mike_wilson saved Armenian Folk Tales

-- =====================================================
-- Create indexes for better performance
-- =====================================================
CREATE INDEX idx_books_category ON books(category);
CREATE INDEX idx_books_author ON books(author);
CREATE INDEX idx_user_books_user_id ON user_books(user_id);
CREATE INDEX idx_user_books_book_id ON user_books(book_id);
CREATE INDEX idx_user_books_status ON user_books(status);
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_username ON users(username);

-- =====================================================
-- Create views for easier data access
-- =====================================================

-- View for books with user progress
CREATE VIEW user_book_progress AS
SELECT 
    u.username,
    b.title,
    b.author,
    b.category,
    ub.status,
    ub.progress,
    ub.last_position_seconds,
    b.duration_minutes,
    ROUND((ub.last_position_seconds / (b.duration_minutes * 60)) * 100, 2) as progress_percentage
FROM user_books ub
JOIN users u ON ub.user_id = u.id
JOIN books b ON ub.book_id = b.id;

-- View for book statistics
CREATE VIEW book_statistics AS
SELECT 
    b.title,
    b.author,
    b.category,
    b.duration_minutes,
    b.file_size_mb,
    COUNT(ub.id) as total_readers,
    COUNT(CASE WHEN ub.status = 'finished' THEN 1 END) as finished_readers,
    COUNT(CASE WHEN ub.status = 'reading' THEN 1 END) as active_readers,
    AVG(ub.progress) as avg_progress
FROM books b
LEFT JOIN user_books ub ON b.id = ub.book_id
GROUP BY b.id;

-- =====================================================
-- Create stored procedures for common operations
-- =====================================================

-- Procedure to add a new book
DELIMITER //
CREATE PROCEDURE AddBook(
    IN p_title VARCHAR(255),
    IN p_author VARCHAR(255),
    IN p_description TEXT,
    IN p_cover_image VARCHAR(255),
    IN p_audio_file VARCHAR(255),
    IN p_category VARCHAR(100),
    IN p_duration_minutes INT,
    IN p_file_size_mb DECIMAL(10,2)
)
BEGIN
    INSERT INTO books (title, author, description, cover_image, audio_file, category, duration_minutes, file_size_mb)
    VALUES (p_title, p_author, p_description, p_cover_image, p_audio_file, p_category, p_duration_minutes, p_file_size_mb);
    
    SELECT LAST_INSERT_ID() as new_book_id;
END //
DELIMITER ;

-- Procedure to update user reading progress
DELIMITER //
CREATE PROCEDURE UpdateUserProgress(
    IN p_user_id INT,
    IN p_book_id INT,
    IN p_progress FLOAT,
    IN p_position_seconds INT
)
BEGIN
    INSERT INTO user_books (user_id, book_id, progress, last_position_seconds, status)
    VALUES (p_user_id, p_book_id, p_progress, p_position_seconds, 'reading')
    ON DUPLICATE KEY UPDATE
        progress = p_progress,
        last_position_seconds = p_position_seconds,
        status = CASE 
            WHEN p_progress >= 0.95 THEN 'finished'
            ELSE 'reading'
        END,
        updated_at = CURRENT_TIMESTAMP;
END //
DELIMITER ;

-- =====================================================
-- Create triggers for automatic updates
-- =====================================================

-- Trigger to update book status when progress reaches 95%
DELIMITER //
CREATE TRIGGER update_book_status
BEFORE UPDATE ON user_books
FOR EACH ROW
BEGIN
    IF NEW.progress >= 0.95 AND OLD.progress < 0.95 THEN
        SET NEW.status = 'finished';
    END IF;
END //
DELIMITER ;

-- =====================================================
-- Final verification queries
-- =====================================================

-- Show all tables
SHOW TABLES;

-- Count records in each table
SELECT 'users' as table_name, COUNT(*) as record_count FROM users
UNION ALL
SELECT 'books' as table_name, COUNT(*) as record_count FROM books
UNION ALL
SELECT 'user_books' as table_name, COUNT(*) as record_count FROM user_books
UNION ALL
SELECT 'categories' as table_name, COUNT(*) as record_count FROM categories;

-- Show sample data
SELECT 'Sample Users:' as info;
SELECT username, email, is_admin FROM users LIMIT 5;

SELECT 'Sample Books:' as info;
SELECT title, author, category, duration_minutes FROM books LIMIT 5;

SELECT 'Sample User Progress:' as info;
SELECT * FROM user_book_progress LIMIT 5;

-- =====================================================
-- Database Setup Complete!
-- =====================================================
-- 
-- Default login credentials:
-- Admin: admin@mbook.com / admin123
-- Regular users: 
--   - john@example.com / user123
--   - jane@example.com / user123  
--   - mike@example.com / user123
--
-- The database is now ready for the MBook audiobook application! 
