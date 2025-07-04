// upload.js - Handles audiobook upload functionality

document.addEventListener('DOMContentLoaded', function() {
    const uploadForm = document.getElementById('uploadForm');
    const audioFileInput = document.getElementById('audioFile');
    const coverImageInput = document.getElementById('coverImage');
    const fileInfo = document.getElementById('fileInfo');
    const imagePreview = document.getElementById('imagePreview');

    // File size validation
    const MAX_FILE_SIZE = 100 * 1024 * 1024; // 100MB
    const ALLOWED_AUDIO_TYPES = [
        'audio/mp3', 'audio/mpeg', 'audio/wav', 'audio/x-wav', 'audio/m4a', 'audio/x-m4a', 'audio/mp4', 'audio/x-mp4'
    ];
    const ALLOWED_AUDIO_EXTENSIONS = ['.mp3', '.wav', '.m4a'];
    const ALLOWED_IMAGE_TYPES = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

    // Audio file input handler
    audioFileInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            // Debug information
            console.log('File selected:', file.name);
            console.log('File type:', file.type);
            console.log('File size:', file.size);
            
            // Validate file type or extension
            const fileTypeOk = ALLOWED_AUDIO_TYPES.includes(file.type);
            const fileName = file.name.toLowerCase();
            const extOk = ALLOWED_AUDIO_EXTENSIONS.some(ext => fileName.endsWith(ext));
            
            console.log('File type OK:', fileTypeOk);
            console.log('File extension OK:', extOk);
            console.log('Allowed types:', ALLOWED_AUDIO_TYPES);
            console.log('Allowed extensions:', ALLOWED_AUDIO_EXTENSIONS);
            
            if (!fileTypeOk && !extOk) {
                alert('Please select a valid audio file (MP3, WAV, or M4A)\n\nDebug info:\nFile type: ' + file.type + '\nFile name: ' + file.name);
                audioFileInput.value = '';
                fileInfo.innerHTML = '';
                return;
            }

            // Validate file size
            if (file.size > MAX_FILE_SIZE) {
                alert('File size must be less than 100MB');
                audioFileInput.value = '';
                fileInfo.innerHTML = '';
                return;
            }

            // Display file info
            const fileSize = (file.size / (1024 * 1024)).toFixed(2);
            fileInfo.innerHTML = `
                <div class="file-details">
                    <strong>${file.name}</strong><br>
                    Size: ${fileSize} MB<br>
                    Type: ${file.type}
                </div>
            `;
        }
    });

    // Cover image input handler
    coverImageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            // Validate file type
            if (!ALLOWED_IMAGE_TYPES.includes(file.type)) {
                alert('Please select a valid image file (JPEG, PNG, GIF, or WebP)');
                coverImageInput.value = '';
                imagePreview.innerHTML = '';
                return;
            }

            // Validate file size (5MB for images)
            if (file.size > 5 * 1024 * 1024) {
                alert('Image file size must be less than 5MB');
                coverImageInput.value = '';
                imagePreview.innerHTML = '';
                return;
            }

            // Display image preview
            const reader = new FileReader();
            reader.onload = function(e) {
                imagePreview.innerHTML = `
                    <div class="preview-image">
                        <img src="${e.target.result}" alt="Cover preview" style="max-width: 200px; max-height: 200px;">
                    </div>
                `;
            };
            reader.readAsDataURL(file);
        }
    });

    // Form submission handler
    uploadForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(uploadForm);
        
        // Show loading state
        const submitBtn = uploadForm.querySelector('.submit-btn');
        const originalText = submitBtn.textContent;
        submitBtn.textContent = 'Uploading...';
        submitBtn.disabled = true;

        // Send upload request
        fetch('../backend/upload/submit_book.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Your audiobook has been submitted for review! You will be notified once it\'s approved.');
                uploadForm.reset();
                fileInfo.innerHTML = '';
                imagePreview.innerHTML = '';
                loadUserUploads(); // Refresh the uploads list
            } else {
                alert('Upload failed: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Upload error:', error);
            alert('Upload failed. Please try again.');
        })
        .finally(() => {
            submitBtn.textContent = originalText;
            submitBtn.disabled = false;
        });
    });

    // Load user's uploads
    function loadUserUploads() {
        fetch('../backend/upload/get_user_uploads.php')
            .then(response => response.json())
            .then(data => {
                const statusList = document.getElementById('statusList');
                const uploadStatus = document.getElementById('uploadStatus');
                
                if (data.uploads && data.uploads.length > 0) {
                    uploadStatus.style.display = 'block';
                    statusList.innerHTML = data.uploads.map(upload => `
                        <div class="status-item ${upload.status}">
                            <div class="status-header">
                                <h4>${upload.title}</h4>
                                <span class="status-badge ${upload.status}">${upload.status}</span>
                            </div>
                            <div class="status-details">
                                <p><strong>Author:</strong> ${upload.author}</p>
                                <p><strong>Category:</strong> ${upload.category}</p>
                                <p><strong>Submitted:</strong> ${new Date(upload.submitted_at).toLocaleDateString()}</p>
                                ${upload.admin_comment ? `<p><strong>Admin Comment:</strong> ${upload.admin_comment}</p>` : ''}
                            </div>
                        </div>
                    `).join('');
                } else {
                    uploadStatus.style.display = 'none';
                }
            })
            .catch(error => {
                console.error('Error loading uploads:', error);
            });
    }

    // Load uploads on page load
    loadUserUploads();
}); 