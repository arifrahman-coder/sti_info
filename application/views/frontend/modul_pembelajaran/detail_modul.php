<?php
$ebookField = isset($listSubBabModul['field']) ? (string) $listSubBabModul['field'] : '';
$isEbookPdf = strtolower(pathinfo($ebookField, PATHINFO_EXTENSION)) === 'pdf';
?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<?php if ($isEbookPdf) { ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.9.179/pdf.min.js"></script>
<?php } ?>
  <style type="text/css">
    .content-pembelajaran .content img{
      width: 100%;
      margin :0;
      padding: 0;
    }
    .content-pembelajaran .content p {
      text-align: justify;
    }
    .content-pembelajaran .card {
      margin-bottom: 10px;
    }
    .ebook-reader {
      --reader-ink: #e8edf7;
      --reader-muted: #aeb9cc;
      border: 1px solid #273246;
      border-radius: 14px;
      overflow: hidden;
      background: #111827;
      box-shadow: 0 18px 45px rgba(15, 23, 42, .2);
      color: var(--reader-ink);
      outline: none;
    }

    .ebook-toolbar,
    .ebook-navigation {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
      padding: 12px 16px;
      background: linear-gradient(180deg, #1f2937 0%, #172033 100%);
    }

    .ebook-toolbar {
      border-bottom: 1px solid rgba(255,255,255,.08);
    }

    .ebook-title {
      min-width: 0;
      display: flex;
      align-items: center;
      gap: 10px;
      font-weight: 700;
    }

    .ebook-title span {
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
    }

    .ebook-toolbar-actions,
    .ebook-navigation-actions {
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .ebook-tool-button,
    .ebook-nav-button,
    .ebook-turn-zone {
      border: 0;
      cursor: pointer;
    }

    .ebook-tool-button {
      width: 36px;
      height: 36px;
      border-radius: 8px;
      color: #eef2ff;
      background: rgba(255,255,255,.09);
    }

    .ebook-tool-button:hover,
    .ebook-tool-button:focus {
      background: rgba(255,255,255,.17);
      outline: 2px solid rgba(96,165,250,.5);
    }

    .ebook-zoom-value {
      min-width: 48px;
      text-align: center;
      color: var(--reader-muted);
      font-size: 13px;
      font-weight: 700;
    }

    .ebook-stage {
      position: relative;
      height: clamp(420px, 72vh, 820px);
      overflow: auto;
      overscroll-behavior: contain;
      background:
        radial-gradient(circle at 50% 38%, rgba(71,85,105,.55), transparent 45%),
        linear-gradient(135deg, #0f172a, #1e293b 52%, #111827);
    }

    .ebook-stage-inner {
      min-width: 100%;
      min-height: 100%;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 34px;
    }

    .ebook-book {
      position: relative;
      flex: 0 0 auto;
      perspective: 2600px;
      transform-style: preserve-3d;
      filter: drop-shadow(0 22px 24px rgba(0,0,0,.42));
    }

    .ebook-book::before {
      content: '';
      position: absolute;
      z-index: 12;
      left: 50%;
      top: 0;
      bottom: 0;
      width: 3px;
      transform: translateX(-50%);
      background: linear-gradient(90deg, rgba(0,0,0,.38), rgba(255,255,255,.28), rgba(0,0,0,.28));
      box-shadow: 0 0 12px rgba(0,0,0,.32);
      pointer-events: none;
    }

    .ebook-static-page,
    .ebook-flip-leaf,
    .ebook-leaf-face {
      position: absolute;
      top: 0;
      width: 50%;
      height: 100%;
    }

    .ebook-static-page {
      overflow: hidden;
      background: #fffdf8;
    }

    .ebook-static-page-left {
      left: 0;
      border-radius: 8px 1px 1px 8px;
      box-shadow: inset -18px 0 25px -24px rgba(15,23,42,.8);
    }

    .ebook-static-page-right {
      right: 0;
      border-radius: 1px 8px 8px 1px;
      box-shadow: inset 18px 0 25px -24px rgba(15,23,42,.8);
    }

    .ebook-page-canvas,
    .ebook-leaf-canvas {
      display: block;
      width: 100%;
      height: 100%;
      background: #fffdf8;
    }

    .ebook-page-number {
      position: absolute;
      z-index: 3;
      bottom: 8px;
      min-width: 28px;
      padding: 3px 7px;
      border-radius: 12px;
      color: #475569;
      background: rgba(255,255,255,.88);
      box-shadow: 0 1px 5px rgba(15,23,42,.15);
      font-size: 12px;
      font-weight: 700;
      text-align: center;
    }

    .ebook-static-page-left .ebook-page-number { left: 10px; }
    .ebook-static-page-right .ebook-page-number { right: 10px; }

    .ebook-flip-leaf {
      z-index: 10;
      display: none;
      transform-style: preserve-3d;
    }

    .ebook-flip-leaf.from-right {
      right: 0;
      transform-origin: left center;
    }

    .ebook-flip-leaf.from-left {
      left: 0;
      transform-origin: right center;
    }

    .ebook-leaf-face {
      inset: 0;
      width: 100%;
      overflow: hidden;
      background: #fffdf8;
      backface-visibility: hidden;
      -webkit-backface-visibility: hidden;
    }

    .ebook-leaf-back { transform: rotateY(180deg); }

    .ebook-flip-leaf.is-turning-forward {
      display: block;
      animation: ebookTurnForward 900ms cubic-bezier(.55,.04,.35,1) forwards;
    }

    .ebook-flip-leaf.is-turning-backward {
      display: block;
      animation: ebookTurnBackward 900ms cubic-bezier(.55,.04,.35,1) forwards;
    }

    @keyframes ebookTurnForward {
      0% { transform: rotateY(0deg); filter: brightness(1); }
      48% { filter: brightness(.76); }
      100% { transform: rotateY(-180deg); filter: brightness(1); }
    }

    @keyframes ebookTurnBackward {
      0% { transform: rotateY(0deg); filter: brightness(1); }
      48% { filter: brightness(.76); }
      100% { transform: rotateY(180deg); filter: brightness(1); }
    }

    .ebook-turn-zone {
      position: sticky;
      z-index: 20;
      top: 50%;
      width: 44px;
      height: 72px;
      margin-top: -36px;
      border-radius: 10px;
      color: #fff;
      background: rgba(15,23,42,.72);
      box-shadow: 0 8px 18px rgba(0,0,0,.24);
      backdrop-filter: blur(5px);
    }

    .ebook-turn-zone:hover,
    .ebook-turn-zone:focus { background: rgba(37,99,235,.9); }
    .ebook-turn-zone-left { float: left; left: 12px; }
    .ebook-turn-zone-right { float: right; right: 12px; }

    .ebook-reader button:disabled {
      opacity: .38;
      cursor: not-allowed;
    }

    .ebook-loading,
    .ebook-error {
      position: absolute;
      z-index: 30;
      inset: 0;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 12px;
      padding: 24px;
      text-align: center;
      background: rgba(15,23,42,.88);
    }

    .ebook-error { display: none; color: #fecaca; }

    .ebook-navigation {
      border-top: 1px solid rgba(255,255,255,.08);
      flex-wrap: wrap;
    }

    .ebook-nav-button {
      min-width: 112px;
      padding: 9px 14px;
      border-radius: 8px;
      color: #fff;
      background: #2563eb;
      font-weight: 700;
    }

    .ebook-nav-button:hover,
    .ebook-nav-button:focus { background: #1d4ed8; }

    .ebook-status-wrap {
      flex: 1 1 220px;
      text-align: center;
    }

    .ebook-status {
      display: block;
      margin-bottom: 6px;
      color: var(--reader-ink);
      font-size: 14px;
      font-weight: 700;
    }

    .ebook-progress {
      height: 5px;
      overflow: hidden;
      border-radius: 999px;
      background: rgba(255,255,255,.12);
    }

    .ebook-progress-bar {
      width: 0;
      height: 100%;
      border-radius: inherit;
      background: linear-gradient(90deg, #60a5fa, #22d3ee);
      transition: width .25s ease;
    }

    .ebook-reader:fullscreen,
    .ebook-reader:-webkit-full-screen {
      width: 100vw;
      height: 100vh;
      border: 0;
      border-radius: 0;
    }

    .ebook-reader:fullscreen .ebook-stage,
    .ebook-reader:-webkit-full-screen .ebook-stage {
      height: calc(100vh - 126px);
    }

    @media (max-width: 767.98px) {
      .ebook-toolbar { align-items: flex-start; }
      .ebook-title { max-width: 52%; }
      .ebook-stage { height: clamp(360px, 58vh, 580px); }
      .ebook-stage-inner { padding: 22px 14px; }
      .ebook-toolbar-actions { gap: 5px; }
      .ebook-tool-button { width: 32px; height: 32px; }
      .ebook-zoom-value { display: none; }
      .ebook-navigation { justify-content: center; }
      .ebook-status-wrap { order: -1; flex-basis: 100%; }
      .ebook-nav-button { flex: 1 1 120px; }
      .ebook-turn-zone { display: none; }
    }

    @media (prefers-reduced-motion: reduce) {
      .ebook-flip-leaf.is-turning-forward,
      .ebook-flip-leaf.is-turning-backward { animation-duration: 1ms; }
    }
  </style>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper content-pembelajaran">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Pembelajaran Modul Penyakit HIV/AIDS</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item">Modul HIV/AIDS</li>
              <li class="breadcrumb-item active">Detail Modul HIV/AIDS</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
      <div class="container-fluid">
        <?php if ($this->session->flashdata('message')) { ?>
          <?= $this->session->flashdata('message') ?>
        <?php } ?>
        <div class="row">
          <!-- /.col-md-6 -->
          <?php
          if($jenisModul == 'bab'){
          ?>
          <div class="col-lg-12">
            <div class="card card-primary card-outline">
              <div class="card-header">
              </div>
              <div class="card-body">
                <p class="card-text">
                  <?=$getDetailModul['field']?>
                </p>
                <?php
                $cekPembelajaran = $this->db->query("SELECT count(id) as jml FROM proses_belajar WHERE id_user = ".$this->session->userdata('id_user')." AND id_modul_pembelajaran = ".$getDetailModul['id_modul_pembelajaran']." AND id_list_detail_modul = 0 AND status='selsai' ")->row_array();
                if($cekPembelajaran['jml'] == 0){
                ?>
                <a href="<?=base_url('frontend/modul_pembelajaran/prosesBelajar/'.$getDetailModul['id_modul_pembelajaran'].'/'.$getDetailModul['id'].'/0/'.$this->session->userdata('id_user').'/mulai_bljr')?>" class="btn btn-primary">Pelajari</a>
                <?php
                }
                ?>
              </div>
            </div>
          </div>
          <?php
          }
          elseif($jenisModul == 'sub_bab'){
          ?>
          <div class="col-lg-12">
            <div class="card card-primary card-outline">
              <div class="card-header">
                <h5 class="m-0"><?=$listSubBabModul['title']?></h5>
              </div>
              <div class="card-body">
                <?php
                if($listSubBabModul['status'] != 'soal' ){
                ?>
                <?php if ($isEbookPdf) { ?>
                  <div class="ebook-reader" id="ebookReader" tabindex="0" aria-label="Pembaca e-book <?= htmlspecialchars($listSubBabModul['title'], ENT_QUOTES, 'UTF-8') ?>">
                    <div class="ebook-toolbar">
                      <div class="ebook-title">
                        <i class="fas fa-book-open" aria-hidden="true"></i>
                        <span><?= htmlspecialchars($listSubBabModul['title'], ENT_QUOTES, 'UTF-8') ?></span>
                      </div>
                      <div class="ebook-toolbar-actions" aria-label="Alat pembaca e-book">
                        <button type="button" class="ebook-tool-button" id="ebookZoomOut" title="Perkecil" aria-label="Perkecil">
                          <i class="fas fa-search-minus" aria-hidden="true"></i>
                        </button>
                        <span class="ebook-zoom-value" id="ebookZoomValue">100%</span>
                        <button type="button" class="ebook-tool-button" id="ebookZoomIn" title="Perbesar" aria-label="Perbesar">
                          <i class="fas fa-search-plus" aria-hidden="true"></i>
                        </button>
                        <button type="button" class="ebook-tool-button" id="ebookResetZoom" title="Sesuaikan layar" aria-label="Sesuaikan ukuran buku dengan layar">
                          <i class="fas fa-compress-arrows-alt" aria-hidden="true"></i>
                        </button>
                        <button type="button" class="ebook-tool-button" id="ebookFullscreen" title="Layar penuh" aria-label="Buka layar penuh">
                          <i class="fas fa-expand" aria-hidden="true"></i>
                        </button>
                      </div>
                    </div>

                    <div class="ebook-stage" id="ebookStage">
                      <button type="button" class="ebook-turn-zone ebook-turn-zone-left" id="ebookStagePrevious" aria-label="Buka lembar sebelumnya">
                        <i class="fas fa-chevron-left" aria-hidden="true"></i>
                      </button>
                      <button type="button" class="ebook-turn-zone ebook-turn-zone-right" id="ebookStageNext" aria-label="Buka lembar berikutnya">
                        <i class="fas fa-chevron-right" aria-hidden="true"></i>
                      </button>

                      <div class="ebook-stage-inner" id="ebookStageInner">
                        <div class="ebook-book" id="ebookBook">
                          <div class="ebook-static-page ebook-static-page-left">
                            <canvas class="ebook-page-canvas" id="ebookLeftCanvas"></canvas>
                            <span class="ebook-page-number" id="ebookLeftPageNumber"></span>
                          </div>
                          <div class="ebook-static-page ebook-static-page-right">
                            <canvas class="ebook-page-canvas" id="ebookRightCanvas"></canvas>
                            <span class="ebook-page-number" id="ebookRightPageNumber"></span>
                          </div>

                          <div class="ebook-flip-leaf" id="ebookFlipLeaf" aria-hidden="true">
                            <div class="ebook-leaf-face ebook-leaf-front">
                              <canvas class="ebook-leaf-canvas" id="ebookFlipFrontCanvas"></canvas>
                            </div>
                            <div class="ebook-leaf-face ebook-leaf-back">
                              <canvas class="ebook-leaf-canvas" id="ebookFlipBackCanvas"></canvas>
                            </div>
                          </div>
                        </div>
                      </div>

                      <div class="ebook-loading" id="ebookLoading">
                        <i class="fas fa-circle-notch fa-spin" aria-hidden="true"></i>
                        <span>Menyiapkan lembar buku…</span>
                      </div>
                      <div class="ebook-error" id="ebookError" role="alert"></div>
                    </div>

                    <div class="ebook-navigation">
                      <button type="button" class="ebook-nav-button" id="ebookPrevious">
                        <i class="fas fa-arrow-left mr-1" aria-hidden="true"></i> Sebelumnya
                      </button>
                      <div class="ebook-status-wrap">
                        <span class="ebook-status" id="ebookStatus" aria-live="polite">Memuat buku…</span>
                        <div class="ebook-progress" aria-hidden="true">
                          <div class="ebook-progress-bar" id="ebookProgressBar"></div>
                        </div>
                      </div>
                      <button type="button" class="ebook-nav-button" id="ebookNext">
                        Berikutnya <i class="fas fa-arrow-right ml-1" aria-hidden="true"></i>
                      </button>
                    </div>
                  </div>
                  <p class="small text-muted text-center mt-2 mb-0">
                    Gunakan tombol, tombol panah pada keyboard, atau geser layar untuk membalik lembar.
                  </p>
                <?php } else { ?>
                  <div class="card-text"><?= $listSubBabModul['field'] ?></div>
                <?php } ?>
                <?php
                } else{
                ?>
                <form method="POST" action="<?=base_url('/frontend/modul_pembelajaran/prosesJawaban/'.$getsDataLink['id_modul'].'/'.$getsDataLink['id_sub_modul'].'/'.$prosesBelajarGetID)?>">
                  <input type="hidden" name="<?=$this->security->get_csrf_token_name();?>" value="<?=$this->security->get_csrf_hash();?>" style="display: none">
                  <?php
                  $abjad = array("A","B","C","D");
                  $j=1;
                  foreach ($getSoal as $key => $value) {
                  ?>
                  <p class="card-text">
                  <?=$j?>. <?=$value['pertanyaan']?>
                  <input type="hidden" name="soal_id[]" value="<?=$value['id']?>">
                  <?php
                  $queryPilihan = $this->db->query("SELECT * FROM pilihan_jawaban WHERE soal_id = ".$value['id']." ")->result_array();
                  $k=0;
                  foreach ($queryPilihan as $key => $value_detail) {
                  ?>
                  <p><input type="checkbox" name="pilihan_jawaban_id[]" value="<?=$value_detail['id'].'-'.$value_detail['is_benar']?>"> <?=$abjad[$k]?>. <?=$value_detail['pilihan']?>
                  </p>
                  <?php
                  $k++;
                  }
                  ?>
                  </p>
                  <?php
                  $j++;
                  }
                  ?>
                  <input type="submit" value="KIRIM JAWABAN" class="btn btn-primary">
                  <?php
                  if($getNilai !=""){
                  ?>
                  <h3>HASIL NILAI : <?=$getNilai?></h3>
                  <?php
                  } else{}
                  ?>
                </form>
                <?php
                }
                $cekPembelajaran = $this->db->query("SELECT count(id) as jml FROM proses_belajar WHERE id_user = ".$this->session->userdata('id_user')." AND id_modul_pembelajaran = ".$getDetailModul['id_modul_pembelajaran']." AND id_list_detail_modul = ".$getsDataLink['id_sub_modul']." AND status='proses_belajar' ")->row_array();
                if($cekPembelajaran['jml'] >= 1){
                  if($listSubBabModul['status'] != 'final'){
                ?>
                <a href="<?=base_url('frontend/modul_pembelajaran/prosesBelajar/'.(int) $getsDataLink['id_modul'].'/'.(int) $listSubBabModul['id_detail_modul'].'/'.(int) $getsDataLink['id_sub_modul'].'/'.(int) $this->session->userdata('id_user').'/prs_bljr')?>" class="btn btn-primary">
                  <?= empty($createLinkListDM) ? 'Lanjut ke Post-Test' : 'Selanjutnya' ?>
                </a>
                <?php
                  }
                  else if($listSubBabModul['status'] == 'final'){
                ?>
                <a href="<?=base_url('frontend/modul_pembelajaran/prosesBelajar/'.(int) $getsDataLink['id_modul'].'/'.(int) $listSubBabModul['id_detail_modul'].'/'.(int) $getsDataLink['id_sub_modul'].'/'.(int) $this->session->userdata('id_user').'/selsai_bljr')?>" class="btn btn-primary">Selesaikan Pelatihan</a>
                <?php
                }}
                ?>
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
  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
    <div class="p-3">
      <h5>Daftar Materi</h5>
    </div>
    <ul class="nav flex-column">
      <li class="nav-item">
        <a class="nav-link" href="<?= base_url('frontend/modul-pembelajaran/tes/'.(int) $getsDataLink['id_modul'].'/pre_test') ?>">
          <i class="fas fa-clipboard-check mr-1"></i> Pre-Test
          <?php if (!empty($testComparison['pre_test'])) { ?>
            <span class="badge badge-success float-right"><?= number_format((float) $testComparison['pre_test']['nilai'], 2, ',', '.') ?></span>
          <?php } ?>
        </a>
      </li>
      <?php
      $i=0;
      foreach ($listDetailModul as $key => $value) {
        if($value['jenis_modul'] == 'pendahuluan'){
      ?>
      <li class="nav-item">
        <a class="nav-link" href="<?=base_url('frontend/modul_pembelajaran/detail_modul/'.$value['id_modul_pembelajaran'].'/0/bab')?>">
          <?=$value['title']?>
        </a>
      </li>
      <?php
        }
        else{
      ?>
        <li class="nav-item">
        <a class="nav-link" href="#aboutSubmenu<?=$i?>" data-bs-toggle="collapse" aria-expanded="false" aria-controls="aboutSubmenu<?=$i?>">
          <?=$value['title']?> <i class="fas fa-chevron-right"></i>
        </a>
        <div class="collapse" id="aboutSubmenu<?=$i?>">
          <ul class="nav flex-column">
            <?php
            $getlistDetailModul = $this->db->query("SELECT * FROM list_detail_modul WHERE id_detail_modul = ".$value['id']." ")->result_array();
            foreach ($getlistDetailModul as $key => $list_detail_value) {
              $cekPembelajaran = $this->db->query("SELECT count(id) as jml FROM proses_belajar WHERE id_user = ".$this->session->userdata('id_user')." AND id_modul_pembelajaran = ".$value['id_modul_pembelajaran']." AND id_list_detail_modul = ".$list_detail_value['id']." AND status !='' ")->row_array();
              if($cekPembelajaran['jml'] != 0){
            ?>
            <li class="nav-item">
                <a class="nav-link" href="<?=base_url('frontend/modul_pembelajaran/detail_modul/'.$value['id_modul_pembelajaran'].'/'.$list_detail_value['id'].'/sub_bab')?>"><i class="fas fa-virus"></i> <?=$list_detail_value['title']?></a>
            </li>
            <?php
            }
            else{
            ?>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="modal" data-bs-target="#myModal-blmBljr"><i class="fas fa-virus"></i> <?=$list_detail_value['title']?></a>
            </li>
            <?php
            }
            }
            ?>
          </ul>
        </div>
      </li>
      <?php
        }
        $i++;
        }
      ?>

      <li class="nav-item">
        <?php if ($canTakePostTest || !empty($testComparison['post_test'])) { ?>
          <a class="nav-link" href="<?= base_url('frontend/modul-pembelajaran/tes/'.(int) $getsDataLink['id_modul'].'/post_test') ?>">
            <i class="fas fa-flag-checkered mr-1"></i> Post-Test
            <?php if (!empty($testComparison['post_test'])) { ?>
              <span class="badge badge-success float-right"><?= number_format((float) $testComparison['post_test']['nilai'], 2, ',', '.') ?></span>
            <?php } ?>
          </a>
        <?php } else { ?>
          <a class="nav-link" data-bs-toggle="modal" data-bs-target="#myModal-blmBljr">
            <i class="fas fa-lock mr-1"></i> Post-Test
          </a>
        <?php } ?>
      </li>


    </ul>
  </aside>

  <!-- Modal belum daftar pembelajaran -->
<div class="modal" id="myModal-blmBljr">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Info</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
        Silahkan Pelajari Modul Dari Awal
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
      </div>

    </div>
  </div>
</div>

<!-- Modal Puzzle -->
<div class="modal fade" id="puzzleModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Puzzle Check!</h5>
      </div>
      <div class="modal-body text-center">
        <p>Susun potongan gambar berikut agar benar untuk melanjutkan:</p>
        <div id="puzzle-container" style="display:flex;flex-wrap:wrap;width:300px;margin:auto"></div>
        <p id="puzzle-status" class="mt-3 text-danger fw-bold"></p>
      </div>
    </div>
  </div>
</div>

<?php if ($isEbookPdf) { ?>
<script>
(async function () {
  const reader = document.getElementById('ebookReader');
  if (!reader) return;

  const PDF_URL = <?= json_encode(base_url('assets/'.$ebookField), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
  const stage = document.getElementById('ebookStage');
  const stageInner = document.getElementById('ebookStageInner');
  const book = document.getElementById('ebookBook');
  const flipLeaf = document.getElementById('ebookFlipLeaf');
  const leftCanvas = document.getElementById('ebookLeftCanvas');
  const rightCanvas = document.getElementById('ebookRightCanvas');
  const flipFrontCanvas = document.getElementById('ebookFlipFrontCanvas');
  const flipBackCanvas = document.getElementById('ebookFlipBackCanvas');
  const leftPageNumber = document.getElementById('ebookLeftPageNumber');
  const rightPageNumber = document.getElementById('ebookRightPageNumber');
  const loading = document.getElementById('ebookLoading');
  const errorBox = document.getElementById('ebookError');
  const statusText = document.getElementById('ebookStatus');
  const progressBar = document.getElementById('ebookProgressBar');
  const zoomValue = document.getElementById('ebookZoomValue');
  const previousButtons = [document.getElementById('ebookPrevious'), document.getElementById('ebookStagePrevious')];
  const nextButtons = [document.getElementById('ebookNext'), document.getElementById('ebookStageNext')];
  const fullscreenButton = document.getElementById('ebookFullscreen');

  let pdfDocument = null;
  let totalPages = 0;
  let currentLeftPage = 0;
  let pageAspectRatio = 1.414;
  let zoom = 1;
  let busy = true;
  let resizeTimer = null;
  let pointerStart = null;
  const renderTasks = new WeakMap();

  function showError(message) {
    loading.style.display = 'none';
    errorBox.textContent = message;
    errorBox.style.display = 'flex';
    busy = false;
    updateControls();
  }

  function updateBookLayout() {
    const horizontalPadding = stage.clientWidth < 768 ? 28 : 96;
    const baseWidth = Math.min(1080, Math.max(290, stage.clientWidth - horizontalPadding));
    const bookWidth = Math.round(baseWidth * zoom);
    const bookHeight = Math.round((bookWidth * pageAspectRatio) / 2);

    book.style.width = bookWidth + 'px';
    book.style.height = bookHeight + 'px';
    stageInner.style.width = Math.max(stage.clientWidth, bookWidth + horizontalPadding) + 'px';
    stageInner.style.height = Math.max(stage.clientHeight, bookHeight + 58) + 'px';
    zoomValue.textContent = Math.round(zoom * 100) + '%';
  }

  function paintBlankPage(canvas) {
    const pixelRatio = Math.min(window.devicePixelRatio || 1, 2);
    const width = Math.max(1, Math.round((book.clientWidth / 2) * pixelRatio));
    const height = Math.max(1, Math.round(book.clientHeight * pixelRatio));
    canvas.width = width;
    canvas.height = height;
    const context = canvas.getContext('2d');
    context.fillStyle = '#f5f0e6';
    context.fillRect(0, 0, width, height);
  }

  async function renderPage(pageNumber, canvas) {
    const previousTask = renderTasks.get(canvas);
    if (previousTask) {
      previousTask.cancel();
      try { await previousTask.promise; } catch (cancelError) {}
    }

    if (pageNumber < 1 || pageNumber > totalPages) {
      paintBlankPage(canvas);
      return;
    }

    const page = await pdfDocument.getPage(pageNumber);
    const naturalViewport = page.getViewport({ scale: 1 });
    const pixelRatio = Math.min(window.devicePixelRatio || 1, 2);
    const targetWidth = Math.max(160, book.clientWidth / 2);
    const viewport = page.getViewport({ scale: (targetWidth / naturalViewport.width) * pixelRatio });
    canvas.width = Math.ceil(viewport.width);
    canvas.height = Math.ceil(viewport.height);

    const task = page.render({
      canvasContext: canvas.getContext('2d'),
      viewport: viewport
    });
    renderTasks.set(canvas, task);

    try {
      await task.promise;
    } catch (renderError) {
      if (!renderError || renderError.name !== 'RenderingCancelledException') throw renderError;
    } finally {
      if (renderTasks.get(canvas) === task) renderTasks.delete(canvas);
    }
  }

  function updateControls() {
    const canGoPrevious = !busy && currentLeftPage > 0;
    const canGoNext = !busy && currentLeftPage + 2 <= totalPages;
    previousButtons.forEach(function (button) { if (button) button.disabled = !canGoPrevious; });
    nextButtons.forEach(function (button) { if (button) button.disabled = !canGoNext; });

    leftPageNumber.textContent = currentLeftPage >= 1 && currentLeftPage <= totalPages ? currentLeftPage : '';
    rightPageNumber.textContent = currentLeftPage + 1 <= totalPages ? currentLeftPage + 1 : '';

    if (!totalPages) {
      statusText.textContent = 'Memuat buku…';
      progressBar.style.width = '0%';
      return;
    }

    if (currentLeftPage === 0) {
      statusText.textContent = 'Sampul • Halaman 1 dari ' + totalPages;
    } else {
      const rightPage = Math.min(currentLeftPage + 1, totalPages);
      statusText.textContent = currentLeftPage === rightPage
        ? 'Halaman ' + currentLeftPage + ' dari ' + totalPages
        : 'Halaman ' + currentLeftPage + '–' + rightPage + ' dari ' + totalPages;
    }

    const lastVisiblePage = Math.min(totalPages, Math.max(1, currentLeftPage + 1));
    progressBar.style.width = ((lastVisiblePage / totalPages) * 100) + '%';
  }

  async function renderCurrentSpread() {
    await Promise.all([
      renderPage(currentLeftPage, leftCanvas),
      renderPage(currentLeftPage + 1, rightCanvas)
    ]);
    leftPageNumber.style.visibility = '';
    rightPageNumber.style.visibility = '';
    updateControls();
  }

  function waitForLeafAnimation() {
    return new Promise(function (resolve) {
      let finished = false;
      const finish = function () {
        if (finished) return;
        finished = true;
        resolve();
      };
      flipLeaf.addEventListener('animationend', finish, { once: true });
      window.setTimeout(finish, 1050);
    });
  }

  async function turnPage(direction) {
    const targetLeftPage = direction === 'forward' ? currentLeftPage + 2 : currentLeftPage - 2;
    if (busy || targetLeftPage < 0 || targetLeftPage > totalPages) return;

    busy = true;
    updateControls();
    leftPageNumber.style.visibility = 'hidden';
    rightPageNumber.style.visibility = 'hidden';

    try {
      flipLeaf.className = 'ebook-flip-leaf ' + (direction === 'forward' ? 'from-right' : 'from-left');

      if (direction === 'forward') {
        await Promise.all([
          renderPage(currentLeftPage, leftCanvas),
          renderPage(targetLeftPage + 1, rightCanvas),
          renderPage(currentLeftPage + 1, flipFrontCanvas),
          renderPage(targetLeftPage, flipBackCanvas)
        ]);
      } else {
        await Promise.all([
          renderPage(targetLeftPage, leftCanvas),
          renderPage(currentLeftPage + 1, rightCanvas),
          renderPage(currentLeftPage, flipFrontCanvas),
          renderPage(targetLeftPage + 1, flipBackCanvas)
        ]);
      }

      void flipLeaf.offsetWidth;
      flipLeaf.classList.add(direction === 'forward' ? 'is-turning-forward' : 'is-turning-backward');
      await waitForLeafAnimation();
      currentLeftPage = targetLeftPage;
      flipLeaf.className = 'ebook-flip-leaf';
      await renderCurrentSpread();
    } catch (turnError) {
      console.error(turnError);
      showError('Lembar buku gagal dibuka. Silakan muat ulang halaman.');
      return;
    } finally {
      busy = false;
      leftPageNumber.style.visibility = '';
      rightPageNumber.style.visibility = '';
      updateControls();
    }
  }

  async function setZoom(nextZoom) {
    if (busy || !pdfDocument) return;
    busy = true;
    updateControls();
    zoom = Math.min(2, Math.max(.75, nextZoom));
    updateBookLayout();
    try {
      await renderCurrentSpread();
    } finally {
      busy = false;
      updateControls();
    }
  }

  async function toggleFullscreen() {
    try {
      if (!document.fullscreenElement && !document.webkitFullscreenElement) {
        if (reader.requestFullscreen) await reader.requestFullscreen();
        else if (reader.webkitRequestFullscreen) reader.webkitRequestFullscreen();
      } else if (document.exitFullscreen) {
        await document.exitFullscreen();
      } else if (document.webkitExitFullscreen) {
        document.webkitExitFullscreen();
      }
    } catch (fullscreenError) {
      console.error(fullscreenError);
    }
  }

  previousButtons.forEach(function (button) {
    if (button) button.addEventListener('click', function () { turnPage('backward'); });
  });
  nextButtons.forEach(function (button) {
    if (button) button.addEventListener('click', function () { turnPage('forward'); });
  });

  document.getElementById('ebookZoomOut').addEventListener('click', function () { setZoom(zoom - .25); });
  document.getElementById('ebookZoomIn').addEventListener('click', function () { setZoom(zoom + .25); });
  document.getElementById('ebookResetZoom').addEventListener('click', function () { setZoom(1); });
  fullscreenButton.addEventListener('click', toggleFullscreen);

  reader.addEventListener('keydown', function (event) {
    if (event.key === 'ArrowRight' || event.key === 'PageDown') {
      event.preventDefault();
      turnPage('forward');
    } else if (event.key === 'ArrowLeft' || event.key === 'PageUp') {
      event.preventDefault();
      turnPage('backward');
    }
  });

  stage.addEventListener('pointerdown', function (event) {
    if (event.pointerType === 'mouse') return;
    pointerStart = { x: event.clientX, y: event.clientY };
  });
  stage.addEventListener('pointerup', function (event) {
    if (!pointerStart || event.pointerType === 'mouse') return;
    const deltaX = event.clientX - pointerStart.x;
    const deltaY = event.clientY - pointerStart.y;
    pointerStart = null;
    if (Math.abs(deltaX) > 65 && Math.abs(deltaX) > Math.abs(deltaY) * 1.35) {
      turnPage(deltaX < 0 ? 'forward' : 'backward');
    }
  });
  stage.addEventListener('pointercancel', function () { pointerStart = null; });

  function handleFullscreenChange() {
    const isFullscreen = document.fullscreenElement === reader || document.webkitFullscreenElement === reader;
    const icon = fullscreenButton.querySelector('i');
    icon.className = isFullscreen ? 'fas fa-compress' : 'fas fa-expand';
    fullscreenButton.setAttribute('aria-label', isFullscreen ? 'Keluar dari layar penuh' : 'Buka layar penuh');
    window.dispatchEvent(new Event('resize'));
  }
  document.addEventListener('fullscreenchange', handleFullscreenChange);
  document.addEventListener('webkitfullscreenchange', handleFullscreenChange);

  window.addEventListener('resize', function () {
    window.clearTimeout(resizeTimer);
    resizeTimer = window.setTimeout(async function () {
      if (!pdfDocument || busy) return;
      busy = true;
      updateBookLayout();
      try { await renderCurrentSpread(); } finally { busy = false; updateControls(); }
    }, 220);
  });

  try {
    if (typeof pdfjsLib === 'undefined') throw new Error('PDF.js tidak tersedia');
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.9.179/pdf.worker.min.js';
    pdfDocument = await pdfjsLib.getDocument({ url: PDF_URL }).promise;
    totalPages = pdfDocument.numPages;
    const firstPage = await pdfDocument.getPage(1);
    const firstViewport = firstPage.getViewport({ scale: 1 });
    pageAspectRatio = firstViewport.height / firstViewport.width;
    updateBookLayout();
    await renderCurrentSpread();
    loading.style.display = 'none';
    busy = false;
    updateControls();
  } catch (loadError) {
    console.error(loadError);
    showError('E-book gagal dimuat. Pastikan file PDF masih tersedia, lalu coba lagi.');
  }
})();
</script>
<?php } ?>
  <script>
  document.querySelectorAll('.nav-link[data-bs-toggle="collapse"]').forEach(function(link) {
    link.addEventListener('click', function() {
      const icon = this.querySelector('i');
      if (icon.classList.contains('fa-chevron-right')) {
        icon.classList.remove('fa-chevron-right');
        icon.classList.add('fa-chevron-down');
      } else {
        icon.classList.remove('fa-chevron-down');
        icon.classList.add('fa-chevron-right');
      }
    });
  });
</script>
 <!-- Bootstrap JS and dependencies -->
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
