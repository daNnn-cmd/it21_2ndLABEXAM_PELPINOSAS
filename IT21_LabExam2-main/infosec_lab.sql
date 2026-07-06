-- ============================================
-- MIGRATION QUERY: Convert to Improved Secure Schema
-- ============================================

USE infosec_lab;

-- Drop existing tables
DROP TABLE IF EXISTS students;
DROP TABLE IF EXISTS users;

-- ============================================
-- TABLE: users (with password hashing support)
-- ============================================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    INDEX idx_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: courses (normalized course data)
-- ============================================
CREATE TABLE courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_code VARCHAR(20) NOT NULL UNIQUE,
    course_name VARCHAR(100) NOT NULL,
    course_description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_course_code (course_code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: students (with foreign key to courses)
-- ============================================
CREATE TABLE students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(20) NOT NULL UNIQUE,
    fullname VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    course_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (course_id) REFERENCES courses(id) 
        ON DELETE RESTRICT 
        ON UPDATE CASCADE,
    
    INDEX idx_student_id (student_id),
    INDEX idx_email (email),
    INDEX idx_course_id (course_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Insert default admin user with hashed password
-- Password: admin123
-- ============================================
INSERT INTO users (username, password) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- ============================================
-- Insert sample courses
-- ============================================
INSERT INTO courses (course_code, course_name, course_description) VALUES
('BSIT', 'Bachelor of Science in Information Technology', 'Focus on software development and network administration'),
('BSCS', 'Bachelor of Science in Computer Science', 'Focus on theoretical computing and algorithms'),
('IS', 'Information Systems', 'Focus on business technology integration'),
('ACT', 'Associate in Computer Technology', '2-year technical program');

-- ============================================
-- Insert sample students
-- ============================================
INSERT INTO students (student_id, fullname, email, course_id) VALUES
('2023-001', 'John Doe', 'john.doe@example.com', 1),
('2023-002', 'Jane Smith', 'jane.smith@example.com', 2),
('2023-003', 'Bob Johnson', 'bob.johnson@example.com', 1),
('2023-004', 'Alice Williams', 'alice.williams@example.com', 3);