-- ============================================================
--  Library API – Database Setup
--  Run this file once in phpMyAdmin or MySQL CLI:
--  mysql -u root -p < setup.sql
-- ============================================================

CREATE DATABASE IF NOT EXISTS library_db;
USE library_db;

CREATE TABLE IF NOT EXISTS books (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    title       VARCHAR(255)  NOT NULL,
    author      VARCHAR(255)  NOT NULL,
    genre       VARCHAR(100)  DEFAULT 'Unknown',
    year        YEAR          DEFAULT NULL,
    available   TINYINT(1)    DEFAULT 1,
    created_at  TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Sample data
INSERT INTO books (title, author, genre, year, available) VALUES
('The Great Gatsby',         'F. Scott Fitzgerald', 'Fiction',       1925, 1),
('To Kill a Mockingbird',    'Harper Lee',          'Fiction',       1960, 1),
('1984',                     'George Orwell',        'Dystopian',     1949, 0),
('Clean Code',               'Robert C. Martin',    'Technology',    2008, 1),
('The Pragmatic Programmer', 'David Thomas',        'Technology',    1999, 1);
