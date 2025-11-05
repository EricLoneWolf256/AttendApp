<?php
require_once '../config.php';
requireRole('student');

$db = getDB();
$student_id = $_SESSION['student_id'];

$stmt = $db->prepare("
    SELECT l.*, cu.name as course_name, cu.code as course_code,
           a.id as attended
    FROM lectures l
    JOIN course_units cu ON l.course_unit_id = cu.id
    JOIN student_units su ON cu.id = su.course_unit_id
    LEFT JOIN attendance a ON l.id = a.lecture_id AND a.student_id = ?
    WHERE su.student_id = ? AND DATE(l.scheduled_start_time) = CURDATE()
    ORDER BY l.scheduled_start_time
");
$stmt->execute([$student_id, $student_id]);
$lectures = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - AttendApp</title>
    <link rel="stylesheet" href="/<?php echo FOLDER_NAME; ?>/assets/css/dashboard.css">
</head>
<body>
    <?php include '../includes/student_nav.php'; ?>
    
    <div class="container">
        <div class="page-header">
            <h1>Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?></h1>
            <p>Student ID: <?php echo htmlspecialchars($_SESSION['student_number'] ?? 'N/A'); ?></p>
        </div>

        <?php if ($msg = getMessage()): ?>
        <div class="alert alert-<?php echo $msg['type']; ?>">
            <?php echo htmlspecialchars($msg['message']); ?>
        </div>
        <?php endif; ?>

        <h2>Today's Lectures</h2>
        
        <?php if (empty($lectures)): ?>
            <div class="empty-state">
                <div class="empty-icon">📅</div>
                <p>No lectures scheduled for today</p>
            </div>
        <?php else: ?>
            <div class="lecture-list">
                <?php foreach ($lectures as $lecture): 
                    $now = time();
                    $start = strtotime($lecture['actual_start_time'] ?? $lecture['scheduled_start_time']);
                    $end = strtotime($lecture['scheduled_end_time']);
                    $isActive = $lecture['status'] === 'active' && $now >= $start && $now <= $end;
                ?>
                <div class="lecture-card <?php echo $isActive ? 'active-lecture' : ''; ?>">
                    <div class="lecture-info">
                        <h3><?php echo htmlspecialchars($lecture['course_code'] . ' - ' . $lecture['course_name']); ?></h3>
                        <p>🕐 <?php echo date('g:i A', strtotime($lecture['scheduled_start_time'])); ?> - <?php echo date('g:i A', strtotime($lecture['scheduled_end_time'])); ?></p>
                        <span class="status-badge status-<?php echo $lecture['status']; ?>"><?php echo ucfirst($lecture['status']); ?></span>
                        
                        <?php if ($lecture['attended']): ?>
                            <span class="badge badge-success">✓ Attended</span>
                        <?php elseif ($isActive): ?>
                            <span class="badge badge-warning">⏳ Mark Attendance</span>
                        <?php endif; ?>
                    </div>

                    <?php if ($isActive && !$lecture['attended']): ?>
                    <form action="attendance.php" method="POST" style="margin-top: 1rem; background: rgba(16, 185, 129, 0.1); border: 2px solid rgba(16, 185, 129, 0.3); border-radius: 10px; padding: 1.5rem;">
                        <input type="hidden" name="lecture_id" value="<?php echo $lecture['id']; ?>">
                        <div class="form-group">
                            <label class="form-label">Enter Secret Code</label>
                            <input type="text" name="secret_code" class="form-input" placeholder="Enter code from lecturer" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Feedback (Optional)</label>
                            <textarea name="feedback" class="form-input" rows="3" placeholder="Share your thoughts about this lecture..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary" style="width: 100%;">Submit Attendance</button>
                    </form>
                    <?php elseif ($lecture['status'] === 'scheduled'): ?>
                    <div class="alert alert-info" style="margin-top: 1rem;">
                        ℹ️ Attendance will be available once the lecturer starts the lecture
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Auto-refresh every 30 seconds to check for active lectures
        setTimeout(() => location.reload(), 30000);
    </script>
</body>
</html>