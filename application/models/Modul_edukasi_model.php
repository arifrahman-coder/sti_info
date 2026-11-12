<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Modul_edukasi_model extends CI_Model {

    private $table = 'modul_edukasi';

    public function getModulEdukasi()
    {
        return $this->db
            ->order_by('id', 'ASC')
            ->get($this->table)
            ->result_array();
    }

    public function getById($id)
    {
        return $this->db
            ->where('id', (int) $id)
            ->get($this->table)
            ->row_array();
    }

    public function insert($data)
    {
        return $this->db->insert($this->table, $data);
    }

    public function update($id, $data)
    {
        return $this->db
            ->where('id', (int) $id)
            ->update($this->table, $data);
    }

    public function delete($id)
    {
        return $this->db
            ->where('id', (int) $id)
            ->delete($this->table);
    }

}
