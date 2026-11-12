<?php
$escape = function ($value) { return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8'); };
$badge = function ($level) {
  $level = strtolower(trim((string) $level));
  return strpos($level, 'mayor') === 0 ? 'danger' : (strpos($level, 'moderate') === 0 ? 'warning' : 'info');
};
?>
<div class="content-wrapper">
  <div class="content-header"><div class="container-fluid"><div class="row mb-2"><div class="col-sm-7"><h1 class="m-0">Data Interaksi Obat</h1></div><div class="col-sm-5"><ol class="breadcrumb float-sm-right"><li class="breadcrumb-item">Backend</li><li class="breadcrumb-item active">Interaksi Obat</li></ol></div></div></div></div>
  <section class="content"><div class="container-fluid">
    <?php if ($this->session->flashdata('message')) echo $this->session->flashdata('message'); ?>
    <div class="card card-primary card-outline">
      <div class="card-header"><a href="<?= base_url('backend/interaksi-obat/tambah') ?>" class="btn btn-primary"><i class="fas fa-plus mr-1"></i>Tambah Data</a></div>
      <div class="card-body">
        <p>Interaksi obat penyerta dengan PrEP Tenofovir 300 mg + Lamivudin 300 mg. Sumber: Ringkasan Interaksi Obat PrEP.</p>
        <table id="interactionAdminTable" class="table table-bordered table-striped">
          <thead><tr><th>No</th><th>Obat / Zat Penyerta</th><th>Tenofovir 300 mg (TDF)</th><th>Lamivudin 300 mg (3TC)</th><th>Referensi</th><th>Status</th><th>Urutan</th><th>Aksi</th></tr></thead>
          <tbody>
          <?php foreach ($interactions as $interaction) { ?>
            <tr>
              <td><?= $escape($interaction['no_sumber']) ?></td>
              <td><strong><?= $escape($interaction['nama_obat']) ?></strong><br><small class="text-muted"><?= $escape(mb_substr($interaction['mekanisme'], 0, 110)) ?><?= mb_strlen($interaction['mekanisme']) > 110 ? '…' : '' ?></small></td>
              <td><span class="badge badge-<?= $badge($interaction['level_tdf']) ?>"><?= $escape($interaction['level_tdf']) ?></span></td>
              <td><span class="badge badge-<?= $badge($interaction['level_3tc']) ?>"><?= $escape($interaction['level_3tc']) ?></span></td>
              <td><?= $escape($interaction['referensi']) ?></td>
              <td><?= (int) $interaction['status'] === 1 ? '<span class="badge badge-success">Aktif</span>' : '<span class="badge badge-secondary">Nonaktif</span>' ?></td>
              <td><?= (int) $interaction['urutan'] ?></td>
              <td class="text-nowrap">
                <a href="<?= base_url('backend/interaksi-obat/edit/'.(int) $interaction['id']) ?>" class="btn btn-sm btn-success"><i class="fas fa-edit"></i></a>
                <button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#deleteInteraction<?= (int) $interaction['id'] ?>"><i class="fas fa-trash"></i></button>
              </td>
            </tr>
          <?php } ?>
          </tbody>
        </table>
      </div>
    </div>
  </div></section>
</div>

<?php foreach ($interactions as $interaction) { ?>
<div class="modal fade" id="deleteInteraction<?= (int) $interaction['id'] ?>" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Hapus Interaksi Obat</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div>
    <form method="POST" action="<?= base_url('backend/interaksi-obat/hapus/'.(int) $interaction['id']) ?>">
      <div class="modal-body"><input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>"><p>Hapus data <strong><?= $escape($interaction['nama_obat']) ?></strong>?</p></div>
      <div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button><button type="submit" class="btn btn-danger">Hapus</button></div>
    </form>
  </div></div>
</div>
<?php } ?>
<script>$(function(){ $('#interactionAdminTable').DataTable({responsive:true,autoWidth:false,pageLength:25}); });</script>
<style>#interactionAdminTable .badge { white-space: normal; text-align: left; line-height: 1.5; }</style>
