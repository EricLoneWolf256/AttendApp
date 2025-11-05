// FILE: admin/courses.php - Complete Course Management
<?php
require_once '../config.php';
requireRole('admin');

$db = getDB();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    
    try {
        if ($action === 'add_course') {
            $code = trim($_POST['code']);
            $name = trim($_POST['name']);
            $course = trim($_POST['course']);
            $year = intval($_POST['year']);
            
            $stmt = $db->prepare("INSERT INTO course_units (code, name, course, year) VALUES (?, ?, ?, ?)");
            $stmt->execute([$code, $name, $course, $year]);
            
            setMessage('success', 'Course unit added successfully!');
            redirect('admin/courses.php');
            
        } elseif ($action === 'assign_lecturer') {
            $course_id = $_POST['course_id'];
            $lecturer_id = $_POST['lecturer_id'];
            
            $stmt = $db->prepare("UPDATE course_units SET lecturer_id = ? WHERE id = ?");
            $stmt->execute([$lecturer_id, $course_id]);
            
            setMessage('success', 'Lecturer assigned successfully!');
            redirect('admin/courses.php');
            
        } elseif ($action === 'delete_course') {
            $course_id = $_POST['course_id'];
            
            $stmt = $db->prepare("DELETE FROM course_units WHERE id = ?");
            $stmt->execute([$course_id]);
            
            setMessage('success', 'Course deleted successfully!');
            redirect('admin/courses.php');
        }
    } catch (Exception $e) {
        setMessage('error', 'Operation failed: ' . $e->getMessage());
        redirect('admin/courses.php');
    }
}

// Get all courses with lecturer info
$courses = $db->query("
    SELECT cu.*, u.name as lecturer_name, u.email as lecturer_email
    FROM course_units cu
    LEFT JOIN lecturers lec ON cu.lecturer_id = lec.id
    LEFT JOIN users u ON lec.user_id = u.id
    ORDER BY cu.course, cu.year, cu.code
")->fetchAll(PDO::FETCH_ASSOC);

// Get all lecturers for assignment dropdown
$lecturers = $db->query("
    SELECT lec.id, u.name, u.email 
    FROM lecturers lec 
    JOIN users u ON lec.user_id = u.id 
    ORDER BY u.name
")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Courses - AttendApp</title>
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <style>
        .table-actions {
            display: flex;
            gap: 0.5rem;
        }
        
        .btn-icon {
            padding: 0.5rem;
            width: 35px;
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .btn-danger {
            background: rgba(239, 68, 68, 0.2);
            color: #fca5a5;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }
        
        .btn-danger:hover {
            background: rgba(239, 68, 68, 0.3);
        }
    </style>
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
                <h1>Course Units</h1>
                <p>Manage course units and assign lecturers</p>
            </div>
            <button onclick="openModal('addCourseModal')" class="btn btn-primary">+ Add Course Unit</button>
        </div>

        <!-- Courses Table -->
        <div style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 15px; overflow: hidden; margin-top: 2rem;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Course/Program</th>
                        <th>Year</th>
                        <th>Lecturer</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($courses)): ?>
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 3rem; color: #6b7280;">
                            <div style="font-size: 3rem; margin-bottom: 1rem;">📚</div>
                            <p>No courses added yet. Click "Add Course Unit" to get started.</p>
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($courses as $course): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($course['code']); ?></strong></td>
                            <td><?php echo htmlspecialchars($course['name']); ?></td>
                            <td><?php echo htmlspecialchars($course['course']); ?></td>
                            <td>Year <?php echo $course['year']; ?></td>
                            <td>
                                <?php if ($course['lecturer_name']): ?>
                                    <div><?php echo htmlspecialchars($course['lecturer_name']); ?></div>
                                    <div style="font-size: 0.875rem; color: #a0aec0;"><?php echo htmlspecialchars($course['lecturer_email']); ?></div>
                                <?php else: ?>
                                    <span style="color: #6b7280;">Not assigned</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($course['lecturer_id']): ?>
                                    <span class="badge badge-success">✓ Assigned</span>
                                <?php else: ?>
                                    <span class="badge badge-warning">⚠ Unassigned</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="table-actions">
                                    <button onclick="assignLecturerModal(<?php echo $course['id']; ?>, '<?php echo htmlspecialchars($course['code']); ?>')" 
                                            class="btn btn-secondary btn-sm" title="Assign Lecturer">
                                        👨‍🏫
                                    </button>
                                    <button onclick="deleteCourse(<?php echo $course['id']; ?>, '<?php echo htmlspecialchars($course['code']); ?>')" 
                                            class="btn btn-danger btn-sm" title="Delete Course">
                                        🗑️
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Course Modal -->
    <div id="addCourseModal" class="modal">
        <div class="modal-content">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                <h2 style="margin: 0;">Add New Course Unit</h2>
                <button onclick="closeModal('addCourseModal')" style="background: none; border: none; color: #fff; font-size: 1.5rem; cursor: pointer;">×</button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="add_course">
                
                <div class="form-group">
                    <label class="form-label">Course Code *</label>
                    <input type="text" name="code" class="form-input" placeholder="e.g., CS101" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Course Name *</label>
                    <input type="text" name="name" class="form-input" placeholder="e.g., Introduction to Programming" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Course/Program *</label>
                    <input type="text" name="course" class="form-input" placeholder="e.g., Computer Science" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Year *</label>
                    <select name="year" class="form-input" required>
                        <option value="">Select Year</option>
                        <option value="1">Year 1</option>
                        <option value="2">Year 2</option>
                        <option value="3">Year 3</option>
                        <option value="4">Year 4</option>
                    </select>
                </div>

                <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                    <button type="button" onclick="closeModal('addCourseModal')" class="btn btn-secondary" style="flex: 1;">Cancel</button>
                    <button type="submit" class="btn btn-primary" style="flex: 1;">Add Course Unit</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Assign Lecturer Modal -->
    <div id="assignLecturerModal" class="modal">
        <div class="modal-content">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                <div>
                    <h2 style="margin: 0;">Assign Lecturer</h2>
                    <p id="assignCourseInfo" style="color: #a0aec0; margin-top: 0.5rem;"></p>
                </div>
                <button onclick="closeModal('assignLecturerModal')" style="background: none; border: none; color: #fff; font-size: 1.5rem; cursor: pointer;">×</button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="assign_lecturer">
                <input type="hidden" name="course_id" id="assignCourseId">
                
                <div class="form-group">
                    <label class="form-label">Select Lecturer *</label>
                    <select name="lecturer_id" class="form-input" required>
                        <option value="">Choose a lecturer</option>
                        <?php foreach ($lecturers as $lecturer): ?>
                            <option value="<?php echo $lecturer['id']; ?>">
                                <?php echo htmlspecialchars($lecturer['name'] . ' (' . $lecturer['email'] . ')'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                    <button type="button" onclick="closeModal('assignLecturerModal')" class="btn btn-secondary" style="flex: 1;">Cancel</button>
                    <button type="submit" class="btn btn-primary" style="flex: 1;">Assign Lecturer</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <h2 style="margin-bottom: 1rem;">Delete Course?</h2>
            <p style="color: #a0aec0; margin-bottom: 2rem;">Are you sure you want to delete <strong id="deleteCourseInfo"></strong>? This action cannot be undone.</p>
            <form method="POST">
                <input type="hidden" name="action" value="delete_course">
                <input type="hidden" name="course_id" id="deleteCourseId">
                
                <div style="display: flex; gap: 1rem;">
                    <button type="button" onclick="closeModal('deleteModal')" class="btn btn-secondary" style="flex: 1;">Cancel</button>
                    <button type="submit" class="btn btn-danger" style="flex: 1;">Delete Course</button>
                </div>
            </form>
        </div>
    </div>

    <script src="../assets/js/modal.js"></script>
    <script>
        function assignLecturerModal(courseId, courseCode) {
            document.getElementById('assignCourseId').value = courseId;
            document.getElementById('assignCourseInfo').textContent = 'Course: ' + courseCode;
            openModal('assignLecturerModal');
        }

        function deleteCourse(courseId, courseCode) {
            document.getElementById('deleteCourseId').value = courseId;
            document.getElementById('deleteCourseInfo').textContent = courseCode;
            openModal('deleteModal');
        }
    </script>
</body>
</html>