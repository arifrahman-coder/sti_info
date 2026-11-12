<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Modul_pembelajaran extends CI_Controller {

	public function __construct()
    {
        parent::__construct();
	        $this->load->helper('url'); // load helper url
	        $this->load->model('ModulPembelajaran_model');
	        $this->load->model('ModulTes_model');
        if (!$this->session->userdata('id_user')) {
            redirect('/'); // redirect ke halaman login jika belum login
        }
    }
	
	public function index($id_penyakit)
	{
		$getModulPembelajaran = $this->ModulPembelajaran_model->getModulPembelajaran($id_penyakit);
		$getTitleModulPembelajaran = $this->ModulPembelajaran_model->getTitleModulPembelajaran($id_penyakit);
		$data['getModulPembelajaran']= $getModulPembelajaran;
		$data['getTitleModulPembelajaran'] = $getTitleModulPembelajaran;
		$this->loadFrontend('modul_pembelajaran/index',$data);
	}

	public function mulai($id_modul)
	{
		$id_modul = (int) $id_modul;
		$id_user = (int) $this->session->userdata('id_user');
		if (!$this->ModulTes_model->getModule($id_modul)) {
			show_404();
			return;
		}

		$this->ModulTes_model->ensureModuleTests($id_modul);
		if (!$this->ModulTes_model->hasCompletedPreTest($id_modul, $id_user)) {
			redirect('/frontend/modul_pembelajaran/tes/'.$id_modul.'/pre_test');
			return;
		}

		redirect('/frontend/modul_pembelajaran/detail_modul/'.$id_modul.'/0/bab');
	}

	public function tes($id_modul, $tipe)
	{
		$id_modul = (int) $id_modul;
		$id_user = (int) $this->session->userdata('id_user');
		$tipe = (string) $tipe;
		$module = $this->ModulTes_model->getModule($id_modul);

		if (!$module || !$this->ModulTes_model->isValidType($tipe)) {
			show_404();
			return;
		}

		if ($tipe === ModulTes_model::POST_TEST
			&& !$this->ModulTes_model->hasCompletedPreTest($id_modul, $id_user)) {
			$this->session->set_flashdata('message', '<div class="alert alert-warning">Selesaikan Pre-Test terlebih dahulu.</div>');
			redirect('/frontend/modul_pembelajaran/tes/'.$id_modul.'/pre_test');
			return;
		}

		$result = $this->ModulTes_model->getResult($id_modul, $tipe, $id_user);
		if ($tipe === ModulTes_model::POST_TEST
			&& !$result
			&& !$this->ModulTes_model->hasCompletedLearning($id_modul, $id_user)) {
			$this->session->set_flashdata('message', '<div class="alert alert-warning">Post-Test tersedia setelah seluruh materi modul diselesaikan.</div>');
			redirect('/frontend/modul_pembelajaran/detail_modul/'.$id_modul.'/0/bab');
			return;
		}

		$data = array(
			'module' => $module,
			'test' => $this->ModulTes_model->getTest($id_modul, $tipe),
			'testType' => $tipe,
			'questions' => $this->ModulTes_model->getQuestions(),
			'result' => $result,
			'comparison' => $this->ModulTes_model->getComparison($id_modul, $id_user)
		);
		$this->loadFrontend('modul_pembelajaran/tes', $data);
	}

	public function simpanTes($id_modul, $tipe)
	{
		if (strtoupper($this->input->method(TRUE)) !== 'POST') {
			show_404();
			return;
		}

		$id_modul = (int) $id_modul;
		$id_user = (int) $this->session->userdata('id_user');
		$tipe = (string) $tipe;
		if (!$this->ModulTes_model->getModule($id_modul) || !$this->ModulTes_model->isValidType($tipe)) {
			show_404();
			return;
		}

		if ($tipe === ModulTes_model::POST_TEST
			&& !$this->ModulTes_model->hasCompletedLearning($id_modul, $id_user)) {
			$this->session->set_flashdata('message', '<div class="alert alert-warning">Post-Test belum dapat dikerjakan.</div>');
			redirect('/frontend/modul_pembelajaran/detail_modul/'.$id_modul.'/0/bab');
			return;
		}

		$answers = $this->input->post('jawaban');
		$result = $this->ModulTes_model->saveResult(
			$id_modul,
			$tipe,
			$id_user,
			is_array($answers) ? $answers : array()
		);

		if (!$result['success']) {
			$this->session->set_flashdata('message', '<div class="alert alert-danger">'.htmlspecialchars($result['message'], ENT_QUOTES, 'UTF-8').'</div>');
			redirect('/frontend/modul_pembelajaran/tes/'.$id_modul.'/'.$tipe);
			return;
		}

		$score = number_format((float) $result['result']['nilai'], 2, ',', '.');
		$this->session->set_flashdata('message', '<div class="alert alert-success">Jawaban tersimpan. Nilai Anda: <strong>'.$score.'</strong>.</div>');
		redirect('/frontend/modul_pembelajaran/tes/'.$id_modul.'/'.$tipe);
	}

	public function detail_modul($id_modul="",$id_sub_modul="",$jenisModul="")
	{
		$id_modul = (int) $id_modul;
		$id_sub_modul = (int) $id_sub_modul;
		$id_user = (int) $this->session->userdata('id_user');
		if (!$this->ModulTes_model->hasCompletedPreTest($id_modul, $id_user)) {
			redirect('/frontend/modul_pembelajaran/tes/'.$id_modul.'/pre_test');
			return;
		}

			$getDetailModul = $this->ModulPembelajaran_model->getDetailModul($id_modul);
		$listDetailModul = $this->ModulPembelajaran_model->listDetailModul($id_modul);
		$listSubBabModul = $this->ModulPembelajaran_model->subBabModul($id_sub_modul);
		$getProsesBljr = $this->ModulPembelajaran_model->getProsesBljr($id_modul,'get_list_detail');
		$createLinkListDM = $this->ModulPembelajaran_model->getProsesBljr($id_modul,'cek_mod');
		$createLinkListDMFinal = $this->ModulPembelajaran_model->getProsesBljr($id_modul,'cek_final_mod');
		$getSoal = $this->ModulPembelajaran_model->getSoal($id_sub_modul);
		$getNilai = $this->ModulPembelajaran_model->getNilai($id_modul,$id_sub_modul);
		//echo $getNilai;
		$prosesBelajarGetID = $this->ModulPembelajaran_model->prosesBelajarGetID($id_modul,$id_sub_modul);
		$getsDataLink = array(
			'id_modul' => $id_modul,
			'id_sub_modul' => $id_sub_modul
		);
		$data['getDetailModul']= $getDetailModul;
		$data['listDetailModul'] = $listDetailModul;
		$data['listSubBabModul'] = $listSubBabModul;
		$data['jenisModul'] = $jenisModul;
		$data['listModul'] = 'modul';
		$data['getidLISTModul'] = @$getProsesBljr[1]['list_detail_modul_id'];
		$data['createLinkListDM'] = $createLinkListDM;
		$data['getsDataLink'] = $getsDataLink;
		$data['createLinkListDMFinal'] = $createLinkListDMFinal;
		$data['getSoal'] = $getSoal;
			$data['getNilai'] = $getNilai;
			$data['prosesBelajarGetID'] = $prosesBelajarGetID;
			$data['testComparison'] = $this->ModulTes_model->getComparison($id_modul, $id_user);
			$data['canTakePostTest'] = $this->ModulTes_model->hasCompletedLearning($id_modul, $id_user);
			$this->loadFrontend('modul_pembelajaran/detail_modul',$data);
	}

	public function prosesBelajar($idModulPembelajaran = 0, $idDetailModul = 0, $idListDetailModul = 0, $idUser = 0, $sts = '')
	{
		$idModulPembelajaran = (int) $idModulPembelajaran;
		$idDetailModul = (int) $idDetailModul;
		$idListDetailModul = (int) $idListDetailModul;
		$idUser = (int) $this->session->userdata('id_user');
		if (!$this->ModulTes_model->getModule($idModulPembelajaran)
			|| !in_array($sts, array('mulai_bljr', 'prs_bljr', 'selsai_bljr'), TRUE)) {
			show_404();
			return;
		}

		if (!$this->ModulTes_model->hasCompletedPreTest($idModulPembelajaran, $idUser)) {
			redirect('/frontend/modul_pembelajaran/tes/'.$idModulPembelajaran.'/pre_test');
			return;
		}

		if ($sts == 'mulai_bljr') {
			$started = $this->ModulPembelajaran_model->inputProsesBljr($idModulPembelajaran, $idDetailModul, $idListDetailModul, $idUser, 'mulaiBljr');
			$nextMod = $this->ModulPembelajaran_model->getProsesBljr($idModulPembelajaran, 'next_mod');
			if ($started && $nextMod) {
				redirect('/frontend/modul_pembelajaran/detail_modul/'.$nextMod['modul_pembelajaran_id'].'/'.$nextMod['list_detail_modul_id'].'/sub_bab');
				return;
			}

			if ($this->ModulTes_model->hasCompletedLearning($idModulPembelajaran, $idUser)) {
				redirect('/frontend/modul_pembelajaran/tes/'.$idModulPembelajaran.'/post_test');
				return;
			}

			$this->session->set_flashdata('message', '<div class="alert alert-warning">Materi pembelajaran belum tersedia.</div>');
			redirect('/frontend/modul_pembelajaran/detail_modul/'.$idModulPembelajaran.'/0/bab');
		} elseif ($sts == 'prs_bljr') {
			$progressed = $this->ModulPembelajaran_model->inputProsesBljr($idModulPembelajaran, $idDetailModul, $idListDetailModul, $idUser, 'prosesBljr');
			if (!$progressed) {
				$this->session->set_flashdata('message', '<div class="alert alert-warning">Progres materi tidak ditemukan atau sudah selesai.</div>');
				redirect('/frontend/modul_pembelajaran/detail_modul/'.$idModulPembelajaran.'/'.$idListDetailModul.'/sub_bab');
				return;
			}

			$nextMod = $this->ModulPembelajaran_model->getProsesBljr($idModulPembelajaran, 'next_mod');
			if ($nextMod) {
				redirect('/frontend/modul_pembelajaran/detail_modul/'.$nextMod['modul_pembelajaran_id'].'/'.$nextMod['list_detail_modul_id'].'/sub_bab');
				return;
			}

			$this->session->set_flashdata('message', '<div class="alert alert-success">Seluruh materi selesai. Silakan kerjakan Post-Test.</div>');
			redirect('/frontend/modul_pembelajaran/tes/'.$idModulPembelajaran.'/post_test');
		} elseif ($sts == 'selsai_bljr') {
			$this->ModulPembelajaran_model->inputProsesBljr($idModulPembelajaran, $idDetailModul, $idListDetailModul, $idUser, 'selsai_bljr');
			$this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Materi selesai. Silakan kerjakan Post-Test untuk melihat hasil akhir.</div>');
			redirect('/frontend/modul_pembelajaran/tes/'.$idModulPembelajaran.'/post_test');
		}
	}

	public function prosesJawaban($modul_pembelajaran,$list_detail_modul,$prosesBelajar)
	{
		$getJawabanUser = $this->ModulPembelajaran_model->getJawabanUser($prosesBelajar,"row");
		if($getJawabanUser['jml'] == 0){
			$soalId = $this->input->post('soal_id');
			$pilihan_jawaban_id = $this->input->post('pilihan_jawaban_id');
			//$data = array();
			$i=0;
			foreach ($soalId as $key => $value) {
				$pecahData = explode('-', $pilihan_jawaban_id[$i]);
				$data[] = array(
	                'soal_id' => $value,
	                'pilihan_jawaban_id' => $pecahData[0], // Gunakan $key untuk mengakses elemen yang sesuai dari array id_kls_mhs
	                'jawaban_benar' => $pecahData[1],
	                'id_proses_belajar' => $prosesBelajar
	            );
				$i++;
			}
			$this->db->insert_batch('jawaban_user', $data);
		} else{
			$getJawabanUserAll = $this->ModulPembelajaran_model->getJawabanUser($prosesBelajar,"result");
			$soalId = $this->input->post('soal_id');
			$pilihan_jawaban_id = $this->input->post('pilihan_jawaban_id');
			//$data = array();
			$i=0;
			foreach ($soalId as $key => $value) {
				$pecahData = explode('-', $pilihan_jawaban_id[$i]);
				$data[] = array(
					'id' => $getJawabanUserAll[$i]['id'],
					'pilihan_jawaban_id' => $pecahData[0],
	                'jawaban_benar' => $pecahData[1]
	            );
				$i++;
			}
			$this->db->update_batch('jawaban_user', $data, 'id');
		}
		redirect('/frontend/modul_pembelajaran/detail_modul/'.$modul_pembelajaran.'/'.$list_detail_modul.'/sub_bab');
		
	}

}
