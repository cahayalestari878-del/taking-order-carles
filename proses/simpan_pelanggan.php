<?php
require_once '../config/koneksi.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect('index.php'); }

$nama = trim($_POST['nama'] ?? '');
$no_meja = trim($_POST['no_meja'] ?? '');
$no_hp = trim($_POST['no_hp'] ?? '');

if ($nama === '' || $no_meja === '') {
    $_SESSION['error'] = "Nama dan nomor meja wajib diisi.";
    redirect('index.php');
}

$nama = mysqli_real_escape_string($koneksi, $nama);
$no_meja = mysqli_real_escape_string($koneksi, $no_meja);
$no_hp = mysqli_real_escape_string($koneksi, $no_hp);

$query = "INSERT INTO pelanggan (nama, no_meja, no_hp) VALUES ('$nama','$no_meja','$no_hp')";
if (mysqli_query($koneksi, $query)) {
    $_SESSION['pelanggan_id'] = mysqli_insert_id($koneksi);
    $_SESSION['pelanggan_nama'] = $nama;
    $_SESSION['pelanggan_meja'] = $no_meja;
    $_SESSION['keranjang'] = [];
    redirect('pelanggan/menu.php');
}
$_SESSION['error'] = "Terjadi kesalahan: " . mysqli_error($koneksi);
redirect('index.php');