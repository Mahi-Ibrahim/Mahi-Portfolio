<?php
/**
 * api/contact.php
 * 
 * PURPOSE:
 * This endpoint handles incoming POST requests from the "Contact Me" form on the public homepage.
 * It validates the data on the server-side, sanitizes it, and securely inserts it into the `messages` table.
 * 
 * INPUTS:
 * POST variables: 'name', 'email', 'message'
 * 
 * OUTPUTS:
 * JSON object indicating 'status' (success/error) and a 'message' describing the outcome.
 */

// 1. Set headers to ensure the client treats the response as JSON.
header('Content-Type: application/json');

// 2. Require the database connection.
// WHY require_once? If the file is missing, the script halts immediately. This prevents us from 
// trying to execute SQL queries without a connection, which would throw errors.
require_once 'db.php';

/**
 * METHOD CHECK
 * Why do we check $_SERVER['REQUEST_METHOD']?
 * We only want to accept POST requests because we are mutating the database. GET requests 
 * should not alter state. If someone tries to navigate to this URL directly in their browser, 
 * it sends a GET request, which we must reject.
 */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // 405 Method Not Allowed
    echo json_encode(['status' => 'error', 'message' => 'INVALID REQUEST METHOD.']);
    exit;
}

// 3. Sanitize and Extract Inputs
// We use trim() to remove accidental whitespace at the beginning or end of the inputs.
// The null coalescing operator (??) ensures that if the field wasn't sent, we default to an empty string.
$name    = trim($_POST['name'] ?? '');
$email   = trim($_POST['email'] ?? '');
$message = trim($_POST['message'] ?? '');

/**
 * SERVER-SIDE VALIDATION
 * Why validate again when we already did it in JavaScript?
 * Client-side validation is purely for User Experience (UX). A malicious user can easily 
 * bypass JavaScript by disabling it or using a tool like Postman to send requests directly 
 * to this endpoint. Server-side validation is mandatory for security.
 */
if (empty($name) || empty($email) || empty($message)) {
    echo json_encode(['status' => 'error', 'message' => 'ALL FIELDS ARE REQUIRED.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['status' => 'error', 'message' => 'INVALID EMAIL FORMAT.']);
    exit;
}

/**
 * MINI-TUTORIAL: Inserting Data Safely with PDO
 * 1. We write our SQL statement using placeholders (the words starting with a colon, e.g., :name).
 * 2. We prepare the statement using $pdo->prepare(). This compiles the SQL template on the database server.
 * 3. We use bindParam() to attach our PHP variables to the SQL placeholders.
 * 
 * WHY PLACEHOLDERS?
 * If we concatenated the string directly (e.g., "... VALUES ('$name', ...)"), a user could type 
 * a malicious SQL command into the name field, and the database would execute it (SQL Injection).
 * By using placeholders, PDO sends the data separately from the SQL command. The database treats 
 * the input strictly as data, never as executable code.
 */
try {
    $sql = "INSERT INTO messages (name, email, message) VALUES (:name, :email, :message)";
    $stmt = $pdo->prepare($sql);
    
    // Bind parameters
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':message', $message);
    
    // Execute the query
    $stmt->execute();
    
    // Respond with success
    echo json_encode(['status' => 'success', 'message' => 'MESSAGE BUFFER SAVED SUCCESSFULLY.']);

} catch (PDOException $e) {
    // If the insert fails, catch the error.
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'DATABASE ERROR: UNABLE TO SAVE MESSAGE.']);
}
?>
