<?php
require_once '../config/koneksi.php';
requireAdmin();

$judul = "Kelola Menu";

$search = trim($_GET['q'] ?? '');
$kategori_filter = $_GET['kategori'] ?? '';

$where = [];
if ($search !== '') {
    $s = mysqli_real_escape_string($koneksi, $search);
    $where[] = "(nama_menu LIKE '%$s%' OR deskripsi LIKE '%$s%')";
}
if ($kategori_filter !== '') {
    $kf = mysqli_real_escape_string($koneksi, $kategori_filter);
    $where[] = "kategori = '$kf'";
}
$whereSql = $where ? "WHERE " . implode(" AND ", $where) : "";

$perPage = 10;
$page = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page - 1) * $perPage;
$totalRow = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS j FROM menu $whereSql"))['j'];
$totalPage = max(1, ceil($totalRow / $perPage));

$q = mysqli_query($koneksi, "SELECT * FROM menu $whereSql ORDER BY kategori, nama_menu LIMIT $perPage OFFSET $offset");
$kategori_list = mysqli_query($koneksi, "SELECT DISTINCT kategori FROM menu ORDER BY kategori");

$stat_menu = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS j FROM menu"))['j'];
$stat_tersedia = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS j FROM menu WHERE status='tersedia'"))['j'];
$stat_habis = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS j FROM menu WHERE status='habis'"))['j'];

include '../includes/header.php';
?>
<div class="flex min-h-screen">
    <?php include '../includes/sidebar_admin.php'; ?>
    <main class="flex-1 bg-cream p-6 md:p-8">

        <div class="flex flex-wrap justify-between items-center mb-6 gap-3">
            <div>
                <h1 class="text-2xl font-bold text-ink">Kelola Menu</h1>
                <p class="text-muted text-sm mt-1">Tambah, edit, dan hapus menu cafe</p>
            </div>
            <a href="<?= ADMIN_URL; ?>menu_tambah.php"
               class="inline-flex items-center gap-2 bg-primary text-cream px-4 py-2.5 rounded-lg hover:bg-secondary transition-colors text-sm font-semibold">
                <i data-lucide="plus" class="w-4 h-4"></i> Tambah Menu
            </a>
        </div>

        <?php if (isset($_SESSION['pesan'])): ?>
            <div class="bg-green-50 border border-green-200 text-green-800 rounded-lg p-3 mb-5 flex items-center gap-2 text-sm" data-auto-dismiss>
                <i data-lucide="check-circle-2" class="w-4 h-4 shrink-0"></i>
                <span><?= htmlspecialchars($_SESSION['pesan']); unset($_SESSION['pesan']); ?></span>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-5">
            <div class="bg-white border border-line rounded-xl p-4 flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-cream flex items-center justify-center">
                    <i data-lucide="coffee" class="w-5 h-5 text-secondary"></i>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-widest text-muted">Total Menu</p>
                    <p class="text-xl font-bold text-ink"><?= (int)$stat_menu; ?></p>
                </div>
            </div>
            <div class="bg-white border border-line rounded-xl p-4 flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-green-50 flex items-center justify-center">
                    <i data-lucide="check-circle-2" class="w-5 h-5 text-green-600"></i>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-widest text-muted">Tersedia</p>
                    <p class="text-xl font-bold text-ink"><?= (int)$stat_tersedia; ?></p>
                </div>
            </div>
            <div class="bg-white border border-line rounded-xl p-4 flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-red-50 flex items-center justify-center">
                    <i data-lucide="x-circle" class="w-5 h-5 text-red-600"></i>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-widest text-muted">Habis</p>
                    <p class="text-xl font-bold text-ink"><?= (int)$stat_habis; ?></p>
                </div>
            </div>
        </div>

        <form method="GET" action="<?= ADMIN_URL; ?>menu.php" class="bg-white border border-line rounded-xl p-4 mb-4 flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-semibold mb-1 text-muted uppercase tracking-wider">Cari Menu</label>
                <input type="text" name="q" value="<?= htmlspecialchars($search); ?>" placeholder="Nama atau deskripsi..."
                    class="w-full border border-line rounded-lg px-3 py-2 text-sm">
            </div>
            <div class="min-w-[150px]">
                <label class="block text-xs font-semibold mb-1 text-muted uppercase tracking-wider">Kategori</label>
                <select name="kategori" class="w-full border border-line rounded-lg px-3 py-2 text-sm bg-white">
                    <option value="">Semua</option>
                    <?php mysqli_data_seek($kategori_list, 0); while ($k = mysqli_fetch_assoc($kategori_list)): ?>
                        <option value="<?= htmlspecialchars($k['kategori']); ?>" <?= $kategori_filter === $k['kategori'] ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($k['kategori']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <button type="submit" class="bg-primary text-cream px-4 py-2 rounded-lg text-sm font-semibold hover:bg-secondary transition-colors inline-flex items-center gap-2">
                <i data-lucide="search" class="w-4 h-4"></i> Cari
            </button>
            <a href="<?= ADMIN_URL; ?>menu.php" class="bg-white border border-line px-4 py-2 rounded-lg text-sm font-medium hover:border-secondary transition-colors text-ink">
                Reset
            </a>
        </form>

        <div class="bg-white border border-line rounded-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-cream border-b border-line">
                        <tr>
                            <th class="text-left px-5 py-3 text-xs uppercase tracking-wider text-muted font-semibold">Gambar</th>
                            <th class="text-left px-5 py-3 text-xs uppercase tracking-wider text-muted font-semibold">Nama</th>
                            <th class="text-left px-5 py-3 text-xs uppercase tracking-wider text-muted font-semibold">Kategori</th>
                            <th class="text-right px-5 py-3 text-xs uppercase tracking-wider text-muted font-semibold">Harga</th>
                            <th class="text-center px-5 py-3 text-xs uppercase tracking-wider text-muted font-semibold">Stok</th>
                            <th class="text-center px-5 py-3 text-xs uppercase tracking-wider text-muted font-semibold">Status</th>
                            <th class="text-center px-5 py-3 text-xs uppercase tracking-wider text-muted font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        <?php if (mysqli_num_rows($q) === 0): ?>
                            <tr><td colspan="7" class="px-5 py-10 text-center text-muted">Tidak ada menu ditemukan.</td></tr>
                        <?php endif; ?>
                        <?php while ($m = mysqli_fetch_assoc($q)): ?>
                        <tr class="hover:bg-cream/50 transition-colors">
                            <td class="px-5 py-3">
                                <?php if (!empty($m['gambar']) && file_exists('../uploads/'.$m['gambar'])): ?>
                                    <img src="<?= UPLOADS_URL . htmlspecialchars($m['gambar']); ?>" alt=""
                                         class="w-14 h-14 object-cover rounded-lg border border-line">
                                <?php else: ?>
                                    <div class="w-14 h-14 rounded-lg bg-cream flex items-center justify-center text-muted/50 border border-line">
                                        <i data-lucide="image" class="w-5 h-5"></i>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td class="px-5 py-3">
                                <p class="font-medium text-ink"><?= htmlspecialchars($m['nama_menu']); ?></p>
                                <p class="text-xs text-muted line-clamp-1 max-w-[220px] mt-0.5"><?= htmlspecialchars($m['deskripsi']); ?></p>
                            </td>
                            <td class="px-5 py-3">
                                <span class="text-xs bg-cream text-primary border border-line px-2 py-1 rounded font-medium">
                                    <?= htmlspecialchars($m['kategori']); ?>
                                </span>
                            </td>
                            <td class="px-5 py-3 text-right font-medium text-ink"><?= rupiah($m['harga']); ?></td>
                            <td class="px-5 py-3 text-center text-ink"><?= (int)$m['stok']; ?></td>
                            <td class="px-5 py-3 text-center">
                                <?php if ($m['status'] === 'tersedia'): ?>
                                    <span class="text-xs bg-green-50 text-green-700 px-2 py-0.5 rounded font-medium">Tersedia</span>
                                <?php else: ?>
                                    <span class="text-xs bg-red-50 text-red-700 px-2 py-0.5 rounded font-medium">Habis</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-5 py-3">
                                <div class="flex items-center justify-center gap-1">
                                    <a href="<?= ADMIN_URL; ?>menu_edit.php?id=<?= (int)$m['id']; ?>"
                                       class="w-8 h-8 rounded-lg flex items-center justify-center text-secondary hover:bg-cream transition-colors" title="Edit">
                                        <i data-lucide="pencil" class="w-4 h-4"></i>
                                    </a>
                                    <a href="<?= ADMIN_URL; ?>menu_hapus.php?id=<?= (int)$m['id']; ?>"
                                       onclick="return confirm('Hapus menu <?= htmlspecialchars($m['nama_menu']); ?>?')"
                                       class="w-8 h-8 rounded-lg flex items-center justify-center text-red-500 hover:bg-red-50 transition-colors" title="Hapus">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <?php if ($totalPage > 1): ?>
        <nav class="flex items-center justify-between mt-4 text-sm">
            <p class="text-muted">Halaman <?= $page; ?> dari <?= $totalPage; ?> (<?= (int)$totalRow; ?> menu)</p>
            <div class="flex gap-1">
                <?php for ($i = 1; $i <= $totalPage; $i++): ?>
                    <?php $qs = http_build_query(array_merge($_GET, ['page' => $i])); ?>
                    <a href="<?= ADMIN_URL; ?>menu.php?<?= $qs; ?>"
                       class="w-8 h-8 flex items-center justify-center rounded-lg text-sm font-medium transition-colors <?= $i === $page ? 'bg-primary text-cream' : 'bg-white border border-line text-ink hover:border-secondary'; ?>">
                        <?= $i; ?>
                    </a>
                <?php endfor; ?>
            </div>
        </nav>
        <?php endif; ?>
    </main>
</div>
<?php include '../includes/footer.php'; ?>