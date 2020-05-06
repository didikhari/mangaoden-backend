<?php
    if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    
    class SequenceDao extends CI_Model{
        
        public function nextval($sequenceName, $incrementBy){
            $query = $this->db->select()
                        ->from('sequence')
                        ->where('name', $sequenceName)
                        ->limit(1)
                        ->get_compiled_select();
            $row = $this->db->query("{$query} FOR UPDATE")->row_array();
            if (!isset($row)) {
                $newSeq = array(
                    'name' => $sequenceName,
                    'value' => '0'
                );
                $this->db->insert('sequence', $newSeq);
                return 0;
            }
            else {
                $this->db->set('value', $row['value'] + $incrementBy);
                $this->db->where('name', $row['name']);
                $this->db->update('sequence');
                return $row['value'] + $incrementBy;
            }
        }
    }
?>