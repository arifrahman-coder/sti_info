<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kuesioner_mars extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Mars_model');
        $this->load->library('Mars_form', NULL, 'marsForm');

        if (!$this->session->userdata('id_admin')) {
            redirect('login_admin');
        }
    }

    public function index()
    {
        $this->loadBackend('kuesioner_mars/index', array(
            'submissions' => $this->Mars_model->getAllSubmissions()
        ));
    }

    public function detail($idDemografi)
    {
        $submission = $this->Mars_model->getSubmissionById($idDemografi);
        if (!$submission) {
            return $this->redirectMessage('danger', 'Data Kuisioner MARS tidak ditemukan.');
        }

        $category = $this->Mars_model->getCategoryWithQuestions();
        $this->loadBackend('kuesioner_mars/detail', array(
            'submission' => $submission,
            'questions' => isset($category['pertanyaan']) ? $category['pertanyaan'] : array()
        ));
    }

    public function create()
    {
        $category = $this->requireActiveQuestionnaire();
        if (!$category) {
            return;
        }

        $oldAnswers = $this->session->flashdata('mars_answers');
        $this->loadBackend('kuesioner_mars/form', array(
            'formTitle' => 'Tambah Pengisian Kuisioner MARS',
            'formAction' => base_url('backend/kuesioner-mars/store'),
            'submitLabel' => 'Simpan',
            'questions' => $category['pertanyaan'],
            'answerMap' => is_array($oldAnswers) ? $oldAnswers : array(),
            'users' => $this->Mars_model->getEligibleUsers(TRUE),
            'selectedUserId' => (int) $this->session->flashdata('mars_user_id'),
            'submission' => array(),
            'isCreate' => TRUE
        ));
    }

    public function store()
    {
        if (strtoupper($this->input->method(TRUE)) !== 'POST') {
            show_404();
            return;
        }

        $category = $this->requireActiveQuestionnaire();
        if (!$category) {
            return;
        }

        $idUser = (int) $this->input->post('id_user');
        $postedAnswers = $this->input->post('jawaban');
        if (!$this->Mars_model->isEligibleUser($idUser)) {
            return $this->returnToCreate('Pengguna yang dipilih tidak valid.', $idUser, $postedAnswers);
        }
        if ($this->Mars_model->getSubmissionByUser($idUser)) {
            return $this->returnToCreate('Pengguna tersebut sudah memiliki pengisian MARS. Gunakan menu Edit.', $idUser, $postedAnswers);
        }

        $result = $this->marsForm->validate($postedAnswers, $category['pertanyaan']);
        if (!$result['success']) {
            return $this->returnToCreate($result['message'], $idUser, $postedAnswers);
        }

        if (!$this->Mars_model->saveAnswers($idUser, $result['answers'])) {
            return $this->returnToCreate('Data Kuisioner MARS gagal disimpan.', $idUser, $postedAnswers);
        }

        $this->redirectMessage('success', 'Data Kuisioner MARS berhasil ditambahkan.');
    }

    public function edit($idDemografi)
    {
        $submission = $this->Mars_model->getSubmissionById($idDemografi);
        if (!$submission) {
            return $this->redirectMessage('danger', 'Data Kuisioner MARS tidak ditemukan.');
        }
        $category = $this->requireActiveQuestionnaire();
        if (!$category) {
            return;
        }

        $oldAnswers = $this->session->flashdata('mars_answers');
        $this->loadBackend('kuesioner_mars/form', array(
            'formTitle' => 'Edit Pengisian Kuisioner MARS',
            'formAction' => base_url('backend/kuesioner-mars/update/'.(int) $idDemografi),
            'submitLabel' => 'Simpan Perubahan',
            'questions' => $category['pertanyaan'],
            'answerMap' => is_array($oldAnswers) ? $oldAnswers : $submission['answers'],
            'users' => array(),
            'selectedUserId' => (int) $submission['id_user'],
            'submission' => $submission,
            'isCreate' => FALSE
        ));
    }

    public function update($idDemografi)
    {
        if (strtoupper($this->input->method(TRUE)) !== 'POST') {
            show_404();
            return;
        }

        $submission = $this->Mars_model->getSubmissionById($idDemografi);
        if (!$submission) {
            return $this->redirectMessage('danger', 'Data Kuisioner MARS tidak ditemukan.');
        }
        $category = $this->requireActiveQuestionnaire();
        if (!$category) {
            return;
        }

        $postedAnswers = $this->input->post('jawaban');
        $result = $this->marsForm->validate($postedAnswers, $category['pertanyaan']);
        if (!$result['success']) {
            $this->session->set_flashdata('message', $this->alert('danger', $result['message']));
            $this->session->set_flashdata('mars_answers', is_array($postedAnswers) ? $postedAnswers : array());
            redirect('backend/kuesioner-mars/edit/'.(int) $idDemografi);
            return;
        }

        if (!$this->Mars_model->saveAnswers((int) $submission['id_user'], $result['answers'], $idDemografi)) {
            $this->session->set_flashdata('message', $this->alert('danger', 'Data Kuisioner MARS gagal diperbarui.'));
            redirect('backend/kuesioner-mars/edit/'.(int) $idDemografi);
            return;
        }

        $this->redirectMessage('success', 'Data Kuisioner MARS berhasil diperbarui.', 'detail/'.(int) $idDemografi);
    }

    public function delete($idDemografi)
    {
        if (strtoupper($this->input->method(TRUE)) !== 'POST') {
            show_404();
            return;
        }

        if (!$this->Mars_model->deleteSubmission($idDemografi)) {
            return $this->redirectMessage('danger', 'Data Kuisioner MARS tidak ditemukan atau gagal dihapus.');
        }
        $this->redirectMessage('success', 'Data Kuisioner MARS berhasil dihapus.');
    }

    private function requireActiveQuestionnaire()
    {
        $category = $this->Mars_model->getCategoryWithQuestions();
        if (empty($category['pertanyaan'])) {
            $this->redirectMessage('danger', 'Kuisioner MARS belum tersedia atau masih nonaktif.');
            return FALSE;
        }
        return $category;
    }

    private function returnToCreate($message, $idUser, $answers)
    {
        $this->session->set_flashdata('message', $this->alert('danger', $message));
        $this->session->set_flashdata('mars_user_id', (int) $idUser);
        $this->session->set_flashdata('mars_answers', is_array($answers) ? $answers : array());
        redirect('backend/kuesioner-mars/create');
    }

    private function redirectMessage($type, $message, $suffix = '')
    {
        $this->session->set_flashdata('message', $this->alert($type, $message));
        redirect('backend/kuesioner-mars'.($suffix !== '' ? '/'.$suffix : ''));
    }

    private function alert($type, $message)
    {
        return '<div class="alert alert-'.$type.'" role="alert">'.htmlspecialchars($message, ENT_QUOTES, 'UTF-8').'</div>';
    }
}
