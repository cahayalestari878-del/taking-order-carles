<?php
if (!defined('BASE_URL')) {
    require_once __DIR__ . '/../config/koneksi.php';
}
?>
<nav class="bg-primary text-cream border-b border-cream/10">
    <div class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">
        <a href="<?= PELANGGAN_URL; ?>menu.php" class="flex items-center gap-3 shrink-0 hover:opacity-90 transition-opacity">
            <img src="<?= BASE_URL; ?>assets/logo-brand.png" alt="Serenéa CAFE"
                 class="h-9 w-auto brightness-0 invert">
            <span class="font-bold tracking-wide text-base hidden sm:inline">Serenéa CAFE</span>
        </a>
        <div class="flex items-center gap-3">
            <?php if (isset($_SESSION['pelanggan_id'])): ?>
                <span class="hidden md:flex items-center gap-1.5 text-sm text-cream/80">
                    <i data-lucide="user" class="w-4 h-4"></i>
                    <span class="font-medium"><?= htmlspecialchars($_SESSION['pelanggan_nama']); ?></span>
                    <span class="text-cream/30">/</span>
                    <span>Meja <?= htmlspecialchars($_SESSION['pelanggan_meja']); ?></span>
                </span>
                <a href="<?= PELANGGAN_URL; ?>keranjang.php"
                   class="relative inline-flex items-center gap-2 bg-accent text-ink font-semibold px-4 py-2 rounded-lg hover:bg-cream transition-colors text-sm">
                    <i data-lucide="shopping-cart" class="w-4 h-4"></i>
                    <span class="hidden sm:inline">Keranjang</span>
                    <span class="absolute -top-1.5 -right-1.5 bg-primary text-cream text-[10px] font-bold rounded-full min-w-[18px] h-[18px] flex items-center justify-center px-1 border border-cream">
                        <?= isset($_SESSION['keranjang']) ? count($_SESSION['keranjang']) : 0; ?>
                    </span>
                </a>
                <a href="<?= PELANGGAN_URL; ?>logout_pelanggan.php"
                   class="inline-flex items-center gap-1.5 border border-cream/25 px-3 py-2 rounded-lg text-sm hover:bg-cream/10 transition-colors">
                    <i data-lucide="log-out" class="w-4 h-4"></i>
                    <span class="hidden sm:inline">Selesai</span>
                </a>
            <?php endif; ?>
        </div>
    </div>
</nav>