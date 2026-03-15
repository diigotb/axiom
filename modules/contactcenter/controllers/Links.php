<?php
class Links extends ClientsController {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('contactcenter_model');      
        
    }  
    

    public function code($hash){
        if($hash){
            $url = $this->contactcenter_model->get_links_custom($hash);
            if($url){
                redirect($url->link);
            }
        }
    }
}
