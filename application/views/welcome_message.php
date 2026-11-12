<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Analisa Data</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <style type="text/css">
  	.table {
  		font-size: 11px;
  	}
  </style>

</head>
<body>
<h1>DATA KEPEMILIKAN LAPTOP PRODI STI 2024/2025</h1>
<?php
foreach ($kelas as $key => $value) {
?>
<table class="table table-bordered">
	<tr>
		<td colspan="4"><b>KELAS <?=$value['kelas']?></b></td>
	</tr>
	<tr>
		<td>NO</td>
		<td>NIM</td>
		<td>NAMA</td>
		<td>KELAS</td>
		<td>STATUS KEPEMILIKAN</td>
	</tr>
	<?php
	$kls = $value['kelas'];
	$query = $this->db->query("SELECT * FROM kepemilikan_laptop WHERE kelas = '".$kls."' GROUP BY nim ")->result_array();
	$totMhs = $this->db->query("SELECT COUNT(DISTINCT nim) AS jml FROM kepemilikan_laptop WHERE kelas = '".$kls."'")->row_array();
	$totMhstdkLaptop = $this->db->query("SELECT COUNT(DISTINCT nim) AS jml FROM kepemilikan_laptop WHERE kelas = '".$kls."' AND sts = 'BELUM'")->row_array();
	$totMhsLaptop = $this->db->query("SELECT COUNT(DISTINCT nim) AS jml FROM kepemilikan_laptop WHERE kelas = '".$kls."' AND sts = 'SUDAH'")->row_array();
	//$jmlTidakLaptop
	$i=1;
	foreach ($query as $key => $value_detail) {
	?>
	<tr>
		<td><?=$i?></td>
		<td><?=$value_detail['nim']?></td>
		<td><?=$value_detail['nama_lengkap']?></td>
		<td><?=$value_detail['kelas']?></td>
		<td><?=$value_detail['sts']?></td>
	</tr>
	<?php
	$i++;
	}
	?>
	<tr>
		<th colspan="3">Jumlah Mahasiswa</th>
		<th colspan="3"><?=$totMhs['jml']?></th>
	</tr>
	<tr class="table-primary">
		<th colspan="3">Tidak Punya Laptop</th>
		<th colspan="3"><?=$totMhstdkLaptop['jml']?></th>
	</tr>
	<tr>
		<th colspan="3">Punya Laptop</th>
		<th colspan="3"><?=$totMhsLaptop['jml']?></th>
	</tr>
	<tr class="table-success">
		<th colspan="3">Jumlah PC STI</th>
		<th colspan="3">10</th>
	</tr>
	<tr>
		<th colspan="3">Jumlah MHS yang bisa pratikum</th>
		<th colspan="3"><?=$totMhsLaptop['jml']+10?></th>
	</tr>
	<tr class="table-danger">
		<th colspan="3">Jumlah MHS yang tidak bisa pratikum</th>
		<th colspan="3"><?=$totMhs['jml']-($totMhsLaptop['jml']+10)?></th>
	</tr>
</table>
<?php

}
?>

</body>
</html>
