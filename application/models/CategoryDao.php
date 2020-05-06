<?php
    if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    
    class CategoryDao extends CI_Model{
        
        public function getOrInsertByName($name){
            $this->db->where('name', $name);       
            $query = $this->db->get('category');
            $row = $query->row_array();
            if (!isset($row)) {
                $category = array(
                    'name' => $name
                );
                $this->db->insert('category', $category);
                return $this->db->insert_id();
            } else {
                return $row['id'];
            }
        }

        public function mapMangaWithCategory($mangaId, $categoryId) {
            $array = array('manga_id' => $mangaId, 'category_id' => $categoryId);
            $this->db->where($array);      
            $query = $this->db->get('manga_category');
            $row = $query->row_array();
            if (!isset($row)) {
                try {
                    $mangaCategory = array(
                        'manga_id' => $mangaId,
                        'category_id' => $categoryId
                    );
                    $this->db->insert('manga_category', $mangaCategory);
                }
                catch (Exception $e) {
                    //log_message('error', $e->getMessage());
                }
                
            }
        }
    }
?>