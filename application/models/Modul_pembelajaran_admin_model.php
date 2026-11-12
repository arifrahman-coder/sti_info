<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Modul_pembelajaran_admin_model extends CI_Model
{
    public function getModules()
    {
        return $this->db
            ->select('modul.*')
            ->select('penyakit.nama AS nama_penyakit')
            ->select('(SELECT COUNT(*) FROM detail_modul detail WHERE detail.id_modul_pembelajaran = modul.id) AS total_bab', FALSE)
            ->select('(SELECT COUNT(*) FROM list_detail_modul materi INNER JOIN detail_modul detail ON detail.id = materi.id_detail_modul WHERE detail.id_modul_pembelajaran = modul.id) AS total_materi', FALSE)
            ->select('(SELECT COUNT(*) FROM bank_soal_tes_hiv bank WHERE bank.aktif = 1) AS total_soal', FALSE)
            ->select('(SELECT COUNT(*) FROM modul_tes tes WHERE tes.id_modul_pembelajaran = modul.id) AS total_tes', FALSE)
            ->select('(SELECT COUNT(DISTINCT proses.id_user) FROM proses_belajar proses WHERE proses.id_modul_pembelajaran = modul.id) AS total_peserta', FALSE)
            ->from('modul_pembelajaran modul')
            ->join('penyakit', 'penyakit.id = modul.id_penyakit', 'left')
            ->order_by('modul.id', 'ASC')
            ->get()
            ->result_array();
    }

    public function getDiseases()
    {
        return $this->db->order_by('nama', 'ASC')->get('penyakit')->result_array();
    }

    public function diseaseExists($diseaseId)
    {
        return $this->db->where('id', (int) $diseaseId)->count_all_results('penyakit') > 0;
    }

    public function getModule($moduleId)
    {
        return $this->db
            ->select('modul.*, penyakit.nama AS nama_penyakit')
            ->from('modul_pembelajaran modul')
            ->join('penyakit', 'penyakit.id = modul.id_penyakit', 'left')
            ->where('modul.id', (int) $moduleId)
            ->get()
            ->row_array();
    }

    public function createModule($data)
    {
        $this->db->trans_begin();
        $this->db->insert('modul_pembelajaran', $data);
        $moduleId = (int) $this->db->insert_id();

        if ($moduleId > 0) {
            $now = date('Y-m-d H:i:s');
            $this->db->insert_batch('modul_tes', array(
                array(
                    'id_modul_pembelajaran' => $moduleId,
                    'tipe' => 'pre_test',
                    'judul' => 'Pre-Test Pengetahuan HIV dan AIDS',
                    'created_at' => $now
                ),
                array(
                    'id_modul_pembelajaran' => $moduleId,
                    'tipe' => 'post_test',
                    'judul' => 'Post-Test Pengetahuan HIV dan AIDS',
                    'created_at' => $now
                )
            ));
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return 0;
        }

        $this->db->trans_commit();
        return $moduleId;
    }

    public function getModuleTests($moduleId)
    {
        return $this->db
            ->select('tes.*')
            ->select('COUNT(hasil.id) AS total_peserta', FALSE)
            ->select('COALESCE(AVG(hasil.nilai), 0) AS rata_rata_nilai', FALSE)
            ->from('modul_tes tes')
            ->join('hasil_tes_modul hasil', 'hasil.id_modul_tes = tes.id', 'left')
            ->where('tes.id_modul_pembelajaran', (int) $moduleId)
            ->group_by('tes.id')
            ->order_by("FIELD(tes.tipe, 'pre_test', 'post_test')", '', FALSE)
            ->get()
            ->result_array();
    }

    public function getModuleParticipantResults($moduleId)
    {
        return $this->db
            ->select('pengguna.id, pengguna.nama, pengguna.email')
            ->select("MAX(CASE WHEN tes.tipe = 'pre_test' THEN hasil.nilai END) AS nilai_pre_test", FALSE)
            ->select("MAX(CASE WHEN tes.tipe = 'post_test' THEN hasil.nilai END) AS nilai_post_test", FALSE)
            ->select("MAX(CASE WHEN tes.tipe = 'pre_test' THEN hasil.completed_at END) AS waktu_pre_test", FALSE)
            ->select("MAX(CASE WHEN tes.tipe = 'post_test' THEN hasil.completed_at END) AS waktu_post_test", FALSE)
            ->from('hasil_tes_modul hasil')
            ->join('modul_tes tes', 'tes.id = hasil.id_modul_tes', 'inner')
            ->join('user pengguna', 'pengguna.id = hasil.id_user', 'inner')
            ->where('tes.id_modul_pembelajaran', (int) $moduleId)
            ->group_by(array('pengguna.id', 'pengguna.nama', 'pengguna.email'))
            ->order_by('pengguna.nama', 'ASC')
            ->get()
            ->result_array();
    }

    public function updateModule($moduleId, $data)
    {
        return $this->db->where('id', (int) $moduleId)->update('modul_pembelajaran', $data);
    }

    public function getModuleHierarchy($moduleId)
    {
        $details = $this->db
            ->where('id_modul_pembelajaran', (int) $moduleId)
            ->order_by('id', 'ASC')
            ->get('detail_modul')
            ->result_array();

        if (empty($details)) {
            return array();
        }

        $detailIds = array_column($details, 'id');
        $contents = $this->db
            ->where_in('id_detail_modul', $detailIds)
            ->order_by('id', 'ASC')
            ->get('list_detail_modul')
            ->result_array();

        $questions = array();
        $options = array();
        if (!empty($contents)) {
            $contentIds = array_column($contents, 'id');
            $questions = $this->db
                ->where_in('list_detail_modul_id', $contentIds)
                ->order_by('id', 'ASC')
                ->get('soal')
                ->result_array();

            if (!empty($questions)) {
                $options = $this->db
                    ->where_in('soal_id', array_column($questions, 'id'))
                    ->order_by('id', 'ASC')
                    ->get('pilihan_jawaban')
                    ->result_array();
            }
        }

        $optionsByQuestion = array();
        foreach ($options as $option) {
            $optionsByQuestion[(int) $option['soal_id']][] = $option;
        }

        $questionsByContent = array();
        foreach ($questions as $question) {
            $question['pilihan'] = isset($optionsByQuestion[(int) $question['id']])
                ? $optionsByQuestion[(int) $question['id']]
                : array();
            $questionsByContent[(int) $question['list_detail_modul_id']][] = $question;
        }

        $contentsByDetail = array();
        foreach ($contents as $content) {
            $content['soal'] = isset($questionsByContent[(int) $content['id']])
                ? $questionsByContent[(int) $content['id']]
                : array();
            $contentsByDetail[(int) $content['id_detail_modul']][] = $content;
        }

        foreach ($details as &$detail) {
            $detail['materi'] = isset($contentsByDetail[(int) $detail['id']])
                ? $contentsByDetail[(int) $detail['id']]
                : array();
        }
        unset($detail);

        return $details;
    }

    public function getDetail($detailId)
    {
        return $this->db
            ->select('detail.*, modul.nama AS nama_modul')
            ->from('detail_modul detail')
            ->join('modul_pembelajaran modul', 'modul.id = detail.id_modul_pembelajaran', 'inner')
            ->where('detail.id', (int) $detailId)
            ->get()
            ->row_array();
    }

    public function getContent($contentId)
    {
        return $this->db
            ->select('materi.*, detail.id_modul_pembelajaran, detail.title AS judul_bab')
            ->from('list_detail_modul materi')
            ->join('detail_modul detail', 'detail.id = materi.id_detail_modul', 'inner')
            ->where('materi.id', (int) $contentId)
            ->get()
            ->row_array();
    }

    public function getQuestion($questionId)
    {
        return $this->db
            ->select('soal.*, materi.id_detail_modul, detail.id_modul_pembelajaran')
            ->from('soal')
            ->join('list_detail_modul materi', 'materi.id = soal.list_detail_modul_id', 'inner')
            ->join('detail_modul detail', 'detail.id = materi.id_detail_modul', 'inner')
            ->where('soal.id', (int) $questionId)
            ->get()
            ->row_array();
    }

    public function getOption($optionId)
    {
        return $this->db
            ->select('pilihan.*, soal.list_detail_modul_id, materi.id_detail_modul, detail.id_modul_pembelajaran')
            ->from('pilihan_jawaban pilihan')
            ->join('soal', 'soal.id = pilihan.soal_id', 'inner')
            ->join('list_detail_modul materi', 'materi.id = soal.list_detail_modul_id', 'inner')
            ->join('detail_modul detail', 'detail.id = materi.id_detail_modul', 'inner')
            ->where('pilihan.id', (int) $optionId)
            ->get()
            ->row_array();
    }

    public function introductoryDetailExists($moduleId, $excludeDetailId = NULL)
    {
        $this->db
            ->where('id_modul_pembelajaran', (int) $moduleId)
            ->where('jenis_modul', 'pendahuluan');

        if ($excludeDetailId !== NULL) {
            $this->db->where('id !=', (int) $excludeDetailId);
        }

        return $this->db->count_all_results('detail_modul') > 0;
    }

    public function detailHasContents($detailId)
    {
        return $this->db->where('id_detail_modul', (int) $detailId)->count_all_results('list_detail_modul') > 0;
    }

    public function contentHasQuestions($contentId)
    {
        return $this->db->where('list_detail_modul_id', (int) $contentId)->count_all_results('soal') > 0;
    }

    public function questionHasCorrectOption($questionId, $excludeOptionId = NULL)
    {
        $this->db
            ->where('soal_id', (int) $questionId)
            ->where('is_benar', 1);

        if ($excludeOptionId !== NULL) {
            $this->db->where('id !=', (int) $excludeOptionId);
        }

        return $this->db->count_all_results('pilihan_jawaban') > 0;
    }

    public function createDetail($data)
    {
        $this->db->trans_begin();
        $this->db->insert('detail_modul', $data);
        $detailId = (int) $this->db->insert_id();

        if ($data['jenis_modul'] === 'pendahuluan') {
            $this->syncActiveLearners(
                (int) $data['id_modul_pembelajaran'],
                $detailId,
                0
            );
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return FALSE;
        }

        $this->db->trans_commit();
        return $detailId;
    }

    public function updateDetail($detailId, $data)
    {
        return $this->db->where('id', (int) $detailId)->update('detail_modul', $data);
    }

    public function createContent($data)
    {
        $detail = $this->getDetail($data['id_detail_modul']);
        if (!$detail) {
            return FALSE;
        }

        $this->db->trans_begin();
        $this->db->insert('list_detail_modul', $data);
        $contentId = (int) $this->db->insert_id();
        $this->syncActiveLearners((int) $detail['id_modul_pembelajaran'], (int) $detail['id'], $contentId);

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return FALSE;
        }

        $this->db->trans_commit();
        return $contentId;
    }

    public function updateContent($contentId, $data)
    {
        return $this->db->where('id', (int) $contentId)->update('list_detail_modul', $data);
    }

    public function createQuestion($data)
    {
        $this->db->insert('soal', $data);
        return (int) $this->db->insert_id();
    }

    public function updateQuestion($questionId, $data)
    {
        return $this->db->where('id', (int) $questionId)->update('soal', $data);
    }

    public function createOption($data)
    {
        $this->db->insert('pilihan_jawaban', $data);
        return (int) $this->db->insert_id();
    }

    public function updateOption($optionId, $data)
    {
        $this->db->trans_begin();
        $this->db->where('id', (int) $optionId)->update('pilihan_jawaban', $data);
        $this->db
            ->where('pilihan_jawaban_id', (int) $optionId)
            ->update('jawaban_user', array('jawaban_benar' => (int) $data['is_benar']));

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return FALSE;
        }

        $this->db->trans_commit();
        return TRUE;
    }

    public function deleteModule($moduleId)
    {
        $module = $this->getModule($moduleId);
        if (!$module) {
            return FALSE;
        }

        $detailIds = $this->getColumnValues('detail_modul', 'id', 'id_modul_pembelajaran', $moduleId);
        $contentIds = $this->getValuesByParentIds('list_detail_modul', 'id', 'id_detail_modul', $detailIds);
        $questionIds = $this->getValuesByParentIds('soal', 'id', 'list_detail_modul_id', $contentIds);
        $processIds = $this->getColumnValues('proses_belajar', 'id', 'id_modul_pembelajaran', $moduleId);

        $this->db->trans_begin();
        $this->deleteWhereIn('jawaban_user', 'id_proses_belajar', $processIds);
        $this->deleteWhereIn('jawaban_user', 'soal_id', $questionIds);
        $this->deleteWhereIn('pilihan_jawaban', 'soal_id', $questionIds);
        $this->deleteWhereIn('soal', 'id', $questionIds);
        $this->db->where('id_modul_pembelajaran', (int) $moduleId)->delete('proses_belajar');
        $this->deleteWhereIn('list_detail_modul', 'id', $contentIds);
        $this->deleteWhereIn('detail_modul', 'id', $detailIds);
        $this->db->where('id', (int) $moduleId)->delete('modul_pembelajaran');

        return $this->finishDeleteTransaction();
    }

    public function deleteDetail($detailId)
    {
        $detail = $this->getDetail($detailId);
        if (!$detail) {
            return FALSE;
        }

        $contentIds = $this->getColumnValues('list_detail_modul', 'id', 'id_detail_modul', $detailId);
        $questionIds = $this->getValuesByParentIds('soal', 'id', 'list_detail_modul_id', $contentIds);
        $processIds = $this->getColumnValues('proses_belajar', 'id', 'id_detail_modul', $detailId);

        $this->db->trans_begin();
        $this->deleteWhereIn('jawaban_user', 'id_proses_belajar', $processIds);
        $this->deleteWhereIn('jawaban_user', 'soal_id', $questionIds);
        $this->deleteWhereIn('pilihan_jawaban', 'soal_id', $questionIds);
        $this->deleteWhereIn('soal', 'id', $questionIds);
        $this->db->where('id_detail_modul', (int) $detailId)->delete('proses_belajar');
        $this->deleteWhereIn('list_detail_modul', 'id', $contentIds);
        $this->db->where('id', (int) $detailId)->delete('detail_modul');

        return $this->finishDeleteTransaction();
    }

    public function deleteContent($contentId)
    {
        $content = $this->getContent($contentId);
        if (!$content) {
            return FALSE;
        }

        $questionIds = $this->getColumnValues('soal', 'id', 'list_detail_modul_id', $contentId);
        $processIds = $this->getColumnValues('proses_belajar', 'id', 'id_list_detail_modul', $contentId);

        $this->db->trans_begin();
        $this->deleteWhereIn('jawaban_user', 'id_proses_belajar', $processIds);
        $this->deleteWhereIn('jawaban_user', 'soal_id', $questionIds);
        $this->deleteWhereIn('pilihan_jawaban', 'soal_id', $questionIds);
        $this->deleteWhereIn('soal', 'id', $questionIds);
        $this->db->where('id_list_detail_modul', (int) $contentId)->delete('proses_belajar');
        $this->db->where('id', (int) $contentId)->delete('list_detail_modul');

        return $this->finishDeleteTransaction();
    }

    public function deleteQuestion($questionId)
    {
        if (!$this->getQuestion($questionId)) {
            return FALSE;
        }

        $this->db->trans_begin();
        $this->db->where('soal_id', (int) $questionId)->delete('jawaban_user');
        $this->db->where('soal_id', (int) $questionId)->delete('pilihan_jawaban');
        $this->db->where('id', (int) $questionId)->delete('soal');

        return $this->finishDeleteTransaction();
    }

    public function deleteOption($optionId)
    {
        if (!$this->getOption($optionId)) {
            return FALSE;
        }

        $this->db->trans_begin();
        $this->db->where('pilihan_jawaban_id', (int) $optionId)->delete('jawaban_user');
        $this->db->where('id', (int) $optionId)->delete('pilihan_jawaban');

        return $this->finishDeleteTransaction();
    }

    private function syncActiveLearners($moduleId, $detailId, $contentId)
    {
        $learners = $this->db
            ->select('id_user')
            ->from('proses_belajar')
            ->where('id_modul_pembelajaran', (int) $moduleId)
            ->group_by('id_user')
            ->having("SUM(CASE WHEN status <> 'selsai' THEN 1 ELSE 0 END) > 0", NULL, FALSE)
            ->get()
            ->result_array();

        foreach ($learners as $learner) {
            $exists = $this->db
                ->where('id_modul_pembelajaran', (int) $moduleId)
                ->where('id_detail_modul', (int) $detailId)
                ->where('id_list_detail_modul', (int) $contentId)
                ->where('id_user', (int) $learner['id_user'])
                ->count_all_results('proses_belajar') > 0;

            if (!$exists) {
                $this->db->insert('proses_belajar', array(
                    'id_modul_pembelajaran' => (int) $moduleId,
                    'id_detail_modul' => (int) $detailId,
                    'id_list_detail_modul' => (int) $contentId,
                    'id_user' => (int) $learner['id_user'],
                    'status' => ''
                ));
            }
        }
    }

    private function getColumnValues($table, $column, $whereColumn, $whereValue)
    {
        $rows = $this->db
            ->select($column)
            ->where($whereColumn, (int) $whereValue)
            ->get($table)
            ->result_array();

        return array_map('intval', array_column($rows, $column));
    }

    private function getValuesByParentIds($table, $column, $parentColumn, $parentIds)
    {
        if (empty($parentIds)) {
            return array();
        }

        $rows = $this->db
            ->select($column)
            ->where_in($parentColumn, $parentIds)
            ->get($table)
            ->result_array();

        return array_map('intval', array_column($rows, $column));
    }

    private function deleteWhereIn($table, $column, $values)
    {
        if (!empty($values)) {
            $this->db->where_in($column, $values)->delete($table);
        }
    }

    private function finishDeleteTransaction()
    {
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return FALSE;
        }

        $this->db->trans_commit();
        return TRUE;
    }
}
