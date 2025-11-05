<?php
require_once '../config.php';
requireRole('student');

$db = getDB();
$student_id = $_SESSION['student_id'];

// Get all feedback submitted by student
$stmt = $db->prepare("
    SELECT f.*, l.scheduled_start_time, cu.name as course_name, cu.code as course_code
    FROM feedback f
    JOIN lectures l ON f.lecture_id = l.id
    JOIN course_units cu ON l.course_unit_id = cu.id
    WHERE f.student_id = ?
    ORDER BY f.timestamp DESC
");
$stmt->execute([$student_id]);
$feedbacks = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Feedback - AttendApp</title>
    <link rel="stylesheet" href="/<?php echo FOLDER_NAME; ?>/assets/css/dashboard.css">
</head>
<body>
    <?php include '../includes/student_nav.php'; ?>
    
    <div class="container">
        <div class="page-header">
            <h1>My Feedback</h1>
            <p>View all your submitted feedback</p>
        </div>

        <?php if (empty($feedbacks)): ?>
            <div class="empty-state">
                <div class="empty-icon">💬</div>
                <p>No feedback submitted yet</p>
            </div>
        <?php else: ?>
            <div class="lecture-list">
                <?php foreach ($feedbacks as $feedback): ?>
                <div class="lecture-card">
                    <div class="lecture-info">
                        <h3><?php echo htmlspecialchars($feedback['course_code'] . ' - ' . $feedback['course_name']); ?></h3>
                        <p style="color: #a0aec0; font-size: 0.875rem; margin: 0.5rem 0;">
                            📅 <?php echo date('F j, Y g:i A', strtotime($feedback['timestamp'])); ?>
                        </p>
                        <div style="background: rgba(255, 255, 255, 0.03); border-left: 3px solid #667eea; padding: 1rem; margin-top: 1rem; border-radius: 5px;">
                            <p style="margin: 0;"><?php echo htmlspecialchars($feedback['comment']); ?></p>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>