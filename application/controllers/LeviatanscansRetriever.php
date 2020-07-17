<?php
    require APPPATH . 'libraries/REST_Controller.php';
    require APPPATH . 'libraries/simple_html_dom.php';

    class LeviatanscansRetriever extends REST_Controller {
        
        public function __construct() {
            parent::__construct();
            $this->load->model('mangaDao');
            $this->load->model('chapterDao');
            $this->load->model('chapterImageDao');
            $this->load->library('Commonutils');
            $this->load->library('Imagekitutils');
            $this->load->library('Firebasenotificationutils');
        }

        public function manga_get($mangaId) {
            $selectedManga = $this->mangaDao->getDetailManga($mangaId);
            //// log_message('info', 'Manga ID: '.$selectedManga);
            $html = file_get_html($selectedManga['source_manga_url']);
            
            $content = $html->find('div[id=content]', 0);
            $container = $content->first_child();

            $descWrapper = $container->find('div', 4)->children(1);
            $descWrapper->children(0)->outertext = ''; 
            $descWrapper->children(1)->outertext = ''; 

            $chapterWrapper = $descWrapper->children(2)->first_child();
            $chapters = $chapterWrapper->find('a');
            
            for ($i=count($chapters); $i > 0; $i--) { 
                $chapter = $chapters[$i-1];
                if($this->commonutils->startsWith($chapter->class, 'item-author')) {
                    
                    $number = $chapter->parent()->parent()->find('span', 0);
                    // log_message('info', 'Chapter Number: '. $number->innertext);
                    $chapterNumber = trim($number->innertext);
                    $chapterExist = $this->chapterDao->countMangaChapterByTitle($mangaId,  trim($chapter->innertext));
                    // log_message('info', 'chapterExist '.$chapterExist);

                    if($chapterExist == 0) {
                        $chapterDb = array(
                            "manga_id" => $mangaId
                        );
                        // log_message('info', 'Chapter: '. $chapter->innertext);
                        $chapterDb['title'] = trim($chapter->innertext);
        
                        // log_message('info', 'Url: '. $chapter->href);
                        $chapterDb['source_chapter_url'] = $chapter->href;
        
                        $chapterDb['number'] = $chapterNumber;
                        $chapterId = $this->chapterDao->save($chapterDb);
    
                        $this->fetchChapterImage($chapterId, $chapter->href, 'images/'.$selectedManga['drive_folder_id'].'/'.$chapterNumber);
                        $this->firebasenotificationutils->broadcash($selectedManga['title'], $chapterDb['title'],
                            $mangaId, $chapterId, $chapterNumber, $selectedManga['cover_url']);
                        break;
                    }

                }
            }

            $selectedManga['last_update_date'] = date("Y/m/m H:i:sa");
            $this->mangaDao->updateManga($selectedManga);
            // log_message('info', "Fetch Chapter Done");
            $this->response(array('status' => 'OK', 'message' => 'Success'));
        }

        private function fetchChapterImage($chapterId, $sourceUrl, $folder) {
            $html = file_get_html($sourceUrl);
            $content = $html->find('div[id=content]', 0);
            $container = $content->first_child();
            $script = $container->children (6);
            $datas = explode(';', $script->innertext);
            foreach ($datas as $data) {
                if ($this->commonutils->startsWith($data, 'window.chapterPages')){
                    // log_message('info', $data);
                    $dataImg = str_replace('window.chapterPages = ', '', $data);
                    $dataImgJson = json_decode($dataImg);
                    
                    $dataDB = array();
                    foreach($dataImgJson as $imgUrl) {
                        // log_message('info', "Image URL: ".$imgUrl);
                        //$content = $this->commonutils->curl_get_contents(LEVIATANSCANS_IMAGE_BASE_URL.$imgUrl, 300);
                        //$mimeType = $this->commonutils->getMimeTypes($imgUrl);
                        $filename = basename(parse_url($imgUrl, PHP_URL_PATH));
                        //$this->commonutils->downloadImage($folder, $filename, LEVIATANSCANS_IMAGE_BASE_URL.$imgUrl, 300);
                        $uploadedImage = $this->imagekitutils->upload(LEVIATANSCANS_IMAGE_BASE_URL.$imgUrl, $filename, $folder);
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
                            //'image_base64' => $this->commonutils->imageUrlToBase64(LEVIATANSCANS_IMAGE_BASE_URL.$imgUrl)
                            )
                        );
                    }
                    $this->chapterImageDao->saveBatch($dataDB);
                break;
                }
            }
        }
    }
?>