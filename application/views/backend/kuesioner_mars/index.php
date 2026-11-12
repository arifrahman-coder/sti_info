<?php $escape = function ($value) { return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8'); }; ?>
<div class="content-wrapper">
  <div class="content-header"><div class="container-fluid"><div class="row mb-2"><div class="col-sm-7"><h1 class="m-0">Kuisioner MARS</h1></div><div class="col-sm-5"><ol class="breadcrumb float-sm-right"><li class="breadcrumb-item"><a href="<?= base_url('backend/dashboard') ?>">Dashboard</a></li><li class="breadcrumb-item active">Kuisioner MARS</li></ol></div></div></div></div>
  <section class="content"><div class="container-fluid">
    <?php if ($this->session->flashdata('message')) echo $this->session->flashdata('message'); ?>
    <div class="row mb-3"><div class="col-md-4"><div class="small-box bg-primary"><div class="inner"><h3><?= count($submissions) ?></h3><p>Pengisian MARS-5</p></div><div class="icon"><i class="fas fa-clipboard-check"></i></div></div></div><div class="col-md-8"><div class="callout callout-info mb-0"><h5>Data pengisian pengguna</h5><p class="mb-0">Kelola jawaban MARS-5 yang telah diinputkan oleh pengguna dari seluruh jenis akun.</p></div></div></div>
    <div class="card card-primary card-outline">
      <div class="card-header"><h3 class="card-title">Daftar Pengisian</h3><div class="card-tools"><a href="<?= base_url('backend/kuesioner-mars/create') ?>" class="btn btn-primary btn-sm"><i class="fas fa-plus mr-1"></i>Tambah Pengisian</a></div></div>
      <div class="card-body"><div class="table-responsive"><table id="marsTable" class="table table-bordered table-striped">
        <thead><tr><th>No</th><th>Pengguna</th><th>M1</th><th>M2</th><th>M3</th><th>M4</th><th>M5</th><th>Skor</th><th>Aksi</th></tr></thead>
        <tbody><?php $no=1; foreach ($submissions as $item) { ?><tr>
          <td><?= $no++ ?></td>
          <td><strong><?= $escape($item['nama'] ?: '-') ?></strong><br><small class="text-muted"><?= $escape($item['email'] ?: '-') ?></small><br><span class="badge badge-light"><?= $escape($item['status_user_nama'] ?: '-') ?></span></td>
          <?php foreach (array('M1','M2','M3','M4','M5') as $code) { ?><td><?= $escape(isset($item['answers'][$code]) ? $item['answers'][$code] : '-') ?></td><?php } ?>
          <td><span class="badge badge-info"><?= (int) $item['score'] ?></span></td>
          <td class="text-nowrap"><a href="<?= base_url('backend/kuesioner-mars/detail/'.(int)$item['id_demografi']) ?>" class="btn btn-info btn-sm" title="Detail"><i class="fas fa-eye"></i></a> <a href="<?= base_url('backend/kuesioner-mars/edit/'.(int)$item['id_demografi']) ?>" class="btn btn-success btn-sm" title="Edit"><i class="fas fa-edit"></i></a> <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteMars<?= (int)$item['id_demografi'] ?>" title="Hapus"><i class="fas fa-trash"></i></button></td>
        </tr><?php } ?></tbody>
      </table></div></div>
    </div>
  </div></section>
</div>
<?php foreach ($submissions as $item) { ?><div class="modal fade" id="deleteMars<?= (int)$item['id_demografi'] ?>" tabindex="-1" role="dialog" aria-hidden="true"><div class="modal-dialog"><div class="modal-content">
  <div class="modal-header"><h5 class="modal-title">Hapus Pengisian MARS</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div>
  <form method="POST" action="<?= base_url('backend/kuesioner-mars/delete/'.(int)$item['id_demografi']) ?>"><div class="modal-body"><input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">Hapus seluruh jawaban MARS milik <strong><?= $escape($item['nama'] ?: '-') ?></strong>? Jawaban kuesioner lain tidak akan dihapus.</div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button><button type="submit" class="btn btn-danger">Hapus</button></div></form>
</div></div></div><?php } ?>
<script>document.addEventListener('DOMContentLoaded',function(){if(window.jQuery&&$.fn.DataTable){$('#marsTable').DataTable({responsive:true,autoWidth:false,order:[[0,'asc']],columnDefs:[{targets:[8],orderable:false,searchable:false}],language:{url:'//cdn.datatables.net/plug-ins/1.10.25/i18n/Indonesian.json'}});}});</script>
