<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_162 extends App_module_migration
{
    public function up()
    {
        $CI = &get_instance();
        if (!$CI->db->table_exists(db_prefix() . 'contactcenter_meta_head')) {
            $CI->db->query("CREATE TABLE tblcontactcenter_meta_head (
                        id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                        title varchar(255) DEFAULT NULL,               
                        body TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,                
                        device_id int(11) DEFAULT NULL, 
                        sourceId varchar(255) DEFAULT NULL,               
                        dev_session varchar(255) DEFAULT NULL,               
                        thumbnailUrl varchar(255) DEFAULT NULL,               
                        sourceUrl varchar(255) DEFAULT NULL,
                        amount  decimal(12,2) DEFAULT 0,               
                        date DATETIME DEFAULT CURRENT_TIMESTAMP
                      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
        }
      
    }
}
