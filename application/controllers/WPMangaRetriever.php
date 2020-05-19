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
            $this->load->library('Commonutils');
        }

        public function manga_get($mangaId) {
            $selectedManga = $this->mangaDao->getDetailManga($mangaId);
            $html = file_get_html($selectedManga['source_manga_url']);
            //$ratingPostId = $html->find('input[class=rating-post-id]', 0)->value;
            //log_message('info', $ratingPostId);
            foreach($html->find('script') as $script){
                
                if (strpos($script->innertext, 'var manga = ') !== false) {
                    $removedCDataScript = str_replace('/*', '', $script->innertext);
                    $removedCDataScript = str_replace('<![CDATA[ */', '', $removedCDataScript);
                    $removedCDataScript = str_replace('/*', '', $removedCDataScript);
                    $removedCDataScript = str_replace(']]>', '', $removedCDataScript);
                    $removedCDataScript = str_replace('*/', '', $removedCDataScript);
                    $removedCDataScript = str_replace(';', '', $removedCDataScript);
                    $removedCDataScript = str_replace('var manga =', '', $removedCDataScript);
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
                    
                    foreach ($chapters as $chapter) {
                        if($this->commonutils->startsWith($chapter->class, 'wp-manga-chapter')) {
                            $chapterLink = $chapter->find('a', 0);
                            $chapterReleaseDateWrapper = $chapter->find('span[class=chapter-release-date]', 0);
                            $chapterReleaseDate = $chapterReleaseDateWrapper->find('i', 0);
                            $chapterNumber = trim($chapterLink->innertext);
                            $chapterExist = $this->chapterDao->countMangaChapterByTitle($mangaId, $chapterNumber);
                            // log_message('info', 'chapterExist '.$chapterExist);

                            if($chapterExist == 0) {
                                $chapterDb = array(
                                    "manga_id" => $mangaId
                                );
                                // log_message('info', 'Chapter: '. $chapter->innertext);
                                $chapterDb['title'] = $chapterNumber;
                
                                // log_message('info', 'Url: '. $chapter->href);
                                $chapterDb['source_chapter_url'] = $chapterLink->href;
                                if($chapterReleaseDate){
                                    $chapterDb['release_date'] = trim($chapterReleaseDate);
                                } else {
                                    $chapterDb['release_date'] = date("Y/m/m H:i:sa");
                                }
                                
                                $chapterDb['number'] = $chapterNumber;
                                $chapterId = $this->chapterDao->save($chapterDb);
            
                                $this->fetchChapterImage($chapterId, $chapterLink->href, 'images/'.$selectedManga['drive_folder_id'].'/'.$chapterNumber);
                                break;
                            }

                        }
                    }
                    $selectedManga['last_update_date'] = date("Y/m/m H:i:sa");
                    $this->mangaDao->updateManga($selectedManga);
                break;
                }
            }
            
            // log_message('info', "Fetch Chapter Done");
            $this->response(array('status' => 'OK', 'message' => 'Success'));
        }

        private function fetchChapterImage($chapterId, $sourceUrl, $folder) {
            $html = file_get_html($sourceUrl);
            $readingContent = $html->find('div[class=reading-content]', 0);
            //log_message('info', $readingContent);
            $contents = $readingContent->find('img');
            $dataDB = array();
            foreach($contents as $content){
                $imgUrl = trim($content->{'data-src'});
                //log_message('info', 'Url: '. $imgUrl);
                $filename = basename(parse_url($imgUrl, PHP_URL_PATH));
                $this->commonutils->downloadImage($folder, $filename, $imgUrl, 300);
                array_push($dataDB, array(
                    'chapter_id' => $chapterId ,
                    'image_url' => $imgUrl ,
                    'drive_file_id' => $folder.'/'.$filename
                    )
                );
            }
            $this->chapterImageDao->saveBatch($dataDB);
        }
    }
?>