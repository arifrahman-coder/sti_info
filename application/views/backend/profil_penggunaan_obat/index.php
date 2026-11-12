<div class="content-wrapper">
  <div class="content-header"><div class="container-fluid"><div class="row mb-2"><div class="col-sm-7"><h1 class="m-0">Profil Penggunaan Obat</h1></div><div class="col-sm-5"><ol class="breadcrumb float-sm-right"><li class="breadcrumb-item"><a href="<?= base_url('backend/dashboard') ?>">Dashboard</a></li><li class="breadcrumb-item active">Profil Penggunaan Obat</li></ol></div></div></div></div>
  <section class="content"><div class="container-fluid">
    <?php if ($this->session->flashdata('message')) echo $this->session->flashdata('message'); ?>
    <div class="row mb-3">
      <div class="col-md-4"><div class="small-box bg-info"><div class="inner"><h3><?= count($profiles) ?></h3><p>Profil tersimpan</p></div><div class="icon"><i class="fas fa-pills"></i></div></div></div>
      <div class="col-md-8"><div class="callout callout-info mb-0"><h5>Data terapi responden</h5><p class="mb-0">Gunakan halaman ini untuk melihat data klinis, regimen ARV, dan regimen PrEP sesuai lembar profil penggunaan obat terbaru.</p></div></div>
    </div>
    <div class="card card-primary card-outline">
      <div class="card-header"><h3 class="card-title">Daftar Profil Penggunaan Obat</h3></div>
      <div class="card-body"><div class="table-responsive"><table id="profileDrugTable" class="table table-bordered table-striped">
        <thead><tr><th>No</th><th>Pengguna</th><th>Kode pasien</th><th>Status HIV</th><th>Terapi ARV</th><th>Terapi PrEP</th><th>Diperbarui</th><th>Aksi</th></tr></thead>
        <tbody><?php $no=1; foreach ($profiles as $item) { ?><tr>
          <td><?= $no++ ?></td>
          <td><strong><?= htmlspecialchars($item['nama'], ENT_QUOTES, 'UTF-8') ?></strong><br><small class="text-muted"><?= htmlspecialchars($item['email'], ENT_QUOTES, 'UTF-8') ?></small><br><span class="badge badge-light"><?= htmlspecialchars($item['status_user_nama'] ?? '-', ENT_QUOTES, 'UTF-8') ?></span></td>
          <td><?= htmlspecialchars($item['kode_pasien'] ?: '-', ENT_QUOTES, 'UTF-8') ?></td>
          <td><span class="badge <?= ($item['status_hiv'] ?? '') === 'Positif' ? 'badge-danger' : 'badge-success' ?>"><?= htmlspecialchars($item['status_hiv'] ?: '-', ENT_QUOTES, 'UTF-8') ?></span></td>
          <td><?= htmlspecialchars($item['status_terapi_arv'] ?: '-', ENT_QUOTES, 'UTF-8') ?><br><small class="text-muted"><?= htmlspecialchars($item['regimen_arv'] ?: '-', ENT_QUOTES, 'UTF-8') ?></small></td>
          <td><?= htmlspecialchars($item['status_terapi_prep'] ?: '-', ENT_QUOTES, 'UTF-8') ?><br><small class="text-muted"><?= htmlspecialchars($item['regimen_prep'] ?: '-', ENT_QUOTES, 'UTF-8') ?></small></td>
          <td><?= date('d-m-Y H:i', strtotime($item['updated_at'])) ?></td>
          <td class="text-nowrap"><a href="<?= base_url('backend/profil-penggunaan-obat/detail/'.(int)$item['id']) ?>" class="btn btn-info btn-sm" title="Detail"><i class="fas fa-eye"></i></a> <a href="<?= base_url('backend/profil-penggunaan-obat/edit/'.(int)$item['id']) ?>" class="btn btn-success btn-sm" title="Edit"><i class="fas fa-edit"></i></a> <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteProfile<?= (int)$item['id'] ?>" title="Hapus"><i class="fas fa-trash"></i></button></td>
        </tr><?php } ?></tbody>
      </table></div></div>
    </div>
  </div></section>
</div>

<?php foreach ($profiles as $item) { ?><div class="modal fade" id="deleteProfile<?= (int)$item['id'] ?>" tabindex="-1" role="dialog" aria-hidden="true"><div class="modal-dialog"><div class="modal-content">
  <div class="modal-header"><h5 class="modal-title">Hapus Profil Penggunaan Obat</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div>
  <form method="POST" action="<?= base_url('backend/profil-penggunaan-obat/delete/'.(int)$item['id']) ?>"><div class="modal-body"><input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">Hapus profil milik <strong><?= htmlspecialchars($item['nama'], ENT_QUOTES, 'UTF-8') ?></strong> beserta daftar obatnya?</div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button><button type="submit" class="btn btn-danger">Hapus</button></div></form>
</div></div></div><?php } ?>

<script>document.addEventListener('DOMContentLoaded',function(){if(window.jQuery&&$.fn.DataTable){$('#profileDrugTable').DataTable({responsive:true,autoWidth:false,order:[[6,'desc']],columnDefs:[{targets:[7],orderable:false,searchable:false}],language:{url:'//cdn.datatables.net/plug-ins/1.10.25/i18n/Indonesian.json'}});}});</script>
