<?php
    require APPPATH . 'libraries/REST_Controller.php';

    class TestApi extends REST_Controller {

        public function __construct() {
            parent::__construct();
        }

        public function index_get(){
            $response['status']=200;
            $response['error']=false;
            $response['message']='Hai from response';

            $this->response($response);
        }
    }
?>