<?php
    if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    
    class SourceDao extends CI_Model{
        
        public function getById($sourceMangaId) {
            $query = $this->db->get_where('source', array('id' => $sourceMangaId));
            return $query->row_array();
        }
    }
?>