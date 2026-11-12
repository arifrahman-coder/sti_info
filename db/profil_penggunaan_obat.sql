-- Profil Penggunaan Obat berdasarkan dokumen revisi
-- "Profil_Penggunaan_Obat_Telefarmasi_HIVAIDS FIX.docx" (28 Agustus 2026).
-- Skrip ini dapat dipakai untuk instalasi baru maupun upgrade tabel lama.

CREATE TABLE IF NOT EXISTS `profil_penggunaan_obat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `kode_pasien` varchar(50) DEFAULT NULL,
  `status_hiv` varchar(20) DEFAULT NULL,
  `diagnosis_utama` varchar(150) NOT NULL DEFAULT 'HIV positif (ICD: B20-B24)',
  `stadium_who` varchar(30) NOT NULL,
  `jumlah_cd4` int(11) DEFAULT NULL,
  `kategori_cd4` varchar(40) NOT NULL,
  `viral_load` varchar(50) NOT NULL,
  `komorbid` text NOT NULL,
  `alergi_obat` varchar(20) NOT NULL,
  `detail_alergi` text DEFAULT NULL,
  `status_terapi_arv` varchar(60) NOT NULL,
  `lini_pengobatan` varchar(30) DEFAULT NULL,
  `regimen_arv` varchar(150) DEFAULT NULL,
  `komponen_arv` text DEFAULT NULL,
  `bentuk_sediaan_arv` varchar(60) DEFAULT NULL,
  `kekuatan_dosis_arv` varchar(255) DEFAULT NULL,
  `aturan_pakai_arv` varchar(60) DEFAULT NULL,
  `aturan_pakai_arv_lainnya` varchar(150) DEFAULT NULL,
  `tanggal_mulai_arv` date DEFAULT NULL,
  `lama_penggunaan_arv` varchar(30) DEFAULT NULL,
  `riwayat_ganti_regimen` varchar(20) DEFAULT NULL,
  `alasan_ganti_regimen` text DEFAULT NULL,
  `sumber_obat` varchar(60) DEFAULT NULL,
  `sumber_obat_lainnya` varchar(150) DEFAULT NULL,
  `frekuensi_pengambilan` varchar(60) DEFAULT NULL,
  `frekuensi_pengambilan_lainnya` varchar(150) DEFAULT NULL,
  `penggunaan_obat_lainnya_arv` text DEFAULT NULL,
  `penggunaan_hormon_arv` text DEFAULT NULL,
  `status_terapi_prep` varchar(60) DEFAULT NULL,
  `lini_pengobatan_prep` varchar(30) DEFAULT NULL,
  `regimen_prep` varchar(150) DEFAULT NULL,
  `komponen_prep` text DEFAULT NULL,
  `bentuk_sediaan_prep` varchar(60) DEFAULT NULL,
  `kekuatan_dosis_prep` varchar(255) DEFAULT NULL,
  `aturan_pakai_prep` varchar(60) DEFAULT NULL,
  `tanggal_mulai_prep` date DEFAULT NULL,
  `lama_penggunaan_prep` varchar(30) DEFAULT NULL,
  `riwayat_ganti_regimen_prep` varchar(20) DEFAULT NULL,
  `alasan_ganti_regimen_prep` text DEFAULT NULL,
  `sumber_obat_prep` varchar(60) DEFAULT NULL,
  `sumber_obat_prep_lainnya` varchar(150) DEFAULT NULL,
  `frekuensi_pengambilan_prep` varchar(60) DEFAULT NULL,
  `frekuensi_pengambilan_prep_lainnya` varchar(150) DEFAULT NULL,
  `penggunaan_obat_lainnya_prep` text DEFAULT NULL,
  `penggunaan_hormon_prep` text DEFAULT NULL,
  `kotrimoksazol` varchar(20) DEFAULT NULL,
  `tpt_status` varchar(40) DEFAULT NULL,
  `obat_anti_tb` varchar(20) DEFAULT NULL,
  `detail_obat_anti_tb` varchar(150) DEFAULT NULL,
  `antijamur` varchar(40) DEFAULT NULL,
  `antijamur_lainnya` varchar(150) DEFAULT NULL,
  `obat_hepatitis` varchar(20) DEFAULT NULL,
  `detail_obat_hepatitis` varchar(150) DEFAULT NULL,
  `obat_ims` varchar(20) DEFAULT NULL,
  `detail_obat_ims` varchar(150) DEFAULT NULL,
  `obat_kronis` text DEFAULT NULL,
  `obat_kronis_lainnya` varchar(150) DEFAULT NULL,
  `suplemen` varchar(20) DEFAULT NULL,
  `detail_suplemen` varchar(150) DEFAULT NULL,
  `tingkat_kepatuhan` varchar(30) DEFAULT NULL,
  `metode_penilaian` text DEFAULT NULL,
  `metode_penilaian_lainnya` varchar(150) DEFAULT NULL,
  `riwayat_lupa` varchar(30) DEFAULT NULL,
  `efek_samping` text DEFAULT NULL,
  `efek_samping_lainnya` varchar(150) DEFAULT NULL,
  `potensi_interaksi` varchar(20) DEFAULT NULL,
  `detail_interaksi` varchar(255) DEFAULT NULL,
  `masalah_obat` text DEFAULT NULL,
  `masalah_obat_lainnya` varchar(150) DEFAULT NULL,
  `kebutuhan_konseling` varchar(30) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_profil_penggunaan_obat_user` (`id_user`),
  CONSTRAINT `fk_profil_penggunaan_obat_user`
    FOREIGN KEY (`id_user`) REFERENCES `user` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Upgrade non-destruktif untuk instalasi yang sudah memakai struktur lama.
ALTER TABLE `profil_penggunaan_obat`
  ADD COLUMN IF NOT EXISTS `kode_pasien` varchar(50) DEFAULT NULL AFTER `id_user`,
  ADD COLUMN IF NOT EXISTS `status_hiv` varchar(20) DEFAULT NULL AFTER `kode_pasien`,
  ADD COLUMN IF NOT EXISTS `penggunaan_obat_lainnya_arv` text DEFAULT NULL AFTER `frekuensi_pengambilan_lainnya`,
  ADD COLUMN IF NOT EXISTS `penggunaan_hormon_arv` text DEFAULT NULL AFTER `penggunaan_obat_lainnya_arv`,
  ADD COLUMN IF NOT EXISTS `status_terapi_prep` varchar(60) DEFAULT NULL AFTER `penggunaan_hormon_arv`,
  ADD COLUMN IF NOT EXISTS `lini_pengobatan_prep` varchar(30) DEFAULT NULL AFTER `status_terapi_prep`,
  ADD COLUMN IF NOT EXISTS `regimen_prep` varchar(150) DEFAULT NULL AFTER `lini_pengobatan_prep`,
  ADD COLUMN IF NOT EXISTS `komponen_prep` text DEFAULT NULL AFTER `regimen_prep`,
  ADD COLUMN IF NOT EXISTS `bentuk_sediaan_prep` varchar(60) DEFAULT NULL AFTER `komponen_prep`,
  ADD COLUMN IF NOT EXISTS `kekuatan_dosis_prep` varchar(255) DEFAULT NULL AFTER `bentuk_sediaan_prep`,
  ADD COLUMN IF NOT EXISTS `aturan_pakai_prep` varchar(60) DEFAULT NULL AFTER `kekuatan_dosis_prep`,
  ADD COLUMN IF NOT EXISTS `tanggal_mulai_prep` date DEFAULT NULL AFTER `aturan_pakai_prep`,
  ADD COLUMN IF NOT EXISTS `lama_penggunaan_prep` varchar(30) DEFAULT NULL AFTER `tanggal_mulai_prep`,
  ADD COLUMN IF NOT EXISTS `riwayat_ganti_regimen_prep` varchar(20) DEFAULT NULL AFTER `lama_penggunaan_prep`,
  ADD COLUMN IF NOT EXISTS `alasan_ganti_regimen_prep` text DEFAULT NULL AFTER `riwayat_ganti_regimen_prep`,
  ADD COLUMN IF NOT EXISTS `sumber_obat_prep` varchar(60) DEFAULT NULL AFTER `alasan_ganti_regimen_prep`,
  ADD COLUMN IF NOT EXISTS `sumber_obat_prep_lainnya` varchar(150) DEFAULT NULL AFTER `sumber_obat_prep`,
  ADD COLUMN IF NOT EXISTS `frekuensi_pengambilan_prep` varchar(60) DEFAULT NULL AFTER `sumber_obat_prep_lainnya`,
  ADD COLUMN IF NOT EXISTS `frekuensi_pengambilan_prep_lainnya` varchar(150) DEFAULT NULL AFTER `frekuensi_pengambilan_prep`,
  ADD COLUMN IF NOT EXISTS `penggunaan_obat_lainnya_prep` text DEFAULT NULL AFTER `frekuensi_pengambilan_prep_lainnya`,
  ADD COLUMN IF NOT EXISTS `penggunaan_hormon_prep` text DEFAULT NULL AFTER `penggunaan_obat_lainnya_prep`,
  MODIFY COLUMN `kategori_cd4` varchar(40) NOT NULL,
  MODIFY COLUMN `kotrimoksazol` varchar(20) DEFAULT NULL,
  MODIFY COLUMN `tpt_status` varchar(40) DEFAULT NULL,
  MODIFY COLUMN `obat_anti_tb` varchar(20) DEFAULT NULL,
  MODIFY COLUMN `antijamur` varchar(40) DEFAULT NULL,
  MODIFY COLUMN `obat_hepatitis` varchar(20) DEFAULT NULL,
  MODIFY COLUMN `obat_ims` varchar(20) DEFAULT NULL,
  MODIFY COLUMN `obat_kronis` text DEFAULT NULL,
  MODIFY COLUMN `suplemen` varchar(20) DEFAULT NULL,
  MODIFY COLUMN `tingkat_kepatuhan` varchar(30) DEFAULT NULL,
  MODIFY COLUMN `metode_penilaian` text DEFAULT NULL,
  MODIFY COLUMN `riwayat_lupa` varchar(30) DEFAULT NULL,
  MODIFY COLUMN `efek_samping` text DEFAULT NULL,
  MODIFY COLUMN `potensi_interaksi` varchar(20) DEFAULT NULL,
  MODIFY COLUMN `masalah_obat` text DEFAULT NULL,
  MODIFY COLUMN `kebutuhan_konseling` varchar(30) DEFAULT NULL;

UPDATE `profil_penggunaan_obat`
SET
  `kode_pasien` = COALESCE(NULLIF(`kode_pasien`, ''), CONCAT('ODHIV-', LPAD(`id_user`, 3, '0'))),
  `status_hiv` = COALESCE(NULLIF(`status_hiv`, ''), IF(LOWER(`diagnosis_utama`) LIKE '%negatif%', 'Negatif', 'Positif')),
  `kategori_cd4` = CASE
    WHEN `jumlah_cd4` IS NOT NULL AND `jumlah_cd4` < 200 THEN '< 200 sel/mm³'
    WHEN `jumlah_cd4` BETWEEN 200 AND 349 THEN '200–349 sel/mm³'
    WHEN `jumlah_cd4` BETWEEN 350 AND 499 THEN '350–499 sel/mm³'
    WHEN `jumlah_cd4` >= 500 THEN '≥ 500 sel/mm³'
    WHEN `kategori_cd4` IN ('< 200', '< 200 sel/mm3') THEN '< 200 sel/mm³'
    WHEN `kategori_cd4` IN ('200-349', '200–349') THEN '200–349 sel/mm³'
    WHEN `kategori_cd4` IN ('350-499', '350–499') THEN '350–499 sel/mm³'
    WHEN `kategori_cd4` IN ('>= 500 sel/mm3', '≥ 500') THEN '≥ 500 sel/mm³'
    ELSE `kategori_cd4`
  END,
  `lama_penggunaan_arv` = CASE
    WHEN `lama_penggunaan_arv` IN ('< 6 bulan', '< 6 bln') THEN '< 6 bln'
    WHEN `lama_penggunaan_arv` IN ('6-12 bulan', '6–12 bulan', '6-12 bln') THEN '6–12 bln'
    WHEN `lama_penggunaan_arv` IN ('1-3 tahun', '1–3 tahun', '1-3 thn') THEN '1–3 thn'
    WHEN `lama_penggunaan_arv` IN ('> 3 tahun', '> 3 thn') THEN '> 3 thn'
    ELSE `lama_penggunaan_arv`
  END;

CREATE TABLE IF NOT EXISTS `profil_penggunaan_obat_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_profil` int(11) NOT NULL,
  `nama_obat` varchar(150) NOT NULL,
  `golongan_obat` varchar(150) NOT NULL,
  `bentuk_sediaan` varchar(100) NOT NULL,
  `kekuatan_dosis` varchar(150) NOT NULL,
  `aturan_pakai` varchar(150) NOT NULL,
  `tanggal_mulai` date DEFAULT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_profil_penggunaan_obat_item_profil` (`id_profil`),
  CONSTRAINT `fk_profil_penggunaan_obat_item_profil`
    FOREIGN KEY (`id_profil`) REFERENCES `profil_penggunaan_obat` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
