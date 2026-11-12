<?php $escape = function ($value) { return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8'); }; ?>
<div class="content-wrapper">
  <div class="content-header"><div class="container-fluid"><div class="row mb-2"><div class="col-sm-7"><h1 class="m-0"><?= $escape($formTitle) ?></h1></div><div class="col-sm-5"><ol class="breadcrumb float-sm-right"><li class="breadcrumb-item"><a href="<?= base_url('backend/kuesioner-mars') ?>">Kuisioner MARS</a></li><li class="breadcrumb-item active"><?= $escape($formTitle) ?></li></ol></div></div></div></div>
  <section class="content"><div class="container-fluid">
    <?php if ($this->session->flashdata('message')) echo $this->session->flashdata('message'); ?>
    <form method="POST" action="<?= $escape($formAction) ?>">
      <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
      <div class="card card-primary card-outline"><div class="card-body">
        <?php if ($isCreate) { ?>
          <div class="form-group"><label for="mars_user">Pengguna <span class="text-danger">*</span></label><select class="form-control" id="mars_user" name="id_user" required><option value="">Pilih pengguna</option><?php foreach ($users as $user) { ?><option value="<?= (int)$user['id'] ?>" <?= (int)$selectedUserId === (int)$user['id'] ? 'selected' : '' ?>><?= $escape($user['nama'].' — '.$user['email'].' — '.$user['status_user_nama']) ?></option><?php } ?></select><?php if (empty($users)) { ?><small class="form-text text-warning">Semua pengguna sudah memiliki pengisian MARS.</small><?php } ?></div>
        <?php } else { ?>
          <div><strong><?= $escape($submission['nama']) ?></strong><br><span class="text-muted"><?= $escape($submission['email']) ?> · <?= $escape($submission['status_user_nama']) ?></span></div>
        <?php } ?>
      </div></div>
      <?php $this->load->view('shared/mars_questions', array('questions' => $questions, 'answerMap' => $answerMap)); ?>
      <div class="card"><div class="card-footer"><a href="<?= base_url('backend/kuesioner-mars') ?>" class="btn btn-secondary">Batal</a><button type="submit" class="btn btn-primary float-right" <?= $isCreate && empty($users) ? 'disabled' : '' ?>><i class="fas fa-save mr-1"></i><?= $escape($submitLabel) ?></button></div></div>
    </form>
  </div></section>
</div>
