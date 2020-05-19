<?php
    if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    
    class SliderImageDao extends CI_Model{
        
        public function findAll(){
            $query = $this->db->get('slider_image');
            return $query->result_array();
        }
    }
?>