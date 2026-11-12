<?php
$profile = isset($profile) && is_array($profile) ? $profile : array();
$oldInput = isset($oldInput) && is_array($oldInput) ? $oldInput : array();
$esc = function ($value) { return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8'); };
$value = function ($name, $default = '') use ($oldInput, $profile) {
    if (array_key_exists($name, $oldInput) && !is_array($oldInput[$name])) return $oldInput[$name];
    if (array_key_exists($name, $profile)) return $profile[$name];
    return $default;
};
$selectedValues = function ($name) use ($oldInput, $profile) {
    if (isset($oldInput[$name]) && is_array($oldInput[$name])) return array_map('strval', $oldInput[$name]);
    if (!empty($profile[$name])) return array_map('trim', explode(',', $profile[$name]));
    return array();
};
$monthValue = function ($name) use ($value) {
    $stored = (string) $value($name);
    return preg_match('/^\d{4}-\d{2}/', $stored) ? substr($stored, 0, 7) : '';
};
$radioGroup = function ($name, array $options, $current, $required = FALSE) use ($esc) {
    foreach ($options as $option) { ?>
      <label class="profile-option">
        <input type="radio" name="<?= $esc($name) ?>" value="<?= $esc($option) ?>" <?= (string) $current === (string) $option ? 'checked' : '' ?> <?= $required ? 'required' : '' ?>>
        <span><?= $esc($option) ?></span>
      </label>
    <?php }
};
$checkboxGroup = function ($name, array $options, array $current) use ($esc) {
    foreach ($options as $option) { ?>
      <label class="profile-option">
        <input type="checkbox" name="<?= $esc($name) ?>[]" value="<?= $esc($option) ?>" <?= in_array($option, $current, TRUE) ? 'checked' : '' ?>>
        <span><?= $esc($option) ?></span>
      </label>
    <?php }
};

$therapySection = function (array $config) use ($esc, $value, $selectedValues, $monthValue, $radioGroup, $checkboxGroup) {
    $f = $config['fields'];
    $key = $config['key'];
    $active = $value($f['status']) !== '' && $value($f['status']) !== 'Belum memulai';
    ?>
    <div class="profile-section therapy-block card card-<?= $esc($config['color']) ?> card-outline" data-status-name="<?= $esc($f['status']) ?>">
      <div class="card-header"><h3 class="card-title"><span class="section-number bg-<?= $esc($config['color']) ?>"><?= (int) $config['number'] ?></span> <?= $esc($config['title']) ?></h3></div>
      <div class="card-body">
        <p class="text-muted mb-4"><?= $esc($config['description']) ?></p>
        <div class="form-group">
          <label><?= $esc($config['status_label']) ?> <span class="text-danger">*</span></label>
          <div class="profile-options stacked"><?php $radioGroup($f['status'], array('Sedang menjalani (on ART)', 'Pernah, kini putus obat', 'Belum memulai'), $value($f['status']), TRUE); ?></div>
        </div>

        <div class="therapy-details" <?= $active ? '' : 'style="display:none"' ?>>
          <div class="row">
            <div class="col-md-4 form-group">
              <label for="<?= $esc($key) ?>_lini">Lini pengobatan <span class="text-danger">*</span></label>
              <select class="form-control" id="<?= $esc($key) ?>_lini" name="<?= $esc($f['lini']) ?>" data-therapy-required>
                <option value="">Pilih lini</option>
                <?php foreach (array('Lini pertama', 'Lini kedua', 'Lini ketiga') as $option) { ?><option value="<?= $esc($option) ?>" <?= $value($f['lini']) === $option ? 'selected' : '' ?>><?= $esc($option) ?></option><?php } ?>
              </select>
            </div>
            <div class="col-md-4 form-group">
              <label for="<?= $esc($key) ?>_regimen">Nama regimen / paduan ARV <span class="text-danger">*</span></label>
              <select class="form-control" id="<?= $esc($key) ?>_regimen" name="<?= $esc($f['regimen']) ?>" data-therapy-required>
                <option value="">Pilih regimen</option>
                <?php foreach (array('TLD', 'TLE') as $option) { ?><option value="<?= $esc($option) ?>" <?= $value($f['regimen']) === $option ? 'selected' : '' ?>><?= $esc($option) ?></option><?php } ?>
              </select>
            </div>
            <div class="col-md-4 form-group">
              <label for="<?= $esc($key) ?>_komponen">Komponen obat ARV <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="<?= $esc($key) ?>_komponen" name="<?= $esc($f['komponen']) ?>" maxlength="500" value="<?= $esc($value($f['komponen'])) ?>" placeholder="NRTI + NRTI + (NNRTI / INSTI / PI)" data-therapy-required>
            </div>
            <div class="col-md-4 form-group">
              <label for="<?= $esc($key) ?>_bentuk">Bentuk sediaan <span class="text-danger">*</span></label>
              <select class="form-control" id="<?= $esc($key) ?>_bentuk" name="<?= $esc($f['bentuk']) ?>" data-therapy-required>
                <option value="">Pilih bentuk sediaan</option>
                <?php foreach (array('Kombinasi dosis tetap (KDT/FDC)', 'Obat lepasan (loose)', 'Kombinasi keduanya') as $option) { ?><option value="<?= $esc($option) ?>" <?= $value($f['bentuk']) === $option ? 'selected' : '' ?>><?= $esc($option) ?></option><?php } ?>
              </select>
            </div>
            <div class="col-md-4 form-group">
              <label for="<?= $esc($key) ?>_dosis">Kekuatan / dosis <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="<?= $esc($key) ?>_dosis" name="<?= $esc($f['dosis']) ?>" maxlength="255" value="<?= $esc($value($f['dosis'])) ?>" placeholder="Sebutkan kekuatan tiap obat (mg)" data-therapy-required>
            </div>
            <div class="col-md-4 form-group">
              <label for="<?= $esc($key) ?>_aturan">Aturan pakai / frekuensi <span class="text-danger">*</span></label>
              <select class="form-control conditional-select" id="<?= $esc($key) ?>_aturan" name="<?= $esc($f['aturan']) ?>" <?= !empty($f['aturan_lainnya']) ? 'data-detail-target="'.$esc($key).'_aturan_lainnya" data-detail-value="Lainnya"' : '' ?> data-therapy-required>
                <option value="">Pilih aturan pakai</option>
                <?php foreach ($config['aturan_options'] as $option) { ?><option value="<?= $esc($option) ?>" <?= $value($f['aturan']) === $option ? 'selected' : '' ?>><?= $esc($option) ?></option><?php } ?>
              </select>
              <?php if (!empty($f['aturan_lainnya'])) { ?>
                <div class="conditional-field mt-2" id="<?= $esc($key) ?>_aturan_lainnya" data-condition-name="<?= $esc($f['aturan']) ?>" data-condition-value="Lainnya">
                  <input type="text" class="form-control" name="<?= $esc($f['aturan_lainnya']) ?>" maxlength="150" value="<?= $esc($value($f['aturan_lainnya'])) ?>" placeholder="Tuliskan aturan pakai lainnya" data-conditional-required>
                </div>
              <?php } ?>
            </div>
            <div class="col-md-4 form-group">
              <label for="<?= $esc($key) ?>_tanggal"><?= $esc($config['tanggal_label']) ?> <span class="text-danger">*</span></label>
              <input type="month" class="form-control" id="<?= $esc($key) ?>_tanggal" name="<?= $esc($f['tanggal']) ?>" max="<?= date('Y-m') ?>" value="<?= $esc($monthValue($f['tanggal'])) ?>" data-therapy-required>
              <small class="form-text text-muted">Isi bulan dan tahun inisiasi terapi.</small>
            </div>
            <div class="col-md-4 form-group">
              <label for="<?= $esc($key) ?>_lama"><?= $esc($config['lama_label']) ?> <span class="text-danger">*</span></label>
              <select class="form-control" id="<?= $esc($key) ?>_lama" name="<?= $esc($f['lama']) ?>" data-therapy-required>
                <option value="">Pilih lama penggunaan</option>
                <?php foreach (array('< 6 bln', '6–12 bln', '1–3 thn', '> 3 thn') as $option) { ?><option value="<?= $esc($option) ?>" <?= $value($f['lama']) === $option ? 'selected' : '' ?>><?= $esc($option) ?></option><?php } ?>
              </select>
            </div>
            <div class="col-md-4 form-group">
              <label for="<?= $esc($key) ?>_ganti">Riwayat penggantian regimen <span class="text-danger">*</span></label>
              <select class="form-control conditional-select" id="<?= $esc($key) ?>_ganti" name="<?= $esc($f['ganti']) ?>" data-detail-target="<?= $esc($key) ?>_alasan_ganti" data-detail-value="Pernah" data-therapy-required>
                <option value="">Pilih riwayat</option>
                <?php foreach (array('Pernah', 'Tidak pernah') as $option) { ?><option value="<?= $esc($option) ?>" <?= $value($f['ganti']) === $option ? 'selected' : '' ?>><?= $esc($option) ?></option><?php } ?>
              </select>
              <div class="conditional-field mt-2" id="<?= $esc($key) ?>_alasan_ganti" data-condition-name="<?= $esc($f['ganti']) ?>" data-condition-value="Pernah">
                <label class="small font-weight-bold mb-2">Alasan penggantian <span class="text-danger">*</span></label>
                <div class="profile-options stacked reason-options"><?php $checkboxGroup($f['alasan'], array('Efek samping', 'Gagal terapi', 'Interaksi', 'Stok'), $selectedValues($f['alasan'])); ?></div>
              </div>
            </div>
            <div class="col-md-6 form-group">
              <label for="<?= $esc($key) ?>_sumber">Sumber &amp; tempat pengambilan obat <span class="text-danger">*</span></label>
              <select class="form-control conditional-select" id="<?= $esc($key) ?>_sumber" name="<?= $esc($f['sumber']) ?>" data-detail-target="<?= $esc($key) ?>_sumber_lainnya" data-detail-value="Lainnya" data-therapy-required>
                <option value="">Pilih sumber obat</option>
                <?php foreach (array('RS rujukan', 'Puskesmas PDP', 'Klinik/layanan swasta', 'Lainnya') as $option) { ?><option value="<?= $esc($option) ?>" <?= $value($f['sumber']) === $option ? 'selected' : '' ?>><?= $esc($option) ?></option><?php } ?>
              </select>
              <div class="conditional-field mt-2" id="<?= $esc($key) ?>_sumber_lainnya" data-condition-name="<?= $esc($f['sumber']) ?>" data-condition-value="Lainnya">
                <input type="text" class="form-control" name="<?= $esc($f['sumber_lainnya']) ?>" maxlength="150" value="<?= $esc($value($f['sumber_lainnya'])) ?>" placeholder="Tuliskan sumber lainnya" data-conditional-required>
              </div>
            </div>
            <div class="col-md-6 form-group">
              <label for="<?= $esc($key) ?>_frekuensi">Frekuensi pengambilan obat <span class="text-danger">*</span></label>
              <select class="form-control conditional-select" id="<?= $esc($key) ?>_frekuensi" name="<?= $esc($f['frekuensi']) ?>" data-detail-target="<?= $esc($key) ?>_frekuensi_lainnya" data-detail-value="Lainnya" data-therapy-required>
                <option value="">Pilih frekuensi pengambilan</option>
                <?php foreach (array('Bulanan', 'Multi-bulan (2–3 bln)', 'Lainnya') as $option) { ?><option value="<?= $esc($option) ?>" <?= $value($f['frekuensi']) === $option ? 'selected' : '' ?>><?= $esc($option) ?></option><?php } ?>
              </select>
              <div class="conditional-field mt-2" id="<?= $esc($key) ?>_frekuensi_lainnya" data-condition-name="<?= $esc($f['frekuensi']) ?>" data-condition-value="Lainnya">
                <input type="text" class="form-control" name="<?= $esc($f['frekuensi_lainnya']) ?>" maxlength="150" value="<?= $esc($value($f['frekuensi_lainnya'])) ?>" placeholder="Tuliskan frekuensi lainnya" data-conditional-required>
              </div>
            </div>
          </div>
        </div>

        <div class="row therapy-additional mt-2">
          <div class="col-md-6 form-group">
            <label for="<?= $esc($key) ?>_obat_lainnya">Penggunaan obat lainnya</label>
            <textarea class="form-control" id="<?= $esc($key) ?>_obat_lainnya" name="<?= $esc($f['obat_lainnya']) ?>" rows="3" maxlength="1000" placeholder="Sebutkan obat lain yang digunakan, jika ada"><?= $esc($value($f['obat_lainnya'])) ?></textarea>
          </div>
          <div class="col-md-6 form-group">
            <label for="<?= $esc($key) ?>_hormon">Penggunaan hormon</label>
            <textarea class="form-control" id="<?= $esc($key) ?>_hormon" name="<?= $esc($f['hormon']) ?>" rows="3" maxlength="1000" placeholder="Sebutkan hormon yang digunakan, jika ada"><?= $esc($value($f['hormon'])) ?></textarea>
          </div>
        </div>
      </div>
    </div>
    <?php
};
?>

<form method="POST" action="<?= $esc($formAction) ?>" id="profilPenggunaanObatForm" novalidate>
  <input type="hidden" name="<?= $esc($this->security->get_csrf_token_name()) ?>" value="<?= $esc($this->security->get_csrf_hash()) ?>">

  <div class="profile-section card card-primary card-outline">
    <div class="card-header"><h3 class="card-title"><span class="section-number">1</span> Data Klinis &amp; Diagnosis (Ringkas)</h3></div>
    <div class="card-body">
      <p class="text-muted mb-4">Data pendukung untuk interpretasi terapi dan penilaian keberhasilan pengobatan.</p>
      <div class="row">
        <div class="col-lg-6 form-group">
          <label for="kode_pasien">Kode / inisial pasien <span class="text-danger">*</span></label>
          <input type="text" class="form-control" id="kode_pasien" name="kode_pasien" maxlength="50" value="<?= $esc($value('kode_pasien')) ?>" placeholder="Contoh: ODHIV-001" required>
          <small class="form-text text-muted">Gunakan kode, bukan nama lengkap pasien.</small>
        </div>
        <div class="col-lg-6 form-group">
          <label>Status HIV <span class="text-danger">*</span></label>
          <div class="profile-options"><?php $radioGroup('status_hiv', array('Positif', 'Negatif'), $value('status_hiv'), TRUE); ?></div>
        </div>
        <div class="col-lg-6 form-group">
          <label>Stadium klinis WHO <span class="text-danger">*</span></label>
          <div class="profile-options stacked"><?php $radioGroup('stadium_who', array('Stadium I', 'Stadium II', 'Stadium III', 'Stadium IV'), $value('stadium_who'), TRUE); ?></div>
        </div>
        <div class="col-lg-6 form-group">
          <label>Jumlah CD4 terakhir <span class="text-danger">*</span></label>
          <div class="profile-options stacked"><?php $radioGroup('kategori_cd4', array('< 200 sel/mm³', '200–349 sel/mm³', '350–499 sel/mm³', '≥ 500 sel/mm³'), $value('kategori_cd4'), TRUE); ?></div>
        </div>
        <div class="col-lg-6 form-group">
          <label>Viral load terakhir <span class="text-danger">*</span></label>
          <div class="profile-options stacked"><?php $radioGroup('viral_load', array('Tidak terdeteksi (<50)', 'Terdeteksi rendah', 'Terdeteksi tinggi', 'Belum diperiksa'), $value('viral_load'), TRUE); ?></div>
        </div>
        <div class="col-lg-6 form-group">
          <label>Penyakit penyerta / komorbid <span class="text-danger">*</span></label>
          <div class="profile-options stacked exclusive-none" data-required-checkbox-group="komorbid"><?php $checkboxGroup('komorbid', array('TB', 'Hepatitis B', 'Hepatitis C', 'IMS lain', 'DM/HT', 'Tidak ada'), $selectedValues('komorbid')); ?></div>
        </div>
        <div class="col-12 form-group">
          <label>Riwayat alergi obat <span class="text-danger">*</span></label>
          <div class="profile-options"><?php $radioGroup('alergi_obat', array('Ada', 'Tidak ada'), $value('alergi_obat'), TRUE); ?></div>
          <div class="conditional-field mt-2" data-condition-name="alergi_obat" data-condition-value="Ada">
            <label for="detail_alergi">Sebutkan obat dan reaksi alerginya <span class="text-danger">*</span></label>
            <textarea class="form-control" id="detail_alergi" name="detail_alergi" rows="2" maxlength="500" data-conditional-required><?= $esc($value('detail_alergi')) ?></textarea>
          </div>
        </div>
      </div>
    </div>
  </div>

  <?php
  $therapySection(array(
      'key' => 'arv',
      'number' => 2,
      'color' => 'info',
      'title' => 'Data Terapi Antiretroviral (ARV)',
      'description' => 'Inti profil penggunaan obat; menggambarkan regimen dan pola terapi ARV pasien.',
      'status_label' => 'Status terapi ARV',
      'tanggal_label' => 'Tanggal mulai ARV',
      'lama_label' => 'Lama penggunaan ARV',
      'aturan_options' => array('1 x sehari', '2 x sehari', 'Lainnya'),
      'fields' => array(
          'status' => 'status_terapi_arv', 'lini' => 'lini_pengobatan', 'regimen' => 'regimen_arv',
          'komponen' => 'komponen_arv', 'bentuk' => 'bentuk_sediaan_arv', 'dosis' => 'kekuatan_dosis_arv',
          'aturan' => 'aturan_pakai_arv', 'aturan_lainnya' => 'aturan_pakai_arv_lainnya',
          'tanggal' => 'tanggal_mulai_arv', 'lama' => 'lama_penggunaan_arv', 'ganti' => 'riwayat_ganti_regimen',
          'alasan' => 'alasan_ganti_regimen', 'sumber' => 'sumber_obat', 'sumber_lainnya' => 'sumber_obat_lainnya',
          'frekuensi' => 'frekuensi_pengambilan', 'frekuensi_lainnya' => 'frekuensi_pengambilan_lainnya',
          'obat_lainnya' => 'penggunaan_obat_lainnya_arv', 'hormon' => 'penggunaan_hormon_arv'
      )
  ));

  $therapySection(array(
      'key' => 'prep',
      'number' => 3,
      'color' => 'success',
      'title' => 'Data Terapi PrEP',
      'description' => 'Menggambarkan regimen dan pola terapi PrEP pasien sesuai lembar profil terbaru.',
      'status_label' => 'Status terapi PrEP',
      'tanggal_label' => 'Tanggal mulai PrEP',
      'lama_label' => 'Lama penggunaan PrEP',
      'aturan_options' => array('Setiap hari', 'Event driven (2-1-1)'),
      'fields' => array(
          'status' => 'status_terapi_prep', 'lini' => 'lini_pengobatan_prep', 'regimen' => 'regimen_prep',
          'komponen' => 'komponen_prep', 'bentuk' => 'bentuk_sediaan_prep', 'dosis' => 'kekuatan_dosis_prep',
          'aturan' => 'aturan_pakai_prep', 'tanggal' => 'tanggal_mulai_prep', 'lama' => 'lama_penggunaan_prep',
          'ganti' => 'riwayat_ganti_regimen_prep', 'alasan' => 'alasan_ganti_regimen_prep',
          'sumber' => 'sumber_obat_prep', 'sumber_lainnya' => 'sumber_obat_prep_lainnya',
          'frekuensi' => 'frekuensi_pengambilan_prep', 'frekuensi_lainnya' => 'frekuensi_pengambilan_prep_lainnya',
          'obat_lainnya' => 'penggunaan_obat_lainnya_prep', 'hormon' => 'penggunaan_hormon_prep'
      )
  ));
  ?>

  <div class="sticky-submit card shadow-sm"><div class="card-body d-flex flex-column flex-md-row align-items-md-center justify-content-between">
    <div class="mb-3 mb-md-0"><strong>Pastikan seluruh data sudah sesuai kondisi terakhir pasien.</strong><br><small class="text-muted">Data dapat diperbarui kembali melalui menu ini.</small></div>
    <div><?php if (!empty($isAdmin)) { ?><a href="<?= base_url('backend/profil-penggunaan-obat') ?>" class="btn btn-secondary mr-2">Batal</a><?php } ?><button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-save mr-1"></i> <?= $esc($submitLabel) ?></button></div>
  </div></div>
</form>

<style>
.profile-section{border-radius:.55rem;overflow:hidden}.profile-section .card-header{background:#fff;padding:1rem 1.25rem}.profile-section .card-title{font-weight:700;font-size:1.08rem;display:flex;align-items:center}.section-number{display:inline-flex;width:32px;height:32px;border-radius:50%;background:#007bff;color:#fff;align-items:center;justify-content:center;margin-right:.7rem;flex:0 0 auto}.profile-options{display:flex;flex-wrap:wrap;gap:.55rem}.profile-options.stacked{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr))}.profile-option{margin:0;border:1px solid #d7dee7;border-radius:.4rem;padding:.65rem .8rem;display:flex;align-items:center;gap:.5rem;cursor:pointer;background:#fff;transition:.15s}.profile-option:hover{border-color:#80bdff;background:#f7fbff}.profile-option input{width:17px;height:17px;flex:0 0 auto}.profile-option.selected{border-color:#007bff;background:#edf6ff;color:#004a99}.conditional-field{display:none}.therapy-additional{border-top:1px solid #e6ebf1;padding-top:1rem}.sticky-submit{position:sticky;bottom:12px;z-index:20;border-top:3px solid #007bff}@media(max-width:767px){.profile-options,.profile-options.stacked{display:grid;grid-template-columns:1fr}.sticky-submit{position:static}}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
  var form = document.getElementById('profilPenggunaanObatForm');

  function refreshSelectedOptions() {
    form.querySelectorAll('.profile-option').forEach(function (label) {
      var input = label.querySelector('input');
      label.classList.toggle('selected', Boolean(input && input.checked));
    });
  }

  function refreshTherapies() {
    form.querySelectorAll('.therapy-block').forEach(function (block) {
      var statusName = block.getAttribute('data-status-name');
      var selected = form.querySelector('input[name="' + statusName + '"]:checked');
      var active = Boolean(selected && selected.value !== 'Belum memulai');
      var details = block.querySelector('.therapy-details');
      details.style.display = active ? '' : 'none';
      details.querySelectorAll('input, select, textarea').forEach(function (field) {
        field.disabled = !active;
        if (field.hasAttribute('data-therapy-required')) field.required = active;
      });
    });
  }

  function controlValue(name) {
    var checked = form.querySelector('[name="' + name + '"]:checked');
    if (checked) return checked.value;
    var control = form.querySelector('[name="' + name + '"]');
    return control ? control.value : '';
  }

  function refreshConditionalFields() {
    form.querySelectorAll('.conditional-field').forEach(function (wrapper) {
      var show = controlValue(wrapper.getAttribute('data-condition-name')) === wrapper.getAttribute('data-condition-value');
      var therapyHidden = wrapper.closest('.therapy-details') && wrapper.closest('.therapy-details').style.display === 'none';
      show = show && !therapyHidden;
      wrapper.style.display = show ? 'block' : 'none';
      wrapper.querySelectorAll('input, select, textarea').forEach(function (field) {
        field.disabled = !show;
        if (field.hasAttribute('data-conditional-required')) field.required = show;
      });
    });
  }

  form.querySelectorAll('.exclusive-none').forEach(function (group) {
    group.addEventListener('change', function (event) {
      if (!event.target.matches('input[type="checkbox"]')) return;
      var boxes = group.querySelectorAll('input[type="checkbox"]');
      if (event.target.value === 'Tidak ada' && event.target.checked) {
        boxes.forEach(function (box) { if (box !== event.target) box.checked = false; });
      } else if (event.target.checked) {
        boxes.forEach(function (box) { if (box.value === 'Tidak ada') box.checked = false; });
      }
    });
  });

  form.addEventListener('change', function () {
    refreshTherapies();
    refreshConditionalFields();
    refreshSelectedOptions();
  });

  form.addEventListener('submit', function (event) {
    var missingCheckboxGroup = Array.prototype.some.call(form.querySelectorAll('[data-required-checkbox-group]'), function (group) {
      return !group.querySelector('input[type="checkbox"]:checked');
    });
    var missingReason = Array.prototype.some.call(form.querySelectorAll('.reason-options'), function (group) {
      return group.offsetParent !== null && !group.querySelector('input[type="checkbox"]:checked');
    });
    if (missingCheckboxGroup || missingReason || !form.checkValidity()) {
      event.preventDefault();
      form.classList.add('was-validated');
      window.scrollTo({top: 0, behavior: 'smooth'});
      alert('Lengkapi seluruh isian wajib sebelum menyimpan profil.');
    }
  });

  refreshTherapies();
  refreshConditionalFields();
  refreshSelectedOptions();
});
</script>
