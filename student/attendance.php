<?php
require_once '../config.php';
requireRole('student');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: dashboard.php');
    exit();
}

$db = getDB();
$student_id = $_SESSION['student_id'];
$lecture_id = $_POST['lecture_id'];
$secret_code = trim($_POST['secret_code']);
$feedback = trim($_POST['feedback'] ?? '');

try {
    // Get lecture details
    $stmt = $db->prepare("SELECT * FROM lectures WHERE id = ?");
    $stmt->execute([$lecture_id]);
    $lecture = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$lecture) {
        throw new Exception('Lecture not found');
    }

    // Check if lecture is active
    if ($lecture['status'] !== 'active') {
        throw new Exception('This lecture is not currently active');
    }

    // Check time window
    $now = time();
    $start = strtotime($lecture['actual_start_time']);
    $end = strtotime($lecture['scheduled_end_time']);

    if ($now < $start || $now > $end) {
        throw new Exception('Attendance window has closed');
    }

    // Verify secret code
    if ($secret_code !== $lecture['secret_code']) {
        throw new Exception('Invalid secret code. Please check and try again.');
    }

    // Check if already attended
    $stmt = $db->prepare("SELECT * FROM attendance WHERE lecture_id = ? AND student_id = ?");
    $stmt->execute([$lecture_id, $student_id]);
    if ($stmt->fetch()) {
        throw new Exception('You have already marked attendance for this lecture');
    }

    $db->beginTransaction();

    // Record attendance
    $stmt = $db->prepare("INSERT INTO attendance (lecture_id, student_id, code_verified) VALUES (?, ?, 1)");
    $stmt->execute([$lecture_id, $student_id]);

    // Record feedback if provided
    if (!empty($feedback)) {
        $stmt = $db->prepare("INSERT INTO feedback (lecture_id, student_id, comment) VALUES (?, ?, ?)");
        $stmt->execute([$lecture_id, $student_id, $feedback]);
    }

    $db->commit();
    setMessage('success', 'Attendance marked successfully! ✓');
    
} catch (Exception $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    setMessage('error', $e->getMessage());
}

header('Location: dashboard.php');
exit();
?>
