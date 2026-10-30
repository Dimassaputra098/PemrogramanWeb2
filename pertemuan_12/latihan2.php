<?php
$dbhost = "localhost";
$dbuser = "root";
$dbpass = "";

$connection = mysqli_connect($dbhost, $dbuser, $dbpass);
if (!$connection) {
    echo "Tidak dapat terhubung dengan database: " . mysqli_connect_error();
    exit;
}

$pilih_db = mysqli_select_db($connection, $dbname);
if (!$pilih_db) {
    echo "Tidak dapat memilih database";
    exit;
}

echo "Koneksi ke database lat_dbase berhasil!";
?>