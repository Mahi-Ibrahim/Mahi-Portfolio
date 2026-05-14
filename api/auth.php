<?php
/**
 * api/auth.php
 * 
 * PURPOSE:
 * Manages the admin authentication state (Login and Logout).
 * Verifies credentials against the `admins` table using password hashes.
 */

session_start();
header('Content-Type: application/json');
require_once 'db.php';

/**
 * Determine action based on HTTP method or a specific POST parameter.
 * Since logging in and logging out might both use POST, we can differentiate 
 * by checking if the request is an explicit logout action.
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Check if it's a logout request
    if (isset($_POST['action']) && $_POST['action'] === 'logout') {
        handleLogout();
    } else {
        handleLogin($pdo);
    }
    
} else {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'METHOD NOT ALLOWED.']);
}

/**
 * Handle Login Validation
 * 
 * @param PDO $pdo Database connection
 */
function handleLogin($pdo) {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($username) || empty($password)) {
        echo json_encode(['status' => 'error', 'message' => 'CREDENTIALS REQUIRED.']);
        return;
    }

    try {
        // Find the user by username
        $stmt = $pdo->prepare("SELECT id, username, password_hash FROM admins WHERE username = :user LIMIT 1");
        $stmt->bindParam(':user', $username);
        $stmt->execute();
        
        $admin = $stmt->fetch();

        /**
         * WHY password_verify()?
         * We NEVER store plain text passwords in the database. If the database is compromised,
         * all passwords would be exposed. We store a cryptographic hash. 
         * password_verify() takes the plain text password from the form and securely compares 
         * it against the stored hash.
         */
        if ($admin && password_verify($password, $admin['password_hash'])) {
            
            // Authentication successful. Initialize secure session variables.
            // Feature D: Session Management
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            
            echo json_encode([
                'status' => 'success', 
                'message' => 'AUTHENTICATION GRANTED.',
                'redirect' => 'index.php' // Tell the JS where to navigate next
            ]);
        } else {
            // Use a generic error message. Don't reveal if the username exists or just the password was wrong.
            echo json_encode(['status' => 'error', 'message' => 'ACCESS DENIED: INVALID CREDENTIALS.']);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'SERVER ERROR.']);
    }
}

/**
 * Handle Logout
 * Destroys the session and clears session cookies.
 */
function handleLogout() {
    // Unset all session variables
    $_SESSION = array();

    // Destroy the session cookie in the browser
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }

    // Destroy the session file on the server
    session_destroy();
    
    echo json_encode([
        'status' => 'success', 
        'redirect' => '../index.php'
    ]);
}
?>
