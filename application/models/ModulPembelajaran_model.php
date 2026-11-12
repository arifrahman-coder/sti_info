<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ModulPembelajaran_model extends CI_Model {

    public function getTitleModulPembelajaran($id_penyakit)
    {
        $query = $this->db->query("
            SELECT 
            penyakit.nama as nama_penyakit
            FROM penyakit
            WHERE id = $id_penyakit
            ");
        return $query->row_array();
    }

    public function getModulPembelajaran($id_penyakit)
    {
        $query = $this->db->query("
            SELECT 
            *
            FROM modul_pembelajaran
            WHERE modul_pembelajaran.id_penyakit = $id_penyakit
            ");
        return $query->result_array();
    }

    public function getDetailModul($id_detail_modul)
    {
        $query = $this->db->query("
            SELECT 
            *
            FROM detail_modul
            WHERE id_modul_pembelajaran = $id_detail_modul
            ");
        return $query->row_array();
    }

    public function prosesBelajarGetID($detailModul,$listDetailModul)
    {
        $idUser = $this->session->userdata('id_user');
        $query = $this->db->query("SELECT id FROM proses_belajar WHERE id_modul_pembelajaran = ".$detailModul." AND id_list_detail_modul = ".$listDetailModul." AND id_user = ".$idUser." ")->row_array();
        return @$query['id'];
    }

    public function getProsesBljr($idModulPembelajaran,$choose)
    {
        if($choose == "get_list_detail"){
        $dataModulBljr = $this->db->query("
            SELECT 
            modul_pembelajaran.id as modul_pembelajaran_id,
            detail_modul.id as detail_modul_id,
            list_detail_modul.id as list_detail_modul_id
            FROM proses_belajar 
            LEFT JOIN modul_pembelajaran ON modul_pembelajaran.id = proses_belajar.id_modul_pembelajaran
            LEFT JOIN detail_modul ON detail_modul.id = proses_belajar.id_detail_modul
            LEFT JOIN list_detail_modul ON list_detail_modul.id = proses_belajar.id_list_detail_modul
            WHERE 
            modul_pembelajaran.id = ".$idModulPembelajaran."
            ORDER BY detail_modul.id ASC;
            ")->result_array();
        }
        else if($choose == "cek_mod"){
            $idUser = $this->session->userdata('id_user');
            $dataModulBljr = $this->db->query("
            SELECT 
            modul_pembelajaran.id as modul_pembelajaran_id,
            detail_modul.id as detail_modul_id,
            list_detail_modul.id as list_detail_modul_id,
            user.id as user_id
            FROM proses_belajar 
            LEFT JOIN modul_pembelajaran ON modul_pembelajaran.id = proses_belajar.id_modul_pembelajaran
            LEFT JOIN detail_modul ON detail_modul.id = proses_belajar.id_detail_modul
            LEFT JOIN list_detail_modul ON list_detail_modul.id = proses_belajar.id_list_detail_modul
            INNER JOIN user ON user.id = proses_belajar.id_user
            WHERE 
            modul_pembelajaran.id = ".$idModulPembelajaran." AND
            proses_belajar.status = '' AND
            user.id = ".$idUser."
            ORDER BY detail_modul.id ASC;
                ")->row_array();
        }
        else if($choose == "cek_final_mod"){
            $idUser = $this->session->userdata('id_user');
            $dataModulBljr = $this->db->query("
            SELECT 
            modul_pembelajaran.id_penyakit,
            modul_pembelajaran.id as modul_pembelajaran_id,
            detail_modul.id as detail_modul_id,
            list_detail_modul.id as list_detail_modul_id,
            user.id as user_id
            FROM proses_belajar 
            LEFT JOIN modul_pembelajaran ON modul_pembelajaran.id = proses_belajar.id_modul_pembelajaran
            LEFT JOIN detail_modul ON detail_modul.id = proses_belajar.id_detail_modul
            LEFT JOIN list_detail_modul ON list_detail_modul.id = proses_belajar.id_list_detail_modul
            INNER JOIN user ON user.id = proses_belajar.id_user
            WHERE 
            modul_pembelajaran.id = ".$idModulPembelajaran." AND
            proses_belajar.status = 'proses_belajar' AND
            user.id = ".$idUser."
            ORDER BY detail_modul.id ASC;
                ")->row_array();
        }
        else if($choose == "next_mod"){
            $idUser = $this->session->userdata('id_user');
            $dataModulBljr = $this->db->query("
            SELECT 
            modul_pembelajaran.id as modul_pembelajaran_id,
            detail_modul.id as detail_modul_id,
            list_detail_modul.id as list_detail_modul_id,
            user.id as user_id
            FROM proses_belajar 
            LEFT JOIN modul_pembelajaran ON modul_pembelajaran.id = proses_belajar.id_modul_pembelajaran
            LEFT JOIN detail_modul ON detail_modul.id = proses_belajar.id_detail_modul
            LEFT JOIN list_detail_modul ON list_detail_modul.id = proses_belajar.id_list_detail_modul
            INNER JOIN user ON user.id = proses_belajar.id_user
            WHERE 
            modul_pembelajaran.id = ".$idModulPembelajaran." AND
            proses_belajar.status = 'proses_belajar' AND
            user.id = ".$idUser."
            ORDER BY detail_modul.id ASC;
                ")->row_array();
        }
        else if($choose == "next_final"){
            $idUser = $this->session->userdata('id_user');
            $dataModulBljr = $this->db->query("
            SELECT 
            modul_pembelajaran.id_penyakit,
            modul_pembelajaran.id as modul_pembelajaran_id,
            detail_modul.id as detail_modul_id,
            list_detail_modul.id as list_detail_modul_id,
            user.id as user_id
            FROM proses_belajar 
            LEFT JOIN modul_pembelajaran ON modul_pembelajaran.id = proses_belajar.id_modul_pembelajaran
            LEFT JOIN detail_modul ON detail_modul.id = proses_belajar.id_detail_modul
            LEFT JOIN list_detail_modul ON list_detail_modul.id = proses_belajar.id_list_detail_modul
            INNER JOIN user ON user.id = proses_belajar.id_user
            WHERE 
            modul_pembelajaran.id = ".$idModulPembelajaran." AND
            proses_belajar.status = 'selsai' AND
            user.id = ".$idUser."
            ORDER BY detail_modul.id DESC;
                ")->row_array();
        }
        return $dataModulBljr;
        /*$getProsesBljr = $this->db->query("SELECT * FROM proses_belajar")->result();
        return $getProsesBljr;*/
    }

    public function listDetailModul($id_detail_modul)
    {
        $query = $this->db->query("
            SELECT 
            *
            FROM detail_modul
            WHERE id_modul_pembelajaran = $id_detail_modul
            ");
        return $query->result_array();
    }

    public function subBabModul($id_detail_modul){
        $query = $this->db->query("
            SELECT 
            *
            FROM list_detail_modul
            WHERE id = $id_detail_modul
            ");
        return $query->row_array();
    }

    public function getSoal($idListDetailSoal)
    {
        $query = $this->db->query("SELECT * FROM soal WHERE list_detail_modul_id = ".$idListDetailSoal." ")->result_array();
        return $query;
    }

    public function getNilai($idSubModul,$idListDetailSoal){
        $idUser = $this->session->userdata('id_user');
        $getIdSoal = $this->db->query("
            SELECT 
            soal.id as id_soal,
            proses_belajar.id as id_proses_belajar,
            soal.nilai 
            FROM soal 
            INNER JOIN proses_belajar ON soal.list_detail_modul_id = proses_belajar.id_list_detail_modul
            WHERE 
            proses_belajar.id_list_detail_modul = ".$idListDetailSoal." AND 
            proses_belajar.id_user = ".$idUser." AND
            proses_belajar.id_modul_pembelajaran = ".$idSubModul."
             ")->result_array();
        @$nilai = 0;
        foreach ($getIdSoal as $key => $value) {
            $getJawabanNilai = $this->db->query("SELECT jawaban_benar FROM jawaban_user WHERE soal_id = ".$value['id_soal']." AND id_proses_belajar = ".$value['id_proses_belajar']." ")->row_array();
            if(@$getJawabanNilai['jawaban_benar'] == 1){
                @$nilai += $value['nilai'];
            }
            else{
                @$nilai += 0;
            }
        }
        return @$nilai;
    }

    public function inputProsesBljr($idModulPembelajaran = "", $idDetailModul = "", $idListDetailModul = "", $idUser = "", $type = ""){
    if ($type == "mulaiBljr") {
        $existingRows = $this->db
            ->where('id_modul_pembelajaran', (int) $idModulPembelajaran)
            ->where('id_user', (int) $idUser)
            ->count_all_results('proses_belajar');

        if ($existingRows > 0) {
            $activeRows = $this->db
                ->where('id_modul_pembelajaran', (int) $idModulPembelajaran)
                ->where('id_user', (int) $idUser)
                ->where('status', 'proses_belajar')
                ->count_all_results('proses_belajar');

            if ($activeRows === 0) {
                $nextRow = $this->db
                    ->where('id_modul_pembelajaran', (int) $idModulPembelajaran)
                    ->where('id_user', (int) $idUser)
                    ->where('status', '')
                    ->order_by('id', 'ASC')
                    ->get('proses_belajar')
                    ->row_array();
                if ($nextRow) {
                    $this->db->where('id', (int) $nextRow['id'])->update(
                        'proses_belajar',
                        array('status' => 'proses_belajar')
                    );
                }
            }

            return $existingRows;
        }

        $dataModulBljr = $this->db->query("
            SELECT 
            modul_pembelajaran.id as modul_pembelajaran_id,
            detail_modul.id as detail_modul_id,
            detail_modul.jenis_modul,
            list_detail_modul.id as list_detail_modul_id
            FROM modul_pembelajaran 
            LEFT JOIN detail_modul ON detail_modul.id_modul_pembelajaran = modul_pembelajaran.id
            LEFT JOIN list_detail_modul ON list_detail_modul.id_detail_modul = detail_modul.id
            WHERE 
            modul_pembelajaran.id = ".$idModulPembelajaran."
            ORDER BY 
            detail_modul.id ASC,
            list_detail_modul.id ASC;
            ")->result_array();
        $jmlData =  count($dataModulBljr);
        $successCount = 0;
        $hasActiveContent = FALSE;
        
        for ($i = 0; $i < $jmlData; $i++) {
            if(is_null($dataModulBljr[$i]['list_detail_modul_id'])){
                $listDetailModuls = 0;
            }
            else{
                $listDetailModuls = $dataModulBljr[$i]['list_detail_modul_id'];
            }
            if ($dataModulBljr[$i]['jenis_modul'] === 'pendahuluan' && $listDetailModuls === 0) {
                $listStatus = 'selsai';
            } elseif (!$hasActiveContent) {
                $listStatus = 'proses_belajar';
                $hasActiveContent = TRUE;
            } else {
                $listStatus = '';
            }
            $data = array(
                'id_modul_pembelajaran' => $dataModulBljr[$i]['modul_pembelajaran_id'],
                'id_detail_modul' => $dataModulBljr[$i]['detail_modul_id'],
                'id_list_detail_modul' => $listDetailModuls,
                'id_user' => $idUser, // Pastikan 'id_user' diisi dengan benar
                'status' => $listStatus
            );
            
            if ($this->db->insert('proses_belajar', $data)) {
                $successCount++; // Increment jika insert sukses
            }
        }
        
        // Mengembalikan jumlah data yang berhasil disimpan
        return $successCount;
    }
    else if ($type == "prosesBljr") {
        $currentRow = $this->db
            ->where('id_modul_pembelajaran', (int) $idModulPembelajaran)
            ->where('id_user', (int) $idUser)
            ->where('status', 'proses_belajar')
            ->order_by('id', 'ASC')
            ->get('proses_belajar')
            ->row_array();

        if (!$currentRow) {
            return 0;
        }

        $this->db->trans_begin();
        $this->db
            ->where('id', (int) $currentRow['id'])
            ->update('proses_belajar', array('status' => 'selsai'));

        $nextRow = $this->db
            ->where('id_modul_pembelajaran', (int) $idModulPembelajaran)
            ->where('id_user', (int) $idUser)
            ->where('status', '')
            ->order_by('id', 'ASC')
            ->get('proses_belajar')
            ->row_array();

        if ($nextRow) {
            $this->db
                ->where('id', (int) $nextRow['id'])
                ->update('proses_belajar', array('status' => 'proses_belajar'));
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return 0;
        }

        $this->db->trans_commit();
        return $nextRow ? 2 : 1;
    }
    else if ($type == "selsai_bljr") {
        $getUpdateProsesBljr = $this->db->query("
            SELECT 
            proses_belajar.id
            FROM proses_belajar 
            LEFT JOIN modul_pembelajaran ON modul_pembelajaran.id = proses_belajar.id_modul_pembelajaran
            LEFT JOIN detail_modul ON detail_modul.id = proses_belajar.id_detail_modul
            LEFT JOIN list_detail_modul ON list_detail_modul.id = proses_belajar.id_list_detail_modul
            INNER JOIN user ON user.id = proses_belajar.id_user
            WHERE 
            modul_pembelajaran.id = ".$idModulPembelajaran." AND
            proses_belajar.status = 'proses_belajar' AND
            user.id = ".$idUser."
            ORDER BY detail_modul.id ASC;
                ")->row_array();
        if (!$getUpdateProsesBljr) {
            return FALSE;
        }
        $data = array(
            'status' => 'selsai'
        );
        $this->db->where('id', (int) $getUpdateProsesBljr['id']);
        return $this->db->update('proses_belajar', $data);
    }
}

    public function getJawabanUser($id_proses_belajar,$sts){
        if($sts == "row"){
            $query = $this->db->query("
                SELECT 
                count(id) as jml,
                jawaban_user.id
                FROM jawaban_user
                WHERE id_proses_belajar = $id_proses_belajar
                ")->row_array();
            return $query;
        }
        else if($sts == "result"){
            $query = $this->db->query("
                SELECT 
                jawaban_user.id
                FROM jawaban_user
                WHERE id_proses_belajar = $id_proses_belajar
                ")->result_array();
            return $query;
        }
    }

}
