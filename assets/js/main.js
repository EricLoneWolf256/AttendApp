// ============================================================================
// FILE: assets/js/main.js - Landing Page JavaScript
// ============================================================================

function openModal(role) {
    const modalId = role + 'Modal';
    document.getElementById(modalId).classList.add('active');
}

function closeModal(role) {
    const modalId = role + 'Modal';
    document.getElementById(modalId).classList.remove('active');
}

// Close modal when clicking outside
document.addEventListener('DOMContentLoaded', function() {
    const modals = document.querySelectorAll('.modal');
    
    modals.forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.remove('active');
            }
        });
    });

    // Close on ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            modals.forEach(modal => modal.classList.remove('active'));
        }
    });
});