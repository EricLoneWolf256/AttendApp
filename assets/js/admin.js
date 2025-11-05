// FILE: assets/js/admin.js - Admin Functions
// ============================================================================

// Delete confirmation
function confirmDelete(message) {
    return confirm(message || 'Are you sure you want to delete this item?');
}

// Assign lecturer modal
function assignLecturer(courseId) {
    const modal = document.getElementById('assignLecturerModal');
    if (modal) {
        document.getElementById('assignCourseId').value = courseId;
        openModal('assignLecturerModal');
    }
}

// Schedule lecture validation
function validateScheduleForm(form) {
    const date = form.querySelector('[name="date"]').value;
    const startTime = form.querySelector('[name="start_time"]').value;
    const endTime = form.querySelector('[name="end_time"]').value;
    
    const start = new Date(`${date} ${startTime}`);
    const end = new Date(`${date} ${endTime}`);
    
    if (end <= start) {
        showNotification('End time must be after start time', 'error');
        return false;
    }
    
    const duration = (end - start) / 60000; // minutes
    if (duration < 30) {
        showNotification('Lecture must be at least 30 minutes long', 'error');
        return false;
    }
    
    if (duration > 240) {
        showNotification('Lecture cannot exceed 4 hours', 'error');
        return false;
    }
    
    return true;
}

document.addEventListener('DOMContentLoaded', function() {
    // Add validation to schedule form
    const scheduleForm = document.querySelector('#scheduleModal form');
    if (scheduleForm) {
        scheduleForm.addEventListener('submit', function(e) {
            if (!validateScheduleForm(this)) {
                e.preventDefault();
            }
        });
    }
    
    // Set minimum date to today
    const dateInputs = document.querySelectorAll('input[type="date"]');
    const today = new Date().toISOString().split('T')[0];
    dateInputs.forEach(input => {
        if (!input.hasAttribute('min')) {
            input.setAttribute('min', today);
        }
    });
});

// Export to CSV
function exportToCSV(tableId, filename) {
    const table = document.getElementById(tableId);
    if (!table) return;
    
    let csv = [];
    const rows = table.querySelectorAll('tr');
    
    rows.forEach(row => {
        const cols = row.querySelectorAll('td, th');
        const rowData = Array.from(cols).map(col => `"${col.textContent.trim()}"`);
        csv.push(rowData.join(','));
    });
    
    const csvContent = csv.join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = filename || 'export.csv';
    a.click();
    window.URL.revokeObjectURL(url);
}

// Print report
function printReport() {
    window.print();
}
