<?php
    require APPPATH . 'libraries/REST_Controller.php';
    require APPPATH . 'libraries/Requests.php';

    class MangaRetriever extends REST_Controller {
        
        public function __construct() {
            parent::__construct();
            Requests::register_autoloader();
            $this->load->model('mangaDao');
            $this->load->model('sequenceDao');
            $this->load->model('categoryDao');
            $this->load->model('authorDao');
            $this->load->model('artistDao');
            $this->load->model('chapterDao');
            $this->load->model('chapterDetailDao');
        }

        public function listManga_get() {
            $page = $this->sequenceDao->nextval('manga_list_page', 1);
            $response = Requests::get(MANGAEDEN_API_URL . 'list/0/?p='.$page.'&l=30', array('Accept' => 'application/json'));
            $responseBody = json_decode($response->body, true);
            foreach($responseBody['manga'] as $item) {
                log_message('info', 'Processing Manga'.$item['t']);
                if(isset($item['im'])){
                    $b64image = base64_encode(file_get_contents(MANGAEDEN_IMAGE_BASE_URL . $item['im']));
                }
                
                // get detail manga
                $responseDetails = Requests::get(MANGAEDEN_API_URL . 'manga/'.$item['i'], array('Accept' => 'application/json'));
                $responseBodyDetails = json_decode($responseDetails->body, true);
                $description = $responseBodyDetails['description'];
                $chaptersLen = $responseBodyDetails['chapters_len'];
                $released = $responseBodyDetails['released'];
                $created = (isset($responseBodyDetails['created']) ? gmdate("Y-m-d\TH:i:s\Z", $responseBodyDetails['created']) : null);

                $manga = array(
                    'title' => $item['t'],
                    'status' => $item['s'],
                    'last_chapter_date' => (isset($item['ld']) ? gmdate("Y-m-d\TH:i:s\Z", $item['ld']) : null),
                    'hits' => $item['h'],
                    'image' => ( isset($b64image) ? $b64image : null ),
                    'id' => $item['i'],
                    'language' => 0,
                    'source' => SOURCE_MANGAEDEN,
                    'description' => $description,
                    'chapters_len' => $chaptersLen,
                    'released' => $released,
                    'created' => $created,
                );
                $this->mangaDao->insertNewManga($manga);

                $author = $responseBodyDetails['author'];
                $artist = $responseBodyDetails['artist'];

                $artistId = $this->artistDao->save($artist);
                $authorId = $this->authorDao->save($author);
                $this->mangaDao->mapMangaArtist($manga['id'], $artistId);
                $this->mangaDao->mapMangaAuthor($manga['id'], $authorId);

                if (isset($item['c'])){
                    foreach ($item['c'] as $c) {
                        log_message('info', 'Processing Category '.$c);
                        $categoryId = $this->categoryDao->getOrInsertByName($c);
                        $this->categoryDao->mapMangaWithCategory($manga['id'], $categoryId);
                    }
                }

                if (isset($responseBodyDetails['chapters'])) {
                    foreach ($responseBodyDetails['chapters'] as $chapter) {
                        log_message('info', 'Processing Chapter '.$chapter[0]);
                        $chapter = array(
                            "id" => $chapter[3],
                            "date" => (isset($chapter[1]) ? gmdate("Y-m-d\TH:i:s\Z", $chapter[1]) : null),
                            "title" => $chapter[2],
                            "number" => $chapter[0],
                            "manga_id" => $manga['id']
                        );

                        $this->chapterDao->save($chapter);
                    }
                }
            }
            $this->response($responseBody['manga']);
        }

        public function chapterDetail_get() {
            $chapterIds = $this->chapterDao->getUndetailedChapterId();
            $processedIds = '';
            foreach ($chapterIds as $chapterId) {
                log_message('info', 'Processing Chapter '.$chapterId->id);
                $responseDetail = Requests::get(MANGAEDEN_API_URL . 'chapter/'.$chapterId->id, array('Accept' => 'application/json'));
                $responseBodyDetail = json_decode($responseDetail->body, true);
                $images = $responseBodyDetail['images'];

                if(isset($images)) {
                    foreach($images as $image) {
                        $b64image = "";
                        if(isset($image[1])){
                            $b64image = base64_encode(file_get_contents(MANGAEDEN_IMAGE_BASE_URL . $image[1]));
                        }
                        $chapterDetail =  array(
                            "id" => $image[0],
                            "image_url" => MANGAEDEN_IMAGE_BASE_URL . $image[1],
                            "width" => $image[2],
                            "height" => $image[3],
                            "chapter_id" => $chapterId->id,
                            "image" => $b64image
                        );
                        $this->chapterDetailDao->save($chapterDetail);
                    }
                }
                $processedIds = $processedIds . ', ' . $chapterId->id;
            }
            $this->response($processedIds);
        }
    }
?>