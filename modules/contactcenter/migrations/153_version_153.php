<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_153 extends App_module_migration
{
    public function up()
    {
        add_option("whatsapp_msg_call", "Olá, no momento não podemos atender ligações");
    }
}
