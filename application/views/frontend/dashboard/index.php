<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6"><h1 class="m-0">Dashboard</h1></div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
            <li class="breadcrumb-item active">Dashboard</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <div class="content">
    <div class="container-fluid">
      <?php if ($this->session->flashdata('success')) { ?>
        <div class="alert alert-success"><?= htmlspecialchars($this->session->flashdata('success'), ENT_QUOTES, 'UTF-8') ?></div>
      <?php } ?>
      <?php if ($this->session->flashdata('error')) { ?>
        <div class="alert alert-danger"><?= htmlspecialchars($this->session->flashdata('error'), ENT_QUOTES, 'UTF-8') ?></div>
      <?php } ?>

      <div class="card card-primary card-outline">
        <div class="card-body p-4">
          <p class="text-primary font-weight-bold mb-1">STIInfo</p>
          <p class="text-muted mb-3">Sexually Transmitted Infections Information</p>
          <h2 class="h3">Selamat Datang di STIInfo</h2>
          <p class="lead">Kenali HIV, Pahami Pengobatan, Jaga Kesehatan Anda.</p>
          <p>STIInfo merupakan platform informasi dan edukasi kesehatan digital yang berfokus pada HIV/AIDS. Website ini dikembangkan untuk membantu pengguna memperoleh informasi yang tepat mengenai HIV, memahami penggunaan obat, serta meningkatkan kesadaran dalam menjaga kesehatan dan mencegah penularan HIV.</p>
          <p class="mb-0">Melalui STIInfo, Anda dapat mengakses berbagai fitur yang mendukung pemahaman dan pengelolaan kesehatan, mulai dari edukasi HIV/AIDS, informasi penggunaan obat, pemantauan kepatuhan pengobatan, skrining kondisi psikologis, hingga informasi potensi interaksi obat.</p>
        </div>
      </div>

      <h2 class="h4 mt-4 mb-3">Layanan yang Tersedia</h2>
      <div class="row">
        <div class="col-md-6 mb-3">
          <div class="card h-100 mb-0"><div class="card-body d-flex flex-column">
            <h3 class="h5"><i class="fas fa-clipboard-list text-primary mr-2" aria-hidden="true"></i>Kuesioner Kepatuhan Obat (MARS)</h3>
            <p>Kenali dan evaluasi kebiasaan Anda dalam menggunakan obat melalui kuesioner kepatuhan pengobatan.</p>
            <a href="<?= base_url('frontend/kuesioner-mars') ?>" class="btn btn-outline-primary align-self-start mt-auto">Isi Kuesioner MARS</a>
          </div></div>
        </div>
        <div class="col-md-6 mb-3">
          <div class="card h-100 mb-0"><div class="card-body d-flex flex-column">
            <h3 class="h5"><i class="fas fa-pills text-primary mr-2" aria-hidden="true"></i>Profil Penggunaan Obat</h3>
            <p>Pelajari dan pantau informasi obat yang Anda gunakan, termasuk dosis, aturan pemakaian, manfaat, dan hal-hal yang perlu diperhatikan.</p>
            <a href="<?= base_url('frontend/profil-penggunaan-obat') ?>" class="btn btn-outline-primary align-self-start mt-auto">Lihat Profil Penggunaan Obat</a>
          </div></div>
        </div>
        <div class="col-md-6 mb-3">
          <div class="card h-100 mb-0"><div class="card-body d-flex flex-column">
            <h3 class="h5"><i class="fas fa-capsules text-primary mr-2" aria-hidden="true"></i>Informasi Interaksi Obat</h3>
            <p>Dapatkan informasi mengenai potensi interaksi antara PrEP, obat penyakit penyerta, hormon, dan obat lainnya untuk mendukung penggunaan obat yang aman.</p>
            <a href="<?= base_url('frontend/interaksi-obat') ?>" class="btn btn-outline-primary align-self-start mt-auto">Periksa Interaksi Obat PrEP</a>
          </div></div>
        </div>
        <div class="col-md-6 mb-3">
          <div class="card h-100 mb-0"><div class="card-body d-flex flex-column">
            <h3 class="h5"><i class="fas fa-book text-primary mr-2" aria-hidden="true"></i>Modul Pembelajaran</h3>
            <p>Tingkatkan pengetahuan mengenai HIV/AIDS, pencegahan HIV, pengobatan, efek samping obat, serta pentingnya menjaga kesehatan.</p>
            <p class="text-muted mb-0 mt-auto">Pilih materi melalui menu Modul Pembelajaran di sidebar.</p>
          </div></div>
        </div>
        <div class="col-md-6 mb-3">
          <div class="card h-100 mb-0"><div class="card-body">
            <h3 class="h5"><i class="fas fa-heart text-primary mr-2" aria-hidden="true"></i>Pemantauan Kesehatan Psikologis</h3>
            <p>Kenali kondisi psikologis melalui skrining awal gejala depresi sebagai bagian dari perhatian terhadap kesehatan secara menyeluruh.</p>
            <p class="text-muted mb-0">Layanan skrining psikologis belum tersedia di STIInfo.</p>
          </div></div>
        </div>
      </div>

      <div class="card mt-2"><div class="card-body p-4">
        <h2 class="h4">Mari Mulai Perjalanan Menuju Kesehatan yang Lebih Baik</h2>
        <p>Pengetahuan yang tepat merupakan langkah awal untuk menjaga kesehatan. Manfaatkan STIInfo sebagai teman belajar dan sumber informasi dalam memahami HIV/AIDS, penggunaan obat, serta upaya pencegahan HIV.</p>
        <p class="font-weight-bold">STIInfo – Informasi Tepat, Pengobatan Bijak, Hidup Lebih Sehat.</p>
        <p class="text-muted mb-0"><small>Informasi dalam STIInfo merupakan media edukasi dan pendukung pelayanan kesehatan, bukan pengganti konsultasi, diagnosis, maupun keputusan pengobatan oleh tenaga kesehatan profesional.</small></p>
      </div></div>
    </div>
  </div>
</div>

<?php
$kuesionerKategori = isset($kuesionerKategori) ? $kuesionerKategori : array();
$sudahMengisi = !empty($cekUser['jml']);
if (!$sudahMengisi && !empty($kuesionerKategori)) {
?>
  <div id="welcomeModal" class="questionnaire-modal" role="dialog" aria-modal="true" aria-labelledby="demographicQuestionnaireTitle">
    <div class="questionnaire-modal-content">
      <button type="button" class="questionnaire-close" aria-label="Tutup">&times;</button>
      <h2 id="demographicQuestionnaireTitle">Kuesioner Responden</h2>
      <p class="questionnaire-lead">Lengkapi seluruh bagian berikut. Jawaban Anda akan dikirim sekaligus dalam satu formulir.</p>

      <form method="POST" action="<?= base_url('frontend/dashboard/simpan') ?>" id="demographicQuestionnaireForm" class="needs-validation" novalidate>
        <input
          type="hidden"
          id="demographicCsrfToken"
          name="<?= $this->security->get_csrf_token_name() ?>"
          value="<?= $this->security->get_csrf_hash() ?>"
        >

        <?php foreach ($kuesionerKategori as $kategori) { ?>
          <?php $isMarsCategory = stripos($kategori['nama'], 'MARS-5') !== FALSE; ?>
          <?php $isDrugProfileCategory = stripos($kategori['nama'], 'Profil Penggunaan Obat') !== FALSE; ?>
          <section class="questionnaire-section questionnaire-panel <?= $isMarsCategory ? 'questionnaire-panel--mars' : '' ?> <?= $isDrugProfileCategory ? 'questionnaire-panel--drug' : '' ?>">
            <?php if ($isMarsCategory) { ?>
              <p class="questionnaire-eyebrow mb-1">Medication Adherence Rating Scale (MARS-5)</p>
              <h5>Kuesioner Penggunaan Obat</h5>
            <?php } elseif ($isDrugProfileCategory) { ?>
              <p class="questionnaire-eyebrow mb-1">Lembar Profil Penggunaan Obat</p>
              <h5><?= htmlspecialchars($kategori['label_pertanyaan'], ENT_QUOTES, 'UTF-8') ?></h5>
            <?php } else { ?>
              <h5><?= htmlspecialchars($kategori['nama'], ENT_QUOTES, 'UTF-8') ?></h5>
            <?php } ?>
            <?php if (!empty($kategori['deskripsi'])) { ?>
              <p class="questionnaire-instruction"><?= nl2br(htmlspecialchars($kategori['deskripsi'], ENT_QUOTES, 'UTF-8')) ?></p>
            <?php } ?>

            <div class="questionnaire-fields <?= $isMarsCategory ? 'questionnaire-fields--mars' : '' ?>">
              <?php foreach ($kategori['pertanyaan'] as $pertanyaan) { ?>
                <?php
                  $tipe = isset($pertanyaan['tipe']) ? $pertanyaan['tipe'] : 'radio';
                  $isTextInput = in_array($tipe, array('text', 'textarea', 'number', 'month', 'date'), TRUE);
                  $isCheckbox = $tipe === 'checkbox';
                ?>
                <div class="questionnaire-field">
                  <div class="questionnaire-question">
                    <span><?= htmlspecialchars($pertanyaan['kode'], ENT_QUOTES, 'UTF-8') ?></span>
                    <strong><?= htmlspecialchars($pertanyaan['pertanyaan'], ENT_QUOTES, 'UTF-8') ?></strong>
                  </div>

                  <?php if ($isTextInput) { ?>
                    <?php if ($tipe === 'textarea') { ?>
                      <textarea
                        name="jawaban[<?= (int) $pertanyaan['id'] ?>]"
                        class="form-control"
                        rows="3"
                        <?= (int) $pertanyaan['wajib'] === 1 ? 'required' : '' ?>
                      ></textarea>
                    <?php } else { ?>
                      <input
                        type="<?= in_array($tipe, array('number', 'month', 'date'), TRUE) ? $tipe : 'text' ?>"
                        name="jawaban[<?= (int) $pertanyaan['id'] ?>]"
                        class="form-control"
                        <?= (int) $pertanyaan['wajib'] === 1 ? 'required' : '' ?>
                      >
                    <?php } ?>
                  <?php } else { ?>
                    <?php $otherOption = NULL; ?>
                    <div class="questionnaire-options <?= $isMarsCategory ? 'questionnaire-options--mars' : '' ?>" <?= $isCheckbox && (int) $pertanyaan['wajib'] === 1 ? 'data-required-checkbox-group="true"' : '' ?>>
                      <?php foreach ($pertanyaan['opsi'] as $opsi) { ?>
                        <?php
                          $inputId = 'jawaban_'.$pertanyaan['id'].'_'.$opsi['id'];
                          $isOtherOption = strcasecmp(trim((string) $opsi['nilai']), 'Lainnya') === 0
                            || strcasecmp(trim((string) $opsi['label']), 'Lainnya') === 0;
                          if ($isOtherOption) {
                            $otherOption = $opsi;
                          }
                        ?>
                        <label for="<?= $inputId ?>">
                          <input
                            type="<?= $isCheckbox ? 'checkbox' : 'radio' ?>"
                            id="<?= $inputId ?>"
                            name="jawaban[<?= (int) $pertanyaan['id'] ?>]<?= $isCheckbox ? '[]' : '' ?>"
                            value="<?= htmlspecialchars($opsi['nilai'], ENT_QUOTES, 'UTF-8') ?>"
                            <?= $isOtherOption ? 'data-other-option="true" aria-controls="jawaban_lainnya_'.$pertanyaan['id'].'"' : '' ?>
                            <?= !$isCheckbox && (int) $pertanyaan['wajib'] === 1 ? 'required' : '' ?>
                          >
                          <?= htmlspecialchars($opsi['label'], ENT_QUOTES, 'UTF-8') ?>
                        </label>
                      <?php } ?>
                    </div>
                    <?php if ($otherOption) { ?>
                      <div class="questionnaire-other-answer" data-other-answer hidden>
                        <label for="jawaban_lainnya_<?= (int) $pertanyaan['id'] ?>">Tuliskan jawaban lainnya</label>
                        <input
                          type="text"
                          id="jawaban_lainnya_<?= (int) $pertanyaan['id'] ?>"
                          name="jawaban_lainnya[<?= (int) $pertanyaan['id'] ?>]"
                          class="form-control"
                          maxlength="500"
                          placeholder="Tulis jawaban Anda"
                          autocomplete="off"
                          disabled
                        >
                      </div>
                    <?php } ?>
                  <?php } ?>
                </div>
              <?php } ?>
            </div>
          </section>
        <?php } ?>

        <div class="form-group text-right mb-0">
          <button type="submit" class="btn btn-primary" id="submitDemographicQuestionnaire">Kirim</button>
        </div>
      </form>
    </div>
  </div>

  <style>
    .questionnaire-modal {
      display: none;
      position: fixed;
      z-index: 2000;
      inset: 0;
      width: 100%;
      height: 100%;
      overflow-y: auto;
      background-color: rgba(0, 0, 0, .62);
      padding: 32px 16px;
    }

    .questionnaire-modal-content {
      position: relative;
      background: #fff;
      margin: 0 auto;
      padding: 28px 32px;
      border-radius: 8px;
      width: 100%;
      max-width: 980px;
      box-shadow: 0 8px 24px rgba(0, 0, 0, .28);
    }

    .questionnaire-close {
      border: 0;
      background: transparent;
      color: #9aa0a6;
      font-size: 30px;
      font-weight: 700;
      line-height: 1;
      position: absolute;
      right: 18px;
      top: 18px;
      cursor: pointer;
    }

    .questionnaire-close:hover { color: #343a40; }
    .questionnaire-modal h2 { margin-right: 32px; margin-bottom: 6px; font-size: 28px; font-weight: 700; }
    .questionnaire-lead { color: #6c757d; margin-bottom: 22px; }
    .questionnaire-section h5 { font-size: 18px; font-weight: 700; margin-bottom: 10px; }

    .questionnaire-panel {
      border: 1px solid #dee2e6;
      border-radius: 8px;
      padding: 18px;
      margin-bottom: 20px;
      background: #fafbfc;
    }

    .questionnaire-instruction { color: #343a40; line-height: 1.6; margin-bottom: 18px; }
    .questionnaire-fields { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px; }
    .questionnaire-fields--mars { grid-template-columns: 1fr; }

    .questionnaire-panel--mars {
      border-color: #14847d;
      background: #f4fbfa;
    }

    .questionnaire-panel--drug {
      border-color: #2f6f9f;
      background: #f5f9fc;
    }

    .questionnaire-eyebrow {
      color: #14847d;
      font-size: 13px;
      font-weight: 800;
      letter-spacing: .08em;
      text-transform: uppercase;
    }

    .questionnaire-field {
      background: #fff;
      border: 1px solid #e4e7eb;
      border-radius: 8px;
      padding: 14px;
    }

    .questionnaire-question {
      display: flex;
      gap: 10px;
      align-items: flex-start;
      margin-bottom: 12px;
      color: #212529;
    }

    .questionnaire-question span {
      flex: 0 0 auto;
      min-width: 46px;
      border-radius: 6px;
      background: #e9ecef;
      color: #343a40;
      font-weight: 700;
      text-align: center;
      padding: 3px 6px;
    }

    .questionnaire-options { display: grid; grid-template-columns: 1fr; gap: 8px; }
    .questionnaire-options--mars { grid-template-columns: repeat(5, minmax(0, 1fr)); }

    .questionnaire-options label {
      display: flex;
      align-items: flex-start;
      gap: 8px;
      margin: 0;
      padding: 9px 10px;
      border: 1px solid #dee2e6;
      border-radius: 8px;
      cursor: pointer;
      line-height: 1.4;
    }

    .questionnaire-options label:hover { background: #f8f9fa; }
    .questionnaire-options input { width: 17px; height: 17px; margin-top: 2px; flex: 0 0 auto; }

    .questionnaire-other-answer {
      margin-top: 12px;
      padding: 12px;
      border-left: 4px solid #14847d;
      border-radius: 6px;
      background: #f4fbfa;
    }

    .questionnaire-other-answer label {
      display: block;
      margin-bottom: 7px;
      color: #285e61;
      font-weight: 700;
    }

    @media (max-width: 576px) {
      .questionnaire-modal { padding: 16px 10px; }
      .questionnaire-modal-content { padding: 22px 16px; }
      .questionnaire-modal h2 { font-size: 24px; }
      .questionnaire-panel { padding: 15px; }
      .questionnaire-fields { grid-template-columns: 1fr; }
      .questionnaire-options--mars { grid-template-columns: 1fr; }
    }

    @media (min-width: 577px) and (max-width: 900px) {
      .questionnaire-options--mars { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }
  </style>

  <script>
    window.addEventListener('load', function () {
      var modal = document.getElementById('welcomeModal');
      var closeButton = document.querySelector('.questionnaire-close');
      var form = document.getElementById('demographicQuestionnaireForm');
      var submitButton = document.getElementById('submitDemographicQuestionnaire');
      var csrfToken = document.getElementById('demographicCsrfToken');

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

      if (!modal || !closeButton) return;

      modal.style.display = 'block';
      closeButton.addEventListener('click', function () { modal.style.display = 'none'; });
      window.addEventListener('click', function (event) {
        if (event.target === modal) modal.style.display = 'none';
      });

      if (form && submitButton) {
        form.querySelectorAll('.questionnaire-field').forEach(function (field) {
          var otherOption = field.querySelector('input[data-other-option="true"]');
          var otherContainer = field.querySelector('[data-other-answer]');
          var otherInput = otherContainer ? otherContainer.querySelector('input[type="text"]') : null;

          if (!otherOption || !otherContainer || !otherInput) return;

          function syncOtherAnswer(focusInput) {
            var showOtherAnswer = otherOption.checked;
            otherContainer.hidden = !showOtherAnswer;
            otherInput.disabled = !showOtherAnswer;
            otherInput.required = showOtherAnswer;

            if (showOtherAnswer && focusInput) {
              otherInput.focus();
            }
          }

          field.querySelectorAll('input[type="radio"], input[type="checkbox"]').forEach(function (option) {
            option.addEventListener('change', function () {
              syncOtherAnswer(option === otherOption);
            });
          });

          syncOtherAnswer(false);
        });

        form.querySelectorAll('[data-required-checkbox-group="true"]').forEach(function (group) {
          var checkboxes = Array.prototype.slice.call(group.querySelectorAll('input[type="checkbox"]'));
          if (!checkboxes.length) return;

          function validateGroup() {
            var hasCheckedOption = checkboxes.some(function (checkbox) { return checkbox.checked; });
            checkboxes[0].setCustomValidity(hasCheckedOption ? '' : 'Pilih minimal satu jawaban.');
          }

          checkboxes.forEach(function (checkbox) {
            checkbox.addEventListener('change', function () {
              if (checkbox.value === 'Tidak ada' && checkbox.checked) {
                checkboxes.forEach(function (otherCheckbox) {
                  if (otherCheckbox !== checkbox) otherCheckbox.checked = false;
                });
              } else if (checkbox.checked) {
                checkboxes.forEach(function (otherCheckbox) {
                  if (otherCheckbox.value === 'Tidak ada') otherCheckbox.checked = false;
                });
              }
              validateGroup();
            });
          });
          validateGroup();
        });

        form.addEventListener('submit', function () {
          if (!form.checkValidity()) return;

          var latestCsrfToken = getCookie('<?= $this->config->item('csrf_cookie_name') ?>');
          if (csrfToken && latestCsrfToken) {
            csrfToken.value = latestCsrfToken;
          }

          submitButton.disabled = true;
          submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Menyimpan...';
        });
      }
    });
  </script>
<?php } ?>
