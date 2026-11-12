<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kuesioner_mars extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Mars_model');
        $this->load->library('Mars_form', NULL, 'marsForm');

        if (!$this->session->userdata('id_user')) {
            redirect('/');
        }
    }

    public function index()
    {
        $idUser = (int) $this->session->userdata('id_user');
        if (!$this->Mars_model->isEligibleUser($idUser)) {
            return $this->redirectMessage('danger', 'Data akun pengguna tidak ditemukan.', 'frontend/dashboard');
        }

        $category = $this->Mars_model->getCategoryWithQuestions();
        if (empty($category['pertanyaan'])) {
            return $this->redirectMessage('danger', 'Kuisioner MARS belum tersedia atau masih nonaktif.', 'frontend/dashboard');
        }

        $submission = $this->Mars_model->getSubmissionByUser($idUser);
        $oldAnswers = $this->session->flashdata('mars_answers');
        $answerMap = is_array($oldAnswers)
            ? $oldAnswers
            : (!empty($submission['answers']) ? $submission['answers'] : array());

        $this->loadFrontend('kuesioner_mars/index', array(
            'category' => $category,
            'questions' => $category['pertanyaan'],
            'answerMap' => $answerMap,
            'submission' => $submission
        ));
    }

    public function save()
    {
        if (strtoupper($this->input->method(TRUE)) !== 'POST') {
            show_404();
            return;
        }

        $idUser = (int) $this->session->userdata('id_user');
        if (!$this->Mars_model->isEligibleUser($idUser)) {
            return $this->redirectMessage('danger', 'Data akun pengguna tidak ditemukan.', 'frontend/dashboard');
        }

        $category = $this->Mars_model->getCategoryWithQuestions();
        if (empty($category['pertanyaan'])) {
            return $this->redirectMessage('danger', 'Kuisioner MARS belum tersedia atau masih nonaktif.');
        }

        $postedAnswers = $this->input->post('jawaban');
        $result = $this->marsForm->validate($postedAnswers, $category['pertanyaan']);
        if (!$result['success']) {
            $this->session->set_flashdata('mars_answers', is_array($postedAnswers) ? $postedAnswers : array());
            return $this->redirectMessage('danger', $result['message']);
        }

        $submission = $this->Mars_model->getSubmissionByUser($idUser);
        $idDemografi = !empty($submission['id_demografi']) ? (int) $submission['id_demografi'] : NULL;
        if (!$this->Mars_model->saveAnswers($idUser, $result['answers'], $idDemografi)) {
            return $this->redirectMessage('danger', 'Jawaban Kuisioner MARS gagal disimpan. Silakan coba kembali.');
        }

        $message = $submission
            ? 'Jawaban Kuisioner MARS berhasil diperbarui.'
            : 'Jawaban Kuisioner MARS berhasil disimpan.';
        $this->redirectMessage('success', $message);
    }

    private function redirectMessage($type, $message, $url = 'frontend/kuesioner-mars')
    {
        $this->session->set_flashdata('message', '<div class="alert alert-'.$type.'" role="alert">'.htmlspecialchars($message, ENT_QUOTES, 'UTF-8').'</div>');
        redirect($url);
    }
}
