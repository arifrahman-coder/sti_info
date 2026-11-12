<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mars_model extends CI_Model
{
    const CATEGORY_NAME = 'Medication Adherence Rating Scale (MARS-5)';

    public function getCategoryWithQuestions()
    {
        if (!$this->db->table_exists('kuesioner_kategori')) {
            return array();
        }

        $category = $this->db
            ->where('nama', self::CATEGORY_NAME)
            ->where('status', 1)
            ->get('kuesioner_kategori')
            ->row_array();

        if (!$category) {
            return array();
        }

        $category['pertanyaan'] = $this->db
            ->where('kategori_id', (int) $category['id'])
            ->where('status', 1)
            ->order_by('urutan', 'ASC')
            ->order_by('id', 'ASC')
            ->get('kuesioner_pertanyaan')
            ->result_array();

        foreach ($category['pertanyaan'] as &$question) {
            $question['opsi'] = $this->db
                ->where('pertanyaan_id', (int) $question['id'])
                ->where('status', 1)
                ->order_by('urutan', 'ASC')
                ->order_by('id', 'ASC')
                ->get('kuesioner_opsi')
                ->result_array();
        }
        unset($question);

        return $category;
    }

    public function isEligibleUser($idUser)
    {
        return !empty($this->getUserById($idUser));
    }

    public function getUserById($idUser)
    {
        return $this->db
            ->select('user.id, user.nama, user.email, user.no_hp, user.id_status, status_user.nama AS status_user_nama')
            ->from('user')
            ->join('status_user', 'status_user.id = user.id_status', 'left')
            ->where('user.id', (int) $idUser)
            ->get()
            ->row_array();
    }

    public function getEligibleUsers($onlyWithoutSubmission = FALSE)
    {
        $codes = $this->getQuestionCodes();
        $this->db
            ->select('user.id, user.nama, user.email, status_user.nama AS status_user_nama')
            ->from('user')
            ->join('status_user', 'status_user.id = user.id_status', 'left');

        if ($onlyWithoutSubmission && !empty($codes)) {
            $escapedCodes = array_map(array($this->db, 'escape'), $codes);
            $this->db->where(
                'NOT EXISTS (SELECT 1 FROM demografi mars_demografi INNER JOIN jawaban_kuesioner mars_jawaban ON mars_jawaban.id_demografi = mars_demografi.id_demografi WHERE mars_demografi.id_user = user.id AND mars_jawaban.pertanyaan IN ('.implode(',', $escapedCodes).'))',
                NULL,
                FALSE
            );
        }

        return $this->db
            ->order_by('user.nama', 'ASC')
            ->get()
            ->result_array();
    }

    public function getAllSubmissions()
    {
        $codes = $this->getQuestionCodes();
        if (empty($codes)) {
            return array();
        }

        $rows = $this->db
            ->distinct()
            ->select('demografi.id_demografi, demografi.id_user, demografi.created_at')
            ->select('user.nama, user.email, user.no_hp, status_user.nama AS status_user_nama')
            ->from('demografi')
            ->join('jawaban_kuesioner', 'jawaban_kuesioner.id_demografi = demografi.id_demografi', 'inner')
            ->join('user', 'user.id = demografi.id_user', 'inner')
            ->join('status_user', 'status_user.id = user.id_status', 'inner')
            ->where_in('jawaban_kuesioner.pertanyaan', $codes)
            ->order_by('demografi.id_demografi', 'DESC')
            ->get()
            ->result_array();

        foreach ($rows as &$row) {
            $row['answers'] = $this->getAnswerMap((int) $row['id_demografi']);
            $row['score'] = $this->calculateScore($row['answers']);
        }
        unset($row);

        return $rows;
    }

    public function getSubmissionByUser($idUser)
    {
        $codes = $this->getQuestionCodes();
        if (empty($codes)) {
            return array();
        }

        $submission = $this->db
            ->distinct()
            ->select('demografi.id_demografi, demografi.id_user, demografi.created_at')
            ->select('user.nama, user.email, user.no_hp, status_user.nama AS status_user_nama')
            ->from('demografi')
            ->join('jawaban_kuesioner', 'jawaban_kuesioner.id_demografi = demografi.id_demografi', 'inner')
            ->join('user', 'user.id = demografi.id_user', 'inner')
            ->join('status_user', 'status_user.id = user.id_status', 'inner')
            ->where('demografi.id_user', (int) $idUser)
            ->where_in('jawaban_kuesioner.pertanyaan', $codes)
            ->order_by('demografi.id_demografi', 'DESC')
            ->limit(1)
            ->get()
            ->row_array();

        return $this->attachSubmissionAnswers($submission);
    }

    public function getSubmissionById($idDemografi)
    {
        $codes = $this->getQuestionCodes();
        if (empty($codes)) {
            return array();
        }

        $submission = $this->db
            ->distinct()
            ->select('demografi.id_demografi, demografi.id_user, demografi.created_at')
            ->select('user.nama, user.email, user.no_hp, status_user.nama AS status_user_nama')
            ->from('demografi')
            ->join('jawaban_kuesioner', 'jawaban_kuesioner.id_demografi = demografi.id_demografi', 'inner')
            ->join('user', 'user.id = demografi.id_user', 'inner')
            ->join('status_user', 'status_user.id = user.id_status', 'inner')
            ->where('demografi.id_demografi', (int) $idDemografi)
            ->where_in('jawaban_kuesioner.pertanyaan', $codes)
            ->limit(1)
            ->get()
            ->row_array();

        return $this->attachSubmissionAnswers($submission);
    }

    public function saveAnswers($idUser, array $answers, $idDemografi = NULL)
    {
        if (empty($answers) || ($idDemografi === NULL && !$this->isEligibleUser($idUser))) {
            return FALSE;
        }

        $this->db->trans_begin();

        if ($idDemografi !== NULL) {
            $demographic = $this->db
                ->where('id_demografi', (int) $idDemografi)
                ->where('id_user', (int) $idUser)
                ->get('demografi')
                ->row_array();
            if (!$demographic) {
                $this->db->trans_rollback();
                return FALSE;
            }
        } else {
            $demographic = $this->db
                ->where('id_user', (int) $idUser)
                ->order_by('id_demografi', 'DESC')
                ->limit(1)
                ->get('demografi')
                ->row_array();
        }

        if (!$demographic) {
            $this->db->insert('demografi', $this->newDemographicData($idUser));
            $idDemografi = (int) $this->db->insert_id();
        } else {
            $idDemografi = (int) $demographic['id_demografi'];
        }

        $codes = $this->getQuestionCodes();
        $this->db
            ->where('id_demografi', $idDemografi)
            ->where_in('pertanyaan', $codes)
            ->delete('jawaban_kuesioner');

        $rows = array();
        foreach ($answers as $code => $answer) {
            $rows[] = array(
                'id_demografi' => $idDemografi,
                'pertanyaan' => (string) $code,
                'jawaban' => (string) $answer
            );
        }
        $this->db->insert_batch('jawaban_kuesioner', $rows);

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return FALSE;
        }

        $this->db->trans_commit();
        return $idDemografi;
    }

    public function deleteSubmission($idDemografi)
    {
        $submission = $this->getSubmissionById($idDemografi);
        if (!$submission) {
            return FALSE;
        }

        $codes = $this->getQuestionCodes();
        $this->db->trans_begin();
        $this->db
            ->where('id_demografi', (int) $idDemografi)
            ->where_in('pertanyaan', $codes)
            ->delete('jawaban_kuesioner');

        $remainingAnswers = (int) $this->db
            ->where('id_demografi', (int) $idDemografi)
            ->count_all_results('jawaban_kuesioner');
        if ($remainingAnswers === 0) {
            $this->db->where('id_demografi', (int) $idDemografi)->delete('demografi');
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return FALSE;
        }

        $this->db->trans_commit();
        return TRUE;
    }

    public function getAnswerMap($idDemografi)
    {
        $answers = array();
        $codes = $this->getQuestionCodes();
        if (empty($codes)) {
            return $answers;
        }

        $rows = $this->db
            ->select('pertanyaan, jawaban')
            ->where('id_demografi', (int) $idDemografi)
            ->where_in('pertanyaan', $codes)
            ->get('jawaban_kuesioner')
            ->result_array();

        foreach ($rows as $row) {
            $answers[(string) $row['pertanyaan']] = (string) $row['jawaban'];
        }
        return $answers;
    }

    private function getQuestionCodes()
    {
        $category = $this->db
            ->select('id')
            ->where('nama', self::CATEGORY_NAME)
            ->get('kuesioner_kategori')
            ->row_array();
        if (!$category) {
            return array();
        }

        $rows = $this->db
            ->select('kode')
            ->where('kategori_id', (int) $category['id'])
            ->where('status', 1)
            ->order_by('urutan', 'ASC')
            ->get('kuesioner_pertanyaan')
            ->result_array();

        return array_column($rows, 'kode');
    }

    private function calculateScore(array $answers)
    {
        if (empty($answers)) {
            return 0;
        }

        $questions = $this->getCategoryWithQuestions();
        $score = 0;
        foreach (isset($questions['pertanyaan']) ? $questions['pertanyaan'] : array() as $question) {
            $code = (string) $question['kode'];
            foreach ($question['opsi'] as $option) {
                if (isset($answers[$code]) && (string) $option['nilai'] === (string) $answers[$code]) {
                    $score += (int) $option['skor'];
                    break;
                }
            }
        }
        return $score;
    }

    private function attachSubmissionAnswers($submission)
    {
        if (!$submission) {
            return array();
        }
        $submission['answers'] = $this->getAnswerMap((int) $submission['id_demografi']);
        $submission['score'] = $this->calculateScore($submission['answers']);
        return $submission;
    }

    private function newDemographicData($idUser)
    {
        $user = $this->getUserById($idUser);
        $statusName = !empty($user['status_user_nama']) ? trim((string) $user['status_user_nama']) : 'Pengguna';

        return array(
            'id_user' => (int) $idUser,
            'jenis_responden' => 'Responden '.$statusName,
            'kode_responden' => '', 'no_responden' => '', 'usia' => 0, 'gender' => '', 'kelas' => '',
            'pendidikan_ayah' => '', 'pendidikan_ibu' => '', 'pekerjaan_ayah' => '', 'pekerjaan_ibu' => '',
            'pernah_informasi_hiv' => '', 'sumber_informasi_hiv' => '', 'pekerjaan' => '', 'pendidikan' => '',
            'pendapatan' => '', 'pengalaman' => '', 'created_at' => date('Y-m-d H:i:s')
        );
    }
}
