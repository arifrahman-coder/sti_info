-- Revisi data demografi khusus modal Responden ODHIV (Orang Dengan HIV).
-- Sumber: Data_Demografi_Telefarmasi_HIVAIDS (3).docx, bagian ODHIV.
--
-- Batas perubahan:
--   * hanya kategori "Responden ODHIV (Orang Dengan HIV)";
--   * tidak mengubah kategori atau butir MARS-5 (M1-M5);
--   * tidak mengubah fitur profil penggunaan obat.
--
-- Skrip aman dijalankan ulang. Nilai opsi lama dipertahankan untuk menjaga
-- kompatibilitas jawaban yang sudah tersimpan; yang diperbarui hanya label
-- tampilannya agar sama dengan dokumen revisi.

SET NAMES utf8mb4;
START TRANSACTION;

SET @odhiv_category_id := (
    SELECT id
    FROM kuesioner_kategori
    WHERE nama = 'Responden ODHIV (Orang Dengan HIV)'
    ORDER BY id
    LIMIT 1
);

-- Pastikan seluruh butir ODHIV lama tetap aktif, wajib, dan berurutan sesuai
-- dokumen. ODHIV7K merupakan isian kabupaten/kota untuk lanjutan poin Domisili.
UPDATE kuesioner_pertanyaan
SET tipe = CASE WHEN kode IN ('ODHIV1', 'ODHIV7K') THEN 'text' ELSE 'radio' END,
    wajib = 1,
    urutan = CASE kode
        WHEN 'ODHIV1'  THEN 1
        WHEN 'ODHIV2'  THEN 2
        WHEN 'ODHIV3'  THEN 3
        WHEN 'ODHIV4'  THEN 4
        WHEN 'ODHIV5'  THEN 5
        WHEN 'ODHIV6'  THEN 6
        WHEN 'ODHIV7'  THEN 7
        WHEN 'ODHIV7K' THEN 8
        WHEN 'ODHIV8'  THEN 9
        WHEN 'ODHIV9'  THEN 10
        WHEN 'ODHIV10' THEN 11
        WHEN 'ODHIV11' THEN 12
        WHEN 'ODHIV12' THEN 13
        WHEN 'ODHIV13' THEN 14
        WHEN 'ODHIV14' THEN 15
        WHEN 'ODHIV15' THEN 16
        WHEN 'ODHIV16' THEN 17
        WHEN 'ODHIV17' THEN 18
        WHEN 'ODHIV18' THEN 19
        WHEN 'ODHIV19' THEN 20
        ELSE urutan
    END,
    status = 1,
    updated_at = NOW()
WHERE kategori_id = @odhiv_category_id
  AND kode IN (
      'ODHIV1', 'ODHIV2', 'ODHIV3', 'ODHIV4', 'ODHIV5', 'ODHIV6',
      'ODHIV7', 'ODHIV7K', 'ODHIV8', 'ODHIV9', 'ODHIV10', 'ODHIV11',
      'ODHIV12', 'ODHIV13', 'ODHIV14', 'ODHIV15', 'ODHIV16',
      'ODHIV17', 'ODHIV18', 'ODHIV19'
  );

-- Dua butir yang ditambahkan pada revisi terbaru.
INSERT INTO kuesioner_pertanyaan
    (kategori_id, kode, pertanyaan, tipe, kunci_jawaban, wajib, urutan, status, created_at, updated_at)
SELECT @odhiv_category_id, 'ODHIV20', 'Tingkat pengetahuan tentang HIV/AIDS',
       'radio', NULL, 1, 21, 1, NOW(), NOW()
WHERE @odhiv_category_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM kuesioner_pertanyaan WHERE kode = 'ODHIV20'
  );

INSERT INTO kuesioner_pertanyaan
    (kategori_id, kode, pertanyaan, tipe, kunci_jawaban, wajib, urutan, status, created_at, updated_at)
SELECT @odhiv_category_id, 'ODHIV21', 'Sumber informasi kesehatan utama',
       'radio', NULL, 1, 22, 1, NOW(), NOW()
WHERE @odhiv_category_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM kuesioner_pertanyaan WHERE kode = 'ODHIV21'
  );

-- Jika kedua kode sudah pernah dibuat, sinkronkan metadata tanpa membuat duplikat.
UPDATE kuesioner_pertanyaan
SET pertanyaan = CASE kode
        WHEN 'ODHIV20' THEN 'Tingkat pengetahuan tentang HIV/AIDS'
        WHEN 'ODHIV21' THEN 'Sumber informasi kesehatan utama'
    END,
    tipe = 'radio',
    kunci_jawaban = NULL,
    wajib = 1,
    urutan = CASE kode WHEN 'ODHIV20' THEN 21 WHEN 'ODHIV21' THEN 22 END,
    status = 1,
    updated_at = NOW()
WHERE kategori_id = @odhiv_category_id
  AND kode IN ('ODHIV20', 'ODHIV21');

-- Samakan tipografi label yang terlihat pengguna dengan dokumen Word.
-- Kolom nilai sengaja tidak diubah agar jawaban lama tetap dapat dipilih saat diedit.
UPDATE kuesioner_opsi AS o
JOIN kuesioner_pertanyaan AS q ON q.id = o.pertanyaan_id
SET o.label = CASE
        WHEN q.kode = 'ODHIV2'  AND o.nilai = '15-24 th' THEN '15–24 th'
        WHEN q.kode = 'ODHIV2'  AND o.nilai = '25-34 th' THEN '25–34 th'
        WHEN q.kode = 'ODHIV2'  AND o.nilai = '35-44 th' THEN '35–44 th'
        WHEN q.kode = 'ODHIV2'  AND o.nilai = '45-54 th' THEN '45–54 th'
        WHEN q.kode = 'ODHIV2'  AND o.nilai = '>= 55 th' THEN '≥ 55 th'
        WHEN q.kode = 'ODHIV8'  AND o.nilai = 'Rp1-3 juta' THEN 'Rp1–3 juta'
        WHEN q.kode = 'ODHIV8'  AND o.nilai = 'Rp3-5 juta' THEN 'Rp3–5 juta'
        WHEN q.kode = 'ODHIV9'  AND o.nilai = '1-3 tahun' THEN '1–3 tahun'
        WHEN q.kode = 'ODHIV9'  AND o.nilai = '4-6 tahun' THEN '4–6 tahun'
        WHEN q.kode = 'ODHIV11' AND o.nilai = '6-12 bulan' THEN '6–12 bulan'
        WHEN q.kode = 'ODHIV11' AND o.nilai = '1-3 tahun' THEN '1–3 tahun'
        WHEN q.kode = 'ODHIV13' AND o.nilai = 'Setiap 2-3 bulan' THEN 'Setiap 2–3 bulan'
        ELSE o.label
    END,
    o.updated_at = NOW()
WHERE q.kategori_id = @odhiv_category_id
  AND (
      (q.kode = 'ODHIV2'  AND o.nilai IN ('15-24 th', '25-34 th', '35-44 th', '45-54 th', '>= 55 th'))
      OR (q.kode = 'ODHIV8'  AND o.nilai IN ('Rp1-3 juta', 'Rp3-5 juta'))
      OR (q.kode = 'ODHIV9'  AND o.nilai IN ('1-3 tahun', '4-6 tahun'))
      OR (q.kode = 'ODHIV11' AND o.nilai IN ('6-12 bulan', '1-3 tahun'))
      OR (q.kode = 'ODHIV13' AND o.nilai = 'Setiap 2-3 bulan')
  );

-- Opsi untuk ODHIV20 dan ODHIV21. LEFT JOIN membuat penyisipan idempoten.
INSERT INTO kuesioner_opsi
    (pertanyaan_id, label, nilai, skor, urutan, status, created_at, updated_at)
SELECT q.id, d.label, d.nilai, 0, d.urutan, 1, NOW(), NOW()
FROM kuesioner_pertanyaan AS q
JOIN (
    SELECT 'ODHIV20' AS kode, 'Sangat memahami' AS label, 'Sangat memahami' AS nilai, 1 AS urutan
    UNION ALL SELECT 'ODHIV20', 'Cukup memahami', 'Cukup memahami', 2
    UNION ALL SELECT 'ODHIV20', 'Kurang memahami', 'Kurang memahami', 3
    UNION ALL SELECT 'ODHIV20', 'Tidak tahu', 'Tidak tahu', 4
    UNION ALL SELECT 'ODHIV21', 'Tenaga kesehatan', 'Tenaga kesehatan', 1
    UNION ALL SELECT 'ODHIV21', 'Internet/media sosial', 'Internet/media sosial', 2
    UNION ALL SELECT 'ODHIV21', 'Keluarga/teman', 'Keluarga/teman', 3
    UNION ALL SELECT 'ODHIV21', 'TV/radio/media cetak', 'TV/radio/media cetak', 4
) AS d ON d.kode = q.kode
LEFT JOIN kuesioner_opsi AS existing_option
       ON existing_option.pertanyaan_id = q.id
      AND existing_option.nilai = d.nilai
WHERE q.kategori_id = @odhiv_category_id
  AND existing_option.id IS NULL;

-- Sinkronkan opsi yang mungkin sudah ada dan nonaktif/tidak berurutan.
UPDATE kuesioner_opsi AS o
JOIN kuesioner_pertanyaan AS q ON q.id = o.pertanyaan_id
SET o.label = o.nilai,
    o.skor = 0,
    o.urutan = CASE o.nilai
        WHEN 'Sangat memahami' THEN 1
        WHEN 'Cukup memahami' THEN 2
        WHEN 'Kurang memahami' THEN 3
        WHEN 'Tidak tahu' THEN 4
        WHEN 'Tenaga kesehatan' THEN 1
        WHEN 'Internet/media sosial' THEN 2
        WHEN 'Keluarga/teman' THEN 3
        WHEN 'TV/radio/media cetak' THEN 4
        ELSE o.urutan
    END,
    o.status = 1,
    o.updated_at = NOW()
WHERE q.kategori_id = @odhiv_category_id
  AND q.kode IN ('ODHIV20', 'ODHIV21');

COMMIT;
