<?php
require_once '../config.php';
requireRole('student');

$db = getDB();
$student_id = $_SESSION['student_id'];

// Get upcoming lectures
$stmt = $db->prepare("
    SELECT l.*, cu.name as course_name, cu.code as course_code,
           u.name as lecturer_name,
           a.id as attended
    FROM lectures l
    JOIN course_units cu ON l.course_unit_id = cu.id
    JOIN student_units su ON cu.id = su.course_unit_id
    LEFT JOIN lecturers lec ON cu.lecturer_id = lec.id
    LEFT JOIN users u ON lec.user_id = u.id
    LEFT JOIN attendance a ON l.id = a.lecture_id AND a.student_id = ?
    WHERE su.student_id = ? AND l.scheduled_start_time >= NOW()
    ORDER BY l.scheduled_start_time
    LIMIT 50
");
$stmt->execute([$student_id, $student_id]);
$lectures = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Timetable - AttendApp</title>
    <link rel="stylesheet" href="/<?php echo FOLDER_NAME; ?>/assets/css/dashboard.css">
</head>
<body>
    <?php include '../includes/student_nav.php'; ?>
    
    <div class="container">
        <div class="page-header">
            <h1>My Timetable</h1>
            <p>Upcoming lectures and schedule</p>
        </div>

        <?php if (empty($lectures)): ?>
            <div class="empty-state">
                <div class="empty-icon">📅</div>
                <p>No upcoming lectures scheduled</p>
            </div>
        <?php else: ?>
            <div style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 15px; overflow: hidden;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Course</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Lecturer</th>
                            <th>Status</th>
                            <th>Attendance</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($lectures as $lecture): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($lecture['course_code']); ?></strong><br>
                                <small style="color: #a0aec0;"><?php echo htmlspecialchars($lecture['course_name']); ?></small>
                            </td>
                            <td><?php echo date('M j, Y', strtotime($lecture['scheduled_start_time'])); ?></td>
                            <td><?php echo date('g:i A', strtotime($lecture['scheduled_start_time'])); ?></td>
                            <td><?php echo htmlspecialchars($lecture['lecturer_name'] ?? 'TBA'); ?></td>
                            <td><span class="status-badge status-<?php echo $lecture['status']; ?>"><?php echo ucfirst($lecture['status']); ?></span></td>
                            <td>
                                <?php if ($lecture['attended']): ?>
                                    <span class="badge badge-success">✓ Attended</span>
                                <?php else: ?>
                                    <span class="badge badge-warning">Not Attended</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>