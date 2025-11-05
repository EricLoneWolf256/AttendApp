// FILE: admin/lecturers.php
// ============================================================================
<?php
require_once '../config.php';
requireRole('admin');
$db = getDB();

$lecturers = $db->query("
    SELECT l.id, u.name, u.email, l.department, 
           COUNT(cu.id) as course_count
    FROM lecturers l
    JOIN users u ON l.user_id = u.id
    LEFT JOIN course_units cu ON l.id = cu.lecturer_id
    GROUP BY l.id
    ORDER BY u.name
")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Lecturers - AttendApp</title>
    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body>
    <?php include '../includes/admin_nav.php'; ?>
    
    <div class="container">
        <div class="page-header">
            <h1>Lecturers</h1>
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Department</th>
                    <th>Assigned Courses</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($lecturers as $lecturer): ?>
                <tr>
                    <td><?php echo htmlspecialchars($lecturer['name']); ?></td>
                    <td><?php echo htmlspecialchars($lecturer['email']); ?></td>
                    <td><?php echo htmlspecialchars($lecturer['department'] ?? 'N/A'); ?></td>
                    <td><?php echo $lecturer['course_count']; ?> courses</td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>