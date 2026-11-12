<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Data_user extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Data_user_model');
        $this->load->helper('url');

        if (!$this->session->userdata('id_admin')) {
            redirect('login_admin');
        }
    }

    public function index()
    {
        $data['getDataUser'] = $this->Data_user_model->getAll();
        $data['getStatusUser'] = $this->Data_user_model->getStatusUser();
        $this->loadBackend('data_user/index', $data);
    }

    public function proses_tambah()
    {
        $data = $this->getUserData();
        if (empty($data)) {
            redirect('backend/data_user');
        }

        $password = trim((string) $this->input->post('password'));
        if ($password == '') {
            $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Password wajib diisi.</div>');
            redirect('backend/data_user');
        }

        $data['password'] = password_hash($password, PASSWORD_DEFAULT, array('cost' => 16));

        $this->Data_user_model->insert($data);
        $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Data user berhasil ditambahkan.</div>');
        redirect('backend/data_user');
    }

    public function proses_edit($id = NULL)
    {
        $user = $this->Data_user_model->getById($id);
        if (!$user) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Data user tidak ditemukan.</div>');
            redirect('backend/data_user');
        }

        $data = $this->getUserData($id);
        if (empty($data)) {
            redirect('backend/data_user');
        }

        $password = trim((string) $this->input->post('password'));
        if ($password != '') {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT, array('cost' => 16));
        }

        $this->Data_user_model->update($id, $data);
        $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Data user berhasil diperbarui.</div>');
        redirect('backend/data_user');
    }

    public function proses_hapus($id = NULL)
    {
        $user = $this->Data_user_model->getById($id);
        if (!$user) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Data user tidak ditemukan.</div>');
            redirect('backend/data_user');
        }

        $this->Data_user_model->delete($id);
        $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Data user berhasil dihapus.</div>');
        redirect('backend/data_user');
    }

    public function proses_hapus_bulk()
    {
        $userIds = $this->input->post('user_ids');
        if (!is_array($userIds) || empty($userIds)) {
            $this->session->set_flashdata('message', '<div class="alert alert-warning" role="alert">Pilih minimal satu user untuk dihapus.</div>');
            redirect('backend/data_user');
        }

        $deletedCount = $this->Data_user_model->deleteBulk($userIds);
        if ($deletedCount > 0) {
            $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">'.$deletedCount.' data user berhasil dihapus.</div>');
        } else {
            $this->session->set_flashdata('message', '<div class="alert alert-warning" role="alert">Tidak ada data user yang terhapus.</div>');
        }

        redirect('backend/data_user');
    }

    private function getUserData($excludeId = NULL)
    {
        $nama = trim((string) $this->input->post('nama', TRUE));
        $email = trim((string) $this->input->post('email', TRUE));
        $noHp = trim((string) $this->input->post('no_hp', TRUE));
        $idStatus = (int) $this->input->post('id_status');
        $statusLogin = (int) $this->input->post('status_login');

        if ($nama == '' || $email == '' || $noHp == '') {
            $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Nama, email, dan no hp wajib diisi.</div>');
            return array();
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Format email tidak valid.</div>');
            return array();
        }

        if (!$this->Data_user_model->statusExists($idStatus)) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Status user tidak valid.</div>');
            return array();
        }

        if ($this->Data_user_model->emailExists($email, $excludeId)) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Email sudah digunakan oleh user lain.</div>');
            return array();
        }

        return array(
            'nama' => $nama,
            'email' => $email,
            'no_hp' => $noHp,
            'id_status' => $idStatus,
            'status_login' => $statusLogin == 1 ? 1 : 0
        );
    }
}
