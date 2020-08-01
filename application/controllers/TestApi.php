<?php
    require APPPATH . 'libraries/REST_Controller.php';
    //require APPPATH . 'libraries/ImageKit/ImageKit.php';
    use ImageKit\ImageKit;

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