<?php
$moduleId = (int) $content['id_modul_pembelajaran'];
$contentId = (int) $content['id'];
?>
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-7"><h1 class="m-0">Kelola Soal dan Pilihan Jawaban</h1></div>
        <div class="col-sm-5">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="<?= base_url('backend/modul-pembelajaran') ?>">Modul</a></li>
            <li class="breadcrumb-item"><a href="<?= base_url('backend/modul-pembelajaran/manage/'.$moduleId) ?>">Struktur</a></li>
            <li class="breadcrumb-item active">Soal</li>
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

      <div class="card card-warning card-outline">
        <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center">
          <div>
            <span class="badge badge-secondary mb-2"><?= htmlspecialchars($module['nama'], ENT_QUOTES, 'UTF-8') ?></span>
            <h3 class="mb-1"><?= htmlspecialchars($content['title'], ENT_QUOTES, 'UTF-8') ?></h3>
            <p class="text-muted mb-0"><?= count($questions) ?> soal tersedia. Setiap soal sebaiknya memiliki minimal dua pilihan dan tepat satu jawaban benar.</p>
          </div>
          <div class="mt-3 mt-md-0">
            <a href="<?= base_url('backend/modul-pembelajaran/manage/'.$moduleId) ?>" class="btn btn-secondary">
              <i class="fas fa-arrow-left mr-1"></i> Kembali ke Struktur
            </a>
          </div>
        </div>
      </div>

      <div class="card card-primary card-outline">
        <div class="card-header"><h3 class="card-title">Tambah Soal</h3></div>
        <form method="POST" action="<?= base_url('backend/modul-pembelajaran/question/create') ?>" class="quiz-crud-form">
          <div class="card-body">
            <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
            <input type="hidden" name="list_detail_modul_id" value="<?= $contentId ?>">
            <div class="form-row">
              <div class="form-group col-md-10 mb-md-0">
                <label>Pertanyaan</label>
                <textarea name="pertanyaan" class="form-control" rows="2" required></textarea>
              </div>
              <div class="form-group col-md-2 mb-md-0">
                <label>Nilai</label>
                <input type="number" name="nilai" class="form-control" min="1" max="10000" required>
              </div>
            </div>
          </div>
          <div class="card-footer text-right">
            <button type="submit" class="btn btn-primary"><i class="fas fa-plus mr-1"></i> Tambah Soal</button>
          </div>
        </form>
      </div>

      <?php if (empty($questions)) { ?>
        <div class="alert alert-info">Belum ada soal pada bagian ini.</div>
      <?php } ?>

      <?php $questionNumber = 1; foreach ($questions as $question) { ?>
        <?php
          $questionId = (int) $question['id'];
          $correctCount = 0;
          foreach ($question['pilihan'] as $option) {
              if ((int) $option['is_benar'] === 1) $correctCount++;
          }
        ?>
        <div class="card card-outline <?= $correctCount === 1 && count($question['pilihan']) >= 2 ? 'card-success' : 'card-danger' ?>">
          <div class="card-header">
            <h3 class="card-title">
              <span class="badge badge-dark mr-2"><?= $questionNumber++ ?></span>
              <?= htmlspecialchars($question['pertanyaan'], ENT_QUOTES, 'UTF-8') ?>
              <span class="badge badge-primary ml-2"><?= (int) $question['nilai'] ?> poin</span>
            </h3>
            <div class="card-tools">
              <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#createOption<?= $questionId ?>">
                <i class="fas fa-plus mr-1"></i> Pilihan
              </button>
              <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#editQuestion<?= $questionId ?>" title="Edit Soal">
                <i class="fas fa-edit"></i>
              </button>
              <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteQuestion<?= $questionId ?>" title="Hapus Soal">
                <i class="fas fa-trash"></i>
              </button>
            </div>
          </div>
          <div class="card-body">
            <?php if (count($question['pilihan']) < 2 || $correctCount !== 1) { ?>
              <div class="alert alert-warning py-2">
                Soal belum siap digunakan: diperlukan minimal dua pilihan dan tepat satu jawaban benar.
              </div>
            <?php } ?>

            <div class="table-responsive">
              <table class="table table-bordered table-sm mb-0">
                <thead class="thead-light">
                  <tr>
                    <th style="width: 60px;">No</th>
                    <th>Pilihan Jawaban</th>
                    <th style="width: 120px;">Status</th>
                    <th style="width: 110px;">Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (empty($question['pilihan'])) { ?>
                    <tr><td colspan="4" class="text-center text-muted">Belum ada pilihan jawaban.</td></tr>
                  <?php } ?>
                  <?php $optionNumber = 1; foreach ($question['pilihan'] as $option) { ?>
                    <?php $optionId = (int) $option['id']; ?>
                    <tr>
                      <td class="text-center"><?= $optionNumber++ ?></td>
                      <td><?= htmlspecialchars($option['pilihan'], ENT_QUOTES, 'UTF-8') ?></td>
                      <td>
                        <?php if ((int) $option['is_benar'] === 1) { ?>
                          <span class="badge badge-success"><i class="fas fa-check mr-1"></i> Benar</span>
                        <?php } else { ?>
                          <span class="badge badge-secondary">Salah</span>
                        <?php } ?>
                      </td>
                      <td class="text-nowrap">
                        <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#editOption<?= $optionId ?>" title="Edit Pilihan">
                          <i class="fas fa-edit"></i>
                        </button>
                        <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteOption<?= $optionId ?>" title="Hapus Pilihan">
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
      <?php } ?>
    </div>
  </section>
</div>

<?php foreach ($questions as $question) { ?>
  <?php $questionId = (int) $question['id']; ?>
  <div class="modal fade" id="createOption<?= $questionId ?>" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <form method="POST" action="<?= base_url('backend/modul-pembelajaran/option/create') ?>" class="quiz-crud-form">
          <div class="modal-header">
            <h5 class="modal-title">Tambah Pilihan Jawaban</h5>
            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
          </div>
          <div class="modal-body">
            <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
            <input type="hidden" name="soal_id" value="<?= $questionId ?>">
            <div class="form-group">
              <label>Pilihan Jawaban</label>
              <input type="text" name="pilihan" class="form-control" maxlength="60" required>
            </div>
            <div class="form-group mb-0">
              <label>Status Jawaban</label>
              <select name="is_benar" class="form-control">
                <option value="0">Salah</option>
                <option value="1">Benar</option>
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Simpan Pilihan</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="modal fade" id="editQuestion<?= $questionId ?>" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <form method="POST" action="<?= base_url('backend/modul-pembelajaran/question/update/'.$questionId) ?>" class="quiz-crud-form">
          <div class="modal-header">
            <h5 class="modal-title">Edit Soal</h5>
            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
          </div>
          <div class="modal-body">
            <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
            <div class="form-row">
              <div class="form-group col-md-10 mb-md-0">
                <label>Pertanyaan</label>
                <textarea name="pertanyaan" class="form-control" rows="3" required><?= htmlspecialchars($question['pertanyaan'], ENT_QUOTES, 'UTF-8') ?></textarea>
              </div>
              <div class="form-group col-md-2 mb-md-0">
                <label>Nilai</label>
                <input type="number" name="nilai" class="form-control" min="1" max="10000" value="<?= (int) $question['nilai'] ?>" required>
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

  <div class="modal fade" id="deleteQuestion<?= $questionId ?>" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <form method="POST" action="<?= base_url('backend/modul-pembelajaran/question/delete/'.$questionId) ?>" class="quiz-crud-form">
          <div class="modal-header">
            <h5 class="modal-title">Hapus Soal</h5>
            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
          </div>
          <div class="modal-body">
            <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
            Seluruh pilihan dan jawaban pengguna untuk soal ini akan ikut dihapus. Lanjutkan?
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-danger"><i class="fas fa-trash mr-1"></i> Hapus Permanen</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <?php foreach ($question['pilihan'] as $option) { ?>
    <?php $optionId = (int) $option['id']; ?>
    <div class="modal fade" id="editOption<?= $optionId ?>" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <form method="POST" action="<?= base_url('backend/modul-pembelajaran/option/update/'.$optionId) ?>" class="quiz-crud-form">
            <div class="modal-header">
              <h5 class="modal-title">Edit Pilihan Jawaban</h5>
              <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
              <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
              <div class="form-group">
                <label>Pilihan Jawaban</label>
                <input type="text" name="pilihan" class="form-control" maxlength="60" value="<?= htmlspecialchars($option['pilihan'], ENT_QUOTES, 'UTF-8') ?>" required>
              </div>
              <div class="form-group mb-0">
                <label>Status Jawaban</label>
                <select name="is_benar" class="form-control">
                  <option value="0" <?= (int) $option['is_benar'] === 0 ? 'selected' : '' ?>>Salah</option>
                  <option value="1" <?= (int) $option['is_benar'] === 1 ? 'selected' : '' ?>>Benar</option>
                </select>
                <small class="form-text text-muted">Untuk mengganti jawaban benar, ubah pilihan benar lama menjadi salah terlebih dahulu.</small>
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

    <div class="modal fade" id="deleteOption<?= $optionId ?>" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <form method="POST" action="<?= base_url('backend/modul-pembelajaran/option/delete/'.$optionId) ?>" class="quiz-crud-form">
            <div class="modal-header">
              <h5 class="modal-title">Hapus Pilihan Jawaban</h5>
              <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
              <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
              Hapus pilihan <strong><?= htmlspecialchars($option['pilihan'], ENT_QUOTES, 'UTF-8') ?></strong> beserta jawaban pengguna yang memilihnya?
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
              <button type="submit" class="btn btn-danger"><i class="fas fa-trash mr-1"></i> Hapus</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  <?php } ?>
<?php } ?>

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

  document.querySelectorAll('.quiz-crud-form').forEach(function (form) {
    form.addEventListener('submit', function () {
      var token = readCookie('<?= $this->config->item('csrf_cookie_name') ?>');
      var input = form.querySelector('input[name="<?= $this->security->get_csrf_token_name() ?>"]');
      if (token && input) input.value = token;
    });
  });
});
</script>
