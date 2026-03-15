-- Migration script to add Ad Set support to Ads Analytics
-- Run this SQL script in your database

-- Step 1: Create ad_sets table if it doesn't exist
CREATE TABLE IF NOT EXISTS `tblcontactcenter_ads_sets` (
    `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `description` TEXT DEFAULT NULL,
    `is_active` TINYINT(1) DEFAULT 1,
    `staffid` INT(11) DEFAULT NULL,
    `date_created` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `date_updated` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Step 2: Add ad_set_id column to creatives table if it doesn't exist
SET @dbname = DATABASE();
SET @tablename = 'tblcontactcenter_ads_creatives';
SET @columnname = 'ad_set_id';
SET @preparedStatement = (SELECT IF(
    (
        SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
        WHERE
            (TABLE_SCHEMA = @dbname)
            AND (TABLE_NAME = @tablename)
            AND (COLUMN_NAME = @columnname)
    ) > 0,
    'SELECT 1',
    CONCAT('ALTER TABLE `', @tablename, '` 
        ADD COLUMN `ad_set_id` INT(11) DEFAULT NULL COMMENT ''Ad Set this creative belongs to (optional)'' AFTER `media_id`')
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Step 3: Add foreign key for ad_set_id if it doesn't exist
SET @tablename = 'tblcontactcenter_ads_creatives';
SET @columnname = 'ad_set_id';
SET @fk_name = 'fk_creatives_ad_set';
SET @preparedStatement = (SELECT IF(
    (
        SELECT COUNT(*) FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
        WHERE
            (TABLE_SCHEMA = DATABASE())
            AND (TABLE_NAME = @tablename)
            AND (COLUMN_NAME = @columnname)
            AND (REFERENCED_TABLE_NAME = 'tblcontactcenter_ads_sets')
    ) > 0,
    'SELECT 1',
    CONCAT('ALTER TABLE `', @tablename, '` 
        ADD CONSTRAINT `', @fk_name, '` 
        FOREIGN KEY (`ad_set_id`) 
        REFERENCES `tblcontactcenter_ads_sets`(`id`) 
        ON DELETE SET NULL')
));
PREPARE fkIfNotExists FROM @preparedStatement;
EXECUTE fkIfNotExists;
DEALLOCATE PREPARE fkIfNotExists;

-- Step 4: Add budget_type column to investments table if it doesn't exist
SET @tablename = 'tblcontactcenter_ads_investments';
SET @columnname = 'budget_type';
SET @preparedStatement = (SELECT IF(
    (
        SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
        WHERE
            (TABLE_SCHEMA = DATABASE())
            AND (TABLE_NAME = @tablename)
            AND (COLUMN_NAME = @columnname)
    ) > 0,
    'SELECT 1',
    CONCAT('ALTER TABLE `', @tablename, '` 
        ADD COLUMN `budget_type` ENUM(''creative'', ''ad_set'') DEFAULT ''creative'' COMMENT ''Type of budget: per creative or per ad set'' AFTER `id`')
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Step 5: Update existing investments to have 'creative' as budget_type
UPDATE `tblcontactcenter_ads_investments` SET `budget_type` = 'creative' WHERE `budget_type` IS NULL;

-- Step 6: Add ad_set_id column to investments table if it doesn't exist
SET @tablename = 'tblcontactcenter_ads_investments';
SET @columnname = 'ad_set_id';
SET @preparedStatement = (SELECT IF(
    (
        SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
        WHERE
            (TABLE_SCHEMA = DATABASE())
            AND (TABLE_NAME = @tablename)
            AND (COLUMN_NAME = @columnname)
    ) > 0,
    'SELECT 1',
    CONCAT('ALTER TABLE `', @tablename, '` 
        ADD COLUMN `ad_set_id` INT(11) DEFAULT NULL COMMENT ''Ad Set ID (if budget_type is ad_set)'' AFTER `creative_id`')
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Step 7: Add foreign key for ad_set_id in investments if it doesn't exist
SET @tablename = 'tblcontactcenter_ads_investments';
SET @columnname = 'ad_set_id';
SET @fk_name = 'fk_investments_ad_set';
SET @preparedStatement = (SELECT IF(
    (
        SELECT COUNT(*) FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
        WHERE
            (TABLE_SCHEMA = DATABASE())
            AND (TABLE_NAME = @tablename)
            AND (COLUMN_NAME = @columnname)
            AND (REFERENCED_TABLE_NAME = 'tblcontactcenter_ads_sets')
    ) > 0,
    'SELECT 1',
    CONCAT('ALTER TABLE `', @tablename, '` 
        ADD CONSTRAINT `', @fk_name, '` 
        FOREIGN KEY (`ad_set_id`) 
        REFERENCES `tblcontactcenter_ads_sets`(`id`) 
        ON DELETE CASCADE')
));
PREPARE fkIfNotExists FROM @preparedStatement;
EXECUTE fkIfNotExists;
DEALLOCATE PREPARE fkIfNotExists;

-- Step 8: Make creative_id nullable in investments table if needed
ALTER TABLE `tblcontactcenter_ads_investments` 
    MODIFY COLUMN `creative_id` INT(11) DEFAULT NULL COMMENT 'Creative ID (if budget_type is creative)';

-- Done!
SELECT 'Migration completed successfully!' AS Result;



