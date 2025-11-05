<?php
require_once '../config.php';
requireRole('lecturer');

$db = getDB();
$lecturer_id = $_SESSION['lecturer_id'];

// Get today's lectures
$stmt = $db->prepare("
    SELECT l.*, cu.name as course_name, cu.code as course_code,
           COUNT(DISTINCT a.id) as attendance_count
    FROM lectures l
    JOIN course_units cu ON l.course_unit_id = cu.id
    LEFT JOIN attendance a ON l.id = a.lecture_id
    WHERE cu.lecturer_id = ? AND DATE(l.scheduled_start_time) = CURDATE()
    GROUP BY l.id
    ORDER BY l.scheduled_start_time
");
$stmt->execute([$lecturer_id]);
$todayLectures = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lecturer Dashboard - AttendApp</title>
    <link rel="stylesheet" href="/<?php echo FOLDER_NAME; ?>/assets/css/dashboard.css">
</head>
<body>
    <?php include '../includes/lecturer_nav.php'; ?>
    
    <div class="container">
        <div class="page-header">
            <h1>Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?></h1>
            <p>Manage your lectures and track attendance</p>
        </div>

        <?php if ($msg = getMessage()): ?>
        <div class="alert alert-<?php echo $msg['type']; ?>">
            <?php echo htmlspecialchars($msg['message']); ?>
        </div>
        <?php endif; ?>

        <h2>Today's Lectures</h2>
        
        <?php if (empty($todayLectures)): ?>
            <div class="empty-state">
                <div class="empty-icon">📅</div>
                <p>No lectures scheduled for today</p>
                <p style="color: #6b7280;">Enjoy your day off!</p>
            </div>
        <?php else: ?>
            <div class="lecture-list">
                <?php foreach ($todayLectures as $lecture): ?>
                <div class="lecture-card">
                    <div class="lecture-info">
                        <h3><?php echo htmlspecialchars($lecture['course_code'] . ' - ' . $lecture['course_name']); ?></h3>
                        <p>🕐 <?php echo date('g:i A', strtotime($lecture['scheduled_start_time'])); ?> - <?php echo date('g:i A', strtotime($lecture['scheduled_end_time'])); ?></p>
                        <span class="status-badge status-<?php echo $lecture['status']; ?>"><?php echo ucfirst($lecture['status']); ?></span>
                        <span class="badge badge-success"><?php echo $lecture['attendance_count']; ?> students attended</span>
                        
                        <?php if ($lecture['status'] === 'active' && $lecture['secret_code']): ?>
                        <div class="secret-code-box">
                            <label style="color: #6ee7b7; font-weight: 600; margin-bottom: 0.5rem; display: block;">Secret Code (Share with students):</label>
                            <div style="display: flex; align-items: center; gap: 1rem;">
                                <span class="code" style="font-size: 2rem; font-weight: 700; letter-spacing: 0.3rem; font-family: 'Courier New', monospace;"><?php echo $lecture['secret_code']; ?></span>
                                <button onclick="copyCode('<?php echo $lecture['secret_code']; ?>')" class="btn btn-secondary btn-sm">Copy</button>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>

                    <div class="lecture-actions">
                        <?php if ($lecture['status'] === 'scheduled'): ?>
                        <a href="start_lecture.php?id=<?php echo $lecture['id']; ?>" class="btn btn-primary">Start Lecture</a>
                        <?php elseif ($lecture['status'] === 'active'): ?>
                        <a href="lecture_details.php?id=<?php echo $lecture['id']; ?>" class="btn btn-secondary">View Details</a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script>
        function copyCode(code) {
            if (navigator.clipboard) {
                navigator.clipboard.writeText(code).then(function() {
                    alert('Code copied to clipboard: ' + code);
                });
            } else {
                alert('Secret Code: ' + code);
            }
        }

        // Auto-refresh every 60 seconds
        setTimeout(() => location.reload(), 60000);
    </script>
</body>
</html>