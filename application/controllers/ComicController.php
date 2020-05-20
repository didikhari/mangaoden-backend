<?php
    require APPPATH . 'libraries/REST_Controller.php';

    class ComicController extends REST_Controller {

        public function __construct() {
            parent::__construct();
            $this->load->model('mangaDao');
            $this->load->model('categoryDao');
            $this->load->model('authorDao');
            $this->load->model('artistDao');
            $this->load->model('chapterDao');
        }

        public function comics_get(){
            $page = ($this->get('page') !== null) ? $this->get('page') : 0;
            $rowPerPage = ($this->get('length') != null) ? $this->get('length') : 30;
            $searchString = $this->get('search');
            $orderBy = ($this->get('order') != null) ? $this->get('order') : 'title';
            $orderType = ($this->get('type') != null) ? $this->get('type') : 'ASC';
            $start = $page * $rowPerPage;
            $listManga = $this->mangaDao->getListManga($start, $rowPerPage, $searchString, $orderBy, $orderType);
            $respManga = [];
            foreach($listManga as $manga){
                $mangaData = array(
                    "id" => (int) $manga['id'],
                    "cover" => $manga['cover_url'],
                    "title" => $manga['title'],
                    "last_chapter_date" => $manga['last_chapter_date'],
                    "status" => (int) $manga['status']
                );
                array_push($respManga, $mangaData);
            }
            $totalManga = $this->mangaDao->countManga($searchString);

            $responseBody = array(
                'start' => $start,
                'end' => count($listManga),
                'page' => (int) $page,
                'total' => $totalManga,
                'comics' => $respManga
            );

            $this->response(array(
                'status' => 'success', 
                'message' => 'Success', 
                'data' => $responseBody)
            );
        }

        public function comicsDetail_get($mangaId){
            $mangaDetail = $this->mangaDao->getDetailManga($mangaId);
            $chapterList = $this->chapterDao->getListChapter(0, 5, 'number', 'DESC', $mangaId);
            $categoryList = $this->categoryDao->getByMangaId($mangaId);
            $authorList = $this->authorDao->getByMangaId($mangaId);
            $artistList = $this->artistDao->getByMangaId($mangaId);

            $chapters = array();
            foreach($chapterList as $chapter) {
                $tmp = array(
                    "number" => $chapter->number, 
                    "release_date" => $chapter->release_date == null ? $chapter->retrieve_date : $chapter->release_date, 
                    "title" => $chapter->title, 
                    "id" => (int) $chapter->id
                );
                array_push($chapters, $tmp);
            }

            $genres = array();
            foreach($categoryList as $category) {
                array_push($genres, $category->name);
            }

            $authors = array();
            foreach($authorList as $author) {
                array_push($authors, $author->name);
            }

            $artists = array();
            foreach($artistList as $artist) {
                array_push($artists, $artist->name);
            }
            $totalChapter = $this->chapterDao->countMangaChapterByMangaId($mangaId);
            $responseBody = array(
                'id' => (int) $mangaDetail['id'],
                'title' => $mangaDetail['title'],
                'background' => $mangaDetail['background'],
                'cover' => $mangaDetail['cover_url'],
                'status' => (int) $mangaDetail['status'],
                'last_chapter_date' => $mangaDetail['last_chapter_date'],
                'total_chapter' => (int) $totalChapter,
                'hits' => (int) $mangaDetail['hits'],
                'is_licenced' => $mangaDetail['is_licenced'] == 0 ? false : true,
                //'source' => $mangaDetail['source'],
                'authors' => $authors,
                'artists' => $artists,
                'description' => $mangaDetail['description'],
                'genres' => $genres,
                'arts' => [],
                'chapters' => $chapters
            );

            $this->response(array(
                'status' => 'success', 
                'message' => 'Success', 
                'data' => $responseBody)
            );
        }
    }
?>