<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Profil_penggunaan_obat extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('ProfilPenggunaanObat_model');
        $this->load->library('ProfilPenggunaanObat_form', NULL, 'profilForm');

        if (!$this->session->userdata('id_user')) {
            redirect('/');
        }
    }

    public function index()
    {
        $idUser = (int) $this->session->userdata('id_user');
        $profile = $this->ProfilPenggunaanObat_model->getByUser($idUser);
        $oldInput = $this->session->flashdata('profil_obat_form_data');

        $data = array(
            'profile' => $profile ?: array(),
            'oldInput' => is_array($oldInput) ? $oldInput : array(),
            'formAction' => base_url('frontend/profil-penggunaan-obat/simpan'),
            'submitLabel' => $profile ? 'Perbarui Profil' : 'Simpan Profil',
            'isAdmin' => FALSE
        );
        $this->loadFrontend('profil_penggunaan_obat/index', $data);
    }

    public function save()
    {
        if (strtoupper($this->input->method(TRUE)) !== 'POST') {
            show_404();
            return;
        }

        // Validasi whitelist dan escaping output ditangani oleh form/view; jangan ubah nilai medis seperti "<50".
        $post = $this->input->post(NULL, FALSE);
        $post = is_array($post) ? $post : array();
        $result = $this->profilForm->validate($post);
        if (!$result['success']) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger"><i class="fas fa-exclamation-circle mr-1"></i>'.htmlspecialchars($result['message'], ENT_QUOTES, 'UTF-8').'</div>');
            $this->session->set_flashdata('profil_obat_form_data', $post);
            redirect('frontend/profil-penggunaan-obat');
            return;
        }

        $idUser = (int) $this->session->userdata('id_user');
        if (!$this->ProfilPenggunaanObat_model->saveForUser($idUser, $result['data'])) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger">Profil penggunaan obat gagal disimpan. Silakan coba kembali.</div>');
            $this->session->set_flashdata('profil_obat_form_data', $post);
            redirect('frontend/profil-penggunaan-obat');
            return;
        }

        $this->session->set_flashdata('message', '<div class="alert alert-success"><i class="fas fa-check-circle mr-1"></i>Profil penggunaan obat berhasil disimpan.</div>');
        redirect('frontend/profil-penggunaan-obat');
    }

}
