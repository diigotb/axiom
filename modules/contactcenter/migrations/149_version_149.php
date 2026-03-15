<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_149 extends App_module_migration
{
    public function up()
    {
        $CI = &get_instance();
        $CI->load->database();

        // se ja tem o registro com essa url, nao insere
            $CI->db->query("INSERT INTO `tblcontactcenter_server` (`url`, `version`, `name`, `api_type`, `value_key`) VALUES
        }

            $CI->db->query("INSERT INTO `tblcontactcenter_server` (`url`, `version`, `name`, `api_type`, `value_key`) VALUES
        }

            $CI->db->query("INSERT INTO `tblcontactcenter_server` (`url`, `version`, `name`, `api_type`, `value_key`) VALUES
        }
    }
}
