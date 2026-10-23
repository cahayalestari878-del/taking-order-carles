<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// =====================================================
// KONFIGURASI BASE URL (single source of truth)
// =====================================================
if (!defined('BASE_URL')) {
    define('BASE_URL', 'http://localhost/taking-order-carles/');
}
if (!defined('ADMIN_URL')) {
    define('ADMIN_URL', BASE_URL . 'admin/');
}
if (!defined('PELANGGAN_URL')) {
    define('PELANGGAN_URL', BASE_URL . 'pelanggan/');
}
if (!defined('PROSES_URL')) {
    define('PROSES_URL', BASE_URL . 'proses/');
}
if (!defined('UPLOADS_URL')) {
    define('UPLOADS_URL', BASE_URL . 'uploads/');
}

// =====================================================
// KONEKSI DATABASE
// =====================================================
$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_cafe";

$koneksi = mysqli_connect($host, $user, $pass, $db);
if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

date_default_timezone_set('Asia/Jakarta');

// =====================================================
// HELPER FUNCTIONS
// =====================================================

function rupiah($angka) {
    return "Rp " . number_format((float)$angka, 0, ',', '.');
}

function generateKodeOrder() {
    return "ORD-" . date("YmdHis") . "-" . rand(100, 999);
}

function url($path = '') {
    return BASE_URL . ltrim($path, '/');
}

function redirect($path = '') {
    header("Location: " . url($path));
    exit;
}

function requireAdmin() {
    if (!isset($_SESSION['admin_id'])) {
        redirect('admin/login.php');
    }
}

function requirePelanggan() {
    if (!isset($_SESSION['pelanggan_id'])) {
        redirect('index.php');
    }
}

function uploadGambar($fileInput, $targetDir = __DIR__ . '/../uploads/') {
    if (!isset($fileInput) || $fileInput['error'] !== UPLOAD_ERR_OK) {
        return [false, "Tidak ada file yang diunggah."];
    }
    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    $mime = mime_content_type($fileInput['tmp_name']);
    if (!isset($allowed[$mime])) {
        return [false, "Format gambar harus JPG, PNG, atau WEBP."];
    }
    if ($fileInput['size'] > 2 * 1024 * 1024) {
        return [false, "Ukuran gambar maksimal 2 MB."];
    }
    if (!is_dir($targetDir)) mkdir($targetDir, 0775, true);
    $ext = $allowed[$mime];
    $filename = 'menu_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    if (move_uploaded_file($fileInput['tmp_name'], $targetDir . $filename)) {
        return [true, $filename];
    }
    return [false, "Gagal menyimpan gambar."];
}