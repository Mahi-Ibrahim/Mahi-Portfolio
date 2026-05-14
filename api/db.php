<?php
/**
 * api/db.php
 * 
 * PURPOSE:
 * This file establishes a secure connection to the MySQL database using PHP Data Objects (PDO).
 * It is meant to be required by other API endpoints whenever database interaction is necessary.
 * 
 * WHY PDO?
 * We use PDO instead of mysqli because PDO supports prepared statements inherently, which is the 
 * standard method to prevent SQL Injection attacks. It also allows us to switch databases easily 
 * in the future if needed, and provides robust error handling capabilities.
 */

// Database credentials
$host = 'localhost';
$dbname = 'portfolio_db';
$username = 'root'; // Default XAMPP/WAMP username
$password = ''; // Default XAMPP/WAMP password is empty

/**
 * MINI-TUTORIAL: Connecting with PDO
 * 1. We wrap the connection attempt in a try...catch block. This is crucial because if the connection fails, 
 *    PDO throws an exception. If we don't catch it, the script crashes and might expose sensitive 
 *    server information in the error message.
 * 2. The Data Source Name (DSN) string "mysql:host=$host;dbname=$dbname;charset=utf8" tells PDO which 
 *    driver to use (mysql), where the server is, the database name, and importantly, the character set (utf8).
 *    Setting charset=utf8 prevents issues with special characters and certain types of SQL injection.
 * 3. We set ATTR_ERRMODE to ERRMODE_EXCEPTION. Why? By default, PDO might fail silently or just trigger a PHP warning. 
 *    We want it to throw an explicit PDOException so our catch block can handle it gracefully.
 * 4. We set ATTR_DEFAULT_FETCH_MODE to FETCH_ASSOC. Why? When we fetch data, we usually want it as an 
 *    associative array (e.g., $row['title']) rather than an object or a numerically indexed array. 
 *    Setting it here saves us from defining it in every single fetch call later.
 */

try {
    // Attempt to construct the PDO instance
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    
    // Configure PDO attributes for error handling and fetching behavior
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    // NOTE: We do NOT echo anything here. This file will be included in API endpoints that return JSON.
    // Echoing text here would corrupt the JSON response.
    
} catch (PDOException $e) {
    // If connection fails, execution drops to this catch block.
    // We send a 500 Internal Server Error status code because this is a critical server failure.
    http_response_code(500);
    
    // We return a JSON-encoded error message. 
    // In a strict production environment, you might log $e->getMessage() to a file 
    // and just send a generic "Database Error" to the client to avoid leaking internals.
    echo json_encode([
        'status' => 'error',
        'message' => 'Database connection failed: ' . $e->getMessage()
    ]);
    
    // We terminate the script because nothing else can proceed without a database connection.
    exit;
}
?>
