<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Edukasi_sti extends CI_Controller {

	public function __construct()
    {
        parent::__construct();
        $this->load->model('Modul_edukasi_model');
        $this->load->helper('url'); // load helper url
        if (!$this->session->userdata('id_admin')) {
            redirect('login_admin'); // redirect ke halaman login jika belum login
        }
    }
	
	public function index()
	{
		$getModulEdukasi = $this->Modul_edukasi_model->getModulEdukasi();
		$data['getModulEdukasi']= $getModulEdukasi;
		$this->loadBackend('edukasi_sti/index',$data);
	}

	public function proses_tambah()
	{
		$data = $this->getVideoData();
        if (empty($data)) {
            redirect('backend/edukasi_sti');
        }

        $this->Modul_edukasi_model->insert($data);
        $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Video Edukasi HIV AIDS berhasil ditambahkan.</div>');
        redirect('backend/edukasi_sti');
	}

	public function proses_edit($id = NULL)
	{
        $modul = $this->Modul_edukasi_model->getById($id);
        if (!$modul) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Video edukasi tidak ditemukan.</div>');
            redirect('backend/edukasi_sti');
        }

		$data = $this->getVideoData();
        if (empty($data)) {
            redirect('backend/edukasi_sti');
        }

        $this->Modul_edukasi_model->update($id, $data);
        $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Video Edukasi HIV AIDS berhasil diperbarui.</div>');
        redirect('backend/edukasi_sti');
	}

	public function proses_hapus($id = NULL)
	{
        $modul = $this->Modul_edukasi_model->getById($id);
        if (!$modul) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Video edukasi tidak ditemukan.</div>');
            redirect('backend/edukasi_sti');
        }

        $this->Modul_edukasi_model->delete($id);
        $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Video Edukasi HIV AIDS berhasil dihapus.</div>');
        redirect('backend/edukasi_sti');
	}

    private function getVideoData()
    {
        $title = trim($this->input->post('title', TRUE));
        $linkVideo = $this->normalizeYoutubeLink($this->input->post('link_video', TRUE));

        if ($title == '' || $linkVideo == '') {
            $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Judul dan link video wajib diisi dengan benar.</div>');
            return array();
        }

        $titleLength = function_exists('mb_strlen') ? mb_strlen($title, 'UTF-8') : strlen($title);
        if ($titleLength > 50) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Judul video maksimal 50 karakter.</div>');
            return array();
        }

        return array(
            'title' => $title,
            'link' => $linkVideo
        );
    }

    private function normalizeYoutubeLink($linkVideo)
    {
        $linkVideo = trim((string) $linkVideo);
        if ($linkVideo == '') {
            return '';
        }

        if (preg_match('/^(www\.)?(youtube\.com|youtu\.be)\//i', $linkVideo)) {
            $linkVideo = 'https://'.$linkVideo;
        }

        if (filter_var($linkVideo, FILTER_VALIDATE_URL)) {
            $parts = parse_url($linkVideo);
            $host = isset($parts['host']) ? strtolower($parts['host']) : '';
            $path = isset($parts['path']) ? trim($parts['path'], '/') : '';

            if (strpos($host, 'youtu.be') !== FALSE && $path != '') {
                return explode('/', $path)[0];
            }

            if (strpos($host, 'youtube.com') !== FALSE) {
                if (isset($parts['query'])) {
                    parse_str($parts['query'], $query);
                    if (!empty($query['v'])) {
                        return $query['v'];
                    }
                }

                $segments = explode('/', $path);
                if (in_array($segments[0], array('embed', 'shorts', 'live'), TRUE) && !empty($segments[1])) {
                    return $segments[1];
                }
            }
        }

        if (preg_match('/^[A-Za-z0-9_-]+$/', $linkVideo)) {
            return $linkVideo;
        }

        return '';
    }

}
