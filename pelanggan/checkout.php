<?php
require_once '../config/koneksi.php';
requirePelanggan();

$keranjang = $_SESSION['keranjang'] ?? [];
if (empty($keranjang)) { redirect('pelanggan/keranjang.php'); }

$total = 0;
foreach ($keranjang as $mid => $jml) {
    $mid = (int)$mid;
    $q = mysqli_query($koneksi, "SELECT harga FROM menu WHERE id = $mid");
    $m = mysqli_fetch_assoc($q);
    if ($m) $total += $m['harga'] * $jml;
}
$judul = "Checkout - Serenéa CAFE";
include '../includes/header.php';
include '../includes/navbar.php';
?>
<div class="max-w-5xl mx-auto px-6 py-8 w-full">
    <h1 class="text-2xl font-bold text-ink mb-6">Konfirmasi Pesanan</h1>
    <div class="grid md:grid-cols-5 gap-6">
        <div class="md:col-span-3 space-y-4">
            <div class="bg-white border border-line rounded-xl p-5">
                <h2 class="font-semibold text-sm text-ink flex items-center gap-2 mb-3">
                    <i data-lucide="user" class="w-4 h-4 text-secondary"></i> Data Pelanggan
                </h2>
                <div class="space-y-1.5 text-sm">
                    <p><span class="text-muted">Nama:</span> <span class="font-medium text-ink"><?= htmlspecialchars($_SESSION['pelanggan_nama']); ?></span></p>
                    <p><span class="text-muted">No. Meja:</span> <span class="font-medium text-ink"><?= htmlspecialchars($_SESSION['pelanggan_meja']); ?></span></p>
                </div>
            </div>
            <form action="<?= PROSES_URL; ?>simpan_order.php" method="POST">
                <div class="bg-white border border-line rounded-xl p-5 mb-4">
                    <h2 class="font-semibold text-sm text-ink flex items-center gap-2 mb-3">
                        <i data-lucide="message-square" class="w-4 h-4 text-secondary"></i> Catatan Tambahan
                    </h2>
                    <textarea name="catatan" rows="3" placeholder="Contoh: tanpa gula, extra shot, dll (opsional)"
                        class="w-full border border-line rounded-lg px-3 py-2 text-sm"></textarea>
                </div>
                <button type="submit" class="w-full bg-primary text-cream font-semibold py-3 rounded-lg hover:bg-secondary transition-colors flex items-center justify-center gap-2">
                    <i data-lucide="send" class="w-4 h-4"></i> Kirim Pesanan
                </button>
            </form>
        </div>
        <div class="md:col-span-2">
            <div class="bg-white border border-line rounded-xl p-5">
                <h2 class="font-semibold text-sm text-ink flex items-center gap-2 mb-3">
                    <i data-lucide="receipt" class="w-4 h-4 text-secondary"></i> Rincian Pesanan
                </h2>
                <div class="space-y-2 text-sm">
                    <?php foreach ($keranjang as $mid => $jml):
                        $mid = (int)$mid;
                        $q = mysqli_query($koneksi, "SELECT * FROM menu WHERE id = $mid");
                        $m = mysqli_fetch_assoc($q);
                        if (!$m) continue;
                    ?>
                    <div class="flex justify-between gap-3">
                        <span class="text-ink"><?= htmlspecialchars($m['nama_menu']); ?> <span class="text-muted">× <?= $jml; ?></span></span>
                        <span class="text-ink shrink-0"><?= rupiah($m['harga'] * $jml); ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
                <hr class="my-3 border-line">
                <div class="flex justify-between font-bold text-lg text-primary">
                    <span>Total</span>
                    <span><?= rupiah($total); ?></span>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include '../includes/footer.php'; ?>