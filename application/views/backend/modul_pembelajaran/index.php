<?php
$encodeAssetPath = function ($path) {
    $safeSegments = array();
    foreach (explode('/', str_replace('\\', '/', (string) $path)) as $segment) {
        if ($segment !== '' && $segment !== '.' && $segment !== '..') {
            $safeSegments[] = rawurlencode($segment);
        }
    }
    return implode('/', $safeSegments);
};
?>
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6"><h1 class="m-0">Modul Pembelajaran</h1></div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="<?= base_url('backend/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item active">Modul Pembelajaran</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <section class="content">
    <div class="container-fluid">
      <?php if ($this->session->flashdata('message')) { ?>
        <?= $this->session->flashdata('message') ?>
      <?php } ?>

      <div class="card card-primary card-outline">
        <div class="card-header">
          <h3 class="card-title">Daftar Modul dan Struktur Konten</h3>
          <div class="card-tools">
            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#createModuleModal">
              <i class="fas fa-plus mr-1"></i> Tambah Modul
            </button>
          </div>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table id="learningModuleTable" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Modul</th>
                  <th>Penyakit</th>
                  <th>Struktur</th>
                  <th>Peserta</th>
                  <th>Tanggal</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php $number = 1; foreach ($modules as $module) { ?>
                  <?php
                    $moduleId = (int) $module['id'];
                    $moduleName = htmlspecialchars($module['nama'], ENT_QUOTES, 'UTF-8');
                    $plainDetail = trim(strip_tags($module['detail']));
                    $detailPreview = function_exists('mb_substr') ? mb_substr($plainDetail, 0, 130) : substr($plainDetail, 0, 130);
                  ?>
                  <tr>
                    <td><?= $number++ ?></td>
                    <td style="min-width: 280px;">
                      <div class="d-flex align-items-start">
                        <img
                          src="<?= base_url('assets/img/'.$encodeAssetPath($module['img'])) ?>"
                          alt="<?= $moduleName ?>"
                          class="module-thumbnail mr-3"
                          onerror="this.src='<?= base_url('assets/img/not-found-image.png') ?>'"
                        >
                        <div>
                          <strong><?= $moduleName ?></strong>
                          <p class="small text-muted mb-0 mt-1"><?= htmlspecialchars($detailPreview, ENT_QUOTES, 'UTF-8') ?><?= strlen($plainDetail) > 130 ? '…' : '' ?></p>
                        </div>
                      </div>
                    </td>
                    <td><?= htmlspecialchars($module['nama_penyakit'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="text-nowrap">
                      <span class="badge badge-primary mr-1"><?= (int) $module['total_bab'] ?> Bab</span>
                      <span class="badge badge-info mr-1"><?= (int) $module['total_materi'] ?> Materi</span>
                      <span class="badge badge-success mr-1"><?= (int) $module['total_tes'] ?> Tes Otomatis</span>
                      <span class="badge badge-warning"><?= (int) $module['total_soal'] ?> Soal/Tes</span>
                    </td>
                    <td><span class="badge badge-secondary"><?= (int) $module['total_peserta'] ?> pengguna</span></td>
                    <td><?= !empty($module['date']) ? date('d-m-Y H:i', strtotime($module['date'])) : '-' ?></td>
                    <td class="text-nowrap">
                      <a href="<?= base_url('backend/modul-pembelajaran/manage/'.$moduleId) ?>" class="btn btn-info btn-sm" title="Kelola Struktur">
                        <i class="fas fa-sitemap"></i> Kelola
                      </a>
                      <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#editModule<?= $moduleId ?>" title="Edit Modul">
                        <i class="fas fa-edit"></i>
                      </button>
                      <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteModule<?= $moduleId ?>" title="Hapus Modul">
                        <i class="fas fa-trash"></i>
                      </button>
                    </td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<div class="modal fade" id="createModuleModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <form method="POST" enctype="multipart/form-data" action="<?= base_url('backend/modul-pembelajaran/create') ?>" class="module-crud-form">
        <div class="modal-header">
          <h5 class="modal-title">Tambah Modul Pembelajaran</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Tutup"><span aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
          <div class="form-row">
            <div class="form-group col-md-7">
              <label>Nama Modul</label>
              <input type="text" name="nama" class="form-control" maxlength="100" required>
            </div>
            <div class="form-group col-md-5">
              <label>Penyakit</label>
              <select name="id_penyakit" class="form-control" required>
                <option value="">Pilih penyakit</option>
                <?php foreach ($diseases as $disease) { ?>
                  <option value="<?= (int) $disease['id'] ?>"><?= htmlspecialchars($disease['nama'], ENT_QUOTES, 'UTF-8') ?></option>
                <?php } ?>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label>Deskripsi Modul</label>
            <textarea name="detail" class="form-control" rows="5" required></textarea>
          </div>
          <div class="form-group mb-0">
            <label>Gambar Sampul <small class="text-muted">(JPG, PNG, atau WEBP; maksimal 4 MB)</small></label>
            <input type="file" name="image" class="form-control-file" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Simpan Modul</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php foreach ($modules as $module) { ?>
  <?php $moduleId = (int) $module['id']; ?>
  <div class="modal fade" id="editModule<?= $moduleId ?>" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <form method="POST" enctype="multipart/form-data" action="<?= base_url('backend/modul-pembelajaran/update/'.$moduleId) ?>" class="module-crud-form">
          <div class="modal-header">
            <h5 class="modal-title">Edit Modul Pembelajaran</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Tutup"><span aria-hidden="true">&times;</span></button>
          </div>
          <div class="modal-body">
            <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
            <div class="form-row">
              <div class="form-group col-md-7">
                <label>Nama Modul</label>
                <input type="text" name="nama" class="form-control" maxlength="100" value="<?= htmlspecialchars($module['nama'], ENT_QUOTES, 'UTF-8') ?>" required>
              </div>
              <div class="form-group col-md-5">
                <label>Penyakit</label>
                <select name="id_penyakit" class="form-control" required>
                  <?php foreach ($diseases as $disease) { ?>
                    <option value="<?= (int) $disease['id'] ?>" <?= (int) $module['id_penyakit'] === (int) $disease['id'] ? 'selected' : '' ?>>
                      <?= htmlspecialchars($disease['nama'], ENT_QUOTES, 'UTF-8') ?>
                    </option>
                  <?php } ?>
                </select>
              </div>
            </div>
            <div class="form-group">
              <label>Deskripsi Modul</label>
              <textarea name="detail" class="form-control" rows="5" required><?= htmlspecialchars($module['detail'], ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>
            <div class="form-group mb-0">
              <label>Ganti Gambar Sampul <small class="text-muted">(kosongkan untuk mempertahankan gambar lama)</small></label>
              <input type="file" name="image" class="form-control-file" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-success"><i class="fas fa-save mr-1"></i> Simpan Perubahan</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="modal fade" id="deleteModule<?= $moduleId ?>" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <form method="POST" action="<?= base_url('backend/modul-pembelajaran/delete/'.$moduleId) ?>" class="module-crud-form">
          <div class="modal-header">
            <h5 class="modal-title">Hapus Modul Pembelajaran</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Tutup"><span aria-hidden="true">&times;</span></button>
          </div>
          <div class="modal-body">
            <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
            <div class="alert alert-warning">
              Seluruh bab, materi, hasil Pre-Test/Post-Test, dan progres pengguna pada modul ini akan ikut dihapus.
            </div>
            Hapus modul <strong><?= htmlspecialchars($module['nama'], ENT_QUOTES, 'UTF-8') ?></strong>?
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-danger"><i class="fas fa-trash mr-1"></i> Hapus Permanen</button>
          </div>
        </form>
      </div>
    </div>
  </div>
<?php } ?>

<style>
  .module-thumbnail {
    width: 76px;
    height: 58px;
    object-fit: cover;
    border-radius: 6px;
    border: 1px solid #dee2e6;
  }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
  if (window.jQuery && $.fn.DataTable) {
    $('#learningModuleTable').DataTable({
      responsive: true,
      autoWidth: false,
      order: [[0, 'asc']],
      columnDefs: [{ targets: [6], orderable: false, searchable: false }],
      language: { url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/Indonesian.json' }
    });
  }

  function readCookie(name) {
    var prefix = encodeURIComponent(name) + '=';
    var cookies = document.cookie ? document.cookie.split('; ') : [];
    for (var i = 0; i < cookies.length; i++) {
      if (cookies[i].indexOf(prefix) === 0) return decodeURIComponent(cookies[i].substring(prefix.length));
    }
    return '';
  }

  document.querySelectorAll('.module-crud-form').forEach(function (form) {
    form.addEventListener('submit', function () {
      var token = readCookie('<?= $this->config->item('csrf_cookie_name') ?>');
      var input = form.querySelector('input[name="<?= $this->security->get_csrf_token_name() ?>"]');
      if (token && input) input.value = token;
    });
  });
});
</script>
