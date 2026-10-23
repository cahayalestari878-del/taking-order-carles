<?php
require_once '../config/koneksi.php';
requirePelanggan();

$menu_id = (int)($_POST['menu_id'] ?? 0);
$jumlah = (int)($_POST['jumlah'] ?? 1);
if ($jumlah < 1) $jumlah = 1;
if (isset($_SESSION['keranjang'][$menu_id])) {
    $_SESSION['keranjang'][$menu_id] = $jumlah;
}
redirect('pelanggan/keranjang.php');