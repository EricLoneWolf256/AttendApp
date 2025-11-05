<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AttendApp - Modern Lecture Attendance Management</title>
    <link rel="stylesheet" href="assets/css/main.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">🎓</div>
            <h1>AttendApp</h1>
            <p class="subtitle">Modern lecture attendance management</p>
        </div>

        <div class="roles-grid">
            <div class="role-card admin" onclick="openModal('admin')">
                <div class="role-icon">👥</div>
                <h2 class="role-title">Admin</h2>
                <p class="role-description">Manage timetables and lecturers</p>
                <button class="role-button">Continue as Admin</button>
            </div>

            <div class="role-card lecturer" onclick="openModal('lecturer')">
                <div class="role-icon">🎓</div>
                <h2 class="role-title">Lecturer</h2>
                <p class="role-description">Manage courses and lectures</p>
                <button class="role-button">Continue as Lecturer</button>
            </div>

            <div class="role-card student" onclick="openModal('student')">
                <div class="role-icon">😊</div>
                <h2 class="role-title">Student</h2>
                <p class="role-description">Attend lectures and submit feedback</p>
                <button class="role-button">Continue as Student</button>
            </div>
        </div>
    </div>

    <!-- Login Modals -->
    <div id="adminModal" class="modal">
        <div class="modal-content">
            <button class="close-modal" onclick="closeModal('admin')">←</button>
            <div class="modal-header">
                <div class="modal-icon admin-icon">👥</div>
                <h2 class="modal-title">Admin Portal</h2>
                <p class="modal-subtitle">Welcome back! Please enter your details.</p>
            </div>
            <form action="auth.php" method="POST">
                <input type="hidden" name="action" value="login">
                <input type="hidden" name="role" value="admin">
                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-input" placeholder="Enter your email" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-input" placeholder="Enter your password" required>
                </div>
                <button type="submit" class="submit-button">Log In</button>
            </form>
        </div>
    </div>

    <div id="lecturerModal" class="modal">
        <div class="modal-content">
            <button class="close-modal" onclick="closeModal('lecturer')">←</button>
            <div class="modal-header">
                <div class="modal-icon lecturer-icon">🎓</div>
                <h2 class="modal-title">Lecturer Login</h2>
                <p class="modal-subtitle">Welcome back! Please enter your details.</p>
            </div>
            <form action="auth.php" method="POST">
                <input type="hidden" name="action" value="login">
                <input type="hidden" name="role" value="lecturer">
                <div class="form-group">
                    <label class="form-label">Email / Username</label>
                    <input type="text" name="email" class="form-input" placeholder="Enter your email" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-input" placeholder="Enter your password" required>
                </div>
                <button type="submit" class="submit-button">Login</button>
            </form>
        </div>
    </div>

    <div id="studentModal" class="modal">
        <div class="modal-content">
            <button class="close-modal" onclick="closeModal('student')">←</button>
            <div class="modal-header">
                <div class="modal-icon student-icon">😊</div>
                <h2 class="modal-title">Welcome Back</h2>
                <p class="modal-subtitle">Log in to your AttendApp account</p>
            </div>
            <form action="auth.php" method="POST">
                <input type="hidden" name="action" value="login">
                <input type="hidden" name="role" value="student">
                <div class="form-group">
                    <label class="form-label">Student ID or Email</label>
                    <input type="text" name="email" class="form-input" placeholder="Enter your student ID or email" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-input" placeholder="Enter your password" required>
                </div>
                <button type="submit" class="submit-button">Log In</button>
                <div class="form-footer">
                    Don't have an account? <a href="register.php">Sign up</a>
                </div>
            </form>
        </div>
    </div>

    <script src="assets/js/main.js"></script>
</body>
</html>