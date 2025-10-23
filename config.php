<?php
$host = "localhost"; // Ganti sesuai konfigurasi Anda
$user = "root"; // Ganti sesuai konfigurasi Anda
$pass = ""; // Ganti sesuai konfigurasi Anda
$db   = "imk"; // Ganti sesuai nama database Anda

$koneksi = new mysqli($host, $user, $pass, $db);

if ($koneksi->connect_error) {
    die("Koneksi Gagal: " . $koneksi->connect_error);
}
?>
