<?php
    if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    
    class SourceDao extends CI_Model{
        
        public function getId($mangaId) {
            $query = $this->db->get_where('source', array('id' => $mangaId));
            return $query->row_array();
        }
    }
?>