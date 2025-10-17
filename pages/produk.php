<?php
include __DIR__ . '/../config/config.php';
include __DIR__ . '/../includes/head.php';         // head (CSS) dulu
include __DIR__ . '/../includes/navigation.php';   // sidebar
include __DIR__ . '/../includes/topbar.php';       // topbar
?>


<!-- [ Main Content ] start -->
<div class="pcoded-main-container">
    <div class="pcoded-wrapper">
        <div class="pcoded-content">
            <div class="pcoded-inner-content">
                <div class="main-body">
                    <div class="page-wrapper">

                        <!-- page header -->
                        <div class="page-header">
                            <div class="page-block">
                                <div class="row align-items-center">
                                    <div class="col-md-12">
                                        <div class="page-header-title">
                                            <h5>Daftar Produk</h5>
                                        </div>
                                        <ul class="breadcrumb">
                                            <li class="breadcrumb-item"><a href="../index.php"><i class="fas fa-home"></i></a></li>
                                            <li class="breadcrumb-item"><a href="#!">Produk</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- controls -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-body d-flex justify-content-between align-items-center">
                                        <div>
                                            <button class="btn btn-primary" data-toggle="modal" data-target="#modalAddProduct">
                                                <i class="fas fa-plus"></i> Tambah Produk
                                            </button>
                                            <!-- tombol Kategori & Gambar dihilangkan -->
                                        </div>
                                        <div>
                                            <input type="text" id="searchProduct" class="form-control" placeholder="Cari produk..." style="width:260px;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- daftar produk -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card table-card">
                                    <div class="card-header">
                                        <h5>Daftar Produk</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-hover" id="productTable">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Foto</th>
                                                        <th>Nama Produk</th>
                                                        <th>Kategori</th>
                                                        <th>Stok</th>
                                                        <th>Harga</th>
                                                        <th>Deskripsi</th>
                                                        <th>Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
<?php
// ambil data produk dari DB dengan pagination
$db = get_db();
$perPage = 10; // ubah sesuai kebutuhan
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $perPage;

// total record untuk menghitung jumlah halaman
$totalStmt = $db->query('SELECT COUNT(*) FROM produk');
$total = (int) $totalStmt->fetchColumn();
$pages = $total > 0 ? (int) ceil($total / $perPage) : 1;

// query terpaginated
$stmt = $db->prepare('SELECT * FROM produk ORDER BY created_at DESC LIMIT :limit OFFSET :offset');
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();

$no = $offset + 1;
while ($p = $stmt->fetch()) {
    $img = $p['foto'] ? $p['foto'] : ASSET . '/images/widget/img-round1.jpg';
    $hargaFmt = 'Rp ' . number_format($p['harga'], 0, ',', '.');
    echo "<tr>
        <td>{$no}</td>
        <td><img src=\"{$img}\" style=\"width:60px;height:40px;object-fit:cover;border-radius:4px;\" alt=\"\"></td>
        <td>" . htmlspecialchars($p['nama']) . "</td>
        <td>" . htmlspecialchars($p['kategori']) . "</td>
        <td>" . intval($p['stok']) . "</td>
        <td>{$hargaFmt}</td>
        <td>" . htmlspecialchars($p['deskripsi']) . "</td>
        <td>
            <button class=\"btn btn-sm btn-warning btn-edit\"
                data-id=\"" . intval($p['id']) . "\"
                data-nama=\"" . htmlspecialchars($p['nama'], ENT_QUOTES) . "\"
                data-kategori=\"" . htmlspecialchars($p['kategori'], ENT_QUOTES) . "\"
                data-harga=\"" . $p['harga'] . "\"
                data-stok=\"" . intval($p['stok']) . "\"
                data-deskripsi=\"" . htmlspecialchars($p['deskripsi'], ENT_QUOTES) . "\"
                data-gambar=\"" . $img . "\"
                title=\"Edit\"><i class=\"fas fa-edit\"></i></button>

            <button class=\"btn btn-sm btn-danger btn-delete\" data-id=\"" . intval($p['id']) . "\" title=\"Hapus\"><i class=\"fas fa-trash-alt\"></i></button>
        </td>
    </tr>";
    $no++;
}
?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="card-footer text-right">
                                        <nav>
                                            <ul class="pagination">
                                                <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                                                    <a class="page-link" href="?page=<?= max(1, $page - 1) ?>">Prev</a>
                                                </li>

                                                <?php for ($i = 1; $i <= $pages; $i++): ?>
                                                    <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                                        <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                                                    </li>
                                                <?php endfor; ?>

                                                <li class="page-item <?= $page >= $pages ? 'disabled' : '' ?>">
                                                    <a class="page-link" href="?page=<?= min($pages, $page + 1) ?>">Next</a>
                                                </li>
                                            </ul>
                                        </nav>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end daftar produk -->

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Tambah Produk -->
<div class="modal fade" id="modalAddProduct" tabindex="-1" role="dialog" aria-labelledby="modalAddProductLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form id="formAddProduct" enctype="multipart/form-data">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Produk</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label>Nama Produk</label>
                                <input type="text" name="nama" class="form-control" required>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Harga (Rp)</label>
                                    <input type="number" name="harga" class="form-control" required>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Stok</label>
                                    <input type="number" name="stok" class="form-control">
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Kategori</label>
                                <select name="kategori" class="form-control">
                                    <option>Celana Panjang</option>
                                    <option>Celana Pendek</option>
                                    <option>Jaket</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Deskripsi</label>
                                <textarea name="deskripsi" class="form-control" rows="4"></textarea>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group text-center">
                                <label>Gambar Produk</label>
                                <div class="mb-2">
                                    <img id="previewAdd" src="<?= ASSET ?>/images/ph.png" alt="preview" style="width:100%;height:150px;object-fit:cover;border-radius:6px;">
                                </div>
                                <input type="file" name="gambar" id="inputAddImage" accept="image/*" class="form-control-file">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Produk</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Edit Produk (sama struktur dengan Add) -->
<div class="modal fade" id="modalEditProduct" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form id="formEditProduct" enctype="multipart/form-data">
            <input type="hidden" name="id" id="edit_id">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Produk</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label>Nama Produk</label>
                                <input type="text" id="edit_nama" name="nama" class="form-control" required>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Harga (Rp)</label>
                                    <input type="number" id="edit_harga" name="harga" class="form-control" required>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Stok</label>
                                    <input type="number" id="edit_stok" name="stok" class="form-control">
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Kategori</label>
                                <select id="edit_kategori" name="kategori" class="form-control">
                                    <option>Celana Panjang</option>
                                    <option>Celana Pendek</option>
                                    <option>Jaket</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Deskripsi</label>
                                <textarea id="edit_deskripsi" name="deskripsi" class="form-control" rows="4"></textarea>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group text-center">
                                <label>Gambar Produk</label>
                                <div class="mb-2">
                                    <img id="previewEdit" src="<?= ASSET ?>/images/ph.png" alt="preview" style="width:100%;height:150px;object-fit:cover;border-radius:6px;">
                                </div>
                                <input type="file" name="gambar" id="inputEditImage" accept="image/*" class="form-control-file">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update Produk</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Hapus Konfirmasi -->
<div class="modal fade" id="modalDelete" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-body text-center">
                <h5>Yakin ingin menghapus produk ini?</h5>
                <p class="text-muted">Tindakan ini tidak dapat dikembalikan.</p>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Hapus</button>
            </div>
        </div>
    </div>
</div>




<?php
// include footer script (jika ada global footer)
// include __DIR__ . '/../includes/footer.php';
?>

<!-- minimal JS untuk demo (jQuery sudah dimuat pada template) -->
<script src="../assets/js/vendor-all.min.js"></script>
<script src="../assets/plugins/bootstrap/js/bootstrap.min.js"></script>
<script src="../assets/js/pcoded.min.js"></script>
<script>
    // helper: upload image and return URL
    async function uploadImage(file) {
        if (!file) return null;
        const fd = new FormData();
        fd.append('gambar', file);
        const res = await fetch('<?= BACKEND_URL ?>/api/upload_image.php', { method: 'POST', body: fd });
        const j = await res.json();
        if (j.success) return j.url;
        throw new Error(j.message || 'Upload failed');
    }

    document.getElementById('formAddProduct')?.addEventListener('submit', async function (e) {
        e.preventDefault();
        const form = e.target;
        const fd = new FormData(form);
        let fotoUrl = null;
        const file = form.querySelector('input[name="gambar"]')?.files?.[0];
        try {
            if (file) fotoUrl = await uploadImage(file);
            const payload = {
                nama: fd.get('nama'),
                kategori: fd.get('kategori'),
                stok: fd.get('stok'),
                harga: fd.get('harga'),
                deskripsi: fd.get('deskripsi'),
                foto: fotoUrl
            };
            const res = await fetch('<?= BACKEND_URL ?>/api/product_create.php', {
                method: 'POST',
                headers: {'Content-Type':'application/json'},
                body: JSON.stringify(payload)
            });
            const j = await res.json();
            if (j.success) location.reload();
            else alert('Gagal: ' + (j.message||''));
        } catch (err) { alert('Error: ' + err.message); }
    });

    document.getElementById('formEditProduct')?.addEventListener('submit', async function (e) {
        e.preventDefault();
        const form = e.target;
        const fd = new FormData(form);
        let fotoUrl = null;
        const file = form.querySelector('input[name="gambar"]')?.files?.[0];
        try {
            if (file) fotoUrl = await uploadImage(file);
            const payload = {
                id: fd.get('id'),
                nama: fd.get('nama'),
                kategori: fd.get('kategori'),
                stok: fd.get('stok'),
                harga: fd.get('harga'),
                deskripsi: fd.get('deskripsi'),
                foto: fotoUrl
            };
            const res = await fetch('<?= BACKEND_URL ?>/api/product_update.php', {
                method: 'POST',
                headers: {'Content-Type':'application/json'},
                body: JSON.stringify(payload)
            });
            const j = await res.json();
            if (j.success) location.reload();
            else alert('Gagal: ' + (j.message||''));
        } catch (err) { alert('Error: ' + err.message); }
    });

    // gunakan delegation supaya bekerja untuk elemen dinamis
    let deleteTargetId = null;

    document.addEventListener('click', function (e) {
        const editBtn = e.target.closest('.btn-edit');
        if (editBtn) {
            const id = editBtn.dataset.id;
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_nama').value = editBtn.dataset.nama || '';
            document.getElementById('edit_kategori').value = editBtn.dataset.kategori || '';
            document.getElementById('edit_harga').value = editBtn.dataset.harga || '';
            document.getElementById('edit_stok').value = editBtn.dataset.stok || '';
            document.getElementById('edit_deskripsi').value = editBtn.dataset.deskripsi || '';
            if (editBtn.dataset.gambar) document.getElementById('previewEdit').src = editBtn.dataset.gambar;
            $('#modalEditProduct').modal('show');
            return;
        }

        const delBtn = e.target.closest('.btn-delete');
        if (delBtn) {
            deleteTargetId = delBtn.dataset.id;
            $('#modalDelete').modal('show');
            return;
        }
    });

    // confirm delete
    document.getElementById('confirmDelete')?.addEventListener('click', async function () {
        if (!deleteTargetId) return;
        try {
            const res = await fetch('<?= BACKEND_URL ?>/api/product_delete.php', {
                method: 'POST',
                headers: {'Content-Type':'application/json'},
                body: JSON.stringify({id: deleteTargetId})
            });
            const j = await res.json();
            if (j.success) location.reload();
            else alert('Gagal: ' + (j.message || ''));
        } catch (err) {
            alert('Error: ' + err.message);
        }
    });

    // reclaim object URLs to avoid leaks
    let prevAddUrl = null;
    let prevEditUrl = null;
    const placeholder = '<?= ASSET ?>/images/ph.png';

    document.getElementById('inputAddImage')?.addEventListener('change', function (e) {
        const file = e.target.files[0];
        if (prevAddUrl) { URL.revokeObjectURL(prevAddUrl); prevAddUrl = null; }
        if (!file) {
            document.getElementById('previewAdd').src = placeholder;
            return;
        }
        const url = URL.createObjectURL(file);
        prevAddUrl = url;
        document.getElementById('previewAdd').src = url;
    });

    document.getElementById('inputEditImage')?.addEventListener('change', function (e) {
        const file = e.target.files[0];
        if (prevEditUrl) { URL.revokeObjectURL(prevEditUrl); prevEditUrl = null; }
        if (!file) {
            document.getElementById('previewEdit').src = placeholder;
            return;
        }
        const url = URL.createObjectURL(file);
        prevEditUrl = url;
        document.getElementById('previewEdit').src = url;
    });

    // reset preview when modal closed
    $('#modalAddProduct').on('hidden.bs.modal', function () {
        if (prevAddUrl) { URL.revokeObjectURL(prevAddUrl); prevAddUrl = null; }
        document.getElementById('previewAdd').src = placeholder;
        document.getElementById('formAddProduct')?.reset();
    });
    $('#modalEditProduct').on('hidden.bs.modal', function () {
        if (prevEditUrl) { URL.revokeObjectURL(prevEditUrl); prevEditUrl = null; }
        document.getElementById('previewEdit').src = placeholder;
        document.getElementById('formEditProduct')?.reset();
    });
</script>