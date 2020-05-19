<?php
    require APPPATH . 'libraries/REST_Controller.php';

    class HomeController extends REST_Controller {

        public function __construct() {
            parent::__construct();
            $this->load->model('mangaDao');
            $this->load->model('sliderImageDao');
        }

        public function slider_get(){
            $comicArray = $this->sliderImageDao->findAll();
            $images = array();
            foreach($comicArray as $comic){
                $image = array(
                    'img' => $comic['image_url'],
                    'title' => $comic['title'],
                    'id' => $comic['manga_id']
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

        public function comics_get($orderBy){
            $comicArray = $this->mangaDao->getListManga(0, 6, null, $orderBy, 'DESC');
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