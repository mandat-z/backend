<?php
include __DIR__ . '/../config/config.php';
include __DIR__ . '/../includes/head.php';
include __DIR__ . '/../includes/navigation.php';
include __DIR__ . '/../includes/topbar.php';
?>

<div class="pcoded-main-container">
  <div class="pcoded-wrapper">
    <div class="pcoded-content">
      <div class="pcoded-inner-content">
        <div class="main-body">
          <div class="page-wrapper">

            <!-- Header -->
            <div class="page-header">
              <div class="page-block">
                <div class="row align-items-center">
                  <div class="col-md-12">
                    <div class="page-header-title">
                      <h5>Kelola Banner</h5>
                    </div>
                    <ul class="breadcrumb">
                      <li class="breadcrumb-item"><a href="index.php"><i class="feather icon-home"></i></a></li>
                      <li class="breadcrumb-item"><a href="settings.php">Pengaturan</a></li>
                      <li class="breadcrumb-item active">Banner</li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>

            <!-- Content -->
            <div class="card">
              <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Daftar Banner</h5>
                <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalAddBanner">
                  <i class="feather icon-plus"></i> Tambah Banner
                </button>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-bordered table-striped">
                    <thead>
                      <tr class="text-center">
                        <th>#</th>
                        <th>Judul</th>
                        <th>Subjudul</th>
                        <th>Gambar</th>
                        <th>Status</th>
                        <th>Aksi</th>
                      </tr>
                    </thead>
                    <tbody id="bannerList">
                      <tr><td colspan="5" class="text-center text-muted">Memuat data...</td></tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
            <!-- End Content -->

          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal Add -->
<div class="modal fade" id="modalAddBanner" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form id="formAddBanner" enctype="multipart/form-data">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title">Tambah Banner Baru</h5>
          <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Judul Banner</label>
            <input type="text" name="judul" class="form-control" required>
          </div>
          <div class="form-group">
            <label>Subjudul Banner</label>
            <input type="text" name="subjudul" class="form-control" required>
          </div>
          <div class="form-group">
            <label>Gambar (JPG/PNG)</label>
            <input type="file" name="gambar" class="form-control" accept="image/*" required>
          </div>
          <div class="form-group">
            <label>Status</label>
            <select name="status" class="form-control">
              <option value="Aktif">Aktif</option>
              <option value="Nonaktif">Nonaktif</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="modalEditBanner" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form id="formEditBanner" enctype="multipart/form-data">
      <div class="modal-content">
        <div class="modal-header bg-success text-white">
          <h5 class="modal-title">Edit Banner</h5>
          <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
        </div>
        <div class="modal-body" id="editBody"></div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Update</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Tambahkan definisi BASE_URL ke JS -->
<script>
const BASE_URL = '<?php echo BACKEND_URL; ?>';
const baseApi = `${BASE_URL}/api`;

// Load data banner
async function loadBanner() {
  try {
    const res = await fetch(`${baseApi}/banner_get.php`);
    const data = await res.json();
    const tbody = document.getElementById('bannerList');
    tbody.innerHTML = '';

    if (!Array.isArray(data) || data.length === 0) {
      tbody.innerHTML = `<tr><td colspan="5" class="text-center text-muted">Belum ada banner</td></tr>`;
      return;
    }

    data.forEach((b, i) => {
      tbody.innerHTML += `
        <tr class="align-middle text-center">
          <td>${i + 1}</td>
          <td>${b.judul}</td>
          <td>${b.subjudul}</td>
          <td><img src="${BASE_URL}/assets/images/banner/${b.gambar}" width="100" class="rounded"></td>
          <td><span class="badge badge-${b.status === 'Aktif' ? 'success' : 'secondary'}">${b.status}</span></td>
          <td>
            <button class="btn btn-sm btn-success" onclick="editBanner(${b.id})"><i class="feather icon-edit"></i></button>
            <button class="btn btn-sm btn-danger" onclick="deleteBanner(${b.id})"><i class="feather icon-trash"></i></button>
          </td>
        </tr>`;
    });
  } catch (err) {
    console.error('Gagal memuat banner:', err);
    document.getElementById('bannerList').innerHTML = `<tr><td colspan="5" class="text-center text-danger">Gagal memuat data!</td></tr>`;
  }
}

// Tambah banner
document.getElementById('formAddBanner').addEventListener('submit', async e => {
  e.preventDefault();
  const formData = new FormData(e.target);
  const res = await fetch(`${baseApi}/banner_create.php`, { method: 'POST', body: formData });
  const result = await res.json();
  alert(result.message);
  $('#modalAddBanner').modal('hide');
  e.target.reset();
  loadBanner();
});

// Edit banner
async function editBanner(id) {
  const res = await fetch(`${baseApi}/banner_get.php?id=${id}`);
  const b = await res.json();

  document.getElementById('editBody').innerHTML = `
    <input type="hidden" name="id" value="${b.id}">
    <div class="form-group">
      <label>Judul</label>
      <input type="text" name="judul" class="form-control" value="${b.judul}">
    </div>
    <div class="form-group">
      <label>Subjudul</label>
      <input type="text" name="subjudul" class="form-control" value="${b.subjudul}">
    </div>
    <div class="form-group">
      <label>Gambar Baru (opsional)</label>
      <input type="file" name="gambar" class="form-control" accept="image/*">
      <img src="${BASE_URL}/assets/images/banner/${b.gambar}" width="100" class="mt-2 rounded">
    </div>
    <div class="form-group">
      <label>Status</label>
      <select name="status" class="form-control">
        <option value="Aktif" ${b.status === 'Aktif' ? 'selected' : ''}>Aktif</option>
        <option value="Nonaktif" ${b.status === 'Nonaktif' ? 'selected' : ''}>Nonaktif</option>
      </select>
    </div>`;
  $('#modalEditBanner').modal('show');
}

// Update banner
document.getElementById('formEditBanner').addEventListener('submit', async e => {
  e.preventDefault();
  const formData = new FormData(e.target);
  const res = await fetch(`${baseApi}/banner_update.php`, { method: 'POST', body: formData });
  const result = await res.json();
  alert(result.message);
  $('#modalEditBanner').modal('hide');
  loadBanner();
});

// Hapus banner
async function deleteBanner(id) {
  if (!confirm('Yakin ingin menghapus banner ini?')) return;
  const formData = new FormData();
  formData.append('id', id);
  const res = await fetch(`${baseApi}/banner_delete.php`, { method: 'POST', body: formData });
  const result = await res.json();
  alert(result.message);
  loadBanner();
}

loadBanner();
</script>

<!-- Bootstrap 4 JS -->
<script src="../assets/js/vendor-all.min.js"></script>
<script src="../assets/plugins/bootstrap/js/bootstrap.min.js"></script>
<script src="../assets/js/pcoded.min.js"></script>
