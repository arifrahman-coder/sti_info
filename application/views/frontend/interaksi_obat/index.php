<?php
$escape = function ($value) { return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8'); };
$levelClass = function ($level) {
  if (stripos(trim((string) $level), 'Mayor') === 0) return 'danger';
  if (stripos(trim((string) $level), 'Moderate') === 0) return 'warning';
  return 'info';
};
$selectedReferences = !empty($selected['referensi']) ? preg_split('/\s*;\s*/', $selected['referensi']) : array();
?>
<div class="content-wrapper">
  <div class="content-header"><div class="container-fluid"><div class="row mb-2">
    <div class="col-sm-7"><h1 class="m-0">Interaksi Obat PrEP</h1></div>
    <div class="col-sm-5"><ol class="breadcrumb float-sm-right"><li class="breadcrumb-item"><a href="<?= base_url('frontend/dashboard') ?>">Dashboard</a></li><li class="breadcrumb-item active">Interaksi Obat PrEP</li></ol></div>
  </div></div></div>

  <section class="content"><div class="container-fluid">
    <div class="card card-primary card-outline"><div class="card-body">
      <h2 class="h5">PrEP Tenofovir 300 mg + Lamivudin 300 mg</h2>
      <p class="mb-0">Periksa potensi interaksi obat penyerta, hormon, dan alkohol dengan masing-masing komponen PrEP: tenofovir disoproksil fumarat (TDF) dan lamivudin (3TC).</p>
    </div></div>

    <div class="card">
      <div class="card-header"><h2 class="card-title"><i class="fas fa-search mr-2" aria-hidden="true"></i>Cari Obat Penyerta</h2></div>
      <form method="GET" action="<?= base_url('frontend/interaksi-obat') ?>">
        <div class="card-body"><div class="form-group mb-0">
          <label for="interactionDrug">Pilih obat atau zat yang digunakan bersama PrEP</label>
          <select class="form-control" id="interactionDrug" name="obat" required>
            <option value="">-- Pilih obat atau zat penyerta --</option>
            <?php foreach ($interactions as $interaction) { ?>
              <option value="<?= (int) $interaction['id'] ?>" <?= !empty($selected) && (int) $selected['id'] === (int) $interaction['id'] ? 'selected' : '' ?>><?= $escape($interaction['nama_obat']) ?></option>
            <?php } ?>
          </select>
          <small class="form-text text-muted">Hasil memuat level interaksi TDF dan 3TC, mekanisme, serta rekomendasi.</small>
        </div></div>
        <div class="card-footer"><button type="submit" class="btn btn-primary"><i class="fas fa-search mr-1" aria-hidden="true"></i>Periksa Interaksi</button></div>
      </form>
    </div>

    <?php if (!empty($selected)) { ?>
      <div class="card card-outline card-primary" id="interactionResult" tabindex="-1">
        <div class="card-header"><h2 class="card-title"><i class="fas fa-capsules mr-2" aria-hidden="true"></i><?= $escape($selected['nama_obat']) ?></h2></div>
        <div class="card-body">
          <div class="row">
            <?php foreach (array('Tenofovir 300 mg (TDF)' => $selected['level_tdf'], 'Lamivudin 300 mg (3TC)' => $selected['level_3tc']) as $component => $level) { ?>
              <div class="col-md-6 mb-3"><h3 class="h5"><?= $escape($component) ?></h3><span class="badge badge-<?= $levelClass($level) ?> interaction-level"><?= $escape($level) ?></span></div>
            <?php } ?>
          </div>
          <h3 class="h5 mt-2">Mekanisme Interaksi</h3>
          <p class="interaction-summary"><?= nl2br($escape(str_replace('  ||  ', "\n\n", $selected['mekanisme']))) ?></p>
          <h3 class="h5">Solusi / Rekomendasi</h3>
          <p class="interaction-summary"><?= nl2br($escape(str_replace('  ||  ', "\n\n", $selected['rekomendasi']))) ?></p>
          <?php if (!empty($selected['catatan'])) { ?><h3 class="h5">Catatan</h3><p><?= nl2br($escape($selected['catatan'])) ?></p><?php } ?>
          <?php if (!empty($selectedReferences)) { ?>
            <h3 class="h5">Referensi</h3>
            <ul class="mb-0">
              <?php foreach ($selectedReferences as $code) { ?>
                <li><?php if (isset($references[$code])) { ?><a href="#reference-<?= $escape($code) ?>"><?= $escape($code) ?></a>: <?= $escape($references[$code]['judul']) ?><?php } else { ?><?= $escape($code) ?><?php } ?></li>
              <?php } ?>
            </ul>
          <?php } ?>
        </div>
      </div>
    <?php } elseif ($this->input->get('obat') !== NULL) { ?>
      <p class="alert alert-info" role="status">Data interaksi yang dipilih tidak tersedia. Silakan pilih obat dari daftar.</p>
    <?php } ?>

    <div class="card">
      <div class="card-header"><h2 class="card-title">Cara Membaca Level</h2></div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-4 mb-2"><span class="badge badge-danger">Mayor</span><p class="mb-0 mt-1">Interaksi/duplikasi dengan potensi konsekuensi serius atau kombinasi yang sebaiknya dihindari.</p></div>
          <div class="col-md-4 mb-2"><span class="badge badge-warning">Moderate</span><p class="mb-0 mt-1">Interaksi potensial yang memerlukan monitoring, penyesuaian, atau supervisi.</p></div>
          <div class="col-md-4 mb-2"><span class="badge badge-info">Minor</span><p class="mb-0 mt-1">Tidak ada interaksi bermakna yang diharapkan atau cukup observasi rutin.</p></div>
        </div>
        <p class="text-muted mb-0 mt-2"><small>Klasifikasi ini merupakan kategori skrining, bukan skala universal tervalidasi.</small></p>
      </div>
    </div>

    <div class="card">
      <div class="card-header"><h2 class="card-title">Daftar Interaksi Tersedia</h2></div>
      <div class="card-body">
        <div class="form-group"><label for="drugFilter">Filter nama obat atau zat</label><input type="search" class="form-control" id="drugFilter" placeholder="Ketik nama obat, hormon, atau alkohol..."></div>
        <div class="table-responsive"><table class="table table-bordered table-hover" id="interactionTable">
          <thead><tr><th>Obat / Zat Penyerta</th><th>Tenofovir 300 mg (TDF)</th><th>Lamivudin 300 mg (3TC)</th><th>Aksi</th></tr></thead>
          <tbody>
            <?php foreach ($interactions as $interaction) { ?>
              <tr data-drug="<?= $escape(mb_strtolower($interaction['nama_obat'])) ?>">
                <td><?= $escape($interaction['nama_obat']) ?></td>
                <td><span class="badge badge-<?= $levelClass($interaction['level_tdf']) ?>"><?= $escape($interaction['level_tdf']) ?></span></td>
                <td><span class="badge badge-<?= $levelClass($interaction['level_3tc']) ?>"><?= $escape($interaction['level_3tc']) ?></span></td>
                <td><a class="btn btn-sm btn-outline-primary" href="<?= base_url('frontend/interaksi-obat?obat='.(int) $interaction['id']) ?>" aria-label="<?= $escape('Lihat interaksi '.$interaction['nama_obat']) ?>">Lihat</a></td>
              </tr>
            <?php } ?>
          </tbody>
        </table></div>
        <p id="drugFilterEmpty" class="text-muted mb-0" role="status" hidden>Tidak ada obat atau zat yang sesuai.</p>
      </div>
    </div>

    <div class="card"><div class="card-body">
      <h2 class="h5">Catatan Penggunaan PrEP</h2>
      <p>PrEP hanya untuk individu HIV-negatif. TDF + lamivudin bukan regimen ART lengkap untuk mengobati HIV. Evaluasi status HIV, HBV, fungsi ginjal, dan seluruh obat yang digunakan. TDF 300 mg yang tercantum sebagai obat penyerta adalah duplikasi jika ditambahkan di luar komponen PrEP.</p>
      <p class="mb-0 text-muted"><small>Sumber: Ringkasan Interaksi Obat PrEP. Informasi merupakan media edukasi dan pendukung pelayanan kesehatan, bukan pengganti konsultasi atau keputusan pengobatan oleh tenaga kesehatan profesional.</small></p>
    </div></div>

    <details class="card" id="interactionReferences">
      <summary class="card-header">Daftar Referensi dan Catatan Interpretasi</summary>
      <div class="card-body"><dl class="mb-0">
        <?php foreach ($references as $code => $reference) { ?>
          <dt id="reference-<?= $escape($code) ?>"><?= $escape($code) ?></dt>
          <dd><a href="<?= $escape($reference['url']) ?>" target="_blank" rel="noopener noreferrer"><?= $escape($reference['judul']) ?></a><br><small><?= $escape($reference['kegunaan']) ?></small></dd>
        <?php } ?>
      </dl></div>
    </details>
  </div></section>
</div>

<style>
.interaction-level, #interactionTable .badge { white-space: normal; text-align: left; line-height: 1.5; padding: .45rem .65rem; }
.interaction-level { font-size: .95rem; }
.interaction-summary { line-height: 1.7; overflow-wrap: anywhere; }
#interactionTable th { min-width: 160px; }
#interactionTable th:last-child { min-width: 70px; }
</style>
<script>
document.addEventListener('DOMContentLoaded', function () {
  var filter = document.getElementById('drugFilter');
  filter.addEventListener('input', function () {
    var query = filter.value.trim().toLowerCase();
    var visible = 0;
    document.querySelectorAll('#interactionTable tbody tr').forEach(function (row) {
      var matches = (row.getAttribute('data-drug') || '').indexOf(query) !== -1;
      row.hidden = !matches;
      if (matches) visible++;
    });
    document.getElementById('drugFilterEmpty').hidden = visible > 0;
  });
  document.querySelectorAll('a[href^="#reference-"]').forEach(function (link) {
    link.addEventListener('click', function () {
      document.getElementById('interactionReferences').open = true;
    });
  });
  var result = document.getElementById('interactionResult');
  if (result) result.focus({preventScroll: true});
});
</script>
