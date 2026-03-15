<?php
/**
 * Migration script to add ad_set_id column to creatives table
 * and budget_type, ad_set_id columns to investments table
 * 
 * Run this once to migrate existing installations
 * 
 * Access via: http://your-domain/modules/contactcenter/migrate_ad_set_columns.php
 * Or run via CLI: php modules/contactcenter/migrate_ad_set_columns.php
 */

// Connect directly to database
// Try to get base path from various methods
$base_path = null;

// Method 1: From current working directory
$cwd = getcwd();
if ($cwd && file_exists($cwd . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'database.php')) {
    $base_path = $cwd;
}

// Method 2: From script directory (3 levels up)
if (!$base_path) {
    $script_path = realpath(__DIR__ . '/../../..');
    if ($script_path && file_exists($script_path . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'database.php')) {
        $base_path = $script_path;
    }
}

// Method 3: From FCPATH (if defined)
if (!$base_path && defined('FCPATH') && FCPATH) {
    $fcpath = rtrim(FCPATH, '/\\');
    if (file_exists($fcpath . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'database.php')) {
        $base_path = $fcpath;
    }
}

if (!$base_path) {
    die("Error: Could not determine base path. Current dir: " . getcwd() . ", Script dir: " . __DIR__ . "\n");
}

$config_path = $base_path . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'database.php';
if (!file_exists($config_path)) {
    die("Error: Could not find database config file at: {$config_path}\n");
}

// Load app config first
$app_config_path = $base_path . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'app-config.php';
if (file_exists($app_config_path)) {
    require_once $app_config_path;
}

$db_config = include $config_path;
$db = $db_config['db']['default'];

// Create database connection
try {
    $conn = new mysqli($db['hostname'], $db['username'], $db['password'], $db['database']);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error . "\n");
    }
    $conn->set_charset($db['char_set'] ?? 'utf8mb4');
    
    // Get table prefix
    $prefix = $db['dbprefix'] ?? '';
    
    // Helper function to check if column exists
    function columnExists($conn, $table, $column) {
        $result = $conn->query("SHOW COLUMNS FROM `{$table}` LIKE '{$column}'");
        return $result && $result->num_rows > 0;
    }
    
    // Helper function to check if table exists
    function tableExists($conn, $table) {
        $result = $conn->query("SHOW TABLES LIKE '{$table}'");
        return $result && $result->num_rows > 0;
    }

    echo "Starting Ad Set migration...\n\n";
    
    $creatives_table = $prefix . 'contactcenter_ads_creatives';
    $investments_table = $prefix . 'contactcenter_ads_investments';
    $ad_sets_table = $prefix . 'contactcenter_ads_sets';
    
    // Check if ad_sets table exists, create if not
    if (!tableExists($conn, $ad_sets_table)) {
        echo "Creating ad_sets table...\n";
        $conn->query("CREATE TABLE `{$ad_sets_table}` (
            `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `name` VARCHAR(255) NOT NULL,
            `description` TEXT DEFAULT NULL,
            `is_active` TINYINT(1) DEFAULT 1,
            `staffid` INT(11) DEFAULT NULL,
            `date_created` DATETIME DEFAULT CURRENT_TIMESTAMP,
            `date_updated` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
        echo "✓ Ad sets table created\n\n";
    } else {
        echo "✓ Ad sets table already exists\n\n";
    }
    
    // Check if creatives table exists
    if (tableExists($conn, $creatives_table)) {
        echo "Checking creatives table for ad_set_id column...\n";
        
        if (!columnExists($conn, $creatives_table, 'ad_set_id')) {
            echo "Adding ad_set_id column to creatives table...\n";
            $conn->query("ALTER TABLE `{$creatives_table}` 
                ADD COLUMN `ad_set_id` INT(11) DEFAULT NULL COMMENT 'Ad Set this creative belongs to (optional)' AFTER `media_id`");
            
            // Add foreign key if ad_sets table exists
            if (tableExists($conn, $ad_sets_table)) {
                // Check if foreign key already exists
                $fk_result = $conn->query("SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
                    WHERE TABLE_SCHEMA = DATABASE() 
                    AND TABLE_NAME = '{$creatives_table}' 
                    AND COLUMN_NAME = 'ad_set_id' 
                    AND REFERENCED_TABLE_NAME = '{$ad_sets_table}'");
                
                if (!$fk_result || $fk_result->num_rows == 0) {
                    try {
                        $conn->query("ALTER TABLE `{$creatives_table}` 
                            ADD FOREIGN KEY (`ad_set_id`) REFERENCES `{$ad_sets_table}`(`id`) ON DELETE SET NULL");
                    } catch (Exception $e) {
                        echo "⚠ Warning: Could not add foreign key: " . $e->getMessage() . "\n";
                    }
                }
            }
            echo "✓ ad_set_id column added to creatives table\n\n";
        } else {
            echo "✓ ad_set_id column already exists in creatives table\n\n";
        }
    } else {
        echo "⚠ Creatives table does not exist\n\n";
    }
    
    // Check if investments table exists
    if (tableExists($conn, $investments_table)) {
        echo "Checking investments table for budget_type and ad_set_id columns...\n";
        
        if (!columnExists($conn, $investments_table, 'budget_type')) {
            echo "Adding budget_type column to investments table...\n";
            $conn->query("ALTER TABLE `{$investments_table}` 
                ADD COLUMN `budget_type` ENUM('creative', 'ad_set') DEFAULT 'creative' COMMENT 'Type of budget: per creative or per ad set' AFTER `id`");
            // Update existing records to have 'creative' as budget_type
            $conn->query("UPDATE `{$investments_table}` SET `budget_type` = 'creative' WHERE `budget_type` IS NULL");
            echo "✓ budget_type column added to investments table\n";
        } else {
            echo "✓ budget_type column already exists in investments table\n";
        }
        
        if (!columnExists($conn, $investments_table, 'ad_set_id')) {
            echo "Adding ad_set_id column to investments table...\n";
            $conn->query("ALTER TABLE `{$investments_table}` 
                ADD COLUMN `ad_set_id` INT(11) DEFAULT NULL COMMENT 'Ad Set ID (if budget_type is ad_set)' AFTER `creative_id`");
            
            // Add foreign key if ad_sets table exists
            if (tableExists($conn, $ad_sets_table)) {
                // Check if foreign key already exists
                $fk_result = $conn->query("SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
                    WHERE TABLE_SCHEMA = DATABASE() 
                    AND TABLE_NAME = '{$investments_table}' 
                    AND COLUMN_NAME = 'ad_set_id' 
                    AND REFERENCED_TABLE_NAME = '{$ad_sets_table}'");
                
                if (!$fk_result || $fk_result->num_rows == 0) {
                    try {
                        $conn->query("ALTER TABLE `{$investments_table}` 
                            ADD FOREIGN KEY (`ad_set_id`) REFERENCES `{$ad_sets_table}`(`id`) ON DELETE CASCADE");
                    } catch (Exception $e) {
                        echo "⚠ Warning: Could not add foreign key: " . $e->getMessage() . "\n";
                    }
                }
            }
            echo "✓ ad_set_id column added to investments table\n";
        } else {
            echo "✓ ad_set_id column already exists in investments table\n";
        }
        
        // Make creative_id nullable if it's not already
        $result = $conn->query("SHOW COLUMNS FROM `{$investments_table}` WHERE Field = 'creative_id'");
        if ($result && $row = $result->fetch_assoc()) {
            if (strpos($row['Null'], 'YES') === false && strpos($row['Type'], 'NULL') === false) {
                echo "Making creative_id nullable in investments table...\n";
                $conn->query("ALTER TABLE `{$investments_table}` 
                    MODIFY COLUMN `creative_id` INT(11) DEFAULT NULL COMMENT 'Creative ID (if budget_type is creative)'");
                echo "✓ creative_id is now nullable\n";
            } else {
                echo "✓ creative_id is already nullable\n";
            }
        }
    } else {
        echo "⚠ Investments table does not exist\n";
    }
    
    $conn->close();
    
    echo "\n✓ Migration completed successfully!\n";
    echo "You can now delete this file: " . __FILE__ . "\n";
    
} catch (Exception $e) {
    die("Error: " . $e->getMessage() . "\n");
}

