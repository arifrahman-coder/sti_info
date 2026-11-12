  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Berita HIV/AIDS</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Berita HIV/AIDS</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <!-- Main content -->
    <div class="content">
      <div class="container-fluid">
        <?php if ($this->session->flashdata('message')) { ?>
          <?= $this->session->flashdata('message') ?>
        <?php } ?>

        <div class="row">
          <div class="col-lg-12">
            <div class="card card-primary card-outline">
              <div class="card-header">
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalTambahBerita">
                  <i class="fas fa-plus"></i> Tambah Berita
                </button>
              </div>
              <div class="card-body">
                <table id="beritaHivTable" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                    <th>No</th>
                    <th>Gambar</th>
                    <th>Judul</th>
                    <th>Ringkasan</th>
                    <th>Status</th>
                    <th>Urutan</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                  </tr>
                  </thead>
                  <tbody>
                  <?php
                  $i = 1;
                  foreach ($getBeritaHiv as $value) {
                    $title = htmlspecialchars($value['title'], ENT_QUOTES, 'UTF-8');
                    $ringkasan = htmlspecialchars($value['ringkasan'], ENT_QUOTES, 'UTF-8');
                    $field = htmlspecialchars($value['field'], ENT_QUOTES, 'UTF-8');
                    $linkSumber = htmlspecialchars($value['link_sumber'], ENT_QUOTES, 'UTF-8');
                    $gambarBerita = '';
                    if ($value['image'] != '' && file_exists(FCPATH.'assets/img/berita_hiv/'.$value['image'])) {
                      $gambarBerita = base_url('assets/img/berita_hiv/'.$value['image']);
                    } elseif ($value['image'] != '' && file_exists(FCPATH.'assets/img/'.$value['image'])) {
                      $gambarBerita = base_url('assets/img/'.$value['image']);
                    }
                  ?> 
                  <tr>
                    <td><?= $i ?></td>
                    <td>
                      <?php if ($gambarBerita != '') { ?>
                        <img src="<?= $gambarBerita ?>" alt="<?= $title ?>" style="width: 90px; height: 60px; object-fit: cover;">
                      <?php } else { ?>
                        <span class="text-muted">Tidak ada gambar</span>
                      <?php } ?>
                    </td>
                    <td><?= $title ?></td>
                    <td><?= $ringkasan ?></td>
                    <td>
                      <?php if ((int) $value['status'] == 1) { ?>
                        <span class="badge badge-success">Aktif</span>
                      <?php } else { ?>
                        <span class="badge badge-secondary">Nonaktif</span>
                      <?php } ?>
                    </td>
                    <td><?= (int) $value['urutan'] ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($value['date'])) ?></td>
                    <td>
                      <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#modalEditBerita<?= $value['id'] ?>">
                        <i class="fas fa-edit"></i> Edit
                      </button>
                      <button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#modalHapusBerita<?= $value['id'] ?>">
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
        </div>
      </div>
    </div>
	  </div>

<?php
foreach ($getBeritaHiv as $value) {
  $title = htmlspecialchars($value['title'], ENT_QUOTES, 'UTF-8');
  $ringkasan = htmlspecialchars($value['ringkasan'], ENT_QUOTES, 'UTF-8');
  $field = htmlspecialchars($value['field'], ENT_QUOTES, 'UTF-8');
  $linkSumber = htmlspecialchars($value['link_sumber'], ENT_QUOTES, 'UTF-8');
?>
<div class="modal fade" id="modalEditBerita<?= $value['id'] ?>">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Edit Berita HIV/AIDS</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <form method="POST" enctype="multipart/form-data" action="<?= base_url('backend/berita_hiv/proses_edit/'.$value['id']) ?>">
        <div class="modal-body">
          <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
          <div class="form-group">
            <label>Judul</label>
            <input type="text" class="form-control" name="title" value="<?= $title ?>" required>
          </div>
          <div class="form-group">
            <label>Ringkasan</label>
            <textarea class="form-control" name="ringkasan" rows="3" required><?= $ringkasan ?></textarea>
          </div>
          <div class="form-group">
            <label>Isi Berita</label>
            <textarea class="form-control" name="field" rows="6" required><?= $field ?></textarea>
          </div>
          <div class="form-group">
            <label>Gambar Berita</label>
            <input type="file" class="form-control" name="image" accept="image/png,image/jpeg,image/webp">
            <small class="form-text text-muted">Kosongkan jika tidak ingin mengganti gambar.</small>
          </div>
          <div class="form-group">
            <label>Link Sumber</label>
            <input type="url" class="form-control" name="link_sumber" value="<?= $linkSumber ?>" placeholder="https://contoh.com/berita">
          </div>
          <div class="form-row">
            <div class="form-group col-md-6">
              <label>Status</label>
              <select name="status" class="form-control">
                <option value="1" <?= (int) $value['status'] == 1 ? 'selected' : '' ?>>Aktif</option>
                <option value="0" <?= (int) $value['status'] == 0 ? 'selected' : '' ?>>Nonaktif</option>
              </select>
            </div>
            <div class="form-group col-md-6">
              <label>Urutan Slider</label>
              <input type="number" class="form-control" name="urutan" min="0" value="<?= (int) $value['urutan'] ?>">
            </div>
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

<div class="modal fade" id="modalHapusBerita<?= $value['id'] ?>">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Hapus Berita</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <form method="POST" action="<?= base_url('backend/berita_hiv/proses_hapus/'.$value['id']) ?>">
        <div class="modal-body">
          <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
          <p>Yakin ingin menghapus berita <strong><?= $title ?></strong>?</p>
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

<div class="modal fade" id="modalTambahBerita">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Tambah Berita HIV/AIDS</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <form method="POST" enctype="multipart/form-data" action="<?= base_url('backend/berita_hiv/proses_tambah') ?>">
        <div class="modal-body">
          <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
          <div class="form-group">
            <label>Judul</label>
            <input type="text" class="form-control" placeholder="Masukkan judul berita" name="title" required>
          </div>
          <div class="form-group">
            <label>Ringkasan</label>
            <textarea class="form-control" placeholder="Ringkasan singkat untuk slider" name="ringkasan" rows="3" required></textarea>
          </div>
          <div class="form-group">
            <label>Isi Berita</label>
            <textarea class="form-control" placeholder="Tulis isi berita lengkap" name="field" rows="6" required></textarea>
          </div>
          <div class="form-group">
            <label>Gambar Berita</label>
            <input type="file" class="form-control" name="image" accept="image/png,image/jpeg,image/webp">
          </div>
          <div class="form-group">
            <label>Link Sumber</label>
            <input type="url" class="form-control" name="link_sumber" placeholder="https://contoh.com/berita">
          </div>
          <div class="form-row">
            <div class="form-group col-md-6">
              <label>Status</label>
              <select name="status" class="form-control">
                <option value="1">Aktif</option>
                <option value="0">Nonaktif</option>
              </select>
            </div>
            <div class="form-group col-md-6">
              <label>Urutan Slider</label>
              <input type="number" class="form-control" name="urutan" min="0" value="0">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
          <button type="submit" class="btn btn-primary">Tambah Berita</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  $(function () {
    $("#beritaHivTable").DataTable({
      "responsive": true,
      "lengthChange": false,
      "autoWidth": false
    });
  });
</script>
