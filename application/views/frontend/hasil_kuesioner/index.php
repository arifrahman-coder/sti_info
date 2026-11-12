<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-7"><h1 class="m-0">Hasil Pengisian Kuesioner</h1></div>
        <div class="col-sm-5">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="<?= base_url('frontend/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item active">Hasil Pengisian</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <section class="content">
    <div class="container-fluid">
      <?php
        $escape = function ($value) {
          return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
        };
        $respondentType = !empty($submission['jenis_responden'])
          ? $submission['jenis_responden']
          : (!empty($submission['status_user_nama']) ? $submission['status_user_nama'] : '-');
        $respondentCode = !empty($submission['kode_responden'])
          ? $submission['kode_responden']
          : (!empty($submission['no_responden']) ? $submission['no_responden'] : '-');
        $answersByCategory = array();
        foreach ($answers as $answer) {
          $category = !empty($answer['kategori_nama']) ? $answer['kategori_nama'] : 'Kuesioner';
          if (!isset($answersByCategory[$category])) {
            $answersByCategory[$category] = array();
          }
          $answersByCategory[$category][] = $answer;
        }
      ?>

      <div class="alert alert-info">
        <i class="fas fa-info-circle mr-1"></i>
        Halaman ini menampilkan kembali jawaban yang telah Anda kirim dan bersifat hanya-baca.
      </div>

      <div class="card card-primary card-outline">
        <div class="card-header"><h3 class="card-title"><i class="fas fa-user-check mr-2"></i>Informasi Pengisian</h3></div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-6">
              <table class="table table-sm table-borderless mb-0">
                <tr><th style="width:170px">Nama/Inisial</th><td><?= $escape($submission['nama'] ?? '-') ?></td></tr>
                <tr><th>Status Akun</th><td><?= $escape($submission['status_user_nama'] ?? '-') ?></td></tr>
                <tr><th>Jenis Responden</th><td><?= $escape($respondentType) ?></td></tr>
              </table>
            </div>
            <div class="col-md-6">
              <table class="table table-sm table-borderless mb-0">
                <tr><th style="width:170px">Kode Responden</th><td><?= $escape($respondentCode) ?></td></tr>
                <tr><th>Tanggal Pengisian</th><td><?= !empty($submission['created_at']) ? date('d-m-Y H:i', strtotime($submission['created_at'])) : '-' ?></td></tr>
                <tr><th>Jumlah Jawaban</th><td><?= (int) ($submission['total_jawaban'] ?? count($answers)) ?></td></tr>
              </table>
            </div>
          </div>
        </div>
      </div>

      <?php foreach ($answersByCategory as $category => $categoryAnswers) { ?>
      <div class="card card-outline card-secondary">
        <div class="card-header">
          <h3 class="card-title"><i class="fas fa-clipboard-list mr-2"></i><?= $escape($category) ?></h3>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-striped table-hover mb-0">
              <thead>
                <tr><th style="width:110px">Kode</th><th>Pertanyaan</th><th style="width:35%">Jawaban Anda</th></tr>
              </thead>
              <tbody>
                <?php foreach ($categoryAnswers as $answer) { ?>
                <?php
                  $code = !empty($answer['kode']) ? $answer['kode'] : $answer['pertanyaan'];
                  $question = !empty($answer['item']) ? $answer['item'] : $answer['pertanyaan'];
                ?>
                <tr>
                  <td><span class="badge badge-light"><?= $escape($code) ?></span></td>
                  <td><?= $escape($question) ?></td>
                  <td><strong><?= $escape($answer['jawaban']) ?></strong></td>
                </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <?php } ?>

      <div class="mb-3">
        <a href="<?= base_url('frontend/dashboard') ?>" class="btn btn-secondary"><i class="fas fa-arrow-left mr-1"></i>Kembali ke Dashboard</a>
      </div>
    </div>
  </section>
</div>
