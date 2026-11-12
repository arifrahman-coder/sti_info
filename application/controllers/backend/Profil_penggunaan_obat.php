<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Profil_penggunaan_obat extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('ProfilPenggunaanObat_model');
        $this->load->library('ProfilPenggunaanObat_form', NULL, 'profilForm');

        if (!$this->session->userdata('id_admin')) {
            redirect('login_admin');
        }
    }

    public function index()
    {
        $this->loadBackend('profil_penggunaan_obat/index', array(
            'profiles' => $this->ProfilPenggunaanObat_model->getAll()
        ));
    }

    public function detail($idProfil)
    {
        $profile = $this->ProfilPenggunaanObat_model->getById($idProfil);
        if (!$profile) {
            return $this->redirectMessage('danger', 'Profil penggunaan obat tidak ditemukan.');
        }

        $this->loadBackend('profil_penggunaan_obat/detail', array(
            'profile' => $profile
        ));
    }

    public function edit($idProfil)
    {
        $profile = $this->ProfilPenggunaanObat_model->getById($idProfil);
        if (!$profile) {
            return $this->redirectMessage('danger', 'Profil penggunaan obat tidak ditemukan.');
        }

        $oldInput = $this->session->flashdata('profil_obat_form_data');

        $this->loadBackend('profil_penggunaan_obat/edit', array(
            'profile' => $profile,
            'oldInput' => is_array($oldInput) ? $oldInput : array(),
            'formAction' => base_url('backend/profil-penggunaan-obat/update/'.(int) $idProfil),
            'submitLabel' => 'Simpan Perubahan',
            'isAdmin' => TRUE
        ));
    }

    public function update($idProfil)
    {
        if (strtoupper($this->input->method(TRUE)) !== 'POST') {
            show_404();
            return;
        }
        if (!$this->ProfilPenggunaanObat_model->getById($idProfil)) {
            return $this->redirectMessage('danger', 'Profil penggunaan obat tidak ditemukan.');
        }

        // Validasi whitelist dan escaping output ditangani oleh form/view; jangan ubah nilai medis seperti "<50".
        $post = $this->input->post(NULL, FALSE);
        $post = is_array($post) ? $post : array();
        $result = $this->profilForm->validate($post);
        if (!$result['success']) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger">'.htmlspecialchars($result['message'], ENT_QUOTES, 'UTF-8').'</div>');
            $this->session->set_flashdata('profil_obat_form_data', $post);
            redirect('backend/profil-penggunaan-obat/edit/'.(int) $idProfil);
            return;
        }

        if (!$this->ProfilPenggunaanObat_model->updateByAdmin($idProfil, $result['data'])) {
            return $this->redirectMessage('danger', 'Profil penggunaan obat gagal diperbarui.', 'edit/'.(int) $idProfil);
        }

        $this->redirectMessage('success', 'Profil penggunaan obat berhasil diperbarui.', 'detail/'.(int) $idProfil);
    }

    public function delete($idProfil)
    {
        if (strtoupper($this->input->method(TRUE)) !== 'POST') {
            show_404();
            return;
        }
        if (!$this->ProfilPenggunaanObat_model->getById($idProfil)) {
            return $this->redirectMessage('danger', 'Profil penggunaan obat tidak ditemukan.');
        }

        if ($this->ProfilPenggunaanObat_model->delete($idProfil)) {
            return $this->redirectMessage('success', 'Profil penggunaan obat berhasil dihapus.');
        }
        $this->redirectMessage('danger', 'Profil penggunaan obat gagal dihapus.');
    }

    private function redirectMessage($type, $message, $suffix = '')
    {
        $this->session->set_flashdata('message', '<div class="alert alert-'.$type.'">'.htmlspecialchars($message, ENT_QUOTES, 'UTF-8').'</div>');
        $url = 'backend/profil-penggunaan-obat'.($suffix !== '' ? '/'.$suffix : '');
        redirect($url);
    }

}
