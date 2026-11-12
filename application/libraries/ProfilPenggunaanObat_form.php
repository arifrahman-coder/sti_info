<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ProfilPenggunaanObat_form
{
    private $errors = array();

    public function validate(array $post)
    {
        $this->errors = array();

        $kodePasien = $this->requiredText($post, 'kode_pasien', 'Kode/inisial pasien', 50);
        $statusHiv = $this->choice($post, 'status_hiv', array('Positif', 'Negatif'), 'Status HIV');
        $stadium = $this->choice($post, 'stadium_who', array('Stadium I', 'Stadium II', 'Stadium III', 'Stadium IV'), 'Stadium klinis WHO');
        $kategoriCd4 = $this->choice(
            $post,
            'kategori_cd4',
            array('< 200 sel/mm³', '200–349 sel/mm³', '350–499 sel/mm³', '≥ 500 sel/mm³'),
            'Jumlah CD4 terakhir'
        );
        $viralLoad = $this->choice(
            $post,
            'viral_load',
            array('Tidak terdeteksi (<50)', 'Terdeteksi rendah', 'Terdeteksi tinggi', 'Belum diperiksa'),
            'Viral load terakhir'
        );
        $komorbid = $this->multiple(
            $post,
            'komorbid',
            array('TB', 'Hepatitis B', 'Hepatitis C', 'IMS lain', 'DM/HT', 'Tidak ada'),
            'Penyakit penyerta/komorbid'
        );
        $alergi = $this->choice($post, 'alergi_obat', array('Ada', 'Tidak ada'), 'Riwayat alergi obat');
        $detailAlergi = $this->text($post, 'detail_alergi', 500);
        $this->requireDetail($alergi === 'Ada', $detailAlergi, 'Obat dan reaksi alergi wajib ditulis.');

        $arv = $this->validateArv($post);
        $prep = $this->validatePrep($post);

        if ($alergi !== 'Ada') {
            $detailAlergi = '';
        }

        if (!empty($this->errors)) {
            return array(
                'success' => FALSE,
                'message' => implode(' ', array_unique($this->errors)),
                'data' => array()
            );
        }

        return array(
            'success' => TRUE,
            'message' => '',
            'data' => array_merge(
                array(
                    'kode_pasien' => $kodePasien,
                    'status_hiv' => $statusHiv,
                    'diagnosis_utama' => 'HIV '.$this->lowercase($statusHiv),
                    'stadium_who' => $stadium,
                    'jumlah_cd4' => NULL,
                    'kategori_cd4' => $kategoriCd4,
                    'viral_load' => $viralLoad,
                    'komorbid' => $this->joinValues($komorbid),
                    'alergi_obat' => $alergi,
                    'detail_alergi' => $detailAlergi
                ),
                $arv,
                $prep
            )
        );
    }

    private function validateArv(array $post)
    {
        $status = $this->choice(
            $post,
            'status_terapi_arv',
            array('Sedang menjalani (on ART)', 'Pernah, kini putus obat', 'Belum memulai'),
            'Status terapi ARV'
        );
        $active = $status !== '' && $status !== 'Belum memulai';

        $lini = $this->optionalChoice($post, 'lini_pengobatan', array('Lini pertama', 'Lini kedua', 'Lini ketiga'), 'Lini pengobatan ARV', $active);
        $regimen = $this->optionalChoice($post, 'regimen_arv', array('TLD', 'TLE'), 'Nama regimen/paduan ARV', $active);
        $komponen = $this->text($post, 'komponen_arv', 500);
        $bentuk = $this->optionalChoice(
            $post,
            'bentuk_sediaan_arv',
            array('Kombinasi dosis tetap (KDT/FDC)', 'Obat lepasan (loose)', 'Kombinasi keduanya'),
            'Bentuk sediaan ARV',
            $active
        );
        $dosis = $this->text($post, 'kekuatan_dosis_arv', 255);
        $aturan = $this->optionalChoice($post, 'aturan_pakai_arv', array('1 x sehari', '2 x sehari', 'Lainnya'), 'Aturan pakai ARV', $active);
        $aturanLainnya = $this->text($post, 'aturan_pakai_arv_lainnya', 150);
        $tanggal = $this->monthValue($post, 'tanggal_mulai_arv', 'Tanggal mulai ARV', $active);
        $lama = $this->optionalChoice($post, 'lama_penggunaan_arv', array('< 6 bln', '6–12 bln', '1–3 thn', '> 3 thn'), 'Lama penggunaan ARV', $active);
        $ganti = $this->optionalChoice($post, 'riwayat_ganti_regimen', array('Pernah', 'Tidak pernah'), 'Riwayat penggantian regimen ARV', $active);
        $alasan = $this->optionalMultiple(
            $post,
            'alasan_ganti_regimen',
            array('Efek samping', 'Gagal terapi', 'Interaksi', 'Stok'),
            'Alasan penggantian regimen ARV',
            $active && $ganti === 'Pernah'
        );
        $sumber = $this->optionalChoice($post, 'sumber_obat', array('RS rujukan', 'Puskesmas PDP', 'Klinik/layanan swasta', 'Lainnya'), 'Sumber pengambilan obat ARV', $active);
        $sumberLainnya = $this->text($post, 'sumber_obat_lainnya', 150);
        $frekuensi = $this->optionalChoice($post, 'frekuensi_pengambilan', array('Bulanan', 'Multi-bulan (2–3 bln)', 'Lainnya'), 'Frekuensi pengambilan obat ARV', $active);
        $frekuensiLainnya = $this->text($post, 'frekuensi_pengambilan_lainnya', 150);
        $obatLainnya = $this->text($post, 'penggunaan_obat_lainnya_arv', 1000);
        $hormon = $this->text($post, 'penggunaan_hormon_arv', 1000);

        if ($active) {
            $this->requireDetail(TRUE, $komponen, 'Komponen/zat aktif ARV wajib ditulis.');
            $this->requireDetail(TRUE, $dosis, 'Kekuatan/dosis ARV wajib ditulis.');
            $this->requireDetail($aturan === 'Lainnya', $aturanLainnya, 'Aturan pakai ARV lainnya wajib ditulis.');
            $this->requireDetail($sumber === 'Lainnya', $sumberLainnya, 'Sumber obat ARV lainnya wajib ditulis.');
            $this->requireDetail($frekuensi === 'Lainnya', $frekuensiLainnya, 'Frekuensi pengambilan ARV lainnya wajib ditulis.');
        } else {
            $lini = $regimen = $komponen = $bentuk = $dosis = $aturan = $aturanLainnya = '';
            $tanggal = NULL;
            $lama = $ganti = $sumber = $sumberLainnya = $frekuensi = $frekuensiLainnya = '';
            $alasan = array();
        }

        if ($aturan !== 'Lainnya') $aturanLainnya = '';
        if ($ganti !== 'Pernah') $alasan = array();
        if ($sumber !== 'Lainnya') $sumberLainnya = '';
        if ($frekuensi !== 'Lainnya') $frekuensiLainnya = '';

        return array(
            'status_terapi_arv' => $status,
            'lini_pengobatan' => $lini,
            'regimen_arv' => $regimen,
            'komponen_arv' => $komponen,
            'bentuk_sediaan_arv' => $bentuk,
            'kekuatan_dosis_arv' => $dosis,
            'aturan_pakai_arv' => $aturan,
            'aturan_pakai_arv_lainnya' => $aturanLainnya,
            'tanggal_mulai_arv' => $tanggal,
            'lama_penggunaan_arv' => $lama,
            'riwayat_ganti_regimen' => $ganti,
            'alasan_ganti_regimen' => $this->joinValues($alasan),
            'sumber_obat' => $sumber,
            'sumber_obat_lainnya' => $sumberLainnya,
            'frekuensi_pengambilan' => $frekuensi,
            'frekuensi_pengambilan_lainnya' => $frekuensiLainnya,
            'penggunaan_obat_lainnya_arv' => $obatLainnya,
            'penggunaan_hormon_arv' => $hormon
        );
    }

    private function validatePrep(array $post)
    {
        $status = $this->choice(
            $post,
            'status_terapi_prep',
            array('Sedang menjalani (on ART)', 'Pernah, kini putus obat', 'Belum memulai'),
            'Status terapi PrEP'
        );
        $active = $status !== '' && $status !== 'Belum memulai';

        $lini = $this->optionalChoice($post, 'lini_pengobatan_prep', array('Lini pertama', 'Lini kedua', 'Lini ketiga'), 'Lini pengobatan PrEP', $active);
        $regimen = $this->optionalChoice($post, 'regimen_prep', array('TLD', 'TLE'), 'Nama regimen/paduan PrEP', $active);
        $komponen = $this->text($post, 'komponen_prep', 500);
        $bentuk = $this->optionalChoice(
            $post,
            'bentuk_sediaan_prep',
            array('Kombinasi dosis tetap (KDT/FDC)', 'Obat lepasan (loose)', 'Kombinasi keduanya'),
            'Bentuk sediaan PrEP',
            $active
        );
        $dosis = $this->text($post, 'kekuatan_dosis_prep', 255);
        $aturan = $this->optionalChoice($post, 'aturan_pakai_prep', array('Setiap hari', 'Event driven (2-1-1)'), 'Aturan pakai PrEP', $active);
        $tanggal = $this->monthValue($post, 'tanggal_mulai_prep', 'Tanggal mulai PrEP', $active);
        $lama = $this->optionalChoice($post, 'lama_penggunaan_prep', array('< 6 bln', '6–12 bln', '1–3 thn', '> 3 thn'), 'Lama penggunaan PrEP', $active);
        $ganti = $this->optionalChoice($post, 'riwayat_ganti_regimen_prep', array('Pernah', 'Tidak pernah'), 'Riwayat penggantian regimen PrEP', $active);
        $alasan = $this->optionalMultiple(
            $post,
            'alasan_ganti_regimen_prep',
            array('Efek samping', 'Gagal terapi', 'Interaksi', 'Stok'),
            'Alasan penggantian regimen PrEP',
            $active && $ganti === 'Pernah'
        );
        $sumber = $this->optionalChoice($post, 'sumber_obat_prep', array('RS rujukan', 'Puskesmas PDP', 'Klinik/layanan swasta', 'Lainnya'), 'Sumber pengambilan obat PrEP', $active);
        $sumberLainnya = $this->text($post, 'sumber_obat_prep_lainnya', 150);
        $frekuensi = $this->optionalChoice($post, 'frekuensi_pengambilan_prep', array('Bulanan', 'Multi-bulan (2–3 bln)', 'Lainnya'), 'Frekuensi pengambilan obat PrEP', $active);
        $frekuensiLainnya = $this->text($post, 'frekuensi_pengambilan_prep_lainnya', 150);
        $obatLainnya = $this->text($post, 'penggunaan_obat_lainnya_prep', 1000);
        $hormon = $this->text($post, 'penggunaan_hormon_prep', 1000);

        if ($active) {
            $this->requireDetail(TRUE, $komponen, 'Komponen/zat aktif PrEP wajib ditulis.');
            $this->requireDetail(TRUE, $dosis, 'Kekuatan/dosis PrEP wajib ditulis.');
            $this->requireDetail($sumber === 'Lainnya', $sumberLainnya, 'Sumber obat PrEP lainnya wajib ditulis.');
            $this->requireDetail($frekuensi === 'Lainnya', $frekuensiLainnya, 'Frekuensi pengambilan PrEP lainnya wajib ditulis.');
        } else {
            $lini = $regimen = $komponen = $bentuk = $dosis = $aturan = '';
            $tanggal = NULL;
            $lama = $ganti = $sumber = $sumberLainnya = $frekuensi = $frekuensiLainnya = '';
            $alasan = array();
        }

        if ($ganti !== 'Pernah') $alasan = array();
        if ($sumber !== 'Lainnya') $sumberLainnya = '';
        if ($frekuensi !== 'Lainnya') $frekuensiLainnya = '';

        return array(
            'status_terapi_prep' => $status,
            'lini_pengobatan_prep' => $lini,
            'regimen_prep' => $regimen,
            'komponen_prep' => $komponen,
            'bentuk_sediaan_prep' => $bentuk,
            'kekuatan_dosis_prep' => $dosis,
            'aturan_pakai_prep' => $aturan,
            'tanggal_mulai_prep' => $tanggal,
            'lama_penggunaan_prep' => $lama,
            'riwayat_ganti_regimen_prep' => $ganti,
            'alasan_ganti_regimen_prep' => $this->joinValues($alasan),
            'sumber_obat_prep' => $sumber,
            'sumber_obat_prep_lainnya' => $sumberLainnya,
            'frekuensi_pengambilan_prep' => $frekuensi,
            'frekuensi_pengambilan_prep_lainnya' => $frekuensiLainnya,
            'penggunaan_obat_lainnya_prep' => $obatLainnya,
            'penggunaan_hormon_prep' => $hormon
        );
    }

    private function text(array $post, $key, $maxLength)
    {
        $value = isset($post[$key]) && is_scalar($post[$key]) ? trim((string) $post[$key]) : '';
        if (mb_strlen($value) > $maxLength) {
            $this->errors[] = 'Isian '.str_replace('_', ' ', $key).' maksimal '.$maxLength.' karakter.';
        }
        return $value;
    }

    private function requiredText(array $post, $key, $label, $maxLength)
    {
        $value = $this->text($post, $key, $maxLength);
        if ($value === '') {
            $this->errors[] = $label.' wajib diisi.';
        }
        return $value;
    }

    private function choice(array $post, $key, array $allowed, $label)
    {
        $value = $this->text($post, $key, 150);
        if (!in_array($value, $allowed, TRUE)) {
            $this->errors[] = $label.' wajib dipilih.';
            return '';
        }
        return $value;
    }

    private function optionalChoice(array $post, $key, array $allowed, $label, $required)
    {
        $value = $this->text($post, $key, 150);
        if ($value === '' && !$required) {
            return '';
        }
        if (!in_array($value, $allowed, TRUE)) {
            $this->errors[] = $label.' wajib dipilih.';
            return '';
        }
        return $value;
    }

    private function multiple(array $post, $key, array $allowed, $label)
    {
        return $this->optionalMultiple($post, $key, $allowed, $label, TRUE);
    }

    private function optionalMultiple(array $post, $key, array $allowed, $label, $required)
    {
        $values = isset($post[$key]) && is_array($post[$key]) ? $post[$key] : array();
        $valid = array();
        foreach ($values as $value) {
            $value = is_scalar($value) ? trim((string) $value) : '';
            if (in_array($value, $allowed, TRUE)) {
                $valid[$value] = $value;
            }
        }
        if ($required && empty($valid)) {
            $this->errors[] = $label.' wajib dipilih.';
        }
        if (isset($valid['Tidak ada']) && count($valid) > 1) {
            $valid = array('Tidak ada' => 'Tidak ada');
        }
        return array_values($valid);
    }

    private function monthValue(array $post, $key, $label, $required)
    {
        $value = $this->text($post, $key, 7);
        if ($value === '' && !$required) {
            return NULL;
        }
        if (!preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $value) || $value > date('Y-m')) {
            $this->errors[] = $label.' tidak valid atau berada di masa depan.';
            return NULL;
        }
        return $value.'-01';
    }

    private function requireDetail($condition, $value, $message)
    {
        if ($condition && trim((string) $value) === '') {
            $this->errors[] = $message;
        }
    }

    private function joinValues(array $values)
    {
        return implode(', ', $values);
    }

    private function lowercase($value)
    {
        return function_exists('mb_strtolower') ? mb_strtolower($value, 'UTF-8') : strtolower($value);
    }
}
