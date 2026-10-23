<?php
require_once '../config/koneksi.php';

$username = mysqli_real_escape_string($koneksi, $_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

$q = mysqli_query($koneksi, "SELECT * FROM admin WHERE username = '$username'");
$admin = mysqli_fetch_assoc($q);

if ($admin && password_verify($password, $admin['password'])) {
    $_SESSION['admin_id']   = $admin['id'];
    $_SESSION['admin_nama'] = $admin['nama_lengkap'];
    redirect('admin/dashboard.php');
} else {
    $_SESSION['error'] = "Username atau password salah.";
    redirect('admin/login.php');
}