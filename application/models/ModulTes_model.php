<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ModulTes_model extends CI_Model
{
    const PRE_TEST = 'pre_test';
    const POST_TEST = 'post_test';

    public function isValidType($type)
    {
        return in_array($type, array(self::PRE_TEST, self::POST_TEST), TRUE);
    }

    public function getModule($moduleId)
    {
        return $this->db
            ->where('id', (int) $moduleId)
            ->get('modul_pembelajaran')
            ->row_array();
    }

    public function ensureModuleTests($moduleId)
    {
        $moduleId = (int) $moduleId;
        if (!$this->getModule($moduleId)) {
            return FALSE;
        }

        $now = date('Y-m-d H:i:s');
        $tests = array(
            self::PRE_TEST => 'Pre-Test Pengetahuan HIV dan AIDS',
            self::POST_TEST => 'Post-Test Pengetahuan HIV dan AIDS'
        );

        foreach ($tests as $type => $title) {
            $this->db->query(
                'INSERT INTO modul_tes (id_modul_pembelajaran, tipe, judul, created_at)
                 VALUES (?, ?, ?, ?)
                 ON DUPLICATE KEY UPDATE judul = VALUES(judul)',
                array($moduleId, $type, $title, $now)
            );
        }

        return $this->db->error()['code'] === 0;
    }

    public function getTest($moduleId, $type)
    {
        if (!$this->isValidType($type)) {
            return array();
        }

        $this->ensureModuleTests($moduleId);

        return $this->db
            ->where('id_modul_pembelajaran', (int) $moduleId)
            ->where('tipe', $type)
            ->get('modul_tes')
            ->row_array();
    }

    public function getQuestions()
    {
        return $this->db
            ->select('id, nomor, kategori, pertanyaan')
            ->where('aktif', 1)
            ->order_by('nomor', 'ASC')
            ->get('bank_soal_tes_hiv')
            ->result_array();
    }

    public function getResult($moduleId, $type, $userId)
    {
        if (!$this->isValidType($type)) {
            return array();
        }

        return $this->db
            ->select('hasil.*, tes.tipe, tes.judul')
            ->from('hasil_tes_modul hasil')
            ->join('modul_tes tes', 'tes.id = hasil.id_modul_tes', 'inner')
            ->where('tes.id_modul_pembelajaran', (int) $moduleId)
            ->where('tes.tipe', $type)
            ->where('hasil.id_user', (int) $userId)
            ->get()
            ->row_array();
    }

    public function getComparison($moduleId, $userId)
    {
        return array(
            self::PRE_TEST => $this->getResult($moduleId, self::PRE_TEST, $userId),
            self::POST_TEST => $this->getResult($moduleId, self::POST_TEST, $userId)
        );
    }

    public function hasCompletedPreTest($moduleId, $userId)
    {
        return !empty($this->getResult($moduleId, self::PRE_TEST, $userId));
    }

    public function hasCompletedLearning($moduleId, $userId)
    {
        $summary = $this->db
            ->select('COUNT(*) AS total')
            ->select("SUM(CASE WHEN status = 'selsai' THEN 1 ELSE 0 END) AS selesai", FALSE)
            ->where('id_modul_pembelajaran', (int) $moduleId)
            ->where('id_user', (int) $userId)
            ->get('proses_belajar')
            ->row_array();

        return !empty($summary)
            && (int) $summary['total'] > 0
            && (int) $summary['total'] === (int) $summary['selesai'];
    }

    public function saveResult($moduleId, $type, $userId, array $answers)
    {
        $test = $this->getTest($moduleId, $type);
        if (!$test) {
            return array('success' => FALSE, 'message' => 'Pre-Test atau Post-Test tidak ditemukan.');
        }

        $existing = $this->getResult($moduleId, $type, $userId);
        if ($existing) {
            return array('success' => TRUE, 'result' => $existing, 'already_completed' => TRUE);
        }

        $questionRows = $this->db
            ->select('id, kunci_jawaban')
            ->where('aktif', 1)
            ->order_by('nomor', 'ASC')
            ->get('bank_soal_tes_hiv')
            ->result_array();

        if (empty($questionRows)) {
            return array('success' => FALSE, 'message' => 'Bank soal belum tersedia.');
        }

        $questionKeys = array();
        foreach ($questionRows as $question) {
            $questionKeys[(int) $question['id']] = $question['kunci_jawaban'];
        }

        $normalizedAnswers = array();
        foreach ($questionKeys as $questionId => $answerKey) {
            $answer = isset($answers[$questionId]) ? strtoupper(trim((string) $answers[$questionId])) : '';
            if (!in_array($answer, array('B', 'S'), TRUE)) {
                return array('success' => FALSE, 'message' => 'Semua pertanyaan wajib dijawab.');
            }
            $normalizedAnswers[$questionId] = $answer;
        }

        if (count($normalizedAnswers) !== count($questionKeys)) {
            return array('success' => FALSE, 'message' => 'Jawaban yang dikirim tidak lengkap.');
        }

        $correct = 0;
        foreach ($normalizedAnswers as $questionId => $answer) {
            if ($answer === $questionKeys[$questionId]) {
                $correct++;
            }
        }

        $total = count($questionKeys);
        $wrong = $total - $correct;
        $score = round(($correct / $total) * 100, 2);
        $now = date('Y-m-d H:i:s');

        $this->db->trans_begin();
        $this->db->insert('hasil_tes_modul', array(
            'id_modul_tes' => (int) $test['id'],
            'id_user' => (int) $userId,
            'jumlah_soal' => $total,
            'jumlah_benar' => $correct,
            'jumlah_salah' => $wrong,
            'nilai' => $score,
            'completed_at' => $now
        ));
        $resultId = (int) $this->db->insert_id();

        $answerRows = array();
        foreach ($normalizedAnswers as $questionId => $answer) {
            $answerRows[] = array(
                'id_hasil_tes' => $resultId,
                'id_bank_soal' => $questionId,
                'jawaban' => $answer,
                'is_benar' => $answer === $questionKeys[$questionId] ? 1 : 0
            );
        }
        $this->db->insert_batch('jawaban_tes_modul', $answerRows);

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('success' => FALSE, 'message' => 'Hasil tes gagal disimpan. Silakan coba kembali.');
        }

        $this->db->trans_commit();
        return array(
            'success' => TRUE,
            'result' => $this->getResult($moduleId, $type, $userId),
            'already_completed' => FALSE
        );
    }
}
