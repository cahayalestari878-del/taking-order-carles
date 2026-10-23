<?php
require_once '../config/koneksi.php';
requireAdmin();

$id = (int)($_GET['id'] ?? 0);
$q = mysqli_query($koneksi, "SELECT gambar FROM menu WHERE id = $id");
$m = mysqli_fetch_assoc($q);
if ($m && $m['gambar'] && file_exists('../uploads/'.$m['gambar'])) {
    unlink('../uploads/'.$m['gambar']);
}
mysqli_query($koneksi, "DELETE FROM menu WHERE id = $id");
$_SESSION['pesan'] = "Menu berhasil dihapus.";
redirect('admin/menu.php');