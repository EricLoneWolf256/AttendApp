// FILE: admin/reports.php
// ============================================================================
<?php
require_once '../config.php';
requireRole('admin');
$db = getDB();

$attendance_stats = $db->query("
    SELECT cu.code, cu.name, COUNT(DISTINCT l.id) as total_lectures,
           COUNT(DISTINCT a.id) as total_attendance,
           ROUND(COUNT(DISTINCT a.id) / NULLIF(COUNT(DISTINCT l.id), 0) * 100, 2) as attendance_rate
    FROM course_units cu
    LEFT JOIN lectures l ON cu.id = l.course_unit_id
    LEFT JOIN attendance a ON l.id = a.lecture_id
    GROUP BY cu.id
    ORDER BY cu.code
")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reports - AttendApp</title>
    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body>
    <?php include '../includes/admin_nav.php'; ?>
    
    <div class="container">
        <div class="page-header">
            <h1>Attendance Reports</h1>
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th>Course Code</th>
                    <th>Course Name</th>
                    <th>Total Lectures</th>
                    <th>Total Attendance</th>
                    <th>Attendance Rate</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($attendance_stats as $stat): ?>
                <tr>
                    <td><?php echo htmlspecialchars($stat['code']); ?></td>
                    <td><?php echo htmlspecialchars($stat['name']); ?></td>
                    <td><?php echo $stat['total_lectures']; ?></td>
                    <td><?php echo $stat['total_attendance']; ?></td>
                    <td>
                        <span class="badge <?php echo $stat['attendance_rate'] >= 75 ? 'badge-success' : 'badge-warning'; ?>">
                            <?php echo $stat['attendance_rate'] ?? 0; ?>%
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>