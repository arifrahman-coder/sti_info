<?php
$isPreTest = $testType === 'pre_test';
$testLabel = $isPreTest ? 'Pre-Test' : 'Post-Test';
$preResult = isset($comparison['pre_test']) ? $comparison['pre_test'] : array();
$postResult = isset($comparison['post_test']) ? $comparison['post_test'] : array();
$scoreDelta = ($preResult && $postResult)
    ? (float) $postResult['nilai'] - (float) $preResult['nilai']
    : NULL;
?>

<style>
  .learning-test-page .test-hero {
    border-radius: 14px;
    color: #fff;
    background: linear-gradient(135deg, #0f766e, #0369a1);
    box-shadow: 0 14px 30px rgba(15, 118, 110, .18);
  }
  .learning-test-page .test-question {
    border: 1px solid #dbe4ee;
    border-radius: 12px;
    box-shadow: 0 5px 15px rgba(15, 23, 42, .05);
  }
  .learning-test-page .question-number {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 42px;
    height: 42px;
    flex: 0 0 42px;
    border-radius: 10px;
    color: #fff;
    background: #0f766e;
    font-weight: 800;
  }
  .learning-test-page .answer-choice {
    display: block;
    height: 100%;
    margin: 0;
    padding: 14px 16px;
    border: 1px solid #cbd5e1;
    border-radius: 10px;
    cursor: pointer;
    transition: border-color .15s ease, background-color .15s ease;
  }
  .learning-test-page .answer-choice:hover {
    border-color: #0f766e;
    background: #f0fdfa;
  }
  .learning-test-page .answer-choice input { margin-right: 9px; }
  .learning-test-page .score-circle {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 150px;
    height: 150px;
    border: 10px solid #ccfbf1;
    border-radius: 50%;
    color: #0f766e;
    background: #fff;
    font-size: 35px;
    font-weight: 800;
  }
  .learning-test-page .result-stat {
    height: 100%;
    padding: 18px;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    background: #f8fafc;
  }
</style>

<div class="content-wrapper learning-test-page">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-7">
          <h1 class="m-0"><?= htmlspecialchars($testLabel, ENT_QUOTES, 'UTF-8') ?> Modul Pembelajaran</h1>
        </div>
        <div class="col-sm-5">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="<?= base_url('frontend/modul_pembelajaran/index/'.(int) $module['id_penyakit']) ?>">Modul</a></li>
            <li class="breadcrumb-item active"><?= htmlspecialchars($testLabel, ENT_QUOTES, 'UTF-8') ?></li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <section class="content pb-4">
    <div class="container-fluid">
      <?php if ($this->session->flashdata('message')) { ?>
        <?= $this->session->flashdata('message') ?>
      <?php } ?>

      <div class="test-hero p-4 p-md-5 mb-4">
        <span class="badge badge-light text-info mb-3"><?= htmlspecialchars($testLabel, ENT_QUOTES, 'UTF-8') ?></span>
        <h2 class="mb-2"><?= htmlspecialchars($module['nama'], ENT_QUOTES, 'UTF-8') ?></h2>
        <p class="mb-0">
          <?= $isPreTest
              ? 'Ukur pengetahuan awal Anda sebelum mempelajari materi.'
              : 'Ukur kembali pengetahuan Anda setelah menyelesaikan seluruh materi.' ?>
          Terdapat <?= count($questions) ?> pernyataan dengan pilihan Benar atau Salah.
        </p>
      </div>

      <?php if ($result) { ?>
        <div class="card card-success card-outline">
          <div class="card-header"><h3 class="card-title">Hasil Nilai <?= htmlspecialchars($testLabel, ENT_QUOTES, 'UTF-8') ?></h3></div>
          <div class="card-body text-center p-4 p-md-5">
            <div class="score-circle mb-4"><?= number_format((float) $result['nilai'], 2, ',', '.') ?></div>
            <h3 class="mb-4">Tes telah selesai</h3>
            <div class="row justify-content-center mb-4">
              <div class="col-sm-4 col-lg-3 mb-3">
                <div class="result-stat">
                  <div class="text-muted">Jawaban Benar</div>
                  <div class="h3 text-success mb-0"><?= (int) $result['jumlah_benar'] ?></div>
                </div>
              </div>
              <div class="col-sm-4 col-lg-3 mb-3">
                <div class="result-stat">
                  <div class="text-muted">Jawaban Salah</div>
                  <div class="h3 text-danger mb-0"><?= (int) $result['jumlah_salah'] ?></div>
                </div>
              </div>
              <div class="col-sm-4 col-lg-3 mb-3">
                <div class="result-stat">
                  <div class="text-muted">Jumlah Soal</div>
                  <div class="h3 mb-0"><?= (int) $result['jumlah_soal'] ?></div>
                </div>
              </div>
            </div>

            <?php if (!$isPreTest && $preResult) { ?>
              <div class="alert alert-info text-left mx-auto" style="max-width: 720px;">
                <div class="d-flex flex-wrap justify-content-between">
                  <span>Nilai Pre-Test: <strong><?= number_format((float) $preResult['nilai'], 2, ',', '.') ?></strong></span>
                  <span>Nilai Post-Test: <strong><?= number_format((float) $postResult['nilai'], 2, ',', '.') ?></strong></span>
                  <span>Perubahan: <strong><?= $scoreDelta >= 0 ? '+' : '' ?><?= number_format($scoreDelta, 2, ',', '.') ?></strong></span>
                </div>
              </div>
            <?php } ?>

            <?php if ($isPreTest) { ?>
              <a href="<?= base_url('frontend/modul_pembelajaran/detail_modul/'.(int) $module['id'].'/0/bab') ?>" class="btn btn-primary btn-lg">
                Lanjut ke Materi <i class="fas fa-arrow-right ml-1"></i>
              </a>
            <?php } else { ?>
              <a href="<?= base_url('frontend/modul_pembelajaran/index/'.(int) $module['id_penyakit']) ?>" class="btn btn-primary btn-lg">
                Kembali ke Daftar Modul
              </a>
            <?php } ?>
          </div>
        </div>
      <?php } else { ?>
        <div class="alert alert-light border">
          <strong>Petunjuk:</strong> pilih satu jawaban untuk setiap pernyataan. Jawaban tidak dapat diubah setelah dikirim.
        </div>

        <form method="POST" action="<?= base_url('frontend/modul-pembelajaran/tes/'.(int) $module['id'].'/'.$testType.'/simpan') ?>" id="learningTestForm">
          <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
          <?php foreach ($questions as $question) { ?>
            <?php $questionId = (int) $question['id']; ?>
            <div class="card test-question mb-3">
              <div class="card-body p-3 p-md-4">
                <div class="d-flex align-items-start mb-3">
                  <span class="question-number mr-3"><?= (int) $question['nomor'] ?></span>
                  <div>
                    <span class="badge badge-light border mb-2"><?= htmlspecialchars($question['kategori'], ENT_QUOTES, 'UTF-8') ?></span>
                    <p class="h5 mb-0"><?= htmlspecialchars($question['pertanyaan'], ENT_QUOTES, 'UTF-8') ?></p>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6 mb-2 mb-md-0">
                    <label class="answer-choice">
                      <input type="radio" name="jawaban[<?= $questionId ?>]" value="B" required>
                      <strong>Benar</strong>
                    </label>
                  </div>
                  <div class="col-md-6">
                    <label class="answer-choice">
                      <input type="radio" name="jawaban[<?= $questionId ?>]" value="S" required>
                      <strong>Salah</strong>
                    </label>
                  </div>
                </div>
              </div>
            </div>
          <?php } ?>

          <div class="card card-primary card-outline">
            <div class="card-body d-flex flex-column flex-md-row align-items-md-center justify-content-between">
              <div class="mb-3 mb-md-0">
                <strong>Pastikan semua <?= count($questions) ?> pertanyaan sudah dijawab.</strong>
                <div class="text-muted small">Nilai dihitung otomatis dalam skala 0–100.</div>
              </div>
              <button type="submit" class="btn btn-primary btn-lg" id="submitLearningTest">
                <i class="fas fa-paper-plane mr-1"></i> Kirim Jawaban
              </button>
            </div>
          </div>
        </form>
      <?php } ?>
    </div>
  </section>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  var form = document.getElementById('learningTestForm');
  if (!form) return;
  form.addEventListener('submit', function () {
    var button = document.getElementById('submitLearningTest');
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...';
  });
});
</script>
