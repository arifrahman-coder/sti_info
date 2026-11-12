

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Dashboard</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Dashboard</li>
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
          <!-- /.col-md-6 -->
          <div class="col-lg-12">
            <div class="card card-primary card-outline">
              <div class="card-header">
                <h5 class="m-0">Dashboard Admin</h5>
              </div>
              <div class="card-body">
                <h6 class="card-title">Pengelolaan STIInfo</h6>
                <p class="card-text">Kelola data responden beserta seluruh jawaban kuesioner yang dikirim melalui modal onboarding.</p>
                <a href="<?= base_url('backend/data-kuesioner-responden') ?>" class="btn btn-primary">
                  <i class="fas fa-address-card"></i> Data Kuesioner Responden
                </a>
                <a href="<?= base_url('backend/data-kuesioner-responden/export') ?>" class="btn btn-outline-success">
                  <i class="fas fa-file-excel"></i> Download Excel
                </a>
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
