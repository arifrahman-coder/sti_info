<?php
$escape = function ($value) { return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8'); };
$value = function ($key, $default = '') use ($interaction) { return isset($interaction[$key]) ? $interaction[$key] : $default; };
?>
<div class="content-wrapper">
  <div class="content-header"><div class="container-fluid"><div class="row mb-2"><div class="col-sm-7"><h1 class="m-0"><?= $escape($formTitle) ?></h1></div><div class="col-sm-5"><ol class="breadcrumb float-sm-right"><li class="breadcrumb-item"><a href="<?= base_url('backend/interaksi-obat') ?>">Interaksi Obat</a></li><li class="breadcrumb-item active"><?= $escape($formTitle) ?></li></ol></div></div></div></div>
  <section class="content"><div class="container-fluid">
    <?php if ($this->session->flashdata('message')) echo $this->session->flashdata('message'); ?>
    <div class="card card-primary card-outline"><form method="POST" action="<?= $formAction ?>">
      <div class="card-body">
        <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
        <div class="row">
          <div class="form-group col-md-3"><label>No. Sumber</label><input type="text" class="form-control" name="no_sumber" maxlength="20" value="<?= $escape($value('no_sumber')) ?>"></div>
          <div class="form-group col-md-9"><label>Nama Obat Penyerta <span class="text-danger">*</span></label><input type="text" class="form-control" name="nama_obat" maxlength="200" value="<?= $escape($value('nama_obat')) ?>" required></div>
        </div>
        <div class="row">
          <div class="form-group col-md-6"><label for="levelTdf">Level Tenofovir 300 mg (TDF) <span class="text-danger">*</span></label><input id="levelTdf" type="text" class="form-control" name="level_tdf" list="interactionLevels" maxlength="200" value="<?= $escape($value('level_tdf')) ?>" required></div>
          <div class="form-group col-md-6"><label for="level3tc">Level Lamivudin 300 mg (3TC) <span class="text-danger">*</span></label><input id="level3tc" type="text" class="form-control" name="level_3tc" list="interactionLevels" maxlength="200" value="<?= $escape($value('level_3tc')) ?>" required></div>
        </div>
        <datalist id="interactionLevels"><?php foreach ($levels as $level) { ?><option value="<?= $escape($level) ?>"><?php } ?></datalist>
        <p class="text-muted"><small>Gunakan Mayor, Moderate, atau Minor. Keterangan dapat ditambahkan, misalnya: Moderate – perlu pemantauan.</small></p>
        <div class="form-group"><label for="mechanism">Mekanisme Interaksi <span class="text-danger">*</span></label><textarea id="mechanism" class="form-control" name="mekanisme" rows="5" required><?= $escape($value('mekanisme')) ?></textarea></div>
        <div class="form-group"><label for="recommendation">Solusi / Rekomendasi <span class="text-danger">*</span></label><textarea id="recommendation" class="form-control" name="rekomendasi" rows="5" required><?= $escape($value('rekomendasi')) ?></textarea></div>
        <div class="form-group"><label for="notes">Catatan</label><textarea id="notes" class="form-control" name="catatan" rows="3"><?= $escape($value('catatan')) ?></textarea></div>
        <div class="row">
          <div class="form-group col-md-4"><label>Referensi</label><input type="text" class="form-control" name="referensi" maxlength="100" placeholder="Contoh: S1;S2;S3" value="<?= $escape($value('referensi')) ?>"></div>
          <div class="form-group col-md-4"><label>Urutan</label><input type="number" class="form-control" name="urutan" min="0" value="<?= (int) $value('urutan', 0) ?>"></div>
          <div class="form-group col-md-4"><label>Status</label><select class="form-control" name="status"><option value="1" <?= (int) $value('status', 1) === 1 ? 'selected' : '' ?>>Aktif</option><option value="0" <?= (int) $value('status', 1) === 0 ? 'selected' : '' ?>>Nonaktif</option></select></div>
        </div>
      </div>
      <div class="card-footer"><a href="<?= base_url('backend/interaksi-obat') ?>" class="btn btn-secondary">Batal</a><button type="submit" class="btn btn-primary float-right"><i class="fas fa-save mr-1"></i>Simpan</button></div>
    </form></div>
  </div></section>
</div>
