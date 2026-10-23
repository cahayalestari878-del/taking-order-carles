<?php
if (!defined('BASE_URL')) {
    require_once __DIR__ . '/../config/koneksi.php';
}

$current = basename($_SERVER['PHP_SELF']);

function navItem($href, $icon, $label, $current) {
    $active = ($current === $href)
        ? 'bg-accent/15 text-cream font-semibold'
        : 'text-cream/70 hover:bg-cream/5 hover:text-cream';
    return "<a href=\"$href\" class=\"flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors $active\">
        <i data-lucide=\"$icon\" class=\"w-4 h-4 shrink-0\"></i> <span>$label</span>
    </a>";
}
?>
<aside data-sidebar class="w-64 bg-primary text-cream min-h-screen hidden md:flex flex-col p-4 gap-1 border-r border-cream/10">

    <a href="<?= ADMIN_URL; ?>dashboard.php" class="flex items-center gap-2.5 px-2 py-4 mb-3 hover:opacity-90 transition-opacity">
        <img src="<?= BASE_URL; ?>assets/logo-brand.png" alt="Serenéa CAFE"
             class="h-8 w-auto brightness-0 invert">
        <div class="leading-tight">
            <div class="font-bold text-sm tracking-wide">Serenéa</div>
            <div class="text-[10px] uppercase tracking-widest text-cream/50">Admin Panel</div>
        </div>
    </a>

    <div class="text-[10px] uppercase tracking-widest text-cream/40 px-3 mb-1">Menu Utama</div>
    <?= navItem(ADMIN_URL . 'dashboard.php', 'layout-dashboard', 'Dashboard', $current); ?>
    <?= navItem(ADMIN_URL . 'menu.php', 'coffee', 'Kelola Menu', $current); ?>
    <?= navItem(ADMIN_URL . 'order.php', 'receipt', 'Data Order', $current); ?>
    <?= navItem(ADMIN_URL . 'laporan.php', 'file-text', 'Laporan', $current); ?>

    <div class="mt-auto pt-4 border-t border-cream/10">
        <div class="flex items-center gap-2 px-2 mb-3">
            <div class="w-8 h-8 rounded-full bg-accent/20 flex items-center justify-center shrink-0">
                <i data-lucide="user" class="w-4 h-4 text-cream"></i>
            </div>
            <div class="min-w-0">
                <div class="text-xs text-cream/50">Login sebagai</div>
                <div class="font-semibold text-sm truncate"><?= htmlspecialchars($_SESSION['admin_nama']); ?></div>
            </div>
        </div>
        <a href="<?= ADMIN_URL; ?>logout.php" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm text-red-200 hover:bg-red-500/15 transition-colors">
            <i data-lucide="log-out" class="w-4 h-4"></i> Logout
        </a>
    </div>
</aside>