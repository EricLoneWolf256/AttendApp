// FILE: assets/js/student.js - Student Dashboard Functions
// ============================================================================

// Submit attendance
async function submitAttendance(lectureId, secretCode, feedback) {
    try {
        const response = await fetch('../api/submit_attendance.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                lecture_id: lectureId,
                secret_code: secretCode,
                feedback: feedback
            })
        });

        const data = await response.json();
        
        if (data.success) {
            showNotification(data.message, 'success');
            setTimeout(() => location.reload(), 1500);
        } else {
            showNotification(data.message, 'error');
        }
    } catch (error) {
        showNotification('Failed to submit attendance', 'error');
        console.error('Error:', error);
    }
}

// Validate attendance form
function validateAttendanceForm(form) {
    const secretCode = form.querySelector('[name="secret_code"]').value.trim();
    
    if (secretCode.length === 0) {
        showNotification('Please enter the secret code', 'error');
        return false;
    }
    
    return true;
}

// Check lecture status
async function checkLectureStatus(lectureId) {
    try {
        const response = await fetch(`../api/lecture_status.php?lecture_id=${lectureId}`);
        const data = await response.json();
        
        return data;
    } catch (error) {
        console.error('Error checking lecture status:', error);
        return null;
    }
}

// Auto-refresh for active lectures
document.addEventListener('DOMContentLoaded', function() {
    const activeLectures = document.querySelectorAll('.lecture-card.active-lecture');
    
    if (activeLectures.length > 0) {
        // Refresh every 30 seconds
        setInterval(() => {
            location.reload();
        }, 30000);
    }
    
    // Add form validation
    const attendanceForms = document.querySelectorAll('.attendance-form');
    attendanceForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!validateAttendanceForm(this)) {
                e.preventDefault();
            }
        });
    });
});

// Show remaining time
function updateRemainingTime(lectureId, endTime) {
    const now = new Date().getTime();
    const end = new Date(endTime).getTime();
    const remaining = Math.max(0, end - now);
    
    const minutes = Math.floor(remaining / 60000);
    const seconds = Math.floor((remaining % 60000) / 1000);
    
    const timeDisplay = document.querySelector(`[data-lecture-id="${lectureId}"] .time-remaining`);
    if (timeDisplay) {
        timeDisplay.textContent = `${minutes}m ${seconds}s remaining`;
    }
    
    if (remaining <= 0) {
        location.reload();
    }
}