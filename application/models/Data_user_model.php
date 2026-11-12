<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Data_user_model extends CI_Model {

    private $table = 'user';

    public function getAll()
    {
        return $this->db
            ->select('u.*, status_user.nama as nama_status')
            ->from($this->table.' u')
            ->join('status_user', 'status_user.id = u.id_status', 'left')
            ->order_by('u.id', 'DESC')
            ->get()
            ->result_array();
    }

    public function getById($id)
    {
        return $this->db
            ->where('id', (int) $id)
            ->get($this->table)
            ->row_array();
    }

    public function getStatusUser()
    {
        return $this->db
            ->order_by('id', 'ASC')
            ->get('status_user')
            ->result_array();
    }

    public function statusExists($id)
    {
        return $this->db
            ->where('id', (int) $id)
            ->count_all_results('status_user') > 0;
    }

    public function emailExists($email, $excludeId = NULL)
    {
        $this->db->where('email', $email);

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

    public function deleteBulk($ids)
    {
        $cleanIds = array();
        foreach ($ids as $id) {
            $id = (int) $id;
            if ($id > 0) {
                $cleanIds[] = $id;
            }
        }

        $cleanIds = array_values(array_unique($cleanIds));
        if (empty($cleanIds)) {
            return 0;
        }

        $this->db
            ->where_in('id', $cleanIds)
            ->delete($this->table);

        return $this->db->affected_rows();
    }
}
