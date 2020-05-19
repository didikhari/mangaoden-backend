<?php
    require APPPATH . 'libraries/REST_Controller.php';

    class HomeController extends REST_Controller {

        public function __construct() {
            parent::__construct();
            $this->load->model('mangaDao');
            $this->load->model('chapterDao');
        }

        public function hot_get(){
            $comicArray = $this->mangaDao->getListManga(1, 6, null, 'last_update_date', 'DESC');
            $images = array();
            foreach($comicArray as $comic){
                $image = array(
                    'img' => $comic['cover_url'],
                    'title' => $comic['title'],
                    'id' => $comic['id']
                );
                array_push($images, $image);
            }
            $data = array(
                'images' => $images
            );

            $this->response(array(
                'status' => 'success', 
                'message' => 'Success', 
                'data' => $data)
            );
        }
    }
?>