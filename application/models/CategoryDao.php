<?php
    if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    
    class CategoryDao extends CI_Model{

        public function getByMangaId($mangaId) {
            $this->db->select('name');
            $this->db->from('genre');
            $this->db->join('manga_genre', 'genre.id = manga_genre.genre_id');
            $this->db->where('manga_genre.manga_id', $mangaId);
            $this->db->order_by('genre.name', 'ASC');
            $query = $this->db->get();
            return $query->result();
        }
    }
?>