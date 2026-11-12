<?php
$moduleId = (int) $module['id'];
$statusLabels = array('materi' => 'Materi', '' => 'Materi', 'soal' => 'Kuis Lama', 'final' => 'Penutup');
$statusClasses = array('materi' => 'badge-info', '' => 'badge-info', 'soal' => 'badge-secondary', 'final' => 'badge-success');
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
        <div class="col-sm-7"><h1 class="m-0">Kelola Struktur Modul</h1></div>
        <div class="col-sm-5">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="<?= base_url('backend/modul-pembelajaran') ?>">Modul Pembelajaran</a></li>
            <li class="breadcrumb-item active"><?= htmlspecialchars($module['nama'], ENT_QUOTES, 'UTF-8') ?></li>
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
        <div class="card-body">
          <div class="row align-items-center">
            <div class="col-md-2 text-center mb-3 mb-md-0">
              <img
                src="<?= base_url('assets/img/'.$encodeAssetPath($module['img'])) ?>"
                class="img-fluid module-cover"
                alt="<?= htmlspecialchars($module['nama'], ENT_QUOTES, 'UTF-8') ?>"
                onerror="this.src='<?= base_url('assets/img/not-found-image.png') ?>'"
              >
            </div>
            <div class="col-md-8">
              <h3><?= htmlspecialchars($module['nama'], ENT_QUOTES, 'UTF-8') ?></h3>
              <span class="badge badge-secondary mb-2"><?= htmlspecialchars($module['nama_penyakit'] ?? '-', ENT_QUOTES, 'UTF-8') ?></span>
              <p class="mb-0"><?= nl2br(htmlspecialchars(strip_tags($module['detail']), ENT_QUOTES, 'UTF-8')) ?></p>
            </div>
            <div class="col-md-2 text-md-right mt-3 mt-md-0">
              <a href="<?= base_url('backend/modul-pembelajaran') ?>" class="btn btn-secondary btn-block">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
              </a>
              <button type="button" class="btn btn-primary btn-block" data-toggle="modal" data-target="#createDetailModal">
                <i class="fas fa-plus mr-1"></i> Tambah Bab
              </button>
            </div>
          </div>
        </div>
      </div>

      <div class="card card-success card-outline">
        <div class="card-header">
          <h3 class="card-title"><i class="fas fa-clipboard-check mr-2"></i>Pre-Test dan Post-Test Otomatis</h3>
        </div>
        <div class="card-body">
          <div class="alert alert-light border">
            Kedua tes otomatis tersedia pada setiap modul dan menggunakan bank tetap berisi
            <strong><?= (int) $testQuestionCount ?> soal Benar/Salah</strong>. Admin tidak perlu membuat soal atau pilihan jawaban lagi.
          </div>
          <div class="row">
            <?php foreach ($moduleTests as $moduleTest) { ?>
              <?php $isPreTest = $moduleTest['tipe'] === 'pre_test'; ?>
              <div class="col-md-6 mb-3 mb-md-0">
                <div class="automatic-test-card <?= $isPreTest ? 'automatic-test-pre' : 'automatic-test-post' ?>">
                  <div class="d-flex justify-content-between align-items-start">
                    <div>
                      <span class="badge <?= $isPreTest ? 'badge-info' : 'badge-success' ?> mb-2">
                        <?= $isPreTest ? 'PRE-TEST' : 'POST-TEST' ?>
                      </span>
                      <h5><?= htmlspecialchars($moduleTest['judul'], ENT_QUOTES, 'UTF-8') ?></h5>
                    </div>
                    <i class="fas <?= $isPreTest ? 'fa-play-circle' : 'fa-flag-checkered' ?> fa-2x text-muted"></i>
                  </div>
                  <div class="d-flex flex-wrap mt-3">
                    <span class="mr-4"><strong><?= (int) $testQuestionCount ?></strong> soal</span>
                    <span class="mr-4"><strong><?= (int) $moduleTest['total_peserta'] ?></strong> peserta</span>
                    <span>Rata-rata <strong><?= number_format((float) $moduleTest['rata_rata_nilai'], 2, ',', '.') ?></strong></span>
                  </div>
                </div>
              </div>
            <?php } ?>
          </div>
        </div>
      </div>

      <?php if (empty($hierarchy)) { ?>
        <div class="alert alert-info">Pre-Test dan Post-Test sudah tersedia. Tambahkan bab pendahuluan dan materi pembelajaran untuk melengkapi modul.</div>
      <?php } ?>

      <?php $detailNumber = 1; foreach ($hierarchy as $detail) { ?>
        <?php
          $detailId = (int) $detail['id'];
          $isIntroduction = $detail['jenis_modul'] === 'pendahuluan';
        ?>
        <div class="card <?= $isIntroduction ? 'card-secondary' : 'card-info' ?> card-outline">
          <div class="card-header">
            <h3 class="card-title">
              <span class="badge badge-light mr-2"><?= $detailNumber++ ?></span>
              <?= htmlspecialchars($detail['title'], ENT_QUOTES, 'UTF-8') ?>
              <span class="badge <?= $isIntroduction ? 'badge-secondary' : 'badge-info' ?> ml-2">
                <?= $isIntroduction ? 'Pendahuluan' : 'Bab Materi' ?>
              </span>
            </h3>
            <div class="card-tools">
              <?php if (!$isIntroduction) { ?>
                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#createContent<?= $detailId ?>">
                  <i class="fas fa-plus mr-1"></i> Tambah Materi
                </button>
              <?php } ?>
              <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#editDetail<?= $detailId ?>" title="Edit Bab">
                <i class="fas fa-edit"></i>
              </button>
              <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteDetail<?= $detailId ?>" title="Hapus Bab">
                <i class="fas fa-trash"></i>
              </button>
            </div>
          </div>
          <div class="card-body">
            <?php if ($isIntroduction) { ?>
              <div class="content-preview"><?= $detail['field'] !== '' ? $detail['field'] : '<span class="text-muted">Konten pendahuluan masih kosong.</span>' ?></div>
            <?php } elseif (empty($detail['materi'])) { ?>
              <div class="alert alert-light border mb-0">Bab ini belum memiliki materi atau penutup.</div>
            <?php } else { ?>
              <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0">
                  <thead class="thead-light">
                    <tr>
                      <th style="width: 60px;">Urutan</th>
                      <th>Judul Bagian</th>
                      <th style="width: 130px;">Jenis</th>
                      <th>Konten</th>
                      <th style="width: 190px;">Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php $contentNumber = 1; foreach ($detail['materi'] as $content) { ?>
                      <?php
                        $contentId = (int) $content['id'];
                        $statusKey = isset($statusLabels[$content['status']]) ? $content['status'] : 'materi';
                        $isPdf = strtolower(pathinfo($content['field'], PATHINFO_EXTENSION)) === 'pdf';
                        $plainContent = trim(strip_tags($content['field']));
                        $preview = function_exists('mb_substr') ? mb_substr($plainContent, 0, 100) : substr($plainContent, 0, 100);
                      ?>
                      <tr>
                        <td class="text-center"><?= $contentNumber++ ?></td>
                        <td><strong><?= htmlspecialchars($content['title'], ENT_QUOTES, 'UTF-8') ?></strong></td>
                        <td><span class="badge <?= $statusClasses[$statusKey] ?>"><?= $statusLabels[$statusKey] ?></span></td>
                        <td>
                          <?php if ($content['status'] === 'soal') { ?>
                            <span class="text-muted"><?= count($content['soal']) ?> soal</span>
                          <?php } elseif ($isPdf) { ?>
                            <a href="<?= base_url('assets/'.$encodeAssetPath($content['field'])) ?>" target="_blank" rel="noopener">
                              <i class="fas fa-file-pdf text-danger mr-1"></i><?= htmlspecialchars(basename($content['field']), ENT_QUOTES, 'UTF-8') ?>
                            </a>
                          <?php } else { ?>
                            <span class="text-muted"><?= htmlspecialchars($preview, ENT_QUOTES, 'UTF-8') ?><?= strlen($plainContent) > 100 ? '…' : '' ?></span>
                          <?php } ?>
                        </td>
                        <td class="text-nowrap">
                          <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#editContent<?= $contentId ?>" title="Edit Materi">
                            <i class="fas fa-edit"></i>
                          </button>
                          <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteContent<?= $contentId ?>" title="Hapus Materi">
                            <i class="fas fa-trash"></i>
                          </button>
                        </td>
                      </tr>
                    <?php } ?>
                  </tbody>
                </table>
              </div>
            <?php } ?>
          </div>
        </div>
      <?php } ?>

      <div class="card card-secondary card-outline mt-4">
        <div class="card-header"><h3 class="card-title"><i class="fas fa-chart-line mr-2"></i>Hasil Nilai Peserta</h3></div>
        <div class="card-body">
          <?php if (empty($participantResults)) { ?>
            <div class="alert alert-light border mb-0">Belum ada peserta yang menyelesaikan Pre-Test atau Post-Test.</div>
          <?php } else { ?>
            <div class="table-responsive">
              <table class="table table-bordered table-striped mb-0" id="moduleTestResultTable">
                <thead>
                  <tr>
                    <th>Peserta</th>
                    <th>Email</th>
                    <th>Nilai Pre-Test</th>
                    <th>Nilai Post-Test</th>
                    <th>Perubahan</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($participantResults as $participant) { ?>
                    <?php
                      $hasPre = $participant['nilai_pre_test'] !== NULL;
                      $hasPost = $participant['nilai_post_test'] !== NULL;
                      $delta = ($hasPre && $hasPost)
                          ? (float) $participant['nilai_post_test'] - (float) $participant['nilai_pre_test']
                          : NULL;
                    ?>
                    <tr>
                      <td><?= htmlspecialchars($participant['nama'], ENT_QUOTES, 'UTF-8') ?></td>
                      <td><?= htmlspecialchars($participant['email'], ENT_QUOTES, 'UTF-8') ?></td>
                      <td><?= $hasPre ? number_format((float) $participant['nilai_pre_test'], 2, ',', '.') : '-' ?></td>
                      <td><?= $hasPost ? number_format((float) $participant['nilai_post_test'], 2, ',', '.') : '-' ?></td>
                      <td>
                        <?php if ($delta === NULL) { ?>
                          -
                        <?php } else { ?>
                          <span class="badge <?= $delta >= 0 ? 'badge-success' : 'badge-danger' ?>">
                            <?= $delta >= 0 ? '+' : '' ?><?= number_format($delta, 2, ',', '.') ?>
                          </span>
                        <?php } ?>
                      </td>
                    </tr>
                  <?php } ?>
                </tbody>
              </table>
            </div>
          <?php } ?>
        </div>
      </div>
    </div>
  </section>
</div>

<div class="modal fade" id="createDetailModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <form method="POST" action="<?= base_url('backend/modul-pembelajaran/detail/create') ?>" class="module-structure-form">
        <div class="modal-header">
          <h5 class="modal-title">Tambah Bab Modul</h5>
          <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
          <input type="hidden" name="id_modul_pembelajaran" value="<?= $moduleId ?>">
          <div class="form-row">
            <div class="form-group col-md-8">
              <label>Judul Bab</label>
              <input type="text" name="title" class="form-control" maxlength="100" required>
            </div>
            <div class="form-group col-md-4">
              <label>Jenis Bab</label>
              <select name="jenis_modul" class="form-control" required>
                <option value="non_soal">Bab Materi</option>
                <option value="pendahuluan">Pendahuluan</option>
              </select>
            </div>
          </div>
          <div class="form-group mb-0">
            <label>Konten Bab <small class="text-muted">(digunakan langsung pada bab pendahuluan)</small></label>
            <textarea name="field" class="form-control" rows="7"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Simpan Bab</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php foreach ($hierarchy as $detail) { ?>
  <?php $detailId = (int) $detail['id']; $isIntroduction = $detail['jenis_modul'] === 'pendahuluan'; ?>
  <div class="modal fade" id="editDetail<?= $detailId ?>" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <form method="POST" action="<?= base_url('backend/modul-pembelajaran/detail/update/'.$detailId) ?>" class="module-structure-form">
          <div class="modal-header">
            <h5 class="modal-title">Edit Bab Modul</h5>
            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
          </div>
          <div class="modal-body">
            <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
            <div class="form-row">
              <div class="form-group col-md-8">
                <label>Judul Bab</label>
                <input type="text" name="title" class="form-control" maxlength="100" value="<?= htmlspecialchars($detail['title'], ENT_QUOTES, 'UTF-8') ?>" required>
              </div>
              <div class="form-group col-md-4">
                <label>Jenis Bab</label>
                <select name="jenis_modul" class="form-control" required>
                  <option value="non_soal" <?= !$isIntroduction ? 'selected' : '' ?>>Bab Materi</option>
                  <option value="pendahuluan" <?= $isIntroduction ? 'selected' : '' ?>>Pendahuluan</option>
                </select>
              </div>
            </div>
            <div class="form-group mb-0">
              <label>Konten Bab</label>
              <textarea name="field" class="form-control" rows="8"><?= htmlspecialchars($detail['field'], ENT_QUOTES, 'UTF-8') ?></textarea>
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

  <div class="modal fade" id="deleteDetail<?= $detailId ?>" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <form method="POST" action="<?= base_url('backend/modul-pembelajaran/detail/delete/'.$detailId) ?>" class="module-structure-form">
          <div class="modal-header">
            <h5 class="modal-title">Hapus Bab Modul</h5>
            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
          </div>
          <div class="modal-body">
            <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
            <div class="alert alert-warning">Seluruh materi dan progres pengguna dalam bab ini akan ikut dihapus.</div>
            Hapus bab <strong><?= htmlspecialchars($detail['title'], ENT_QUOTES, 'UTF-8') ?></strong>?
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-danger"><i class="fas fa-trash mr-1"></i> Hapus Permanen</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <?php if (!$isIntroduction) { ?>
    <div class="modal fade" id="createContent<?= $detailId ?>" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <form method="POST" enctype="multipart/form-data" action="<?= base_url('backend/modul-pembelajaran/content/create') ?>" class="module-structure-form content-editor-form">
            <div class="modal-header">
              <h5 class="modal-title">Tambah Materi — <?= htmlspecialchars($detail['title'], ENT_QUOTES, 'UTF-8') ?></h5>
              <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
              <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
              <input type="hidden" name="id_detail_modul" value="<?= $detailId ?>">
              <div class="form-row">
                <div class="form-group col-md-8">
                  <label>Judul Bagian</label>
                  <input type="text" name="title" class="form-control" maxlength="100" required>
                </div>
                <div class="form-group col-md-4">
                  <label>Jenis Bagian</label>
                  <select name="status" class="form-control content-type-select" required>
                    <option value="materi">Materi</option>
                    <option value="final">Penutup</option>
                  </select>
                </div>
              </div>
              <div class="content-fields">
                <div class="form-group">
                  <label>Konten HTML/Teks</label>
                  <textarea name="field" class="form-control" rows="8"></textarea>
                </div>
                <div class="form-group mb-0">
                  <label>Atau unggah PDF <small class="text-muted">(maksimal 10 MB; PDF menggantikan konten teks)</small></label>
                  <input type="file" name="material_file" class="form-control-file" accept=".pdf,application/pdf">
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
              <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Simpan Materi</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  <?php } ?>

  <?php foreach ($detail['materi'] as $content) { ?>
    <?php
      $contentId = (int) $content['id'];
      $storedStatus = $content['status'] === 'final' ? 'final' : 'materi';
      $isPdf = strtolower(pathinfo($content['field'], PATHINFO_EXTENSION)) === 'pdf';
    ?>
    <div class="modal fade" id="editContent<?= $contentId ?>" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <form method="POST" enctype="multipart/form-data" action="<?= base_url('backend/modul-pembelajaran/content/update/'.$contentId) ?>" class="module-structure-form content-editor-form">
            <div class="modal-header">
              <h5 class="modal-title">Edit Materi Modul</h5>
              <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
              <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
              <div class="form-row">
                <div class="form-group col-md-8">
                  <label>Judul Bagian</label>
                  <input type="text" name="title" class="form-control" maxlength="100" value="<?= htmlspecialchars($content['title'], ENT_QUOTES, 'UTF-8') ?>" required>
                </div>
                <div class="form-group col-md-4">
                  <label>Jenis Bagian</label>
                  <select name="status" class="form-control content-type-select" required>
                    <option value="materi" <?= $storedStatus === 'materi' ? 'selected' : '' ?>>Materi</option>
                    <option value="final" <?= $storedStatus === 'final' ? 'selected' : '' ?>>Penutup</option>
                  </select>
                </div>
              </div>
              <div class="content-fields">
                <?php if ($isPdf) { ?>
                  <div class="alert alert-light border">
                    PDF saat ini: <strong><?= htmlspecialchars(basename($content['field']), ENT_QUOTES, 'UTF-8') ?></strong>. Unggah PDF baru atau isi konten teks untuk menggantinya.
                  </div>
                <?php } ?>
                <div class="form-group">
                  <label>Konten HTML/Teks</label>
                  <textarea name="field" class="form-control" rows="8"><?= $isPdf ? '' : htmlspecialchars($content['field'], ENT_QUOTES, 'UTF-8') ?></textarea>
                </div>
                <div class="form-group mb-0">
                  <label>Ganti dengan PDF <small class="text-muted">(maksimal 10 MB)</small></label>
                  <input type="file" name="material_file" class="form-control-file" accept=".pdf,application/pdf">
                </div>
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

    <div class="modal fade" id="deleteContent<?= $contentId ?>" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <form method="POST" action="<?= base_url('backend/modul-pembelajaran/content/delete/'.$contentId) ?>" class="module-structure-form">
            <div class="modal-header">
              <h5 class="modal-title">Hapus Materi Modul</h5>
              <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
              <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
              <?php if ($content['status'] === 'soal') { ?>
                <div class="alert alert-warning">Seluruh soal, pilihan jawaban, dan jawaban pengguna di bagian ini akan ikut dihapus.</div>
              <?php } ?>
              Hapus bagian <strong><?= htmlspecialchars($content['title'], ENT_QUOTES, 'UTF-8') ?></strong>?
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
<?php } ?>

<style>
  .module-cover { max-height: 130px; border-radius: 8px; object-fit: cover; }
  .content-preview { max-height: 260px; overflow: auto; padding: 12px; border: 1px solid #e9ecef; border-radius: 6px; }
  .content-preview img { max-width: 100%; height: auto; }
  .automatic-test-card { height: 100%; padding: 20px; border: 1px solid #d9e2ec; border-radius: 10px; background: #fff; }
  .automatic-test-pre { border-left: 5px solid #17a2b8; }
  .automatic-test-post { border-left: 5px solid #28a745; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
  function readCookie(name) {
    var prefix = encodeURIComponent(name) + '=';
    var cookies = document.cookie ? document.cookie.split('; ') : [];
    for (var i = 0; i < cookies.length; i++) {
      if (cookies[i].indexOf(prefix) === 0) return decodeURIComponent(cookies[i].substring(prefix.length));
    }
    return '';
  }

  document.querySelectorAll('.module-structure-form').forEach(function (form) {
    form.addEventListener('submit', function () {
      var token = readCookie('<?= $this->config->item('csrf_cookie_name') ?>');
      var input = form.querySelector('input[name="<?= $this->security->get_csrf_token_name() ?>"]');
      if (token && input) input.value = token;
    });
  });

  if (window.jQuery && $.fn.DataTable && document.getElementById('moduleTestResultTable')) {
    $('#moduleTestResultTable').DataTable({ responsive: true, autoWidth: false, pageLength: 10 });
  }
});
</script>
