<?php

require_once FCPATH . 'modules/contactcenter/vendor/autoload.php'; 
use OpenApi\scan;

class Swagger extends CI_Controller
{
    public function json()
    {
        header('Content-Type: application/json');

        // Use a função `scan`
        $openapi = \OpenApi\scan([FCPATH . 'modules/contactcenter/controllers']);
        echo $openapi->toJson();
    }   
}
