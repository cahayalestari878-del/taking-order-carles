<?php
require_once 'config/koneksi.php';
if (isset($_SESSION['pelanggan_id'])) {
    redirect('pelanggan/menu.php');
}
$judul = "Selamat Datang - Serenéa CAFE";
include 'includes/header.php';
?>
<div class="min-h-screen grid lg:grid-cols-2">

    <div class="hidden lg:flex bg-primary text-cream p-12 flex-col justify-between relative overflow-hidden">
        <div class="absolute inset-0 opacity-[0.03]" style="background-image: repeating-linear-gradient(45deg, #f9f8f3 0, #f9f8f3 1px, transparent 1px, transparent 24px);"></div>

        <div class="relative">
            <img src="<?= BASE_URL; ?>assets/logo-brand.png" alt="Serenéa CAFE"
                 class="h-14 w-auto brightness-0 invert mb-8">
            <h1 class="text-5xl font-bold leading-tight mb-4">
                Kopi yang tenang,<br>
                <span class="text-accent">suasana yang hangat.</span>
            </h1>
            <p class="text-cream/70 max-w-md leading-relaxed">
                Selamat datang di Serenéa CAFE. Pesan menu favorit Anda langsung dari meja — tanpa antre, tanpa menunggu.
            </p>
        </div>

        <div class="relative grid grid-cols-3 gap-6 pt-8 border-t border-cream/10">
            <div>
                <div class="text-2xl font-bold">11+</div>
                <div class="text-xs text-cream/60 mt-1">Menu Pilihan</div>
            </div>
            <div>
                <div class="text-2xl font-bold">08.00</div>
                <div class="text-xs text-cream/60 mt-1">Buka Setiap Hari</div>
            </div>
            <div>
                <div class="text-2xl font-bold">A01</div>
                <div class="text-xs text-cream/60 mt-1">Kode Meja Anda</div>
            </div>
        </div>
    </div>

    <div class="flex items-center justify-center p-6 sm:p-12 bg-cream">
        <div class="w-full max-w-md">
            <div class="lg:hidden text-center mb-8">
                <img src="<?= BASE_URL; ?>assets/logo-brand.png" alt="Serenéa CAFE" class="h-12 w-auto mx-auto mb-3">
            </div>

            <div class="mb-8">
                <h2 class="text-2xl font-bold text-ink mb-1">Mulai Pesanan</h2>
                <p class="text-muted text-sm">Isi data diri Anda untuk memulai pemesanan di meja.</p>
            </div>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg p-3 mb-6 flex items-start gap-2" data-auto-dismiss>
                    <i data-lucide="alert-circle" class="w-4 h-4 mt-0.5 shrink-0"></i>
                    <span><?= $_SESSION['error']; unset($_SESSION['error']); ?></span>
                </div>
            <?php endif; ?>

            <form action="<?= PROSES_URL; ?>simpan_pelanggan.php" method="POST" class="space-y-5">
                <div>
                    <label class="block text-sm font-semibold mb-1.5 text-ink">Nama Lengkap</label>
                    <div class="relative">
                        <i data-lucide="user" class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-muted pointer-events-none"></i>
                        <input type="text" name="nama" required placeholder="Masukkan nama Anda"
                            class="w-full pl-10 pr-3 py-3 border border-line rounded-lg bg-white text-sm transition-colors">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1.5 text-ink">Nomor Meja</label>
                    <div class="relative">
                        <i data-lucide="grid-3x3" class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-muted pointer-events-none"></i>
                        <input type="text" name="no_meja" required placeholder="Contoh: A01"
                            class="w-full pl-10 pr-3 py-3 border border-line rounded-lg bg-white text-sm transition-colors">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1.5 text-ink">
                        No. HP <span class="text-muted font-normal">(Opsional)</span>
                    </label>
                    <div class="relative">
                        <i data-lucide="phone" class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-muted pointer-events-none"></i>
                        <input type="text" name="no_hp" placeholder="08xxxxxxxxxx"
                            class="w-full pl-10 pr-3 py-3 border border-line rounded-lg bg-white text-sm transition-colors">
                    </div>
                </div>
                <button type="submit"
                    class="w-full bg-primary text-cream font-semibold py-3 rounded-lg hover:bg-secondary transition-colors flex items-center justify-center gap-2">
                    Mulai Pesan
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </button>
            </form>

            <p class="text-xs text-muted text-center mt-8">
                Dengan melanjutkan, Anda menyetujui ketentuan layanan Serenéa CAFE.
            </p>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>