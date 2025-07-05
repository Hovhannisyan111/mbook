<?php
/**
 * MBook Database Installation Script
 * 
 * This script automatically sets up the complete database for the MBook audiobook application.
 * Run this script once to initialize your database with all necessary tables and sample data.
 */

// Database configuration
$host = 'localhost';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

echo "=== MBook Database Installation ===\n\n";

try {
    // Connect to MySQL server (without specifying database)
    $dsn = "mysql:host=$host;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    
    $pdo = new PDO($dsn, $user, $pass, $options);
    echo "✓ Connected to MySQL server successfully\n";
    
    // Read and execute the SQL script
    $sqlFile = 'database_setup.sql';
    if (!file_exists($sqlFile)) {
        throw new Exception("SQL file '$sqlFile' not found!");
    }
    
    $sql = file_get_contents($sqlFile);
    echo "✓ SQL script loaded successfully\n";
    
    // Split SQL into individual statements
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    echo "Executing database setup...\n";
    $totalStatements = count($statements);
    $currentStatement = 0;
    
    foreach ($statements as $statement) {
        if (!empty($statement)) {
            $currentStatement++;
            echo "Progress: $currentStatement/$totalStatements\r";
            
            try {
                $pdo->exec($statement);
            } catch (PDOException $e) {
                // Skip some expected errors (like dropping non-existent tables)
                if (strpos($e->getMessage(), "doesn't exist") === false) {
                    echo "\nWarning: " . $e->getMessage() . "\n";
                }
            }
        }
    }
    
    echo "\n✓ Database setup completed successfully!\n\n";
    
    // Verify the installation
    echo "=== Verification ===\n";
    
    // Connect to the MBook database
    $pdo = new PDO("mysql:host=$host;dbname=MBook;charset=$charset", $user, $pass, $options);
    
    // Check tables
    $tables = ['users', 'books', 'user_books', 'categories'];
    foreach ($tables as $table) {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM $table");
        $count = $stmt->fetch()['count'];
        echo "✓ Table '$table': $count records\n";
    }
    
    // Check views
    $views = ['user_book_progress', 'book_statistics'];
    foreach ($views as $view) {
        try {
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM $view");
            $count = $stmt->fetch()['count'];
            echo "✓ View '$view': $count records\n";
        } catch (Exception $e) {
            echo "⚠ View '$view': Error - " . $e->getMessage() . "\n";
        }
    }
    
    echo "\n=== Installation Complete! ===\n";
    echo "Your MBook database is now ready to use.\n\n";
    
    echo "Default login credentials:\n";
    echo "Admin: admin@mbook.com / admin123\n";
    echo "Regular users:\n";
    echo "  - john@example.com / user123\n";
    echo "  - jane@example.com / user123\n";
    echo "  - mike@example.com / user123\n\n";
    
    echo "Next steps:\n";
    echo "1. Make sure your web server (XAMPP/LAMPP) is running\n";
    echo "2. Navigate to: http://localhost/mbook/public/\n";
    echo "3. You'll be redirected to the login page\n";
    echo "4. Use any of the credentials above to log in\n\n";
    
    echo "Enjoy your audiobook application! 🎧\n";
    
} catch (Exception $e) {
    echo "❌ Installation failed: " . $e->getMessage() . "\n";
    echo "\nTroubleshooting:\n";
    echo "1. Make sure MySQL is running\n";
    echo "2. Check your database credentials in this script\n";
    echo "3. Ensure you have proper permissions\n";
    echo "4. Make sure the database_setup.sql file exists\n";
}
?> 