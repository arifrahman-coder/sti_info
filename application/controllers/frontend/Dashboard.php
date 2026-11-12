<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Kuesioner_model');

        if (!$this->session->userdata('id_user')) {
            redirect('/');
        }
    }

    public function index()
    {
        $idUser = (int) $this->session->userdata('id_user');
        $idStatus = $this->getEffectiveUserStatusId($idUser);
        $pendingCategories = $this->getPendingQuestionnaireCategories($idUser, $idStatus);

        $data = array(
            'cekUser' => array('jml' => empty($pendingCategories) ? 1 : 0),
            'kuesionerKategori' => $pendingCategories
        );

        $this->loadFrontend('dashboard/index', $data);
    }

    public function simpan()
    {
        if (strtoupper($this->input->method()) !== 'POST') {
            redirect('frontend/dashboard');
            return;
        }

        $idUser = (int) $this->session->userdata('id_user');
        $idStatus = $this->getEffectiveUserStatusId($idUser);
        $kategori = $this->getPendingQuestionnaireCategories($idUser, $idStatus);
        $jawabanPost = $this->input->post('jawaban');
        $jawabanLainnyaPost = $this->input->post('jawaban_lainnya');

        if (!is_array($jawabanPost)) {
            $jawabanPost = array();
        }
        if (!is_array($jawabanLainnyaPost)) {
            $jawabanLainnyaPost = array();
        }

        if (empty($kategori)) {
            return $this->redirectWithError('Kuesioner untuk status akun Anda belum tersedia.');
        }

        $kategoriAktif = $kategori[0];
        $dataDemografi = array(
            'id_user' => $idUser,
            'jenis_responden' => $kategoriAktif['nama'],
            'kode_responden' => '',
            'no_responden' => '',
            'usia' => 0,
            'gender' => '',
            'kelas' => '',
            'pendidikan_ayah' => '',
            'pendidikan_ibu' => '',
            'pekerjaan_ayah' => '',
            'pekerjaan_ibu' => '',
            'pernah_informasi_hiv' => '',
            'sumber_informasi_hiv' => '',
            'pekerjaan' => '',
            'pendidikan' => '',
            'pendapatan' => '',
            'pengalaman' => '',
            'created_at' => date('Y-m-d H:i:s')
        );

        $dataJawaban = array();
        foreach ($kategori as $itemKategori) {
            foreach ($itemKategori['pertanyaan'] as $pertanyaan) {
                $idPertanyaan = (int) $pertanyaan['id'];
                $tipe = isset($pertanyaan['tipe']) ? $pertanyaan['tipe'] : 'radio';
                $nilaiPost = isset($jawabanPost[$idPertanyaan]) ? $jawabanPost[$idPertanyaan] : '';

                if ($tipe === 'checkbox') {
                    $hasilValidasi = $this->validateCheckboxAnswer(
                        $nilaiPost,
                        $pertanyaan,
                        isset($jawabanLainnyaPost[$idPertanyaan]) ? $jawabanLainnyaPost[$idPertanyaan] : ''
                    );
                    if ($hasilValidasi === FALSE) {
                        return $this->redirectWithError('Jawaban kuesioner tidak valid.');
                    }
                    $jawaban = $hasilValidasi;
                } else {
                    $jawaban = is_scalar($nilaiPost) ? trim((string) $nilaiPost) : '';
                }

                if ((int) $pertanyaan['wajib'] === 1 && $jawaban === '') {
                    return $this->redirectWithError('Semua pertanyaan wajib diisi.');
                }

                if ($jawaban !== '' && !$this->isValidTypedValue($tipe, $jawaban)) {
                    return $this->redirectWithError('Format jawaban kuesioner tidak valid.');
                }

                if ($jawaban !== '' && !in_array($tipe, array('text', 'textarea', 'number', 'month', 'date', 'checkbox'), TRUE)) {
                    $opsiValid = array();
                    foreach ($pertanyaan['opsi'] as $opsi) {
                        $opsiValid[(string) $opsi['nilai']] = $opsi;
                    }

                    if (!isset($opsiValid[$jawaban])) {
                        return $this->redirectWithError('Jawaban kuesioner tidak valid.');
                    }

                    if ($this->isOtherOption($opsiValid[$jawaban])) {
                        $jawabanLainnya = isset($jawabanLainnyaPost[$idPertanyaan])
                            ? trim((string) $jawabanLainnyaPost[$idPertanyaan])
                            : '';

                        if ($jawabanLainnya === '') {
                            return $this->redirectWithError('Jawaban untuk pilihan Lainnya wajib ditulis.');
                        }
                        if (mb_strlen($jawabanLainnya) > 500) {
                            return $this->redirectWithError('Jawaban untuk pilihan Lainnya maksimal 500 karakter.');
                        }

                        $jawaban = (string) $opsiValid[$jawaban]['nilai'].': '.$jawabanLainnya;
                    }
                }

                if ($jawaban === '') {
                    continue;
                }

                if (stripos((string) $itemKategori['nama'], 'Responden ') === 0) {
                    $this->applyDemografiSummary($dataDemografi, $pertanyaan['pertanyaan'], $jawaban);
                }
                $dataJawaban[] = array(
                    'pertanyaan' => $pertanyaan['kode'],
                    'jawaban' => $jawaban
                );
            }
        }

        if (!$this->Kuesioner_model->save_kuesioner_data($dataDemografi, $dataJawaban)) {
            return $this->redirectWithError('Gagal menyimpan data kuesioner. Silakan coba lagi.');
        }

        $this->session->set_flashdata('success', 'Data kuesioner berhasil disimpan. Hasil pengisian dapat dilihat melalui menu Hasil Pengisian di sidebar.');
        redirect('frontend/dashboard');
    }

    private function getPendingQuestionnaireCategories($idUser, $idStatus)
    {
        $categories = $this->Kuesioner_model->getActiveCategoriesWithQuestions($idStatus);
        if (empty($categories)) {
            return array();
        }

        // MARS-5 memiliki halaman tersendiri pada menu frontend dan tidak lagi
        // digabungkan ke modal kuesioner onboarding.
        $categories = array_values(array_filter($categories, function ($category) {
            return stripos((string) $category['nama'], 'MARS-5') === FALSE;
        }));

        // Bagian C (Terapi PrEP) pada kuesioner onboarding hanya ditujukan
        // untuk responden dengan jenis akun Populasi Kunci.
        if (!$this->isKeyPopulationStatus($idStatus)) {
            $categories = array_values(array_filter($categories, function ($category) {
                return !$this->isPrepTherapyCategory($category);
            }));
        }

        if (empty($categories)) {
            return array();
        }

        $answeredRows = $this->db
            ->select('jawaban_kuesioner.pertanyaan')
            ->from('jawaban_kuesioner')
            ->join('demografi', 'demografi.id_demografi = jawaban_kuesioner.id_demografi', 'inner')
            ->where('demografi.id_user', (int) $idUser)
            ->get()
            ->result_array();
        $answeredCodes = array();
        foreach ($answeredRows as $row) {
            $answeredCodes[(string) $row['pertanyaan']] = TRUE;
        }

        return array_values(array_filter($categories, function ($category) use ($answeredCodes) {
            foreach ($category['pertanyaan'] as $question) {
                if ((int) $question['wajib'] === 1 && empty($answeredCodes[(string) $question['kode']])) {
                    return TRUE;
                }
            }
            return FALSE;
        }));
    }

    private function isKeyPopulationStatus($idStatus)
    {
        $status = $this->db
            ->select('nama')
            ->where('id', (int) $idStatus)
            ->get('status_user')
            ->row_array();

        return isset($status['nama'])
            && strcasecmp(trim((string) $status['nama']), 'Populasi Kunci') === 0;
    }

    private function isPrepTherapyCategory($category)
    {
        $name = isset($category['nama']) ? (string) $category['nama'] : '';
        $label = isset($category['label_pertanyaan']) ? (string) $category['label_pertanyaan'] : '';

        return stripos($name, 'Terapi PrEP') !== FALSE
            || stripos($label, 'Data Terapi PrEP') !== FALSE;
    }

    private function validateCheckboxAnswer($postedValue, $question, $otherValue)
    {
        $postedValues = is_array($postedValue) ? $postedValue : array();
        $validOptions = array();
        foreach ($question['opsi'] as $option) {
            $validOptions[(string) $option['nilai']] = $option;
        }

        $answers = array();
        foreach ($postedValues as $value) {
            if (!is_scalar($value)) {
                return FALSE;
            }
            $value = trim((string) $value);
            if ($value === '' || !isset($validOptions[$value])) {
                return FALSE;
            }
            if ($this->isOtherOption($validOptions[$value])) {
                $detail = is_scalar($otherValue) ? trim((string) $otherValue) : '';
                if ($detail === '' || mb_strlen($detail) > 500) {
                    return FALSE;
                }
                $value .= ': '.$detail;
            }
            $answers[$value] = $value;
        }

        if (isset($answers['Tidak ada']) && count($answers) > 1) {
            return FALSE;
        }

        return implode('; ', array_values($answers));
    }

    private function isValidTypedValue($type, $value)
    {
        if ($type === 'number') {
            return is_numeric($value);
        }
        if ($type === 'month') {
            return (bool) preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $value);
        }
        if ($type === 'date') {
            $date = DateTime::createFromFormat('Y-m-d', $value);
            return $date && $date->format('Y-m-d') === $value;
        }
        return TRUE;
    }

    private function getEffectiveUserStatusId($idUser)
    {
        $user = $this->db->select('id_status')->where('id', (int) $idUser)->get('user')->row_array();
        $idStatus = isset($user['id_status']) ? (int) $user['id_status'] : 0;

        if (in_array($idStatus, array(2, 3, 4), TRUE)) {
            return $this->getStatusIdByName('Tenaga Kesehatan', $idStatus);
        }

        if ($idStatus === 5) {
            return $this->getStatusIdByName('ODHIV', $idStatus);
        }

        return $idStatus;
    }

    private function getStatusIdByName($name, $fallback)
    {
        $status = $this->db->select('id')->where('nama', $name)->get('status_user')->row_array();
        return isset($status['id']) ? (int) $status['id'] : (int) $fallback;
    }

    private function applyDemografiSummary(&$dataDemografi, $pertanyaan, $jawaban)
    {
        $label = strtolower($pertanyaan);

        if (strpos($label, 'kode') !== FALSE || strpos($label, 'inisial') !== FALSE) {
            $dataDemografi['kode_responden'] = $jawaban;
            $dataDemografi['no_responden'] = $jawaban;
        } elseif (strpos($label, 'usia') !== FALSE) {
            $dataDemografi['usia'] = 0;
        } elseif (strpos($label, 'jenis kelamin') !== FALSE || strpos($label, 'identitas gender') !== FALSE) {
            $dataDemografi['gender'] = $jawaban;
        } elseif (strpos($label, 'pendidikan') !== FALSE) {
            $dataDemografi['pendidikan'] = $jawaban;
        } elseif (strpos($label, 'pekerjaan') !== FALSE || strpos($label, 'profesi') !== FALSE) {
            $dataDemografi['pekerjaan'] = $jawaban;
        } elseif (strpos($label, 'penghasilan') !== FALSE) {
            $dataDemografi['pendapatan'] = $jawaban;
        } elseif (strpos($label, 'pengalaman') !== FALSE) {
            $dataDemografi['pengalaman'] = $jawaban;
        }
    }

    private function isOtherOption($option)
    {
        $value = isset($option['nilai']) ? trim((string) $option['nilai']) : '';
        $label = isset($option['label']) ? trim((string) $option['label']) : '';

        return strcasecmp($value, 'Lainnya') === 0 || strcasecmp($label, 'Lainnya') === 0;
    }

    private function redirectWithError($message)
    {
        $this->session->set_flashdata('error', $message);
        redirect('frontend/dashboard');
    }
}
