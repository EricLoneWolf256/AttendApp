// ============================================================================
// FILE: assets/js/lecturer.js - Lecturer Dashboard Functions
// ============================================================================

// Copy secret code to clipboard
function copyCode(code) {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(code).then(function() {
            showNotification('Code copied to clipboard!', 'success');
        }).catch(function() {
            fallbackCopyCode(code);
        });
    } else {
        fallbackCopyCode(code);
    }
}

// Fallback copy method
function fallbackCopyCode(code) {
    const textarea = document.createElement('textarea');
    textarea.value = code;
    textarea.style.position = 'fixed';
    textarea.style.opacity = '0';
    document.body.appendChild(textarea);
    textarea.select();
    
    try {
        document.execCommand('copy');
        showNotification('Code copied to clipboard!', 'success');
    } catch (err) {
        showNotification('Failed to copy code', 'error');
    }
    
    document.body.removeChild(textarea);
}

// Show notification
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 1rem 1.5rem;
        background: ${type === 'success' ? 'rgba(16, 185, 129, 0.9)' : 'rgba(239, 68, 68, 0.9)'};
        color: white;
        border-radius: 10px;
        font-weight: 600;
        z-index: 10000;
        animation: slideIn 0.3s ease;
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Auto-refresh lecture status
function refreshLectureStatus() {
    const lectureCards = document.querySelectorAll('[data-lecture-id]');
    
    lectureCards.forEach(card => {
        const lectureId = card.dataset.lectureId;
        
        fetch(`../api/lecture_status.php?lecture_id=${lectureId}`)
            .then(response => response.json())
            .then(data => {
                if (data.status) {
                    const statusBadge = card.querySelector('.status-badge');
                    if (statusBadge) {
                        statusBadge.textContent = data.status.charAt(0).toUpperCase() + data.status.slice(1);
                        statusBadge.className = `status-badge status-${data.status}`;
                    }
                }
            })
            .catch(error => console.error('Error fetching lecture status:', error));
    });
}

// Set up auto-refresh
document.addEventListener('DOMContentLoaded', function() {
    // Refresh every 30 seconds
    setInterval(refreshLectureStatus, 30000);
});

// Confirm before starting lecture
function confirmStartLecture(lectureId) {
    if (confirm('Are you sure you want to start this lecture? A secret code will be generated.')) {
        window.location.href = `start_lecture.php?id=${lectureId}`;
    }
}

// =====================================================