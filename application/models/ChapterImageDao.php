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
    }
?>