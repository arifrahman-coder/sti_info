<?php
$field = function ($name, $default = '') use ($profile) {
    return array_key_exists($name, $profile) ? $profile[$name] : $default;
};
$esc = function ($value) {
    return htmlspecialchars((string) ($value === '' || $value === NULL ? '-' : $value), ENT_QUOTES, 'UTF-8');
};
$detail = function ($value, $extra) {
    if ($extra !== '' && $extra !== NULL) return ($value !== '' && $value !== NULL ? $value.' — ' : '').$extra;
    return $value;
};
$month = function ($value) {
    if (!$value || $value === '0000-00-00') return '-';
    $timestamp = strtotime($value);
    return $timestamp ? date('m-Y', $timestamp) : '-';
};

$sections = array(
    'Data Klinis & Diagnosis (Ringkas)' => array(
        'Kode / inisial pasien' => $field('kode_pasien'),
        'Status HIV' => $field('status_hiv'),
        'Stadium klinis WHO' => $field('stadium_who'),
        'Jumlah CD4 terakhir' => $field('kategori_cd4'),
        'Viral load terakhir' => $field('viral_load'),
        'Penyakit penyerta / komorbid' => $field('komorbid'),
        'Riwayat alergi obat' => $detail($field('alergi_obat'), $field('detail_alergi'))
    ),
    'Data Terapi Antiretroviral (ARV)' => array(
        'Status terapi ARV' => $field('status_terapi_arv'),
        'Lini pengobatan' => $field('lini_pengobatan'),
        'Nama regimen / paduan ARV' => $field('regimen_arv'),
        'Komponen obat ARV' => $field('komponen_arv'),
        'Bentuk sediaan' => $field('bentuk_sediaan_arv'),
        'Kekuatan / dosis' => $field('kekuatan_dosis_arv'),
        'Aturan pakai / frekuensi' => $detail($field('aturan_pakai_arv'), $field('aturan_pakai_arv_lainnya')),
        'Tanggal mulai ARV' => $month($field('tanggal_mulai_arv')),
        'Lama penggunaan ARV' => $field('lama_penggunaan_arv'),
        'Riwayat penggantian regimen' => $detail($field('riwayat_ganti_regimen'), $field('alasan_ganti_regimen')),
        'Sumber & tempat pengambilan obat' => $detail($field('sumber_obat'), $field('sumber_obat_lainnya')),
        'Frekuensi pengambilan obat' => $detail($field('frekuensi_pengambilan'), $field('frekuensi_pengambilan_lainnya')),
        'Penggunaan obat lainnya' => $field('penggunaan_obat_lainnya_arv'),
        'Penggunaan hormon' => $field('penggunaan_hormon_arv')
    ),
    'Data Terapi PrEP' => array(
        'Status terapi PrEP' => $field('status_terapi_prep'),
        'Lini pengobatan' => $field('lini_pengobatan_prep'),
        'Nama regimen / paduan ARV' => $field('regimen_prep'),
        'Komponen obat ARV' => $field('komponen_prep'),
        'Bentuk sediaan' => $field('bentuk_sediaan_prep'),
        'Kekuatan / dosis' => $field('kekuatan_dosis_prep'),
        'Aturan pakai / frekuensi' => $field('aturan_pakai_prep'),
        'Tanggal mulai PrEP' => $month($field('tanggal_mulai_prep')),
        'Lama penggunaan PrEP' => $field('lama_penggunaan_prep'),
        'Riwayat penggantian regimen' => $detail($field('riwayat_ganti_regimen_prep'), $field('alasan_ganti_regimen_prep')),
        'Sumber & tempat pengambilan obat' => $detail($field('sumber_obat_prep'), $field('sumber_obat_prep_lainnya')),
        'Frekuensi pengambilan obat' => $detail($field('frekuensi_pengambilan_prep'), $field('frekuensi_pengambilan_prep_lainnya')),
        'Penggunaan obat lainnya' => $field('penggunaan_obat_lainnya_prep'),
        'Penggunaan hormon' => $field('penggunaan_hormon_prep')
    )
);
?>
<div class="content-wrapper">
  <div class="content-header"><div class="container-fluid"><div class="row mb-2"><div class="col-sm-7"><h1 class="m-0">Detail Profil Penggunaan Obat</h1></div><div class="col-sm-5"><ol class="breadcrumb float-sm-right"><li class="breadcrumb-item"><a href="<?= base_url('backend/profil-penggunaan-obat') ?>">Profil Penggunaan Obat</a></li><li class="breadcrumb-item active">Detail</li></ol></div></div></div></div>
  <section class="content"><div class="container-fluid">
    <?php if ($this->session->flashdata('message')) echo $this->session->flashdata('message'); ?>
    <div class="card card-primary card-outline">
      <div class="card-header"><a href="<?= base_url('backend/profil-penggunaan-obat') ?>" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left mr-1"></i>Kembali</a> <a href="<?= base_url('backend/profil-penggunaan-obat/edit/'.(int)$profile['id']) ?>" class="btn btn-success btn-sm"><i class="fas fa-edit mr-1"></i>Edit</a> <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteProfile"><i class="fas fa-trash mr-1"></i>Hapus</button></div>
      <div class="card-body"><div class="row"><div class="col-md-6"><table class="table table-sm table-borderless"><tr><th style="width:150px">Nama akun</th><td><?= $esc($field('nama')) ?></td></tr><tr><th>Email</th><td><?= $esc($field('email')) ?></td></tr><tr><th>No. HP</th><td><?= $esc($field('no_hp')) ?></td></tr></table></div><div class="col-md-6"><table class="table table-sm table-borderless"><tr><th style="width:150px">Status akun</th><td><?= $esc($field('status_user_nama')) ?></td></tr><tr><th>Dibuat</th><td><?= $field('created_at') ? date('d-m-Y H:i', strtotime($field('created_at'))) : '-' ?></td></tr><tr><th>Diperbarui</th><td><?= $field('updated_at') ? date('d-m-Y H:i', strtotime($field('updated_at'))) : '-' ?></td></tr></table></div></div></div>
    </div>

    <?php foreach ($sections as $title => $rows) { ?>
      <div class="card profile-detail-card">
        <div class="card-header bg-light"><h3 class="card-title font-weight-bold"><?= $esc($title) ?></h3></div>
        <div class="card-body p-0"><div class="table-responsive"><table class="table table-striped mb-0 detail-table"><tbody>
          <?php foreach ($rows as $label => $content) { ?><tr><th><?= $esc($label) ?></th><td><?= $esc($content) ?></td></tr><?php } ?>
        </tbody></table></div></div>
      </div>
    <?php } ?>
  </div></section>
</div>

<div class="modal fade" id="deleteProfile" tabindex="-1" role="dialog" aria-hidden="true"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">Hapus Profil Penggunaan Obat</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div><form method="POST" action="<?= base_url('backend/profil-penggunaan-obat/delete/'.(int)$profile['id']) ?>"><div class="modal-body"><input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">Data profil penggunaan obat akan dihapus.</div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button><button type="submit" class="btn btn-danger">Hapus</button></div></form></div></div></div>
<style>.detail-table th{width:34%;min-width:230px;font-weight:600}.detail-table th,.detail-table td{vertical-align:top;padding:.7rem .9rem}.profile-detail-card{overflow:hidden}</style>
