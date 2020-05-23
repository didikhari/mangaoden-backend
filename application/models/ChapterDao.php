<?php
    if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    
    class ChapterDao extends CI_Model{
        
        public function save($chapter){
            $array = array('number' => $chapter['number'], 'manga_id' => $chapter['manga_id']);
            $this->db->where($array);
            $query = $this->db->get('chapter');
            $row = $query->row_array();
            if (!isset($row)) {
                $this->db->insert('chapter', $chapter);
                return $this->db->insert_id();
            } else {
                return $row['id'];
            }
        }

        public function getUndetailedChapterId(){
            $query = $this->db->query('SELECT DISTINCT id FROM chapter WHERE id NOT IN (SELECT chapter_id from chapter_image) ORDER BY `date` ASC LIMIT 5');
            $rows = $query->result();
            return $rows;
        }

        public function getUndetailedSourceUrl($baseUrl){
            $query = $this->db->query('SELECT DISTINCT source_chapter_url FROM chapter WHERE id NOT IN (SELECT chapter_id from chapter_image) and source_url like $baseUrl.`%` ORDER BY `date_ago` ASC LIMIT 1');
            $rows = $query->result();
            return $rows;
        }

        public function getByMangaId($mangaId){
            $this->db->order_by('number', 'DESC');
            $query = $this->db->get_where('chapter', array('manga_id' => $mangaId));
            return $query->result();
        }
        
        public function getListChapter($start, $rowPerPage, $orderBy, $orderType, $mangaId) {
            $this->db->order_by($orderBy, $orderType);
            $this->db->limit($rowPerPage, $start);
            $query = $this->db->get_where('chapter', array('manga_id' => $mangaId));
            return $query->result();
        }

        public function getByChapterId($chapterId){
            $this->db->order_by('id', 'ASC');
            $query = $this->db->get_where('chapter_image', array('chapter_id' => $chapterId));
            return $query->result();
        }

        public function countMangaChapter($mangaId, $chapterNumber) {
            $array = array('manga_id' => $mangaId, 'number' => $chapterNumber);
            $this->db->where($array);
            $this->db->from('chapter');
            return $this->db->count_all_results();
        }

        public function countMangaChapterByTitle($mangaId, $title) {
            $array = array('manga_id' => $mangaId, 'title' => $title);
            $this->db->where($array);
            $this->db->from('chapter');
            return $this->db->count_all_results();
        }
        
        public function countMangaChapterByMangaId($mangaId) {
            $array = array('manga_id' => $mangaId);
            $this->db->where($array);
            $this->db->from('chapter');
            return $this->db->count_all_results();
        }

        public function getLastChapterByMangaId($mangaId){
            $this->db->order_by('number', 'DESC');
            $this->db->limit(1);
            $query = $this->db->get_where('chapter', array('manga_id' => $mangaId));
            return $query->row_array();
        }

        public function getChapterById($chapterId){
            $query = $this->db->get_where('chapter', array('id' => $chapterId));
            return $query->row_array();
        }

        public function getPrevChapter($mangaId, $currentChapterNumber) {
            $this->db->order_by('number', 'DESC');
            $this->db->limit(1);
            $query = $this->db->get_where('chapter', array('manga_id' => $mangaId, 'number < ' => $currentChapterNumber));
            return $query->row_array();
        }

        public function getNextChapter($mangaId, $currentChapterNumber) {
            $this->db->order_by('number', 'ASC');
            $this->db->limit(1);
            $query = $this->db->get_where('chapter', array('manga_id' => $mangaId, 'number > ' => $currentChapterNumber));
            return $query->row_array();
        }
    }
?>