<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Berita_hiv extends CI_Controller {

    private $uploadPath = './assets/img/berita_hiv/';

	public function __construct()
    {
        parent::__construct();
        $this->load->model('Berita_hiv_model');
        $this->load->helper('url');

        if (!$this->session->userdata('id_admin')) {
            redirect('login_admin');
        }
    }
	
	public function index()
	{
        $data['getBeritaHiv'] = $this->Berita_hiv_model->getAll();
		$this->loadBackend('berita_hiv/index', $data);
	}

	public function proses_tambah()
	{
        $image = $this->uploadImage();

		$data = array(
	        'title' => $this->input->post('title', TRUE),
	        'ringkasan' => $this->input->post('ringkasan', TRUE),
	        'field' => $this->input->post('field', TRUE),
	        'image' => $image,
	        'link_sumber' => $this->input->post('link_sumber', TRUE),
	        'status' => (int) $this->input->post('status'),
	        'urutan' => (int) $this->input->post('urutan'),
	        'date' => date('Y-m-d H:i:s')
        );

        $this->Berita_hiv_model->insert($data);
        $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Berita HIV/AIDS berhasil ditambahkan.</div>');
        redirect('backend/berita_hiv');
	}

	public function proses_edit($id)
	{
        $berita = $this->Berita_hiv_model->getById($id);
        if (!$berita) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Berita tidak ditemukan.</div>');
            redirect('backend/berita_hiv');
        }

        $image = $this->uploadImage();
        if ($image == '') {
            $image = $berita['image'];
        } else {
            $this->deleteImageFile($berita['image']);
        }

		$data = array(
	        'title' => $this->input->post('title', TRUE),
	        'ringkasan' => $this->input->post('ringkasan', TRUE),
	        'field' => $this->input->post('field', TRUE),
	        'image' => $image,
	        'link_sumber' => $this->input->post('link_sumber', TRUE),
	        'status' => (int) $this->input->post('status'),
	        'urutan' => (int) $this->input->post('urutan')
        );

        $this->Berita_hiv_model->update($id, $data);
        $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Berita HIV/AIDS berhasil diperbarui.</div>');
        redirect('backend/berita_hiv');
	}

	public function proses_hapus($id)
	{
        $berita = $this->Berita_hiv_model->getById($id);
        if ($berita) {
            $this->deleteImageFile($berita['image']);
            $this->Berita_hiv_model->delete($id);
            $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Berita HIV/AIDS berhasil dihapus.</div>');
        }

        redirect('backend/berita_hiv');
	}

    private function uploadImage()
    {
        if (empty($_FILES['image']['name'])) {
            return '';
        }

        $config['upload_path'] = $this->uploadPath;
        $config['allowed_types'] = 'jpg|jpeg|png|webp';
        $config['max_size'] = 4096;
        $config['encrypt_name'] = TRUE;

        $this->upload->initialize($config);

        if (!$this->upload->do_upload('image')) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">'.$this->upload->display_errors('', '').'</div>');
            redirect('backend/berita_hiv');
        }

        return $this->upload->data('file_name');
    }

    private function deleteImageFile($fileName)
    {
        if ($fileName == '') {
            return;
        }

        $filePath = $this->uploadPath.$fileName;
        if (is_file($filePath)) {
            unlink($filePath);
        }
    }
}
