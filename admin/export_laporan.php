<?php
require_once '../config/koneksi.php';
requireAdmin();

$tgl_awal = $_GET['tgl_awal'] ?? date('Y-m-01');
$tgl_akhir = $_GET['tgl_akhir'] ?? date('Y-m-d');
$ta = mysqli_real_escape_string($koneksi, $tgl_awal);
$tk = mysqli_real_escape_string($koneksi, $tgl_akhir);

$q = mysqli_query($koneksi, "SELECT o.*, p.nama FROM orders o
                             JOIN pelanggan p ON o.pelanggan_id = p.id
                             WHERE DATE(o.waktu_order) BETWEEN '$ta' AND '$tk'
                             ORDER BY o.waktu_order ASC");

$filename = "laporan_order_{$tgl_awal}_sd_{$tgl_akhir}.csv";
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

$output = fopen('php://output', 'w');
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
fputcsv($output, ['No', 'Kode Order', 'Nama', 'Meja', 'Total', 'Status', 'Waktu']);

$total = 0;
$no = 1;
while ($o = mysqli_fetch_assoc($q)) {
    if ($o['status'] === 'selesai') $total += $o['total_harga'];
    fputcsv($output, [
        $no++,
        $o['kode_order'],
        $o['nama'],
        $o['no_meja'],
        $o['total_harga'],
        ucfirst($o['status']),
        date('d/m/Y H:i', strtotime($o['waktu_order'])),
    ]);
}
fputcsv($output, []);
fputcsv($output, ['', '', '', 'TOTAL PENDAPATAN', $total, '', '']);
fclose($output);
exit;