<?php

date_default_timezone_set('Asia/Jakarta');
$sekarang = getdate();
$bulan = $sekarang['month'];
$hari = $sekarang['mday'];
$tahun = $sekarang['year'];
$jam = $sekarang['hours'];
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Getdate</title>
</head>

<body>
    <div style="text-align: center;">
        <h1>
            <?php
            if ($jam <= 11) {
                echo 'Selamat Pagi';
            } elseif ($jam <= 15) {
                echo 'Selamat Siang';
            } elseif ($jam <= 18) {
                echo 'Selamat Sore';
            } else {
                echo 'Selamat Malam';
            }
            ?>
        </h1>
        <h2>Selamat datang</h2>
        <h3>Sekarang adalah tanggal <?php echo "$hari $bulan $tahun"; ?></h3>
    </div>
</body>

</html>
