<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_164 extends App_module_migration
{
    public function up()
    {
        $CI = &get_instance();
        add_option("contac_active_link_call", 0);
    }
}
