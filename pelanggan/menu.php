<?php
require_once '../config/koneksi.php';
requirePelanggan();

$judul = "Menu - Serenéa CAFE";
include '../includes/header.php';
include '../includes/navbar.php';

$kategori_filter = $_GET['kategori'] ?? '';
$search = trim($_GET['q'] ?? '');

$where = ["status = 'tersedia'"];
if ($kategori_filter !== '') {
    $kf = mysqli_real_escape_string($koneksi, $kategori_filter);
    $where[] = "kategori = '$kf'";
}
if ($search !== '') {
    $s = mysqli_real_escape_string($koneksi, $search);
    $where[] = "(nama_menu LIKE '%$s%' OR deskripsi LIKE '%$s%')";
}
$whereSql = "WHERE " . implode(" AND ", $where);

$query = mysqli_query($koneksi, "SELECT * FROM menu $whereSql ORDER BY kategori, nama_menu");
$kategori_list = mysqli_query($koneksi, "SELECT DISTINCT kategori FROM menu ORDER BY kategori");
?>

<section class="bg-primary text-cream border-b border-cream/10">
    <div class="max-w-7xl mx-auto px-6 py-12">
        <div class="flex flex-wrap items-end justify-between gap-6">
            <div>
                <p class="text-xs uppercase tracking-widest text-accent mb-2 font-semibold">Menu Hari Ini</p>
                <h1 class="text-3xl md:text-4xl font-bold mb-3 leading-tight max-w-xl">
                    Pilih menu favorit Anda
                </h1>
                <p class="text-cream/70 text-sm max-w-lg leading-relaxed">
                    Racikan kopi pilihan, hidangan hangat, dan minuman segar. Semua disiapkan dengan bahan terbaik.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <div class="bg-cream/5 border border-cream/10 px-4 py-2.5 rounded-lg">
                    <p class="text-[10px] uppercase tracking-widest text-cream/50 mb-0.5">Pelanggan</p>
                    <p class="font-semibold text-sm"><?= htmlspecialchars($_SESSION['pelanggan_nama']); ?></p>
                </div>
                <div class="bg-cream/5 border border-cream/10 px-4 py-2.5 rounded-lg">
                    <p class="text-[10px] uppercase tracking-widest text-cream/50 mb-0.5">Meja</p>
                    <p class="font-semibold text-sm"><?= htmlspecialchars($_SESSION['pelanggan_meja']); ?></p>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="max-w-7xl mx-auto px-6 py-10 w-full">
    <?php if (isset($_SESSION['pesan'])): ?>
        <div class="bg-green-50 border border-green-200 text-green-800 rounded-lg p-3 mb-6 flex items-center gap-2 text-sm" data-auto-dismiss>
            <i data-lucide="check-circle-2" class="w-4 h-4 shrink-0"></i>
            <span><?= htmlspecialchars($_SESSION['pesan']); unset($_SESSION['pesan']); ?></span>
        </div>
    <?php endif; ?>

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <form method="GET" action="<?= PELANGGAN_URL; ?>menu.php" class="relative w-full md:w-80">
            <i data-lucide="search" class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-muted pointer-events-none"></i>
            <input type="text" name="q" value="<?= htmlspecialchars($search); ?>" placeholder="Cari menu..."
                class="w-full pl-10 pr-3 py-2.5 border border-line rounded-lg bg-white text-sm transition-colors">
            <?php if ($kategori_filter !== ''): ?>
                <input type="hidden" name="kategori" value="<?= htmlspecialchars($kategori_filter); ?>">
            <?php endif; ?>
        </form>

        <div class="flex flex-wrap gap-1.5">
            <a href="<?= PELANGGAN_URL; ?>menu.php<?= $search ? '?q='.urlencode($search) : ''; ?>"
               class="px-3 py-1.5 rounded-md text-sm font-medium transition-colors <?= $kategori_filter==='' ? 'bg-primary text-cream' : 'bg-white text-ink border border-line hover:border-secondary'; ?>">
                Semua
            </a>
            <?php mysqli_data_seek($kategori_list, 0); while ($k = mysqli_fetch_assoc($kategori_list)): ?>
                <?php $qs = http_build_query(array_filter(['kategori' => $k['kategori'], 'q' => $search])); ?>
                <a href="<?= PELANGGAN_URL; ?>menu.php?<?= $qs; ?>"
                   class="px-3 py-1.5 rounded-md text-sm font-medium transition-colors <?= $kategori_filter===$k['kategori'] ? 'bg-primary text-cream' : 'bg-white text-ink border border-line hover:border-secondary'; ?>">
                    <?= htmlspecialchars($k['kategori']); ?>
                </a>
            <?php endwhile; ?>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        <?php if (mysqli_num_rows($query) === 0): ?>
            <div class="col-span-full text-center py-20 text-muted">
                <i data-lucide="package-open" class="w-12 h-12 mx-auto mb-3 opacity-40"></i>
                <p class="font-medium">Tidak ada menu yang cocok</p>
                <p class="text-sm mt-1">Coba kata kunci lain atau ubah filter kategori.</p>
            </div>
        <?php endif; ?>
        <?php while ($m = mysqli_fetch_assoc($query)): ?>
        <article class="bg-white rounded-xl overflow-hidden border border-line card-menu flex flex-col">
            <div class="aspect-[4/3] bg-cream overflow-hidden relative">
                <?php if (!empty($m['gambar']) && file_exists('../uploads/'.$m['gambar'])): ?>
                    <img src="<?= UPLOADS_URL . htmlspecialchars($m['gambar']); ?>"
                         alt="<?= htmlspecialchars($m['nama_menu']); ?>"
                         class="w-full h-full object-cover">
                <?php else: ?>
                    <div class="w-full h-full flex items-center justify-center text-muted/40">
                        <i data-lucide="image" class="w-10 h-10"></i>
                    </div>
                <?php endif; ?>
                <span class="absolute top-3 left-3 text-[11px] font-semibold uppercase tracking-wider bg-white text-primary px-2 py-1 rounded shadow-sm">
                    <?= htmlspecialchars($m['kategori']); ?>
                </span>
            </div>
            <div class="p-5 flex-1 flex flex-col">
                <h3 class="font-bold text-base text-ink mb-1 line-clamp-1"><?= htmlspecialchars($m['nama_menu']); ?></h3>
                <p class="text-muted text-sm mb-4 flex-1 line-clamp-2 leading-relaxed"><?= htmlspecialchars($m['deskripsi']); ?></p>

                <div class="flex items-baseline justify-between mb-4">
                    <span class="font-bold text-primary text-lg"><?= rupiah($m['harga']); ?></span>
                    <?php if ((int)$m['stok'] > 0): ?>
                        <span class="text-xs text-muted">Stok <?= (int)$m['stok']; ?></span>
                    <?php else: ?>
                        <span class="text-xs text-red-600 font-semibold">Habis</span>
                    <?php endif; ?>
                </div>

                <form action="<?= PROSES_URL; ?>tambah_keranjang.php" method="POST" class="flex gap-2">
                    <input type="hidden" name="menu_id" value="<?= (int)$m['id']; ?>">
                    <input type="number" name="jumlah" value="1" min="1" max="<?= (int)$m['stok']; ?>"
                        class="w-16 border border-line rounded-lg px-2 py-2 text-sm text-center"
                        <?= (int)$m['stok'] === 0 ? 'disabled' : ''; ?>>
                    <button type="submit" <?= (int)$m['stok'] === 0 ? 'disabled' : ''; ?>
                        class="flex-1 bg-primary text-cream rounded-lg py-2 text-sm font-semibold hover:bg-secondary transition-colors disabled:bg-line disabled:text-muted disabled:cursor-not-allowed flex items-center justify-center gap-1.5">
                        <i data-lucide="plus" class="w-4 h-4"></i>
                        Tambah
                    </button>
                </form>
            </div>
        </article>
        <?php endwhile; ?>
    </div>
</div>
<?php include '../includes/footer.php'; ?>