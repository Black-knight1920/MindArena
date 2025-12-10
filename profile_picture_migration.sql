-- Migration script to add profile_picture column to user table
-- Run this script in your MySQL database to add the profile picture functionality

-- Add profile_picture column to user table
ALTER TABLE `user` 
ADD COLUMN `profile_picture` VARCHAR(255) NULL 
AFTER `banned`;

-- Verify the column was added
-- SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE, COLUMN_DEFAULT 
-- FROM INFORMATION_SCHEMA.COLUMNS 
-- WHERE TABLE_SCHEMA = 'projetj' AND TABLE_NAME = 'user' AND COLUMN_NAME = 'profile_picture';

