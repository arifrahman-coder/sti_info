  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Pengaturan Kuesioner</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Pengaturan Kuesioner</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <div class="content">
      <div class="container-fluid">
        <?php if ($this->session->flashdata('message')) { ?>
          <?= $this->session->flashdata('message') ?>
        <?php } ?>

        <div class="row">
          <div class="col-lg-12">
            <div class="card card-primary card-outline">
              <div class="card-header">
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalTambahKategori">
                  <i class="fas fa-plus"></i> Tambah Kuesioner
                </button>
              </div>
              <div class="card-body">
                <table id="kategoriKuesionerTable" class="table table-bordered table-striped">
                  <thead>
                    <tr>
                      <th>No</th>
                      <th>Kuesioner</th>
                      <th>Status Akun</th>
                      <th>Urutan</th>
                      <th>Status</th>
                      <th>Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php $i = 1; foreach ($kategori as $item) { ?>
                      <tr>
                        <td><?= $i ?></td>
                        <td><?= htmlspecialchars($item['nama'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($item['status_nama'] ?: 'Semua Pengguna', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= (int) $item['urutan'] ?></td>
                        <td>
                          <?php if ((int) $item['status'] == 1) { ?>
                            <span class="badge badge-success">Aktif</span>
                          <?php } else { ?>
                            <span class="badge badge-secondary">Nonaktif</span>
                          <?php } ?>
                        </td>
                        <td>
                          <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#modalEditKategori<?= (int) $item['id'] ?>">
                            <i class="fas fa-edit"></i> Edit
                          </button>
                          <button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#modalHapusKategori<?= (int) $item['id'] ?>">
                            <i class="fas fa-trash"></i> Hapus
                          </button>
                        </td>
                      </tr>
                    <?php $i++; } ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <div class="col-lg-12">
            <div class="card card-primary card-outline">
              <div class="card-header">
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalTambahPertanyaan">
                  <i class="fas fa-plus"></i> Tambah Pernyataan
                </button>
              </div>
              <div class="card-body">
                <table id="pertanyaanKuesionerTable" class="table table-bordered table-striped">
                  <thead>
                    <tr>
                      <th>No</th>
                      <th>Kuesioner</th>
                      <th>Kode</th>
                      <th>Pernyataan</th>
                      <th>Tipe</th>
                      <th>Wajib</th>
                      <th>Urutan</th>
                      <th>Status</th>
                      <th>Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php $i = 1; foreach ($pertanyaan as $item) { ?>
                      <tr>
                        <td><?= $i ?></td>
                        <td><?= htmlspecialchars($item['kategori_nama'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($item['kode'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($item['pertanyaan'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($item['tipe'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= (int) $item['wajib'] == 1 ? 'Ya' : 'Tidak' ?></td>
                        <td><?= (int) $item['urutan'] ?></td>
                        <td>
                          <?php if ((int) $item['status'] == 1) { ?>
                            <span class="badge badge-success">Aktif</span>
                          <?php } else { ?>
                            <span class="badge badge-secondary">Nonaktif</span>
                          <?php } ?>
                        </td>
                        <td>
                          <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#modalEditPertanyaan<?= (int) $item['id'] ?>">
                            <i class="fas fa-edit"></i> Edit
                          </button>
                          <button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#modalHapusPertanyaan<?= (int) $item['id'] ?>">
                            <i class="fas fa-trash"></i> Hapus
                          </button>
                        </td>
                      </tr>
                    <?php $i++; } ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <div class="col-lg-12">
            <div class="card card-primary card-outline">
              <div class="card-header">
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalTambahOpsi">
                  <i class="fas fa-plus"></i> Tambah Opsi
                </button>
              </div>
              <div class="card-body">
                <table id="opsiKuesionerTable" class="table table-bordered table-striped">
                  <thead>
                    <tr>
                      <th>No</th>
                      <th>Pertanyaan</th>
                      <th>Label</th>
                      <th>Nilai Simpan</th>
                      <th>Urutan</th>
                      <th>Status</th>
                      <th>Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php $i = 1; foreach ($opsi as $item) { ?>
                      <tr>
                        <td><?= $i ?></td>
                        <td>
                          <strong><?= htmlspecialchars($item['pertanyaan_kode'] ?? '-', ENT_QUOTES, 'UTF-8') ?></strong>
                          <?= htmlspecialchars($item['pertanyaan_teks'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                        </td>
                        <td><?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($item['nilai'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= (int) $item['urutan'] ?></td>
                        <td>
                          <?php if ((int) $item['status'] == 1) { ?>
                            <span class="badge badge-success">Aktif</span>
                          <?php } else { ?>
                            <span class="badge badge-secondary">Nonaktif</span>
                          <?php } ?>
                        </td>
                        <td>
                          <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#modalEditOpsi<?= (int) $item['id'] ?>">
                            <i class="fas fa-edit"></i> Edit
                          </button>
                          <button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#modalHapusOpsi<?= (int) $item['id'] ?>">
                            <i class="fas fa-trash"></i> Hapus
                          </button>
                        </td>
                      </tr>
                    <?php $i++; } ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

<div class="modal fade" id="modalTambahKategori">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Tambah Kuesioner</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <form method="POST" action="<?= base_url('backend/kuesioner/proses_tambah_kategori') ?>">
        <div class="modal-body">
          <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
          <div class="form-row">
            <div class="form-group col-md-6">
              <label>Nama Kuesioner</label>
              <input type="text" name="nama" class="form-control" required>
            </div>
            <div class="form-group col-md-6">
              <label>Status Akun</label>
              <select name="id_status" class="form-control" required>
                <option value="0">Semua Pengguna</option>
                <?php foreach ($statusUser as $status) { ?>
                  <option value="<?= (int) $status['id'] ?>"><?= htmlspecialchars($status['nama'], ENT_QUOTES, 'UTF-8') ?></option>
                <?php } ?>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label>Petunjuk</label>
            <textarea name="deskripsi" class="form-control" rows="4"></textarea>
          </div>
          <div class="form-row">
            <div class="form-group col-md-4">
              <label>Label Pernyataan</label>
              <input type="text" name="label_pertanyaan" class="form-control" value="Pernyataan" required>
            </div>
            <div class="form-group col-md-4">
              <label>Urutan</label>
              <input type="number" name="urutan" class="form-control" value="0" min="0">
            </div>
            <div class="form-group col-md-4">
              <label>Status</label>
              <select name="status" class="form-control">
                <option value="1">Aktif</option>
                <option value="0">Nonaktif</option>
              </select>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
          <button type="submit" class="btn btn-primary">Tambah</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="modalTambahPertanyaan">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Tambah Pernyataan</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <form method="POST" action="<?= base_url('backend/kuesioner/proses_tambah_pertanyaan') ?>">
        <div class="modal-body">
          <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
          <div class="form-row">
            <div class="form-group col-md-6">
              <label>Kuesioner</label>
              <select name="kategori_id" class="form-control" required>
                <?php foreach ($kategori as $itemKategori) { ?>
                  <option value="<?= (int) $itemKategori['id'] ?>"><?= htmlspecialchars($itemKategori['nama'], ENT_QUOTES, 'UTF-8') ?></option>
                <?php } ?>
              </select>
            </div>
            <div class="form-group col-md-3">
              <label>Kode</label>
              <input type="text" name="kode" class="form-control" placeholder="M1" maxlength="50" required>
            </div>
            <div class="form-group col-md-3">
              <label>Tipe Input</label>
              <select name="tipe" class="form-control">
                <option value="radio">Radio</option>
                <option value="text">Teks</option>
                <option value="textarea">Teks Panjang</option>
                <option value="number">Angka</option>
                <option value="checkbox">Kotak Centang</option>
                <option value="month">Bulan dan Tahun</option>
                <option value="date">Tanggal</option>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label>Pernyataan</label>
            <textarea name="pertanyaan" class="form-control" rows="3" required></textarea>
          </div>
          <div class="form-row">
            <div class="form-group col-md-4">
              <label>Wajib</label>
              <select name="wajib" class="form-control">
                <option value="1">Ya</option>
                <option value="0">Tidak</option>
              </select>
            </div>
            <div class="form-group col-md-4">
              <label>Urutan</label>
              <input type="number" name="urutan" class="form-control" value="0" min="0">
            </div>
            <div class="form-group col-md-4">
              <label>Status</label>
              <select name="status" class="form-control">
                <option value="1">Aktif</option>
                <option value="0">Nonaktif</option>
              </select>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
          <button type="submit" class="btn btn-primary">Tambah</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="modalTambahOpsi">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Tambah Opsi</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <form method="POST" action="<?= base_url('backend/kuesioner/proses_tambah_opsi') ?>">
        <div class="modal-body">
          <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
          <div class="form-group">
            <label>Pertanyaan</label>
            <select name="pertanyaan_id" class="form-control" required>
              <?php foreach ($pertanyaan as $itemPertanyaan) { ?>
                <option value="<?= (int) $itemPertanyaan['id'] ?>">
                  <?= htmlspecialchars($itemPertanyaan['kode'].' - '.$itemPertanyaan['pertanyaan'], ENT_QUOTES, 'UTF-8') ?>
                </option>
              <?php } ?>
            </select>
          </div>
          <div class="form-row">
            <div class="form-group col-md-6">
              <label>Label</label>
              <input type="text" name="label" class="form-control" required>
            </div>
            <div class="form-group col-md-6">
              <label>Nilai Simpan</label>
              <input type="text" name="nilai" class="form-control" required>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group col-md-4">
              <label>Skor</label>
              <input type="number" name="skor" class="form-control" value="0">
            </div>
            <div class="form-group col-md-4">
              <label>Urutan</label>
              <input type="number" name="urutan" class="form-control" value="0" min="0">
            </div>
            <div class="form-group col-md-4">
              <label>Status</label>
              <select name="status" class="form-control">
                <option value="1">Aktif</option>
                <option value="0">Nonaktif</option>
              </select>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
          <button type="submit" class="btn btn-primary">Tambah</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php foreach ($kategori as $item) { ?>
  <div class="modal fade" id="modalEditKategori<?= (int) $item['id'] ?>">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Edit Kuesioner</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <form method="POST" action="<?= base_url('backend/kuesioner/proses_edit_kategori/'.$item['id']) ?>">
          <div class="modal-body">
            <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
            <div class="form-row">
              <div class="form-group col-md-6">
                <label>Nama Kuesioner</label>
                <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($item['nama'], ENT_QUOTES, 'UTF-8') ?>" required>
              </div>
              <div class="form-group col-md-6">
                <label>Status Akun</label>
                <select name="id_status" class="form-control" required>
                  <option value="0" <?= empty($item['id_status']) ? 'selected' : '' ?>>Semua Pengguna</option>
                  <?php foreach ($statusUser as $status) { ?>
                    <option value="<?= (int) $status['id'] ?>" <?= (int) ($item['id_status'] ?? 0) == (int) $status['id'] ? 'selected' : '' ?>>
                      <?= htmlspecialchars($status['nama'], ENT_QUOTES, 'UTF-8') ?>
                    </option>
                  <?php } ?>
                </select>
              </div>
            </div>
            <div class="form-group">
              <label>Petunjuk</label>
              <textarea name="deskripsi" class="form-control" rows="4"><?= htmlspecialchars($item['deskripsi'], ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>
            <div class="form-row">
              <div class="form-group col-md-4">
                <label>Label Pernyataan</label>
                <input type="text" name="label_pertanyaan" class="form-control" value="<?= htmlspecialchars($item['label_pertanyaan'], ENT_QUOTES, 'UTF-8') ?>" required>
              </div>
              <div class="form-group col-md-4">
                <label>Urutan</label>
                <input type="number" name="urutan" class="form-control" value="<?= (int) $item['urutan'] ?>" min="0">
              </div>
              <div class="form-group col-md-4">
                <label>Status</label>
                <select name="status" class="form-control">
                  <option value="1" <?= (int) $item['status'] == 1 ? 'selected' : '' ?>>Aktif</option>
                  <option value="0" <?= (int) $item['status'] == 0 ? 'selected' : '' ?>>Nonaktif</option>
                </select>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            <button type="submit" class="btn btn-primary">Simpan</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="modal fade" id="modalHapusKategori<?= (int) $item['id'] ?>">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Hapus Kuesioner</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <form method="POST" action="<?= base_url('backend/kuesioner/proses_hapus_kategori/'.$item['id']) ?>">
          <div class="modal-body">
            <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
            <p>Yakin ingin menghapus <strong><?= htmlspecialchars($item['nama'], ENT_QUOTES, 'UTF-8') ?></strong>?</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-danger">Hapus</button>
          </div>
        </form>
      </div>
    </div>
  </div>
<?php } ?>

<?php foreach ($pertanyaan as $item) { ?>
  <div class="modal fade" id="modalEditPertanyaan<?= (int) $item['id'] ?>">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Edit Pernyataan</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <form method="POST" action="<?= base_url('backend/kuesioner/proses_edit_pertanyaan/'.$item['id']) ?>">
          <div class="modal-body">
            <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
            <div class="form-row">
              <div class="form-group col-md-6">
                <label>Kuesioner</label>
                <select name="kategori_id" class="form-control" required>
                  <?php foreach ($kategori as $itemKategori) { ?>
                    <option value="<?= (int) $itemKategori['id'] ?>" <?= (int) $item['kategori_id'] == (int) $itemKategori['id'] ? 'selected' : '' ?>>
                      <?= htmlspecialchars($itemKategori['nama'], ENT_QUOTES, 'UTF-8') ?>
                    </option>
                  <?php } ?>
                </select>
              </div>
              <div class="form-group col-md-3">
                <label>Kode</label>
                <input type="text" name="kode" class="form-control" value="<?= htmlspecialchars($item['kode'], ENT_QUOTES, 'UTF-8') ?>" maxlength="50" required>
              </div>
              <div class="form-group col-md-3">
                <label>Tipe Input</label>
                <select name="tipe" class="form-control">
                  <?php foreach (array('radio' => 'Radio', 'checkbox' => 'Kotak Centang', 'text' => 'Teks', 'textarea' => 'Teks Panjang', 'number' => 'Angka', 'month' => 'Bulan dan Tahun', 'date' => 'Tanggal') as $value => $label) { ?>
                    <option value="<?= $value ?>" <?= $item['tipe'] == $value ? 'selected' : '' ?>><?= $label ?></option>
                  <?php } ?>
                </select>
              </div>
            </div>
            <div class="form-group">
              <label>Pernyataan</label>
              <textarea name="pertanyaan" class="form-control" rows="3" required><?= htmlspecialchars($item['pertanyaan'], ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>
            <div class="form-row">
              <div class="form-group col-md-4">
                <label>Wajib</label>
                <select name="wajib" class="form-control">
                  <option value="1" <?= (int) $item['wajib'] == 1 ? 'selected' : '' ?>>Ya</option>
                  <option value="0" <?= (int) $item['wajib'] == 0 ? 'selected' : '' ?>>Tidak</option>
                </select>
              </div>
              <div class="form-group col-md-4">
                <label>Urutan</label>
                <input type="number" name="urutan" class="form-control" value="<?= (int) $item['urutan'] ?>" min="0">
              </div>
              <div class="form-group col-md-4">
                <label>Status</label>
                <select name="status" class="form-control">
                  <option value="1" <?= (int) $item['status'] == 1 ? 'selected' : '' ?>>Aktif</option>
                  <option value="0" <?= (int) $item['status'] == 0 ? 'selected' : '' ?>>Nonaktif</option>
                </select>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            <button type="submit" class="btn btn-primary">Simpan</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="modal fade" id="modalHapusPertanyaan<?= (int) $item['id'] ?>">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Hapus Pernyataan</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <form method="POST" action="<?= base_url('backend/kuesioner/proses_hapus_pertanyaan/'.$item['id']) ?>">
          <div class="modal-body">
            <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
            <p>Yakin ingin menghapus pernyataan <strong><?= htmlspecialchars($item['kode'], ENT_QUOTES, 'UTF-8') ?></strong>?</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-danger">Hapus</button>
          </div>
        </form>
      </div>
    </div>
  </div>
<?php } ?>

<?php foreach ($opsi as $item) { ?>
  <div class="modal fade" id="modalEditOpsi<?= (int) $item['id'] ?>">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Edit Opsi</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <form method="POST" action="<?= base_url('backend/kuesioner/proses_edit_opsi/'.$item['id']) ?>">
          <div class="modal-body">
            <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
            <div class="form-group">
              <label>Pertanyaan</label>
              <select name="pertanyaan_id" class="form-control" required>
                <?php foreach ($pertanyaan as $itemPertanyaan) { ?>
                  <option value="<?= (int) $itemPertanyaan['id'] ?>" <?= (int) ($item['pertanyaan_id'] ?? 0) == (int) $itemPertanyaan['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($itemPertanyaan['kode'].' - '.$itemPertanyaan['pertanyaan'], ENT_QUOTES, 'UTF-8') ?>
                  </option>
                <?php } ?>
              </select>
            </div>
            <div class="form-row">
              <div class="form-group col-md-6">
                <label>Label</label>
                <input type="text" name="label" class="form-control" value="<?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8') ?>" required>
              </div>
              <div class="form-group col-md-6">
                <label>Nilai Simpan</label>
                <input type="text" name="nilai" class="form-control" value="<?= htmlspecialchars($item['nilai'], ENT_QUOTES, 'UTF-8') ?>" required>
              </div>
            </div>
            <div class="form-row">
              <div class="form-group col-md-4">
                <label>Skor</label>
                <input type="number" name="skor" class="form-control" value="<?= (int) $item['skor'] ?>">
              </div>
              <div class="form-group col-md-4">
                <label>Urutan</label>
                <input type="number" name="urutan" class="form-control" value="<?= (int) $item['urutan'] ?>" min="0">
              </div>
              <div class="form-group col-md-4">
                <label>Status</label>
                <select name="status" class="form-control">
                  <option value="1" <?= (int) $item['status'] == 1 ? 'selected' : '' ?>>Aktif</option>
                  <option value="0" <?= (int) $item['status'] == 0 ? 'selected' : '' ?>>Nonaktif</option>
                </select>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            <button type="submit" class="btn btn-primary">Simpan</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="modal fade" id="modalHapusOpsi<?= (int) $item['id'] ?>">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Hapus Opsi</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <form method="POST" action="<?= base_url('backend/kuesioner/proses_hapus_opsi/'.$item['id']) ?>">
          <div class="modal-body">
            <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
            <p>Yakin ingin menghapus opsi <strong><?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8') ?></strong>?</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-danger">Hapus</button>
          </div>
        </form>
      </div>
    </div>
  </div>
<?php } ?>

<script>
  $(function () {
    $("#kategoriKuesionerTable, #pertanyaanKuesionerTable, #opsiKuesionerTable").DataTable({
      "responsive": true,
      "lengthChange": false,
      "autoWidth": false
    });
  });
</script>
