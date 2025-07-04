// admin.js - Handles admin panel functionality

let currentSubmission = null;

document.addEventListener('DOMContentLoaded', function() {
    // Check if user is admin
    checkAdminStatus();
    
    // Load submissions
    loadSubmissions();
    
    // Set up event listeners
    setupEventListeners();
});

function checkAdminStatus() {
    fetch('../backend/admin/check_admin.php')
        .then(response => response.json())
        .then(data => {
            if (!data.is_admin) {
                alert('Access denied. Admin privileges required.');
                window.location.href = 'home.html';
            }
        })
        .catch(error => {
            console.error('Admin check failed:', error);
            window.location.href = 'home.html';
        });
}

function setupEventListeners() {
    // Status filter
    const statusFilter = document.getElementById('statusFilter');
    statusFilter.addEventListener('change', function() {
        loadSubmissions();
    });

    // Refresh button
    const refreshBtn = document.getElementById('refreshBtn');
    refreshBtn.addEventListener('click', function() {
        loadSubmissions();
    });
}

function loadSubmissions() {
    const statusFilter = document.getElementById('statusFilter').value;
    
    fetch(`../backend/admin/get_submissions.php?status=${statusFilter}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displaySubmissions(data.submissions);
                updateStats(data.stats);
            } else {
                console.error('Failed to load submissions:', data.message);
            }
        })
        .catch(error => {
            console.error('Error loading submissions:', error);
        });
}

function displaySubmissions(submissions) {
    const submissionsList = document.getElementById('submissionsList');
    
    if (submissions.length === 0) {
        submissionsList.innerHTML = '<div class="no-submissions">No submissions found.</div>';
        return;
    }

    submissionsList.innerHTML = submissions.map(submission => `
        <div class="submission-card ${submission.status}">
            <div class="submission-header">
                <h3>${submission.title}</h3>
                <span class="status-badge ${submission.status}">${submission.status}</span>
            </div>
            <div class="submission-details">
                <p><strong>Author:</strong> ${submission.author}</p>
                <p><strong>Category:</strong> ${submission.category}</p>
                <p><strong>Submitted by:</strong> ${submission.username}</p>
                <p><strong>Date:</strong> ${new Date(submission.submitted_at).toLocaleDateString()}</p>
                ${submission.admin_comment ? `<p><strong>Admin Comment:</strong> ${submission.admin_comment}</p>` : ''}
            </div>
            <div class="submission-actions">
                ${submission.status === 'pending' ? 
                    `<button class="review-btn" onclick="openReviewModal(${submission.id})">Review</button>` : 
                    `<button class="view-btn" onclick="openReviewModal(${submission.id})">View Details</button>`
                }
            </div>
        </div>
    `).join('');
}

function updateStats(stats) {
    document.getElementById('pendingCount').textContent = stats.pending || 0;
    document.getElementById('approvedCount').textContent = stats.approved || 0;
    document.getElementById('rejectedCount').textContent = stats.rejected || 0;
}

function openReviewModal(submissionId) {
    fetch(`../backend/admin/get_submission_details.php?id=${submissionId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                currentSubmission = data.submission;
                displaySubmissionDetails(data.submission);
                document.getElementById('reviewModal').style.display = 'block';
            } else {
                alert('Failed to load submission details');
            }
        })
        .catch(error => {
            console.error('Error loading submission details:', error);
        });
}

function displaySubmissionDetails(submission) {
    document.getElementById('modalTitle').textContent = submission.title;
    document.getElementById('modalAuthor').textContent = submission.author;
    document.getElementById('modalCategory').textContent = submission.category;
    document.getElementById('modalDescription').textContent = submission.description || 'No description provided';
    document.getElementById('modalUser').textContent = submission.username;
    document.getElementById('modalDate').textContent = new Date(submission.submitted_at).toLocaleDateString();

    // Set audio source
    const audioPlayer = document.getElementById('audioPlayer');
    audioPlayer.src = '../' + submission.audio_file;

    // Display cover image if available
    const coverPreview = document.getElementById('coverPreview');
    if (submission.cover_image) {
        coverPreview.innerHTML = `
            <h4>Cover Image</h4>
            <img src="../${submission.cover_image}" alt="Book cover" style="max-width: 200px; max-height: 200px;">
        `;
    } else {
        coverPreview.innerHTML = '<h4>Cover Image</h4><p>No cover image provided</p>';
    }

    // Show/hide decision buttons based on status
    const decisionButtons = document.querySelector('.decision-buttons');
    if (submission.status === 'pending') {
        decisionButtons.style.display = 'flex';
    } else {
        decisionButtons.style.display = 'none';
    }
}

function closeModal() {
    document.getElementById('reviewModal').style.display = 'none';
    currentSubmission = null;
    document.getElementById('adminComment').value = '';
}

function approveSubmission() {
    if (!currentSubmission) return;
    
    const comment = document.getElementById('adminComment').value;
    
    fetch('../backend/admin/review_submission.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            submission_id: currentSubmission.id,
            action: 'approve',
            comment: comment
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Submission approved successfully!');
            closeModal();
            loadSubmissions();
        } else {
            alert('Failed to approve submission: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error approving submission:', error);
        alert('Failed to approve submission');
    });
}

function rejectSubmission() {
    if (!currentSubmission) return;
    
    const comment = document.getElementById('adminComment').value;
    
    if (!comment.trim()) {
        alert('Please provide a reason for rejection.');
        return;
    }
    
    fetch('../backend/admin/review_submission.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            submission_id: currentSubmission.id,
            action: 'reject',
            comment: comment
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Submission rejected successfully!');
            closeModal();
            loadSubmissions();
        } else {
            alert('Failed to reject submission: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error rejecting submission:', error);
        alert('Failed to reject submission');
    });
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('reviewModal');
    if (event.target === modal) {
        closeModal();
    }
} 