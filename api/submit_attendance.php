// FILE: api/submit_attendance.php
// ============================================================================
<?php
require_once '../config.php';
header('Content-Type: application/json');

if (!isLoggedIn() || getUserRole() !== 'student') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$lecture_id = $data['lecture_id'] ?? null;
$secret_code = $data['secret_code'] ?? null;

if (!$lecture_id || !$secret_code) {
    echo json_encode(['success' => false, 'message' => 'Missing parameters']);
    exit();
}

$db = getDB();
$student_id = $_SESSION['student_id'];

try {
    $stmt = $db->prepare("SELECT * FROM lectures WHERE id = ?");
    $stmt->execute([$lecture_id]);
    $lecture = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$lecture || $lecture['status'] !== 'active') {
        throw new Exception('Lecture is not active');
    }

    if ($secret_code !== $lecture['secret_code']) {
        throw new Exception('Invalid secret code');
    }

    $stmt = $db->prepare("INSERT INTO attendance (lecture_id, student_id, code_verified) VALUES (?, ?, 1)");
    $stmt->execute([$lecture_id, $student_id]);

    echo json_encode(['success' => true, 'message' => 'Attendance marked successfully']);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>