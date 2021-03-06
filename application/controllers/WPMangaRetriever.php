<?php
    require APPPATH . 'libraries/REST_Controller.php';
    require APPPATH . 'libraries/simple_html_dom.php';
    require APPPATH . 'libraries/Requests.php';

    class WPMangaRetriever extends REST_Controller {
        
        public function __construct() {
            parent::__construct();
            Requests::register_autoloader();
            $this->load->model('mangaDao');
            $this->load->model('chapterDao');
            $this->load->model('chapterImageDao');
            $this->load->library('Googleservice');
            $this->load->library('Commonutils');
            $this->load->library('Firebasenotificationutils');
        }

        public function manga_get($mangaId, $dateFormatFlag) {
            $selectedManga = $this->mangaDao->getDetailManga($mangaId);
            if(is_null($selectedManga['gdrive_id'])) {
                $folderId = $this->googleservice->createSubFolder('1IhvVBZoNErV-khr1oboaBQVcSjhz3wQH', $selectedManga['drive_folder_id']);
                $selectedManga['gdrive_id'] = $folderId;
                $this->mangaDao->updateManga($selectedManga);
                log_message('info', $selectedManga['title'].' : '.$folderId);
            }
            $html = file_get_html($selectedManga['source_manga_url']);
            
            foreach($html->find('script') as $script){
                
                if (strpos($script->innertext, 'var manga') !== false) {
                    $removedCDataScript = str_replace('/*', '', $script->innertext);
                    $removedCDataScript = str_replace('<![CDATA[ */', '', $removedCDataScript);
                    $removedCDataScript = str_replace('/*', '', $removedCDataScript);
                    $removedCDataScript = str_replace(']]>', '', $removedCDataScript);
                    $removedCDataScript = str_replace('*/', '', $removedCDataScript);
                    $removedCDataScript = str_replace(';', '', $removedCDataScript);
                    $removedCDataScript = str_replace('var manga =', '', $removedCDataScript);
                    $removedCDataScript = str_replace('var manga=', '', $removedCDataScript);
                    //log_message('info', trim($removedCDataScript));
                    $mangaAjax = json_decode(trim($removedCDataScript));
                    
                    $ajaxUrl = $mangaAjax->{'ajax_url'};
                    $ajaxMangaId = $mangaAjax->{'manga_id'};
                    $response = Requests::post($ajaxUrl, array(), 'action=manga_get_chapters&manga='.$ajaxMangaId);
                    $responseBody = $response->body;
                    //log_message('info', $responseBody);
                    $content = str_get_html($responseBody);
                    $versionsList = $content->find('ul', 0);
                    $chapters = $versionsList->find('li');
                    
                    //foreach ($chapters as $chapter) {
                    for ($i=count($chapters); $i > 0; $i--) { 
                        $chapter = $chapters[$i-1];
                        if($this->commonutils->startsWith($chapter->class, 'wp-manga-chapter')) {
                            $chapterLink = $chapter->find('a', 0);
                            $chapterReleaseDateWrapper = $chapter->find('span[class=chapter-release-date]', 0);
                            $chapterReleaseDate = $chapterReleaseDateWrapper->find('i', 0);
                            $chapterTitle = trim($chapterLink->innertext);
                            //$chapterExist = $this->chapterDao->countMangaChapterByTitle($mangaId, $chapterNumber);
                            
                            $chapterNumber = $this->commonutils->getChapterNoFromTitile($chapterTitle);
                            $chapterExist = $this->chapterDao->countMangaChapter($mangaId, $chapterNumber);
                            // log_message('info', 'chapterExist '.$chapterExist);

                            if($chapterExist == 0) {
                                $chapterDb = array(
                                    "manga_id" => $mangaId
                                );
                                // log_message('info', 'Chapter: '. $chapter->innertext);
                                $chapterDb['title'] = $chapterTitle;
                
                                // log_message('info', 'Url: '. $chapter->href);
                                $chapterDb['source_chapter_url'] = $chapterLink->href;
                                if($chapterReleaseDate){
                                    $releaseDate = $this->commonutils->formatDate($chapterReleaseDate->innertext, $dateFormatFlag);
                                    $chapterDb['release_date'] = $releaseDate;
                                } else {
                                    $chapterDb['release_date'] = date("Y/m/d H:i:sa");
                                }
                                
                                $chapterDb['number'] = $chapterNumber;
                                //$chapterFolderId = $this->googleservice->createSubFolder($selectedManga['gdrive_id'] , $chapterDb['number'] );
                                //$chapterDb['gdrive_id'] = $chapterFolderId;
                                $chapterId = $this->chapterDao->save($chapterDb);
            
                                $this->fetchChapterImage($chapterId, $chapterLink->href, null);
                                $this->firebasenotificationutils->broadcash($selectedManga['title'], $chapterDb['title'],
                                    $mangaId, $chapterId, $chapterNumber, $selectedManga['cover_url']);

                                $selectedManga['last_chapter_date'] = date("Y/m/d H:i:sa");
                                $selectedManga['last_update_date'] = date("Y/m/d H:i:sa");
                                $this->mangaDao->updateManga($selectedManga);
                                break;
                            }

                        }
                    }
                break;
                }
            }
            
            // log_message('info', "Fetch Chapter Done");
            $this->response(array('status' => 'OK', 'message' => 'Success'));
        }

        private function fetchChapterImage($chapterId, $sourceUrl, $folderId) {
            $html = file_get_html($sourceUrl);
            $readingContent = $html->find('div[class=reading-content]', 0);
            //log_message('info', $readingContent);
            $contents = $readingContent->find('img');
            $dataDB = array();
            foreach($contents as $content){
                $imgUrl = trim($content->{'data-src'});
                if ($this->commonutils->IsNullOrEmptyString($imgUrl))
                    $imgUrl = trim($content->{'src'});
                //log_message('info', 'Url: '. $imgUrl);
                $filename = basename(parse_url($imgUrl, PHP_URL_PATH));
                //$this->commonutils->downloadImage($folder, $filename, $imgUrl, 300);
                // $uploadedImage = $this->imagekitutils->upload($imgUrl, $filename, $folder);
                // if(isset($uploadedImage) && isset($uploadedImage->success) ) {
                //     $height = $uploadedImage->success->height;
                //     $width = $uploadedImage->success->width;
                //     $size = $uploadedImage->success->size;
                //     $imagekitUrl = $uploadedImage->success->url;
                // }
                // $fileId = $this->googleservice->upload($this->commonutils->url_get_contents($imgUrl, 1200),
                //         $filename, $this->commonutils->getMimeTypes($imgUrl), $folderId);
            
                array_push($dataDB, array(
                    'chapter_id' => $chapterId ,
                    'image_url' => $imgUrl ,
                    //'drive_file_id' => $folder.'/'.$filename,
                    // 'height' => isset($height) ? $height : null,
                    // 'width' => isset($width) ? $width : null,
                    // 'size' => isset($size) ? $size : null,
                    // 'imagekit_url' => isset($imagekitUrl) ? $imagekitUrl : null
                    )
                );
            }
            $this->chapterImageDao->saveBatch($dataDB);
        }
    }
?>