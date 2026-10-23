<?php
require_once '../config/koneksi.php';
requirePelanggan();

$id = (int)($_GET['id'] ?? 0);
unset($_SESSION['keranjang'][$id]);
redirect('pelanggan/keranjang.php');