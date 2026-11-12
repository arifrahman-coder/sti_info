ALTER TABLE `status_user`
  MODIFY `nama` varchar(50) NOT NULL;

ALTER TABLE `status_user`
  ADD COLUMN IF NOT EXISTS `deskripsi` varchar(255) NOT NULL DEFAULT '' AFTER `nama`;

UPDATE `status_user`
SET `deskripsi` = 'masyarakat umum'
WHERE `nama` = 'Masyarakat';

INSERT INTO `status_user` (`nama`, `deskripsi`)
SELECT 'Populasi Kunci', 'kelompok yang berisiko tinggi tertular atau menularkan HIV'
WHERE NOT EXISTS (
  SELECT 1 FROM `status_user` WHERE `nama` = 'Populasi Kunci'
);

INSERT INTO `status_user` (`nama`, `deskripsi`)
SELECT 'ODHIV', 'Orang dengan HIV'
WHERE NOT EXISTS (
  SELECT 1 FROM `status_user` WHERE `nama` = 'ODHIV'
);

INSERT INTO `status_user` (`nama`, `deskripsi`)
SELECT 'Tenaga Kesehatan', 'tim skrining, tenaga medis, perawat dan juga dokter'
WHERE NOT EXISTS (
  SELECT 1 FROM `status_user` WHERE `nama` = 'Tenaga Kesehatan'
);

UPDATE `status_user`
SET `deskripsi` = 'kelompok yang berisiko tinggi tertular atau menularkan HIV'
WHERE `nama` = 'Populasi Kunci';

UPDATE `status_user`
SET `deskripsi` = 'Orang dengan HIV'
WHERE `nama` = 'ODHIV';

UPDATE `status_user`
SET `deskripsi` = 'tim skrining, tenaga medis, perawat dan juga dokter'
WHERE `nama` = 'Tenaga Kesehatan';
