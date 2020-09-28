<?php
    if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    
    class ChapterImageDao extends CI_Model{
        
        public function save($chapterImage){
            $array = array('id' => $chapterImage['id'], 'chapter_id' => $chapterImage['chapter_id']);
            $this->db->where($array);
            $query = $this->db->get('chapter_image');
            $row = $query->row_array();
            if (!isset($row)) {
                $this->db->insert('chapter_image', $chapterImage);
            }
        }

        public function saveBatch($data) {
             $this->db->insert_batch('chapter_image', $data);
        }

        public function updateGdriveId($imageId, $fileId) {
            $this->db->set('gdrive_id', $fileId);
            $this->db->where('id', $imageId);
            $this->db->update('chapter_image');
        }

        public function getUnUploadedChapterId($mangaId) {
            $query = $this->db->query('SELECT MIN(chapter_id) as min_chapter_id FROM `chapter_image` WHERE gdrive_id is null and chapter_id in (select id from chapter where manga_id = '.$mangaId.') ');
            return $query->row_array();
        }

    }
?>