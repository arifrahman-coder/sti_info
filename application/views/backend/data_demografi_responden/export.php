<!DOCTYPE html>
<html lang="id" xmlns:x="urn:schemas-microsoft-com:office:excel">
<head><meta charset="utf-8"><title>Data Kuesioner Responden</title></head>
<body>
  <h3>Data Kuesioner Responden</h3>
  <table border="1">
    <tr><th>No</th><th>Nama</th><th>Email</th><th>No. HP</th><th>Status Akun</th><th>Jenis Responden</th><th>Kode Responden</th><th>Tanggal Isi</th><th>Jawaban</th></tr>
    <?php $number = 1; foreach ($submissions as $submission) { ?>
      <?php
        $idDemografi = (int) $submission['id_demografi'];
        $answers = isset($answersBySubmission[$idDemografi]) ? $answersBySubmission[$idDemografi] : array();
        $respondentType = !empty($submission['jenis_responden']) ? $submission['jenis_responden'] : (!empty($submission['kategori_nama']) ? $submission['kategori_nama'] : ($submission['status_user_nama'] ?? '-'));
        $respondentCode = !empty($submission['kode_responden']) ? $submission['kode_responden'] : (!empty($submission['no_responden']) ? $submission['no_responden'] : '-');
      ?>
      <tr>
        <td><?= $number++ ?></td>
        <td><?= htmlspecialchars($submission['nama'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
        <td><?= htmlspecialchars($submission['email'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
        <td><?= htmlspecialchars($submission['no_hp'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
        <td><?= htmlspecialchars($submission['status_user_nama'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
        <td><?= htmlspecialchars($respondentType, ENT_QUOTES, 'UTF-8') ?></td>
        <td><?= htmlspecialchars($respondentCode, ENT_QUOTES, 'UTF-8') ?></td>
        <td><?= htmlspecialchars($submission['created_at'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
        <td>
          <table border="1"><tr><th>Kode</th><th>Bagian Kuesioner</th><th>Pertanyaan</th><th>Jawaban</th></tr>
            <?php foreach ($answers as $answer) { ?>
              <tr>
                <td><?= htmlspecialchars(!empty($answer['kode']) ? $answer['kode'] : $answer['pertanyaan'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars(!empty($answer['kategori_nama']) ? $answer['kategori_nama'] : '-', ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars(!empty($answer['item']) ? $answer['item'] : $answer['pertanyaan'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($answer['jawaban'], ENT_QUOTES, 'UTF-8') ?></td>
              </tr>
            <?php } ?>
          </table>
        </td>
      </tr>
    <?php } ?>
  </table>
</body>
</html>
