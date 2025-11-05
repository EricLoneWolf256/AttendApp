// FILE: api/get_secret_code.php
// ============================================================================
<?php
require_once '../config.php';
header('Content-Type: application/json');

if (!isLoggedIn() || getUserRole() !== 'lecturer') {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$lecture_id = $_GET['lecture_id'] ?? null;

if (!$lecture_id) {
    echo json_encode(['error' => 'Lecture ID required']);
    exit();
}

$db = getDB();
$lecturer_id = $_SESSION['lecturer_id'];

$stmt = $db->prepare("
    SELECT l.secret_code, l.status 
    FROM lectures l
    JOIN course_units cu ON l.course_unit_id = cu.id
    WHERE l.id = ? AND cu.lecturer_id = ?
");
$stmt->execute([$lecture_id, $lecturer_id]);
$lecture = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$lecture) {
    echo json_encode(['error' => 'Lecture not found']);
    exit();
}

echo json_encode([
    'secret_code' => $lecture['secret_code'],
    'status' => $lecture['status']
]);
?>