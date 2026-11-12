<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-7"><h1 class="m-0">Data Kuesioner Responden</h1></div>
        <div class="col-sm-5">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="<?= base_url('backend/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item active">Data Kuesioner Responden</li>
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
          <h3 class="card-title">Daftar Responden yang Sudah Mengisi Modal Kuesioner</h3>
          <div class="card-tools">
            <button type="submit" class="btn btn-danger btn-sm mr-1" form="bulkDeleteDemographicForm" id="bulkDeleteDemographicButton" disabled>
              <i class="fas fa-trash mr-1"></i> Bulk Delete (<span id="selectedDemographicCount">0</span>)
            </button>
            <a href="<?= base_url('backend/data-kuesioner-responden/export') ?>" class="btn btn-success btn-sm">
              <i class="fas fa-file-excel mr-1"></i> Download Excel
            </a>
          </div>
        </div>
        <div class="card-body">
          <form method="POST" action="<?= base_url('backend/data-kuesioner-responden/bulk-delete') ?>" id="bulkDeleteDemographicForm">
            <input
              type="hidden"
              id="bulkDemographicCsrfToken"
              name="<?= $this->security->get_csrf_token_name() ?>"
              value="<?= $this->security->get_csrf_hash() ?>"
            >
          <div class="table-responsive">
            <table id="dataDemografiTable" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th class="text-center select-demographic-column" style="width: 42px;">
                    <input type="checkbox" id="checkAllDemographic" aria-label="Pilih semua data kuesioner di halaman ini">
                  </th>
                  <th>No</th>
                  <th>Pengguna</th>
                  <th>Jenis Responden</th>
                  <th>Kode Responden</th>
                  <th>Total Jawaban</th>
                  <th>Tanggal Isi</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php $number = 1; foreach ($submissions as $submission) { ?>
                  <?php
                    $idDemografi = (int) $submission['id_demografi'];
                    $respondentType = !empty($submission['jenis_responden'])
                      ? $submission['jenis_responden']
                      : (!empty($submission['kategori_nama']) ? $submission['kategori_nama'] : ($submission['status_user_nama'] ?? '-'));
                    $respondentCode = !empty($submission['kode_responden'])
                      ? $submission['kode_responden']
                      : (!empty($submission['no_responden']) ? $submission['no_responden'] : '-');
                  ?>
                  <tr>
                    <td class="text-center select-demographic-column">
                      <input
                        type="checkbox"
                        class="demographic-checkbox"
                        value="<?= $idDemografi ?>"
                        aria-label="Pilih data kuesioner <?= htmlspecialchars($submission['nama'] ?? '-', ENT_QUOTES, 'UTF-8') ?>"
                      >
                    </td>
                    <td><?= $number++ ?></td>
                    <td>
                      <strong><?= htmlspecialchars($submission['nama'] ?? '-', ENT_QUOTES, 'UTF-8') ?></strong><br>
                      <small class="text-muted"><?= htmlspecialchars($submission['email'] ?? '-', ENT_QUOTES, 'UTF-8') ?></small><br>
                      <span class="badge badge-light"><?= htmlspecialchars($submission['status_user_nama'] ?? '-', ENT_QUOTES, 'UTF-8') ?></span>
                    </td>
                    <td><?= htmlspecialchars($respondentType, ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($respondentCode, ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= (int) $submission['total_jawaban'] ?></td>
                    <td><?= !empty($submission['created_at']) ? date('d-m-Y H:i', strtotime($submission['created_at'])) : '-' ?></td>
                    <td class="text-nowrap">
                      <a href="<?= base_url('backend/data-kuesioner-responden/detail/'.$idDemografi) ?>" class="btn btn-info btn-sm" title="Detail"><i class="fas fa-eye"></i></a>
                      <a href="<?= base_url('backend/data-kuesioner-responden/edit/'.$idDemografi) ?>" class="btn btn-success btn-sm" title="Edit"><i class="fas fa-edit"></i></a>
                      <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteDemographic<?= $idDemografi ?>" title="Hapus"><i class="fas fa-trash"></i></button>
                    </td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
          </form>
        </div>
      </div>
    </div>
  </section>
</div>

<?php foreach ($submissions as $submission) { ?>
  <?php $idDemografi = (int) $submission['id_demografi']; ?>
  <div class="modal fade" id="deleteDemographic<?= $idDemografi ?>" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Hapus Data Kuesioner</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Tutup"><span aria-hidden="true">&times;</span></button>
        </div>
        <form method="POST" action="<?= base_url('backend/data-kuesioner-responden/delete/'.$idDemografi) ?>">
          <div class="modal-body">
            <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
            Hapus data kuesioner milik <strong><?= htmlspecialchars($submission['nama'] ?? '-', ENT_QUOTES, 'UTF-8') ?></strong> beserta seluruh jawabannya?
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

<style>
  #dataDemografiTable .select-demographic-column {
    vertical-align: middle;
  }

  #dataDemografiTable .select-demographic-column input[type="checkbox"] {
    width: 18px;
    height: 18px;
    cursor: pointer;
  }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
  if (!window.jQuery) return;

  var selectedDemographicIds = {};
  var dataDemografiTable = null;

  if ($.fn.DataTable) {
    dataDemografiTable = $('#dataDemografiTable').DataTable({
      responsive: true,
      autoWidth: false,
      order: [[6, 'desc']],
      columnDefs: [
        { targets: [0, 7], orderable: false, searchable: false }
      ],
      language: { url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/Indonesian.json' }
    });
  }

  function getCookie(name) {
    var prefix = encodeURIComponent(name) + '=';
    var cookies = document.cookie ? document.cookie.split('; ') : [];

    for (var i = 0; i < cookies.length; i++) {
      if (cookies[i].indexOf(prefix) === 0) {
        return decodeURIComponent(cookies[i].substring(prefix.length));
      }
    }

    return '';
  }

  function getCurrentPageCheckboxes() {
    if (dataDemografiTable) {
      return $('.demographic-checkbox', dataDemografiTable.rows({ page: 'current' }).nodes());
    }

    return $('.demographic-checkbox');
  }

  function syncCheckboxes() {
    var checkboxes = dataDemografiTable
      ? $('.demographic-checkbox', dataDemografiTable.rows().nodes())
      : $('.demographic-checkbox');

    checkboxes.each(function () {
      this.checked = !!selectedDemographicIds[this.value];
    });
  }

  function updateBulkDeleteState() {
    var selectedCount = Object.keys(selectedDemographicIds).length;
    var currentPageCheckboxes = getCurrentPageCheckboxes();
    var currentPageCheckedCount = currentPageCheckboxes.filter(':checked').length;

    $('#bulkDeleteDemographicButton').prop('disabled', selectedCount === 0);
    $('#selectedDemographicCount').text(selectedCount);
    $('#checkAllDemographic').prop(
      'checked',
      currentPageCheckboxes.length > 0 && currentPageCheckedCount === currentPageCheckboxes.length
    );
    $('#checkAllDemographic').prop(
      'indeterminate',
      currentPageCheckedCount > 0 && currentPageCheckedCount < currentPageCheckboxes.length
    );
  }

  $(document).on('click', '#checkAllDemographic, .demographic-checkbox', function (event) {
    event.stopPropagation();
  });

  $(document).on('change', '#checkAllDemographic', function () {
    var isChecked = this.checked;

    getCurrentPageCheckboxes().each(function () {
      this.checked = isChecked;
      if (isChecked) {
        selectedDemographicIds[this.value] = true;
      } else {
        delete selectedDemographicIds[this.value];
      }
    });

    updateBulkDeleteState();
  });

  $('#dataDemografiTable').on('change', '.demographic-checkbox', function () {
    if (this.checked) {
      selectedDemographicIds[this.value] = true;
    } else {
      delete selectedDemographicIds[this.value];
    }

    updateBulkDeleteState();
  });

  if (dataDemografiTable) {
    dataDemografiTable.on('draw', function () {
      syncCheckboxes();
      updateBulkDeleteState();
    });
  }

  $('#bulkDeleteDemographicForm').on('submit', function () {
    var selectedIds = Object.keys(selectedDemographicIds);
    if (selectedIds.length === 0) {
      alert('Pilih minimal satu data kuesioner untuk dihapus.');
      return false;
    }

    if (!confirm('Yakin ingin menghapus ' + selectedIds.length + ' data kuesioner responden beserta seluruh jawabannya?')) {
      return false;
    }

    var latestCsrfToken = getCookie('<?= $this->config->item('csrf_cookie_name') ?>');
    if (latestCsrfToken) {
      $('#bulkDemographicCsrfToken').val(latestCsrfToken);
    }

    $('.bulk-demographic-id').remove();
    selectedIds.forEach(function (idDemografi) {
      $('<input>')
        .attr('type', 'hidden')
        .attr('name', 'demografi_ids[]')
        .addClass('bulk-demographic-id')
        .val(idDemografi)
        .appendTo('#bulkDeleteDemographicForm');
    });

    $('#bulkDeleteDemographicButton')
      .prop('disabled', true)
      .html('<i class="fas fa-spinner fa-spin mr-1"></i> Menghapus...');

    return true;
  });

  syncCheckboxes();
  updateBulkDeleteState();
});
</script>
