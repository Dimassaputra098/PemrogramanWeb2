<?php

date_default_timezone_set('Asia/Jakarta');
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Tanggal</title>
</head>

<body>
    <div style="font-size: 10px;">
        <?php
        echo 'Sekarang tanggal ';
        echo date('d-F-Y');
        echo '<br>dan jam ';
        echo date('h:i:s A');
        ?>
    </div>
</body>

</html>
