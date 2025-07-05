# MBook3 - Audiobook Application

A modern audiobook application with user authentication and audio playback functionality.

## Features

- **User Authentication**: Login and registration system
- **Audiobook Library**: Browse and listen to audiobooks
- **Audio Player**: Full-featured audio player with progress tracking
- **User Profiles**: Save and track reading progress
- **Responsive Design**: Works on desktop and mobile devices

## Setup Instructions

### Prerequisites

1. **XAMPP/LAMPP**: Make sure you have XAMPP or LAMPP installed
2. **MySQL Database**: Ensure MySQL service is running
3. **PHP**: PHP should be enabled in your web server

### Installation

1. **Clone/Download**: Place this project in your `htdocs` folder
2. **Database Setup**: 
   - Open your web browser and navigate to: `http://localhost/MBook3/backend/setup_database.php`
   - This will create the necessary database tables and insert sample data

3. **Database Configuration**: 
   - Edit `backend/db.php` if you need to change database credentials
   - Default settings:
     - Host: localhost
     - Database: MBook
     - Username: root
     - Password: (empty)

4. **Audio Files**: 
   - Ensure the audio files (`.wav`) are in the root directory
   - Current audio files: `isahakyan.wav`, `zeytun.wav`, `avarayr.wav`

### Usage

1. **Start the Application**: 
   - Navigate to: `http://localhost/MBook3/public/`
   - You'll be redirected to the login page

2. **Register/Login**: 
   - Create a new account or login with existing credentials
   - After successful login, you'll be redirected to the home page

3. **Listen to Audiobooks**: 
   - Click the "Listen" button on any book card
   - You'll be taken to the book page with the audio player
   - Use the player controls to play, pause, and navigate through the audio

4. **Navigation**: 
   - Use the profile button to access your profile page
   - Use the logout button to sign out

## File Structure

```
MBook3/
├── backend/
│   ├── auth/
│   │   ├── login.php
│   │   ├── register.php
│   │   ├── logout.php
│   │   ├── session.php
│   │   └── check_session.php
│   ├── db.php
│   └── setup_database.php
├── public/
│   ├── css/
│   │   ├── auth.css
│   │   ├── home.css
│   │   ├── book.css
│   │   ├── profile.css
│   │   └── upload.css
│   ├── js/
│   │   ├── auth.js
│   │   └── main.js
│   ├── home.html
│   ├── book.html
│   ├── profile.html
│   ├── login.html
│   ├── register.html
│   └── upload.html
├── images/
│   ├── logo.png
│   ├── isahakyan.jpg
│   ├── zeytun.jpg
│   └── avarayr.jpg
├── isahakyan.wav
├── zeytun.wav
├── avarayr.wav
└── README.md
```

## Features Overview

### Authentication System
- User registration and login
- Session management
- Secure password hashing
- Automatic redirect to login for unauthenticated users

### Audiobook Player
- Play/pause functionality
- Progress bar with seek capability
- Time display (current time / total duration)
- Audio file mapping to book titles

### User Interface
- Modern, responsive design
- Dark theme with blue accents
- Mobile-friendly layout
- Intuitive navigation

### Database Schema
- **users**: User accounts and authentication
- **books**: Audiobook metadata and file paths
- **user_books**: User's saved books and reading progress

## Troubleshooting

1. **Database Connection Issues**: 
   - Check if MySQL is running
   - Verify database credentials in `backend/db.php`
   - Ensure the 'MBook' database exists

2. **Audio Not Playing**: 
   - Check if audio files exist in the root directory
   - Verify file permissions
   - Check browser console for errors

3. **Login Issues**: 
   - Run the database setup script first
   - Check if sessions are enabled in PHP
   - Verify the auth folder permissions

## Browser Compatibility

- Chrome (recommended)
- Firefox
- Safari
- Edge

## License

This project is for educational purposes. 