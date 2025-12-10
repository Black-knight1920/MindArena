-- Migration script to add banned status column to user table
-- Run this script in your MySQL database to add the banned functionality

-- Add banned column to user table (defaults to 0 = not banned)
ALTER TABLE `user` 
ADD COLUMN `banned` TINYINT(1) NOT NULL DEFAULT 0 
AFTER `donation`;

-- Optional: Add an index for better query performance when checking banned status
CREATE INDEX `idx_banned` ON `user` (`banned`);

-- Verify the column was added
-- SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE, COLUMN_DEFAULT 
-- FROM INFORMATION_SCHEMA.COLUMNS 
-- WHERE TABLE_SCHEMA = 'projetj' AND TABLE_NAME = 'user' AND COLUMN_NAME = 'banned';

