-- MySQL Initialization Script for MBG Laravel Application
-- This script runs when the MySQL container starts for the first time

-- Create database if it doesn't exist
CREATE DATABASE IF NOT EXISTS `mbg_production` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Create application user with appropriate privileges
CREATE USER IF NOT EXISTS 'mbg_user'@'%' IDENTIFIED BY 'secure_password_123';
GRANT ALL PRIVILEGES ON `mbg_production`.* TO 'mbg_user'@'%';
GRANT SELECT ON `performance_schema`.* TO 'mbg_user'@'%';

-- Flush privileges to apply changes
FLUSH PRIVILEGES;

-- Set global SQL mode to be more compatible
SET GLOBAL sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));

-- Set timezone to Asia/Jakarta
SET GLOBAL time_zone = '+7:00';

-- Log initialization completion
SELECT 'MySQL initialization completed successfully' AS status;
