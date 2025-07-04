<?php
$host = 'localhost';
$db   = 'MBook'; 
$user = 'root'; 
$pass = '';
$charset = 'utf8mb4';

echo "Testing database connection...\n";
echo "Host: $host\n";
echo "Database: $db\n";
echo "User: $user\n";

try {
    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    $pdo = new PDO($dsn, $user, $pass, $options);
    echo "✅ Database connection successful!\n";
    
    // Test if we can query the database
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll();
    echo "Current tables in database:\n";
    foreach ($tables as $table) {
        // Handle both associative and indexed arrays
        $tableName = is_array($table) ? (isset($table[0]) ? $table[0] : array_values($table)[0]) : $table;
        echo "- " . $tableName . "\n";
    }
    
} catch (\PDOException $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "\n";
    echo "Error code: " . $e->getCode() . "\n";
}
?> 