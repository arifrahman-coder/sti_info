<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Data_demografi_responden extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Kuesioner_model');

        if (!$this->session->userdata('id_admin')) {
            redirect('login_admin');
        }
    }

    public function index()
    {
        $data['submissions'] = $this->Kuesioner_model->getFilledDemographicSubmissions();
        $this->loadBackend('data_demografi_responden/index', $data);
    }

    public function detail($idDemografi)
    {
        $submission = $this->Kuesioner_model->getDemographicSubmissionById($idDemografi);
        if (!$submission) {
            return $this->redirectWithMessage('danger', 'Data kuesioner responden tidak ditemukan.');
        }

        $data = array(
            'submission' => $submission,
            'answers' => $this->Kuesioner_model->getDemographicAnswers($idDemografi)
        );
        $this->loadBackend('data_demografi_responden/detail', $data);
    }

    public function edit($idDemografi)
    {
        $submission = $this->Kuesioner_model->getDemographicSubmissionById($idDemografi);
        if (!$submission) {
            return $this->redirectWithMessage('danger', 'Data kuesioner responden tidak ditemukan.');
        }

        $questionnaires = $this->getEditableQuestionnaires($idDemografi, $submission);
        if (empty($questionnaires)) {
            return $this->redirectWithMessage('danger', 'Form kuesioner untuk status responden ini tidak tersedia.');
        }

        $data = array(
            'submission' => $submission,
            'questionnaires' => $questionnaires,
            'answerMap' => $this->Kuesioner_model->getDemographicAnswerMap($idDemografi)
        );
        $this->loadBackend('data_demografi_responden/edit', $data);
    }

    public function update($idDemografi)
    {
        if (strtoupper($this->input->method()) !== 'POST') {
            show_404();
        }

        $submission = $this->Kuesioner_model->getDemographicSubmissionById($idDemografi);
        if (!$submission) {
            return $this->redirectWithMessage('danger', 'Data kuesioner responden tidak ditemukan.');
        }

        $questionnaires = $this->getEditableQuestionnaires($idDemografi, $submission);
        if (empty($questionnaires)) {
            return $this->redirectWithMessage('danger', 'Form kuesioner untuk status responden ini tidak tersedia.', 'edit/'.$idDemografi);
        }

        $answers = $this->validateAnswers($questionnaires, $idDemografi);
        if ($answers === FALSE) {
            return;
        }

        $questions = array();
        foreach ($questionnaires as $questionnaire) {
            $questions = array_merge($questions, $questionnaire['pertanyaan']);
        }

        $dataDemografi = $this->buildDemographicSummary($questionnaires[0]['nama'], $questions, $answers);
        if ($this->Kuesioner_model->updateDemographicSubmission($idDemografi, $dataDemografi, $answers)) {
            return $this->redirectWithMessage('success', 'Data kuesioner responden berhasil diperbarui.', 'detail/'.$idDemografi);
        }

        $this->redirectWithMessage('danger', 'Gagal memperbarui data kuesioner responden.', 'edit/'.$idDemografi);
    }

    public function delete($idDemografi)
    {
        if (strtoupper($this->input->method()) !== 'POST') {
            show_404();
        }

        if (!$this->Kuesioner_model->getDemographicSubmissionById($idDemografi)) {
            return $this->redirectWithMessage('danger', 'Data kuesioner responden tidak ditemukan.');
        }

        if ($this->Kuesioner_model->deleteDemographicSubmission($idDemografi)) {
            return $this->redirectWithMessage('success', 'Data kuesioner responden berhasil dihapus.');
        }

        $this->redirectWithMessage('danger', 'Gagal menghapus data kuesioner responden.');
    }

    public function bulkDelete()
    {
        if (strtoupper($this->input->method()) !== 'POST') {
            show_404();
        }

        $postedIds = $this->input->post('demografi_ids');
        if (!is_array($postedIds) || empty($postedIds)) {
            return $this->redirectWithMessage('warning', 'Pilih minimal satu data kuesioner untuk dihapus.');
        }

        $demographicIds = array();
        foreach ($postedIds as $postedId) {
            if (!is_scalar($postedId) || !ctype_digit((string) $postedId)) {
                continue;
            }

            $idDemografi = (int) $postedId;
            if ($idDemografi > 0) {
                $demographicIds[$idDemografi] = $idDemografi;
            }
        }

        if (empty($demographicIds)) {
            return $this->redirectWithMessage('warning', 'Data kuesioner yang dipilih tidak valid.');
        }

        $deletedCount = $this->Kuesioner_model->deleteDemographicSubmissions(array_values($demographicIds));
        if ($deletedCount === FALSE) {
            return $this->redirectWithMessage('danger', 'Gagal menghapus data kuesioner yang dipilih.');
        }

        if ($deletedCount > 0) {
            return $this->redirectWithMessage('success', $deletedCount.' data kuesioner responden berhasil dihapus.');
        }

        $this->redirectWithMessage('warning', 'Tidak ada data kuesioner responden yang terhapus.');
    }

    public function export()
    {
        $data['submissions'] = $this->Kuesioner_model->getFilledDemographicSubmissions();
        $data['answersBySubmission'] = array();

        foreach ($data['submissions'] as $submission) {
            $data['answersBySubmission'][(int) $submission['id_demografi']] =
                $this->Kuesioner_model->getDemographicAnswers($submission['id_demografi']);
        }

        $fileName = date('ymdhis').'_data_kuesioner_responden.xls';
        $html = $this->load->view('backend/data_demografi_responden/export', $data, TRUE);

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="'.$fileName.'"');
        header('Pragma: no-cache');
        header('Expires: 0');
        echo $html;
        exit;
    }

    private function validateAnswers($questionnaires, $idDemografi)
    {
        if (empty($questionnaires)) {
            $this->redirectWithMessage('danger', 'Pertanyaan kuesioner aktif tidak ditemukan.', 'edit/'.$idDemografi);
            return FALSE;
        }

        $answerPost = $this->input->post('jawaban');
        $otherAnswerPost = $this->input->post('jawaban_lainnya');
        if (!is_array($answerPost)) {
            $answerPost = array();
        }
        if (!is_array($otherAnswerPost)) {
            $otherAnswerPost = array();
        }

        $answers = array();
        foreach ($questionnaires as $questionnaire) {
            foreach ($questionnaire['pertanyaan'] as $question) {
                $questionId = (int) $question['id'];
                $type = isset($question['tipe']) ? $question['tipe'] : 'radio';
                $postedValue = isset($answerPost[$questionId]) ? $answerPost[$questionId] : '';

                if ($type === 'checkbox') {
                    $answer = $this->validateCheckboxAnswer(
                        $postedValue,
                        $question,
                        isset($otherAnswerPost[$questionId]) ? $otherAnswerPost[$questionId] : ''
                    );
                    if ($answer === FALSE) {
                        $this->redirectWithMessage('danger', 'Terdapat jawaban kotak centang yang tidak valid.', 'edit/'.$idDemografi);
                        return FALSE;
                    }
                } else {
                    $answer = is_scalar($postedValue) ? trim((string) $postedValue) : '';
                }

                if ((int) $question['wajib'] === 1 && $answer === '') {
                    $this->redirectWithMessage('danger', 'Semua pertanyaan wajib diisi.', 'edit/'.$idDemografi);
                    return FALSE;
                }

                if ($answer !== '' && !in_array($type, array('text', 'textarea', 'number', 'month', 'date', 'checkbox'), TRUE)) {
                    $validOptions = array();
                    foreach ($question['opsi'] as $option) {
                        $validOptions[(string) $option['nilai']] = $option;
                    }

                    if (!isset($validOptions[$answer])) {
                        $this->redirectWithMessage('danger', 'Terdapat jawaban yang tidak valid.', 'edit/'.$idDemografi);
                        return FALSE;
                    }

                    if ($this->isOtherOption($validOptions[$answer])) {
                        $otherAnswer = isset($otherAnswerPost[$questionId])
                            ? trim((string) $otherAnswerPost[$questionId])
                            : '';

                        if ($otherAnswer === '') {
                            $this->redirectWithMessage('danger', 'Jawaban untuk pilihan Lainnya wajib ditulis.', 'edit/'.$idDemografi);
                            return FALSE;
                        }
                        if (mb_strlen($otherAnswer) > 500) {
                            $this->redirectWithMessage('danger', 'Jawaban untuk pilihan Lainnya maksimal 500 karakter.', 'edit/'.$idDemografi);
                            return FALSE;
                        }

                        $answer = (string) $validOptions[$answer]['nilai'].': '.$otherAnswer;
                    }
                }

                if ($answer !== '' && !$this->isValidTypedValue($type, $answer)) {
                    $this->redirectWithMessage('danger', 'Format jawaban tidak valid.', 'edit/'.$idDemografi);
                    return FALSE;
                }

                if ($answer !== '') {
                    $answers[] = array(
                        'pertanyaan' => $question['kode'],
                        'jawaban' => $answer
                    );
                }
            }
        }

        return $answers;
    }

    private function validateCheckboxAnswer($postedValue, $question, $otherValue)
    {
        $postedValues = is_array($postedValue) ? $postedValue : array();
        $validOptions = array();
        foreach ($question['opsi'] as $option) {
            $validOptions[(string) $option['nilai']] = $option;
        }

        $answers = array();
        foreach ($postedValues as $value) {
            if (!is_scalar($value)) {
                return FALSE;
            }
            $value = trim((string) $value);
            if ($value === '' || !isset($validOptions[$value])) {
                return FALSE;
            }
            if ($this->isOtherOption($validOptions[$value])) {
                $detail = is_scalar($otherValue) ? trim((string) $otherValue) : '';
                if ($detail === '' || mb_strlen($detail) > 500) {
                    return FALSE;
                }
                $value .= ': '.$detail;
            }
            $answers[$value] = $value;
        }

        if (isset($answers['Tidak ada']) && count($answers) > 1) {
            return FALSE;
        }

        return implode('; ', array_values($answers));
    }

    private function isValidTypedValue($type, $value)
    {
        if ($type === 'number') {
            return is_numeric($value);
        }
        if ($type === 'month') {
            return (bool) preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $value);
        }
        if ($type === 'date') {
            $date = DateTime::createFromFormat('Y-m-d', $value);
            return $date && $date->format('Y-m-d') === $value;
        }
        return TRUE;
    }

    private function buildDemographicSummary($respondentType, $questions, $answers)
    {
        $answerByCode = array();
        foreach ($answers as $answer) {
            $answerByCode[$answer['pertanyaan']] = $answer['jawaban'];
        }

        $data = array(
            'jenis_responden' => $respondentType,
            'kode_responden' => '',
            'no_responden' => '',
            'usia' => 0,
            'gender' => '',
            'kelas' => '',
            'pendidikan_ayah' => '',
            'pendidikan_ibu' => '',
            'pekerjaan_ayah' => '',
            'pekerjaan_ibu' => '',
            'pernah_informasi_hiv' => '',
            'sumber_informasi_hiv' => '',
            'pekerjaan' => '',
            'pendidikan' => '',
            'pendapatan' => '',
            'pengalaman' => ''
        );

        foreach ($questions as $question) {
            if (strpos((string) $question['kode'], '_') === FALSE && isset($answerByCode[$question['kode']])) {
                $this->applySummaryValue($data, $question['pertanyaan'], $answerByCode[$question['kode']]);
            }
        }

        return $data;
    }

    private function applySummaryValue(&$data, $question, $answer)
    {
        $label = strtolower($question);

        if (strpos($label, 'kode') !== FALSE || strpos($label, 'inisial') !== FALSE) {
            $data['kode_responden'] = $answer;
            $data['no_responden'] = $answer;
        } elseif (strpos($label, 'usia') !== FALSE) {
            $data['usia'] = is_numeric($answer) ? (int) $answer : 0;
        } elseif (strpos($label, 'jenis kelamin') !== FALSE || strpos($label, 'identitas gender') !== FALSE) {
            $data['gender'] = $answer;
        } elseif (strpos($label, 'pendidikan') !== FALSE) {
            $data['pendidikan'] = $answer;
        } elseif (strpos($label, 'pekerjaan') !== FALSE || strpos($label, 'profesi') !== FALSE) {
            $data['pekerjaan'] = $answer;
        } elseif (strpos($label, 'penghasilan') !== FALSE || strpos($label, 'pendapatan') !== FALSE) {
            $data['pendapatan'] = $answer;
        } elseif (strpos($label, 'pengalaman') !== FALSE) {
            $data['pengalaman'] = $answer;
        }
    }

    private function isOtherOption($option)
    {
        $value = isset($option['nilai']) ? trim((string) $option['nilai']) : '';
        $label = isset($option['label']) ? trim((string) $option['label']) : '';

        return strcasecmp($value, 'Lainnya') === 0 || strcasecmp($label, 'Lainnya') === 0;
    }

    private function getEditableQuestionnaires($idDemografi, $submission)
    {
        $category = $this->Kuesioner_model->getDemographicSubmissionCategory($idDemografi);
        $statusId = !empty($category['id_status'])
            ? (int) $category['id_status']
            : $this->getEffectiveStatusId(isset($submission['id_status']) ? $submission['id_status'] : 0);
        if ($statusId <= 0) {
            return array();
        }

        return $this->Kuesioner_model->getActiveCategoriesWithQuestions($statusId);
    }

    private function getEffectiveStatusId($statusId)
    {
        $statusId = (int) $statusId;
        if (in_array($statusId, array(2, 3, 4), TRUE)) {
            return $this->getStatusIdByName('Tenaga Kesehatan', $statusId);
        }
        if ($statusId === 5) {
            return $this->getStatusIdByName('ODHIV', $statusId);
        }
        return $statusId;
    }

    private function getStatusIdByName($name, $fallback)
    {
        $status = $this->db->select('id')->where('nama', $name)->get('status_user')->row_array();
        return isset($status['id']) ? (int) $status['id'] : (int) $fallback;
    }

    private function redirectWithMessage($type, $message, $path = '')
    {
        $this->session->set_flashdata('message', '<div class="alert alert-'.$type.'" role="alert">'.htmlspecialchars($message, ENT_QUOTES, 'UTF-8').'</div>');
        $url = 'backend/data-kuesioner-responden';
        if ($path !== '') {
            $url .= '/'.$path;
        }
        redirect($url);
    }
}
