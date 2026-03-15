<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_152 extends App_module_migration
{
    public function up()
    {

        add_option("contac_active_confirm_agendamento", 1);
        add_option("contac_title_agendamento", "Agendamento via WhatsApp");

    }
}