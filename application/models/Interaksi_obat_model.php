<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Interaksi_obat_model extends CI_Model
{
    private $table = 'interaksi_obat';

    public function getReferences()
    {
        $this->config->load('interaksi_obat_prep');
        return $this->config->item('interaksi_obat_referensi');
    }

    public function getAll($activeOnly = FALSE)
    {
        if (!$this->db->table_exists($this->table)) {
            return array();
        }
        if ($activeOnly) {
            $this->db->where('status', 1);
        }
        return $this->db->order_by('urutan', 'ASC')->order_by('nama_obat', 'ASC')->get($this->table)->result_array();
    }

    public function getById($id, $activeOnly = FALSE)
    {
        if (!$this->db->table_exists($this->table)) {
            return array();
        }
        $this->db->where('id', (int) $id);
        if ($activeOnly) {
            $this->db->where('status', 1);
        }
        return $this->db->get($this->table)->row_array();
    }

    public function nameExists($name, $excludeId = NULL)
    {
        $this->db->where('LOWER(nama_obat) =', mb_strtolower(trim((string) $name)));
        if ($excludeId !== NULL) {
            $this->db->where('id !=', (int) $excludeId);
        }
        return $this->db->count_all_results($this->table) > 0;
    }

    public function insert($data)
    {
        return $this->db->insert($this->table, $data);
    }

    public function update($id, $data)
    {
        return $this->db->where('id', (int) $id)->update($this->table, $data);
    }

    public function delete($id)
    {
        return $this->db->where('id', (int) $id)->delete($this->table);
    }
}
