<?php
    if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    
    class MangaDao extends CI_Model{
        
        public function insertNewManga($manga) {
            $this->db->where('id', $manga['id']);       
            $query = $this->db->get('manga');
            $row = $query->row_array();
            if (!isset($row)) {
                $this->db->insert('manga', $manga);
            }
            else {
                $this->db->where('id', $manga['id']);
                unset($manga['id']);
                $this->db->update('manga', $manga);
            }
        }

        public function mapMangaArtist($mangaId, $artistId) {
            $array = array('manga_id' => $mangaId, 'artist_id' => $artistId);
            $this->db->where($array);
            $query = $this->db->get('manga_artist');
            $row = $query->row_array();
            if (!isset($row)) {
                try {
                    $mangaArtist = array(
                        'manga_id' => $mangaId,
                        'artist_id' => $artistId
                    );
                    $this->db->insert('manga_artist', $mangaArtist);
                }
                catch (Exception $e) {
                    //log_message('error', $e->getMessage());
                }
            }
        }
        
        public function mapMangaAuthor($mangaId, $authorId) {
            $array = array('manga_id' => $mangaId, 'author_id' => $authorId);
            $this->db->where($array);
            $query = $this->db->get('manga_author');
            $row = $query->row_array();
            if (!isset($row)) {
                try {
                    $mangaAuthor = array(
                        'manga_id' => $mangaId,
                        'author_id' => $authorId
                    );
                    $this->db->insert('manga_author', $mangaAuthor);
                }
                catch (Exception $e) {
                    //log_message('error', $e->getMessage());
                }
                
            }
        }

        public function getListManga($start, $rowPerPage, $searchString, $orderBy, $orderType) {
            if (isset($searchString)) {
                $this->db->like('title', $searchString);
            }
            
            $this->db->order_by($orderBy, $orderType);
            $this->db->limit($rowPerPage, $start);
            $query = $this->db->get('manga');
            return $query->result_array();
        }

        public function countManga($searchString) {
            if (isset($searchString)) {
                $this->db->like('title', $searchString);
            }
            $this->db->from('manga');
            return $this->db->count_all_results();
        }

        public function getDetailManga($mangaId) {
            $query = $this->db->get_where('manga', array('id' => $mangaId));
            return $query->row_array(0);
        }
        
        public function updateManga($manga) {
            $this->db->where('id', $manga['id']);
            unset($manga['id']);
            $this->db->update('manga', $manga);
        }

    }
?>