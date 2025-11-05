<?php
require_once '../config.php';
requireRole('admin');

$db = getDB();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_unit_id = $_POST['course_unit_id'];
    $date = $_POST['date'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    
    $scheduled_start = "$date $start_time:00";
    $scheduled_end = "$date $end_time:00";
    
    try {
        $stmt = $db->prepare("INSERT INTO lectures (course_unit_id, scheduled_start_time, scheduled_end_time) VALUES (?, ?, ?)");
        $stmt->execute([$course_unit_id, $scheduled_start, $scheduled_end]);
        
        setMessage('success', 'Lecture scheduled successfully!');
        header('Location: timetable.php');
        exit();
    } catch (Exception $e) {
        setMessage('error', 'Failed to schedule lecture: ' . $e->getMessage());
    }
}

// Get courses with assigned lecturers
$courses = $db->query("SELECT id, code, name FROM course_units WHERE lecturer_id IS NOT NULL ORDER BY code")->fetchAll();

// Get upcoming lectures
$lectures = $db->query("
    SELECT l.*, cu.code, cu.name as course_name, u.name as lecturer_name
    FROM lectures l
    JOIN course_units cu ON l.course_unit_id = cu.id
    LEFT JOIN lecturers lec ON cu.lecturer_id = lec.id
    LEFT JOIN users u ON lec.user_id = u.id
    WHERE l.scheduled_start_time >= NOW()
    ORDER BY l.scheduled_start_time
    LIMIT 50
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Timetable Management - AttendApp</title>
    <link rel="stylesheet" href="/<?php echo FOLDER_NAME; ?>/assets/css/dashboard.css">
</head>
<body>
    <?php include '../includes/admin_nav.php'; ?>
    
    <div class="container">
        <?php if ($msg = getMessage()): ?>
        <div class="alert alert-<?php echo $msg['type']; ?>">
            <?php echo htmlspecialchars($msg['message']); ?>
        </div>
        <?php endif; ?>

        <div class="page-header" style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h1>Timetable Management</h1>
                <p>Schedule and manage lectures</p>
            </div>
            <button onclick="openModal('scheduleModal')" class="btn btn-primary">+ Schedule Lecture</button>
        </div>

        <div class="lecture-list">
            <?php if (empty($lectures)): ?>
                <div class="empty-state">
                    <div class="empty-icon">📅</div>
                    <p>No lectures scheduled yet</p>
                </div>
            <?php else: ?>
                <?php foreach ($lectures as $lecture): ?>
                <div class="lecture-card">
                    <div class="lecture-info">
                        <h3><?php echo htmlspecialchars($lecture['code'] . ' - ' . $lecture['course_name']); ?></h3>
                        <p>📅 <?php echo date('F j, Y', strtotime($lecture['scheduled_start_time'])); ?></p>
                        <p>🕐 <?php echo date('g:i A', strtotime($lecture['scheduled_start_time'])); ?> - <?php echo date('g:i A', strtotime($lecture['scheduled_end_time'])); ?></p>
                        <p>👨‍🏫 <?php echo htmlspecialchars($lecture['lecturer_name']); ?></p>
                    </div>
                    <span class="status-badge status-<?php echo $lecture['status']; ?>"><?php echo ucfirst($lecture['status']); ?></span>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Schedule Modal -->
    <div id="scheduleModal" class="modal">
        <div class="modal-content">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                <h2 style="margin: 0;">Schedule New Lecture</h2>
                <button onclick="closeModal('scheduleModal')" style="background: none; border: none; color: #fff; font-size: 1.5rem; cursor: pointer;">×</button>
            </div>
            <form method="POST">
                <div class="form-group">
                    <label class="form-label">Course Unit *</label>
                    <select name="course_unit_id" class="form-input" required>
                        <option value="">Select a course</option>
                        <?php foreach ($courses as $course): ?>
                        <option value="<?php echo $course['id']; ?>"><?php echo htmlspecialchars($course['code'] . ' - ' . $course['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Date *</label>
                    <input type="date" name="date" class="form-input" required min="<?php echo date('Y-m-d'); ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">Start Time *</label>
                    <input type="time" name="start_time" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">End Time *</label>
                    <input type="time" name="end_time" class="form-input" required>
                </div>
                <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                    <button type="button" onclick="closeModal('scheduleModal')" class="btn btn-secondary" style="flex: 1;">Cancel</button>
                    <button type="submit" class="btn btn-primary" style="flex: 1;">Schedule Lecture</button>
                </div>
            </form>
        </div>
    </div>

    <script src="/<?php echo FOLDER_NAME; ?>/assets/js/modal.js"></script>
</body>
</html>