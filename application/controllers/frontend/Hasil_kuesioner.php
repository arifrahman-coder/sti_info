<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Hasil_kuesioner extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Kuesioner_model');

        if (!$this->session->userdata('id_user')) {
            redirect('/');
        }
    }

    public function index()
    {
        $idUser = (int) $this->session->userdata('id_user');
        $submission = $this->Kuesioner_model->getLatestDemographicSubmissionByUser($idUser);

        if (!$submission) {
            $this->session->set_flashdata('error', 'Anda belum memiliki hasil pengisian kuesioner.');
            redirect('frontend/dashboard');
            return;
        }

        $data = array(
            'submission' => $submission,
            'answers' => $this->Kuesioner_model->getDemographicAnswers((int) $submission['id_demografi'])
        );
        $this->loadFrontend('hasil_kuesioner/index', $data);
    }
}
