<?php
require_once '../config/koneksi.php';
if (isset($_SESSION['admin_id'])) {
    redirect('admin/dashboard.php');
}
$judul = "Login Admin - Serenéa CAFE";
include '../includes/header.php';
?>
<div class="min-h-screen grid lg:grid-cols-2">

    <div class="hidden lg:flex bg-primary text-cream p-12 flex-col justify-between relative overflow-hidden">
        <div class="absolute inset-0 opacity-[0.03]" style="background-image: repeating-linear-gradient(45deg, #f9f8f3 0, #f9f8f3 1px, transparent 1px, transparent 24px);"></div>
        <div class="relative">
            <img src="<?= BASE_URL; ?>assets/logo-brand.png" alt="Serenéa CAFE"
                 class="h-14 w-auto brightness-0 invert mb-8">
            <h1 class="text-4xl font-bold leading-tight mb-4">
                Panel<br>
                <span class="text-accent">Administrator</span>
            </h1>
            <p class="text-cream/70 max-w-md leading-relaxed">
                Kelola menu, pantau order, dan lihat laporan penjualan cafe Anda dari satu tempat.
            </p>
        </div>
        <div class="relative">
            <div class="flex items-start gap-3 p-4 bg-cream/5 rounded-lg border border-cream/10">
                <i data-lucide="shield-check" class="w-5 h-5 text-accent shrink-0 mt-0.5"></i>
                <p class="text-xs text-cream/70 leading-relaxed">
                    Akses terbatas untuk administrator. Pastikan Anda menjaga kerahasiaan kredensial login.
                </p>
            </div>
        </div>
    </div>

    <div class="flex items-center justify-center p-6 sm:p-12 bg-cream">
        <div class="w-full max-w-sm">
            <div class="lg:hidden text-center mb-8">
                <img src="<?= BASE_URL; ?>assets/logo-brand.png" alt="Serenéa CAFE" class="h-12 w-auto mx-auto">
            </div>

            <div class="mb-8">
                <h2 class="text-2xl font-bold text-ink mb-1">Masuk ke Panel</h2>
                <p class="text-muted text-sm">Gunakan kredensial administrator Anda.</p>
            </div>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg p-3 mb-6 flex items-start gap-2">
                    <i data-lucide="alert-circle" class="w-4 h-4 mt-0.5 shrink-0"></i>
                    <span><?= $_SESSION['error']; unset($_SESSION['error']); ?></span>
                </div>
            <?php endif; ?>

            <form action="<?= ADMIN_URL; ?>proses_login.php" method="POST" class="space-y-5">
                <div>
                    <label class="block text-sm font-semibold mb-1.5 text-ink">Username</label>
                    <div class="relative">
                        <i data-lucide="user" class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-muted pointer-events-none"></i>
                        <input type="text" name="username" required autofocus
                            class="w-full pl-10 pr-3 py-3 border border-line rounded-lg bg-white text-sm transition-colors">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1.5 text-ink">Password</label>
                    <div class="relative">
                        <i data-lucide="lock" class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-muted pointer-events-none"></i>
                        <input type="password" name="password" required
                            class="w-full pl-10 pr-3 py-3 border border-line rounded-lg bg-white text-sm transition-colors">
                    </div>
                </div>
                <button type="submit"
                    class="w-full bg-primary text-cream font-semibold py-3 rounded-lg hover:bg-secondary transition-colors flex items-center justify-center gap-2">
                    Masuk
                    <i data-lucide="log-in" class="w-4 h-4"></i>
                </button>
            </form>

            <div class="mt-8 pt-6 border-t border-line text-center">
                <a href="<?= BASE_URL; ?>index.php" class="text-xs text-muted hover:text-primary transition-colors inline-flex items-center gap-1">
                    <i data-lucide="arrow-left" class="w-3 h-3"></i>
                    Kembali ke halaman pelanggan
                </a>
            </div>
        </div>
    </div>
</div>
<?php include '../includes/footer.php'; ?>