<?php
    require APPPATH . 'libraries/REST_Controller.php';
    
    class TestApi extends REST_Controller {

        public function __construct() {
            parent::__construct();
            $this->load->library('Google_utils');
        }

        public function index_get(){
            $this->google_utils->test();
            $this->response(array('status' => 'OK', 'message' => 'Success'));
        }
    }
?>