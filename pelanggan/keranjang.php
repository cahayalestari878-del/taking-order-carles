<?php
require_once '../config/koneksi.php';
requirePelanggan();

$judul = "Keranjang - Serenéa CAFE";
include '../includes/header.php';
include '../includes/navbar.php';

$keranjang = $_SESSION['keranjang'] ?? [];
$total = 0;
?>
<div class="max-w-5xl mx-auto px-6 py-8 w-full">
    <h1 class="text-2xl font-bold text-ink mb-6">Keranjang Pesanan</h1>

    <?php if (empty($keranjang)): ?>
        <div class="bg-white border border-line rounded-xl p-12 text-center">
            <i data-lucide="shopping-bag" class="w-16 h-16 text-muted/40 mx-auto mb-3"></i>
            <p class="text-muted mb-5">Keranjang masih kosong.</p>
            <a href="<?= PELANGGAN_URL; ?>menu.php"
               class="inline-flex items-center gap-2 bg-primary text-cream px-5 py-2.5 rounded-lg hover:bg-secondary transition-colors text-sm font-semibold">
                <i data-lucide="utensils" class="w-4 h-4"></i> Lihat Menu
            </a>
        </div>
    <?php else: ?>
        <div class="bg-white border border-line rounded-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-cream border-b border-line">
                        <tr>
                            <th class="text-left px-5 py-3 text-xs uppercase tracking-wider text-muted font-semibold">Menu</th>
                            <th class="text-right px-5 py-3 text-xs uppercase tracking-wider text-muted font-semibold">Harga</th>
                            <th class="text-center px-5 py-3 text-xs uppercase tracking-wider text-muted font-semibold">Jumlah</th>
                            <th class="text-right px-5 py-3 text-xs uppercase tracking-wider text-muted font-semibold">Subtotal</th>
                            <th class="text-center px-5 py-3 text-xs uppercase tracking-wider text-muted font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        <?php foreach ($keranjang as $mid => $jml):
                            $mid = (int)$mid;
                            $q = mysqli_query($koneksi, "SELECT * FROM menu WHERE id = $mid");
                            $m = mysqli_fetch_assoc($q);
                            if (!$m) continue;
                            $sub = $m['harga'] * $jml;
                            $total += $sub;
                        ?>
                        <tr>
                            <td class="px-5 py-4 font-medium text-ink"><?= htmlspecialchars($m['nama_menu']); ?></td>
                            <td class="px-5 py-4 text-right text-ink"><?= rupiah($m['harga']); ?></td>
                            <td class="px-5 py-4">
                                <form action="<?= PROSES_URL; ?>update_keranjang.php" method="POST" class="flex items-center gap-1 justify-center">
                                    <input type="hidden" name="menu_id" value="<?= $mid; ?>">
                                    <input type="number" name="jumlah" value="<?= $jml; ?>" min="1" max="<?= $m['stok']; ?>"
                                        class="w-16 border border-line rounded-lg px-2 py-1.5 text-center text-sm">
                                    <button type="submit" class="w-8 h-8 flex items-center justify-center rounded-lg text-secondary hover:bg-cream transition-colors" title="Update">
                                        <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            </td>
                            <td class="px-5 py-4 text-right font-semibold text-ink"><?= rupiah($sub); ?></td>
                            <td class="px-5 py-4 text-center">
                                <a href="<?= PROSES_URL; ?>hapus_keranjang.php?id=<?= $mid; ?>"
                                   class="inline-flex w-8 h-8 items-center justify-center rounded-lg text-red-500 hover:bg-red-50 transition-colors" title="Hapus">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot class="bg-cream border-t border-line">
                        <tr>
                            <td colspan="3" class="px-5 py-4 text-right font-semibold text-ink">Total</td>
                            <td class="px-5 py-4 text-right font-bold text-primary text-lg"><?= rupiah($total); ?></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="flex justify-between mt-6">
            <a href="<?= PELANGGAN_URL; ?>menu.php"
               class="inline-flex items-center gap-2 bg-white border border-line px-4 py-2.5 rounded-lg hover:border-secondary transition-colors text-sm font-medium text-ink">
                <i data-lucide="arrow-left" class="w-4 h-4"></i> Lanjut Pesan
            </a>
            <a href="<?= PELANGGAN_URL; ?>checkout.php"
               class="inline-flex items-center gap-2 bg-primary text-cream font-semibold px-5 py-2.5 rounded-lg hover:bg-secondary transition-colors text-sm">
                <i data-lucide="check-circle" class="w-4 h-4"></i> Checkout
            </a>
        </div>
    <?php endif; ?>
</div>
<?php include '../includes/footer.php'; ?>