<?php
    if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    
    class AuthorDao extends CI_Model{
        
        public function getByMangaId($mangaId) {
            $this->db->select('name');
            $this->db->from('author');
            $this->db->join('manga_author', 'author.id = manga_author.author_id');
            $this->db->where('manga_author.manga_id', $mangaId);
            $this->db->order_by('author.name', 'ASC');
            $query = $this->db->get();
            return $query->result();
        }
    }
?>