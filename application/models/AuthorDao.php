<?php
    if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    
    class AuthorDao extends CI_Model{
        
        public function save($name){
            $array = array('name' => $name);
            $this->db->where($array);
            $query = $this->db->get('author');
            $row = $query->row_array();
            if (!isset($row)) {
                $newAuthor = array(
                    'name' => $name,
                );
                $this->db->insert('author', $newAuthor);
                return $this->db->insert_id();
            } else {
                return $row['id'];
            }
        }
    }
?>