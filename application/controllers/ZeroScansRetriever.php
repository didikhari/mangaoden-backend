<?php
    require APPPATH . 'libraries/REST_Controller.php';
    require APPPATH . 'libraries/simple_html_dom.php';

    class ZeroScansRetriever extends REST_Controller {
        
        public function __construct() {
            parent::__construct();
            $this->load->model('mangaDao');
            $this->load->model('chapterDao');
            $this->load->model('chapterImageDao');
            $this->load->library('Commonutils');
            $this->load->library('Googleservice');
            $this->load->library('Firebasenotificationutils');
        }

        public function manga_get($mangaId) {
            $selectedManga = $this->mangaDao->getDetailManga($mangaId);
            if(is_null($selectedManga['gdrive_id'])) {
                $folderId = $this->googleservice->createSubFolder('1IhvVBZoNErV-khr1oboaBQVcSjhz3wQH', $selectedManga['drive_folder_id']);
                $selectedManga['gdrive_id'] = $folderId;
                $this->mangaDao->updateManga($selectedManga);
                log_message('info', $selectedManga['title'].' : '.$folderId);
            }
            $html = file_get_html($selectedManga['source_manga_url']);
            
            $content = $html->find('div[id=content]', 0);
            $container = $content->first_child();

            $descWrapper = $container->find('div', 4)->children(1);
            $descWrapper->children(0)->outertext = ''; 
            $descWrapper->children(1)->outertext = ''; 

            $chapterWrapper = $descWrapper->children(2)->first_child();
            $chapters = $chapterWrapper->find('a');
            
            //foreach ($chapters as $chapter) {
            for ($i=count($chapters); $i > 0; $i--) { 
                $chapter = $chapters[$i-1];
                if($this->commonutils->startsWith($chapter->class, 'item-author')) {
                    
                    $number = $chapter->parent()->parent()->find('span', 0);
                    // log_message('info', 'Chapter Number: '. $number->innertext);
                    $chapterNumber = trim($number->innertext);
                    $chapterExist = $this->chapterDao->countMangaChapter($mangaId, $chapterNumber);
                    // log_message('info', 'chapterExist '.$chapterExist);

                    if($chapterExist == 0) {
                        $chapterDb = array(
                            "manga_id" => $mangaId
                        );
                        // log_message('info', 'Chapter: '. $chapter->innertext);
                        $chapterDb['title'] = $chapter->innertext;
        
                        // log_message('info', 'Url: '. $chapter->href);
                        $chapterDb['source_chapter_url'] = $chapter->href;
        
                        $chapterDb['number'] = $chapterNumber;
                        //$chapterFolderId = $this->googleservice->createSubFolder($selectedManga['gdrive_id'] , $chapterNumber);
                        //$chapterDb['gdrive_id'] = $chapterFolderId;
                        $chapterId = $this->chapterDao->save($chapterDb);
    
                        $this->fetchChapterImage($chapterId, $chapter->href, null);
                        $this->firebasenotificationutils->broadcash($selectedManga['title'], $chapterDb['title'],
                            $mangaId, $chapterId, $chapterNumber, $selectedManga['cover_url']);

                        $selectedManga['last_chapter_date'] = date("Y/m/d H:i:sa");
                        $selectedManga['last_update_date'] = date("Y/m/d H:i:sa");
                        $this->mangaDao->updateManga($selectedManga);
                        break;
                    }

                }
            }
            // log_message('info', "Fetch Chapter Done");
            $this->response(array('status' => 'OK', 'message' => 'Success'));
        }

        private function fetchChapterImage($chapterId, $sourceUrl, $folderId) {
            $html = file_get_html($sourceUrl);
            $content = $html->find('div[id=content]', 0);
            $container = $content->first_child();
            $script = $container->find('script', 0);
            $datas = explode(';', $script->innertext);
            foreach ($datas as $data) {
                if ($this->commonutils->startsWith($data, 'window.chapterPages')){
                    // log_message('info', $data);
                    $dataImg = str_replace('window.chapterPages = ', '', $data);
                    $dataImgJson = json_decode($dataImg);
                    
                    $dataDB = array();
                    foreach($dataImgJson as $imgUrl) {
                        // log_message('info', "Image URL: ".$imgUrl);
                        //$content = $this->commonutils->curl_get_contents(ZEROSCANS_IMAGE_BASE_URL.$imgUrl, 300);
                        //$mimeType = $this->commonutils->getMimeTypes($imgUrl);
                        $filename = basename(parse_url($imgUrl, PHP_URL_PATH));
                        //$this->commonutils->downloadImage($folder, $filename, ZEROSCANS_IMAGE_BASE_URL.$imgUrl, 300);
                        // $uploadedImage = $this->imagekitutils->upload(ZEROSCANS_IMAGE_BASE_URL.$imgUrl, $filename, $folder);
                        // if( isset($uploadedImage) && isset($uploadedImage->success) ) {
                        //     $height = $uploadedImage->success->height;
                        //     $width = $uploadedImage->success->width;
                        //     $size = $uploadedImage->success->size;
                        //     $imagekitUrl = $uploadedImage->success->url;
                        // }
                        // $fileId = $this->googleservice->upload($this->commonutils->url_get_contents(ZEROSCANS_IMAGE_BASE_URL.$imgUrl, 1200),
                        //     $filename, $this->commonutils->getMimeTypes($imgUrl), $folderId);
                    
                        array_push($dataDB, array(
                            'chapter_id' => $chapterId ,
                            'image_url' => $imgUrl
                            // 'gdrive_id' => $fileId
                            //'drive_file_id' => $folder.'/'.$filename,
                            // 'height' => isset($height) ? $height : null,
                            // 'width' => isset($width) ? $width : null,
                            // 'size' => isset($size) ? $size : null,
                            // 'imagekit_url' => isset($imagekitUrl) ? $imagekitUrl : null
                            //'image_base64' => $this->commonutils->imageUrlToBase64(ZEROSCANS_IMAGE_BASE_URL.$imgUrl)
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