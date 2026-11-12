

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Media Pembelajaran <?=$getTitleModulPembelajaran['nama_penyakit']?></h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Media Pembelajaran <?=$getTitleModulPembelajaran['nama_penyakit']?></li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
      <div class="container-fluid">
        <div class="row">
          <?php
          foreach ($getModulPembelajaran as $key => $value) {
          ?>
          <div class="col-lg-4">
            <div class="card card-primary card-outline">
              <img src="<?=base_url('assets/img/'.$value['img'])?>" style="max-height: 250px">
              <div class="card-header">
                <h5 class="m-0"><?=$value['nama']?></h5>
              </div>
              <div class="card-body">
                <p class="card-text">
                  <?=$value['detail']?>
                </p>
                <a href="<?=base_url('frontend/modul-pembelajaran/mulai/'.$value['id'])?>" class="btn btn-primary">Mulai Modul</a>
              </div>
            </div>
          </div>
          <?php
          }
          ?>
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <?php if ($this->session->flashdata('message')){?>
<!-- Modal -->
    <div class="modal fade" id="autoPopup" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Selamat</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?= $this->session->flashdata('message') ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
<?php
}
?>

<!-- Script untuk menampilkan modal otomatis -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var myModal = new bootstrap.Modal(document.getElementById('autoPopup'));
            myModal.show();
        });
    </script>

