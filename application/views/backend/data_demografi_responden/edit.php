<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-7"><h1 class="m-0">Edit Data Kuesioner Responden</h1></div>
        <div class="col-sm-5">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="<?= base_url('backend/data-kuesioner-responden') ?>">Data Kuesioner Responden</a></li>
            <li class="breadcrumb-item active">Edit</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <section class="content">
    <div class="container-fluid">
      <?php if ($this->session->flashdata('message')) { ?><?= $this->session->flashdata('message') ?><?php } ?>
      <?php $idDemografi = (int) $submission['id_demografi']; ?>

      <form method="POST" action="<?= base_url('backend/data-kuesioner-responden/update/'.$idDemografi) ?>" id="demographicEditForm">
        <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">

        <div class="card card-primary card-outline">
          <div class="card-header">
            <a href="<?= base_url('backend/data-kuesioner-responden/detail/'.$idDemografi) ?>" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left mr-1"></i> Kembali</a>
            <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-save mr-1"></i> Simpan Perubahan</button>
          </div>
          <div class="card-body">
            <div class="alert alert-light border">
              <strong><?= htmlspecialchars($submission['nama'] ?? '-', ENT_QUOTES, 'UTF-8') ?></strong><br>
              <?= htmlspecialchars($submission['email'] ?? '-', ENT_QUOTES, 'UTF-8') ?><br>
              <span class="badge badge-light"><?= htmlspecialchars($submission['status_user_nama'] ?? '-', ENT_QUOTES, 'UTF-8') ?></span>
              <?php foreach ($questionnaires as $questionnaire) { ?>
                <span class="badge badge-primary"><?= htmlspecialchars($questionnaire['nama'], ENT_QUOTES, 'UTF-8') ?></span>
              <?php } ?>
            </div>
            <div class="alert alert-info">Form edit memuat seluruh bagian kuesioner aktif sesuai status akun responden.</div>

            <div class="demographic-edit-list">
              <?php foreach ($questionnaires as $questionnaire) { ?>
                <section class="questionnaire-edit-section">
                  <h5><?= htmlspecialchars($questionnaire['nama'], ENT_QUOTES, 'UTF-8') ?></h5>
                  <?php if (!empty($questionnaire['deskripsi'])) { ?>
                    <p class="text-muted mb-3"><?= htmlspecialchars($questionnaire['deskripsi'], ENT_QUOTES, 'UTF-8') ?></p>
                  <?php } ?>
              <?php foreach ($questionnaire['pertanyaan'] as $question) { ?>
                <?php
                  $questionId = (int) $question['id'];
                  $type = !empty($question['tipe']) ? $question['tipe'] : 'radio';
                  $answer = isset($answerMap[$questionId]) ? $answerMap[$questionId] : (isset($answerMap[$question['kode']]) ? $answerMap[$question['kode']] : '');
                  $required = (int) $question['wajib'] === 1 ? 'required' : '';
                  $selectedAnswer = $answer;
                  $selectedAnswers = $type === 'checkbox' ? array_map('trim', explode(';', (string) $answer)) : array((string) $answer);
                  $otherAnswer = '';
                  $otherOptionValue = NULL;

                  foreach ($question['opsi'] as $questionOption) {
                    $isOtherOption = strcasecmp(trim((string) $questionOption['nilai']), 'Lainnya') === 0
                      || strcasecmp(trim((string) $questionOption['label']), 'Lainnya') === 0;
                    if (!$isOtherOption) {
                      continue;
                    }

                    $otherOptionValue = (string) $questionOption['nilai'];
                    $otherPrefix = $otherOptionValue.':';
                    if (stripos((string) $answer, $otherPrefix) === 0) {
                      $selectedAnswer = $otherOptionValue;
                      $otherAnswer = trim(substr((string) $answer, strlen($otherPrefix)));
                    }
                    foreach ($selectedAnswers as $selectedIndex => $selectedValue) {
                      if (stripos($selectedValue, $otherPrefix) === 0) {
                        $selectedAnswers[$selectedIndex] = $otherOptionValue;
                        $otherAnswer = trim(substr($selectedValue, strlen($otherPrefix)));
                      }
                    }
                  }
                ?>
                <div class="demographic-question">
                  <label class="question-label" for="answer<?= $questionId ?>">
                    <span class="question-code"><?= htmlspecialchars($question['kode'], ENT_QUOTES, 'UTF-8') ?></span>
                    <?= htmlspecialchars($question['pertanyaan'], ENT_QUOTES, 'UTF-8') ?>
                    <?= (int) $question['wajib'] === 1 ? '<span class="text-danger">*</span>' : '' ?>
                  </label>

                  <?php if (in_array($type, array('text', 'number', 'month', 'date'), TRUE)) { ?>
                    <input type="<?= in_array($type, array('number', 'month', 'date'), TRUE) ? $type : 'text' ?>" class="form-control" id="answer<?= $questionId ?>" name="jawaban[<?= $questionId ?>]" value="<?= htmlspecialchars($answer, ENT_QUOTES, 'UTF-8') ?>" <?= $required ?>>
                  <?php } elseif ($type === 'textarea') { ?>
                    <textarea class="form-control" id="answer<?= $questionId ?>" name="jawaban[<?= $questionId ?>]" rows="3" <?= $required ?>><?= htmlspecialchars($answer, ENT_QUOTES, 'UTF-8') ?></textarea>
                  <?php } else { ?>
                    <div class="option-grid" <?= $type === 'checkbox' && (int) $question['wajib'] === 1 ? 'data-required-checkbox-group="true"' : '' ?>>
                      <?php foreach ($question['opsi'] as $option) { ?>
                        <?php
                          $inputId = 'answer'.$questionId.'_'.(int) $option['id'];
                          $isOtherOption = strcasecmp(trim((string) $option['nilai']), 'Lainnya') === 0
                            || strcasecmp(trim((string) $option['label']), 'Lainnya') === 0;
                        ?>
                        <div class="custom-control custom-<?= $type === 'checkbox' ? 'checkbox' : 'radio' ?>">
                          <input type="<?= $type === 'checkbox' ? 'checkbox' : 'radio' ?>" class="custom-control-input" id="<?= $inputId ?>" name="jawaban[<?= $questionId ?>]<?= $type === 'checkbox' ? '[]' : '' ?>" value="<?= htmlspecialchars($option['nilai'], ENT_QUOTES, 'UTF-8') ?>" <?= in_array((string) $option['nilai'], $selectedAnswers, TRUE) ? 'checked' : '' ?> <?= $isOtherOption ? 'data-other-option="true" aria-controls="otherAnswer'.$questionId.'"' : '' ?> <?= $type !== 'checkbox' ? $required : '' ?>>
                          <label class="custom-control-label" for="<?= $inputId ?>"><?= htmlspecialchars($option['label'], ENT_QUOTES, 'UTF-8') ?></label>
                        </div>
                      <?php } ?>
                    </div>
                    <?php if ($otherOptionValue !== NULL) { ?>
                      <?php $showOtherAnswer = in_array((string) $otherOptionValue, $selectedAnswers, TRUE); ?>
                      <div class="other-answer-field" data-other-answer <?= $showOtherAnswer ? '' : 'hidden' ?>>
                        <label for="otherAnswer<?= $questionId ?>">Tuliskan jawaban lainnya</label>
                        <input
                          type="text"
                          class="form-control"
                          id="otherAnswer<?= $questionId ?>"
                          name="jawaban_lainnya[<?= $questionId ?>]"
                          value="<?= htmlspecialchars($otherAnswer, ENT_QUOTES, 'UTF-8') ?>"
                          maxlength="500"
                          placeholder="Tulis jawaban responden"
                          autocomplete="off"
                          <?= $showOtherAnswer ? 'required' : 'disabled' ?>
                        >
                      </div>
                    <?php } ?>
                  <?php } ?>
                </div>
              <?php } ?>
                </section>
              <?php } ?>
            </div>
          </div>
          <div class="card-footer text-right">
            <a href="<?= base_url('backend/data-kuesioner-responden/detail/'.$idDemografi) ?>" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Simpan Perubahan</button>
          </div>
        </div>
      </form>
    </div>
  </section>
</div>

<style>
.demographic-edit-list { display:grid; gap:14px; }
.questionnaire-edit-section { display:grid; gap:14px; border:1px solid #d9e2ec; border-radius:8px; padding:18px; background:#f8fafc; }
.questionnaire-edit-section h5 { margin:0; font-weight:700; }
.demographic-question { border:1px solid #e3e8ef; border-radius:7px; padding:15px; background:#fff; }
.question-label { display:block; font-weight:700; margin-bottom:11px; }
.question-code { display:inline-block; min-width:72px; color:#0b5ed7; }
.option-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(220px,1fr)); gap:10px 18px; }
.other-answer-field { margin-top:12px; padding:12px; border-left:4px solid #17a2b8; border-radius:6px; background:#f1f9fb; }
.other-answer-field label { display:block; margin-bottom:7px; color:#155b66; font-weight:700; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
  var form = document.getElementById('demographicEditForm');
  if (!form) return;

  form.querySelectorAll('.demographic-question').forEach(function (question) {
    var otherRadio = question.querySelector('input[data-other-option="true"]');
    var otherContainer = question.querySelector('[data-other-answer]');
    var otherInput = otherContainer ? otherContainer.querySelector('input[type="text"]') : null;

    if (!otherRadio || !otherContainer || !otherInput) return;

    function syncOtherAnswer(focusInput) {
      var showOtherAnswer = otherRadio.checked;
      otherContainer.hidden = !showOtherAnswer;
      otherInput.disabled = !showOtherAnswer;
      otherInput.required = showOtherAnswer;

      if (showOtherAnswer && focusInput) {
        otherInput.focus();
      }
    }

    question.querySelectorAll('input[type="radio"], input[type="checkbox"]').forEach(function (radio) {
      radio.addEventListener('change', function () {
        syncOtherAnswer(radio === otherRadio);
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
          checkboxes.forEach(function (otherCheckbox) { if (otherCheckbox !== checkbox) otherCheckbox.checked = false; });
        } else if (checkbox.checked) {
          checkboxes.forEach(function (otherCheckbox) { if (otherCheckbox.value === 'Tidak ada') otherCheckbox.checked = false; });
        }
        validateGroup();
      });
    });
    validateGroup();
  });

  form.addEventListener('submit', function () {
    if (!form.checkValidity()) return;
    form.querySelectorAll('button[type="submit"]').forEach(function (button) { button.disabled = true; });
  });
});
</script>
