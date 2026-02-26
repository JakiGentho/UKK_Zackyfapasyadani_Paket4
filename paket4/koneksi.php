<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "peminjaman_buku";

$koneksi = mysqli_connect($servername, $username, $password, $database);

if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

mysqli_set_charset($koneksi, "utf8");
?>
