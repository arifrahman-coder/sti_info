<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login_admin extends CI_Controller {

	public function __construct()
    {
        parent::__construct();
        $this->load->model('login_model'); // load model auth_model
    }

	public function index()
	{
		// cek apakah user sudah login atau belum
        if (!$this->session->userdata('id_admin')) {
            // validasi form login
	        $this->form_validation->set_rules('email', 'Email', 'required');
	        $this->form_validation->set_rules('password', 'Password', 'required');

	        if ($this->form_validation->run() == false) {
	            // tampilkan halaman login jika validasi gagal
	            $this->load->view('login_admin');
	        } else {
	            // cek login
	            $this->proses_login();
	        }
        }
        else{
        	redirect('backend/dashboard'); // redirect ke halaman dashboard
        }
	}

	public function proses_login(){
		//recaptcha google
        $this->config->load('external_services', TRUE);
    	$url = "https://www.google.com/recaptcha/api/siteverify";
    	$data = [
		   'secret' => $this->config->item('recaptcha_admin_secret', 'external_services'),/*your secret key */
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

		$email = $this->input->post('email');
		$password = $this->input->post('password');
		$user = $this->login_model->getData_Admin($email);
		if ($user) {
            // cek apakah password cocok
            	if (password_verify($password, $user['password'])) {
	                $data = [
	                    'id_admin'			=> $user['id']
	                ];
                	$this->session->set_userdata($data);
                	//printf($data['id_prodiSMS']);
                	redirect('backend/dashboard'); // redirect ke halaman dashboard
	            } 
	            else {
	                $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Wrong password!</div>');
	                redirect('login_admin'); // redirect kembali ke halaman login
	            }
	        } 
	    else {
	            $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Username not found!</div>');
	            redirect('login_admin'); // redirect kembali ke halaman login
	    }
		}
		else{
			redirect('login_admin'); // redirect kembali ke halaman login
		}
	}

	public function logout()
    {
        $this->session->unset_userdata('id_admin');
        redirect('login_admin'); // redirect kembali ke halaman login
    }
}
