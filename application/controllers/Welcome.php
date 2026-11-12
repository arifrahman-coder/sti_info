<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/userguide3/general/urls.html
	 */
	public function index()
	{
		$kelas = $this->db->query("SELECT * FROM kepemilikan_laptop GROUP BY kelas")->result_array();
		$data['kelas'] = $kelas;
		$this->load->view('welcome_message',$data);
	}
public function coba()
{
    $this->config->load('external_services', TRUE);
    $apiKey = $this->config->item('openai_api_key', 'external_services');

    $url = "https://api.openai.com/v1/responses";

    $payload = [
        "model" => "gpt-5-nano",
        "input" => "write a haiku about ai",
        "store" => true
    ];

    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Authorization: Bearer " . $apiKey
    ]);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        log_message('error', 'Curl Error: ' . curl_error($ch));
        show_error('Terjadi kesalahan koneksi API');
    }

    curl_close($ch);

    // tampilkan response (sementara untuk testing)
    header('Content-Type: application/json');
    echo $response;
}

}
