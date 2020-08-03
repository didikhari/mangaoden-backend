<?php
    require APPPATH . 'libraries/REST_Controller.php';

    class TestApi extends REST_Controller {

        public function __construct() {
            parent::__construct();
            $this->load->library('Googleservice');
        }

        public function index_get(){
            $files = $this->googleservice->list(1);
            if(is_null($files)) {
                $this->response(array('status' => 'OK', 'message' => 'Failed'));
            } else {
                log_message('info', $files->id);
                $this->response(array('status' => 'OK', 'message' => 'Success', 'file_id' => $files->id));
            }
            
        }
    }
?>