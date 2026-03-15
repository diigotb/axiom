<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_163 extends App_module_migration
{
    public function up()
    {
        $CI = &get_instance();
        if ($CI->db->query("SELECT * FROM `tblcontactcenter_server` WHERE `url` = ''")->num_rows() == 0) {
            $CI->db->query("INSERT INTO `tblcontactcenter_server` (`url`, `version`, `name`, `api_type`, `value_key`) VALUES           
            ('', '2', 'Inexxus Evolution (server 5)', 'axiom_evolution', '12d7cf0603036564a574eedcf904d84b');");
        }
      
    }
}
