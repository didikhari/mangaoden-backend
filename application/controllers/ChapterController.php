<?php
    require APPPATH . 'libraries/REST_Controller.php';

    class ChapterController extends REST_Controller {

        public function __construct() {
            parent::__construct();
            $this->load->model('chapterDao');
        }

        public function chapters_get($mangaId){
            $chaptersList = $this->chapterDao->getByMangaId($mangaId);
            $chapters = array();
            foreach($chaptersList as $chapter) {
                $tmp = array(
                    "id" => (int) $chapter->id,
                    "number" => $chapter->number, 
                    "title" => $chapter->title, 
                    "retrieve_date" => $chapter->retrieve_date, 
                    "release_date" => $chapter->release_date
                );
                array_push($chapters, $tmp);
            }
            $responseBody = array(
                'chapters' => $chapters
            );

            $this->response(array(
                'status' => 'success', 
                'message' => 'Success', 
                'data' => $responseBody)
            );
        }

        public function chapterDetail_get($chapterId){
            $imageList = $this->chapterDao->getByChapterId($chapterId);
            $chapterImages = array();
            foreach($imageList as $chapterImage) {
                $tmp = array(
                    "id" => (int) $chapter->id,
                    "image_url" => $chapter->image_url, 
                    "width" => $chapter->width, 
                    "height" => $chapter->height
                );
                array_push($chapterImages, $tmp);
            }
            $responseBody = array(
                'images' => $chapterImages
            );

            $this->response(array(
                'status' => 'success', 
                'message' => 'Success', 
                'data' => $responseBody)
            );
        }
    }
?>