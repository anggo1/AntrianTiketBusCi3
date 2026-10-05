<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller{

    public function index(){
        $data['title'] = 'Admin area';
        $this->load->view('_part/backend_head', $data);
        $this->load->view('_part/backend_sidebar_v');
        $this->load->view('_part/backend_topbar_v');
        $this->load->view('backend/admin_v');
        $this->load->view('_part/backend_footer_v');
        $this->load->view('_part/backend_foot');
    }


}