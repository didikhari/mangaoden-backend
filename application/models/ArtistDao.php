<?php
    if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    
    class ArtistDao extends CI_Model{
        
        public function save($name){
            $array = array('name' => $name);
            $this->db->where($array);
            $query = $this->db->get('artist');
            $row = $query->row_array();
            if (!isset($row)) {
                $newArtist = array(
                    'name' => $name,
                );
                $this->db->insert('artist', $newArtist);
                return $this->db->insert_id();
            } else {
                return $row['id'];
            }
        }
    }
?>