<?php
// backend/pages/produk.php
include __DIR__ . '/../config/config.php';
include __DIR__ . '/../includes/head.php';
include __DIR__ . '/../includes/navigation.php';
include __DIR__ . '/../includes/topbar.php';

// koneksi database & setup pagination + filter + search
$db = get_db();
$perPage = 10;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $perPage;

$filterKategori = $_GET['kategori'] ?? '';
$search = trim($_GET['search'] ?? '');

// siapkan query filter
$where = [];
$params = [];

if ($filterKategori !== '') {
  $where[] = "kategori = :kategori";
  $params[':kategori'] = $filterKategori;
}

if ($search !== '') {
  $where[] = "(nama LIKE :s OR kategori LIKE :s OR CAST(harga AS CHAR) LIKE :s OR deskripsi LIKE :s)";
  $params[':s'] = "%$search%";
}

$whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';

// hitung total data
$totalStmt = $db->prepare("SELECT COUNT(*) FROM produk $whereClause");
$totalStmt->execute($params);
$total = (int)$totalStmt->fetchColumn();
$pages = max(1, ceil($total / $perPage));

// ambil data produk
$stmt = $db->prepare("SELECT * FROM produk $whereClause ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
foreach ($params as $k => $v) $stmt->bindValue($k, $v);
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ambil semua kategori
$catStmt = $db->query("SELECT nama_kategori FROM kategori_produk ORDER BY nama_kategori ASC");
$allCategories = $catStmt->fetchAll(PDO::FETCH_COLUMN);
?>

<div class="pcoded-main-container">
  <div class="pcoded-wrapper">
    <div class="pcoded-content">
      <div class="pcoded-inner-content">
        <div class="main-body">
          <div class="page-wrapper">

            <!-- header -->
            <div class="page-header mb-3">
              <div class="page-block">
                <div class="row align-items-center">
                  <div class="col-md-12">
                    <div class="page-header-title">
                      <h5>Kelola Produk</h5>
                    </div>
                    <ul class="breadcrumb">
                      <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/index.php"><i class="fas fa-home"></i></a></li>
                      <li class="breadcrumb-item"><a href="#!">Produk</a></li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>

            <!-- controls -->
            <div class="row mb-3">
              <div class="col-12">
                <div class="card">
                  <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-2">

                    <!-- tombol kiri -->
                    <div class="d-flex gap-2 flex-wrap align-items-center">
                      <button class="btn btn-primary" data-toggle="modal" data-target="#modalAddProduct">
                        <i class="fas fa-plus"></i> Tambah Produk
                      </button>
                      <button class="btn btn-outline-secondary" data-toggle="modal" data-target="#modalKategori">
                        <i class="fas fa-list"></i> Kelola Kategori
                      </button>

                      <!-- filter kategori -->
                      <div class="dropdown">
                        <button class="btn btn-outline-primary dropdown-toggle" type="button" data-toggle="dropdown">
                          <i class="fas fa-filter"></i> Filter
                        </button>
                        <div class="dropdown-menu p-2">
                          <form id="filterForm" method="get" class="mb-0">
                            <select name="kategori" class="form-control mb-2" onchange="this.form.submit()">
                              <option value="">Semua Kategori</option>
                              <?php foreach ($allCategories as $c): ?>
                                <option value="<?= htmlspecialchars($c) ?>" <?= $filterKategori === $c ? 'selected' : '' ?>>
                                  <?= htmlspecialchars($c) ?>
                                </option>
                              <?php endforeach; ?>
                            </select>
                            <?php if ($search !== ''): ?>
                              <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
                            <?php endif; ?>
                          </form>
                        </div>
                      </div>
                    </div>

                    <!-- Search Input -->
                    <form method="get" class="position-relative" style="max-width:260px;">
                      <?php if ($filterKategori !== ''): ?>
                        <input type="hidden" name="kategori" value="<?= htmlspecialchars($filterKategori) ?>">
                      <?php endif; ?>
                      <input 
                        type="text" 
                        name="search" 
                        value="<?= htmlspecialchars($search) ?>" 
                        class="form-control ps-5" 
                        placeholder="Cari produk..."
                        style="border-radius: 50px;"
                      >
                      <i class="fas fa-search position-absolute text-muted" 
                        style="top: 50%; right: 15px; transform: translateY(-50%); pointer-events: none;"></i>
                    </form>

                  </div>
                </div>
              </div>
            </div>

            <!-- products table -->
            <div class="row">
              <div class="col-12">
                <div class="card table-card">
                  <div class="card-header"><h5>Daftar Produk</h5></div>
                  <div class="card-body">
                    <div class="table-responsive">
                      <table class="table table-hover table-sm">
                        <thead class="thead-light">
                          <tr>
                            <th>#</th>
                            <th>Foto</th>
                            <th>Nama</th>
                            <th>Kategori</th>
                            <th>Stok</th>
                            <th>Harga</th>
                            <th>Deskripsi</th>
                            <th>Aksi</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php if (empty($products)): ?>
                            <tr>
                              <td colspan="8" class="text-center py-4">
                                <i class="fas fa-search text-muted mb-2" style="font-size:1.4rem;"></i><br>
                                <span class="text-muted">Tidak ada produk yang cocok dengan pencarian atau filter.</span>
                              </td>
                            </tr>
                          <?php else: ?>
                            <?php $no = $offset + 1; foreach ($products as $p): ?>
                              <?php $img = $p['foto'] ?: ASSET . '/images/ph.png'; ?>
                              <tr>
                                <td><?= $no++ ?></td>
                                <td><img src="<?= htmlspecialchars($img) ?>" style="width:60px;height:40px;object-fit:cover;border-radius:4px;"></td>
                                <td><?= htmlspecialchars($p['nama']) ?></td>
                                <td><?= htmlspecialchars($p['kategori']) ?></td>
                                <td><?= (int)$p['stok'] ?></td>
                                <td>Rp <?= number_format($p['harga'], 0, ',', '.') ?></td>
                                <td><?= htmlspecialchars(strlen($p['deskripsi']) > 120 ? substr($p['deskripsi'], 0, 120).'...' : $p['deskripsi']) ?></td>
                                <td>
                                  <button class="btn btn-sm btn-warning btn-edit"
                                    data-id="<?= $p['id'] ?>"
                                    data-nama="<?= htmlspecialchars($p['nama'], ENT_QUOTES) ?>"
                                    data-kategori="<?= htmlspecialchars($p['kategori'], ENT_QUOTES) ?>"
                                    data-harga="<?= $p['harga'] ?>"
                                    data-stok="<?= $p['stok'] ?>"
                                    data-deskripsi="<?= htmlspecialchars($p['deskripsi'], ENT_QUOTES) ?>"
                                    data-gambar="<?= htmlspecialchars($img) ?>">
                                    <i class="fas fa-edit"></i>
                                  </button>
                                  <button class="btn btn-sm btn-danger btn-delete" data-id="<?= $p['id'] ?>">
                                    <i class="fas fa-trash-alt"></i>
                                  </button>
                                </td>
                              </tr>
                            <?php endforeach; ?>
                          <?php endif; ?>
                        </tbody>
                      </table>
                    </div>

                    <!-- pagination -->
                    <nav aria-label="page-nav" class="mt-3">
                      <ul class="pagination justify-content-end">
                        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                          <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => max(1,$page-1)])) ?>">Prev</a>
                        </li>

                        <?php
                        $start = max(1, $page - 3);
                        $end = min($pages, $page + 3);
                        for ($i = $start; $i <= $end; $i++): ?>
                          <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                            <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>"><?= $i ?></a>
                          </li>
                        <?php endfor; ?>

                        <li class="page-item <?= $page >= $pages ? 'disabled' : '' ?>">
                          <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => min($pages,$page+1)])) ?>">Next</a>
                        </li>
                      </ul>
                    </nav>

                  </div>
                </div>
              </div>
            </div>

          </div> <!-- .page-wrapper -->
        </div>
      </div>
    </div>
  </div>
</div>


<!-- MODALS -->
<!-- Add Product -->
<div class="modal fade" id="modalAddProduct" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <form id="formAddProduct" enctype="multipart/form-data">
      <div class="modal-content">
        <div class="modal-header"><h5 class="modal-title">Tambah Produk</h5>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-8">
              <div class="form-group"><label>Nama Produk</label><input type="text" name="nama" class="form-control" required></div>
              <div class="form-row">
                <div class="form-group col-md-6"><label>Harga (Rp)</label><input type="number" name="harga" class="form-control" required></div>
                <div class="form-group col-md-6"><label>Stok</label><input type="number" name="stok" class="form-control"></div>
              </div>
              <div class="form-group"><label>Kategori</label>
                <select id="kategoriProdukAdd" name="kategori" class="form-control">
                  <option value="">(muat saat modal dibuka)</option>
                </select>
              </div>
              <div class="form-group"><label>Deskripsi</label><textarea name="deskripsi" class="form-control" rows="4"></textarea></div>
            </div>

            <div class="col-md-4 text-center">
              <label>Gambar Produk</label>
              <div class="mb-2"><img id="previewAdd" src="<?= ASSET ?>/images/ph.png" style="width:100%;height:150px;object-fit:cover;border-radius:6px;"></div>
              <input type="file" name="gambar" id="inputAddImage" accept="image/*" class="form-control-file">
            </div>
          </div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button><button class="btn btn-primary" type="submit">Simpan Produk</button></div>
      </div>
    </form>
  </div>
</div>

<!-- Edit Product -->
<div class="modal fade" id="modalEditProduct" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <form id="formEditProduct" enctype="multipart/form-data">
      <input type="hidden" name="id" id="edit_id">
      <div class="modal-content">
        <div class="modal-header"><h5 class="modal-title">Edit Produk</h5>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-8">
              <div class="form-group"><label>Nama Produk</label><input type="text" id="edit_nama" name="nama" class="form-control" required></div>
              <div class="form-row">
                <div class="form-group col-md-6"><label>Harga (Rp)</label><input type="number" id="edit_harga" name="harga" class="form-control" required></div>
                <div class="form-group col-md-6"><label>Stok</label><input type="number" id="edit_stok" name="stok" class="form-control"></div>
              </div>
              <div class="form-group"><label>Kategori</label>
                <select id="kategoriProdukEdit" name="kategori" class="form-control">
                  <option value="">(muat saat modal dibuka)</option>
                </select>
              </div>
              <div class="form-group"><label>Deskripsi</label><textarea id="edit_deskripsi" name="deskripsi" class="form-control" rows="4"></textarea></div>
            </div>

            <div class="col-md-4 text-center">
              <label>Gambar Produk</label>
              <div class="mb-2"><img id="previewEdit" src="<?= ASSET ?>/images/ph.png" style="width:100%;height:150px;object-fit:cover;border-radius:6px;"></div>
              <input type="file" name="gambar" id="inputEditImage" accept="image/*" class="form-control-file">
            </div>
          </div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button><button class="btn btn-primary" type="submit">Update Produk</button></div>
      </div>
    </form>
  </div>
</div>

<!-- Kategori modal -->
<div class="modal fade" id="modalKategori" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form id="formKategori">
      <div class="modal-content">
        <div class="modal-header"><h5 class="modal-title">Kategori Produk</h5>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <ul class="list-group mb-3" id="kategoriList"></ul>
          <div class="input-group">
            <input type="text" id="newKategori" class="form-control" placeholder="Tambah kategori baru">
            <div class="input-group-append">
              <button class="btn btn-primary" id="addKategoriBtn" type="button"><i class="fas fa-plus"></i> Tambah</button>
            </div>
          </div>
        </div>
        <div class="modal-footer"><button class="btn btn-secondary" data-dismiss="modal">Tutup</button></div>
      </div>
    </form>
  </div>
</div>

<!-- Confirm delete -->
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

<!-- scripts (jQuery must be loaded before this; vendor-all.min.js contains jQuery in template) -->
<script src="../assets/js/vendor-all.min.js"></script>
<script src="../assets/plugins/bootstrap/js/bootstrap.min.js"></script>
<script src="../assets/js/pcoded.min.js"></script>

<script>
$(function() {
  const API_KATEGORI = "<?= BACKEND_URL ?>/api/kategori_handler.php";
  const API_CREATE = "<?= BACKEND_URL ?>/api/product_create.php";
  const API_UPDATE = "<?= BACKEND_URL ?>/api/product_update.php";
  const API_DELETE = "<?= BACKEND_URL ?>/api/product_delete.php";
  const API_UPLOAD = "<?= BACKEND_URL ?>/api/upload_image.php";

  // --- kategori functions ---
  function renderKategoriList(data) {
    let list = '';
    if (!Array.isArray(data) || data.length === 0) {
      list = '<li class="list-group-item text-center text-muted">Belum ada kategori</li>';
    } else {
      data.forEach(item => {
        list += `<li class="list-group-item d-flex justify-content-between align-items-center">
          ${item.nama_kategori}
          <span>
            <button class="btn btn-sm btn-outline-warning edit-btn" data-id="${item.id_kategori}" data-nama="${item.nama_kategori}"><i class="fas fa-edit"></i></button>
            <button class="btn btn-sm btn-outline-danger delete-btn" data-id="${item.id_kategori}"><i class="fas fa-trash-alt"></i></button>
          </span>
        </li>`;
      });
    }
    $('#kategoriList').html(list);
  }

  function loadKategoriList() {
    $.post(API_KATEGORI, { action: 'fetch' }, function(res) {
      const data = (typeof res === 'string') ? JSON.parse(res) : res;
      renderKategoriList(data);
    }).fail(function(xhr){ console.error(xhr.responseText); });
  }

  function loadKategoriDropdown() {
    $.post(API_KATEGORI, { action: 'fetch' }, function(res) {
      const data = (typeof res === 'string') ? JSON.parse(res) : res;
      let options = '<option value="">Pilih Kategori</option>';
      data.forEach(k => options += `<option value="${k.nama_kategori}">${k.nama_kategori}</option>`);
      $('#kategoriProdukAdd, #kategoriProdukEdit').html(options);
    }).fail(function(xhr){ console.error(xhr.responseText); });
  }

  $('#modalKategori').on('show.bs.modal', loadKategoriList);
  $('#modalAddProduct, #modalEditProduct').on('show.bs.modal', loadKategoriDropdown);

  $('#addKategoriBtn').click(function() {
    const nama = $('#newKategori').val().trim();
    if (!nama) return alert('Nama kategori kosong');
    $.post(API_KATEGORI, { action: 'add', nama }, function(res) {
      const j = (typeof res === 'string') ? JSON.parse(res) : res;
      if (j.success) {
        $('#newKategori').val('');
        loadKategoriList();
        loadKategoriDropdown();
      } else alert(j.message || 'Gagal menambah kategori');
    }).fail(function(xhr){ console.error(xhr.responseText); });
  });

  $(document).on('click', '.delete-btn', function() {
    if (!confirm('Hapus kategori ini?')) return;
    const id = $(this).data('id');
    $.post(API_KATEGORI, { action: 'delete', id }, function() {
      loadKategoriList();
      loadKategoriDropdown();
    }).fail(function(xhr){ console.error(xhr.responseText); });
  });

  $(document).on('click', '.edit-btn', function() {
    const id = $(this).data('id');
    const old = $(this).data('nama');
    const nama = prompt('Edit nama kategori:', old);
    if (!nama || nama.trim() === '') return;
    $.post(API_KATEGORI, { action: 'edit', id, nama }, function() {
      loadKategoriList();
      loadKategoriDropdown();
    }).fail(function(xhr){ console.error(xhr.responseText); });
  });

  // --- image preview helpers ---
  function readPreview(input, imgEl) {
    const file = input.files && input.files[0];
    if (!file) { imgEl.src = "<?= ASSET ?>/images/ph.png"; return; }
    const url = URL.createObjectURL(file);
    imgEl.src = url;
  }

  $('#inputAddImage').on('change', function(){ readPreview(this, document.getElementById('previewAdd')); });
  $('#inputEditImage').on('change', function(){ readPreview(this, document.getElementById('previewEdit')); });

  // --- upload image (API) ---
  async function uploadImage(file) {
    if (!file) return null;
    const fd = new FormData();
    fd.append('gambar', file);
    const res = await fetch(API_UPLOAD, { method: 'POST', body: fd });
    const j = await res.json();
    if (j.success) return j.url;
    throw new Error(j.message || 'Upload gagal');
  }

  // --- product create ---
  $('#formAddProduct').on('submit', async function(e) {
    e.preventDefault();
    const form = this;
    const fd = new FormData(form);
    try {
      const file = fd.get('gambar');
      let fotoUrl = null;
      if (file && file.size > 0) fotoUrl = await uploadImage(file);
      const payload = {
        nama: fd.get('nama'),
        kategori: fd.get('kategori'),
        stok: fd.get('stok') || 0,
        harga: fd.get('harga') || 0,
        deskripsi: fd.get('deskripsi') || '',
        foto: fotoUrl
      };
      const res = await fetch(API_CREATE, { method: 'POST', headers: {'Content-Type':'application/json'}, body: JSON.stringify(payload) });
      const j = await res.json();
      if (j.success) location.reload();
      else alert('Gagal: ' + (j.message || ''));
    } catch (err) { alert('Error: ' + err.message); }
  });

  // --- fill edit modal when edit clicked ---
  $(document).on('click', '.btn-edit', function() {
    const btn = $(this);
    $('#edit_id').val(btn.data('id'));
    $('#edit_nama').val(btn.data('nama'));
    $('#edit_harga').val(btn.data('harga'));
    $('#edit_stok').val(btn.data('stok'));
    $('#edit_deskripsi').val(btn.data('deskripsi'));
    if (btn.data('gambar')) $('#previewEdit').attr('src', btn.data('gambar'));
    // set category after dropdown loaded (dropdown loads when modal show)
    $('#modalEditProduct').on('shown.bs.modal.editFill', function() {
      $('#kategoriProdukEdit').val(btn.data('kategori'));
      $('#modalEditProduct').off('shown.bs.modal.editFill');
    });
    $('#modalEditProduct').modal('show');
  });

  // --- product update ---
  $('#formEditProduct').on('submit', async function(e) {
    e.preventDefault();
    const form = this;
    const fd = new FormData(form);
    try {
      const file = fd.get('gambar');
      let fotoUrl = null;
      if (file && file.size > 0) fotoUrl = await uploadImage(file);
      const payload = {
        id: fd.get('id'),
        nama: fd.get('nama'),
        kategori: fd.get('kategori'),
        stok: fd.get('stok') || 0,
        harga: fd.get('harga') || 0,
        deskripsi: fd.get('deskripsi') || '',
        foto: fotoUrl
      };
      const res = await fetch(API_UPDATE, { method: 'POST', headers: {'Content-Type':'application/json'}, body: JSON.stringify(payload) });
      const j = await res.json();
      if (j.success) location.reload();
      else alert('Gagal: ' + (j.message || ''));
    } catch (err) { alert('Error: ' + err.message); }
  });

  // --- product delete ---
  let deleteTargetId = null;
  $(document).on('click', '.btn-delete', function() {
    deleteTargetId = $(this).data('id');
    $('#modalDelete').modal('show');
  });

  $('#confirmDelete').on('click', async function() {
    if (!deleteTargetId) return;
    try {
      const res = await fetch(API_DELETE, { method: 'POST', headers: {'Content-Type':'application/json'}, body: JSON.stringify({ id: deleteTargetId }) });
      const j = await res.json();
      if (j.success) location.reload();
      else alert('Gagal menghapus produk.');
    } catch (err) { alert('Error: ' + err.message); }
  });

  // initial load (category list not needed initially, but keep for modal)
  // loadKategoriList(); // only when opening modal
});
</script>

