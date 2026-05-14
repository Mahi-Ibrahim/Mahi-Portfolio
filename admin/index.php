<?php
/**
 * admin/index.php
 * 
 * PURPOSE:
 * The protected dashboard view.
 * 
 * FEATURE D: Session Management & Protection
 * We MUST call session_start() at the very top before any HTML is sent to the browser.
 * If the session variable 'admin_logged_in' is not set or true, we immediately 
 * redirect the user back to the login page and halt script execution.
 */
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

// Require DB connection to fetch messages directly for this view
require_once '../api/db.php';

try {
    $stmt = $pdo->query("SELECT * FROM messages ORDER BY created_at DESC");
    $messages = $stmt->fetchAll();
} catch (PDOException $e) {
    $messages = [];
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Console - Dev Portfolio</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>

    <header>
        <a href="../index.php" class="logo">~/DEV_PORTFOLIO</a>
        <nav>
            <ul>
                <li><a href="../index.php" target="_blank">View Site</a></li>
                <li><span style="color: var(--accent-color);">[ ADMIN: <?php echo htmlspecialchars($_SESSION['admin_username']); ?> ]</span></li>
                <!-- 
                  Logout triggers an AJAX call to api/auth.php via admin.js 
                -->
                <li><button id="logout-btn" class="btn">Logout</button></li>
            </ul>
        </nav>
    </header>

    <main class="admin-dashboard">
        <div class="dashboard-header">
            <div>
                <span class="context-path">~/root/admin/dashboard</span>
                <h1>System Console</h1>
            </div>
        </div>

        <div class="admin-grid">
            
            <!-- Left Column: Project Management (Feature E) -->
            <div class="admin-col">
                <h2>Project Initialization</h2>
                <form id="add-project-form">
                    <div id="project-form-message" class="message" style="display: none;"></div>
                    
                    <div class="form-group">
                        <label>PROJECT_TITLE</label>
                        <input type="text" name="title" required>
                    </div>
                    <div class="form-group">
                        <label>DESCRIPTION_TEXT</label>
                        <textarea name="description" rows="3" required></textarea>
                    </div>
                    <div class="form-group">
                        <label>TECH_STACK (Comma separated)</label>
                        <input type="text" name="tech_stack" placeholder="e.g. PHP, MySQL, JS">
                    </div>
                    <div style="display: flex; gap: 1rem;">
                        <div class="form-group" style="flex: 1;">
                            <label>REPO_URL</label>
                            <input type="url" name="repo_url">
                        </div>
                        <div class="form-group" style="flex: 1;">
                            <label>LIVE_URL</label>
                            <input type="url" name="live_url">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">INITIALIZE_PROJECT</button>
                </form>

                <h2 style="margin-top: 3rem;">Active Array Data</h2>
                <!-- 
                  This table body will be populated dynamically by admin.js fetching from api/projects.php
                -->
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>TITLE</th>
                            <th>ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody id="admin-projects-list">
                        <tr><td colspan="3">Fetching data...</td></tr>
                    </tbody>
                </table>
            </div>

            <!-- Right Column: Messages Inbox -->
            <div class="admin-col">
                <span class="context-path">INBOX_STREAM</span>
                <h2>Incoming Transmissions</h2>
                
                <div class="inbox-container">
                    <?php if (count($messages) === 0): ?>
                        <p style="color: var(--text-secondary); font-family: var(--font-mono);">Buffer empty. No messages.</p>
                    <?php else: ?>
                        <?php foreach($messages as $msg): ?>
                            <div class="inquiry-card">
                                <div class="inquiry-meta">
                                    <span style="color: var(--accent-color);">ID: <?php echo htmlspecialchars($msg['id']); ?></span>
                                    <span><?php echo date('Y.m.d H:i', strtotime($msg['created_at'])); ?></span>
                                </div>
                                <div class="inquiry-email">FROM: <?php echo htmlspecialchars($msg['email']); ?> (<?php echo htmlspecialchars($msg['name']); ?>)</div>
                                <div class="inquiry-snippet">
                                    > <?php echo nl2br(htmlspecialchars($msg['message'])); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </main>

    <script src="../assets/js/admin.js"></script>
</body>
</html>
