<?php
require_once '../config/koneksi.php';
requireAdmin();

$judul = "Data Order";
$filter = $_GET['status'] ?? '';
$where = "";
if ($filter !== '') {
    $f = mysqli_real_escape_string($koneksi, $filter);
    $where = "WHERE o.status = '$f'";
}
$q = mysqli_query($koneksi, "SELECT o.*, p.nama FROM orders o
                             JOIN pelanggan p ON o.pelanggan_id = p.id
                             $where ORDER BY o.waktu_order DESC");
include '../includes/header.php';

$statuses = [
    ''           => ['label' => 'Semua',      'class' => 'bg-primary text-cream'],
    'pending'    => ['label' => 'Pending',    'class' => 'bg-amber-600 text-white'],
    'diproses'   => ['label' => 'Diproses',   'class' => 'bg-blue-600 text-white'],
    'selesai'    => ['label' => 'Selesai',    'class' => 'bg-green-600 text-white'],
    'dibatalkan' => ['label' => 'Dibatalkan', 'class' => 'bg-red-600 text-white'],
];
?>
<div class="flex min-h-screen">
    <?php include '../includes/sidebar_admin.php'; ?>
    <main class="flex-1 bg-cream p-6 md:p-8">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-ink">Data Order</h1>
            <p class="text-muted text-sm mt-1">Pantau dan kelola status pesanan pelanggan</p>
        </div>

        <div class="flex flex-wrap gap-2 mb-4">
            <?php foreach ($statuses as $key => $s): ?>
                <a href="<?= ADMIN_URL; ?>order.php<?= $key !== '' ? '?status='.$key : ''; ?>"
                   class="px-3 py-1.5 rounded-lg text-sm font-medium transition-colors <?= $filter === $key ? $s['class'] : 'bg-white border border-line text-ink hover:border-secondary'; ?>">
                    <?= $s['label']; ?>
                </a>
            <?php endforeach; ?>
        </div>

        <div class="bg-white border border-line rounded-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-cream border-b border-line">
                        <tr>
                            <th class="text-left px-5 py-3 text-xs uppercase tracking-wider text-muted font-semibold">Kode</th>
                            <th class="text-left px-5 py-3 text-xs uppercase tracking-wider text-muted font-semibold">Nama</th>
                            <th class="text-center px-5 py-3 text-xs uppercase tracking-wider text-muted font-semibold">Meja</th>
                            <th class="text-right px-5 py-3 text-xs uppercase tracking-wider text-muted font-semibold">Total</th>
                            <th class="text-center px-5 py-3 text-xs uppercase tracking-wider text-muted font-semibold">Status</th>
                            <th class="text-left px-5 py-3 text-xs uppercase tracking-wider text-muted font-semibold">Waktu</th>
                            <th class="text-center px-5 py-3 text-xs uppercase tracking-wider text-muted font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        <?php if (mysqli_num_rows($q) === 0): ?>
                            <tr><td colspan="7" class="px-5 py-10 text-center text-muted">Belum ada order.</td></tr>
                        <?php endif; ?>
                        <?php while ($o = mysqli_fetch_assoc($q)): ?>
                        <tr class="hover:bg-cream/50 transition-colors">
                            <td class="px-5 py-3 font-mono text-xs text-ink"><?= htmlspecialchars($o['kode_order']); ?></td>
                            <td class="px-5 py-3 text-ink"><?= htmlspecialchars($o['nama']); ?></td>
                            <td class="px-5 py-3 text-center text-ink"><?= htmlspecialchars($o['no_meja']); ?></td>
                            <td class="px-5 py-3 text-right font-medium text-ink"><?= rupiah($o['total_harga']); ?></td>
                            <td class="px-5 py-3 text-center">
                                <?php
                                $badge = 'bg-gray-100 text-gray-700';
                                if ($o['status'] === 'pending') $badge = 'bg-amber-50 text-amber-700';
                                elseif ($o['status'] === 'diproses') $badge = 'bg-blue-50 text-blue-700';
                                elseif ($o['status'] === 'selesai') $badge = 'bg-green-50 text-green-700';
                                elseif ($o['status'] === 'dibatalkan') $badge = 'bg-red-50 text-red-700';
                                ?>
                                <span class="text-xs px-2 py-0.5 rounded font-medium <?= $badge; ?>"><?= ucfirst($o['status']); ?></span>
                            </td>
                            <td class="px-5 py-3 text-xs text-muted"><?= date('d/m/Y H:i', strtotime($o['waktu_order'])); ?></td>
                            <td class="px-5 py-3 text-center">
                                <a href="<?= ADMIN_URL; ?>order_detail.php?id=<?= (int)$o['id']; ?>"
                                   class="inline-flex w-8 h-8 items-center justify-center rounded-lg text-secondary hover:bg-cream transition-colors" title="Detail">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>
<?php include '../includes/footer.php'; ?>