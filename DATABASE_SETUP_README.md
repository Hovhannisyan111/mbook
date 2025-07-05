# MBook Database Setup Guide

This guide will help you set up the complete database for the MBook audiobook application. The database includes all necessary tables, sample data, and advanced features for a fully functional audiobook platform.

## 📋 Prerequisites

Before setting up the database, make sure you have:

1. **XAMPP/LAMPP** installed and running
2. **MySQL** service started
3. **PHP** enabled in your web server
4. The MBook project files in your `htdocs` folder

## 🚀 Quick Setup (Recommended)

### Method 1: Automatic Installation (Easiest)

1. **Start your web server** (XAMPP/LAMPP)
2. **Open your browser** and navigate to:
   ```
   http://localhost/mbook/install_database.php
   ```
3. **Follow the on-screen instructions**
4. **Done!** Your database is ready to use

### Method 2: Manual SQL Execution

1. **Open phpMyAdmin**:
   ```
   http://localhost/phpmyadmin
   ```
2. **Create a new database** named `MBook`
3. **Import the SQL file**:
   - Click on the `MBook` database
   - Go to the "Import" tab
   - Choose the `database_setup.sql` file
   - Click "Go" to execute

### Method 3: Command Line

1. **Open terminal/command prompt**
2. **Navigate to your project directory**
3. **Run the MySQL command**:
   ```bash
   mysql -u root -p < database_setup.sql
   ```

## 📊 Database Structure

The database includes the following tables:

### Core Tables

| Table | Description | Records |
|-------|-------------|---------|
| `users` | User accounts and authentication | 4 (1 admin + 3 regular) |
| `books` | Audiobook metadata and file paths | 6 sample books |
| `user_books` | User reading progress and book relationships | 6 sample relationships |
| `categories` | Book categories for organization | 6 categories |

### Advanced Features

- **Views**: Pre-built queries for common operations
- **Stored Procedures**: Automated book and progress management
- **Triggers**: Automatic status updates
- **Indexes**: Optimized performance for large datasets

## 🔐 Default Login Credentials

After setup, you can use these accounts to test the application:

### Admin Account
- **Email**: `admin@mbook.com`
- **Password**: `admin123`
- **Access**: Full administrative privileges

### Regular User Accounts
- **Email**: `john@example.com` / **Password**: `user123`
- **Email**: `jane@example.com` / **Password**: `user123`
- **Email**: `mike@example.com` / **Password**: `user123`

## 📚 Sample Data Included

### Books Available
1. **Avetik Isahakyan** - Literature (45 min)
2. **First Zeitun Resistance** - History (60 min)
3. **The Battle of Avarayr** - History (55 min)
4. **Armenian Folk Tales** - Literature (30 min)
5. **The Armenian Genocide: A History** - History (90 min)
6. **Modern Armenian Poetry** - Literature (40 min)

### Sample User Progress
- John Doe: Reading "Avetik Isahakyan" (25% complete)
- Jane Smith: Finished "Avetik Isahakyan", reading "The Battle of Avarayr" (50% complete)
- Mike Wilson: Reading "First Zeitun Resistance" (10% complete)

## 🛠️ Database Features

### Views
- `user_book_progress`: Shows user reading progress with percentages
- `book_statistics`: Displays book popularity and completion statistics

### Stored Procedures
- `AddBook()`: Add new books to the library
- `UpdateUserProgress()`: Update user reading progress

### Triggers
- Automatic status update to "finished" when progress reaches 95%

## 🔧 Configuration

### Database Connection
The application uses these default settings in `backend/db.php`:
- **Host**: `localhost`
- **Database**: `MBook`
- **Username**: `root`
- **Password**: (empty)

### Customizing Database Settings
If you need to change the database credentials:

1. **Edit** `backend/db.php`
2. **Update** the connection parameters
3. **Modify** `install_database.php` if using automatic installation

## 🚨 Troubleshooting

### Common Issues

#### 1. "Database connection failed"
- **Solution**: Make sure MySQL is running in XAMPP/LAMPP
- **Check**: MySQL service status in control panel

#### 2. "Access denied for user 'root'"
- **Solution**: Check if MySQL password is set
- **Fix**: Update credentials in `backend/db.php` and `install_database.php`

#### 3. "Table doesn't exist"
- **Solution**: Run the installation script again
- **Check**: Make sure you're using the correct database

#### 4. "SQL file not found"
- **Solution**: Ensure `database_setup.sql` is in the project root
- **Check**: File permissions and location

### Verification Steps

After installation, verify everything works:

1. **Check tables exist**:
   ```sql
   SHOW TABLES;
   ```

2. **Verify sample data**:
   ```sql
   SELECT COUNT(*) FROM users;
   SELECT COUNT(*) FROM books;
   ```

3. **Test login**:
   - Go to `http://localhost/mbook/public/`
   - Try logging in with the provided credentials

## 📈 Performance Optimization

The database includes several optimizations:

- **Indexes** on frequently queried columns
- **Foreign key constraints** for data integrity
- **Efficient data types** for optimal storage
- **Prepared statements** in PHP code for security

## 🔄 Updating the Database

### Adding New Books
Use the provided stored procedure:
```sql
CALL AddBook('Book Title', 'Author Name', 'Description', 'cover.jpg', 'audio.wav', 'Category', 60, 20.5);
```

### Adding New Users
```sql
INSERT INTO users (username, email, password_hash) 
VALUES ('newuser', 'user@example.com', '$2y$10$...');
```

## 📞 Support

If you encounter any issues:

1. **Check the troubleshooting section** above
2. **Verify all prerequisites** are met
3. **Review error messages** carefully
4. **Ensure file permissions** are correct

## 🎉 Success!

Once the database is set up successfully, you can:

1. **Access the application**: `http://localhost/mbook/public/`
2. **Login with provided credentials**
3. **Browse and listen to audiobooks**
4. **Track your reading progress**
5. **Enjoy the full audiobook experience!**

---

**Note**: This database setup is designed to work with the existing MBook application. Make sure all audio files and images referenced in the sample data are present in your project directory. 