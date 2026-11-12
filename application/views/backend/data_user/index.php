
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Data User</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Data User</li>
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
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalTambahUser">
                  <i class="fas fa-plus"></i> Tambah User
                </button>
                <button type="submit" class="btn btn-danger ml-2" form="bulkDeleteUserForm" id="bulkDeleteUserButton" disabled>
                  <i class="fas fa-trash"></i> Bulk Delete
                </button>
              </div>
              <div class="card-body">
                <form method="POST" action="<?= base_url('backend/data_user/proses_hapus_bulk') ?>" id="bulkDeleteUserForm">
                  <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                <table id="dataUserTable" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                    <th class="text-center select-user-column" style="width: 42px;">
                      <input type="checkbox" id="checkAllDataUser" aria-label="Pilih semua user di halaman ini">
                    </th>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>No Hp</th>
                    <th>Status Akun</th>
                    <th>Status Login</th>
                    <th>Aksi</th>
                  </tr>
                  </thead>
                  <tbody>
                  <?php
                  $i = 1;
                  foreach ($getDataUser as $value) {
                    $id = (int) $value['id'];
                    $nama = htmlspecialchars($value['nama'], ENT_QUOTES, 'UTF-8');
                    $email = htmlspecialchars($value['email'], ENT_QUOTES, 'UTF-8');
                    $noHp = htmlspecialchars($value['no_hp'], ENT_QUOTES, 'UTF-8');
                    $namaStatus = htmlspecialchars($value['nama_status'] ?: 'Tidak ada status', ENT_QUOTES, 'UTF-8');
                  ?>
                  <tr>
                    <td class="text-center select-user-column">
                      <input type="checkbox" class="data-user-checkbox" value="<?= $id ?>" aria-label="Pilih user <?= $nama ?>">
                    </td>
                    <td><?= $i ?></td>
                    <td><?= $nama ?></td>
                    <td><?= $email ?></td>
                    <td><?= $noHp ?></td>
                    <td><?= $namaStatus ?></td>
                    <td>
                      <?php if ((int) $value['status_login'] == 1) { ?>
                        <span class="badge badge-success">Login</span>
                      <?php } else { ?>
                        <span class="badge badge-secondary">Tidak Login</span>
                      <?php } ?>
                    </td>
                    <td>
                      <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#modalEditUser<?= $id ?>">
                        <i class="fas fa-edit"></i> Edit
                      </button>
                      <button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#modalHapusUser<?= $id ?>">
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
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

<?php
foreach ($getDataUser as $value) {
  $id = (int) $value['id'];
  $nama = htmlspecialchars($value['nama'], ENT_QUOTES, 'UTF-8');
  $email = htmlspecialchars($value['email'], ENT_QUOTES, 'UTF-8');
  $noHp = htmlspecialchars($value['no_hp'], ENT_QUOTES, 'UTF-8');
?>
<div class="modal fade" id="modalEditUser<?= $id ?>">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Edit User</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <form method="POST" action="<?= base_url('backend/data_user/proses_edit/'.$id) ?>">
        <div class="modal-body">
          <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
          <div class="form-row">
            <div class="form-group col-md-6">
              <label>Nama</label>
              <input type="text" class="form-control" name="nama" value="<?= $nama ?>" maxlength="100" required>
            </div>
            <div class="form-group col-md-6">
              <label>Email</label>
              <input type="email" class="form-control" name="email" value="<?= $email ?>" maxlength="100" required>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group col-md-6">
              <label>No Hp</label>
              <input type="text" class="form-control" name="no_hp" value="<?= $noHp ?>" maxlength="100" required>
            </div>
            <div class="form-group col-md-6">
              <label>Status Akun</label>
              <select name="id_status" class="form-control" required>
                <?php foreach ($getStatusUser as $statusUser) { ?>
                  <option value="<?= (int) $statusUser['id'] ?>" <?= (int) $value['id_status'] == (int) $statusUser['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($statusUser['nama'], ENT_QUOTES, 'UTF-8') ?>
                  </option>
                <?php } ?>
              </select>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group col-md-6">
              <label>Status Login</label>
              <select name="status_login" class="form-control">
                <option value="0" <?= (int) $value['status_login'] == 0 ? 'selected' : '' ?>>Tidak Login</option>
                <option value="1" <?= (int) $value['status_login'] == 1 ? 'selected' : '' ?>>Login</option>
              </select>
            </div>
            <div class="form-group col-md-6">
              <label>Password Baru</label>
              <input type="password" class="form-control" name="password" placeholder="Kosongkan jika tidak diganti">
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

<div class="modal fade" id="modalHapusUser<?= $id ?>">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Hapus User</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <form method="POST" action="<?= base_url('backend/data_user/proses_hapus/'.$id) ?>">
        <div class="modal-body">
          <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
          <p>Yakin ingin menghapus user <strong><?= $nama ?></strong>?</p>
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

<div class="modal fade" id="modalTambahUser">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Tambah User</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <form method="POST" action="<?= base_url('backend/data_user/proses_tambah') ?>">
        <div class="modal-body">
          <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
          <div class="form-row">
            <div class="form-group col-md-6">
              <label>Nama</label>
              <input type="text" class="form-control" name="nama" placeholder="Masukkan nama" maxlength="100" required>
            </div>
            <div class="form-group col-md-6">
              <label>Email</label>
              <input type="email" class="form-control" name="email" placeholder="Masukkan email" maxlength="100" required>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group col-md-6">
              <label>No Hp</label>
              <input type="text" class="form-control" name="no_hp" placeholder="Masukkan no hp" maxlength="100" required>
            </div>
            <div class="form-group col-md-6">
              <label>Status Akun</label>
              <select name="id_status" class="form-control" required>
                <option value="">Pilih Status</option>
                <?php foreach ($getStatusUser as $statusUser) { ?>
                  <option value="<?= (int) $statusUser['id'] ?>">
                    <?= htmlspecialchars($statusUser['nama'], ENT_QUOTES, 'UTF-8') ?>
                  </option>
                <?php } ?>
              </select>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group col-md-6">
              <label>Status Login</label>
              <select name="status_login" class="form-control">
                <option value="0">Tidak Login</option>
                <option value="1">Login</option>
              </select>
            </div>
            <div class="form-group col-md-6">
              <label>Password</label>
              <input type="password" class="form-control" name="password" placeholder="Masukkan password" required>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
          <button type="submit" class="btn btn-primary">Tambah User</button>
        </div>
      </form>
    </div>
  </div>
</div>

<style>
  #dataUserTable .select-user-column {
    vertical-align: middle;
  }

  #dataUserTable .select-user-column input[type="checkbox"] {
    cursor: pointer;
    width: 18px;
    height: 18px;
  }
</style>

<script>
  $(function () {
    var selectedUserIds = {};
    var dataUserTable = $("#dataUserTable").DataTable({
      "responsive": true,
      "lengthChange": false,
      "autoWidth": false,
      "order": [[1, "asc"]],
      "columnDefs": [
        {
          "targets": [0, 7],
          "orderable": false,
          "searchable": false
        }
      ]
    });

    function getCheckedCount() {
      return Object.keys(selectedUserIds).length;
    }

    function getCurrentPageCheckboxes() {
      return $(".data-user-checkbox", dataUserTable.rows({ page: "current" }).nodes());
    }

    function syncCheckboxes() {
      $(".data-user-checkbox", dataUserTable.rows().nodes()).each(function () {
        this.checked = !!selectedUserIds[this.value];
      });
    }

    function updateBulkDeleteButton() {
      var checkedCount = getCheckedCount();
      var currentPageCheckboxes = getCurrentPageCheckboxes();
      var currentPageCheckedCount = currentPageCheckboxes.filter(":checked").length;

      $("#bulkDeleteUserButton").prop("disabled", checkedCount === 0);
      $("#checkAllDataUser").prop("checked", currentPageCheckboxes.length > 0 && currentPageCheckedCount === currentPageCheckboxes.length);
    }

    $(document).on("click", "#checkAllDataUser", function (event) {
      event.stopPropagation();
    });

    $(document).on("change", "#checkAllDataUser", function () {
      var isChecked = this.checked;

      getCurrentPageCheckboxes().each(function () {
        this.checked = isChecked;
        if (isChecked) {
          selectedUserIds[this.value] = true;
        } else {
          delete selectedUserIds[this.value];
        }
      });

      updateBulkDeleteButton();
    });

    $("#dataUserTable").on("click", ".data-user-checkbox", function (event) {
      event.stopPropagation();
    });

    $("#dataUserTable").on("change", ".data-user-checkbox", function () {
      if (this.checked) {
        selectedUserIds[this.value] = true;
      } else {
        delete selectedUserIds[this.value];
      }

      updateBulkDeleteButton();
    });

    dataUserTable.on("draw", function () {
      syncCheckboxes();
      updateBulkDeleteButton();
    });

    $("#bulkDeleteUserForm").on("submit", function () {
      var selectedIds = Object.keys(selectedUserIds);
      var checkedCount = selectedIds.length;
      if (checkedCount === 0) {
        alert("Pilih minimal satu user untuk dihapus.");
        return false;
      }

      if (!confirm("Yakin ingin menghapus " + checkedCount + " data user yang dipilih?")) {
        return false;
      }

      $(".bulk-user-id").remove();
      selectedIds.forEach(function (userId) {
        $("<input>")
          .attr("type", "hidden")
          .attr("name", "user_ids[]")
          .addClass("bulk-user-id")
          .val(userId)
          .appendTo("#bulkDeleteUserForm");
      });

      return true;
    });

    syncCheckboxes();
    updateBulkDeleteButton();
  });
</script>
