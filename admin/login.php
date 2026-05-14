<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Dev Portfolio</title>
    <!-- Use main styles for layout consistency, but load admin-specific JS later -->
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body style="display: flex; justify-content: center; align-items: center;">

    <main style="max-width: 400px; padding: 2rem;">
        <span class="context-path">~/system/auth</span>
        <h2>Authentication Required</h2>
        
        <div id="login-message" class="message" style="display: none;"></div>

        <!-- 
          Form submission is hijacked by admin.js to send data asynchronously 
          to api/auth.php. No full page reload.
        -->
        <form id="admin-login-form" style="padding: 2rem 0; border: none; background: transparent;">
            <div class="form-group">
                <label for="username">USERNAME</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">PASSWORD</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%;">AUTHENTICATE</button>
        </form>
        
        <div style="text-align: center; margin-top: 2rem;">
            <a href="../index.php" style="color: var(--text-secondary); font-family: var(--font-mono); font-size: 0.8rem; text-decoration: none;">< RETURN_TO_ROOT</a>
        </div>
    </main>

    <script src="../assets/js/admin.js"></script>
</body>
</html>
