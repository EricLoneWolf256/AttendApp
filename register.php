<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - AttendApp</title>
    <link rel="stylesheet" href="assets/css/main.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">🎓</div>
            <h1>Create Account</h1>
            <p class="subtitle">Register as a student</p>
        </div>

        <?php if ($msg = getMessage()): ?>
        <div class="alert alert-<?php echo $msg['type']; ?>" style="max-width: 500px; margin: 0 auto 2rem;">
            <?php echo htmlspecialchars($msg['message']); ?>
        </div>
        <?php endif; ?>

        <div style="max-width: 500px; margin: 0 auto;">
            <div class="modal-content" style="position: relative;">
                <a href="index.php" style="position: absolute; top: 1rem; left: 1rem; color: #fff; text-decoration: none; font-size: 1.5rem;">←</a>
                
                <div class="modal-header">
                    <div class="modal-icon student-icon">😊</div>
                    <h2 class="modal-title">Student Registration</h2>
                    <p class="modal-subtitle">Fill in your details to create an account</p>
                </div>

                <form action="auth.php" method="POST">
                    <input type="hidden" name="action" value="register">
                    <input type="hidden" name="role" value="student">

                    <div class="form-group">
                        <label class="form-label">Full Name *</label>
                        <input type="text" name="name" class="form-input" placeholder="Enter your full name" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Student ID *</label>
                        <input type="text" name="student_id" class="form-input" placeholder="Enter your student ID" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Email Address *</label>
                        <input type="email" name="email" class="form-input" placeholder="Enter your email" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Course/Program *</label>
                        <input type="text" name="course" class="form-input" placeholder="e.g., Computer Science" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Year of Study *</label>
                        <select name="year" class="form-input" required>
                            <option value="">Select Year</option>
                            <option value="1">Year 1</option>
                            <option value="2">Year 2</option>
                            <option value="3">Year 3</option>
                            <option value="4">Year 4</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Password *</label>
                        <input type="password" name="password" class="form-input" placeholder="Create a password" required minlength="6">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Confirm Password *</label>
                        <input type="password" name="confirm_password" class="form-input" placeholder="Confirm your password" required minlength="6">
                    </div>

                    <button type="submit" class="submit-button">Create Account</button>

                    <div class="form-footer">
                        Already have an account? <a href="index.php">Login</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Password validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const password = document.querySelector('[name="password"]').value;
            const confirm = document.querySelector('[name="confirm_password"]').value;
            
            if (password !== confirm) {
                e.preventDefault();
                alert('Passwords do not match!');
            }
            
            if (password.length < 6) {
                e.preventDefault();
                alert('Password must be at least 6 characters long!');
            }
        });
    </script>
</body>
</html>