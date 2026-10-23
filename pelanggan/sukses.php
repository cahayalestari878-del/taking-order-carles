<?php
require_once '../config/koneksi.php';
if (!isset($_SESSION['pelanggan_id']) || !isset($_SESSION['last_order_id'])) {
    redirect('index.php');
}
$oid = (int)$_SESSION['last_order_id'];

$q_order = mysqli_query($koneksi, "SELECT o.*, p.nama, p.no_hp FROM orders o
                                   JOIN pelanggan p ON o.pelanggan_id = p.id
                                   WHERE o.id = $oid");
$order = mysqli_fetch_assoc($q_order);
if (!$order) { redirect('index.php'); }

$q_detail = mysqli_query($koneksi, "SELECT * FROM order_detail WHERE order_id = $oid");
$detail = [];
while ($d = mysqli_fetch_assoc($q_detail)) $detail[] = $d;

$judul = "Struk Pesanan - Serenéa CAFE";
include '../includes/header.php';
?>
<div class="max-w-md mx-auto px-6 py-8 w-full">
    <div class="text-center mb-4">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-green-50 mb-2">
            <i data-lucide="check-circle-2" class="w-8 h-8 text-green-600"></i>
        </div>
        <h1 class="text-xl font-bold text-ink">Pesanan Berhasil</h1>
        <p class="text-muted text-sm">Silakan tunjukkan struk ini ke kasir atau pelayan</p>
    </div>

    <div class="bg-white border border-line rounded-xl p-6 text-sm">
        <div class="text-center mb-4">
            <h2 class="font-bold text-base tracking-wide text-ink">SERENÉA CAFE</h2>
            <p class="text-muted text-xs">Jl. Contoh Alamat No. 123, Kota Anda</p>
            <p class="text-muted text-xs">Telp: 021-1234567</p>
        </div>
        <hr class="border-dashed border-line my-3">
        <div class="space-y-1">
            <div class="flex justify-between"><span class="text-muted">Kode Order</span><span class="font-mono font-semibold text-ink"><?= htmlspecialchars($order['kode_order']); ?></span></div>
            <div class="flex justify-between"><span class="text-muted">Tanggal</span><span class="text-ink"><?= date('d/m/Y H:i', strtotime($order['waktu_order'])); ?></span></div>
            <div class="flex justify-between"><span class="text-muted">Nama</span><span class="text-ink"><?= htmlspecialchars($order['nama']); ?></span></div>
            <div class="flex justify-between"><span class="text-muted">No. Meja</span><span class="text-ink"><?= htmlspecialchars($order['no_meja']); ?></span></div>
            <?php if (!empty($order['no_hp'])): ?>
            <div class="flex justify-between"><span class="text-muted">No. HP</span><span class="text-ink"><?= htmlspecialchars($order['no_hp']); ?></span></div>
            <?php endif; ?>
        </div>
        <hr class="border-dashed border-line my-3">
        <div class="space-y-2">
            <?php foreach ($detail as $d): ?>
            <div>
                <div class="font-medium text-ink"><?= htmlspecialchars($d['nama_menu']); ?></div>
                <div class="flex justify-between text-muted text-xs">
                    <span><?= (int)$d['jumlah']; ?> × <?= number_format($d['harga'],0,',','.'); ?></span>
                    <span><?= number_format($d['subtotal'],0,',','.'); ?></span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <hr class="border-dashed border-line my-3">
        <div class="flex justify-between font-bold text-primary text-base">
            <span>TOTAL</span>
            <span><?= rupiah($order['total_harga']); ?></span>
        </div>
        <div class="flex justify-between text-sm mt-1"><span class="text-muted">Status</span><span class="text-ink"><?= ucfirst($order['status']); ?></span></div>
        <?php if (!empty($order['catatan'])): ?>
        <hr class="border-dashed border-line my-3">
        <div class="text-xs">
            <div class="font-semibold text-muted">Catatan:</div>
            <div class="text-ink"><?= nl2br(htmlspecialchars($order['catatan'])); ?></div>
        </div>
        <?php endif; ?>
        <hr class="border-dashed border-line my-3">
        <div class="text-center text-xs text-muted">
            <p>Terima kasih atas kunjungan Anda</p>
            <p>Selamat menikmati</p>
        </div>
    </div>

    <div class="flex gap-3 mt-6">
        <a href="<?= PELANGGAN_URL; ?>menu.php" class="flex-1 text-center bg-white border border-line py-2.5 rounded-lg hover:border-secondary transition-colors text-sm font-medium text-ink">Order Lagi</a>
        <a href="<?= PELANGGAN_URL; ?>logout_pelanggan.php" class="flex-1 text-center bg-primary text-cream py-2.5 rounded-lg hover:bg-secondary transition-colors text-sm font-medium">Selesai</a>
    </div>
</div>
<?php include '../includes/footer.php'; ?>