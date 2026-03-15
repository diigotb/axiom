<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_165 extends App_module_migration
{
    public function up()
    {
        $CI = &get_instance();
        if (!$CI->db->table_exists(db_prefix().'contactcenter_message_queue')) {
            $CI->db->query("CREATE TABLE `".db_prefix()."contactcenter_message_queue` (
                `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `number` varchar(50) NOT NULL,
                `msg` text DEFAULT NULL,
                `staffid` int(11) DEFAULT NULL,
                `url` varchar(255) DEFAULT NULL,
                `type` varchar(50) DEFAULT NULL,
                `file_name` varchar(255) DEFAULT NULL,
                `status` varchar(20) DEFAULT 'pending',
                `reply_id` varchar(50) DEFAULT NULL,
                `edit_id` varchar(50) DEFAULT NULL,
                `created_at` datetime DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
        }
    }
}
