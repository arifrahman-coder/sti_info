-- Pre-Test dan Post-Test otomatis untuk setiap modul pembelajaran.
-- Bank soal bersumber dari "Kuesioner Pengetahuan HIV AIDS FIX (3).docx".

CREATE TABLE IF NOT EXISTS `bank_soal_tes_hiv` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nomor` tinyint(3) unsigned NOT NULL,
  `kategori` varchar(120) NOT NULL,
  `pertanyaan` text NOT NULL,
  `kunci_jawaban` enum('B','S') NOT NULL,
  `aktif` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_bank_soal_tes_hiv_nomor` (`nomor`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `modul_tes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_modul_pembelajaran` int(11) NOT NULL,
  `tipe` enum('pre_test','post_test') NOT NULL,
  `judul` varchar(100) NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_modul_tes_tipe` (`id_modul_pembelajaran`,`tipe`),
  KEY `idx_modul_tes_modul` (`id_modul_pembelajaran`),
  CONSTRAINT `fk_modul_tes_modul` FOREIGN KEY (`id_modul_pembelajaran`) REFERENCES `modul_pembelajaran` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `hasil_tes_modul` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_modul_tes` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `jumlah_soal` tinyint(3) unsigned NOT NULL,
  `jumlah_benar` tinyint(3) unsigned NOT NULL,
  `jumlah_salah` tinyint(3) unsigned NOT NULL,
  `nilai` decimal(5,2) NOT NULL,
  `completed_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_hasil_tes_modul_user` (`id_modul_tes`,`id_user`),
  KEY `idx_hasil_tes_modul_user` (`id_user`),
  CONSTRAINT `fk_hasil_tes_modul_tes` FOREIGN KEY (`id_modul_tes`) REFERENCES `modul_tes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_hasil_tes_modul_user` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `jawaban_tes_modul` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_hasil_tes` bigint(20) unsigned NOT NULL,
  `id_bank_soal` int(11) NOT NULL,
  `jawaban` enum('B','S') NOT NULL,
  `is_benar` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_jawaban_tes_modul_soal` (`id_hasil_tes`,`id_bank_soal`),
  KEY `idx_jawaban_tes_modul_bank` (`id_bank_soal`),
  CONSTRAINT `fk_jawaban_tes_modul_hasil` FOREIGN KEY (`id_hasil_tes`) REFERENCES `hasil_tes_modul` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_jawaban_tes_modul_bank` FOREIGN KEY (`id_bank_soal`) REFERENCES `bank_soal_tes_hiv` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `bank_soal_tes_hiv` (`nomor`, `kategori`, `pertanyaan`, `kunci_jawaban`, `aktif`) VALUES
(1, 'Pengertian HIV dan AIDS', 'HIV (Human Influenza Virus) adalah penyakit yang menurunkan daya tahan tubuh manusia.', 'S', 1),
(2, 'Pengertian HIV dan AIDS', 'AIDS (Acquired Immuno Deficiency Syndrom) adalah kumpulan gejala penyakit akibat menurunnya daya tahan tubuh seseorang.', 'B', 1),
(3, 'Pengertian HIV dan AIDS', 'Infeksi HIV tidak mengakibatkan terjadinya penurunan kekebalan tubuh yang terus-menerus.', 'S', 1),
(4, 'Pengertian HIV dan AIDS', 'Orang yang terinfeksi HIV dan AIDS menyebabkan daya tahan tubuhnya turun menjadi lebih rentan terhadap berbagai macam infeksi.', 'B', 1),
(5, 'Pengertian HIV dan AIDS', 'Timbulnya berbagai infeksi tertentu bukan merupakan tanda bahwa infeksi HIV telah berkembang menjadi AIDS.', 'S', 1),
(6, 'Pengertian HIV dan AIDS', 'Human Immunodeficiency Virus adalah penyakit yang menurunkan daya tahan tubuh manusia.', 'B', 1),
(7, 'Gejala HIV dan AIDS', 'Sebagian besar orang yang terinfeksi HIV tidak menyadarinya karena tidak ada gejala yang tampak segera setelah terjadinya infeksi awal.', 'B', 1),
(8, 'Gejala HIV dan AIDS', 'HIV adalah penyakit yang tidak mematikan.', 'S', 1),
(9, 'Gejala HIV dan AIDS', 'Seseorang yang menderita HIV tidak dapat menularkan virus tersebut kepada orang lain.', 'S', 1),
(10, 'Gejala HIV dan AIDS', 'Satu-satunya cara untuk mengetahui positif HIV dengan cara tes HIV.', 'B', 1),
(11, 'Gejala HIV dan AIDS', 'VCT adalah test laboratorium untuk mengetahui positif atau negative HIV.', 'B', 1),
(12, 'Gejala HIV dan AIDS', 'HIV dapat dirasakan segera setelah seseorang terkena virus HIV.', 'S', 1),
(13, 'Penularan HIV dan AIDS', 'HIV dan AIDS menular melalui perpindahan virus HIV dari darah dan atau cairan sperma atau vagina seorang penderita kepada orang lain.', 'B', 1),
(14, 'Penularan HIV dan AIDS', 'Hubungan seksual dapat menularkan HIV.', 'B', 1),
(15, 'Penularan HIV dan AIDS', 'Penggunaan jarum suntik yang sama dengan penderita HIV dan AIDS tidak dapat menularkan HIV.', 'S', 1),
(16, 'Penularan HIV dan AIDS', 'Air susu ibu yang terinfeksi HIV tidak dapat menularkan ke bayinya.', 'S', 1),
(17, 'Alasan HIV dan AIDS perlu diwaspadai', 'Belum ada vaksin untuk melakukan pencegahan HIV dan AIDS', 'B', 1),
(18, 'Alasan HIV dan AIDS perlu diwaspadai', 'HIV dan AIDS dapat menyerang siapa saja tanpa mengenal perbedaan warna kulit, kelamin, usia, dan sebagainya.', 'B', 1),
(19, 'Alasan HIV dan AIDS perlu diwaspadai', 'Laju penyebarannya HIV dan AIDS lambat dan sebagian besar penularannya terjadi melalui seksual yang menyimpang sehingga mudah penanganannya.', 'S', 1),
(20, 'Alasan HIV dan AIDS perlu diwaspadai', 'AIDS tidak menyebabkan kematian sehingga tidak perlu dihindari.', 'S', 1),
(21, 'Kegiatan berisiko dan tidak berisiko', 'HIV dan AIDS bisa menular melalui aktifitas sosial biasa.', 'S', 1),
(22, 'Kegiatan berisiko dan tidak berisiko', 'Berjabat tangan dengan pengidap HIV dan atau penderita AIDS dapat menularkan HIV dan AIDS.', 'S', 1),
(23, 'Kegiatan berisiko dan tidak berisiko', 'Mendapat tranfusi darah dari penderita HIV dapat tertular HIV.', 'B', 1),
(24, 'Kegiatan berisiko dan tidak berisiko', 'Makan atau minum dengan pengidap HIV dan atau penderita AIDS atau menggunakan peralatan makan dan minum yang sama dapat menularkan HIV dan AIDS.', 'S', 1),
(25, 'Kegiatan berisiko dan tidak berisiko', 'Berhubungan seksual dengan penderita HIV dapat tertular HIV.', 'B', 1),
(26, 'Kegiatan berisiko dan tidak berisiko', 'Gigitan nyamuk atau serangga dapat menjadi penyebab HIV dan AIDS.', 'S', 1),
(27, 'Kegiatan berisiko dan tidak berisiko', 'Pecandu narkoba suntik sangat berisiko tertular HIV.', 'B', 1),
(28, 'Kegiatan berisiko dan tidak berisiko', 'Berganti-ganti pasangan seks dapat meningkatkan risiko tertular HIV.', 'B', 1),
(29, 'Kegiatan berisiko dan tidak berisiko', 'Memakai pakaian bekas pengidap HIV dan AIDS tidak dapat menularkan HIV dan AIDS.', 'B', 1),
(30, 'Pencegahan HIV dan AIDS', 'Berenang bersama penderita HIV dan AIDS dapat menularkan HIV dan AIDS.', 'S', 1),
(31, 'Pencegahan HIV dan AIDS', 'HIV dan AIDS dapat dicegah dengan tidak melakukan hubungan seks tidak aman dan berganti-ganti pasangan.', 'B', 1),
(32, 'Pencegahan HIV dan AIDS', 'Penggunaan kondom saat berhubungan seksual tidak dapat mencegah tertularnya HIV dan AIDS.', 'S', 1),
(33, 'Pencegahan HIV dan AIDS', 'Tidak menggunakan narkotika suntik dapat mencegah HIV dan AIDS.', 'B', 1),
(34, 'Pencegahan HIV dan AIDS', 'Oral seks merupakan cara mencegah penularan HIV.', 'S', 1)
ON DUPLICATE KEY UPDATE
  `kategori` = VALUES(`kategori`),
  `pertanyaan` = VALUES(`pertanyaan`),
  `kunci_jawaban` = VALUES(`kunci_jawaban`),
  `aktif` = VALUES(`aktif`);

INSERT INTO `modul_tes` (`id_modul_pembelajaran`, `tipe`, `judul`, `created_at`)
SELECT `id`, 'pre_test', 'Pre-Test Pengetahuan HIV dan AIDS', NOW()
FROM `modul_pembelajaran`
ON DUPLICATE KEY UPDATE `judul` = VALUES(`judul`);

INSERT INTO `modul_tes` (`id_modul_pembelajaran`, `tipe`, `judul`, `created_at`)
SELECT `id`, 'post_test', 'Post-Test Pengetahuan HIV dan AIDS', NOW()
FROM `modul_pembelajaran`
ON DUPLICATE KEY UPDATE `judul` = VALUES(`judul`);
