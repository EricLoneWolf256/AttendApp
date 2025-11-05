// ============================================================================
// FILE: lecturer/lectures.php
// ============================================================================
<?php
require_once '../config.php';
requireRole('lecturer');
$db = getDB();

$lecturer_id = $_SESSION['lecturer_id'];

$stmt = $db->prepare("
    SELECT l.*, cu.name as course_name, cu.code as course_code,
           COUNT(DISTINCT a.id) as attendance_count
    FROM lectures l
    JOIN course_units cu ON l.course_unit_id = cu.id
    LEFT JOIN attendance a ON l.id = a.lecture_id
    WHERE cu.lecturer_id = ?
    GROUP BY l.id
    ORDER BY l.scheduled_start_time DESC
    LIMIT 50
");
$stmt->execute([$lecturer_id]);
$lectures = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Lectures - AttendApp</title>
    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body>
    <?php include '../includes/lecturer_nav.php'; ?>
    
    <div class="container">
        <div class="page-header">
            <h1>My Lectures</h1>
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th>Course</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Status</th>
                    <th>Attendance</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($lectures as $lecture): ?>
                <tr>
                    <td><?php echo htmlspecialchars($lecture['course_code'] . ' - ' . $lecture['course_name']); ?></td>
                    <td><?php echo date('M j, Y', strtotime($lecture['scheduled_start_time'])); ?></td>
                    <td><?php echo date('g:i A', strtotime($lecture['scheduled_start_time'])); ?></td>
                    <td><span class="status-badge status-<?php echo $lecture['status']; ?>"><?php echo ucfirst($lecture['status']); ?></span></td>
                    <td><?php echo $lecture['attendance_count']; ?> students</td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>