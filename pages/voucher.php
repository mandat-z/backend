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
                      <h5>Kelola Voucher</h5>
                    </div>
                    <ul class="breadcrumb">
                      <li class="breadcrumb-item"><a href="index.php"><i class="feather icon-home"></i></a></li>
                      <li class="breadcrumb-item"><a href="pengaturan.php">Pengaturan</a></li>
                      <li class="breadcrumb-item active">Voucher</li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>

            <!-- Content -->
            <div class="card">
              <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Daftar Voucher</h5>
                <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalAddVoucher">
                  <i class="feather icon-plus"></i> Tambah Voucher
                </button>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-bordered table-striped">
                    <thead>
                      <tr class="text-center">
                        <th>#</th>
                        <th>Kode Voucher</th>
                        <th>Diskon</th>
                        <th>Tipe</th>
                        <th>Maksimal Diskon</th>
                        <th>Minimal Belanja</th>
                        <th>Berlaku Hingga</th>
                        <th>Status</th>
                        <th>Aksi</th>
                      </tr>
                    </thead>
                    <tbody id="voucherList">
                      <tr><td colspan="9" class="text-center text-muted">Memuat data...</td></tr>
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
<div class="modal fade" id="modalAddVoucher" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form id="formAddVoucher">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title">Tambah Voucher Baru</h5>
          <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Kode Voucher</label>
            <input type="text" name="kode_voucher" class="form-control" required>
          </div>
          <div class="form-group">
            <label>Diskon</label>
            <input type="number" name="diskon" class="form-control" min="0" required>
          </div>
          <div class="form-group">
            <label>Tipe Diskon</label>
            <select name="tipe_diskon" class="form-control" required>
              <option value="persen">Persen (%)</option>
              <option value="nominal">Nominal (Rp)</option>
            </select>
          </div>
          <div class="form-group">
            <label>Maksimal Diskon (Rp)</label>
            <input type="number" name="maksimal_diskon" class="form-control" min="0" required>
          </div>
          <div class="form-group">
            <label>Minimal Belanja (Rp)</label>
            <input type="number" name="minimal_belanja" class="form-control" min="0" required>
          </div>
          <div class="form-group">
            <label>Berlaku Hingga</label>
            <input type="date" name="berlaku_hingga" class="form-control" required>
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
<div class="modal fade" id="modalEditVoucher" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form id="formEditVoucher">
      <div class="modal-content">
        <div class="modal-header bg-success text-white">
          <h5 class="modal-title">Edit Voucher</h5>
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

// Load data voucher
async function loadVoucher() {
  try {
    const res = await fetch(`${baseApi}/voucher_get.php`);
    const data = await res.json();
    const tbody = document.getElementById('voucherList');
    tbody.innerHTML = '';

    if (!Array.isArray(data) || data.length === 0) {
      tbody.innerHTML = `<tr><td colspan="8" class="text-center text-muted">Belum ada voucher</td></tr>`;
      return;
    }

    data.forEach((v, i) => {
      tbody.innerHTML += `
        <tr class="align-middle text-center">
          <td>${i + 1}</td>
          <td>${v.kode_voucher}</td>
          <td>${v.diskon} ${v.tipe_diskon === 'persen' ? '%' : 'Rp'}</td>
          <td>${v.tipe_diskon === 'persen' ? 'Persen' : 'Nominal'}</td>
          <td>Rp ${parseInt(v.maksimal_diskon).toLocaleString()}</td>
          <td>Rp ${parseInt(v.minimal_belanja).toLocaleString()}</td>
          <td>${new Date(v.berlaku_hingga).toLocaleDateString('id-ID')}</td>
          <td><span class="badge badge-${v.status === 'Aktif' ? 'success' : 'secondary'}">${v.status}</span></td>
          <td>
            <button class="btn btn-sm btn-success" onclick="editVoucher(${v.id})"><i class="feather icon-edit"></i></button>
            <button class="btn btn-sm btn-danger" onclick="deleteVoucher(${v.id})"><i class="feather icon-trash"></i></button>
          </td>
        </tr>`;
    });
  } catch (err) {
    console.error('Gagal memuat voucher:', err);
    document.getElementById('voucherList').innerHTML = `<tr><td colspan="8" class="text-center text-danger">Gagal memuat data!</td></tr>`;
  }
}

// Tambah voucher
document.getElementById('formAddVoucher').addEventListener('submit', async e => {
  e.preventDefault();
  const formData = new FormData(e.target);
  const res = await fetch(`${baseApi}/voucher_create.php`, { method: 'POST', body: formData });
  const result = await res.json();
  alert(result.message);
  $('#modalAddVoucher').modal('hide');
  e.target.reset();
  loadVoucher();
});

// Edit voucher
async function editVoucher(id) {
  const res = await fetch(`${baseApi}/voucher_get.php?id=${id}`);
  const v = await res.json();

  document.getElementById('editBody').innerHTML = `
    <input type="hidden" name="id" value="${v.id}">
    <div class="form-group">
      <label>Kode Voucher</label>
      <input type="text" name="kode_voucher" class="form-control" value="${v.kode_voucher}">
    </div>
    <div class="form-group">
      <label>Diskon</label>
      <input type="number" name="diskon" class="form-control" min="0" value="${v.diskon}">
    </div>
    <div class="form-group">
      <label>Tipe Diskon</label>
      <select name="tipe_diskon" class="form-control">
        <option value="persen" ${v.tipe_diskon === 'persen' ? 'selected' : ''}>Persen (%)</option>
        <option value="nominal" ${v.tipe_diskon === 'nominal' ? 'selected' : ''}>Nominal (Rp)</option>
      </select>
    </div>
    <div class="form-group">
      <label>Maksimal Diskon (Rp)</label>
      <input type="number" name="maksimal_diskon" class="form-control" min="0" value="${v.maksimal_diskon}">
    </div>
    <div class="form-group">
      <label>Minimal Belanja (Rp)</label>
      <input type="number" name="minimal_belanja" class="form-control" min="0" value="${v.minimal_belanja}">
    </div>
    <div class="form-group">
      <label>Berlaku Hingga</label>
      <input type="date" name="berlaku_hingga" class="form-control" value="${v.berlaku_hingga}">
    </div>
    <div class="form-group">
      <label>Status</label>
      <select name="status" class="form-control">
        <option value="Aktif" ${v.status === 'Aktif' ? 'selected' : ''}>Aktif</option>
        <option value="Nonaktif" ${v.status === 'Nonaktif' ? 'selected' : ''}>Nonaktif</option>
      </select>
    </div>`;
  $('#modalEditVoucher').modal('show');
}

// Update voucher
document.getElementById('formEditVoucher').addEventListener('submit', async e => {
  e.preventDefault();
  const formData = new FormData(e.target);
  const res = await fetch(`${baseApi}/voucher_update.php`, { method: 'POST', body: formData });
  const result = await res.json();
  alert(result.message);
  $('#modalEditVoucher').modal('hide');
  loadVoucher();
});

// Hapus voucher
async function deleteVoucher(id) {
  if (!confirm('Yakin ingin menghapus voucher ini?')) return;
  const formData = new FormData();
  formData.append('id', id);
  const res = await fetch(`${baseApi}/voucher_delete.php`, { method: 'POST', body: formData });
  const result = await res.json();
  alert(result.message);
  loadVoucher();
}

loadVoucher();
</script>

<!-- Bootstrap 4 JS -->
<script src="../assets/js/vendor-all.min.js"></script>
<script src="../assets/plugins/bootstrap/js/bootstrap.min.js"></script>
<script src="../assets/js/pcoded.min.js"></script>
