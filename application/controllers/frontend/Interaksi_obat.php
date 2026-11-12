<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Interaksi_obat extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Interaksi_obat_model');
        if (!$this->session->userdata('id_user')) {
            redirect('/');
        }
    }

    public function index()
    {
        $selectedId = (int) $this->input->get('obat', TRUE);
        $data = array(
            'interactions' => $this->Interaksi_obat_model->getAll(TRUE),
            'selected' => $selectedId > 0 ? $this->Interaksi_obat_model->getById($selectedId, TRUE) : array(),
            'references' => $this->Interaksi_obat_model->getReferences()
        );
        $this->loadFrontend('interaksi_obat/index', $data);
    }
}
