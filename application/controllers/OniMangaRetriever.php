<?php
    require APPPATH . 'libraries/REST_Controller.php';
    require APPPATH . 'libraries/simple_html_dom.php';

    class OniMangaRetriever extends REST_Controller {

        public function __construct() {
            parent::__construct();
            Requests::register_autoloader();
            $this->load->model('mangaDao');
            $this->load->model('chapterDao');
            $this->load->model('chapterImageDao');
            $this->load->library('Imagekitutils');
            $this->load->library('Commonutils');
            $this->load->library('Firebasenotificationutils');
        }

        public function manga_get($mangaId) {
            $selectedManga = $this->mangaDao->getDetailManga($mangaId);
            $html = file_get_html($selectedManga['source_manga_url']);
            $content = $html->find('div[class=manga-chapters]', 0);
            $chapters = $content->find('a');
            for ($i=count($chapters); $i > 0; $i--) { 
                $chapter = $chapters[$i-1];
                $title = trim($chapter->innertext);
                $chapterExist = $this->chapterDao->countMangaChapterByTitle($mangaId, $title);

                if($chapterExist == 0) {
                    $chapterDb = array(
                        "manga_id" => $mangaId
                    );
                    $chapterDb['title'] = $title;
                    
                    $url = trim($chapter->href);
                    $chapterDb['source_chapter_url'] = $url;

                    $urlBaseName = basename(parse_url($url, PHP_URL_PATH));
                    
                    $chapterNumber = str_replace('-', '.', $urlBaseName);
                    $chapterDb['number'] = $chapterNumber;
                    
                    $chapterId = $this->chapterDao->save($chapterDb);

                    $this->fetchChapterImage($chapterId, $url, 'images/'.$selectedManga['drive_folder_id'].'/'.$chapterNumber);
                    $this->firebasenotificationutils->broadcash($selectedManga['title'], $chapterDb['title'],
                        $mangaId, $chapterId, $chapterNumber, $selectedManga['cover_url']);
                    break;
                }
                
            }
            
            $this->response(array('status' => 'OK', 'message' => 'Success'));
        }

        private function fetchChapterImage($chapterId, $sourceUrl, $folder) {
            $html = file_get_html($sourceUrl);
            $readingContent = $html->find('div[class=reader]', 0);
            $contents = $readingContent->find('img');
            $dataDB = array();
            $filenameArray = array();
            foreach($contents as $content) {
                $imgUrl = trim($content->{'src'});
                $imgUrl = 'https://onimanga.com'.$imgUrl;
                $filename = basename(parse_url($imgUrl, PHP_URL_PATH));
                if(!in_array($filename, $filenameArray)) {
                    array_push($filenameArray, $filename);
                    $uploadedImage = $this->imagekitutils->upload($imgUrl, $filename, $folder);

                    if(isset($uploadedImage) && isset($uploadedImage->success) ) {
                        $height = $uploadedImage->success->height;
                        $width = $uploadedImage->success->width;
                        $size = $uploadedImage->success->size;
                        $imagekitUrl = $uploadedImage->success->url;
                    }

                    array_push($dataDB, array(
                        'chapter_id' => $chapterId ,
                        'image_url' => $imgUrl ,
                        //'drive_file_id' => $folder.'/'.$filename,
                        'height' => isset($height) ? $height : null,
                        'width' => isset($width) ? $width : null,
                        'size' => isset($size) ? $size : null,
                        'imagekit_url' => isset($imagekitUrl) ? $imagekitUrl : null
                        )
                    );
                }
            }
            $this->chapterImageDao->saveBatch($dataDB);
        }
    
    }
?>