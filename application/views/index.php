<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>STIINFO</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- CSS CUSTOM -->
  <link rel="stylesheet" type="text/css" href="<?= base_url('assets/style.css?v=berita-hiv-1') ?>">
  <!-- FONT UPLOAD -->
  <link href="https://fonts.googleapis.com/css2?family=Red+Hat+Display:wght@400;500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <!-- recapthca google v3-->
  <script src="https://www.google.com/recaptcha/api.js?render=6Lc4mUcqAAAAAHSYJ1R6F9CC253bIEqzqGwvKEYH"></script>

  <style>
        /* Menambahkan warna background dan ukuran ke tombol previous & next */
        .carousel-control-prev-icon,
        .carousel-control-next-icon {
            background-color: rgba(0, 0, 0, 0.5); /* Warna latar belakang transparan */
            padding: 15px; /* Ukuran area tombol */
            border-radius: 50%; /* Membuat tombol menjadi bulat */
        }

        /* Untuk mengubah ikon panah menjadi lebih besar dan jelas */
        .carousel-control-prev-icon {
            background-image: url('data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" fill="%23fff" viewBox="0 0 16 16"%3E%3Cpath d="M11.354 1.646a.5.5 0 0 1 0 .708L6.707 7l4.647 4.646a.5.5 0 0 1-.708.708l-5-5a.5.5 0 0 1 0-.708l5-5a.5.5 0 0 1 .708 0z"/%3E%3C/svg%3E');
        }

        .carousel-control-next-icon {
            background-image: url('data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" fill="%23fff" viewBox="0 0 16 16"%3E%3Cpath d="M4.646 1.646a.5.5 0 0 1 .708 0l5 5a.5.5 0 0 1 0 .708l-5 5a.5.5 0 0 1-.708-.708L9.293 8 4.646 3.354a.5.5 0 0 1 0-.708z"/%3E%3C/svg%3E');
        }

        /* Posisi tombol Previous dan Next di pinggir layar */
        .carousel-control-prev, 
        .carousel-control-next {
            width: 5%; /* Sesuaikan dengan keinginan, 5% agar menempel di pinggir */
        }

        /* Menambahkan hover effect untuk memperjelas tombol saat diklik */
        .carousel-control-prev:hover,
        .carousel-control-next:hover {
            background-color: rgba(0, 0, 0, 0.7); /* Lebih gelap saat dihover */
        }
    </style>

</head>
<body>
  <!--NAVBAR-->
  <nav class="navbar navbar-expand-lg navbar-light bg-light navbar-custom fixed-top">
  <div class="container">
    <a class="navbar-brand" href="#">
      <img src="assets/img/logo.png" alt="Logo" class="d-inline-block align-text-top">
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="#beranda">Beranda</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#tentang-kami">Tentang Kami</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#edukasi-penyakit">Edukasi HIV AIDS</a>
        </li>
        <!---<li class="nav-item">
          <a class="nav-link" href="#konsultasi-online">Konsultasi Online</a>
        </li>-->
        <li class="nav-item">
          <a class="nav-link" href="#pembelajaran">Media Pembelajaran</a>
        </li>
       <!---<li class="nav-item">
          <a class="nav-link" href="#berita">Berita</a>
        </li>--->
        <?php
          if($status_login == 0){
        ?>
        <!-- Menu My Account dengan Dropdown -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Akun
          </a>
          <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
            <li><a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#modalLogin">Login</a></li>
            <li><a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#modalRegister">Register</a></li>
          </ul>
        </li>
        <?php
          }
          else{
            ?>
          <li class="nav-item">
    <a class="nav-link" href="<?=base_url('frontend/dashboard')?>">
        <i class="fas fa-user"></i> <?=$name?>
    </a>
</li>

        <?php
          }
        ?>
      </ul>
    </div>
  </div>
</nav>


  <!-- BERANDA -->
  <section id="beranda">
    <div class="container">
      <?php
        $beritaHivList = isset($getBeritaHiv) ? $getBeritaHiv : array();
      ?>
      <?php if (!empty($beritaHivList)) { ?>
      <div id="beritaHivCarousel" class="carousel slide berita-hiv-slider" data-bs-ride="carousel" data-bs-interval="6500">
        <div class="carousel-indicators">
          <?php foreach ($beritaHivList as $key => $value) { ?>
          <button type="button" data-bs-target="#beritaHivCarousel" data-bs-slide-to="<?= $key ?>" class="<?= $key == 0 ? 'active' : '' ?>" aria-current="<?= $key == 0 ? 'true' : 'false' ?>" aria-label="Slide <?= $key + 1 ?>"></button>
          <?php } ?>
        </div>
        <div class="carousel-inner">
          <?php
          foreach ($beritaHivList as $key => $value) {
            $judulBerita = htmlspecialchars($value['title'], ENT_QUOTES, 'UTF-8');
            $ringkasanBerita = htmlspecialchars($value['ringkasan'], ENT_QUOTES, 'UTF-8');
            $isiBerita = htmlspecialchars($value['field'], ENT_QUOTES, 'UTF-8');
            $tanggalBerita = !empty($value['date']) ? date('d/m/Y', strtotime($value['date'])) : '';
            $gambarBerita = base_url('assets/img/PosterHIV-1.png');
            if (!empty($value['image']) && file_exists(FCPATH.'assets/img/berita_hiv/'.$value['image'])) {
              $gambarBerita = base_url('assets/img/berita_hiv/'.$value['image']);
            } elseif (!empty($value['image']) && file_exists(FCPATH.'assets/img/'.$value['image'])) {
              $gambarBerita = base_url('assets/img/'.$value['image']);
            }
            $linkSumberRaw = isset($value['link_sumber']) ? trim($value['link_sumber']) : '';
            $linkSumber = preg_match('/^https?:\/\//i', $linkSumberRaw) ? htmlspecialchars($linkSumberRaw, ENT_QUOTES, 'UTF-8') : '';
          ?>
          <div class="carousel-item <?= $key == 0 ? 'active' : '' ?>">
            <div class="berita-hiv-slide">
              <div class="berita-hiv-content">
                <span>Berita HIV/AIDS</span>
                <h1><?= $judulBerita ?></h1>
                <p><?= $ringkasanBerita ?></p>
                <div class="berita-hiv-actions">
                  <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalBeritaHiv<?= $value['id'] ?>">Baca Berita</button>
                  <?php if ($tanggalBerita != '') { ?>
                  <small><?= $tanggalBerita ?></small>
                  <?php } ?>
                </div>
              </div>
              <div class="berita-hiv-image">
                <img src="<?= $gambarBerita ?>" alt="<?= $judulBerita ?>">
              </div>
            </div>
          </div>
          <?php } ?>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#beritaHivCarousel" data-bs-slide="prev">
          <span class="carousel-control-prev-icon" aria-hidden="true"></span>
          <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#beritaHivCarousel" data-bs-slide="next">
          <span class="carousel-control-next-icon" aria-hidden="true"></span>
          <span class="visually-hidden">Next</span>
        </button>
      </div>
      <?php } else { ?>
      <div class="berita-hiv-slide berita-hiv-empty">
        <div class="berita-hiv-content">
          <span>Berita HIV/AIDS</span>
          <h1>Informasi HIV/AIDS yang mudah dibaca dan dipahami</h1>
          <p>Kenali pencegahan, pemeriksaan, dan perawatan HIV/AIDS melalui berita edukatif yang ringkas dan terpercaya.</p>
        </div>
        <div class="berita-hiv-image">
          <img src="<?= base_url('assets/img/PosterHIV-1.png') ?>" alt="Informasi HIV/AIDS">
        </div>
      </div>
      <?php } ?>
    </div>
  </section>

  <!-- TENTANG KAMI -->
  <section id="edukasi-penyakit">
    <div class="container">
      <div class="row">
        <div class="col-lg-12">
          <h1>Edukasi HIV AIDS</h1>
        </div>
      </div>
      <div class="row">
        <!---<p>
          <button type="button" class="btn btn-primary" onclick="toggleContent('hiv_aids')">
            Edukasi HIV/AIDS
          </button> 
          <button type="button" class="btn btn-primary" onclick="toggleContent('sifilis')">
            Edukasi Sifilis
          </button>
        </p>--->
        <div id="hiv_aids" class="collapse show col-education">
          <div class="row">
            <?php
            foreach ($getModulEdukasi as $key => $value) {
            ?>
            <div class="col-lg-4">
              <div class="card">
                <iframe src="https://www.youtube.com/embed/<?=$value['link']?>" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" height="300px" allowfullscreen></iframe>
                <div class="card-body">
                  <h5 class="card-title"><?=$value['title']?></h5>
                </div>
              </div>
            </div>
            <?php
            }
            ?>

          </div>
        </div>

        <div id="sifilis" class="collapse col-education">
          <div class="col-lg-3">
              <div class="card">
                <iframe src="" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" height="300px" allowfullscreen></iframe>
                <div class="card-body">
                  <h5 class="card-title">EDUKASI</h5>
                </div>
              </div>
          </div>
        </div>

      </div>
    </div>
  </section>

  <!-- KONSULTASI ONLINE -->
  <!--<section id="konsultasi-online">
    <div class="container">
      <div class="row">
        <div class="col-lg-12">
          <h1>Konsultasi Online</h1>
        </div>
      </div>
      <div class="row row-custom">
        <div class="col-lg-3 col-md-4 col-sm-3 col-6">
          <div class="col-custom">
            <div class="wrap-img">
              <button class="btn btn-danger">Offline</button>
              <img src="assets/img/user-dokter.png">
            </div>
            <div class="wrap-txt">
              <h5>Dr. Irawan Muhammad</h5>
              <p>(Spesialis.....)</p>
              <button class="btn btn-danger">Chat</button>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-md-4 col-sm-3 col-6">
          <div class="col-custom">
            <div class="wrap-img">
              <button class="btn btn-info">Online</button>
              <img src="assets/img/not-found-image.png">
            </div>
            <div class="wrap-txt">
              <h5>Dr. Irawan Muhammad</h5>
              <p>(Spesialis.....)</p>
              <button class="btn btn-danger">Chat</button>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-md-4 col-sm-3 col-6">
          <div class="col-custom">
            <div class="wrap-img">
              <button class="btn btn-info">Online</button>
              <img src="assets/img/user-dokter.png">
            </div>
            <div class="wrap-txt">
              <h5>Dr. Irawan Muhammad</h5>
              <p>(Spesialis.....)</p>
              <button class="btn btn-danger">Chat</button>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-md-4 col-sm-3 col-6">
          <div class="col-custom">
            <div class="wrap-img">
              <button class="btn btn-info">Online</button>
              <img src="assets/img/user-dokter.png">
            </div>
            <div class="wrap-txt">
              <h5>Dr. Irawan Muhammad</h5>
              <p>(Spesialis.....)</p>
              <button class="btn btn-danger">Chat</button>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-md-4 col-sm-3 col-6">
          <div class="col-custom">
            <div class="wrap-img">
              <button class="btn btn-info">Online</button>
              <img src="assets/img/user-dokter.png">
            </div>
            <div class="wrap-txt">
              <h5>Dr. Irawan Muhammad</h5>
              <p>(Spesialis.....)</p>
              <button class="btn btn-danger">Chat</button>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-md-4 col-sm-3 col-6">
          <div class="col-custom">
            <div class="wrap-img">
              <button class="btn btn-info">Online</button>
              <img src="assets/img/user-dokter.png">
            </div>
            <div class="wrap-txt">
              <h5>Dr. Irawan Muhammad</h5>
              <p>(Spesialis.....)</p>
              <button class="btn btn-danger">Chat</button>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-md-4 col-sm-3 col-6">
          <div class="col-custom">
            <div class="wrap-img">
              <button class="btn btn-info">Online</button>
              <img src="assets/img/user-dokter.png">
            </div>
            <div class="wrap-txt">
              <h5>Dr. Irawan Muhammad</h5>
              <p>(Spesialis.....)</p>
              <button class="btn btn-danger">Chat</button>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-md-4 col-sm-3 col-6">
          <div class="col-custom">
            <div class="wrap-img">
              <button class="btn btn-info">Online</button>
              <img src="assets/img/user-dokter.png">
            </div>
            <div class="wrap-txt">
              <h5>Dr. Irawan Muhammad</h5>
              <p>(Spesialis.....)</p>
              <button class="btn btn-danger">Chat</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>-->

  <!-- TENTANG KAMI -->
  <section id="tentang-kami">
    <div class="container">
      <div class="row">
        <div class="col-lg-6">
          <div class="img-left">
            <img src="assets/img/tentang-kami.png">
          </div>
        </div>
        <div class="col-lg-6 wrap-right">
          <h1>Selamat Datang di smartSTIinfo</h1>
          <h3>Platform Edukasi Infeksi Menular Seksual (IMS)</h3>
          <p>
            Di sini, kami berkomitmen untuk memberikan informasi yang mudah diakses mengenai Infeksi Menular Seksual (IMS). IMS adalah masalah kesehatan global yang dapat dicegah dan diobati, tetapi kesadaran dan edukasi masih menjadi kunci utama dalam memerangi penyebarannya. Platform ini dirancang untuk menjawab kebutuhan Anda akan informasi tentang alur skrining, pencegahan, gejala, diagnosa, dan pengobatan IMS. Kami memahami bahwa berbicara tentang kesehatan seksual seringkali sulit, tetapi di sini Anda akan menemukan ruang yang aman dan mendukung untuk belajar dan memahami risiko, serta bagaimana menjaga kesehatan seksual Anda. Mari bersama-sama memutus rantai penyebaran IMS melalui edukasi yang baik dan tindakan preventif yang bijak.
          </p>
          <div class="medsos">
            <p>Media Sosial Kami</p>
            <!--<a href="">
              <img src="assets/img/facebook.png">
            </a>-->
            <a href="https://www.instagram.com/smartstiinfo/">
              <img src="assets/img/instagram.png">
            </a>
            <a href="https://www.tiktok.com/@smartstiinfo">
              <img src="assets/img/tiktok.png">
            </a>
            <a href="https://www.youtube.com/@SmartSTI.informasi/videos">
              <img src="assets/img/youtube.png">
            </a>
          </div>
       </div>
      </div>
    </div>
  </section>

  <!-- MEDIA PEMBELAJARAN -->
  <section id="pembelajaran">
    <div class="container">
      <div class="row">
        <div class="col-lg-12">
          <h1>Media Pembelajaran</h1>
        </div>
      </div>
      <div class="row">
        <?php
          foreach ($getModulPembelajaran as $key => $value) {
        ?>
        <div class="col-lg-4">
          <div class="card ">
            <div class="card-body">
              <h5 class="card-title"><?=$value['nama']?></h5>
                <p class="card-text">
                  <?=$value['detail']?>
                </p>
                <div class="alert alert-info">
                  Login / Masuk akun untuk mempelajari modul ini
                </div>
            </div>
          </div>
        </div>
        <?php
        }
        ?>

      </div>
    </div>
  </section>

  <footer class="footer bg-dark text-light pt-2">
    <div class="container">
        <div class="row">
            <div class="col-md-6 col-lg-4 mb-4 col-custom">
                <img src="assets/img/logo.png" alt="Logo Perusahaan" class="img-fluid mb-3">
                <ul class="list-unstyled">
                    <li><a href="#">Beranda</a></li>
                    <li><a href="#">Tentang Kami</a></li>
                    <li><a href="#">Edukasi Penyakit</a></li>
                    <li><a href="#">Konsultasi Online</a></li>
                    <li><a href="#">Berita</a></li>
                </ul>
            </div>
            <div class="col-md-6 col-lg-4 mb-4 col-custom">
                <h5>Alamat</h5>
                <address>
                    Nusa Tenggara Barat
                </address>
                <h5>Kontak</h5>
                <address>
                    <a href="mailto:contoh@email.com">stiinformasi@gmail.com</a><br>
                </address>
            </div>
            <div class="col-lg-4 col-custom">
                <h5>Media Sosial</h5>
                <!--<a href="https://facebook.com" target="_blank">
                    <i class="fab fa-facebook"></i> Facebook
                </a>-->
                <br>
                <a href="https://www.instagram.com/smartstiinfo/" target="_blank">
                    <i class="fab fa-instagram"></i> Instagram
                </a>
                <br>
                <a href="https://www.tiktok.com/@smartstiinfo" target="_blank">
                    <i class="fab fa-tiktok"></i> TikTok
                </a>
                <br>
                <a href="https://www.youtube.com/@SmartSTI.informasi/videos" target="_blank">
                    <i class="fab fa-youtube"></i> Youtube
                </a>
            </div>
        </div>
    </div>
    <div class="text-center and-footer">
        <small>&copy; 2024</small>
    </div>
</footer>

<!-- The Modal berita HIV/AIDS -->
<?php
if (!empty($beritaHivList)) {
foreach ($beritaHivList as $value) {
  $judulBerita = htmlspecialchars($value['title'], ENT_QUOTES, 'UTF-8');
  $ringkasanBerita = htmlspecialchars($value['ringkasan'], ENT_QUOTES, 'UTF-8');
  $isiBerita = htmlspecialchars($value['field'], ENT_QUOTES, 'UTF-8');
  $tanggalBerita = !empty($value['date']) ? date('d/m/Y', strtotime($value['date'])) : '';
  $gambarBerita = base_url('assets/img/PosterHIV-1.png');
  if (!empty($value['image']) && file_exists(FCPATH.'assets/img/berita_hiv/'.$value['image'])) {
    $gambarBerita = base_url('assets/img/berita_hiv/'.$value['image']);
  } elseif (!empty($value['image']) && file_exists(FCPATH.'assets/img/'.$value['image'])) {
    $gambarBerita = base_url('assets/img/'.$value['image']);
  }
  $linkSumberRaw = isset($value['link_sumber']) ? trim($value['link_sumber']) : '';
  $linkSumber = preg_match('/^https?:\/\//i', $linkSumberRaw) ? htmlspecialchars($linkSumberRaw, ENT_QUOTES, 'UTF-8') : '';
?>
<div class="modal modal-berita-hiv" id="modalBeritaHiv<?= $value['id'] ?>">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title"><?= $judulBerita ?></h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <img src="<?= $gambarBerita ?>" alt="<?= $judulBerita ?>" class="img-fluid">
        <?php if ($tanggalBerita != '') { ?>
        <small><?= $tanggalBerita ?></small>
        <?php } ?>
        <p class="lead"><?= $ringkasanBerita ?></p>
        <p><?= nl2br($isiBerita) ?></p>
        <?php if ($linkSumber != '') { ?>
        <a class="btn btn-outline-danger" href="<?= $linkSumber ?>" target="_blank" rel="noopener">Buka Sumber</a>
        <?php } ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>
<?php
}
}
?>

<!-- The Modal cek penyakit -->
<?php
foreach ($getPenyakit as $key => $value) {
?>
<div class="modal modal-skrening-penyakit" id="modalCekHiv<?=$value['id']?>">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h1 class="modal-title">Cek <?=$value['nama']?></h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
        <?php
        if($value['img_screening'] !=""){
        ?>
        <a target="_blank" href="<?=base_url('assets/img/'.$value['img_screening'])?>">
          <img src="<?=base_url('assets/img/'.$value['img_screening'])?>">
        </a>
        <?php
        }
        ?>
        <!--<h2>Form Skrining Mandiri</h2>
          <form>
            <div class="mb-3 mt-3">
              <label>Nama</label>
              <input type="text" class="form-control" placeholder="Masukkan Nama" name="nama">
            </div>
            <div class="mb-3">
              <label for="pwd">Alamat:</label>
              <textarea class="form-control" placeholder="Tulis Alamat tempat tinggal"></textarea>
            </div>
            <a target="_blank" href="https://wa.me/6282244347856?text=Nama%20Saya%20Arif%20Rahman%20Saya%20Ingin%20Cek%20HIV/AIDS">
              <button type="submit" class="btn btn-primary">Kirim WA</button>
            </a>
          </form>--->
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
      </div>

    </div>
  </div>
</div>
<?php
}
?>
<!-- The Modal Login -->
<div class="modal modal-skrening-penyakit" id="modalLogin">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h1 class="modal-title">Login</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
          <form method="POST" action="<?=base_url()?>">
            <input type="hidden" name="<?=$this->security->get_csrf_token_name();?>" value="<?=$this->security->get_csrf_hash();?>" style="display: none">
            <input type="hidden" id="token" name="token">
            <div class="mb-3 mt-3">
              <label for="loginNoHp">No HP</label>
              <input type="tel" class="form-control" id="loginNoHp" placeholder="Masukkan No HP" name="no_hp" autocomplete="tel" inputmode="tel" required>
            </div>
            <div class="mb-3">
              <label for="pwd">Password</label>
              <input type="password" class="form-control" placeholder="Masukkan Password" name="password">
            </div>
              <button type="submit" class="btn btn-primary">Login</button>
          </form>
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
      </div>

    </div>
  </div>
</div>

<!-- The Modal Register -->
<div class="modal modal-skrening-penyakit" id="modalRegister">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable register-modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h1 class="modal-title">Register</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
          <?php
          $statusUserOptions = !empty($getStatusUser) ? $getStatusUser : array();
          ?>
          <form method="POST" action="<?=base_url('/home/prosesTambahUser')?>">
            <input type="hidden" name="<?=$this->security->get_csrf_token_name();?>" value="<?=$this->security->get_csrf_hash();?>" style="display: none">
            <input type="hidden" id="token1" name="token">
            <div class="mb-3 mt-3">
              <label for="registerNama" id="registerNamaLabel">Nama</label>
              <input type="text" class="form-control" id="registerNama" placeholder="Masukkan Nama" name="nama" aria-describedby="registerNamaHelp" autocomplete="name" data-initial-mode="nama" required>
              <small class="form-text text-muted d-none" id="registerNamaHelp">Gunakan inisial saja, bukan nama lengkap, untuk menjaga kerahasiaan.</small>
            </div>
            <div class="mb-3 mt-3">
              <label for="registerNoHp">No HP</label>
              <input type="tel" class="form-control" id="registerNoHp" placeholder="Masukkan No HP" name="no_hp" autocomplete="tel" inputmode="tel" required>
            </div>
            <div class="mb-3 mt-3 register-status-field">
              <label>Status Akun</label>
              <p class="register-status-help">Pilih kategori akun yang paling sesuai agar sistem dapat mengenali kebutuhan pengguna.</p>
              <?php if (empty($statusUserOptions)) { ?>
              <div class="alert alert-warning">
                Data status akun belum tersedia. Silakan tambahkan status pada tabel status_user terlebih dahulu.
              </div>
              <?php } else { ?>
              <div class="register-status-grid">
                <?php foreach ($statusUserOptions as $statusUser) { ?>
                <label class="register-status-card">
                  <input class="register-status-input" type="radio" name="status" value="<?= (int) $statusUser['id'] ?>" data-status-name="<?= htmlspecialchars($statusUser['nama'], ENT_QUOTES, 'UTF-8') ?>" required>
                  <span class="register-status-content">
                    <strong><?= htmlspecialchars($statusUser['nama'], ENT_QUOTES, 'UTF-8') ?></strong>
                    <small><?= htmlspecialchars($statusUser['deskripsi'], ENT_QUOTES, 'UTF-8') ?></small>
                  </span>
                </label>
                <?php } ?>
              </div>
              <?php } ?>
            </div>
            <div class="mb-3">
              <label for="pwd">Password</label>
              <input type="password" class="form-control" placeholder="Masukkan Password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary" <?= empty($statusUserOptions) ? 'disabled' : '' ?>>Register</button>
          </form>
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
      </div>

    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  var registerModal = document.getElementById('modalRegister');
  var nameLabel = document.getElementById('registerNamaLabel');
  var nameInput = document.getElementById('registerNama');
  var nameHelp = document.getElementById('registerNamaHelp');

  if (!registerModal || !nameLabel || !nameInput || !nameHelp) {
    return;
  }

  function updateRegisterNameField(selectedStatus) {
    var statusName = selectedStatus
      ? (selectedStatus.getAttribute('data-status-name') || '').trim().toLowerCase()
      : '';
    var usesFullName = statusName === 'masyarakat' || statusName === 'tenaga kesehatan';
    var usesInitials = statusName !== '' && !usesFullName;

    var nextMode = usesInitials ? 'inisial' : 'nama';
    if (nameInput.getAttribute('data-initial-mode') !== nextMode && nameInput.value.trim() !== '') {
      nameInput.value = '';
    }

    nameInput.setAttribute('data-initial-mode', nextMode);
    nameLabel.textContent = usesInitials ? 'Tulis Inisial' : 'Nama';
    nameInput.placeholder = usesInitials ? 'Masukkan Inisial' : 'Masukkan Nama';
    nameInput.setAttribute('aria-label', usesInitials ? 'Tulis Inisial' : 'Nama');
    nameInput.setAttribute('autocomplete', usesInitials ? 'off' : 'name');
    nameHelp.classList.toggle('d-none', !usesInitials);
  }

  registerModal.querySelectorAll('input[name="status"]').forEach(function (statusInput) {
    statusInput.addEventListener('change', function () {
      updateRegisterNameField(statusInput);
    });
  });

  updateRegisterNameField(registerModal.querySelector('input[name="status"]:checked'));
});
</script>

<!-- The Modal Pembelajaran -->
<div class="modal modal-skrening-penyakit" id="modalPembelajaran">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h1 class="modal-title">Pembelajaran 1 (Devinisi HIV/AIDS)</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
          <p class="card-text">
                  Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec faucibus at purus id pharetra. Nullam elit mauris, pellentesque in tempus convallis, hendrerit quis ex. Mauris accumsan urna at ante luctus mattis. Phasellus a risus luctus, auctor eros eget, ullamcorper metus. Proin et erat vel nibh interdum accumsan ut in sapien. Cras quis metus et magna suscipit imperdiet. In ac magna id est sollicitudin auctor. Aenean sapien nisl, malesuada eu sagittis eget, facilisis tincidunt dolor. Lorem ipsum dolor sit amet, consectetur adipiscing elit.

                  Nam vitae blandit velit. Vivamus eu varius nisl. Quisque vestibulum tellus ut urna tempus scelerisque. Vestibulum ultricies vehicula turpis, in commodo lorem dictum a. Nunc nec enim lorem. Sed sit amet arcu condimentum, elementum metus et, pretium tellus. Aliquam venenatis nisl at gravida mattis. Nulla placerat, turpis nec suscipit auctor, lectus est semper turpis, quis eleifend lorem leo eu neque. Etiam ac porta eros, id egestas est. Duis ut nibh vitae lectus semper scelerisque sed sed enim. Curabitur quis ultricies magna. Maecenas leo dui, hendrerit quis lectus vel, mattis gravida velit. Duis blandit faucibus ante. Suspendisse molestie, orci nec pulvinar rutrum, neque turpis semper est, nec maximus sem mi mattis nisl. Curabitur ac sapien eget sapien consectetur vestibulum. Aliquam vel dui quis dui sodales sagittis a et eros.

                  Integer eget dui sit amet lectus elementum pharetra vel quis arcu. Sed consectetur porttitor sodales. Ut et molestie lorem, quis placerat orci. Vivamus accumsan dui in nibh interdum, eget egestas nisl lobortis. Nunc accumsan lorem et tellus tempor, vitae fermentum sapien sodales. Duis nec diam at sapien venenatis sagittis. Integer nec ipsum diam. Curabitur fermentum enim tortor, sed dignissim nisi elementum in. Donec ornare magna lectus, ac ultrices nunc porta sed. Quisque sagittis commodo erat at vehicula. Interdum et malesuada fames ac ante ipsum primis in faucibus. Curabitur ullamcorper eget ipsum nec rhoncus. Aenean convallis maximus nisi, eu ultrices tellus lobortis vitae.

                  Pellentesque sit amet odio sed est interdum semper sit amet at est. Duis eu risus id nisi rutrum ultricies at in quam. Aliquam sed tellus vitae metus varius consectetur. Aenean semper tortor est, ut tristique ante fermentum nec. Aenean dapibus fringilla nunc vel condimentum. Nulla efficitur scelerisque tellus id egestas. Donec vehicula neque orci, et bibendum nulla dictum nec.

                  Nam eu blandit arcu, eget tincidunt turpis. Mauris sapien est, posuere sit amet scelerisque feugiat, finibus quis turpis. Proin malesuada, tortor at porta porta, augue felis tempus ligula, ac blandit ex ipsum sit amet libero. Nunc ut purus leo. Maecenas aliquet varius ligula, nec finibus lorem auctor nec. Suspendisse aliquet, metus eget aliquam tempor, magna justo dictum lacus, at facilisis libero ante in eros. Cras mi felis, iaculis non tortor tristique, semper laoreet ligula. In id sagittis augue. Aliquam accumsan ante ut venenatis gravida. Ut dapibus molestie lacus vel tempor. Donec nulla odio, rhoncus suscipit congue a, porttitor in nunc. Aliquam tristique tincidunt nunc, non mattis dui tincidunt at. Pellentesque in dignissim sapien.
                  <img src="assets/img/tentang-kami.png" class="img-fluid">
                  Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec faucibus at purus id pharetra. Nullam elit mauris, pellentesque in tempus convallis, hendrerit quis ex. Mauris accumsan urna at ante luctus mattis. Phasellus a risus luctus, auctor eros eget, ullamcorper metus. Proin et erat vel nibh interdum accumsan ut in sapien. Cras quis metus et magna suscipit imperdiet. In ac magna id est sollicitudin auctor. Aenean sapien nisl, malesuada eu sagittis eget, facilisis tincidunt dolor. Lorem ipsum dolor sit amet, consectetur adipiscing elit.

                  Nam vitae blandit velit. Vivamus eu varius nisl. Quisque vestibulum tellus ut urna tempus scelerisque. Vestibulum ultricies vehicula turpis, in commodo lorem dictum a. Nunc nec enim lorem. Sed sit amet arcu condimentum, elementum metus et, pretium tellus. Aliquam venenatis nisl at gravida mattis. Nulla placerat, turpis nec suscipit auctor, lectus est semper turpis, quis eleifend lorem leo eu neque. Etiam ac porta eros, id egestas est. Duis ut nibh vitae lectus semper scelerisque sed sed enim. Curabitur quis ultricies magna. Maecenas leo dui, hendrerit quis lectus vel, mattis gravida velit. Duis blandit faucibus ante. Suspendisse molestie, orci nec pulvinar rutrum, neque turpis semper est, nec maximus sem mi mattis nisl. Curabitur ac sapien eget sapien consectetur vestibulum. Aliquam vel dui quis dui sodales sagittis a et eros.

                  Integer eget dui sit amet lectus elementum pharetra vel quis arcu. Sed consectetur porttitor sodales. Ut et molestie lorem, quis placerat orci. Vivamus accumsan dui in nibh interdum, eget egestas nisl lobortis. Nunc accumsan lorem et tellus tempor, vitae fermentum sapien sodales. Duis nec diam at sapien venenatis sagittis. Integer nec ipsum diam. Curabitur fermentum enim tortor, sed dignissim nisi elementum in. Donec ornare magna lectus, ac ultrices nunc porta sed. Quisque sagittis commodo erat at vehicula. Interdum et malesuada fames ac ante ipsum primis in faucibus. Curabitur ullamcorper eget ipsum nec rhoncus. Aenean convallis maximus nisi, eu ultrices tellus lobortis vitae.

                  Pellentesque sit amet odio sed est interdum semper sit amet at est. Duis eu risus id nisi rutrum ultricies at in quam. Aliquam sed tellus vitae metus varius consectetur. Aenean semper tortor est, ut tristique ante fermentum nec. Aenean dapibus fringilla nunc vel condimentum. Nulla efficitur scelerisque tellus id egestas. Donec vehicula neque orci, et bibendum nulla dictum nec.

                  Nam eu blandit arcu, eget tincidunt turpis. Mauris sapien est, posuere sit amet scelerisque feugiat, finibus quis turpis. Proin malesuada, tortor at porta porta, augue felis tempus ligula, ac blandit ex ipsum sit amet libero. Nunc ut purus leo. Maecenas aliquet varius ligula, nec finibus lorem auctor nec. Suspendisse aliquet, metus eget aliquam tempor, magna justo dictum lacus, at facilisis libero ante in eros. Cras mi felis, iaculis non tortor tristique, semper laoreet ligula. In id sagittis augue. Aliquam accumsan ante ut venenatis gravida. Ut dapibus molestie lacus vel tempor. Donec nulla odio, rhoncus suscipit congue a, porttitor in nunc. Aliquam tristique tincidunt nunc, non mattis dui tincidunt at. Pellentesque in dignissim sapien.
                </p>
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
      </div>

    </div>
  </div>
</div>

<?php if ($this->session->flashdata('message')){?>
<!-- Modal -->
    <div class="modal fade" id="autoPopup" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Peringatan!</h5>
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

  <!-- Bootstrap Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <!--<script>
  function toggleContent(targetId) {
    // Hide all content
    document.getElementById('hiv_aids').classList.remove('show');
    document.getElementById('sifilis').classList.remove('show');

    // Show the selected content
    document.getElementById(targetId).classList.add('show');
  }
</script>-->
<!--- google recpatcha -->
<script>
   grecaptcha.ready(function() {
     grecaptcha.execute('6Lc4mUcqAAAAAHSYJ1R6F9CC253bIEqzqGwvKEYH', {action: 'homepage'}).then(function(token) {
     console.log("onload's token : "+token);
     document.getElementById("token").value = token;
     });
   });
   /*refresh get token before it's expire every 90 Seconds */
   setInterval(function () {
     grecaptcha.ready(function () {
     grecaptcha.execute("6Lc4mUcqAAAAAHSYJ1R6F9CC253bIEqzqGwvKEYH", {action: "request_call_back"}).then(function (token) {
     console.log("refresh toekn : "+token);
     document.getElementById("token").value = token;
     });
     });
   }, 90 * 1000);
</script>
<!--- google recpatcha -->
<script>
   grecaptcha.ready(function() {
     grecaptcha.execute('6Lc4mUcqAAAAAHSYJ1R6F9CC253bIEqzqGwvKEYH', {action: 'homepage'}).then(function(token) {
     console.log("onload's token : "+token);
     document.getElementById("token1").value = token;
     });
   });
   /*refresh get token before it's expire every 90 Seconds */
   setInterval(function () {
     grecaptcha.ready(function () {
     grecaptcha.execute("6Lc4mUcqAAAAAHSYJ1R6F9CC253bIEqzqGwvKEYH", {action: "request_call_back"}).then(function (token) {
     console.log("refresh toekn : "+token);
     document.getElementById("token1").value = token;
     });
     });
   }, 90 * 1000);
</script>
</body>
</html>
