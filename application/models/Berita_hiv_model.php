<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Berita_hiv_model extends CI_Model {

    private $table = 'berita_hiv';

    public function tableExists()
    {
        return $this->db->table_exists($this->table);
    }

    public function getAll()
    {
        if (!$this->tableExists()) {
            return array();
        }

        return $this->db
            ->order_by('urutan', 'ASC')
            ->order_by('date', 'DESC')
            ->get($this->table)
            ->result_array();
    }

    public function getAktif($limit = 6)
    {
        if (!$this->tableExists()) {
            return array();
        }

        return $this->db
            ->where('status', 1)
            ->order_by('urutan', 'ASC')
            ->order_by('date', 'DESC')
            ->limit((int) $limit)
            ->get($this->table)
            ->result_array();
    }

    public function getById($id)
    {
        if (!$this->tableExists()) {
            return array();
        }

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
