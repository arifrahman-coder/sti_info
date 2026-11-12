<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home_model extends CI_Model {

    private $registerStatusNames = array(
        'Masyarakat',
        'Populasi Kunci',
        'ODHIV',
        'Tenaga Kesehatan'
    );

    private $registerStatusDescriptions = array(
        'Masyarakat' => 'masyarakat umum',
        'Populasi Kunci' => 'kelompok yang berisiko tinggi tertular atau menularkan HIV',
        'ODHIV' => 'Orang dengan HIV',
        'Tenaga Kesehatan' => 'tim skrining, tenaga medis, perawat dan juga dokter'
    );

    public function getPenyakit()
    {
        $query = $this->db->query("SELECT * FROM penyakit")->result_array();
        return $query;
    }

    public function getModulPembelajaran()
    {
        $query = $this->db->query("SELECT * FROM modul_pembelajaran")->result_array();
        return $query;
    }

    public function getModulEdukasi()
    {
        $query = $this->db->query("SELECT * FROM modul_edukasi")->result_array();
        return $query;
    }

    public function getBeritaHiv()
    {
        if (!$this->db->table_exists('berita_hiv')) {
            return array();
        }

        $query = $this->db
            ->where('status', 1)
            ->order_by('urutan', 'ASC')
            ->order_by('date', 'DESC')
            ->limit(6)
            ->get('berita_hiv')
            ->result_array();

        return $query;
    }

    public function getStatusUser()
    {
        if (!$this->db->table_exists('status_user')) {
            return array();
        }

        $hasDeskripsi = $this->db->field_exists('deskripsi', 'status_user');
        $select = $hasDeskripsi ? 'id, nama, deskripsi' : 'id, nama';

        $query = $this->db
            ->select($select)
            ->where_in('nama', $this->registerStatusNames)
            ->order_by('id', 'ASC')
            ->get('status_user')
            ->result_array();

        if (empty($query)) {
            return array();
        }

        $statusByName = array();
        foreach ($query as $statusUser) {
            $nama = $statusUser['nama'];
            if (!isset($this->registerStatusDescriptions[$nama])) {
                continue;
            }

            if (!isset($statusUser['deskripsi']) || trim($statusUser['deskripsi']) == '') {
                $statusUser['deskripsi'] = $this->registerStatusDescriptions[$nama];
            }

            $statusByName[$nama] = $statusUser;
        }

        $orderedStatus = array();
        foreach ($this->registerStatusNames as $statusName) {
            if (isset($statusByName[$statusName])) {
                $orderedStatus[] = $statusByName[$statusName];
            }
        }

        return $orderedStatus;
    }

    public function statusUserExists($id)
    {
        foreach ($this->getStatusUser() as $statusUser) {
            if ((int) $statusUser['id'] === (int) $id) {
                return TRUE;
            }
        }

        return FALSE;
    }

}
