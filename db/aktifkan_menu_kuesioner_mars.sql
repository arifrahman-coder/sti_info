-- Mengaktifkan MARS-5 sebagai menu mandiri untuk seluruh jenis akun frontend.
-- Aman dijalankan ulang dan tidak mengubah jawaban yang sudah tersimpan.

START TRANSACTION;

UPDATE `kuesioner_kategori`
SET `id_status` = NULL,
    `status` = 1,
    `updated_at` = NOW()
WHERE `nama` = 'Medication Adherence Rating Scale (MARS-5)';

UPDATE `kuesioner_pertanyaan`
SET `status` = 1,
    `updated_at` = NOW()
WHERE `kategori_id` IN (
  SELECT `id`
  FROM `kuesioner_kategori`
  WHERE `nama` = 'Medication Adherence Rating Scale (MARS-5)'
)
AND `kode` IN ('M1', 'M2', 'M3', 'M4', 'M5');

UPDATE `kuesioner_opsi`
SET `status` = 1,
    `updated_at` = NOW()
WHERE `pertanyaan_id` IN (
  SELECT `id`
  FROM `kuesioner_pertanyaan`
  WHERE `kategori_id` IN (
    SELECT `id`
    FROM `kuesioner_kategori`
    WHERE `nama` = 'Medication Adherence Rating Scale (MARS-5)'
  )
  AND `kode` IN ('M1', 'M2', 'M3', 'M4', 'M5')
);

COMMIT;
