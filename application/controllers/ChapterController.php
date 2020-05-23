<?php
    require APPPATH . 'libraries/REST_Controller.php';

    class ChapterController extends REST_Controller {

        public function __construct() {
            parent::__construct();
            $this->load->model('chapterDao');
            $this->load->model('mangaDao');
            $this->load->model('sourceDao');
            $this->load->library('Commonutils');
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

        public function chapterDetail_get($mangaId, $chapterId){
            $imageList = $this->chapterDao->getByChapterId($chapterId);
            $mangaDetail = $this->mangaDao->getDetailManga($mangaId);
            $source = $this->sourceDao->getById($mangaDetail['source_id']);
            $imageBaseUrl = $source['base_url'];
            $chapterImages = array();
            foreach($imageList as $chapterImage) {
                $imageUrl = $chapterImage->image_url;
                if(!$this->commonutils->startsWith($chapterImage->image_url, 'http')) {
                    $imageUrl = $imageBaseUrl.$chapterImage->image_url;
                } 

                $tmp = array(
                    "id" => (int) $chapterImage->id,
                    "image_url" => $imageUrl, 
                    "width" => $chapterImage->width, 
                    "height" => $chapterImage->height
                );
                array_push($chapterImages, $tmp);
            }
            
            $chapter = $this->chapterDao->getChapterById($chapterId);
            $prevChapterDb = $this->chapterDao->getPrevChapter($mangaId, $chapter['number']);
            $nextChapterDb = $this->chapterDao->getNextChapter($mangaId, $chapter['number']);
            if(isset($prevChapterDb)){
                $prevChapter = array(
                    "id" => (int) $prevChapterDb['id'],
                    'title' => $prevChapterDb['title'],
                    'number' => $prevChapterDb['number']
                );
            }
            
            if(isset($nextChapterDb)) {
                $nextChapter = array(
                    "id" => (int) $nextChapterDb['id'],
                    'title' => $nextChapterDb['title'],
                    'number' => $nextChapterDb['number']
                );
            }
            
            $responseBody = array(
                'prev_chapter' => isset($prevChapter) ? $prevChapter : null,
                'next_chapter' => isset($nextChapter) ? $nextChapter : null,
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