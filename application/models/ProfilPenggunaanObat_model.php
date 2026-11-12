<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ProfilPenggunaanObat_model extends CI_Model
{
    public function getByUser($idUser)
    {
        return $this->db
            ->where('id_user', (int) $idUser)
            ->get('profil_penggunaan_obat')
            ->row_array();
    }

    public function getById($idProfil)
    {
        return $this->db
            ->select('profil_penggunaan_obat.*, user.nama, user.email, user.no_hp, status_user.nama AS status_user_nama')
            ->from('profil_penggunaan_obat')
            ->join('user', 'user.id = profil_penggunaan_obat.id_user', 'inner')
            ->join('status_user', 'status_user.id = user.id_status', 'left')
            ->where('profil_penggunaan_obat.id', (int) $idProfil)
            ->get()
            ->row_array();
    }

    public function getAll()
    {
        return $this->db
            ->select('profil_penggunaan_obat.*, user.nama, user.email, user.no_hp, status_user.nama AS status_user_nama')
            ->from('profil_penggunaan_obat')
            ->join('user', 'user.id = profil_penggunaan_obat.id_user', 'inner')
            ->join('status_user', 'status_user.id = user.id_status', 'left')
            ->order_by('profil_penggunaan_obat.updated_at', 'DESC')
            ->get()
            ->result_array();
    }

    public function saveForUser($idUser, array $data)
    {
        $existing = $this->getByUser($idUser);
        $now = date('Y-m-d H:i:s');
        $data['id_user'] = (int) $idUser;
        $data['updated_at'] = $now;

        $this->db->trans_begin();
        if ($existing) {
            $idProfil = (int) $existing['id'];
            $this->db->where('id', $idProfil)->update('profil_penggunaan_obat', $data);
        } else {
            $data['created_at'] = $now;
            $this->db->insert('profil_penggunaan_obat', $data);
            $idProfil = (int) $this->db->insert_id();
        }

        if ($this->db->trans_status() === FALSE || $idProfil <= 0) {
            $this->db->trans_rollback();
            return FALSE;
        }

        $this->db->trans_commit();
        return $idProfil;
    }

    public function updateByAdmin($idProfil, array $data)
    {
        $profile = $this->getById($idProfil);
        if (!$profile) {
            return FALSE;
        }

        unset($data['id_user'], $data['created_at']);
        $now = date('Y-m-d H:i:s');
        $data['updated_at'] = $now;

        $this->db->trans_begin();
        $this->db->where('id', (int) $idProfil)->update('profil_penggunaan_obat', $data);

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return FALSE;
        }

        $this->db->trans_commit();
        return TRUE;
    }

    public function delete($idProfil)
    {
        return $this->db
            ->where('id', (int) $idProfil)
            ->delete('profil_penggunaan_obat');
    }

}
