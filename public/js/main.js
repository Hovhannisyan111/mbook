// main.js - Handles session management and audiobook functionality

document.addEventListener('DOMContentLoaded', function() {
    // Check session status
    checkSession();
    
    // Set up event listeners
    setupEventListeners();
});

function checkSession() {
    fetch('../backend/auth/check_session.php')
        .then(response => response.json())
        .then(data => {
            if (!data.logged_in) {
                // Redirect to login if not logged in
                const currentPath = window.location.pathname;
                const isLoginPage = currentPath.includes('login.html');
                const isRegisterPage = currentPath.includes('register.html');
                
                if (!isLoginPage && !isRegisterPage) {
                    window.location.href = 'login.html';
                }
            } else {
                // Update UI with user info
                updateUserInterface(data.username);
            }
        })
        .catch(error => {
            console.error('Session check failed:', error);
            // If session check fails, redirect to login
            const currentPath = window.location.pathname;
            const isLoginPage = currentPath.includes('login.html');
            const isRegisterPage = currentPath.includes('register.html');
            
            if (!isLoginPage && !isRegisterPage) {
                window.location.href = 'login.html';
            }
        });
}

function updateUserInterface(username) {
    // Update profile button or any user-specific UI elements
    const profileBtn = document.querySelector('.profile-btn');
    if (profileBtn) {
        profileBtn.setAttribute('title', `Profile - ${username}`);
    }
    
    // Update profile username display
    const profileUsername = document.getElementById('profileUsername');
    if (profileUsername) {
        profileUsername.textContent = username;
    }
    
    // Admin panel access removed - only dedicated admin account can access admin panel
}

function setupEventListeners() {
    // Logo click functionality - navigate to home page
    const logos = document.querySelectorAll('.logo-img, .sidebar-logo img');
    logos.forEach(logo => {
        logo.style.cursor = 'pointer';
        logo.addEventListener('click', function() {
            window.location.href = 'home.html';
        });
    });

    // Profile button click
    const profileBtn = document.querySelector('.profile-btn');
    if (profileBtn) {
        profileBtn.addEventListener('click', function() {
            window.location.href = 'profile.html';
        });
    }

    // Listen buttons for audiobooks
    const playButtons = document.querySelectorAll('.play-btn');
    playButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Get book information from the card
            const bookCard = button.closest('.book-card');
            const bookTitle = bookCard.querySelector('.book-title').textContent;
            const bookAuthor = bookCard.querySelector('.book-author').textContent;
            const bookCover = bookCard.querySelector('.book-cover').src;
            
            // Store book info in sessionStorage for the book page
            sessionStorage.setItem('currentBook', JSON.stringify({
                title: bookTitle,
                author: bookAuthor,
                cover: bookCover
            }));
            
            // Navigate to book page
            window.location.href = 'book.html';
        });
    });

    // Logout functionality
    const logoutBtn = document.querySelector('.logout-btn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', function(e) {
            e.preventDefault();
            fetch('../backend/auth/logout.php')
                .then(() => {
                    window.location.href = 'login.html';
                })
                .catch(error => {
                    console.error('Logout failed:', error);
                    window.location.href = 'login.html';
                });
        });
    }
}

// Audio player functionality
function initializeAudioPlayer() {
    const audioPlayer = document.querySelector('.media-player');
    if (!audioPlayer) return;

    const playBtn = audioPlayer.querySelector('.play-main');
    const progressBar = audioPlayer.querySelector('.progress-bar');
    const currentTimeSpan = audioPlayer.querySelector('.current-time');
    const durationSpan = audioPlayer.querySelector('.duration');
    
    // Create audio element
    const audio = new Audio();
    
    // Get book info and set audio source
    const bookInfo = JSON.parse(sessionStorage.getItem('currentBook') || '{}');
    if (bookInfo.title && bookInfo.audioFile) {
        // Set audio source from book info - handle both relative and absolute paths
        if (bookInfo.audioFile.startsWith('../')) {
            audio.src = bookInfo.audioFile;
        } else {
            audio.src = '../' + bookInfo.audioFile;
        }
    }

    let isPlaying = false;

    // Play/Pause functionality
    if (playBtn) {
        playBtn.addEventListener('click', function() {
            if (isPlaying) {
                audio.pause();
                playBtn.innerHTML = '<span>&#9654;</span>';
            } else {
                audio.play();
                playBtn.innerHTML = '<span>&#10074;&#10074;</span>';
            }
            isPlaying = !isPlaying;
        });
    }

    // Update progress bar
    audio.addEventListener('timeupdate', function() {
        if (audio.duration) {
            const progress = (audio.currentTime / audio.duration) * 100;
            progressBar.value = progress;
            
            // Update time display
            currentTimeSpan.textContent = formatTime(audio.currentTime);
            durationSpan.textContent = formatTime(audio.duration);
        }
    });

    // Progress bar control
    if (progressBar) {
        progressBar.addEventListener('input', function() {
            const time = (progressBar.value / 100) * audio.duration;
            audio.currentTime = time;
        });
    }

    // Audio ended
    audio.addEventListener('ended', function() {
        isPlaying = false;
        if (playBtn) {
            playBtn.innerHTML = '<span>&#9654;</span>';
        }
    });
}

function formatTime(seconds) {
    const minutes = Math.floor(seconds / 60);
    const remainingSeconds = Math.floor(seconds % 60);
    return `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`;
} 