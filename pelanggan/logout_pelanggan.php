<?php
require_once '../config/koneksi.php';
unset($_SESSION['pelanggan_id'], $_SESSION['pelanggan_nama'], $_SESSION['pelanggan_meja'], $_SESSION['keranjang'], $_SESSION['last_order_id']);
session_destroy();
redirect('index.php');