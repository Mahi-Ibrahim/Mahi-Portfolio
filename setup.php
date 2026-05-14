<?php
/**
 * setup.php
 * 
 * UTILITY SCRIPT: Run this file ONCE in your browser to initialize the database schema.
 * It connects to MySQL without selecting a database first, creates `portfolio_db`, 
 * and then generates the necessary tables based on the MASTER SPECIFICATION.
 */

$host = 'localhost';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;port=3307;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 1. Create Database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS portfolio_db");
    $pdo->exec("USE portfolio_db");
    echo "Database initialized.<br>";

    // 2. Create `projects` table
    $sql_projects = "CREATE TABLE IF NOT EXISTS projects (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(150) NOT NULL,
        description TEXT NOT NULL,
        tech_stack VARCHAR(255) NULL,
        repo_url VARCHAR(255) NULL,
        live_url VARCHAR(255) NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($sql_projects);
    echo "Table 'projects' initialized.<br>";

    // 3. Create `messages` table
    $sql_messages = "CREATE TABLE IF NOT EXISTS messages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL,
        message TEXT NOT NULL,
        status ENUM('unread', 'read') DEFAULT 'unread',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($sql_messages);
    echo "Table 'messages' initialized.<br>";

    // 4. Create `admins` table
    $sql_admins = "CREATE TABLE IF NOT EXISTS admins (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password_hash VARCHAR(255) NOT NULL
    )";
    $pdo->exec($sql_admins);
    echo "Table 'admins' initialized.<br>";

    // 5. Insert Default Admin if not exists
    $stmt = $pdo->query("SELECT COUNT(*) FROM admins WHERE username = 'admin'");
    if ($stmt->fetchColumn() == 0) {
        $hash = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO admins (username, password_hash) VALUES ('admin', :hash)");
        $stmt->execute([':hash' => $hash]);
        echo "Default admin generated (User: admin / Pass: admin123).<br>";
    }

    echo "<h3>System setup complete. <a href='index.php'>Go to Index</a></h3>";

} catch (PDOException $e) {
    die("Setup failed: " . $e->getMessage());
}
?>
