<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Modul_pembelajaran extends CI_Controller
{
    private $imageUploadPath = './assets/img/modul_pembelajaran/';
    private $materialUploadPath = './assets/modul_pembelajaran/';

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Modul_pembelajaran_admin_model', 'moduleAdmin');

        if (!$this->session->userdata('id_admin')) {
            redirect('login_admin');
        }

        $legacyQuizMethods = array(
            'quiz',
            'createQuestion',
            'updateQuestion',
            'deleteQuestion',
            'createOption',
            'updateOption',
            'deleteOption'
        );
        if (in_array($this->router->fetch_method(), $legacyQuizMethods, TRUE)) {
            $this->session->set_flashdata(
                'message',
                '<div class="alert alert-warning">Soal manual sudah dinonaktifkan. Setiap modul otomatis memakai Pre-Test dan Post-Test.</div>'
            );
            redirect('backend/modul-pembelajaran');
        }
    }

    public function index()
    {
        $data = array(
            'modules' => $this->moduleAdmin->getModules(),
            'diseases' => $this->moduleAdmin->getDiseases()
        );
        $this->loadBackend('modul_pembelajaran/index', $data);
    }

    public function manage($moduleId)
    {
        $module = $this->moduleAdmin->getModule($moduleId);
        if (!$module) {
            return $this->redirectWithMessage('danger', 'Modul pembelajaran tidak ditemukan.');
        }

        $data = array(
            'module' => $module,
            'hierarchy' => $this->moduleAdmin->getModuleHierarchy($moduleId),
            'moduleTests' => $this->moduleAdmin->getModuleTests($moduleId),
            'participantResults' => $this->moduleAdmin->getModuleParticipantResults($moduleId),
            'testQuestionCount' => $this->db->where('aktif', 1)->count_all_results('bank_soal_tes_hiv')
        );
        $this->loadBackend('modul_pembelajaran/manage', $data);
    }

    public function quiz($contentId)
    {
        $content = $this->moduleAdmin->getContent($contentId);
        $target = $content ? 'manage/'.(int) $content['id_modul_pembelajaran'] : '';
        return $this->redirectWithMessage(
            'warning',
            'Soal tidak lagi dikelola manual. Setiap modul otomatis memakai 34 soal tetap untuk Pre-Test dan Post-Test.',
            $target
        );
    }

    public function createModule()
    {
        $this->requirePost();
        $name = trim((string) $this->input->post('nama', TRUE));
        $detail = trim((string) $this->input->post('detail'));
        $diseaseId = (int) $this->input->post('id_penyakit');

        if ($name === '' || $detail === '' || !$this->moduleAdmin->diseaseExists($diseaseId)) {
            return $this->redirectWithMessage('danger', 'Nama, deskripsi, dan penyakit modul wajib diisi dengan benar.');
        }

        $image = $this->uploadModuleImage();
        if ($image === FALSE) {
            return $this->redirectWithMessage('danger', $this->upload->display_errors('', ''));
        }

        $moduleId = $this->moduleAdmin->createModule(array(
            'id_penyakit' => $diseaseId,
            'nama' => $this->limitText($name, 100),
            'detail' => $detail,
            'img' => $image !== '' ? $image : 'not-found-image.png',
            'date' => date('Y-m-d H:i:s')
        ));

        if ($moduleId <= 0) {
            $this->deleteManagedImage($image);
            return $this->redirectWithMessage('danger', 'Gagal menambahkan modul pembelajaran.');
        }

        $this->redirectWithMessage(
            'success',
            'Modul pembelajaran berhasil ditambahkan. Pre-Test dan Post-Test dibuat otomatis.',
            'manage/'.$moduleId
        );
    }

    public function updateModule($moduleId)
    {
        $this->requirePost();
        $module = $this->moduleAdmin->getModule($moduleId);
        if (!$module) {
            return $this->redirectWithMessage('danger', 'Modul pembelajaran tidak ditemukan.');
        }

        $name = trim((string) $this->input->post('nama', TRUE));
        $detail = trim((string) $this->input->post('detail'));
        $diseaseId = (int) $this->input->post('id_penyakit');
        if ($name === '' || $detail === '' || !$this->moduleAdmin->diseaseExists($diseaseId)) {
            return $this->redirectWithMessage('danger', 'Nama, deskripsi, dan penyakit modul wajib diisi dengan benar.');
        }

        $image = $this->uploadModuleImage();
        if ($image === FALSE) {
            return $this->redirectWithMessage('danger', $this->upload->display_errors('', ''));
        }

        $updated = $this->moduleAdmin->updateModule($moduleId, array(
            'id_penyakit' => $diseaseId,
            'nama' => $this->limitText($name, 100),
            'detail' => $detail,
            'img' => $image !== '' ? $image : $module['img']
        ));

        if ($updated && $image !== '' && $image !== $module['img']) {
            $this->deleteManagedImage($module['img']);
        } elseif (!$updated && $image !== '') {
            $this->deleteManagedImage($image);
        }

        $this->redirectWithMessage(
            $updated ? 'success' : 'danger',
            $updated ? 'Modul pembelajaran berhasil diperbarui.' : 'Gagal memperbarui modul pembelajaran.'
        );
    }

    public function deleteModule($moduleId)
    {
        $this->requirePost();
        $module = $this->moduleAdmin->getModule($moduleId);
        if (!$module) {
            return $this->redirectWithMessage('danger', 'Modul pembelajaran tidak ditemukan.');
        }

        $materialFiles = $this->getManagedMaterialFiles($moduleId);
        $deleted = $this->moduleAdmin->deleteModule($moduleId);
        if ($deleted) {
            $this->deleteManagedImage($module['img']);
            foreach ($materialFiles as $materialFile) {
                $this->deleteManagedMaterial($materialFile);
            }
        }
        $this->redirectWithMessage(
            $deleted ? 'success' : 'danger',
            $deleted
                ? 'Modul beserta materi, hasil Pre-Test/Post-Test, dan progres terkait berhasil dihapus.'
                : 'Gagal menghapus modul pembelajaran.'
        );
    }

    public function createDetail()
    {
        $this->requirePost();
        $moduleId = (int) $this->input->post('id_modul_pembelajaran');
        $module = $this->moduleAdmin->getModule($moduleId);
        $title = trim((string) $this->input->post('title', TRUE));
        $field = trim((string) $this->input->post('field'));
        $type = (string) $this->input->post('jenis_modul');

        if (!$module || $title === '' || !in_array($type, array('pendahuluan', 'non_soal'), TRUE)) {
            return $this->redirectWithMessage('danger', 'Data bab modul tidak valid.', $module ? 'manage/'.$moduleId : '');
        }

        if ($type === 'pendahuluan' && $this->moduleAdmin->introductoryDetailExists($moduleId)) {
            return $this->redirectWithMessage('warning', 'Setiap modul hanya boleh memiliki satu bab pendahuluan.', 'manage/'.$moduleId);
        }

        $detailId = $this->moduleAdmin->createDetail(array(
            'id_modul_pembelajaran' => $moduleId,
            'title' => $this->limitText($title, 100),
            'field' => $field,
            'jenis_modul' => $type,
            'date' => date('Y-m-d H:i:s')
        ));

        $this->redirectWithMessage(
            $detailId ? 'success' : 'danger',
            $detailId ? 'Bab modul berhasil ditambahkan.' : 'Gagal menambahkan bab modul.',
            'manage/'.$moduleId
        );
    }

    public function updateDetail($detailId)
    {
        $this->requirePost();
        $detail = $this->moduleAdmin->getDetail($detailId);
        if (!$detail) {
            return $this->redirectWithMessage('danger', 'Bab modul tidak ditemukan.');
        }

        $moduleId = (int) $detail['id_modul_pembelajaran'];
        $title = trim((string) $this->input->post('title', TRUE));
        $field = trim((string) $this->input->post('field'));
        $type = (string) $this->input->post('jenis_modul');
        if ($title === '' || !in_array($type, array('pendahuluan', 'non_soal'), TRUE)) {
            return $this->redirectWithMessage('danger', 'Data bab modul tidak valid.', 'manage/'.$moduleId);
        }

        if ($type === 'pendahuluan' && $this->moduleAdmin->introductoryDetailExists($moduleId, $detailId)) {
            return $this->redirectWithMessage('warning', 'Setiap modul hanya boleh memiliki satu bab pendahuluan.', 'manage/'.$moduleId);
        }

        if ($type === 'pendahuluan' && $this->moduleAdmin->detailHasContents($detailId)) {
            return $this->redirectWithMessage('warning', 'Bab yang masih memiliki materi tidak dapat diubah menjadi pendahuluan.', 'manage/'.$moduleId);
        }

        $updated = $this->moduleAdmin->updateDetail($detailId, array(
            'title' => $this->limitText($title, 100),
            'field' => $field,
            'jenis_modul' => $type
        ));

        $this->redirectWithMessage(
            $updated ? 'success' : 'danger',
            $updated ? 'Bab modul berhasil diperbarui.' : 'Gagal memperbarui bab modul.',
            'manage/'.$moduleId
        );
    }

    public function deleteDetail($detailId)
    {
        $this->requirePost();
        $detail = $this->moduleAdmin->getDetail($detailId);
        if (!$detail) {
            return $this->redirectWithMessage('danger', 'Bab modul tidak ditemukan.');
        }

        $moduleId = (int) $detail['id_modul_pembelajaran'];
        $materialFiles = $this->getManagedMaterialFiles($moduleId, $detailId);
        $deleted = $this->moduleAdmin->deleteDetail($detailId);
        if ($deleted) {
            foreach ($materialFiles as $materialFile) {
                $this->deleteManagedMaterial($materialFile);
            }
        }
        $this->redirectWithMessage(
            $deleted ? 'success' : 'danger',
            $deleted ? 'Bab beserta seluruh materi dan soal terkait berhasil dihapus.' : 'Gagal menghapus bab modul.',
            'manage/'.$moduleId
        );
    }

    public function createContent()
    {
        $this->requirePost();
        $detailId = (int) $this->input->post('id_detail_modul');
        $detail = $this->moduleAdmin->getDetail($detailId);
        if (!$detail) {
            return $this->redirectWithMessage('danger', 'Bab modul tidak ditemukan.');
        }

        $moduleId = (int) $detail['id_modul_pembelajaran'];
        if ($detail['jenis_modul'] === 'pendahuluan') {
            return $this->redirectWithMessage('warning', 'Materi tidak dapat ditambahkan ke bab pendahuluan.', 'manage/'.$moduleId);
        }

        $title = trim((string) $this->input->post('title', TRUE));
        $status = (string) $this->input->post('status');
        if ($title === '' || !in_array($status, array('materi', 'final'), TRUE)) {
            return $this->redirectWithMessage('danger', 'Judul dan jenis materi wajib diisi dengan benar.', 'manage/'.$moduleId);
        }

        $field = $this->getMaterialField('');
        if ($field === FALSE) {
            return $this->redirectWithMessage('danger', $this->upload->display_errors('', ''), 'manage/'.$moduleId);
        }

        $contentId = $this->moduleAdmin->createContent(array(
            'id_detail_modul' => $detailId,
            'title' => $this->limitText($title, 100),
            'field' => $field,
            'status' => $status,
            'date' => date('Y-m-d H:i:s')
        ));

        if (!$contentId) {
            $this->deleteManagedMaterial($field);
        }

        $this->redirectWithMessage(
            $contentId ? 'success' : 'danger',
            $contentId ? 'Materi atau bagian modul berhasil ditambahkan.' : 'Gagal menambahkan materi modul.',
            'manage/'.$moduleId
        );
    }

    public function updateContent($contentId)
    {
        $this->requirePost();
        $content = $this->moduleAdmin->getContent($contentId);
        if (!$content) {
            return $this->redirectWithMessage('danger', 'Materi modul tidak ditemukan.');
        }

        $moduleId = (int) $content['id_modul_pembelajaran'];
        $title = trim((string) $this->input->post('title', TRUE));
        $status = (string) $this->input->post('status');
        if ($title === '' || !in_array($status, array('materi', 'final'), TRUE)) {
            return $this->redirectWithMessage('danger', 'Judul dan jenis materi wajib diisi dengan benar.', 'manage/'.$moduleId);
        }

        if ($content['status'] === 'soal' && $this->moduleAdmin->contentHasQuestions($contentId)) {
            return $this->redirectWithMessage('warning', 'Bagian kuis lama tidak dapat diubah karena masih memiliki soal.', 'manage/'.$moduleId);
        }

        $field = $this->getMaterialField($content['field']);
        if ($field === FALSE) {
            return $this->redirectWithMessage('danger', $this->upload->display_errors('', ''), 'manage/'.$moduleId);
        }

        $updated = $this->moduleAdmin->updateContent($contentId, array(
            'title' => $this->limitText($title, 100),
            'field' => $field,
            'status' => $status
        ));

        if ($updated && $field !== $content['field']) {
            $this->deleteManagedMaterial($content['field']);
        } elseif (!$updated && $field !== $content['field']) {
            $this->deleteManagedMaterial($field);
        }

        $this->redirectWithMessage(
            $updated ? 'success' : 'danger',
            $updated ? 'Materi modul berhasil diperbarui.' : 'Gagal memperbarui materi modul.',
            'manage/'.$moduleId
        );
    }

    public function deleteContent($contentId)
    {
        $this->requirePost();
        $content = $this->moduleAdmin->getContent($contentId);
        if (!$content) {
            return $this->redirectWithMessage('danger', 'Materi modul tidak ditemukan.');
        }

        $moduleId = (int) $content['id_modul_pembelajaran'];
        $deleted = $this->moduleAdmin->deleteContent($contentId);
        if ($deleted) {
            $this->deleteManagedMaterial($content['field']);
        }
        $this->redirectWithMessage(
            $deleted ? 'success' : 'danger',
            $deleted ? 'Materi beserta soal dan progres terkait berhasil dihapus.' : 'Gagal menghapus materi modul.',
            'manage/'.$moduleId
        );
    }

    public function createQuestion()
    {
        $this->requirePost();
        $contentId = (int) $this->input->post('list_detail_modul_id');
        $content = $this->moduleAdmin->getContent($contentId);
        if (!$content || $content['status'] !== 'soal') {
            return $this->redirectWithMessage('danger', 'Bagian soal tidak ditemukan.');
        }

        $question = trim((string) $this->input->post('pertanyaan', TRUE));
        $score = (int) $this->input->post('nilai');
        if ($question === '' || $score <= 0) {
            return $this->redirectWithMessage('danger', 'Pertanyaan dan nilai lebih dari nol wajib diisi.', 'quiz/'.$contentId);
        }

        $questionId = $this->moduleAdmin->createQuestion(array(
            'list_detail_modul_id' => $contentId,
            'pertanyaan' => $question,
            'nilai' => $score
        ));

        $this->redirectWithMessage(
            $questionId ? 'success' : 'danger',
            $questionId ? 'Soal berhasil ditambahkan. Silakan tambahkan pilihan jawabannya.' : 'Gagal menambahkan soal.',
            'quiz/'.$contentId
        );
    }

    public function updateQuestion($questionId)
    {
        $this->requirePost();
        $questionData = $this->moduleAdmin->getQuestion($questionId);
        if (!$questionData) {
            return $this->redirectWithMessage('danger', 'Soal tidak ditemukan.');
        }

        $question = trim((string) $this->input->post('pertanyaan', TRUE));
        $score = (int) $this->input->post('nilai');
        if ($question === '' || $score <= 0) {
            return $this->redirectWithMessage('danger', 'Pertanyaan dan nilai lebih dari nol wajib diisi.', 'quiz/'.$questionData['list_detail_modul_id']);
        }

        $updated = $this->moduleAdmin->updateQuestion($questionId, array(
            'pertanyaan' => $question,
            'nilai' => $score
        ));

        $this->redirectWithMessage(
            $updated ? 'success' : 'danger',
            $updated ? 'Soal berhasil diperbarui.' : 'Gagal memperbarui soal.',
            'quiz/'.$questionData['list_detail_modul_id']
        );
    }

    public function deleteQuestion($questionId)
    {
        $this->requirePost();
        $question = $this->moduleAdmin->getQuestion($questionId);
        if (!$question) {
            return $this->redirectWithMessage('danger', 'Soal tidak ditemukan.');
        }

        $deleted = $this->moduleAdmin->deleteQuestion($questionId);
        $this->redirectWithMessage(
            $deleted ? 'success' : 'danger',
            $deleted ? 'Soal dan seluruh pilihan jawabannya berhasil dihapus.' : 'Gagal menghapus soal.',
            'quiz/'.$question['list_detail_modul_id']
        );
    }

    public function createOption()
    {
        $this->requirePost();
        $questionId = (int) $this->input->post('soal_id');
        $question = $this->moduleAdmin->getQuestion($questionId);
        if (!$question) {
            return $this->redirectWithMessage('danger', 'Soal tidak ditemukan.');
        }

        $option = trim((string) $this->input->post('pilihan', TRUE));
        $isCorrect = $this->input->post('is_benar') === '1' ? 1 : 0;
        if ($option === '') {
            return $this->redirectWithMessage('danger', 'Teks pilihan jawaban wajib diisi.', 'quiz/'.$question['list_detail_modul_id']);
        }

        if ($isCorrect && $this->moduleAdmin->questionHasCorrectOption($questionId)) {
            return $this->redirectWithMessage('warning', 'Soal ini sudah memiliki jawaban benar. Ubah pilihan lama terlebih dahulu.', 'quiz/'.$question['list_detail_modul_id']);
        }

        $optionId = $this->moduleAdmin->createOption(array(
            'soal_id' => $questionId,
            'pilihan' => $this->limitText($option, 60),
            'is_benar' => $isCorrect
        ));

        $this->redirectWithMessage(
            $optionId ? 'success' : 'danger',
            $optionId ? 'Pilihan jawaban berhasil ditambahkan.' : 'Gagal menambahkan pilihan jawaban.',
            'quiz/'.$question['list_detail_modul_id']
        );
    }

    public function updateOption($optionId)
    {
        $this->requirePost();
        $optionData = $this->moduleAdmin->getOption($optionId);
        if (!$optionData) {
            return $this->redirectWithMessage('danger', 'Pilihan jawaban tidak ditemukan.');
        }

        $option = trim((string) $this->input->post('pilihan', TRUE));
        $isCorrect = $this->input->post('is_benar') === '1' ? 1 : 0;
        if ($option === '') {
            return $this->redirectWithMessage('danger', 'Teks pilihan jawaban wajib diisi.', 'quiz/'.$optionData['list_detail_modul_id']);
        }

        if ($isCorrect && $this->moduleAdmin->questionHasCorrectOption($optionData['soal_id'], $optionId)) {
            return $this->redirectWithMessage('warning', 'Soal ini sudah memiliki jawaban benar. Ubah pilihan lama terlebih dahulu.', 'quiz/'.$optionData['list_detail_modul_id']);
        }

        $updated = $this->moduleAdmin->updateOption($optionId, array(
            'pilihan' => $this->limitText($option, 60),
            'is_benar' => $isCorrect
        ));

        $this->redirectWithMessage(
            $updated ? 'success' : 'danger',
            $updated ? 'Pilihan jawaban berhasil diperbarui.' : 'Gagal memperbarui pilihan jawaban.',
            'quiz/'.$optionData['list_detail_modul_id']
        );
    }

    public function deleteOption($optionId)
    {
        $this->requirePost();
        $option = $this->moduleAdmin->getOption($optionId);
        if (!$option) {
            return $this->redirectWithMessage('danger', 'Pilihan jawaban tidak ditemukan.');
        }

        $deleted = $this->moduleAdmin->deleteOption($optionId);
        $this->redirectWithMessage(
            $deleted ? 'success' : 'danger',
            $deleted ? 'Pilihan jawaban berhasil dihapus.' : 'Gagal menghapus pilihan jawaban.',
            'quiz/'.$option['list_detail_modul_id']
        );
    }

    private function getMaterialField($existingField)
    {
        $uploadedFile = $this->uploadMaterialPdf();
        if ($uploadedFile === FALSE) {
            return FALSE;
        }

        if ($uploadedFile !== '') {
            return $uploadedFile;
        }

        $content = trim((string) $this->input->post('field'));
        return $content !== '' ? $content : $existingField;
    }

    private function uploadModuleImage()
    {
        if (empty($_FILES['image']['name'])) {
            return '';
        }

        $this->upload->initialize(array(
            'upload_path' => $this->imageUploadPath,
            'allowed_types' => 'jpg|jpeg|png|webp',
            'max_size' => 4096,
            'encrypt_name' => TRUE
        ));

        return $this->upload->do_upload('image')
            ? 'modul_pembelajaran/'.$this->upload->data('file_name')
            : FALSE;
    }

    private function uploadMaterialPdf()
    {
        if (empty($_FILES['material_file']['name'])) {
            return '';
        }

        $this->upload->initialize(array(
            'upload_path' => $this->materialUploadPath,
            'allowed_types' => 'pdf',
            'max_size' => 10240,
            'encrypt_name' => TRUE
        ));

        return $this->upload->do_upload('material_file')
            ? 'modul_pembelajaran/'.$this->upload->data('file_name')
            : FALSE;
    }

    private function getManagedMaterialFiles($moduleId, $detailId = NULL)
    {
        $files = array();
        foreach ($this->moduleAdmin->getModuleHierarchy($moduleId) as $detail) {
            if ($detailId !== NULL && (int) $detail['id'] !== (int) $detailId) {
                continue;
            }

            foreach ($detail['materi'] as $content) {
                if ($this->isManagedAsset($content['field'])) {
                    $files[$content['field']] = $content['field'];
                }
            }
        }

        return array_values($files);
    }

    private function deleteManagedImage($path)
    {
        $this->deleteManagedAsset($path, $this->imageUploadPath);
    }

    private function deleteManagedMaterial($path)
    {
        $this->deleteManagedAsset($path, $this->materialUploadPath);
    }

    private function deleteManagedAsset($path, $directory)
    {
        if (!$this->isManagedAsset($path)) {
            return;
        }

        $fileName = basename(str_replace('\\', '/', $path));
        if ($fileName === '' || $fileName === '.' || $fileName === '..') {
            return;
        }

        $filePath = rtrim($directory, '/\\').DIRECTORY_SEPARATOR.$fileName;
        if (is_file($filePath)) {
            unlink($filePath);
        }
    }

    private function isManagedAsset($path)
    {
        return strpos(str_replace('\\', '/', (string) $path), 'modul_pembelajaran/') === 0;
    }

    private function requirePost()
    {
        if (strtoupper($this->input->method()) !== 'POST') {
            show_404();
        }
    }

    private function limitText($value, $length)
    {
        return function_exists('mb_substr')
            ? mb_substr($value, 0, $length)
            : substr($value, 0, $length);
    }

    private function redirectWithMessage($type, $message, $path = '')
    {
        $this->session->set_flashdata(
            'message',
            '<div class="alert alert-'.$type.'" role="alert">'.htmlspecialchars($message, ENT_QUOTES, 'UTF-8').'</div>'
        );

        $url = 'backend/modul-pembelajaran';
        if ($path !== '') {
            $url .= '/'.$path;
        }
        redirect($url);
    }
}
