<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Interaksi_obat extends CI_Controller
{
    private $levels = array('Mayor', 'Moderate', 'Minor');

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Interaksi_obat_model');
        if (!$this->session->userdata('id_admin')) {
            redirect('login_admin');
        }
    }

    public function index()
    {
        $this->loadBackend('interaksi_obat/index', array(
            'interactions' => $this->Interaksi_obat_model->getAll()
        ));
    }

    public function create()
    {
        $this->loadBackend('interaksi_obat/form', array(
            'interaction' => array(),
            'levels' => $this->levels,
            'formAction' => base_url('backend/interaksi-obat/simpan'),
            'formTitle' => 'Tambah Interaksi Obat'
        ));
    }

    public function store()
    {
        $this->requirePost();
        $data = $this->validatedPayload();
        if ($data === FALSE) {
            return;
        }
        if ($this->Interaksi_obat_model->nameExists($data['nama_obat'])) {
            return $this->redirectMessage('danger', 'Nama obat sudah tersedia.', 'tambah');
        }
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        if (!$this->Interaksi_obat_model->insert($data)) {
            return $this->redirectMessage('danger', 'Data interaksi gagal disimpan.', 'tambah');
        }
        $this->redirectMessage('success', 'Data interaksi obat berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $interaction = $this->Interaksi_obat_model->getById($id);
        if (!$interaction) {
            return $this->redirectMessage('danger', 'Data interaksi obat tidak ditemukan.');
        }
        $this->loadBackend('interaksi_obat/form', array(
            'interaction' => $interaction,
            'levels' => $this->levels,
            'formAction' => base_url('backend/interaksi-obat/update/'.(int) $id),
            'formTitle' => 'Edit Interaksi Obat'
        ));
    }

    public function update($id)
    {
        $this->requirePost();
        if (!$this->Interaksi_obat_model->getById($id)) {
            return $this->redirectMessage('danger', 'Data interaksi obat tidak ditemukan.');
        }
        $data = $this->validatedPayload('edit/'.(int) $id);
        if ($data === FALSE) {
            return;
        }
        if ($this->Interaksi_obat_model->nameExists($data['nama_obat'], $id)) {
            return $this->redirectMessage('danger', 'Nama obat sudah digunakan data lain.', 'edit/'.(int) $id);
        }
        $data['updated_at'] = date('Y-m-d H:i:s');
        if (!$this->Interaksi_obat_model->update($id, $data)) {
            return $this->redirectMessage('danger', 'Data interaksi gagal diperbarui.', 'edit/'.(int) $id);
        }
        $this->redirectMessage('success', 'Data interaksi obat berhasil diperbarui.');
    }

    public function delete($id)
    {
        $this->requirePost();
        $interaction = $this->Interaksi_obat_model->getById($id);
        if (!$interaction) {
            return $this->redirectMessage('danger', 'Data interaksi obat tidak ditemukan.');
        }
        if (!$this->Interaksi_obat_model->delete($id)) {
            return $this->redirectMessage('danger', 'Data interaksi obat gagal dihapus.');
        }
        $this->redirectMessage('success', 'Data interaksi obat berhasil dihapus.');
    }

    private function validatedPayload($returnPath = 'tambah')
    {
        $name = trim((string) $this->input->post('nama_obat', TRUE));
        $levelTdf = trim((string) $this->input->post('level_tdf', TRUE));
        $level3tc = trim((string) $this->input->post('level_3tc', TRUE));
        $mechanism = trim((string) $this->input->post('mekanisme', TRUE));
        $recommendation = trim((string) $this->input->post('rekomendasi', TRUE));
        if ($name === '' || mb_strlen($name) > 200 || $mechanism === '' || $recommendation === '' || !$this->isValidLevel($levelTdf) || !$this->isValidLevel($level3tc)) {
            $this->redirectMessage('danger', 'Nama obat (maksimal 200 karakter), level TDF/3TC, mekanisme, dan rekomendasi wajib diisi dengan benar.', $returnPath);
            return FALSE;
        }
        return array(
            'no_sumber' => mb_substr(trim((string) $this->input->post('no_sumber', TRUE)), 0, 20),
            'nama_obat' => $name,
            'level_tdf' => $levelTdf,
            'level_3tc' => $level3tc,
            'mekanisme' => $mechanism,
            'rekomendasi' => $recommendation,
            'catatan' => trim((string) $this->input->post('catatan', TRUE)),
            'referensi' => mb_substr(trim((string) $this->input->post('referensi', TRUE)), 0, 100),
            'urutan' => max(0, (int) $this->input->post('urutan')),
            'status' => $this->input->post('status') === '1' ? 1 : 0
        );
    }

    private function isValidLevel($level)
    {
        // Preserve the source's explanation after the screening category.
        return mb_strlen($level) <= 200
            && preg_match('/^(Mayor|Moderate|Minor)(?:\s+[–-]\s+[^\r\n]+)?$/u', $level) === 1;
    }

    private function requirePost()
    {
        if (strtoupper($this->input->method(TRUE)) !== 'POST') {
            show_404();
            exit;
        }
    }

    private function redirectMessage($type, $message, $suffix = '')
    {
        $this->session->set_flashdata('message', '<div class="alert alert-'.$type.'" role="alert">'.htmlspecialchars($message, ENT_QUOTES, 'UTF-8').'</div>');
        redirect('backend/interaksi-obat'.($suffix !== '' ? '/'.$suffix : ''));
    }
}
