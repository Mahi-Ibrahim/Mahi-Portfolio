<?php
/**
 * api/projects.php
 * 
 * PURPOSE:
 * This endpoint serves as a RESTful API for the `projects` table.
 * - GET: Fetches projects (publicly available).
 * - POST: Adds a new project (requires admin session).
 * - DELETE: Removes a project (requires admin session).
 * 
 * INPUTS/OUTPUTS depend on the HTTP method utilized. Returns JSON.
 */

// We must start the session to verify admin credentials for POST/DELETE actions.
session_start();

header('Content-Type: application/json');
require_once 'db.php';

$method = $_SERVER['REQUEST_METHOD'];

/**
 * Helper function to check if an admin is currently logged in.
 * Why? We want anyone to view projects (GET), but only the admin can modify them (POST, DELETE).
 */
function verifyAdminAuth() {
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        http_response_code(403); // 403 Forbidden
        echo json_encode(['status' => 'error', 'message' => 'UNAUTHORIZED ACCESS.']);
        exit;
    }
}

// Route the request based on the HTTP verb
switch ($method) {
    case 'GET':
        handleGetProjects($pdo);
        break;
    case 'POST':
        verifyAdminAuth();
        handleAddProject($pdo);
        break;
    case 'DELETE':
        verifyAdminAuth();
        handleDeleteProject($pdo);
        break;
    default:
        http_response_code(405);
        echo json_encode(['status' => 'error', 'message' => 'METHOD NOT ALLOWED.']);
        break;
}

/**
 * Handle GET request: Fetch all active projects
 * 
 * @param PDO $pdo The database connection object
 */
function handleGetProjects($pdo) {
    try {
        // Query projects, ordering by newest first
        $stmt = $pdo->query("SELECT id, title, description, tech_stack, repo_url, live_url, created_at FROM projects ORDER BY created_at DESC");
        // fetchAll() gets all rows as an associative array
        $projects = $stmt->fetchAll();
        
        // Return as JSON. The JavaScript Fetch API will parse this automatically.
        echo json_encode($projects);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'FAILED TO FETCH PROJECTS.']);
    }
}

/**
 * Handle POST request: Add a new project
 * Note: Data will come via a FormData object from JavaScript, which populates $_POST.
 * 
 * @param PDO $pdo The database connection object
 */
function handleAddProject($pdo) {
    $title       = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $tech_stack  = trim($_POST['tech_stack'] ?? '');
    $repo_url    = trim($_POST['repo_url'] ?? '');
    $live_url    = trim($_POST['live_url'] ?? '');

    // Basic Validation
    if (empty($title) || empty($description)) {
        echo json_encode(['status' => 'error', 'message' => 'TITLE AND DESCRIPTION REQUIRED.']);
        return;
    }

    try {
        $sql = "INSERT INTO projects (title, description, tech_stack, repo_url, live_url) 
                VALUES (:title, :desc, :tech, :repo, :live)";
        $stmt = $pdo->prepare($sql);
        
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':desc', $description);
        $stmt->bindParam(':tech', $tech_stack);
        $stmt->bindParam(':repo', $repo_url);
        $stmt->bindParam(':live', $live_url);
        
        $stmt->execute();
        
        echo json_encode(['status' => 'success', 'message' => 'PROJECT INITIALIZED.']);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'DATABASE ERROR.']);
    }
}

/**
 * Handle DELETE request: Remove a project by ID
 * Note: DELETE requests often send data in the URL query string (api/projects.php?id=5) 
 * or as a JSON payload. Here we check the query string via $_GET.
 * 
 * @param PDO $pdo The database connection object
 */
function handleDeleteProject($pdo) {
    // Cast ID to integer for safety. If missing, it becomes 0.
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

    if ($id <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'INVALID PROJECT ID.']);
        return;
    }

    try {
        $stmt = $pdo->prepare("DELETE FROM projects WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        // Row count checks if a record was actually deleted
        if ($stmt->rowCount() > 0) {
            echo json_encode(['status' => 'success', 'message' => 'PROJECT DELETED.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'PROJECT NOT FOUND.']);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'DATABASE ERROR.']);
    }
}
?>
