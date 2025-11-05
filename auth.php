<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit();
}

$action = $_POST['action'] ?? '';
$db = getDB();

if ($action === 'login') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $role = $_POST['role'];

    // Get user
    $stmt = $db->prepare("SELECT * FROM users WHERE email = ? AND role = ?");
    $stmt->execute([$email, $role]);
    $user = $stmt->fetch();

    if (!$user) {
        setMessage('error', 'User not found');
        header('Location: index.php');
        exit();
    }

    if (!password_verify($password, $user['password'])) {
        setMessage('error', 'Wrong password');
        header('Location: index.php');
        exit();
    }

    // Set session
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['name'] = $user['name'];

    // Get role data
    if ($role === 'lecturer') {
        $stmt = $db->prepare("SELECT * FROM lecturers WHERE user_id = ?");
        $stmt->execute([$user['id']]);
        $lec = $stmt->fetch();
        if ($lec) {
            $_SESSION['lecturer_id'] = $lec['id'];
        }
    } elseif ($role === 'student') {
        $stmt = $db->prepare("SELECT * FROM students WHERE user_id = ?");
        $stmt->execute([$user['id']]);
        $stu = $stmt->fetch();
        if ($stu) {
            $_SESSION['student_id'] = $stu['id'];
            $_SESSION['student_number'] = $stu['student_id'];
        }
    }

    // Redirect
    if ($role === 'admin') {
        header('Location: /'.FOLDER_NAME.'/admin/dashboard.php');
    } elseif ($role === 'lecturer') {
        header('Location: /'.FOLDER_NAME.'/lecturer/dashboard.php');
    } else {
        header('Location: /'.FOLDER_NAME.'/student/dashboard.php');
    }
    exit();

} elseif ($action === 'register') {
    // Registration code here
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = 'student';

    try {
        $db->beginTransaction();
        
        $stmt = $db->prepare("INSERT INTO users (email, password, role, name) VALUES (?, ?, ?, ?)");
        $stmt->execute([$email, $password, $role, $name]);
        $user_id = $db->lastInsertId();

        $stmt = $db->prepare("INSERT INTO students (user_id, student_id, course, year) VALUES (?, ?, ?, ?)");
        $stmt->execute([$user_id, $_POST['student_id'], $_POST['course'], $_POST['year']]);

        $db->commit();
        setMessage('success', 'Registration successful! Please login.');
        header('Location: index.php');
    } catch (Exception $e) {
        $db->rollBack();
        setMessage('error', 'Registration failed: ' . $e->getMessage());
        header('Location: register.php');
    }
    exit();
}

header('Location: index.php');
exit();
?>
