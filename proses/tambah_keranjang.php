<?php
require_once '../config/koneksi.php';
requirePelanggan();

$menu_id = (int)($_POST['menu_id'] ?? 0);
$jumlah = (int)($_POST['jumlah'] ?? 1);
if ($jumlah < 1) $jumlah = 1;

$q = mysqli_query($koneksi, "SELECT * FROM menu WHERE id = $menu_id AND status = 'tersedia'");
$menu = mysqli_fetch_assoc($q);
if (!$menu) {
    $_SESSION['pesan'] = "Menu tidak ditemukan.";
    redirect('pelanggan/menu.php');
}

if ($jumlah > $menu['stok']) $jumlah = $menu['stok'];
if (!isset($_SESSION['keranjang'])) $_SESSION['keranjang'] = [];

if (isset($_SESSION['keranjang'][$menu_id])) {
    $_SESSION['keranjang'][$menu_id] += $jumlah;
    if ($_SESSION['keranjang'][$menu_id] > $menu['stok']) {
        $_SESSION['keranjang'][$menu_id] = $menu['stok'];
    }
} else {
    $_SESSION['keranjang'][$menu_id] = $jumlah;
}
$_SESSION['pesan'] = $menu['nama_menu'] . " ditambahkan ke keranjang.";
redirect('pelanggan/menu.php');