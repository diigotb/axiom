-- Add sales_knowledge column to contactcenter_device table for OmniU Intelligence
-- Run this SQL to add the column if the migration hasn't been executed yet

ALTER TABLE `tblcontactcenter_device` 
ADD COLUMN `sales_knowledge` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL 
COMMENT 'Product information, sales pitch, and context for OmniU Intelligence strategies' 
AFTER `dev_instance_name`;
