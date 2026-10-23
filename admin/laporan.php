<?php
require_once '../config/koneksi.php';
requireAdmin();

$judul = "Laporan Order";

$tgl_awal = $_GET['tgl_awal'] ?? date('Y-m-01');
$tgl_akhir = $_GET['tgl_akhir'] ?? date('Y-m-d');
$ta = mysqli_real_escape_string($koneksi, $tgl_awal);
$tk = mysqli_real_escape_string($koneksi, $tgl_akhir);

$q = mysqli_query($koneksi, "SELECT o.*, p.nama FROM orders o
                             JOIN pelanggan p ON o.pelanggan_id = p.id
                             WHERE DATE(o.waktu_order) BETWEEN '$ta' AND '$tk'
                             ORDER BY o.waktu_order DESC");
$total_pendapatan = 0;
$data = [];
while ($row = mysqli_fetch_assoc($q)) {
    $data[] = $row;
    if ($row['status'] === 'selesai') $total_pendapatan += $row['total_harga'];
}
include '../includes/header.php';
?>
<div class="flex min-h-screen">
    <?php include '../includes/sidebar_admin.php'; ?>
    <main class="flex-1 bg-cream p-6 md:p-8">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-ink">Laporan Order</h1>
            <p class="text-muted text-sm mt-1">Filter berdasarkan rentang tanggal</p>
        </div>

        <form method="GET" action="<?= ADMIN_URL; ?>laporan.php" class="bg-white border border-line rounded-xl p-4 mb-5 grid md:grid-cols-4 gap-3 items-end">
            <div>
                <label class="block text-xs font-semibold mb-1.5 text-muted uppercase tracking-wider">Tanggal Awal</label>
                <input type="date" name="tgl_awal" value="<?= htmlspecialchars($tgl_awal); ?>"
                    class="w-full border border-line rounded-lg px-3 py-2.5 text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold mb-1.5 text-muted uppercase tracking-wider">Tanggal Akhir</label>
                <input type="date" name="tgl_akhir" value="<?= htmlspecialchars($tgl_akhir); ?>"
                    class="w-full border border-line rounded-lg px-3 py-2.5 text-sm">
            </div>
            <div>
                <button type="submit" class="w-full bg-primary text-cream py-2.5 rounded-lg text-sm font-semibold hover:bg-secondary transition-colors inline-flex items-center justify-center gap-2">
                    <i data-lucide="search" class="w-4 h-4"></i> Filter
                </button>
            </div>
            <div class="flex gap-2">
                <a href="<?= ADMIN_URL; ?>cetak_laporan.php?tgl_awal=<?= urlencode($tgl_awal); ?>&tgl_akhir=<?= urlencode($tgl_akhir); ?>"
                    target="_blank"
                    class="flex-1 text-center bg-white border border-primary text-primary py-2.5 rounded-lg text-sm font-semibold hover:bg-primary hover:text-cream transition-colors inline-flex items-center justify-center gap-1.5">
                    <i data-lucide="printer" class="w-4 h-4"></i> Cetak
                </a>
                <a href="<?= ADMIN_URL; ?>export_laporan.php?tgl_awal=<?= urlencode($tgl_awal); ?>&tgl_akhir=<?= urlencode($tgl_akhir); ?>"
                    class="flex-1 text-center bg-green-600 text-white py-2.5 rounded-lg text-sm font-semibold hover:bg-green-700 transition-colors inline-flex items-center justify-center gap-1.5">
                    <i data-lucide="download" class="w-4 h-4"></i> CSV
                </a>
            </div>
        </form>

        <div class="bg-white border border-line rounded-xl p-5 mb-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-lg bg-accent/20 flex items-center justify-center shrink-0">
                <i data-lucide="wallet" class="w-6 h-6 text-secondary"></i>
            </div>
            <div>
                <p class="text-xs uppercase tracking-widest text-muted">Total Pendapatan (order selesai)</p>
                <p class="text-2xl font-bold text-primary mt-0.5"><?= rupiah($total_pendapatan); ?></p>
            </div>
        </div>

        <div class="bg-white border border-line rounded-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-cream border-b border-line">
                        <tr>
                            <th class="text-left px-5 py-3 text-xs uppercase tracking-wider text-muted font-semibold">No</th>
                            <th class="text-left px-5 py-3 text-xs uppercase tracking-wider text-muted font-semibold">Kode</th>
                            <th class="text-left px-5 py-3 text-xs uppercase tracking-wider text-muted font-semibold">Nama</th>
                            <th class="text-center px-5 py-3 text-xs uppercase tracking-wider text-muted font-semibold">Meja</th>
                            <th class="text-right px-5 py-3 text-xs uppercase tracking-wider text-muted font-semibold">Total</th>
                            <th class="text-center px-5 py-3 text-xs uppercase tracking-wider text-muted font-semibold">Status</th>
                            <th class="text-left px-5 py-3 text-xs uppercase tracking-wider text-muted font-semibold">Waktu</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        <?php $no = 1; foreach ($data as $o): ?>
                        <tr class="hover:bg-cream/50 transition-colors">
                            <td class="px-5 py-2.5 text-ink"><?= $no++; ?></td>
                            <td class="px-5 py-2.5 font-mono text-xs text-ink"><?= htmlspecialchars($o['kode_order']); ?></td>
                            <td class="px-5 py-2.5 text-ink"><?= htmlspecialchars($o['nama']); ?></td>
                            <td class="px-5 py-2.5 text-center text-ink"><?= htmlspecialchars($o['no_meja']); ?></td>
                            <td class="px-5 py-2.5 text-right font-medium text-ink"><?= rupiah($o['total_harga']); ?></td>
                            <td class="px-5 py-2.5 text-center text-ink"><?= ucfirst($o['status']); ?></td>
                            <td class="px-5 py-2.5 text-xs text-muted"><?= date('d/m/Y H:i', strtotime($o['waktu_order'])); ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($data)): ?>
                        <tr><td colspan="7" class="px-5 py-10 text-center text-muted">Tidak ada data pada periode ini.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>
<?php include '../includes/footer.php'; ?>