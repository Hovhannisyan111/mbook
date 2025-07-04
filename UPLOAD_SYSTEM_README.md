# MBook3 Upload & Admin Approval System

## 🎯 Overview

The MBook3 application now includes a complete upload and admin approval system that allows users to submit audiobooks for review before they appear on the main website.

## 🚀 Features

### For Users:
- **Upload Audiobooks**: Submit audiobooks with title, author, category, description
- **File Upload**: Support for MP3, WAV, M4A audio files (up to 100MB)
- **Cover Images**: Optional cover image upload (JPEG, PNG, GIF, WebP up to 5MB)
- **Upload History**: View status of all your submissions
- **Real-time Status**: See pending, approved, or rejected submissions

### For Admins:
- **Admin Panel**: Dedicated interface for reviewing submissions
- **Audio Preview**: Listen to submitted audiobooks
- **Cover Preview**: View uploaded cover images
- **Approve/Reject**: Make decisions with optional comments
- **Statistics**: View counts of pending, approved, and rejected submissions
- **Filter System**: Filter submissions by status

## 📋 Setup Instructions

### 1. Database Setup
```bash
# Run the upload system setup
/opt/lampp/bin/php backend/setup_upload_system.php
```

### 2. Create Admin User
```bash
# Make the first registered user an admin
/opt/lampp/bin/php backend/make_admin.php
```

### 3. Create Uploads Directory
The system will automatically create the uploads directory, but you can also create it manually:
```bash
mkdir -p uploads/audio uploads/images
chmod 755 uploads uploads/audio uploads/images
```

## 🎮 How to Use

### For Regular Users:

1. **Register/Login**: Create an account or login
2. **Upload Audiobook**: 
   - Go to "Upload Book" in the navigation
   - Fill in book details (title, author, category, description)
   - Upload audio file (MP3, WAV, M4A)
   - Optionally upload a cover image
   - Submit for review
3. **Track Status**: View your upload history and status

### For Admins:

1. **Access Admin Panel**: 
   - Login as an admin user
   - Click "Admin Panel" in the navigation
2. **Review Submissions**:
   - View all pending submissions
   - Listen to audio files
   - View cover images
   - Approve or reject with comments
3. **Manage Content**: Approved books automatically appear on the main page

## 📁 File Structure

```
MBook3/
├── backend/
│   ├── upload/
│   │   ├── submit_book.php          # Handle book submissions
│   │   └── get_user_uploads.php     # Get user's upload history
│   ├── admin/
│   │   ├── check_admin.php          # Check admin status
│   │   ├── get_submissions.php      # Get all submissions
│   │   ├── get_submission_details.php # Get specific submission
│   │   └── review_submission.php    # Approve/reject submissions
│   ├── setup_upload_system.php      # Database setup
│   └── make_admin.php               # Make user admin
├── public/
│   ├── upload.html                  # Upload page
│   ├── admin.html                   # Admin panel
│   ├── js/
│   │   ├── upload.js                # Upload functionality
│   │   └── admin.js                 # Admin panel functionality
│   └── css/
│       ├── upload.css               # Upload page styles
│       └── admin.css                # Admin panel styles
└── uploads/                         # Uploaded files
    ├── audio/                       # Audio files
    └── images/                      # Cover images
```

## 🗄️ Database Schema

### New Tables:

**pending_books** - Stores user submissions
```sql
- id (Primary Key)
- user_id (Foreign Key to users)
- title, author, description, category
- audio_file, cover_image (file paths)
- status (pending/approved/rejected)
- admin_comment, admin_id
- submitted_at, reviewed_at
```

**admin_users** - Tracks admin users
```sql
- id (Primary Key)
- user_id (Foreign Key to users)
- role (admin/super_admin)
- created_at
```

**users** - Updated with admin column
```sql
- is_admin (BOOLEAN) - Added to existing table
```

## 🔧 Configuration

### File Upload Limits
- **Audio Files**: 100MB maximum
- **Image Files**: 5MB maximum
- **Supported Audio**: MP3, WAV, M4A
- **Supported Images**: JPEG, PNG, GIF, WebP

### Security Features
- File type validation
- File size limits
- Unique filename generation
- Admin-only access to review system
- Session-based authentication

## 🎨 User Interface

### Upload Page Features:
- ✅ Form validation
- ✅ File type checking
- ✅ File size validation
- ✅ Image preview
- ✅ File info display
- ✅ Upload progress
- ✅ Upload history

### Admin Panel Features:
- ✅ Statistics dashboard
- ✅ Filter by status
- ✅ Audio player preview
- ✅ Image preview
- ✅ Approval/rejection with comments
- ✅ Real-time updates

## 🚨 Troubleshooting

### Common Issues:

1. **Upload Fails**:
   - Check file size limits
   - Verify file type is supported
   - Ensure uploads directory has write permissions

2. **Admin Panel Not Accessible**:
   - Run `make_admin.php` to create admin user
   - Check if user has `is_admin = TRUE` in database

3. **Audio Not Playing**:
   - Check if audio file was uploaded correctly
   - Verify file path in database
   - Check browser console for errors

4. **Database Errors**:
   - Run setup scripts again
   - Check MySQL service is running
   - Verify database permissions

## 🔄 Workflow

1. **User Uploads** → File saved to `uploads/` directory
2. **Admin Reviews** → Listens to audio, views details
3. **Admin Approves** → Book added to main `books` table
4. **Book Appears** → Shows on home page for all users

## 📝 Notes

- Approved books are automatically added to the main books table
- Rejected books remain in pending_books with admin comments
- Users can view their upload history and status
- Admin comments are visible to users for rejected submissions
- All file uploads are validated for security

## 🎉 Success!

Your MBook3 application now has a complete upload and approval system! Users can submit audiobooks, and admins can review and approve them before they appear on the main website. 