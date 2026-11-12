-- Sinkronisasi modal onboarding dengan dua dokumen sumber tanggal 15 September 2026:
-- 1. KUESIONER - LEMBAR DATA DEMOGRAFI WEBSTIINFO.docx
-- 2. KUESIONER - PROFIL PENGGUNAAN OBAT TELEFARMASI WEBSTIINFO.docx
--
-- Skrip ini idempoten. Jawaban lama tidak dihapus; butir yang tidak lagi ada pada
-- dokumen hanya dinonaktifkan. Profil obat ditampilkan untuk Populasi Kunci dan
-- ODHIV karena dokumen mencakup status HIV positif/negatif serta terapi ARV/PrEP.

SET NAMES utf8mb4;
START TRANSACTION;

DROP TEMPORARY TABLE IF EXISTS desired_categories;
CREATE TEMPORARY TABLE desired_categories (
  status_name varchar(100) NOT NULL,
  category_name varchar(150) NOT NULL,
  description_text text NOT NULL,
  question_label varchar(150) NOT NULL,
  sort_order int NOT NULL,
  PRIMARY KEY (category_name)
);

INSERT INTO desired_categories VALUES
('Masyarakat','Responden Masyarakat (Umum)','Petunjuk: Berikan tanda (√) pada pilihan jawaban yang sesuai. Data ini digunakan sebagai karakteristik responden dan variabel pendukung analisis penerimaan website telefarmasi HIV/AIDS.','Lembar Data Demografi Responden',1),
('Populasi Kunci','Responden Populasi Kunci','Petunjuk: Berikan tanda (√) pada pilihan jawaban yang sesuai. Data ini digunakan sebagai karakteristik responden dan variabel pendukung analisis penerimaan website telefarmasi HIV/AIDS.','Lembar Data Demografi Responden',1),
('ODHIV','Responden ODHIV (Orang Dengan HIV)','Petunjuk: Berikan tanda (√) pada pilihan jawaban yang sesuai. Data ini digunakan sebagai karakteristik responden dan variabel pendukung analisis penerimaan website telefarmasi HIV/AIDS.','Lembar Data Demografi Responden',1),
('Tenaga Kesehatan','Responden Tenaga Kesehatan','Petunjuk: Berikan tanda (√) pada pilihan jawaban yang sesuai. Data ini digunakan sebagai karakteristik responden dan variabel pendukung analisis penerimaan website telefarmasi HIV/AIDS.','Lembar Data Demografi Responden',1),
('Populasi Kunci','Profil Penggunaan Obat - Data Klinis (Populasi Kunci)','Petunjuk: Berikan tanda (√) pada pilihan jawaban yang sesuai.','A. Data Klinis & Diagnosis (Ringkas)',20),
('Populasi Kunci','Profil Penggunaan Obat - Terapi ARV (Populasi Kunci)','Isi bagian yang sesuai dengan riwayat terapi Anda.','B. Data Terapi Antiretroviral (ARV)',21),
('Populasi Kunci','Profil Penggunaan Obat - Terapi PrEP (Populasi Kunci)','Isi bagian yang sesuai dengan riwayat terapi Anda.','C. Data Terapi PrEP',22),
('ODHIV','Profil Penggunaan Obat - Data Klinis (ODHIV)','Petunjuk: Berikan tanda (√) pada pilihan jawaban yang sesuai.','A. Data Klinis & Diagnosis (Ringkas)',20),
('ODHIV','Profil Penggunaan Obat - Terapi ARV (ODHIV)','Isi bagian yang sesuai dengan riwayat terapi Anda.','B. Data Terapi Antiretroviral (ARV)',21),
('ODHIV','Profil Penggunaan Obat - Terapi PrEP (ODHIV)','Isi bagian yang sesuai dengan riwayat terapi Anda.','C. Data Terapi PrEP',22);

INSERT INTO kuesioner_kategori
  (id_status,nama,deskripsi,label_pertanyaan,urutan,status,created_at,updated_at)
SELECT s.id,d.category_name,d.description_text,d.question_label,d.sort_order,1,NOW(),NOW()
FROM desired_categories d
JOIN status_user s ON s.nama=d.status_name
LEFT JOIN kuesioner_kategori k ON k.nama=d.category_name
WHERE k.id IS NULL;

UPDATE kuesioner_kategori k
JOIN desired_categories d ON d.category_name=k.nama
JOIN status_user s ON s.nama=d.status_name
SET k.id_status=s.id,
    k.deskripsi=d.description_text,
    k.label_pertanyaan=d.question_label,
    k.urutan=d.sort_order,
    k.status=1,
    k.updated_at=NOW();

-- MARS-5 dan butir demografi lama tidak terdapat pada dokumen pengganti.
UPDATE kuesioner_kategori
SET status=0, updated_at=NOW()
WHERE nama='Medication Adherence Rating Scale (MARS-5)';

DROP TEMPORARY TABLE IF EXISTS desired_questions;
CREATE TEMPORARY TABLE desired_questions (
  category_name varchar(150) NOT NULL,
  code varchar(50) NOT NULL,
  question_text text NOT NULL,
  input_type varchar(20) NOT NULL,
  is_required tinyint(1) NOT NULL,
  sort_order int NOT NULL,
  PRIMARY KEY (code)
);

-- Lembar demografi: hanya butir yang tercantum dalam DOCX yang diaktifkan.
INSERT INTO desired_questions VALUES
('Responden Masyarakat (Umum)','MSY1','Kode/Inisial responden','text',1,1),
('Responden Masyarakat (Umum)','MSY2','Usia','radio',1,2),
('Responden Masyarakat (Umum)','MSY3','Jenis kelamin','radio',1,3),
('Responden Masyarakat (Umum)','MSY4','Pendidikan terakhir','radio',1,4),
('Responden Masyarakat (Umum)','MSY5','Pekerjaan','radio',1,5),
('Responden Masyarakat (Umum)','MSY6','Status pernikahan','radio',1,6),
('Responden Masyarakat (Umum)','MSY7','Domisili','radio',1,7),
('Responden Masyarakat (Umum)','MSY7K','Kabupaten/Kota domisili','text',1,8),
('Responden Masyarakat (Umum)','MSY12','Pengalaman menggunakan layanan kesehatan digital (telemedicine/aplikasi kesehatan)','radio',1,9),
('Responden Masyarakat (Umum)','MSY13','Tingkat pengetahuan tentang HIV/AIDS','radio',1,10),
('Responden Masyarakat (Umum)','MSY14','Sumber informasi kesehatan utama','radio',1,11),

('Responden Populasi Kunci','PK1','Kode/Inisial responden','text',1,1),
('Responden Populasi Kunci','PK2','Usia','radio',1,2),
('Responden Populasi Kunci','PK3','Jenis kelamin/identitas gender','radio',1,3),
('Responden Populasi Kunci','PK4','Kategori populasi kunci','radio',1,4),
('Responden Populasi Kunci','PK5','Pendidikan terakhir','radio',1,5),
('Responden Populasi Kunci','PK6','Pekerjaan','radio',1,6),
('Responden Populasi Kunci','PK7','Status pernikahan','radio',1,7),
('Responden Populasi Kunci','PK8','Domisili','radio',1,8),
('Responden Populasi Kunci','PK8K','Kabupaten/Kota domisili','text',1,9),
('Responden Populasi Kunci','PK10','Status tes HIV','radio',1,10),
('Responden Populasi Kunci','PK11','Keterlibatan dengan komunitas/LSM pendamping','radio',1,11),
('Responden Populasi Kunci','PK12','Akses ke layanan HIV','radio',1,12),
('Responden Populasi Kunci','PK16','Pengalaman menggunakan layanan kesehatan digital','radio',1,13),
('Responden Populasi Kunci','PK17','Tingkat pengetahuan tentang HIV/AIDS','radio',1,14),
('Responden Populasi Kunci','PK18','Sumber informasi kesehatan utama','radio',1,15),

('Responden ODHIV (Orang Dengan HIV)','ODHIV1','Kode/Inisial responden','text',1,1),
('Responden ODHIV (Orang Dengan HIV)','ODHIV2','Usia','radio',1,2),
('Responden ODHIV (Orang Dengan HIV)','ODHIV3','Jenis kelamin','radio',1,3),
('Responden ODHIV (Orang Dengan HIV)','ODHIV4','Pendidikan terakhir','radio',1,4),
('Responden ODHIV (Orang Dengan HIV)','ODHIV5','Pekerjaan','radio',1,5),
('Responden ODHIV (Orang Dengan HIV)','ODHIV6','Status pernikahan','radio',1,6),
('Responden ODHIV (Orang Dengan HIV)','ODHIV7','Domisili','radio',1,7),
('Responden ODHIV (Orang Dengan HIV)','ODHIV7K','Kabupaten/Kota domisili','text',1,8),
('Responden ODHIV (Orang Dengan HIV)','ODHIV9','Lama terdiagnosis HIV','radio',1,9),
('Responden ODHIV (Orang Dengan HIV)','ODHIV10','Status pengobatan ARV','radio',1,10),
('Responden ODHIV (Orang Dengan HIV)','ODHIV11','Lama menjalani terapi ARV','radio',1,11),
('Responden ODHIV (Orang Dengan HIV)','ODHIV12','Tempat pengambilan obat ARV','radio',1,12),
('Responden ODHIV (Orang Dengan HIV)','ODHIV13','Frekuensi pengambilan obat ARV','radio',1,13),
('Responden ODHIV (Orang Dengan HIV)','ODHIV14','Kepatuhan pengobatan','radio',1,14),
('Responden ODHIV (Orang Dengan HIV)','ODHIV15','Status keterbukaan status (disclosure)','radio',1,15),
('Responden ODHIV (Orang Dengan HIV)','ODHIV19','Pengalaman menggunakan layanan kesehatan digital','radio',1,16),
('Responden ODHIV (Orang Dengan HIV)','ODHIV20','Tingkat pengetahuan tentang HIV/AIDS','radio',1,17),
('Responden ODHIV (Orang Dengan HIV)','ODHIV21','Sumber informasi kesehatan utama','radio',1,18),

('Responden Tenaga Kesehatan','NAKES1','Kode/Inisial responden','text',1,1),
('Responden Tenaga Kesehatan','NAKES2','Usia','radio',1,2),
('Responden Tenaga Kesehatan','NAKES3','Jenis kelamin','radio',1,3),
('Responden Tenaga Kesehatan','NAKES4','Profesi','radio',1,4),
('Responden Tenaga Kesehatan','NAKES5','Pendidikan terakhir','radio',1,5),
('Responden Tenaga Kesehatan','NAKES6','Tempat kerja (fasyankes)','radio',1,6),
('Responden Tenaga Kesehatan','NAKES7','Masa kerja','radio',1,7),
('Responden Tenaga Kesehatan','NAKES8','Pengalaman menangani pasien HIV/AIDS','radio',1,8),
('Responden Tenaga Kesehatan','NAKES9','Pelatihan terkait HIV/AIDS atau PDP','radio',1,9),
('Responden Tenaga Kesehatan','NAKES10','Keterlibatan dalam layanan telefarmasi/telemedicine','radio',1,10),
('Responden Tenaga Kesehatan','NAKES11','Familiaritas dengan teknologi/sistem informasi kesehatan','radio',1,11),
('Responden Tenaga Kesehatan','NAKES15','Pengalaman menggunakan layanan kesehatan digital','radio',1,12),
('Responden Tenaga Kesehatan','NAKES16','Tingkat pengetahuan tentang HIV/AIDS','radio',1,13),
('Responden Tenaga Kesehatan','NAKES17','Sumber informasi kesehatan utama','radio',1,14);

-- Profil penggunaan obat untuk Populasi Kunci.
INSERT INTO desired_questions VALUES
('Profil Penggunaan Obat - Data Klinis (Populasi Kunci)','PK_KL1','Kode/inisial pasien','text',1,1),
('Profil Penggunaan Obat - Data Klinis (Populasi Kunci)','PK_KL2','Status HIV','radio',1,2),
('Profil Penggunaan Obat - Data Klinis (Populasi Kunci)','PK_KL3','Stadium klinis WHO','radio',0,3),
('Profil Penggunaan Obat - Data Klinis (Populasi Kunci)','PK_KL4','Penyakit penyerta/komorbid','checkbox',1,4),
('Profil Penggunaan Obat - Data Klinis (Populasi Kunci)','PK_KL5','Riwayat alergi obat','radio',1,5),
('Profil Penggunaan Obat - Data Klinis (Populasi Kunci)','PK_KL6','Jika ada riwayat alergi obat, sebutkan','text',0,6),
('Profil Penggunaan Obat - Terapi ARV (Populasi Kunci)','PK_ARV1','Status terapi ARV','radio',1,1),
('Profil Penggunaan Obat - Terapi ARV (Populasi Kunci)','PK_ARV2','Nama regimen/paduan ARV','radio',0,2),
('Profil Penggunaan Obat - Terapi ARV (Populasi Kunci)','PK_ARV3','Aturan pakai/frekuensi','radio',0,3),
('Profil Penggunaan Obat - Terapi ARV (Populasi Kunci)','PK_ARV4','Tanggal mulai ARV (bulan/tahun)','month',0,4),
('Profil Penggunaan Obat - Terapi ARV (Populasi Kunci)','PK_ARV5','Lama penggunaan ARV','radio',0,5),
('Profil Penggunaan Obat - Terapi ARV (Populasi Kunci)','PK_ARV6','Riwayat penggantian regimen','radio',0,6),
('Profil Penggunaan Obat - Terapi ARV (Populasi Kunci)','PK_ARV7','Sumber & tempat pengambilan obat','radio',0,7),
('Profil Penggunaan Obat - Terapi ARV (Populasi Kunci)','PK_ARV8','Frekuensi pengambilan obat','radio',0,8),
('Profil Penggunaan Obat - Terapi ARV (Populasi Kunci)','PK_ARV9','Penggunaan obat lainnya (sebutkan)','textarea',0,9),
('Profil Penggunaan Obat - Terapi ARV (Populasi Kunci)','PK_ARV10','Penggunaan hormon (sebutkan)','textarea',0,10),
('Profil Penggunaan Obat - Terapi PrEP (Populasi Kunci)','PK_PREP1','Status terapi PrEP','radio',1,1),
('Profil Penggunaan Obat - Terapi PrEP (Populasi Kunci)','PK_PREP2','Aturan pakai/frekuensi','radio',0,2),
('Profil Penggunaan Obat - Terapi PrEP (Populasi Kunci)','PK_PREP3','Tanggal mulai PrEP (bulan/tahun)','month',0,3),
('Profil Penggunaan Obat - Terapi PrEP (Populasi Kunci)','PK_PREP4','Lama penggunaan PrEP','radio',0,4),
('Profil Penggunaan Obat - Terapi PrEP (Populasi Kunci)','PK_PREP5','Sumber & tempat pengambilan obat','radio',0,5),
('Profil Penggunaan Obat - Terapi PrEP (Populasi Kunci)','PK_PREP6','Frekuensi pengambilan obat','radio',0,6),
('Profil Penggunaan Obat - Terapi PrEP (Populasi Kunci)','PK_PREP7','Penggunaan obat lainnya (sebutkan)','textarea',0,7),
('Profil Penggunaan Obat - Terapi PrEP (Populasi Kunci)','PK_PREP8','Penggunaan hormon (sebutkan)','textarea',0,8);

-- Profil yang sama untuk ODHIV menggunakan kode terpisah agar riwayat tiap status stabil.
INSERT INTO desired_questions
SELECT REPLACE(category_name,'(Populasi Kunci)','(ODHIV)'),
       CONCAT('OD_',SUBSTRING(code,4)),question_text,input_type,is_required,sort_order
FROM desired_questions
WHERE code LIKE 'PK\_%';

-- Nonaktifkan seluruh butir lama pada kategori yang dikelola migrasi ini, lalu aktifkan daftar DOCX.
UPDATE kuesioner_pertanyaan q
JOIN kuesioner_kategori k ON k.id=q.kategori_id
JOIN desired_categories d ON d.category_name=k.nama
SET q.status=0, q.updated_at=NOW();

INSERT INTO kuesioner_pertanyaan
  (kategori_id,kode,pertanyaan,tipe,kunci_jawaban,wajib,urutan,status,created_at,updated_at)
SELECT k.id,d.code,d.question_text,d.input_type,NULL,d.is_required,d.sort_order,1,NOW(),NOW()
FROM desired_questions d
JOIN kuesioner_kategori k ON k.nama=d.category_name
LEFT JOIN kuesioner_pertanyaan q ON q.kode=d.code
WHERE q.id IS NULL;

UPDATE kuesioner_pertanyaan q
JOIN desired_questions d ON d.code=q.kode
JOIN kuesioner_kategori k ON k.nama=d.category_name
SET q.kategori_id=k.id,
    q.pertanyaan=d.question_text,
    q.tipe=d.input_type,
    q.kunci_jawaban=NULL,
    q.wajib=d.is_required,
    q.urutan=d.sort_order,
    q.status=1,
    q.updated_at=NOW();

DROP TEMPORARY TABLE IF EXISTS desired_options;
CREATE TEMPORARY TABLE desired_options (
  code varchar(50) NOT NULL,
  option_label varchar(255) NOT NULL,
  option_value varchar(255) NOT NULL,
  sort_order int NOT NULL,
  PRIMARY KEY (code,option_value)
);

-- Opsi demografi sesuai ejaan pada dokumen.
INSERT INTO desired_options VALUES
('MSY2','17-25 tahun','17-25 tahun',1),('MSY2','26-35 tahun','26-35 tahun',2),('MSY2','36-45 tahun','36-45 tahun',3),('MSY2','46-55 tahun','46-55 tahun',4),('MSY2','>55 tahun','>55 tahun',5),
('MSY3','Laki-laki','Laki-laki',1),('MSY3','Perempuan','Perempuan',2),
('MSY4','Tidak sekolah/SD','Tidak sekolah/SD',1),('MSY4','SMP','SMP',2),('MSY4','SMA/SMK','SMA/SMK',3),('MSY4','Diploma','Diploma',4),('MSY4','Sarjana (S1)','Sarjana (S1)',5),('MSY4','Pascasarjana (S2/S3)','Pascasarjana (S2/S3)',6),
('MSY5','Pelajar/Mahasiswa','Pelajar/Mahasiswa',1),('MSY5','PNS/TNI/Polri','PNS/TNI/Polri',2),('MSY5','Karyawan swasta','Karyawan swasta',3),('MSY5','Wiraswasta','Wiraswasta',4),('MSY5','Ibu rumah tangga','Ibu rumah tangga',5),('MSY5','Tidak bekerja','Tidak bekerja',6),('MSY5','Lainnya (sebutkan)','Lainnya',7),
('MSY6','Belum menikah','Belum menikah',1),('MSY6','Menikah','Menikah',2),('MSY6','Cerai hidup','Cerai hidup',3),('MSY6','Cerai mati','Cerai mati',4),
('MSY7','Perkotaan','Perkotaan',1),('MSY7','Pinggiran kota','Pinggiran kota',2),('MSY7','Pedesaan','Pedesaan',3),
('MSY12','Sering','Sering',1),('MSY12','Pernah beberapa kali','Pernah beberapa kali',2),('MSY12','Baru sekali','Baru sekali',3),('MSY12','Belum pernah','Belum pernah',4),
('MSY13','Sangat memahami','Sangat memahami',1),('MSY13','Cukup memahami','Cukup memahami',2),('MSY13','Kurang memahami','Kurang memahami',3),('MSY13','Tidak tahu','Tidak tahu',4),
('MSY14','Tenaga kesehatan','Tenaga kesehatan',1),('MSY14','Internet/media sosial','Internet/media sosial',2),('MSY14','Keluarga/teman','Keluarga/teman',3),('MSY14','TV/radio/media cetak','TV/radio/media cetak',4),

('PK2','15-19 tahun','15-19 tahun',1),('PK2','20-29 tahun','20-29 tahun',2),('PK2','30-39 tahun','30-39 tahun',3),('PK2','40-49 tahun','40-49 tahun',4),('PK2','≥50 tahun','≥50 tahun',5),
('PK3','Laki-laki','Laki-laki',1),('PK3','Perempuan','Perempuan',2),('PK3','Transgender/waria','Transgender/waria',3),('PK3','Lainnya (sebutkan)','Lainnya',4),
('PK4','LSL','LSL',1),('PK4','WPS','WPS',2),('PK4','Waria/transgender','Waria/transgender',3),('PK4','Penasun','Penasun',4),('PK4','WBP','WBP',5),('PK4','Pasangan risiko tinggi','Pasangan risiko tinggi',6),
('PK5','Tidak sekolah/SD','Tidak sekolah/SD',1),('PK5','SMP','SMP',2),('PK5','SMA/SMK','SMA/SMK',3),('PK5','Diploma','Diploma',4),('PK5','Sarjana ke atas','Sarjana ke atas',5),
('PK6','Tidak bekerja','Tidak bekerja',1),('PK6','Karyawan','Karyawan',2),('PK6','Wiraswasta','Wiraswasta',3),('PK6','Pekerja informal','Pekerja informal',4),('PK6','Lainnya (sebutkan)','Lainnya',5),
('PK7','Belum menikah','Belum menikah',1),('PK7','Menikah','Menikah',2),('PK7','Cerai','Cerai',3),
('PK8','Perkotaan','Perkotaan',1),('PK8','Pinggiran kota','Pinggiran kota',2),('PK8','Pedesaan','Pedesaan',3),
('PK10','Rutin/berkala','Rutin/berkala',1),('PK10','Pernah, tidak rutin','Pernah, tidak rutin',2),('PK10','Belum pernah tes','Belum pernah tes',3),
('PK11','Aktif','Aktif',1),('PK11','Kadang-kadang','Kadang-kadang',2),('PK11','Tidak terlibat','Tidak terlibat',3),
('PK12','Mudah dijangkau','Mudah dijangkau',1),('PK12','Cukup sulit','Cukup sulit',2),('PK12','Sulit dijangkau','Sulit dijangkau',3),
('PK16','Sering','Sering',1),('PK16','Pernah beberapa kali','Pernah beberapa kali',2),('PK16','Baru sekali','Baru sekali',3),('PK16','Belum pernah','Belum pernah',4),
('PK17','Sangat memahami','Sangat memahami',1),('PK17','Cukup memahami','Cukup memahami',2),('PK17','Kurang memahami','Kurang memahami',3),('PK17','Tidak tahu','Tidak tahu',4),
('PK18','Tenaga kesehatan','Tenaga kesehatan',1),('PK18','Internet/media sosial','Internet/media sosial',2),('PK18','Keluarga/teman','Keluarga/teman',3),('PK18','TV/radio/media cetak','TV/radio/media cetak',4),

('ODHIV2','15-24 tahun','15-24 tahun',1),('ODHIV2','25-34 tahun','25-34 tahun',2),('ODHIV2','35-44 tahun','35-44 tahun',3),('ODHIV2','45-54 tahun','45-54 tahun',4),('ODHIV2','≥55 tahun','≥55 tahun',5),
('ODHIV3','Laki-laki','Laki-laki',1),('ODHIV3','Perempuan','Perempuan',2),('ODHIV3','Transgender/waria','Transgender/waria',3),
('ODHIV4','Tidak sekolah/SD','Tidak sekolah/SD',1),('ODHIV4','SMP','SMP',2),('ODHIV4','SMA/SMK','SMA/SMK',3),('ODHIV4','Diploma','Diploma',4),('ODHIV4','Sarjana ke atas','Sarjana ke atas',5),
('ODHIV5','Tidak bekerja','Tidak bekerja',1),('ODHIV5','Karyawan swasta','Karyawan swasta',2),('ODHIV5','Wiraswasta','Wiraswasta',3),('ODHIV5','PNS/TNI/Polri','PNS/TNI/Polri',4),('ODHIV5','Lainnya (sebutkan)','Lainnya',5),
('ODHIV6','Belum menikah','Belum menikah',1),('ODHIV6','Menikah','Menikah',2),('ODHIV6','Cerai','Cerai',3),
('ODHIV7','Perkotaan','Perkotaan',1),('ODHIV7','Pinggiran kota','Pinggiran kota',2),('ODHIV7','Pedesaan','Pedesaan',3),
('ODHIV9','<1 tahun','<1 tahun',1),('ODHIV9','1-3 tahun','1-3 tahun',2),('ODHIV9','4-6 tahun','4-6 tahun',3),('ODHIV9','>6 tahun','>6 tahun',4),
('ODHIV10','Sedang menjalani ARV','Sedang menjalani ARV',1),('ODHIV10','Pernah, kini berhenti','Pernah, kini berhenti',2),('ODHIV10','Belum memulai','Belum memulai',3),
('ODHIV11','<6 bulan','<6 bulan',1),('ODHIV11','6-12 bulan','6-12 bulan',2),('ODHIV11','1-3 tahun','1-3 tahun',3),('ODHIV11','>3 tahun','>3 tahun',4),
('ODHIV12','RS rujukan','RS rujukan',1),('ODHIV12','Puskesmas PDP','Puskesmas PDP',2),('ODHIV12','Klinik/layanan swasta','Klinik/layanan swasta',3),('ODHIV12','Lainnya (sebutkan)','Lainnya',4),
('ODHIV13','Setiap bulan','Setiap bulan',1),('ODHIV13','Setiap 2-3 bulan','Setiap 2-3 bulan',2),('ODHIV13','Lainnya (sebutkan)','Lainnya',3),
('ODHIV14','Selalu patuh','Selalu patuh',1),('ODHIV14','Kadang terlewat','Kadang terlewat',2),('ODHIV14','Sering terlewat','Sering terlewat',3),
('ODHIV15','Terbuka ke keluarga/pasangan','Terbuka ke keluarga/pasangan',1),('ODHIV15','Terbatas','Terbatas',2),('ODHIV15','Tertutup','Tertutup',3),
('ODHIV19','Sering','Sering',1),('ODHIV19','Pernah beberapa kali','Pernah beberapa kali',2),('ODHIV19','Baru sekali','Baru sekali',3),('ODHIV19','Belum pernah','Belum pernah',4),
('ODHIV20','Sangat memahami','Sangat memahami',1),('ODHIV20','Cukup memahami','Cukup memahami',2),('ODHIV20','Kurang memahami','Kurang memahami',3),('ODHIV20','Tidak tahu','Tidak tahu',4),
('ODHIV21','Tenaga kesehatan','Tenaga kesehatan',1),('ODHIV21','Internet/media sosial','Internet/media sosial',2),('ODHIV21','Keluarga/teman','Keluarga/teman',3),('ODHIV21','TV/radio/media cetak','TV/radio/media cetak',4);

-- Opsi tenaga kesehatan yang spesifik; opsi pengalaman/pengetahuan/sumber disalin dari pola umum.
INSERT INTO desired_options VALUES
('NAKES2','21-30 tahun','21-30 tahun',1),('NAKES2','31-40 tahun','31-40 tahun',2),('NAKES2','41-50 tahun','41-50 tahun',3),('NAKES2','>50 tahun','>50 tahun',4),
('NAKES3','Laki-laki','Laki-laki',1),('NAKES3','Perempuan','Perempuan',2),
('NAKES4','Dokter umum','Dokter umum',1),('NAKES4','Dokter spesialis','Dokter spesialis',2),('NAKES4','Apoteker','Apoteker',3),('NAKES4','TTK','TTK',4),('NAKES4','Perawat','Perawat',5),('NAKES4','Bidan','Bidan',6),('NAKES4','Analis/laboratorium','Analis/laboratorium',7),('NAKES4','Lainnya (sebutkan)','Lainnya',8),
('NAKES5','Diploma (D3)','Diploma (D3)',1),('NAKES5','Sarjana/Profesi (S1/Profesi)','Sarjana/Profesi (S1/Profesi)',2),('NAKES5','Pascasarjana (S2/Sp/S3)','Pascasarjana (S2/Sp/S3)',3),
('NAKES6','Rumah sakit','Rumah sakit',1),('NAKES6','Puskesmas','Puskesmas',2),('NAKES6','Klinik','Klinik',3),('NAKES6','Apotek','Apotek',4),('NAKES6','Dinas kesehatan','Dinas kesehatan',5),('NAKES6','Lainnya (sebutkan)','Lainnya',6),
('NAKES7','<1 tahun','<1 tahun',1),('NAKES7','1-5 tahun','1-5 tahun',2),('NAKES7','6-10 tahun','6-10 tahun',3),('NAKES7','>10 tahun','>10 tahun',4),
('NAKES8','Rutin/setiap hari','Rutin/setiap hari',1),('NAKES8','Kadang-kadang','Kadang-kadang',2),('NAKES8','Jarang','Jarang',3),('NAKES8','Belum pernah','Belum pernah',4),
('NAKES9','Pernah bersertifikat','Pernah bersertifikat',1),('NAKES9','Pernah tanpa sertifikat','Pernah tanpa sertifikat',2),('NAKES9','Belum pernah','Belum pernah',3),
('NAKES10','Aktif terlibat','Aktif terlibat',1),('NAKES10','Pernah','Pernah',2),('NAKES10','Belum pernah','Belum pernah',3),
('NAKES11','Sangat familiar','Sangat familiar',1),('NAKES11','Cukup familiar','Cukup familiar',2),('NAKES11','Kurang familiar','Kurang familiar',3),
('NAKES15','Sering','Sering',1),('NAKES15','Pernah beberapa kali','Pernah beberapa kali',2),('NAKES15','Baru sekali','Baru sekali',3),('NAKES15','Belum pernah','Belum pernah',4),
('NAKES16','Sangat memahami','Sangat memahami',1),('NAKES16','Cukup memahami','Cukup memahami',2),('NAKES16','Kurang memahami','Kurang memahami',3),('NAKES16','Tidak tahu','Tidak tahu',4),
('NAKES17','Tenaga kesehatan','Tenaga kesehatan',1),('NAKES17','Internet/media sosial','Internet/media sosial',2),('NAKES17','Keluarga/teman','Keluarga/teman',3),('NAKES17','TV/radio/media cetak','TV/radio/media cetak',4);

-- Opsi profil obat untuk kode PK, lalu disalin ke kode ODHIV.
INSERT INTO desired_options VALUES
('PK_KL2','Positif','Positif',1),('PK_KL2','Negatif','Negatif',2),
('PK_KL3','Stadium I','Stadium I',1),('PK_KL3','Stadium II','Stadium II',2),('PK_KL3','Stadium III','Stadium III',3),('PK_KL3','Stadium IV','Stadium IV',4),
('PK_KL4','TB','TB',1),('PK_KL4','Hepatitis B','Hepatitis B',2),('PK_KL4','Hepatitis C','Hepatitis C',3),('PK_KL4','IMS lain','IMS lain',4),('PK_KL4','DM/HT','DM/HT',5),('PK_KL4','Tidak ada','Tidak ada',6),
('PK_KL5','Ada','Ada',1),('PK_KL5','Tidak ada','Tidak ada',2),
('PK_ARV1','Sedang menjalani (on ART)','Sedang menjalani (on ART)',1),('PK_ARV1','Pernah, kini putus obat','Pernah, kini putus obat',2),('PK_ARV1','Belum memulai','Belum memulai',3),
('PK_ARV2','TLD','TLD',1),('PK_ARV2','TLE','TLE',2),
('PK_ARV3','1 x sehari','1 x sehari',1),('PK_ARV3','2 x sehari','2 x sehari',2),('PK_ARV3','Lainnya (sebutkan)','Lainnya',3),
('PK_ARV5','<6 bulan','<6 bulan',1),('PK_ARV5','6-12 bulan','6-12 bulan',2),('PK_ARV5','1-3 tahun','1-3 tahun',3),('PK_ARV5','>3 tahun','>3 tahun',4),
('PK_ARV6','Pernah','Pernah',1),('PK_ARV6','Tidak pernah','Tidak pernah',2),
('PK_ARV7','RS rujukan','RS rujukan',1),('PK_ARV7','Puskesmas PDP','Puskesmas PDP',2),('PK_ARV7','Klinik/layanan swasta','Klinik/layanan swasta',3),('PK_ARV7','Lainnya (sebutkan)','Lainnya',4),
('PK_ARV8','Bulanan','Bulanan',1),('PK_ARV8','Multi-bulan (2-3 bulan)','Multi-bulan (2-3 bulan)',2),('PK_ARV8','Lainnya (sebutkan)','Lainnya',3),
('PK_PREP1','Sedang menjalani','Sedang menjalani',1),('PK_PREP1','Pernah, kini putus obat','Pernah, kini putus obat',2),('PK_PREP1','Belum memulai','Belum memulai',3),
('PK_PREP2','Setiap hari','Setiap hari',1),('PK_PREP2','Event driven (2-1-1)','Event driven (2-1-1)',2),
('PK_PREP4','<6 bulan','<6 bulan',1),('PK_PREP4','6-12 bulan','6-12 bulan',2),('PK_PREP4','1-3 tahun','1-3 tahun',3),('PK_PREP4','>3 tahun','>3 tahun',4),
('PK_PREP5','RS rujukan','RS rujukan',1),('PK_PREP5','Puskesmas PDP','Puskesmas PDP',2),('PK_PREP5','Klinik/layanan swasta','Klinik/layanan swasta',3),('PK_PREP5','Lainnya (sebutkan)','Lainnya',4),
('PK_PREP6','Bulanan','Bulanan',1),('PK_PREP6','Multi-bulan (2-3 bulan)','Multi-bulan (2-3 bulan)',2),('PK_PREP6','Lainnya (sebutkan)','Lainnya',3);

INSERT INTO desired_options
SELECT CONCAT('OD_',SUBSTRING(code,4)),option_label,option_value,sort_order
FROM desired_options
WHERE code LIKE 'PK\_%';

-- Pertahankan data opsi lama untuk audit, tetapi hanya opsi DOCX yang ditampilkan.
UPDATE kuesioner_opsi o
JOIN kuesioner_pertanyaan q ON q.id=o.pertanyaan_id
JOIN desired_questions d ON d.code=q.kode
SET o.status=0, o.updated_at=NOW();

INSERT INTO kuesioner_opsi
  (pertanyaan_id,label,nilai,skor,urutan,status,created_at,updated_at)
SELECT q.id,d.option_label,d.option_value,0,d.sort_order,1,NOW(),NOW()
FROM desired_options d
JOIN kuesioner_pertanyaan q ON q.kode=d.code
LEFT JOIN kuesioner_opsi o ON o.pertanyaan_id=q.id AND o.nilai=d.option_value
WHERE o.id IS NULL;

UPDATE kuesioner_opsi o
JOIN kuesioner_pertanyaan q ON q.id=o.pertanyaan_id
JOIN desired_options d ON d.code=q.kode AND d.option_value=o.nilai
SET o.label=d.option_label,
    o.skor=0,
    o.urutan=d.sort_order,
    o.status=1,
    o.updated_at=NOW();

DROP TEMPORARY TABLE desired_options;
DROP TEMPORARY TABLE desired_questions;
DROP TEMPORARY TABLE desired_categories;

COMMIT;
