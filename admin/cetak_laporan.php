<?php
require_once '../config/koneksi.php';

// Guard login inline (JANGAN pakai requireAdmin() karena redirect() mengirim header
// yang bisa konflik dengan output HTML laporan cetak).
if (!isset($_SESSION['admin_id'])) {
    redirect('admin/login.php');
}

$tgl_awal  = $_GET['tgl_awal']  ?? date('Y-m-01');
$tgl_akhir = $_GET['tgl_akhir'] ?? date('Y-m-d');

$ta = mysqli_real_escape_string($koneksi, $tgl_awal);
$tk = mysqli_real_escape_string($koneksi, $tgl_akhir);

$q = mysqli_query($koneksi, "SELECT o.*, p.nama FROM orders o
                             JOIN pelanggan p ON o.pelanggan_id = p.id
                             WHERE DATE(o.waktu_order) BETWEEN '$ta' AND '$tk'
                             ORDER BY o.waktu_order ASC");

$total_pendapatan = 0;
$total_order_selesai = 0;
$total_order = 0;
$data = [];
while ($row = mysqli_fetch_assoc($q)) {
    $data[] = $row;
    $total_order++;
    if ($row['status'] === 'selesai') {
        $total_pendapatan += $row['total_harga'];
        $total_order_selesai++;
    }
}

// Cek apakah logo ada
$logoPath = __DIR__ . '/../assets/logo-brand.png';
$logoUrl  = BASE_URL . 'assets/logo-brand.png';
$hasLogo  = file_exists($logoPath);

$namaAdmin = $_SESSION['admin_nama'] ?? 'Administrator';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Laporan - Serenéa CAFE</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* ============================================
           KONFIGURASI CETAK — A4 Portrait
           ============================================ */
        @page {
            size: A4 portrait;
            margin: 15mm 12mm 18mm 12mm;
        }

        /* Pastikan warna & background tercetak */
        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        html, body {
            font-family: 'Inter', 'Segoe UI', Tahoma, sans-serif;
            font-size: 11px;
            color: #142214;
            background: #fff;
            margin: 0;
            padding: 0;
            line-height: 1.45;
        }

        /* Wrapper utama */
        .laporan-wrapper {
            max-width: 190mm;
            margin: 0 auto;
            padding: 12mm 0;
        }

        /* Kop surat */
        .kop-surat {
            display: flex;
            align-items: center;
            gap: 14px;
            padding-bottom: 12px;
            border-bottom: 3px double #1e331e;
            margin-bottom: 14px;
        }
        .kop-surat .logo {
            width: 62px;
            height: 62px;
            object-fit: contain;
            flex-shrink: 0;
        }
        .kop-surat .logo-fallback {
            width: 62px;
            height: 62px;
            border: 2px solid #1e331e;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            font-weight: 700;
            color: #1e331e;
            text-align: center;
            line-height: 1.1;
            flex-shrink: 0;
        }
        .kop-surat .kop-text h1 {
            margin: 0;
            font-size: 22px;
            font-weight: 800;
            letter-spacing: 3px;
            color: #1e331e;
            line-height: 1.1;
        }
        .kop-surat .kop-text p {
            margin: 2px 0 0;
            font-size: 10px;
            color: #5c6b5b;
        }

        /* Judul laporan */
        .judul-laporan {
            text-align: center;
            margin: 0 0 14px;
        }
        .judul-laporan h2 {
            margin: 0 0 3px;
            font-size: 14px;
            font-weight: 700;
            letter-spacing: 2px;
            color: #1e331e;
            text-transform: uppercase;
        }
        .judul-laporan .periode {
            font-size: 11px;
            color: #5c6b5b;
        }

        /* Info meta (dicetak pada, admin) */
        .info-meta {
            display: flex;
            justify-content: space-between;
            font-size: 10px;
            color: #5c6b5b;
            margin-bottom: 10px;
            padding: 8px 10px;
            background: #f9f8f3;
            border: 1px solid #e2e8df;
            border-radius: 4px;
        }
        .info-meta div { line-height: 1.6; }
        .info-meta strong { color: #142214; font-weight: 600; }

        /* Tabel */
        table.laporan {
            width: 100%;
            border-collapse: collapse;
            font-size: 10.5px;
            margin-bottom: 12px;
        }
        table.laporan thead {
            display: table-header-group; /* header ulang di setiap halaman */
        }
        table.laporan thead th {
            background: #1e331e !important;
            color: #f9f8f3 !important;
            padding: 7px 6px;
            font-weight: 600;
            text-align: left;
            border: 1px solid #1e331e;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        table.laporan tbody td {
            padding: 6px;
            border: 1px solid #e2e8df;
            vertical-align: top;
        }
        table.laporan tbody tr:nth-child(even) {
            background: #fafaf7;
        }
        table.laporan tbody tr.no-data td {
            text-align: center;
            padding: 30px 10px;
            color: #5c6b5b;
            font-style: italic;
        }

        /* Alignment kolom */
        .col-no      { width: 32px;  text-align: center; }
        .col-kode    { width: 110px; font-family: 'Courier New', monospace; font-size: 10px; }
        .col-nama    { width: auto; }
        .col-meja    { width: 44px;  text-align: center; }
        .col-total   { width: 90px;  text-align: right; font-weight: 600; }
        .col-status  { width: 70px;  text-align: center; }
        .col-waktu   { width: 100px; text-align: center; white-space: nowrap; }

        /* Badge status */
        .badge {
            display: inline-block;
            padding: 1px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: 600;
            text-transform: capitalize;
        }
        .badge-pending    { background: #fef3c7; color: #92400e; }
        .badge-diproses   { background: #dbeafe; color: #1e40af; }
        .badge-selesai    { background: #dcfce7; color: #166534; }
        .badge-dibatalkan { background: #fee2e2; color: #991b1b; }

        /* Ringkasan */
        .ringkasan {
            display: grid;
            grid-template-columns: 1fr 1fr 1.4fr;
            gap: 8px;
            margin-bottom: 18px;
            page-break-inside: avoid;
        }
        .ringkasan .box {
            border: 1px solid #e2e8df;
            border-radius: 4px;
            padding: 8px 10px;
        }
        .ringkasan .box .label {
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #5c6b5b;
            font-weight: 600;
        }
        .ringkasan .box .value {
            font-size: 13px;
            font-weight: 700;
            color: #142214;
            margin-top: 2px;
        }
        .ringkasan .box.primary {
            background: #1e331e;
            border-color: #1e331e;
        }
        .ringkasan .box.primary .label { color: #a1b98e; }
        .ringkasan .box.primary .value { color: #f9f8f3; font-size: 15px; }

        /* Tanda tangan */
        .ttd-wrapper {
            margin-top: 24px;
            display: flex;
            justify-content: flex-end;
            page-break-inside: avoid;
        }
        .ttd {
            text-align: center;
            width: 200px;
        }
        .ttd .kota-tanggal {
            font-size: 10.5px;
            margin-bottom: 4px;
            color: #142214;
        }
        .ttd .jabatan {
            font-size: 10.5px;
            margin-bottom: 56px;
            color: #142214;
        }
        .ttd .nama {
            font-size: 11px;
            font-weight: 700;
            color: #1e331e;
            border-top: 1px solid #142214;
            padding-top: 4px;
            margin: 0 12px;
        }

        /* Footer cetak */
        .footer-cetak {
            margin-top: 20px;
            padding-top: 8px;
            border-top: 1px solid #e2e8df;
            display: flex;
            justify-content: space-between;
            font-size: 9px;
            color: #5c6b5b;
        }

        /* ============================================
           TOOLBAR (hanya tampil di layar, tidak cetak)
           ============================================ */
        .toolbar-cetak {
            position: fixed;
            top: 16px;
            right: 16px;
            z-index: 9999;
            display: flex;
            gap: 8px;
            background: #fff;
            padding: 10px;
            border-radius: 10px;
            box-shadow: 0 10px 25px -5px rgba(20,34,20,.15), 0 4px 6px -2px rgba(20,34,20,.08);
            border: 1px solid #e2e8df;
        }
        .toolbar-cetak button,
        .toolbar-cetak a {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 14px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            transition: background .15s ease;
            text-decoration: none;
            font-family: inherit;
        }
        .btn-print {
            background: #1e331e;
            color: #f9f8f3;
        }
        .btn-print:hover { background: #4a6b46; }
        .btn-close {
            background: #f9f8f3;
            color: #142214;
            border: 1px solid #e2e8df !important;
        }
        .btn-close:hover { background: #fff; border-color: #4a6b46 !important; }

        /* ============================================
           MODE CETAK — sembunyikan toolbar & elemen UI
           ============================================ */
        @media print {
            .no-print { display: none !important; }
            .laporan-wrapper { padding: 0; max-width: none; }
            body { font-size: 10.5px; }

            /* Nomor halaman di kanan bawah */
            .footer-cetak::after {
                content: "Halaman " counter(page) " dari " counter(pages);
                font-weight: 600;
                color: #142214;
            }
        }
    </style>
</head>
<body>

<!-- Toolbar (tidak muncul di cetak) -->
<div class="toolbar-cetak no-print">
    <button type="button" class="btn-print" onclick="doPrint()">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
        Cetak
    </button>
    <a href="<?= ADMIN_URL; ?>laporan.php?tgl_awal=<?= urlencode($tgl_awal); ?>&tgl_akhir=<?= urlencode($tgl_akhir); ?>" class="btn-close">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        Tutup
    </a>
</div>

<div class="laporan-wrapper">

    <!-- KOP SURAT -->
    <div class="kop-surat">
        <?php if ($hasLogo): ?>
            <img src="<?= $logoUrl; ?>" alt="Serenéa CAFE" class="logo"
                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
            <div class="logo-fallback" style="display:none;">SERENÉA</div>
        <?php else: ?>
            <div class="logo-fallback">SERENÉA</div>
        <?php endif; ?>
        <div class="kop-text">
            <h1>SERENÉA CAFE</h1>
            <p>Jl. Contoh Alamat No. 123, Kota Anda 12345</p>
            <p>Telp: 021-1234567 &nbsp;|&nbsp; Email: info@sereneacafe.com</p>
        </div>
    </div>

    <!-- JUDUL -->
    <div class="judul-laporan">
        <h2>Laporan Order</h2>
        <div class="periode">
            Periode: <?= date('d/m/Y', strtotime($tgl_awal)); ?> &ndash; <?= date('d/m/Y', strtotime($tgl_akhir)); ?>
        </div>
    </div>

    <!-- INFO META -->
    <div class="info-meta">
        <div>
            <strong>Dicetak:</strong> <?= date('d/m/Y H:i'); ?> WIB
        </div>
        <div>
            <strong>Oleh:</strong> <?= htmlspecialchars($namaAdmin); ?>
        </div>
        <div>
            <strong>Total Data:</strong> <?= (int)$total_order; ?> order
        </div>
    </div>

    <!-- TABEL -->
    <table class="laporan">
        <thead>
            <tr>
                <th class="col-no">No</th>
                <th class="col-kode">Kode Order</th>
                <th class="col-nama">Nama Pelanggan</th>
                <th class="col-meja">Meja</th>
                <th class="col-total">Total</th>
                <th class="col-status">Status</th>
                <th class="col-waktu">Waktu</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($data)): ?>
                <tr class="no-data">
                    <td colspan="7">Tidak ada data pada periode ini.</td>
                </tr>
            <?php else: ?>
                <?php $no = 1; foreach ($data as $o): ?>
                    <?php
                    $badgeClass = 'badge-pending';
                    if ($o['status'] === 'diproses')   $badgeClass = 'badge-diproses';
                    elseif ($o['status'] === 'selesai')    $badgeClass = 'badge-selesai';
                    elseif ($o['status'] === 'dibatalkan') $badgeClass = 'badge-dibatalkan';
                    ?>
                    <tr>
                        <td class="col-no"><?= $no++; ?></td>
                        <td class="col-kode"><?= htmlspecialchars($o['kode_order']); ?></td>
                        <td class="col-nama"><?= htmlspecialchars($o['nama']); ?></td>
                        <td class="col-meja"><?= htmlspecialchars($o['no_meja']); ?></td>
                        <td class="col-total"><?= rupiah($o['total_harga']); ?></td>
                        <td class="col-status">
                            <span class="badge <?= $badgeClass; ?>"><?= ucfirst($o['status']); ?></span>
                        </td>
                        <td class="col-waktu"><?= date('d/m/Y H:i', strtotime($o['waktu_order'])); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- RINGKASAN -->
    <div class="ringkasan">
        <div class="box">
            <div class="label">Total Order</div>
            <div class="value"><?= (int)$total_order; ?></div>
        </div>
        <div class="box">
            <div class="label">Order Selesai</div>
            <div class="value"><?= (int)$total_order_selesai; ?></div>
        </div>
        <div class="box primary">
            <div class="label">Total Pendapatan</div>
            <div class="value"><?= rupiah($total_pendapatan); ?></div>
        </div>
    </div>

    <!-- TANDA TANGAN -->
    <div class="ttd-wrapper">
        <div class="ttd">
            <div class="kota-tanggal">Kota Anda, <?= date('d/m/Y'); ?></div>
            <div class="jabatan">Administrator,</div>
            <div class="nama"><?= htmlspecialchars($namaAdmin); ?></div>
        </div>
    </div>

    <!-- FOOTER CETAK -->
    <div class="footer-cetak">
        <span>Serenéa CAFE &mdash; Sistem Pemesanan Berbasis Web</span>
        <span>Dokumen ini digenerate otomatis oleh sistem.</span>
    </div>

</div>

<script>
    // Flag agar auto-print hanya sekali
    let hasPrinted = false;

    function doPrint() {
        window.print();
    }

    // Tunggu font & layout stabil sebelum auto-print
    function autoPrint() {
        if (hasPrinted) return;
        hasPrinted = true;

        const trigger = () => {
            // double requestAnimationFrame memastikan layout sudah settle
            requestAnimationFrame(() => {
                requestAnimationFrame(() => {
                    setTimeout(() => window.print(), 150);
                });
            });
        };

        if (document.fonts && document.fonts.ready) {
            document.fonts.ready.then(trigger).catch(trigger);
        } else {
            trigger();
        }
    }

    // Auto-print saat halaman selesai load
    window.addEventListener('load', () => {
        setTimeout(autoPrint, 200);
    });

    // Setelah print dialog ditutup, JANGAN close tab otomatis
    // (browser modern blokir window.close() untuk tab yang dibuka manual).
    // Biarkan user menutup manual via tombol "Tutup" di toolbar.
    window.addEventListener('afterprint', () => {
        // Bisa dipakai untuk tracking, tidak untuk close()
    });
</script>

</body>
</html>