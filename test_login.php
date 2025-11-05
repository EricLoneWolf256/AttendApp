<?php
require_once 'config.php';

// Test database connection
try {
    $db = getDB();
    echo "<h3 style='color: green;'>✓ Database Connected Successfully</h3>";
} catch (Exception $e) {
    echo "<h3 style='color: red;'>✗ Database Connection Failed: " . $e->getMessage() . "</h3>";
    exit();
}

// Test users
$users = $db->query("SELECT id, email, role, name FROM users")->fetchAll();

echo "<h3>Users in Database:</h3>";
echo "<table border='1' cellpadding='10'>";
echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th></tr>";
foreach ($users as $user) {
    echo "<tr>";
    echo "<td>{$user['id']}</td>";
    echo "<td>{$user['name']}</td>";
    echo "<td>{$user['email']}</td>";
    echo "<td>{$user['role']}</td>";
    echo "</tr>";
}
echo "</table>";

// Test login form
echo "<h3>Test Login Form:</h3>";
?>
<!DOCTYPE html>
<html>
<head>
    <title>Test Login</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f5f5f5; }
        .form-group { margin: 15px 0; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input, select { padding: 8px; width: 300px; }
        button { padding: 10px 20px; background: #007bff; color: white; border: none; cursor: pointer; }
        .info { background: #e3f2fd; padding: 15px; margin: 20px 0; border-left: 4px solid #2196F3; }
    </style>
</head>
<body>
    <div class="info">
        <strong>Site URL:</strong> <?php echo SITE_URL; ?><br>
        <strong>Base Path:</strong> <?php echo BASE_PATH; ?>
    </div>

    <form action="auth.php" method="POST">
        <input type="hidden" name="action" value="login">
        
        <div class="form-group">
            <label>Email:</label>
            <input type="email" name="email" value="admin@attendapp.com" required>
        </div>

        <div class="form-group">
            <label>Password:</label>
            <input type="password" name="password" value="admin123" required>
        </div>

        <div class="form-group">
            <label>Role:</label>
            <select name="role" required>
                <option value="admin">Admin</option>
                <option value="lecturer">Lecturer</option>
                <option value="student">Student</option>
            </select>
        </div>

        <button type="submit">Test Login</button>
    </form>

    <div class="info" style="margin-top: 30px;">
        <strong>Default Accounts:</strong><br>
        Admin: admin@attendapp.com / admin123<br>
        Lecturer: lecturer@test.com / lecturer123
    </div>
</body>
</html>
