<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kuesioner_model extends CI_Model
{
    public function tableExists($table)
    {
        return $this->db->table_exists($table);
    }

    public function getActiveCategoriesWithQuestions($idStatus = NULL)
    {
        if (!$this->tableExists('kuesioner_kategori') || !$this->tableExists('kuesioner_pertanyaan')) {
            return array();
        }

        $this->db->where('status', 1);
        if ($idStatus !== NULL && $this->db->field_exists('id_status', 'kuesioner_kategori')) {
            $this->db->where('id_status', (int) $idStatus);
        }

        $categories = $this->db
            ->order_by('urutan', 'ASC')
            ->order_by('id', 'ASC')
            ->get('kuesioner_kategori')
            ->result_array();

        foreach ($categories as &$category) {
            $category = $this->attachActiveQuestions($category);
        }
        unset($category);

        return $categories;
    }

    public function getActiveCategoryWithQuestionsById($categoryId)
    {
        if (!$this->tableExists('kuesioner_kategori')) {
            return array();
        }

        $category = $this->db
            ->where('id', (int) $categoryId)
            ->where('status', 1)
            ->get('kuesioner_kategori')
            ->row_array();

        return $category ? $this->attachActiveQuestions($category) : array();
    }

    private function attachActiveQuestions($category)
    {
        $category['pertanyaan'] = $this->db
            ->where('kategori_id', (int) $category['id'])
            ->where('status', 1)
            ->order_by('urutan', 'ASC')
            ->order_by('id', 'ASC')
            ->get('kuesioner_pertanyaan')
            ->result_array();

        foreach ($category['pertanyaan'] as &$question) {
            $question['opsi'] = $this->getActiveOptionsByQuestionId($question['id']);
        }
        unset($question);

        return $category;
    }

    public function getActiveOptionsByQuestionId($questionId)
    {
        if (!$this->tableExists('kuesioner_opsi')) {
            return array();
        }

        return $this->db
            ->where('pertanyaan_id', (int) $questionId)
            ->where('status', 1)
            ->order_by('urutan', 'ASC')
            ->order_by('id', 'ASC')
            ->get('kuesioner_opsi')
            ->result_array();
    }

    public function getAllCategories()
    {
        if (!$this->tableExists('kuesioner_kategori')) {
            return array();
        }

        return $this->db
            ->select('kuesioner_kategori.*, status_user.nama AS status_nama')
            ->from('kuesioner_kategori')
            ->join('status_user', 'status_user.id = kuesioner_kategori.id_status', 'left')
            ->order_by('kuesioner_kategori.urutan', 'ASC')
            ->order_by('kuesioner_kategori.id', 'ASC')
            ->get()
            ->result_array();
    }

    public function getAllQuestions()
    {
        if (!$this->tableExists('kuesioner_pertanyaan')) {
            return array();
        }

        return $this->db
            ->select('kuesioner_pertanyaan.*, kuesioner_kategori.nama AS kategori_nama')
            ->from('kuesioner_pertanyaan')
            ->join('kuesioner_kategori', 'kuesioner_kategori.id = kuesioner_pertanyaan.kategori_id', 'left')
            ->order_by('kuesioner_kategori.urutan', 'ASC')
            ->order_by('kuesioner_pertanyaan.urutan', 'ASC')
            ->order_by('kuesioner_pertanyaan.id', 'ASC')
            ->get()
            ->result_array();
    }

    public function getAllOptions()
    {
        if (!$this->tableExists('kuesioner_opsi')) {
            return array();
        }

        return $this->db
            ->select('kuesioner_opsi.*, kuesioner_pertanyaan.kode AS pertanyaan_kode, kuesioner_pertanyaan.pertanyaan AS pertanyaan_teks')
            ->from('kuesioner_opsi')
            ->join('kuesioner_pertanyaan', 'kuesioner_pertanyaan.id = kuesioner_opsi.pertanyaan_id', 'left')
            ->order_by('kuesioner_pertanyaan.urutan', 'ASC')
            ->order_by('kuesioner_opsi.urutan', 'ASC')
            ->order_by('kuesioner_opsi.id', 'ASC')
            ->get()
            ->result_array();
    }

    public function getCategoryById($id)
    {
        return $this->db->where('id', (int) $id)->get('kuesioner_kategori')->row_array();
    }

    public function getQuestionById($id)
    {
        return $this->db->where('id', (int) $id)->get('kuesioner_pertanyaan')->row_array();
    }

    public function getOptionById($id)
    {
        return $this->db->where('id', (int) $id)->get('kuesioner_opsi')->row_array();
    }

    public function insertCategory($data)
    {
        return $this->db->insert('kuesioner_kategori', $data);
    }

    public function updateCategory($id, $data)
    {
        return $this->db->where('id', (int) $id)->update('kuesioner_kategori', $data);
    }

    public function deleteCategory($id)
    {
        return $this->db->where('id', (int) $id)->delete('kuesioner_kategori');
    }

    public function insertQuestion($data)
    {
        return $this->db->insert('kuesioner_pertanyaan', $data);
    }

    public function updateQuestion($id, $data)
    {
        return $this->db->where('id', (int) $id)->update('kuesioner_pertanyaan', $data);
    }

    public function deleteQuestion($id)
    {
        return $this->db->where('id', (int) $id)->delete('kuesioner_pertanyaan');
    }

    public function insertOption($data)
    {
        return $this->db->insert('kuesioner_opsi', $data);
    }

    public function updateOption($id, $data)
    {
        return $this->db->where('id', (int) $id)->update('kuesioner_opsi', $data);
    }

    public function deleteOption($id)
    {
        return $this->db->where('id', (int) $id)->delete('kuesioner_opsi');
    }

    public function questionCodeExists($kode, $excludeId = NULL)
    {
        $this->db->where('kode', $kode);
        if ($excludeId !== NULL) {
            $this->db->where('id !=', (int) $excludeId);
        }

        return (int) $this->db->count_all_results('kuesioner_pertanyaan') > 0;
    }

    public function getFilledDemographicSubmissions()
    {
        if (!$this->tableExists('demografi') || !$this->tableExists('jawaban_kuesioner')) {
            return array();
        }

        $this->db
            ->select('demografi.*')
            ->select('user.id AS user_id, user.nama, user.email, user.no_hp, user.id_status')
            ->select('status_user.nama AS status_user_nama')
            ->select('(SELECT COUNT(*) FROM jawaban_kuesioner jawaban_count WHERE jawaban_count.id_demografi = demografi.id_demografi) AS total_jawaban', FALSE)
            ->from('demografi')
            ->join('user', 'user.id = demografi.id_user', 'left')
            ->join('status_user', 'status_user.id = user.id_status', 'left');

        if ($this->tableExists('kuesioner_pertanyaan') && $this->tableExists('kuesioner_kategori')) {
            $this->db->select('(SELECT kategori.nama FROM jawaban_kuesioner jawaban_kategori INNER JOIN kuesioner_pertanyaan pertanyaan_kategori ON pertanyaan_kategori.kode = jawaban_kategori.pertanyaan INNER JOIN kuesioner_kategori kategori ON kategori.id = pertanyaan_kategori.kategori_id WHERE jawaban_kategori.id_demografi = demografi.id_demografi ORDER BY kategori.urutan ASC, kategori.id ASC LIMIT 1) AS kategori_nama', FALSE);
        } else {
            $this->db->select('NULL AS kategori_nama', FALSE);
        }

        return $this->db
            ->where('EXISTS (SELECT 1 FROM jawaban_kuesioner jawaban_exists WHERE jawaban_exists.id_demografi = demografi.id_demografi)', NULL, FALSE)
            ->order_by('demografi.created_at', 'DESC')
            ->order_by('demografi.id_demografi', 'DESC')
            ->get()
            ->result_array();
    }

    public function getDemographicSubmissionById($idDemografi)
    {
        if (!$this->tableExists('demografi')) {
            return array();
        }

        return $this->db
            ->select('demografi.*')
            ->select('user.id AS user_id, user.nama, user.email, user.no_hp, user.id_status')
            ->select('status_user.nama AS status_user_nama')
            ->from('demografi')
            ->join('user', 'user.id = demografi.id_user', 'left')
            ->join('status_user', 'status_user.id = user.id_status', 'left')
            ->where('demografi.id_demografi', (int) $idDemografi)
            ->get()
            ->row_array();
    }

    public function getLatestDemographicSubmissionByUser($idUser)
    {
        if (!$this->tableExists('demografi') || !$this->tableExists('jawaban_kuesioner')) {
            return array();
        }

        return $this->db
            ->select('demografi.*')
            ->select('user.id AS user_id, user.nama, user.no_hp, user.id_status')
            ->select('status_user.nama AS status_user_nama')
            ->select('(SELECT COUNT(*) FROM jawaban_kuesioner jawaban_count WHERE jawaban_count.id_demografi = demografi.id_demografi) AS total_jawaban', FALSE)
            ->from('demografi')
            ->join('user', 'user.id = demografi.id_user', 'inner')
            ->join('status_user', 'status_user.id = user.id_status', 'left')
            ->where('demografi.id_user', (int) $idUser)
            ->where('EXISTS (SELECT 1 FROM jawaban_kuesioner jawaban_exists WHERE jawaban_exists.id_demografi = demografi.id_demografi)', NULL, FALSE)
            ->order_by('demografi.created_at', 'DESC')
            ->order_by('demografi.id_demografi', 'DESC')
            ->limit(1)
            ->get()
            ->row_array();
    }

    public function getDemographicSubmissionCategory($idDemografi)
    {
        if (
            !$this->tableExists('jawaban_kuesioner') ||
            !$this->tableExists('kuesioner_pertanyaan') ||
            !$this->tableExists('kuesioner_kategori')
        ) {
            return array();
        }

        $category = $this->db
            ->select('kuesioner_kategori.*')
            ->from('jawaban_kuesioner')
            ->join('kuesioner_pertanyaan', 'kuesioner_pertanyaan.kode = jawaban_kuesioner.pertanyaan', 'inner')
            ->join('kuesioner_kategori', 'kuesioner_kategori.id = kuesioner_pertanyaan.kategori_id', 'inner')
            ->where('jawaban_kuesioner.id_demografi', (int) $idDemografi)
            ->order_by('kuesioner_kategori.urutan', 'ASC')
            ->order_by('kuesioner_kategori.id', 'ASC')
            ->limit(1)
            ->get()
            ->row_array();

        if ($category) {
            return $category;
        }

        $submission = $this->getDemographicSubmissionById($idDemografi);
        if (!empty($submission['jenis_responden'])) {
            return $this->db
                ->where('nama', $submission['jenis_responden'])
                ->where('status', 1)
                ->get('kuesioner_kategori')
                ->row_array();
        }

        return array();
    }

    public function getDemographicAnswers($idDemografi)
    {
        if (!$this->tableExists('jawaban_kuesioner')) {
            return array();
        }

        $this->db
            ->select('jawaban_kuesioner.*')
            ->from('jawaban_kuesioner');

        if ($this->tableExists('kuesioner_pertanyaan')) {
            $this->db
                ->select('kuesioner_pertanyaan.id AS pertanyaan_id, kuesioner_pertanyaan.kode, kuesioner_pertanyaan.pertanyaan AS item, kuesioner_pertanyaan.tipe, kuesioner_pertanyaan.wajib, kuesioner_pertanyaan.urutan')
                ->join('kuesioner_pertanyaan', 'kuesioner_pertanyaan.kode = jawaban_kuesioner.pertanyaan', 'left');

            if ($this->tableExists('kuesioner_kategori')) {
                $this->db
                    ->select('kuesioner_kategori.id AS kategori_id, kuesioner_kategori.nama AS kategori_nama, kuesioner_kategori.urutan AS kategori_urutan')
                    ->join('kuesioner_kategori', 'kuesioner_kategori.id = kuesioner_pertanyaan.kategori_id', 'left')
                    ->order_by('kuesioner_kategori.urutan', 'ASC');
            }

            $this->db->order_by('kuesioner_pertanyaan.urutan', 'ASC');
        }

        return $this->db
            ->where('jawaban_kuesioner.id_demografi', (int) $idDemografi)
            ->order_by('jawaban_kuesioner.id_jawaban', 'ASC')
            ->get()
            ->result_array();
    }

    public function getDemographicAnswerMap($idDemografi)
    {
        $answers = array();
        foreach ($this->getDemographicAnswers($idDemografi) as $answer) {
            if (!empty($answer['pertanyaan_id'])) {
                $answers[(int) $answer['pertanyaan_id']] = $answer['jawaban'];
            }
            $answers[$answer['pertanyaan']] = $answer['jawaban'];
        }

        return $answers;
    }

    public function updateDemographicSubmission($idDemografi, $dataDemografi, $dataAnswers)
    {
        $this->db->trans_begin();

        $this->db
            ->where('id_demografi', (int) $idDemografi)
            ->update('demografi', $dataDemografi);

        $this->db
            ->where('id_demografi', (int) $idDemografi)
            ->delete('jawaban_kuesioner');

        foreach ($dataAnswers as &$answer) {
            $answer['id_demografi'] = (int) $idDemografi;
        }
        unset($answer);

        if (!empty($dataAnswers)) {
            $this->db->insert_batch('jawaban_kuesioner', $dataAnswers);
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return FALSE;
        }

        $this->db->trans_commit();
        return TRUE;
    }

    public function deleteDemographicSubmission($idDemografi)
    {
        $this->db->trans_begin();
        $this->db->where('id_demografi', (int) $idDemografi)->delete('jawaban_kuesioner');
        $this->db->where('id_demografi', (int) $idDemografi)->delete('demografi');

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return FALSE;
        }

        $this->db->trans_commit();
        return TRUE;
    }

    public function deleteDemographicSubmissions($demographicIds)
    {
        if (!is_array($demographicIds) || empty($demographicIds)) {
            return 0;
        }

        $demographicIds = array_values(array_unique(array_map('intval', $demographicIds)));
        $demographicIds = array_values(array_filter($demographicIds, function ($idDemografi) {
            return $idDemografi > 0;
        }));

        if (empty($demographicIds)) {
            return 0;
        }

        $this->db->trans_begin();
        $this->db->where_in('id_demografi', $demographicIds)->delete('jawaban_kuesioner');
        $this->db->where_in('id_demografi', $demographicIds)->delete('demografi');
        $deletedCount = $this->db->affected_rows();

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return FALSE;
        }

        $this->db->trans_commit();
        return $deletedCount;
    }

    public function save_kuesioner_data($dataDemografi, $dataJawaban)
    {
        $this->db->trans_begin();
        $this->db->insert('demografi', $dataDemografi);
        $idDemografi = (int) $this->db->insert_id();

        foreach ($dataJawaban as &$jawaban) {
            $jawaban['id_demografi'] = $idDemografi;
        }
        unset($jawaban);

        if (!empty($dataJawaban)) {
            $this->db->insert_batch('jawaban_kuesioner', $dataJawaban);
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return FALSE;
        }

        $this->db->trans_commit();
        return $idDemografi;
    }
}
