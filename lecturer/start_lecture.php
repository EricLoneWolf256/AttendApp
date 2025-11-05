<?php
require_once '../config.php';
requireRole('lecturer');

$db = getDB();
$lecture_id = $_GET['id'] ?? null;

if (!$lecture_id) {
    setMessage('error', 'Invalid lecture ID');
    header('Location: dashboard.php');
    exit();
}

// Get lecture details
$stmt = $db->prepare("
    SELECT l.*, cu.name as course_name, cu.code, cu.lecturer_id
    FROM lectures l
    JOIN course_units cu ON l.course_unit_id = cu.id
    WHERE l.id = ?
");
$stmt->execute([$lecture_id]);
$lecture = $stmt->fetch();

if (!$lecture || $lecture['lecturer_id'] != $_SESSION['lecturer_id']) {
    setMessage('error', 'Unauthorized access');
    header('Location: dashboard.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Generate secret code
    $secret_code = strtoupper(substr(md5(uniqid(rand(), true)), 0, 6));
    
    $stmt = $db->prepare("UPDATE lectures SET status = 'active', actual_start_time = NOW(), secret_code = ? WHERE id = ?");
    $stmt->execute([$secret_code, $lecture_id]);
    
    setMessage('success', 'Lecture started successfully! Secret Code: ' . $secret_code);
    header('Location: dashboard.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Start Lecture - AttendApp</title>
    <link rel="stylesheet" href="/<?php echo FOLDER_NAME; ?>/assets/css/dashboard.css">
</head>
<body>
    <?php include '../includes/lecturer_nav.php'; ?>
    
    <div class="container">
        <div style="max-width: 600px; margin: 2rem auto;">
            <div style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 20px; padding: 2.5rem;">
                <div style="text-align: center; margin-bottom: 2rem;">
                    <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); border-radius: 20px; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; font-size: 2.5rem;">🎓</div>
                    <h1>Start Lecture</h1>
                    <p style="color: #a0aec0;">Confirm lecture details before starting</p>
                </div>

                <div style="background: rgba(255, 255, 255, 0.03); border-radius: 15px; padding: 1.5rem; margin-bottom: 2rem;">
                    <div style="display: flex; justify-content: space-between; padding: 0.75rem 0; border-bottom: 1px solid rgba(255, 255, 255, 0.05);">
                        <span style="color: #a0aec0;">Course Code</span>
                        <span style="font-weight: 600;"><?php echo htmlspecialchars($lecture['code']); ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 0.75rem 0; border-bottom: 1px solid rgba(255, 255, 255, 0.05);">
                        <span style="color: #a0aec0;">Course Name</span>
                        <span style="font-weight: 600;"><?php echo htmlspecialchars($lecture['course_name']); ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 0.75rem 0; border-bottom: 1px solid rgba(255, 255, 255, 0.05);">
                        <span style="color: #a0aec0;">Scheduled Time</span>
                        <span style="font-weight: 600;"><?php echo date('g:i A', strtotime($lecture['scheduled_start_time'])); ?> - <?php echo date('g:i A', strtotime($lecture['scheduled_end_time'])); ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 0.75rem 0;">
                        <span style="color: #a0aec0;">Date</span>
                        <span style="font-weight: 600;"><?php echo date('F j, Y', strtotime($lecture['scheduled_start_time'])); ?></span>
                    </div>
                </div>

                <div style="background: rgba(59, 130, 246, 0.1); border: 1px solid rgba(59, 130, 246, 0.3); border-radius: 10px; padding: 1rem; margin-bottom: 2rem; color: #93c5fd;">
                    ℹ️ Starting the lecture will generate a unique secret code for students to mark their attendance. The lecture will automatically close at the scheduled end time.
                </div>

                <form method="POST">
                    <div style="display: flex; gap: 1rem;">
                        <a href="dashboard.php" class="btn btn-secondary" style="flex: 1; text-align: center;">Cancel</a>
                        <button type="submit" class="btn btn-primary" style="flex: 1;">Start Lecture Now</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
