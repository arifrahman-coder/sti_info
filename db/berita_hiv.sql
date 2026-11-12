CREATE TABLE IF NOT EXISTS `berita_hiv` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(150) NOT NULL,
  `ringkasan` varchar(255) NOT NULL,
  `field` text NOT NULL,
  `image` varchar(255) NOT NULL,
  `link_sumber` text NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `urutan` int(11) NOT NULL DEFAULT 0,
  `date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT IGNORE INTO `berita_hiv` (`id`, `title`, `ringkasan`, `field`, `image`, `link_sumber`, `status`, `urutan`, `date`) VALUES
(1, 'Kenali HIV/AIDS Sejak Dini', 'Pemeriksaan lebih awal membantu seseorang mendapatkan informasi, dukungan, dan perawatan yang tepat.', 'HIV dapat dikelola dengan baik ketika diketahui lebih awal. Pemeriksaan dan konseling membantu seseorang memahami kondisi tubuhnya, mengurangi risiko penularan, dan memulai perawatan sesuai anjuran tenaga kesehatan.', 'PosterHIV-1.png', '', 1, 1, '2026-07-12 00:00:00'),
(2, 'Pencegahan HIV Dimulai dari Informasi yang Benar', 'Informasi yang tepat membantu masyarakat memilih langkah pencegahan yang aman dan bertanggung jawab.', 'Pencegahan HIV dapat dilakukan melalui edukasi kesehatan seksual, pemeriksaan rutin sesuai risiko, penggunaan alat pelindung, dan konsultasi dengan tenaga kesehatan. Hindari stigma karena dukungan sosial sangat penting bagi kesehatan ODHA.', 'screening_hiv.webp', '', 1, 2, '2026-07-12 00:00:00'),
(3, 'Dukungan untuk ODHA Meningkatkan Kualitas Hidup', 'Dukungan keluarga dan lingkungan membuat pengobatan HIV lebih mudah dijalani secara konsisten.', 'Orang dengan HIV/AIDS membutuhkan akses informasi, layanan kesehatan, dan lingkungan yang tidak menghakimi. Dengan dukungan yang baik, ODHA dapat menjalani aktivitas harian, mengikuti terapi, dan menjaga kualitas hidup.', 'tentang-kami.png', '', 1, 3, '2026-07-12 00:00:00');
