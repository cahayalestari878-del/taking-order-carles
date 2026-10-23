<?php
require_once '../config/koneksi.php';
requireAdmin();

$id = (int)($_GET['id'] ?? 0);
$q = mysqli_query($koneksi, "SELECT * FROM menu WHERE id = $id");
$m = mysqli_fetch_assoc($q);
if (!$m) { redirect('admin/menu.php'); }

$judul = "Edit Menu";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama_menu']);
    $kat = mysqli_real_escape_string($koneksi, $_POST['kategori']);
    $harga = (float)$_POST['harga'];
    $desc = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    $stok = (int)$_POST['stok'];
    $status = $_POST['status'];
    $gambar = $m['gambar'];

    if (!empty($_FILES['gambar']['name'])) {
        [$ok, $result] = uploadGambar($_FILES['gambar']);
        if (!$ok) {
            $_SESSION['error'] = $result;
            redirect("admin/menu_edit.php?id=$id");
        }
        if ($gambar && file_exists('../uploads/' . $gambar)) unlink('../uploads/' . $gambar);
        $gambar = $result;
    }
    $gambarSql = $gambar ? "'$gambar'" : "NULL";

    $sql = "UPDATE menu SET nama_menu='$nama', kategori='$kat', harga=$harga,
            deskripsi='$desc', stok=$stok, status='$status', gambar=$gambarSql WHERE id=$id";
    if (mysqli_query($koneksi, $sql)) {
        $_SESSION['pesan'] = "Menu berhasil diperbarui.";
        redirect('admin/menu.php');
    }
}
include '../includes/header.php';
?>
<div class="flex min-h-screen">
    <?php include '../includes/sidebar_admin.php'; ?>
    <main class="flex-1 bg-cream p-6 md:p-8">
        <div class="mb-6">
            <a href="<?= ADMIN_URL; ?>menu.php" class="inline-flex items-center gap-1.5 text-xs text-muted hover:text-primary transition-colors mb-2">
                <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i> Kembali ke Kelola Menu
            </a>
            <h1 class="text-2xl font-bold text-ink">Edit Menu</h1>
        </div>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg p-3 mb-4 flex items-start gap-2">
                <i data-lucide="alert-circle" class="w-4 h-4 mt-0.5 shrink-0"></i>
                <span><?= $_SESSION['error']; unset($_SESSION['error']); ?></span>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="bg-white border border-line rounded-xl p-6 max-w-3xl">
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold mb-1.5 text-ink">Nama Menu</label>
                    <input type="text" name="nama_menu" required value="<?= htmlspecialchars($m['nama_menu']); ?>"
                        class="w-full border border-line rounded-lg px-3 py-2.5 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1.5 text-ink">Kategori</label>
                    <input type="text" name="kategori" required value="<?= htmlspecialchars($m['kategori']); ?>"
                        class="w-full border border-line rounded-lg px-3 py-2.5 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1.5 text-ink">Harga</label>
                    <input type="number" name="harga" required min="0" step="100" value="<?= (float)$m['harga']; ?>"
                        class="w-full border border-line rounded-lg px-3 py-2.5 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1.5 text-ink">Stok</label>
                    <input type="number" name="stok" required min="0" value="<?= (int)$m['stok']; ?>"
                        class="w-full border border-line rounded-lg px-3 py-2.5 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1.5 text-ink">Status</label>
                    <select name="status" class="w-full border border-line rounded-lg px-3 py-2.5 text-sm bg-white">
                        <option value="tersedia" <?= $m['status']==='tersedia'?'selected':''; ?>>Tersedia</option>
                        <option value="habis" <?= $m['status']==='habis'?'selected':''; ?>>Habis</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1.5 text-ink">Gambar Produk</label>
                    <?php if (!empty($m['gambar']) && file_exists('../uploads/'.$m['gambar'])): ?>
                        <img src="<?= UPLOADS_URL . htmlspecialchars($m['gambar']); ?>" alt=""
                             class="w-24 h-24 object-cover rounded-lg mb-2 border border-line">
                    <?php endif; ?>
                    <input type="file" name="gambar" accept="image/jpeg,image/png,image/webp"
                        class="w-full border border-line rounded-lg px-3 py-2 text-sm file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:bg-primary file:text-cream file:text-xs file:font-semibold">
                    <p class="text-xs text-muted mt-1">Biarkan kosong jika tidak ingin mengganti</p>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold mb-1.5 text-ink">Deskripsi</label>
                    <textarea name="deskripsi" rows="3" class="w-full border border-line rounded-lg px-3 py-2.5 text-sm"><?= htmlspecialchars($m['deskripsi']); ?></textarea>
                </div>
            </div>
            <div class="flex gap-3 mt-6">
                <button type="submit" class="bg-primary text-cream font-semibold px-5 py-2.5 rounded-lg hover:bg-secondary transition-colors text-sm inline-flex items-center gap-2">
                    <i data-lucide="save" class="w-4 h-4"></i> Update
                </button>
                <a href="<?= ADMIN_URL; ?>menu.php" class="bg-white border border-line px-5 py-2.5 rounded-lg hover:border-secondary transition-colors text-sm font-medium text-ink">
                    Batal
                </a>
            </div>
        </form>
    </main>
</div>
<?php include '../includes/footer.php'; ?>