<?php
    require APPPATH . 'libraries/REST_Controller.php';
    //require APPPATH . 'libraries/ImageKit/ImageKit.php';
    use ImageKit\ImageKit;

    class TestApi extends REST_Controller {

        public function __construct() {
            parent::__construct();
            $this->load->library('Imagekitutils');
        }

        public function index_get(){
            // $imageKit = new ImageKit(
            //     IMAGEKIT_PUBLIC_KEY,
            //     IMAGEKIT_PRIVATE_KEY,
            //     IMAGEKIT_ENDPOINT
            // );
            // $uploadFile = $imageKit->upload(array(
            //     'file' => "http://crawl.didikhari.web.id/images/wind-sword/1/01.png",
            //     'fileName' => "02.png",
            //     'folder' => '/wind-sword/1/',
            //     "useUniqueFileName" => false,
            //     "isPrivateFile" => false
            // ));
            // log_message('info', $uploadFile);
            // echo ("Upload URL" . json_encode($uploadFile));
            // $response['status']=200;
            // $response['error']=false;
            // $response['message']='Hai from response';
            $uploadFile = $this->imagekitutils->upload(
                'https://earlymanga.net/wp-content/uploads/WP-manga/data/manga_5e6e34d1686b9/e3bd6cd335a8a96fd7c07194b68226d2/001.jpg', 
                '002.jpg', 
                'images/master-of-legendary-realms/Chapter-6'
            );
            $this->response($uploadFile);
        }
    }
?>