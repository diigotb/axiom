<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_174 extends App_module_migration
{
    public function up()
    {
        $CI = &get_instance();
        
        // Add sales_knowledge field to device table for AXIOM Intelligence
        if ($CI->db->table_exists(db_prefix() . 'contactcenter_device')) {
            if (!$CI->db->field_exists('sales_knowledge', db_prefix() . 'contactcenter_device')) {
                $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_device` 
                    ADD COLUMN `sales_knowledge` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL 
                    COMMENT 'Product information, sales pitch, and context for AXIOM Intelligence strategies' 
                    AFTER `dev_instance_name`;");
            }
        }
    }
    
    public function down()
    {
        $CI = &get_instance();
        
        // Remove sales_knowledge field
        if ($CI->db->table_exists(db_prefix() . 'contactcenter_device')) {
            if ($CI->db->field_exists('sales_knowledge', db_prefix() . 'contactcenter_device')) {
                $CI->db->query("ALTER TABLE `" . db_prefix() . "contactcenter_device` 
                    DROP COLUMN `sales_knowledge`;");
            }
        }
    }
}
