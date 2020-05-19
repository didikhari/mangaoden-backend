<?php
    if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    
    class ArtistDao extends CI_Model{
        
        public function getByMangaId($mangaId) {
            $this->db->select('name');
            $this->db->from('artist');
            $this->db->join('manga_artist', 'artist.id = manga_artist.artist_id');
            $this->db->where('manga_artist.manga_id', $mangaId);
            $this->db->order_by('artist.name', 'ASC');
            $query = $this->db->get();
            return $query->result();
        }
    }
?>