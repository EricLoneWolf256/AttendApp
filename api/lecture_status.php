// FILE: api/lecture_status.php
// ============================================================================
<?php
require_once '../config.php';
header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$lecture_id = $_GET['lecture_id'] ?? null;

if (!$lecture_id) {
    echo json_encode(['error' => 'Lecture ID required']);
    exit();
}

$db = getDB();
$stmt = $db->prepare("
    SELECT l.*, cu.code, cu.name as course_name
    FROM lectures l
    JOIN course_units cu ON l.course_unit_id = cu.id
    WHERE l.id = ?
");
$stmt->execute([$lecture_id]);
$lecture = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$lecture) {
    echo json_encode(['error' => 'Lecture not found']);
    exit();
}

// Check if active
$now = time();
$start = strtotime($lecture['actual_start_time'] ?? $lecture['scheduled_start_time']);
$end = strtotime($lecture['scheduled_end_time']);

$lecture['is_active'] = $lecture['status'] === 'active' && $now >= $start && $now <= $end;
$lecture['time_remaining'] = max(0, $end - $now);

echo json_encode($lecture);
?>
