<?php
if (!defined('BASE_URL')) {
    require_once __DIR__ . '/../config/koneksi.php';
}
?>
<footer class="bg-primary text-cream mt-auto">
    <div class="max-w-7xl mx-auto px-6 py-10">
        <div class="grid md:grid-cols-3 gap-8">
            <div>
                <img src="<?= BASE_URL; ?>assets/logo-brand.png" alt="Serenéa CAFE"
                     class="h-12 w-auto mb-3 brightness-0 invert opacity-90">
                <p class="text-sm text-cream/70 max-w-xs leading-relaxed">
                    Kopi pilihan, suasana hangat, dan pelayanan yang tenang. Rasakan pengalaman menikmati kopi yang berbeda.
                </p>
            </div>
            <div>
                <h3 class="font-semibold mb-3 text-sm uppercase tracking-wider text-cream/90">Kunjungi</h3>
                <p class="text-sm text-cream/70 leading-relaxed">
                    Jl. Contoh Alamat No. 123<br>
                    Kota Anda, 12345<br>
                    Telp: 021-1234567
                </p>
            </div>
            <div>
                <h3 class="font-semibold mb-3 text-sm uppercase tracking-wider text-cream/90">Jam Buka</h3>
                <p class="text-sm text-cream/70 leading-relaxed">
                    Senin – Jumat: 08.00 – 22.00<br>
                    Sabtu – Minggu: 09.00 – 23.00
                </p>
            </div>
        </div>
        <div class="mt-8 pt-6 border-t border-cream/10 flex flex-col md:flex-row items-center justify-between gap-3">
            <p class="text-xs text-cream/60">&copy; <?= date('Y'); ?> Serenéa CAFE. Seluruh hak cipta dilindungi.</p>
            <p class="text-xs text-cream/60">Sistem Pemesanan Berbasis Web</p>
        </div>
    </div>
</footer>
<script>lucide.createIcons();</script>
</body>
</html>