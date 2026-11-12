    <div class="sidebar">
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="<?= base_url('assets/AdminLTE/dist/img/default-150x150.png') ?>" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <?php
            $getUser = $this->db
              ->select('user.nama AS nama_user, status_user.nama AS nama_sts_user')
              ->from('user')
              ->join('status_user', 'status_user.id = user.id_status', 'inner')
              ->where('user.id', (int) $this->session->userdata('id_user'))
              ->get()
              ->row_array();
            $namaUser = htmlspecialchars($getUser['nama_user'] ?? '-', ENT_QUOTES, 'UTF-8');
            $statusUser = htmlspecialchars($getUser['nama_sts_user'] ?? '-', ENT_QUOTES, 'UTF-8');
            $controller = strtolower((string) $this->uri->segment(2));
            $moduleActive = $controller === 'modul_pembelajaran';
            $drugProfileActive = $controller === 'profil-penggunaan-obat' || $controller === 'profil_penggunaan_obat';
            $questionnaireResultActive = $controller === 'hasil-kuesioner' || $controller === 'hasil_kuesioner';
            $marsActive = $controller === 'kuesioner-mars' || $controller === 'kuesioner_mars';
            $drugInteractionActive = $controller === 'interaksi-obat' || $controller === 'interaksi_obat';
            $hasQuestionnaireResult = FALSE;
            if ($this->db->table_exists('demografi') && $this->db->table_exists('jawaban_kuesioner')) {
              $hasQuestionnaireResult = (int) $this->db
                ->from('demografi')
                ->where('demografi.id_user', (int) $this->session->userdata('id_user'))
                ->where('EXISTS (SELECT 1 FROM jawaban_kuesioner jawaban_sidebar WHERE jawaban_sidebar.id_demografi = demografi.id_demografi)', NULL, FALSE)
                ->count_all_results() > 0;
            }
          ?>
          <a href="#" class="d-block"><?= $namaUser ?> (<?= $statusUser ?>)</a>
        </div>
      </div>

      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <li class="nav-item">
            <a href="<?= base_url('frontend/dashboard') ?>" class="nav-link <?= $controller === 'dashboard' ? 'active' : '' ?>">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>Dashboard</p>
            </a>
          </li>
          <?php if ($hasQuestionnaireResult) { ?>
          <li class="nav-item">
            <a href="<?= base_url('frontend/hasil-kuesioner') ?>" class="nav-link <?= $questionnaireResultActive ? 'active' : '' ?>">
              <i class="nav-icon fas fa-clipboard-check"></i>
              <p>Hasil Pengisian</p>
            </a>
          </li>
          <?php } ?>
          <li class="nav-item">
            <a href="<?= base_url('frontend/kuesioner-mars') ?>" class="nav-link <?= $marsActive ? 'active' : '' ?>">
              <i class="nav-icon fas fa-clipboard-list"></i>
              <p>Kuisioner MARS</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="<?= base_url('frontend/profil-penggunaan-obat') ?>" class="nav-link <?= $drugProfileActive ? 'active' : '' ?>">
              <i class="nav-icon fas fa-pills"></i>
              <p>Profil Penggunaan Obat</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="<?= base_url('frontend/interaksi-obat') ?>" class="nav-link <?= $drugInteractionActive ? 'active' : '' ?>">
              <i class="nav-icon fas fa-capsules"></i>
              <p>Interaksi Obat</p>
            </a>
          </li>
          <li class="nav-item <?= $moduleActive ? 'menu-open' : '' ?>">
            <a href="#" class="nav-link <?= $moduleActive ? 'active' : '' ?>">
              <i class="nav-icon fas fa-book"></i>
              <p>Modul Pembelajaran<i class="right fas fa-angle-left"></i></p>
            </a>
            <ul class="nav nav-treeview">
              <?php foreach ($this->db->get('penyakit')->result_array() as $value) { ?>
                <li class="nav-item">
                  <a href="<?= base_url('frontend/modul_pembelajaran/index/'.$value['id']) ?>" class="nav-link">
                    <i class="<?= htmlspecialchars($value['icon'], ENT_QUOTES, 'UTF-8') ?>"></i>
                    <p><?= htmlspecialchars($value['nama'], ENT_QUOTES, 'UTF-8') ?></p>
                  </a>
                </li>
              <?php } ?>
            </ul>
          </li>
        </ul>
      </nav>
    </div>
  </aside>
