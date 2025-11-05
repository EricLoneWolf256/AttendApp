// FILE: lecturer/reports.php
// ============================================================================
<?php
require_once '../config.php';
requireRole('lecturer');
$db = getDB();

$lecturer_id = $_SESSION['lecturer_id'];

$stats = $db->prepare("
    SELECT cu.code, cu.name,
           COUNT(DISTINCT l.id) as total_lectures,
           COUNT(DISTINCT a.id) as total_attendance
    FROM course_units cu
    LEFT JOIN lectures l ON cu.id = l.course_unit_id
    LEFT JOIN attendance a ON l.id = a.lecture_id
    WHERE cu.lecturer_id = ?
    GROUP BY cu.id
");
$stats->execute([$lecturer_id]);
$courses = $stats->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reports - AttendApp</title>
    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body>
    <?php include '../includes/lecturer_nav.php'; ?>
    
    <div class="container">
        <div class="page-header">
            <h1>My Reports</h1>
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th>Course</th>
                    <th>Total Lectures</th>
                    <th>Total Attendance</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($courses as $course): ?>
                <tr>
                    <td><?php echo htmlspecialchars($course['code'] . ' - ' . $course['name']); ?></td>
                    <td><?php echo $course['total_lectures']; ?></td>
                    <td><?php echo $course['total_attendance']; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>