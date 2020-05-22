<?php
    require APPPATH . 'libraries/REST_Controller.php';
    //require APPPATH . 'libraries/ImageKit/ImageKit.php';
    use ImageKit\ImageKit;

    class TestApi extends REST_Controller {

        public function __construct() {
            parent::__construct();
        }

        public function index_get(){
            $imageKit = new ImageKit(
                IMAGEKIT_PUBLIC_KEY,
                IMAGEKIT_PRIVATE_KEY,
                IMAGEKIT_ENDPOINT
            );
            $uploadFile = $imageKit->upload(array(
                'file' => "https://earlymanga.net/wp-content/uploads/WP-manga/data/manga_5e84abbb0fc04/89e817afb4118678b1fb2ac81e50d2b2/Martial-Master-Chapter-262_002.png",
                'fileName' => "Martial-Master-Chapter-262_002.png",
                'folder' => '/martial-master/262/',
                "useUniqueFileName" => false,
                "isPrivateFile" => false
            ));
            
            // echo ("Upload URL" . json_encode($uploadFile));
            // $response['status']=200;
            // $response['error']=false;
            // $response['message']='Hai from response';
            $this->response($uploadFile);
        }
    }
?>