<?php
require_once '../config/koneksi.php';
requireAdmin();

$id = (int)($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $st = mysqli_real_escape_string($koneksi, $_POST['status']);
    mysqli_query($koneksi, "UPDATE orders SET status='$st' WHERE id=$id");
    $_SESSION['pesan'] = "Status order berhasil diperbarui.";
    redirect("admin/order_detail.php?id=$id");
}

$judul = "Detail Order";
include '../includes/header.php';

$q = mysqli_query($koneksi, "SELECT o.*, p.nama, p.no_hp FROM orders o
                             JOIN pelanggan p ON o.pelanggan_id = p.id WHERE o.id = $id");
$o = mysqli_fetch_assoc($q);
if (!$o) { redirect('admin/order.php'); }
$qd = mysqli_query($koneksi, "SELECT * FROM order_detail WHERE order_id = $id");
?>
<div class="flex min-h-screen">
    <?php include '../includes/sidebar_admin.php'; ?>
    <main class="flex-1 bg-cream p-6 md:p-8">
        <a href="<?= ADMIN_URL; ?>order.php" class="inline-flex items-center gap-1.5 text-xs text-muted hover:text-primary transition-colors mb-2">
            <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i> Kembali ke Data Order
        </a>
        <h1 class="text-2xl font-bold text-ink mb-6">Detail Order <span class="font-mono text-base text-secondary"><?= htmlspecialchars($o['kode_order']); ?></span></h1>

        <?php if (isset($_SESSION['pesan'])): ?>
            <div class="bg-green-50 border border-green-200 text-green-800 rounded-lg p-3 mb-4 flex items-center gap-2 text-sm" data-auto-dismiss>
                <i data-lucide="check-circle-2" class="w-4 h-4 shrink-0"></i>
                <span><?= $_SESSION['pesan']; unset($_SESSION['pesan']); ?></span>
            </div>
        <?php endif; ?>

        <div class="grid md:grid-cols-5 gap-5">
            <div class="md:col-span-2 space-y-4">
                <div class="bg-white border border-line rounded-xl p-5">
                    <h2 class="font-semibold text-sm text-ink mb-3 flex items-center gap-2"><i data-lucide="user" class="w-4 h-4 text-secondary"></i> Informasi Pelanggan</h2>
                    <dl class="space-y-2 text-sm">
                        <div class="flex justify-between gap-3"><dt class="text-muted">Nama</dt><dd class="font-medium text-ink text-right"><?= htmlspecialchars($o['nama']); ?></dd></div>
                        <div class="flex justify-between gap-3"><dt class="text-muted">No. HP</dt><dd class="text-ink text-right"><?= htmlspecialchars($o['no_hp'] ?: '-'); ?></dd></div>
                        <div class="flex justify-between gap-3"><dt class="text-muted">No. Meja</dt><dd class="text-ink text-right"><?= htmlspecialchars($o['no_meja']); ?></dd></div>
                        <div class="flex justify-between gap-3"><dt class="text-muted">Waktu</dt><dd class="text-ink text-right"><?= date('d/m/Y H:i', strtotime($o['waktu_order'])); ?></dd></div>
                        <div class="flex justify-between gap-3"><dt class="text-muted">Catatan</dt><dd class="text-ink text-right max-w-[60%]"><?= htmlspecialchars($o['catatan'] ?: '-'); ?></dd></div>
                    </dl>
                </div>
                <div class="bg-white border border-line rounded-xl p-5">
                    <h2 class="font-semibold text-sm text-ink mb-3 flex items-center gap-2"><i data-lucide="refresh-cw" class="w-4 h-4 text-secondary"></i> Update Status</h2>
                    <form method="POST">
                        <select name="status" class="w-full border border-line rounded-lg px-3 py-2.5 text-sm mb-3 bg-white">
                            <?php foreach (['pending','diproses','selesai','dibatalkan'] as $st): ?>
                                <option value="<?= $st; ?>" <?= $o['status']===$st?'selected':''; ?>><?= ucfirst($st); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button class="w-full bg-primary text-cream font-semibold py-2.5 rounded-lg hover:bg-secondary transition-colors text-sm flex items-center justify-center gap-2">
                            <i data-lucide="save" class="w-4 h-4"></i> Update Status
                        </button>
                    </form>
                </div>
            </div>
            <div class="md:col-span-3">
                <div class="bg-white border border-line rounded-xl overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-line font-semibold text-sm text-ink flex items-center gap-2">
                        <i data-lucide="receipt" class="w-4 h-4 text-secondary"></i> Rincian Item
                    </div>
                    <table class="w-full text-sm">
                        <thead class="bg-cream border-b border-line">
                            <tr>
                                <th class="text-left px-5 py-2.5 text-xs uppercase tracking-wider text-muted font-semibold">Menu</th>
                                <th class="text-right px-5 py-2.5 text-xs uppercase tracking-wider text-muted font-semibold">Harga</th>
                                <th class="text-center px-5 py-2.5 text-xs uppercase tracking-wider text-muted font-semibold">Qty</th>
                                <th class="text-right px-5 py-2.5 text-xs uppercase tracking-wider text-muted font-semibold">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-line">
                            <?php while ($d = mysqli_fetch_assoc($qd)): ?>
                            <tr>
                                <td class="px-5 py-2.5 text-ink"><?= htmlspecialchars($d['nama_menu']); ?></td>
                                <td class="px-5 py-2.5 text-right text-ink"><?= rupiah($d['harga']); ?></td>
                                <td class="px-5 py-2.5 text-center text-ink"><?= (int)$d['jumlah']; ?></td>
                                <td class="px-5 py-2.5 text-right text-ink"><?= rupiah($d['subtotal']); ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                        <tfoot class="bg-cream border-t border-line">
                            <tr>
                                <th colspan="3" class="px-5 py-3 text-right font-semibold text-ink">Total</th>
                                <th class="px-5 py-3 text-right text-primary text-base font-bold"><?= rupiah($o['total_harga']); ?></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </main>
</div>
<?php include '../includes/footer.php'; ?>