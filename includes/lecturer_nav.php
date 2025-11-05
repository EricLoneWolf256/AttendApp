<?php
// Make sure config is loaded
if (!defined('FOLDER_NAME')) {
    require_once dirname(__DIR__) . '/config.php';
}
?>
<nav class="navbar">
    <a href="/<?php echo FOLDER_NAME; ?>/lecturer/dashboard.php" class="navbar-brand">🎓 AttendApp</a>
    <div class="navbar-menu">
        <a href="/<?php echo FOLDER_NAME; ?>/lecturer/dashboard.php">Dashboard</a>
        <a href="/<?php echo FOLDER_NAME; ?>/lecturer/lectures.php">My Lectures</a>
        <a href="/<?php echo FOLDER_NAME; ?>/lecturer/reports.php">Reports</a>
    </div>
    <div class="user-info">
        <span><?php echo htmlspecialchars($_SESSION['name'] ?? 'Lecturer'); ?></span>
        <a href="/<?php echo FOLDER_NAME; ?>/logout.php" class="btn btn-logout">Logout</a>
    </div>
</nav>