-- Menggabungkan MARS-5 ke modal kuesioner onboarding untuk responden ODHIV.
-- Skrip ini aman dijalankan ulang. Jawaban dari tabel fitur lama dipindahkan
-- ke jawaban_kuesioner sebelum kedua tabel khusus dihapus.

CREATE TABLE IF NOT EXISTS `kuesioner_kepatuhan_pengisian` (
  `id_pengisian` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `jenis_kuesioner` varchar(30) NOT NULL DEFAULT 'MARS5',
  `nama_kuesioner` varchar(150) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id_pengisian`),
  UNIQUE KEY `user_jenis_kuesioner` (`id_user`, `jenis_kuesioner`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `kuesioner_kepatuhan_jawaban` (
  `id_jawaban` int(11) NOT NULL AUTO_INCREMENT,
  `id_pengisian` int(11) NOT NULL,
  `pertanyaan_id` int(11) DEFAULT NULL,
  `kode_pertanyaan` varchar(20) NOT NULL,
  `pertanyaan` text NOT NULL,
  `jawaban` varchar(50) NOT NULL,
  `skor` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id_jawaban`),
  UNIQUE KEY `pengisian_kode_pertanyaan` (`id_pengisian`, `kode_pertanyaan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

START TRANSACTION;

INSERT INTO `kuesioner_kategori`
  (`id_status`, `nama`, `deskripsi`, `label_pertanyaan`, `urutan`, `status`, `created_at`, `updated_at`)
SELECT
  `status_user`.`id`,
  'Medication Adherence Rating Scale (MARS-5)',
  'Di bawah ini terdapat beberapa pernyataan. Pilih satu jawaban yang paling sesuai dengan cara Anda menggunakan obat-obatan. Tidak ada jawaban yang salah atau benar.',
  'Pernyataan',
  4,
  1,
  NOW(),
  NOW()
FROM `status_user`
WHERE LOWER(TRIM(`status_user`.`nama`)) = 'odhiv'
  AND NOT EXISTS (
    SELECT 1
    FROM `kuesioner_kategori`
    WHERE `kuesioner_kategori`.`nama` = 'Medication Adherence Rating Scale (MARS-5)'
  )
LIMIT 1;

INSERT INTO `kuesioner_pertanyaan`
  (`kategori_id`, `kode`, `pertanyaan`, `tipe`, `kunci_jawaban`, `wajib`, `urutan`, `status`, `created_at`, `updated_at`)
SELECT
  `kategori`.`id`,
  `instrumen`.`kode`,
  `instrumen`.`pertanyaan`,
  'radio',
  NULL,
  1,
  `instrumen`.`urutan`,
  1,
  NOW(),
  NOW()
FROM `kuesioner_kategori` AS `kategori`
CROSS JOIN (
  SELECT 'M1' AS `kode`, 'Saya lupa minum obat saya' AS `pertanyaan`, 1 AS `urutan`
  UNION ALL SELECT 'M2', 'Saya mengubah dosis obat saya', 2
  UNION ALL SELECT 'M3', 'Saya berhenti minum obat saya untuk beberapa waktu', 3
  UNION ALL SELECT 'M4', 'Saya memutuskan untuk tidak menaati dosis', 4
  UNION ALL SELECT 'M5', 'Saya minum obat kurang dari aturan yang tertera', 5
) AS `instrumen`
WHERE `kategori`.`nama` = 'Medication Adherence Rating Scale (MARS-5)'
  AND NOT EXISTS (
    SELECT 1
    FROM `kuesioner_pertanyaan` AS `pertanyaan_lama`
    WHERE `pertanyaan_lama`.`kode` = `instrumen`.`kode`
  );

INSERT INTO `kuesioner_opsi`
  (`pertanyaan_id`, `label`, `nilai`, `skor`, `urutan`, `status`, `created_at`, `updated_at`)
SELECT
  `pertanyaan`.`id`,
  `opsi`.`label`,
  `opsi`.`label`,
  `opsi`.`skor`,
  `opsi`.`urutan`,
  1,
  NOW(),
  NOW()
FROM `kuesioner_pertanyaan` AS `pertanyaan`
INNER JOIN `kuesioner_kategori` AS `kategori`
  ON `kategori`.`id` = `pertanyaan`.`kategori_id`
CROSS JOIN (
  SELECT 'Selalu' AS `label`, 1 AS `skor`, 1 AS `urutan`
  UNION ALL SELECT 'Sering', 2, 2
  UNION ALL SELECT 'Kadang-kadang', 3, 3
  UNION ALL SELECT 'Jarang', 4, 4
  UNION ALL SELECT 'Tidak pernah', 5, 5
) AS `opsi`
WHERE `kategori`.`nama` = 'Medication Adherence Rating Scale (MARS-5)'
  AND `pertanyaan`.`kode` IN ('M1', 'M2', 'M3', 'M4', 'M5')
  AND NOT EXISTS (
    SELECT 1
    FROM `kuesioner_opsi` AS `opsi_lama`
    WHERE `opsi_lama`.`pertanyaan_id` = `pertanyaan`.`id`
      AND `opsi_lama`.`nilai` = `opsi`.`label`
  );

-- Buat wadah generik untuk pengisian MARS-5 lama yang belum memiliki data demografi.
INSERT INTO `demografi`
  (`id_user`, `jenis_responden`, `kode_responden`, `no_responden`, `usia`, `gender`, `kelas`,
   `pendidikan_ayah`, `pendidikan_ibu`, `pekerjaan_ayah`, `pekerjaan_ibu`, `pernah_informasi_hiv`,
   `sumber_informasi_hiv`, `pekerjaan`, `pendidikan`, `pendapatan`, `pengalaman`, `created_at`)
SELECT
  `pengisian`.`id_user`,
  'Responden ODHIV (Orang Dengan HIV)',
  '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', `pengisian`.`created_at`
FROM `kuesioner_kepatuhan_pengisian` AS `pengisian`
WHERE `pengisian`.`jenis_kuesioner` = 'MARS5'
  AND NOT EXISTS (
    SELECT 1 FROM `demografi` WHERE `demografi`.`id_user` = `pengisian`.`id_user`
  );

INSERT INTO `jawaban_kuesioner` (`id_demografi`, `pertanyaan`, `jawaban`)
SELECT
  `demografi`.`id_demografi`,
  `jawaban_lama`.`kode_pertanyaan`,
  `jawaban_lama`.`jawaban`
FROM `kuesioner_kepatuhan_pengisian` AS `pengisian`
INNER JOIN `kuesioner_kepatuhan_jawaban` AS `jawaban_lama`
  ON `jawaban_lama`.`id_pengisian` = `pengisian`.`id_pengisian`
INNER JOIN `demografi`
  ON `demografi`.`id_demografi` = (
    SELECT MAX(`demografi_terakhir`.`id_demografi`)
    FROM `demografi` AS `demografi_terakhir`
    WHERE `demografi_terakhir`.`id_user` = `pengisian`.`id_user`
  )
INNER JOIN `kuesioner_pertanyaan` AS `pertanyaan_baru`
  ON `pertanyaan_baru`.`kode` = `jawaban_lama`.`kode_pertanyaan`
INNER JOIN `kuesioner_kategori` AS `kategori_baru`
  ON `kategori_baru`.`id` = `pertanyaan_baru`.`kategori_id`
  AND `kategori_baru`.`nama` = 'Medication Adherence Rating Scale (MARS-5)'
WHERE `pengisian`.`jenis_kuesioner` = 'MARS5'
  AND NOT EXISTS (
    SELECT 1
    FROM `jawaban_kuesioner` AS `jawaban_baru`
    WHERE `jawaban_baru`.`id_demografi` = `demografi`.`id_demografi`
      AND `jawaban_baru`.`pertanyaan` = `jawaban_lama`.`kode_pertanyaan`
  );

COMMIT;

DROP TABLE IF EXISTS `kuesioner_kepatuhan_jawaban`;
DROP TABLE IF EXISTS `kuesioner_kepatuhan_pengisian`;
