-- Tạo cơ sở dữ liệu
CREATE DATABASE IF NOT EXISTS url_shortener
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE url_shortener;

-- Tạo bảng urls
CREATE TABLE IF NOT EXISTS urls (
    id INT AUTO_INCREMENT PRIMARY KEY,
    original_url VARCHAR(2048) NOT NULL,
    short_code VARCHAR(20) NOT NULL,
    note TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_short_code (short_code),
    INDEX idx_short_code (short_code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tạo bảng notepads
CREATE TABLE IF NOT EXISTS notepads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    content TEXT NOT NULL,
    short_code VARCHAR(20) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_short_code (short_code),
    INDEX idx_short_code (short_code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;