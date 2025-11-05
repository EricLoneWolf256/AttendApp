<?php
require_once '../config.php';

// Debug - check session
if (isset($_GET['debug'])) {
    echo '<pre>';
    print_r($_SESSION);
    echo '</pre>';
    exit();
}

requireRole('admin');

$db = getDB();

// Get statistics
$stats = [
    'lecturers' => $db->query("SELECT COUNT(*) FROM lecturers")->fetchColumn(),
    'students' => $db->query("SELECT COUNT(*) FROM students")->fetchColumn(),
    'courses' => $db->query("SELECT COUNT(*) FROM course_units")->fetchColumn(),
    'today_lectures' => $db->query("SELECT COUNT(*) FROM lectures WHERE DATE(scheduled_start_time) = CURDATE()")->fetchColumn()
];

// Get today's lectures
$recentLectures = $db->query("
    SELECT l.*, cu.code, cu.name as course_name, u.name as lecturer_name
    FROM lectures l
    JOIN course_units cu ON l.course_unit_id = cu.id
    LEFT JOIN lecturers lec ON cu.lecturer_id = lec.id
    LEFT JOIN users u ON lec.user_id = u.id
    WHERE DATE(l.scheduled_start_time) = CURDATE()
    ORDER BY l.scheduled_start_time
    LIMIT 10
")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - AttendApp</title>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>assets/css/dashboard.css">
</head>
<body>
    <nav class="navbar">
        <a href="<?php echo SITE_URL; ?>admin/dashboard.php" class="navbar-brand">🎓 AttendApp</a>
        <div class="navbar-menu">
            <a href="<?php echo SITE_URL; ?>admin/dashboard.php">Dashboard</a>
            <a href="<?php echo SITE_URL; ?>admin/timetable.php">Timetable</a>
            <a href="<?php echo SITE_URL; ?>admin/lecturers.php">Lecturers</a>
            <a href="<?php echo SITE_URL; ?>admin/courses.php">Courses</a>
            <a href="<?php echo SITE_URL; ?>admin/reports.php">Reports</a>
        </div>
        <div class="user-info">
            <span><?php echo htmlspecialchars($_SESSION['name']); ?></span>
            <a href="<?php echo SITE_URL; ?>logout.php" class="btn btn-logout">Logout</a>
        </div>
    </nav>
    
    <div class="container">
        <?php if ($msg = getMessage()): ?>
        <div class="alert alert-<?php echo $msg['type']; ?>">
            <?php echo htmlspecialchars($msg['message']); ?>
        </div>
        <?php endif; ?>

        <div class="page-header">
            <div>
                <h1>Admin Dashboard</h1>
                <p>Manage your institution's attendance system</p>
            </div>
        </div>

        <!-- Statistics Grid -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon blue">👨‍🏫</div>
                <div>
                    <div class="stat-value"><?php echo $stats['lecturers']; ?></div>
                    <div class="stat-label">Total Lecturers</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon green">🎓</div>
                <div>
                    <div class="stat-value"><?php echo $stats['students']; ?></div>
                    <div class="stat-label">Total Students</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon purple">📚</div>
                <div>
                    <div class="stat-value"><?php echo $stats['courses']; ?></div>
                    <div class="stat-label">Course Units</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon orange">📅</div>
                <div>
                    <div class="stat-value"><?php echo $stats['today_lectures']; ?></div>
                    <div class="stat-label">Today's Lectures</div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <h2 style="margin: 2rem 0 1rem 0;">Quick Actions</h2>
        <div class="quick-actions">
            <a href="<?php echo SITE_URL; ?>admin/timetable.php" class="action-card">
                <div class="action-icon">📅</div>
                <h3>Manage Timetable</h3>
                <p>Set up weekly schedules and lecture times</p>
            </a>

            <a href="<?php echo SITE_URL; ?>admin/lecturers.php" class="action-card">
                <div class="action-icon">👥</div>
                <h3>Manage Lecturers</h3>
                <p>Add and assign lecturers to courses</p>
            </a>

            <a href="<?php echo SITE_URL; ?>admin/courses.php" class="action-card">
                <div class="action-icon">📚</div>
                <h3>Manage Courses</h3>
                <p>Create and edit course units</p>
            </a>

            <a href="<?php echo SITE_URL; ?>admin/reports.php" class="action-card">
                <div class="action-icon">📊</div>
                <h3>View Reports</h3>
                <p>Attendance analytics and insights</p>
            </a>
        </div>

        <?php if (!empty($recentLectures)): ?>
        <h2 style="margin: 2rem 0 1rem 0;">Today's Lectures</h2>
        <div class="lecture-list">
            <?php foreach ($recentLectures as $lecture): ?>
            <div class="lecture-card">
                <div class="lecture-info">
                    <h3><?php echo htmlspecialchars($lecture['code'] . ' - ' . $lecture['course_name']); ?></h3>
                    <p>🕐 <?php echo date('g:i A', strtotime($lecture['scheduled_start_time'])) . ' - ' . date('g:i A', strtotime($lecture['scheduled_end_time'])); ?></p>
                    <p>👨‍🏫 <?php echo htmlspecialchars($lecture['lecturer_name']); ?></p>
                </div>
                <span class="status-badge status-<?php echo $lecture['status']; ?>">
                    <?php echo ucfirst($lecture['status']); ?>
                </span>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>