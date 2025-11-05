<?php
// Make sure config is loaded
if (!defined('FOLDER_NAME')) {
    require_once dirname(__DIR__) . '/config.php';
}
?>
<nav class="navbar">
    <a href="/<?php echo FOLDER_NAME; ?>/student/dashboard.php" class="navbar-brand">🎓 AttendApp</a>
    <div class="navbar-menu">
        <a href="/<?php echo FOLDER_NAME; ?>/student/dashboard.php">Dashboard</a>
        <a href="/<?php echo FOLDER_NAME; ?>/student/timetable.php">Timetable</a>
        <a href="/<?php echo FOLDER_NAME; ?>/student/feedback.php">My Feedback</a>
    </div>
    <div class="user-info">
        <span><?php echo htmlspecialchars($_SESSION['name'] ?? 'Student'); ?></span>
        <a href="/<?php echo FOLDER_NAME; ?>/logout.php" class="btn btn-logout">Logout</a>
    </div>
</nav>
