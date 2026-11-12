<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

	public function __construct()
    {
        parent::__construct();
        $this->load->model('login_model'); // load model auth_model
        $this->load->model('home_model');
    }

	public function index()
	{
		// validasi form login
	        $this->form_validation->set_rules('no_hp', 'No HP', 'required');
	        $this->form_validation->set_rules('password', 'Password', 'required');

	        if ($this->form_validation->run() == false) {
	            // tampilkan halaman login jika validasi gagal
	            @$id = $this->session->userdata('id_user');
	            @$getstsLogin = $this->login_model->getStsLogin($id);
	            @$data['status_login'] = $getstsLogin['status_login'];
	            @$data['name'] = $getstsLogin['nama'];
	            $data['getPenyakit'] = $this->home_model->getPenyakit();
	            $data['getModulEdukasi'] = $this->home_model->getModulEdukasi();
	            $data['getBeritaHiv'] = $this->home_model->getBeritaHiv();
	            $data['getStatusUser'] = $this->home_model->getStatusUser();

	            $data['getModulPembelajaran'] = $this->home_model->getModulPembelajaran();

	            $this->load->view('index',$data);
	        } else {
	            // cek login
	            $this->proses_login();
	        }
	}

	public function proses_login(){
		//recaptcha google
        $this->config->load('external_services', TRUE);
    	$url = "https://www.google.com/recaptcha/api/siteverify";
    	$data = [
		   'secret' => $this->config->item('recaptcha_home_secret', 'external_services'),/*your secret key */
		   'response' => $this->input->post('token'),
		   'remoteip' => $_SERVER['REMOTE_ADDR']
		];
        $options = array(
		   'http' => array(
		   'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
		   'method'  => 'POST',
		   'content' => http_build_query($data)
		   )
		);
		$context  = stream_context_create($options);
		$response = file_get_contents($url, false, $context);
		/*convert json */
		$res = json_decode($response, true);

		if ($res['success'] == 1 && $res['score'] >= 0.5) {

		$noHp = $this->normalizePhone($this->input->post('no_hp', TRUE));
		$password = $this->input->post('password');
		if (!$this->isValidPhone($noHp)) {
			$this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Nomor HP tidak valid.</div>');
			redirect('/');
		}

		$users = $this->login_model->getData_UsersByPhone($noHp);
		$user = NULL;
		foreach ($users as $candidate) {
			if (password_verify($password, $candidate['password'])) {
				$user = $candidate;
				break;
			}
		}
		if ($user) {
			$this->login_model->cekAccountUserID($user['id'], 1);
			$this->session->set_userdata(array('id_user' => $user['id']));
			redirect('frontend/dashboard');
		}

		if (empty($users)) {
			$this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Nomor HP tidak ditemukan.</div>');
		} else {
			$this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Password salah.</div>');
		}
		redirect('/');
		}
		else{
			$this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">recaptcha error</div>');
			redirect('/'); // redirect kembali ke halaman login
		}
	}

	public function prosesTambahUser(){
		$status = (int) $this->input->post("status");
		if (!$this->home_model->statusUserExists($status)) {
			$this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Silakan pilih status akun yang tersedia.</div>');
			redirect('/');
		}

		$noHp = $this->normalizePhone($this->input->post('no_hp', TRUE));
		if (!$this->isValidPhone($noHp)) {
			$this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Nomor HP tidak valid. Gunakan nomor Indonesia yang aktif.</div>');
			redirect('/');
		}

		$user = $this->login_model->getData_UsersByPhone($noHp);
		if (!$user) {
		$options = [
            'cost' => 16,
        ];
        $password_hash = password_hash($this->input->post("password"),PASSWORD_DEFAULT,$options);

        $data = array(
            'nama' => $this->input->post("nama", TRUE),
			'email' => '', // Kolom lama dipertahankan untuk kompatibilitas data historis.
			'no_hp' => $noHp,
            'id_status' => $status,
			'status_login' => 1,
            'password' => $password_hash
        );
		if (!$this->db->insert('user', $data)) {
			$this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Registrasi gagal disimpan.</div>');
			redirect('/');
		}
		$this->session->set_userdata(array('id_user' => $this->db->insert_id()));
		redirect('frontend/dashboard');
    	}
    	else{
			$this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Maaf, nomor HP sudah digunakan.</div>');
	                redirect('/');
    	}

	}

	private function normalizePhone($phone)
	{
		$digits = preg_replace('/[^0-9]/', '', (string) $phone);
		if (strpos($digits, '62') === 0) {
			return '0' . substr($digits, 2);
		}
		if (strpos($digits, '8') === 0) {
			return '0' . $digits;
		}
		return $digits;
	}

	private function isValidPhone($phone)
	{
		return preg_match('/^08[0-9]{8,13}$/', (string) $phone) === 1;
	}

	public function logout()
    {
    	$id = $this->session->userdata('id_user');
		$this->login_model->cekAccountUserID($id, 0);
        $this->session->unset_userdata('id_user');
        redirect('/'); // redirect kembali ke halaman login
    }
}
