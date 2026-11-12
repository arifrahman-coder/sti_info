<?php
$escape = function ($value) { return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8'); };
$answerMap = isset($answerMap) && is_array($answerMap) ? $answerMap : array();
?>
<div class="mars-question-list">
  <?php foreach ($questions as $question) { ?>
    <?php
      $questionId = (int) $question['id'];
      $questionCode = (string) $question['kode'];
      $currentAnswer = isset($answerMap[$questionId])
        ? (string) $answerMap[$questionId]
        : (isset($answerMap[$questionCode]) ? (string) $answerMap[$questionCode] : '');
    ?>
    <div class="card card-outline card-secondary mars-question">
      <div class="card-header">
        <h3 class="card-title"><span class="badge badge-primary mr-2"><?= $escape($questionCode) ?></span><?= $escape($question['pertanyaan']) ?></h3>
      </div>
      <div class="card-body">
        <div class="mars-options" role="radiogroup" aria-label="<?= $escape($question['pertanyaan']) ?>">
          <?php foreach ($question['opsi'] as $option) { ?>
            <?php $inputId = 'mars_'.$questionId.'_'.(int) $option['id']; ?>
            <label class="mars-option <?= $currentAnswer === (string) $option['nilai'] ? 'selected' : '' ?>" for="<?= $inputId ?>">
              <input
                type="radio"
                id="<?= $inputId ?>"
                name="jawaban[<?= $questionId ?>]"
                value="<?= $escape($option['nilai']) ?>"
                <?= $currentAnswer === (string) $option['nilai'] ? 'checked' : '' ?>
                required
              >
              <span><?= $escape($option['label']) ?></span>
            </label>
          <?php } ?>
        </div>
      </div>
    </div>
  <?php } ?>
</div>

<style>
.mars-question{border-radius:.5rem;overflow:hidden}.mars-question .card-header{background:#f8fafc}.mars-question .card-title{font-size:1rem;font-weight:600;display:flex;align-items:center}.mars-options{display:grid;grid-template-columns:repeat(5,minmax(115px,1fr));gap:.65rem}.mars-option{display:flex;align-items:center;justify-content:center;gap:.5rem;margin:0;padding:.8rem .65rem;border:1px solid #d6dce4;border-radius:.45rem;background:#fff;cursor:pointer;text-align:center;transition:.15s}.mars-option:hover{border-color:#80bdff;background:#f4f9ff}.mars-option.selected{border-color:#007bff;background:#eaf4ff;color:#004a99;font-weight:600}.mars-option input{width:17px;height:17px;flex:0 0 auto}@media(max-width:991px){.mars-options{grid-template-columns:repeat(2,minmax(140px,1fr))}}@media(max-width:575px){.mars-options{grid-template-columns:1fr}.mars-option{justify-content:flex-start}}
</style>
<script>
document.addEventListener('DOMContentLoaded',function(){document.querySelectorAll('.mars-option input').forEach(function(input){input.addEventListener('change',function(){var group=input.closest('.mars-options');group.querySelectorAll('.mars-option').forEach(function(label){label.classList.remove('selected');});input.closest('.mars-option').classList.add('selected');});});});
</script>
