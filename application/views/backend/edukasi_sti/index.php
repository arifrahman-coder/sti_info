
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Edukasi HIV AIDS</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Edukasi HIV AIDS</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
      <div class="container-fluid">
        <?php if ($this->session->flashdata('message')) { ?>
          <?= $this->session->flashdata('message') ?>
        <?php } ?>

        <div class="row">
          <!-- /.col-md-6 -->
          <div class="col-lg-12">
            <div class="card card-primary card-outline">
              <div class="card-header">
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalTambahVideo">
                  <i class="fas fa-plus"></i> Tambah Video
                </button>
              </div>
              <div class="card-body">
                <table id="edukasiHivAidsTable" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                    <th>No</th>
                    <th>Title Video</th>
                    <th>Link Video</th>
                    <th>Aksi</th>
                  </tr>
                  </thead>
                  <tbody>
                  <?php
                  $i = 1;
                  foreach ($getModulEdukasi as $value) {
                    $id = (int) $value['id'];
                    $title = htmlspecialchars($value['title'], ENT_QUOTES, 'UTF-8');
                    $link = htmlspecialchars($value['link'], ENT_QUOTES, 'UTF-8');
                  ?>
                  <tr>
                    <td><?= $i ?></td>
                    <td><?= $title ?></td>
                    <td>
                      <a target="_blank" href="https://www.youtube.com/watch?v=<?= rawurlencode($value['link']) ?>">link Video</a>
                    </td>
                    <td>
                      <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#modalEditVideo<?= $id ?>">
                        <i class="fas fa-edit"></i> Edit
                      </button>
                      <button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#modalHapusVideo<?= $id ?>">
                        <i class="fas fa-trash"></i> Hapus
                      </button>
                    </td>
                  </tr>
                  <?php
                    $i++;
                  }
                  ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          <!-- /.col-md-6 -->
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

<?php
foreach ($getModulEdukasi as $value) {
  $id = (int) $value['id'];
  $title = htmlspecialchars($value['title'], ENT_QUOTES, 'UTF-8');
  $link = htmlspecialchars($value['link'], ENT_QUOTES, 'UTF-8');
?>
<div class="modal fade" id="modalEditVideo<?= $id ?>">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Edit Video Edukasi HIV AIDS</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <form method="POST" action="<?= base_url('backend/edukasi_sti/proses_edit/'.$id) ?>">
        <div class="modal-body">
          <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
          <div class="form-group">
            <label>Title</label>
            <input type="text" class="form-control" name="title" value="<?= $title ?>" maxlength="50" required>
          </div>
          <div class="form-group">
            <label>Link Video</label>
            <input type="text" class="form-control" name="link_video" value="<?= $link ?>" required>
            <small class="form-text text-muted">Masukkan ID video atau URL YouTube lengkap.</small>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
          <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="modalHapusVideo<?= $id ?>">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Hapus Video</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <form method="POST" action="<?= base_url('backend/edukasi_sti/proses_hapus/'.$id) ?>">
        <div class="modal-body">
          <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
          <p>Yakin ingin menghapus video <strong><?= $title ?></strong>?</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-danger">Hapus</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php
}
?>

<div class="modal fade" id="modalTambahVideo">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Tambah Video Edukasi HIV AIDS</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <form method="POST" action="<?= base_url('backend/edukasi_sti/proses_tambah') ?>">
        <div class="modal-body">
          <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
          <div class="form-group">
            <label>Title</label>
            <input type="text" class="form-control" placeholder="Masukan Title" name="title" maxlength="50" required>
          </div>
          <div class="form-group">
            <label>Link Video</label>
            <input type="text" class="form-control" placeholder="Masukkan ID video atau URL YouTube" name="link_video" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
          <button type="submit" class="btn btn-primary">Tambah Link Video</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  $(function () {
    $("#edukasiHivAidsTable").DataTable({
      "responsive": true,
      "lengthChange": false,
      "autoWidth": false
    });
  });
</script>
