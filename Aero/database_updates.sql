-- Add email and phone columns to passengers table
ALTER TABLE passengers
ADD COLUMN email VARCHAR(100) NOT NULL AFTER age,
ADD COLUMN phone VARCHAR(20) DEFAULT NULL AFTER email;
