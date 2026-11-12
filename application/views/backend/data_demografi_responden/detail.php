<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-7"><h1 class="m-0">Detail Data Kuesioner Responden</h1></div>
        <div class="col-sm-5">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="<?= base_url('backend/data-kuesioner-responden') ?>">Data Kuesioner Responden</a></li>
            <li class="breadcrumb-item active">Detail</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <section class="content">
    <div class="container-fluid">
      <?php if ($this->session->flashdata('message')) { ?><?= $this->session->flashdata('message') ?><?php } ?>
      <?php
        $idDemografi = (int) $submission['id_demografi'];
        $respondentType = !empty($submission['jenis_responden']) ? $submission['jenis_responden'] : ($submission['status_user_nama'] ?? '-');
        $respondentCode = !empty($submission['kode_responden']) ? $submission['kode_responden'] : (!empty($submission['no_responden']) ? $submission['no_responden'] : '-');
      ?>

      <div class="card card-primary card-outline">
        <div class="card-header">
          <a href="<?= base_url('backend/data-kuesioner-responden') ?>" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left mr-1"></i> Kembali</a>
          <a href="<?= base_url('backend/data-kuesioner-responden/edit/'.$idDemografi) ?>" class="btn btn-success btn-sm"><i class="fas fa-edit mr-1"></i> Edit</a>
          <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteDemographic"><i class="fas fa-trash mr-1"></i> Hapus</button>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-6">
              <table class="table table-sm table-borderless">
                <tr><th style="width:160px">Nama</th><td><?= htmlspecialchars($submission['nama'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td></tr>
                <tr><th>Email</th><td><?= htmlspecialchars($submission['email'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td></tr>
                <tr><th>No. HP</th><td><?= htmlspecialchars($submission['no_hp'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td></tr>
              </table>
            </div>
            <div class="col-md-6">
              <table class="table table-sm table-borderless">
                <tr><th style="width:160px">Status Akun</th><td><?= htmlspecialchars($submission['status_user_nama'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td></tr>
                <tr><th>Jenis Responden</th><td><?= htmlspecialchars($respondentType, ENT_QUOTES, 'UTF-8') ?></td></tr>
                <tr><th>Kode Responden</th><td><?= htmlspecialchars($respondentCode, ENT_QUOTES, 'UTF-8') ?></td></tr>
                <tr><th>Tanggal Isi</th><td><?= !empty($submission['created_at']) ? date('d-m-Y H:i', strtotime($submission['created_at'])) : '-' ?></td></tr>
              </table>
            </div>
          </div>

          <div class="table-responsive mt-3">
            <table class="table table-bordered table-striped">
              <thead><tr><th style="width:110px">Kode</th><th>Bagian Kuesioner</th><th>Pertanyaan</th><th>Jawaban</th></tr></thead>
              <tbody>
                <?php foreach ($answers as $answer) { ?>
                  <?php
                    $code = !empty($answer['kode']) ? $answer['kode'] : $answer['pertanyaan'];
                    $item = !empty($answer['item']) ? $answer['item'] : $answer['pertanyaan'];
                  ?>
                  <tr>
                    <td><?= htmlspecialchars($code, ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars(!empty($answer['kategori_nama']) ? $answer['kategori_nama'] : '-', ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($item, ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($answer['jawaban'], ENT_QUOTES, 'UTF-8') ?></td>
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

<div class="modal fade" id="deleteDemographic" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Hapus Data Kuesioner</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div>
    <form method="POST" action="<?= base_url('backend/data-kuesioner-responden/delete/'.$idDemografi) ?>">
      <div class="modal-body">
        <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
        Data beserta seluruh jawaban akan dihapus dan tidak dapat dipulihkan.
      </div>
      <div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button><button type="submit" class="btn btn-danger">Hapus</button></div>
    </form>
  </div></div>
</div>
