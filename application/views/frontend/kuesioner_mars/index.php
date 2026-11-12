<div class="content-wrapper">
  <div class="content-header"><div class="container-fluid"><div class="row mb-2">
    <div class="col-sm-7"><h1 class="m-0">Kuisioner MARS</h1></div>
    <div class="col-sm-5"><ol class="breadcrumb float-sm-right"><li class="breadcrumb-item"><a href="<?= base_url('frontend/dashboard') ?>">Dashboard</a></li><li class="breadcrumb-item active">Kuisioner MARS</li></ol></div>
  </div></div></div>
  <section class="content"><div class="container-fluid">
    <?php if ($this->session->flashdata('message')) echo $this->session->flashdata('message'); ?>
    <div class="callout callout-info">
      <h5><i class="fas fa-clipboard-list mr-1"></i> Medication Adherence Rating Scale (MARS-5)</h5>
      <p class="mb-0"><?= nl2br(htmlspecialchars($category['deskripsi'], ENT_QUOTES, 'UTF-8')) ?></p>
    </div>
    <?php if (!empty($submission)) { ?>
      <div class="alert alert-success"><i class="fas fa-check-circle mr-1"></i>Anda sudah pernah mengisi kuisioner ini. Jawaban yang dikirim kembali akan memperbarui jawaban sebelumnya.</div>
    <?php } ?>
    <form method="POST" action="<?= base_url('frontend/kuesioner-mars/simpan') ?>" id="marsForm">
      <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
      <?php $this->load->view('shared/mars_questions', array('questions' => $questions, 'answerMap' => $answerMap)); ?>
      <div class="card card-primary"><div class="card-body d-flex justify-content-between align-items-center">
        <span>Pastikan kelima pernyataan sudah dijawab.</span>
        <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i><?= !empty($submission) ? 'Perbarui Jawaban' : 'Kirim Jawaban' ?></button>
      </div></div>
    </form>
  </div></section>
</div>
