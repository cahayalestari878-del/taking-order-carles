<?php
require_once '../config/koneksi.php';
requireAdmin();

$judul = "Dashboard Admin";

$total_menu = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS j FROM menu"))['j'];
$total_order = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS j FROM orders"))['j'];
$total_pending = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS j FROM orders WHERE status='pending'"))['j'];
$total_pendapatan = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COALESCE(SUM(total_harga),0) AS j FROM orders WHERE status='selesai'"))['j'];

$order_hari_ini = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS j FROM orders WHERE DATE(waktu_order) = CURDATE()"))['j'];
$pendapatan_hari_ini = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COALESCE(SUM(total_harga),0) AS j FROM orders WHERE status='selesai' AND DATE(waktu_order) = CURDATE()"))['j'];

$recent = mysqli_query($koneksi, "SELECT o.*, p.nama FROM orders o
                                  JOIN pelanggan p ON o.pelanggan_id = p.id
                                  ORDER BY o.waktu_order DESC LIMIT 5");

$labels = []; $values = [];
for ($i = 6; $i >= 0; $i--) {
    $tgl = date('Y-m-d', strtotime("-$i days"));
    $labels[] = date('d/m', strtotime($tgl));
    $val = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COALESCE(SUM(total_harga),0) AS j FROM orders WHERE status='selesai' AND DATE(waktu_order) = '$tgl'"))['j'];
    $values[] = (float)$val;
}

$top_menu = mysqli_query($koneksi, "SELECT nama_menu, SUM(jumlah) AS total FROM order_detail
                                    GROUP BY menu_id, nama_menu ORDER BY total DESC LIMIT 5");

include '../includes/header.php';
?>
<div class="flex min-h-screen">
    <?php include '../includes/sidebar_admin.php'; ?>
    <main class="flex-1 bg-cream p-6 md:p-8">
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-ink">Dashboard</h1>
            <p class="text-muted text-sm mt-1">Selamat datang kembali, <?= htmlspecialchars($_SESSION['admin_nama']); ?></p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-white border border-line rounded-xl p-5">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs uppercase tracking-widest text-muted">Total Menu</span>
                    <i data-lucide="coffee" class="w-4 h-4 text-secondary"></i>
                </div>
                <p class="text-3xl font-bold text-ink"><?= (int)$total_menu; ?></p>
            </div>
            <div class="bg-white border border-line rounded-xl p-5">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs uppercase tracking-widest text-muted">Total Order</span>
                    <i data-lucide="receipt-text" class="w-4 h-4 text-secondary"></i>
                </div>
                <p class="text-3xl font-bold text-ink"><?= (int)$total_order; ?></p>
                <p class="text-xs text-muted mt-1"><?= (int)$order_hari_ini; ?> order hari ini</p>
            </div>
            <div class="bg-white border border-line rounded-xl p-5">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs uppercase tracking-widest text-muted">Pending</span>
                    <i data-lucide="clock" class="w-4 h-4 text-amber-600"></i>
                </div>
                <p class="text-3xl font-bold text-ink"><?= (int)$total_pending; ?></p>
                <a href="<?= ADMIN_URL; ?>order.php?status=pending" class="text-xs text-secondary hover:underline mt-1 inline-block font-medium">Lihat antrian</a>
            </div>
            <div class="bg-primary text-cream rounded-xl p-5">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs uppercase tracking-widest text-cream/60">Pendapatan</span>
                    <i data-lucide="wallet" class="w-4 h-4 text-accent"></i>
                </div>
                <p class="text-2xl font-bold"><?= rupiah($total_pendapatan); ?></p>
                <p class="text-xs text-cream/60 mt-1">+<?= rupiah($pendapatan_hari_ini); ?> hari ini</p>
            </div>
        </div>

        <div class="grid lg:grid-cols-3 gap-4">
            <div class="lg:col-span-2 bg-white border border-line rounded-xl p-5">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-semibold text-sm text-ink flex items-center gap-2">
                        <i data-lucide="trending-up" class="w-4 h-4 text-secondary"></i>
                        Pendapatan 7 Hari Terakhir
                    </h2>
                </div>
                <div class="h-64"><canvas id="chartPendapatan"></canvas></div>
            </div>

            <div class="bg-white border border-line rounded-xl p-5">
                <h2 class="font-semibold text-sm text-ink flex items-center gap-2 mb-4">
                    <i data-lucide="flame" class="w-4 h-4 text-secondary"></i> Menu Terlaris
                </h2>
                <div class="space-y-3.5">
                    <?php
                    $rank = 1;
                    $topArr = [];
                    while ($tm = mysqli_fetch_assoc($top_menu)) $topArr[] = $tm;
                    $maxVal = $topArr ? max(array_column($topArr, 'total')) : 1;
                    foreach ($topArr as $tm): ?>
                    <div class="flex items-center gap-3">
                        <span class="w-6 h-6 rounded-md bg-cream text-primary text-xs font-bold flex items-center justify-center shrink-0"><?= $rank++; ?></span>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-ink truncate"><?= htmlspecialchars($tm['nama_menu']); ?></p>
                            <div class="h-1 bg-cream rounded-full mt-1.5 overflow-hidden">
                                <div class="h-full bg-secondary" style="width: <?= max(6, ($tm['total'] / $maxVal) * 100); ?>%"></div>
                            </div>
                        </div>
                        <span class="text-xs text-muted shrink-0"><?= (int)$tm['total']; ?>x</span>
                    </div>
                    <?php endforeach; ?>
                    <?php if (empty($topArr)): ?>
                        <p class="text-sm text-muted text-center py-4">Belum ada data penjualan.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="bg-white border border-line rounded-xl overflow-hidden mt-4">
            <div class="px-5 py-3.5 border-b border-line flex items-center justify-between">
                <h2 class="font-semibold text-sm text-ink flex items-center gap-2">
                    <i data-lucide="receipt" class="w-4 h-4 text-secondary"></i> Order Terbaru
                </h2>
                <a href="<?= ADMIN_URL; ?>order.php" class="text-xs text-secondary hover:underline font-medium">Lihat semua</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-cream border-b border-line">
                        <tr>
                            <th class="text-left px-5 py-2.5 text-xs uppercase tracking-wider text-muted font-semibold">Kode</th>
                            <th class="text-left px-5 py-2.5 text-xs uppercase tracking-wider text-muted font-semibold">Pelanggan</th>
                            <th class="text-center px-5 py-2.5 text-xs uppercase tracking-wider text-muted font-semibold">Meja</th>
                            <th class="text-right px-5 py-2.5 text-xs uppercase tracking-wider text-muted font-semibold">Total</th>
                            <th class="text-center px-5 py-2.5 text-xs uppercase tracking-wider text-muted font-semibold">Status</th>
                            <th class="text-left px-5 py-2.5 text-xs uppercase tracking-wider text-muted font-semibold">Waktu</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        <?php while ($o = mysqli_fetch_assoc($recent)): ?>
                        <tr class="hover:bg-cream/50 transition-colors">
                            <td class="px-5 py-3 font-mono text-xs text-ink"><?= htmlspecialchars($o['kode_order']); ?></td>
                            <td class="px-5 py-3 text-ink"><?= htmlspecialchars($o['nama']); ?></td>
                            <td class="px-5 py-3 text-center text-ink"><?= htmlspecialchars($o['no_meja']); ?></td>
                            <td class="px-5 py-3 text-right font-medium text-ink"><?= rupiah($o['total_harga']); ?></td>
                            <td class="px-5 py-3 text-center">
                                <?php
                                $badge = 'bg-gray-100 text-gray-700';
                                if ($o['status']==='pending') $badge='bg-amber-50 text-amber-700';
                                elseif ($o['status']==='diproses') $badge='bg-blue-50 text-blue-700';
                                elseif ($o['status']==='selesai') $badge='bg-green-50 text-green-700';
                                elseif ($o['status']==='dibatalkan') $badge='bg-red-50 text-red-700';
                                ?>
                                <span class="text-xs px-2 py-0.5 rounded font-medium <?= $badge; ?>"><?= ucfirst($o['status']); ?></span>
                            </td>
                            <td class="px-5 py-3 text-xs text-muted"><?= date('d/m/Y H:i', strtotime($o['waktu_order'])); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
new Chart(document.getElementById('chartPendapatan'), {
    type: 'line',
    data: {
        labels: <?= json_encode($labels); ?>,
        datasets: [{
            data: <?= json_encode($values); ?>,
            borderColor: '#4a6b46',
            backgroundColor: 'rgba(142, 168, 125, 0.12)',
            fill: true,
            tension: 0.35,
            pointBackgroundColor: '#1e331e',
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            pointRadius: 4,
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false }, tooltip: { backgroundColor: '#1e331e', padding: 10, cornerRadius: 8 } },
        scales: {
            y: {
                beginAtZero: true,
                grid: { color: '#e2e8df' },
                ticks: { callback: v => 'Rp ' + v.toLocaleString('id-ID'), color: '#5c6b5b', font: { size: 11 } }
            },
            x: { grid: { display: false }, ticks: { color: '#5c6b5b', font: { size: 11 } } }
        }
    }
});
</script>
<?php include '../includes/footer.php'; ?>