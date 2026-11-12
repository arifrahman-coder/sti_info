<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kuesioner extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Kuesioner_model');
        $this->load->helper('url');

        if (!$this->session->userdata('id_admin')) {
            redirect('login_admin');
        }
    }

    public function index()
    {
        $data['kategori'] = $this->Kuesioner_model->getAllCategories();
        $data['pertanyaan'] = $this->Kuesioner_model->getAllQuestions();
        $data['opsi'] = $this->Kuesioner_model->getAllOptions();
        $data['statusUser'] = $this->db->order_by('id', 'ASC')->get('status_user')->result_array();
        $this->loadBackend('kuesioner/index', $data);
    }

    public function proses_tambah_kategori()
    {
        $idStatus = (int) $this->input->post('id_status');
        $data = array(
            'id_status' => $idStatus > 0 ? $idStatus : NULL,
            'nama' => $this->input->post('nama', TRUE),
            'deskripsi' => $this->input->post('deskripsi', TRUE),
            'label_pertanyaan' => $this->input->post('label_pertanyaan', TRUE),
            'urutan' => (int) $this->input->post('urutan'),
            'status' => (int) $this->input->post('status'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        );

        $this->Kuesioner_model->insertCategory($data);
        $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Kategori kuesioner berhasil ditambahkan.</div>');
        redirect('backend/kuesioner');
    }

    public function proses_edit_kategori($id = NULL)
    {
        if (!$this->Kuesioner_model->getCategoryById($id)) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Kategori tidak ditemukan.</div>');
            redirect('backend/kuesioner');
        }

        $idStatus = (int) $this->input->post('id_status');
        $data = array(
            'id_status' => $idStatus > 0 ? $idStatus : NULL,
            'nama' => $this->input->post('nama', TRUE),
            'deskripsi' => $this->input->post('deskripsi', TRUE),
            'label_pertanyaan' => $this->input->post('label_pertanyaan', TRUE),
            'urutan' => (int) $this->input->post('urutan'),
            'status' => (int) $this->input->post('status'),
            'updated_at' => date('Y-m-d H:i:s')
        );

        $this->Kuesioner_model->updateCategory($id, $data);
        $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Kategori kuesioner berhasil diperbarui.</div>');
        redirect('backend/kuesioner');
    }

    public function proses_hapus_kategori($id = NULL)
    {
        if (!$this->Kuesioner_model->getCategoryById($id)) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Kategori tidak ditemukan.</div>');
            redirect('backend/kuesioner');
        }

        $this->Kuesioner_model->deleteCategory($id);
        $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Kategori kuesioner berhasil dihapus.</div>');
        redirect('backend/kuesioner');
    }

    public function proses_tambah_pertanyaan()
    {
        $kode = strtoupper(trim((string) $this->input->post('kode', TRUE)));
        if ($kode == '' || $this->Kuesioner_model->questionCodeExists($kode)) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Kode pertanyaan kosong atau sudah digunakan.</div>');
            redirect('backend/kuesioner');
        }

        $data = array(
            'kategori_id' => (int) $this->input->post('kategori_id'),
            'kode' => $kode,
            'pertanyaan' => $this->input->post('pertanyaan', TRUE),
            'tipe' => $this->input->post('tipe', TRUE),
            'kunci_jawaban' => strtoupper(trim((string) $this->input->post('kunci_jawaban', TRUE))),
            'wajib' => (int) $this->input->post('wajib'),
            'urutan' => (int) $this->input->post('urutan'),
            'status' => (int) $this->input->post('status'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        );

        $this->Kuesioner_model->insertQuestion($data);
        $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Pertanyaan berhasil ditambahkan.</div>');
        redirect('backend/kuesioner');
    }

    public function proses_edit_pertanyaan($id = NULL)
    {
        if (!$this->Kuesioner_model->getQuestionById($id)) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Pertanyaan tidak ditemukan.</div>');
            redirect('backend/kuesioner');
        }

        $kode = strtoupper(trim((string) $this->input->post('kode', TRUE)));
        if ($kode == '' || $this->Kuesioner_model->questionCodeExists($kode, $id)) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Kode pertanyaan kosong atau sudah digunakan.</div>');
            redirect('backend/kuesioner');
        }

        $data = array(
            'kategori_id' => (int) $this->input->post('kategori_id'),
            'kode' => $kode,
            'pertanyaan' => $this->input->post('pertanyaan', TRUE),
            'tipe' => $this->input->post('tipe', TRUE),
            'kunci_jawaban' => strtoupper(trim((string) $this->input->post('kunci_jawaban', TRUE))),
            'wajib' => (int) $this->input->post('wajib'),
            'urutan' => (int) $this->input->post('urutan'),
            'status' => (int) $this->input->post('status'),
            'updated_at' => date('Y-m-d H:i:s')
        );

        $this->Kuesioner_model->updateQuestion($id, $data);
        $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Pertanyaan berhasil diperbarui.</div>');
        redirect('backend/kuesioner');
    }

    public function proses_hapus_pertanyaan($id = NULL)
    {
        if (!$this->Kuesioner_model->getQuestionById($id)) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Pertanyaan tidak ditemukan.</div>');
            redirect('backend/kuesioner');
        }

        $this->Kuesioner_model->deleteQuestion($id);
        $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Pertanyaan berhasil dihapus.</div>');
        redirect('backend/kuesioner');
    }

    public function proses_tambah_opsi()
    {
        $data = array(
            'pertanyaan_id' => (int) $this->input->post('pertanyaan_id'),
            'label' => $this->input->post('label', TRUE),
            'nilai' => $this->input->post('nilai', TRUE),
            'skor' => (int) $this->input->post('skor'),
            'urutan' => (int) $this->input->post('urutan'),
            'status' => (int) $this->input->post('status'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        );

        $this->Kuesioner_model->insertOption($data);
        $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Opsi jawaban berhasil ditambahkan.</div>');
        redirect('backend/kuesioner');
    }

    public function proses_edit_opsi($id = NULL)
    {
        if (!$this->Kuesioner_model->getOptionById($id)) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Opsi jawaban tidak ditemukan.</div>');
            redirect('backend/kuesioner');
        }

        $data = array(
            'pertanyaan_id' => (int) $this->input->post('pertanyaan_id'),
            'label' => $this->input->post('label', TRUE),
            'nilai' => $this->input->post('nilai', TRUE),
            'skor' => (int) $this->input->post('skor'),
            'urutan' => (int) $this->input->post('urutan'),
            'status' => (int) $this->input->post('status'),
            'updated_at' => date('Y-m-d H:i:s')
        );

        $this->Kuesioner_model->updateOption($id, $data);
        $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Opsi jawaban berhasil diperbarui.</div>');
        redirect('backend/kuesioner');
    }

    public function proses_hapus_opsi($id = NULL)
    {
        if (!$this->Kuesioner_model->getOptionById($id)) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Opsi jawaban tidak ditemukan.</div>');
            redirect('backend/kuesioner');
        }

        $this->Kuesioner_model->deleteOption($id);
        $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Opsi jawaban berhasil dihapus.</div>');
        redirect('backend/kuesioner');
    }
}
