<?php
    require APPPATH . 'libraries/REST_Controller.php';

    class Google_drive_uploader_api extends REST_Controller {

        public function __construct() {
            parent::__construct();
            $this->load->library('Googleservice');
            $this->load->model('chapterDao');
            $this->load->model('chapterImageDao');
            $this->load->library('Commonutils');
            $this->load->model('mangaDao');
            $this->load->model('sourceDao');
        }

        public function index_get(){
            $chapter = $this->chapterDao->getNotUploadedGDrive();
            $selectedManga = $this->mangaDao->getDetailManga($chapter['manga_id']);
            $source = $this->sourceDao->getById($selectedManga['source_id']);
            if(is_null($selectedManga['gdrive_id'])) {
                $folderId = $this->googleservice->createSubFolder('1IhvVBZoNErV-khr1oboaBQVcSjhz3wQH', $selectedManga['drive_folder_id']);
                $selectedManga['gdrive_id'] = $folderId;
                $this->mangaDao->updateManga($selectedManga);
                log_message('info', $selectedManga['title'].' : '.$folderId);
            }

            $chapterFolderId = $this->googleservice->createSubFolder($selectedManga['gdrive_id'], $chapter['number']);
            $chapter['gdrive_id'] = $chapterFolderId;
            $this->chapterDao->updateGDriveId($chapter);
            log_message('info', $selectedManga['title'].'/'.$chapter['number'].' : '.$chapterFolderId);

            $imageList = $this->chapterDao->getByChapterId($chapter['id']);
            foreach($imageList as $chapterImage) {
                if(!is_null($chapterImage->image_url) && !empty($chapterImage->image_url)) {
                    
                    $imageUrl = $chapterImage->imagekit_url;
                    if(is_null($imageUrl) || empty($imageUrl)) {
                        if(is_null($chapterImage->drive_file_id) || empty($chapterImage->drive_file_id)) {
                            $imageUrl = $chapterImage->image_url;
                            if(!$this->commonutils->startsWith($chapterImage->image_url, 'http')) {
                                $imageUrl = $source['base_url'].$chapterImage->image_url;
                            } 
                        } else {
                            $imageUrl = IMAGEKIT_ENDPOINT.'/'.$chapterImage->drive_file_id;
                        }
                    }
                    $filename = basename(parse_url($imageUrl, PHP_URL_PATH));
                    $fileId = $this->googleservice->upload($this->commonutils->url_get_contents($imageUrl, 1200),
                            $filename, $this->commonutils->getMimeTypes($imageUrl), $chapterFolderId);
                    $this->chapterImageDao->updateGdriveId($chapterImage->id, $fileId);    
                }
                
            }
            $this->response(array('status' => 'OK', 'message' => 'Success'));
        }
    }
?>