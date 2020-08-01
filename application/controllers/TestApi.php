<?php
    require APPPATH . 'libraries/REST_Controller.php';

    class TestApi extends REST_Controller {

        public function __construct() {
            parent::__construct();
            $this->load->library('Googleservice');
        }

        public function index_get(){
            //$this->google_utils->test();
            log_message('info', file_exists($_SERVER['DOCUMENT_ROOT'].'assets/client_secrets.json'));
            $this->googleservice->test();
            $this->response(array('status' => 'OK', 'message' => 'Success'));
        }
    }
?>