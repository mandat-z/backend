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
                      <h5>Kelola FAQ</h5>
                    </div>
                    <ul class="breadcrumb">
                      <li class="breadcrumb-item"><a href="index.php"><i class="feather icon-home"></i></a></li>
                      <li class="breadcrumb-item active">FAQ</li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>

            <!-- Content -->
            <div class="card">
              <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Daftar FAQ</h5>
                <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalAddFaq">
                  <i class="feather icon-plus"></i> Tambah FAQ
                </button>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-bordered table-striped">
                    <thead>
                      <tr class="text-center">
                        <th>#</th>
                        <th>Pertanyaan</th>
                        <th>Jawaban</th>
                        <th>Status</th>
                        <th>Aksi</th>
                      </tr>
                    </thead>
                    <tbody id="faqList">
                      <tr><td colspan="5" class="text-center text-muted">Memuat data...</td></tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal Add -->
<div class="modal fade" id="modalAddFaq" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form id="formAddFaq">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title">Tambah FAQ</h5>
          <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Pertanyaan</label>
            <input type="text" name="pertanyaan" class="form-control" required>
          </div>
          <div class="form-group">
            <label>Jawaban</label>
            <textarea name="jawaban" class="form-control" rows="4" required></textarea>
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
<div class="modal fade" id="modalEditFaq" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form id="formEditFaq">
      <div class="modal-content">
        <div class="modal-header bg-success text-white">
          <h5 class="modal-title">Edit FAQ</h5>
          <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
        </div>
        <div class="modal-body" id="editFaqBody"></div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Update</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Script -->
 <script> const BASE_URL = "<?php echo BASE_URL; ?>"; </script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
const baseApi = `${BASE_URL}/api`;

// Load FAQ
async function loadFaq() {
  const res = await fetch(`${baseApi}/faq_get.php`);
  const data = await res.json();
  const tbody = document.getElementById('faqList');
  tbody.innerHTML = '';

  if (!data.length) {
    tbody.innerHTML = `<tr><td colspan="5" class="text-center text-muted">Belum ada FAQ</td></tr>`;
    return;
  }

  data.forEach((f, i) => {
    tbody.innerHTML += `
      <tr class="align-middle text-center">
        <td>${i + 1}</td>
        <td>${f.pertanyaan}</td>
        <td>${f.jawaban}</td>
        <td><span class="badge badge-${f.status === 'Aktif' ? 'success' : 'secondary'}">${f.status}</span></td>
        <td>
          <button class="btn btn-sm btn-success" onclick="editFaq(${f.id})"><i class="feather icon-edit"></i></button>
          <button class="btn btn-sm btn-danger" onclick="deleteFaq(${f.id})"><i class="feather icon-trash"></i></button>
        </td>
      </tr>`;
  });
}

// Tambah FAQ
document.addEventListener('DOMContentLoaded', () => {
  document.getElementById('formAddFaq').addEventListener('submit', async e => {
    e.preventDefault();
    const formData = new FormData(e.target);
    const res = await fetch(`${baseApi}/faq_create.php`, { method: 'POST', body: formData });
    const result = await res.json();

    Swal.fire({
      icon: result.success ? 'success' : 'error',
      title: result.message,
      showConfirmButton: false,
      timer: 1500
    });

    if (result.success) {
      $('#modalAddFaq').modal('hide');
      e.target.reset();
      loadFaq();
    }
  });

  // Update FAQ
  document.getElementById('formEditFaq').addEventListener('submit', async e => {
    e.preventDefault();
    const formData = new FormData(e.target);
    const res = await fetch(`${baseApi}/faq_update.php`, { method: 'POST', body: formData });
    const result = await res.json();

    Swal.fire({
      icon: result.success ? 'success' : 'error',
      title: result.message,
      showConfirmButton: false,
      timer: 1500
    });

    if (result.success) {
      $('#modalEditFaq').modal('hide');
      loadFaq();
    }
  });

  loadFaq();
});

// Edit FAQ
async function editFaq(id) {
  const res = await fetch(`${baseApi}/faq_get.php?id=${id}`);
  const f = await res.json();

  document.getElementById('editFaqBody').innerHTML = `
    <input type="hidden" name="id" value="${f.id}">
    <div class="form-group">
      <label>Pertanyaan</label>
      <input type="text" name="pertanyaan" class="form-control" value="${f.pertanyaan}">
    </div>
    <div class="form-group">
      <label>Jawaban</label>
      <textarea name="jawaban" class="form-control" rows="4">${f.jawaban}</textarea>
    </div>
    <div class="form-group">
      <label>Status</label>
      <select name="status" class="form-control">
        <option value="Aktif" ${f.status === 'Aktif' ? 'selected' : ''}>Aktif</option>
        <option value="Nonaktif" ${f.status === 'Nonaktif' ? 'selected' : ''}>Nonaktif</option>
      </select>
    </div>`;
  $('#modalEditFaq').modal('show');
}

// Hapus FAQ
async function deleteFaq(id) {
  const confirm = await Swal.fire({
    title: 'Hapus FAQ ini?',
    text: 'Tindakan ini tidak dapat dibatalkan.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Ya, hapus!',
    cancelButtonText: 'Batal'
  });

  if (!confirm.isConfirmed) return;

  const formData = new FormData();
  formData.append('id', id);
  const res = await fetch(`${baseApi}/faq_delete.php`, { method: 'POST', body: formData });
  const result = await res.json();

  Swal.fire({
    icon: result.success ? 'success' : 'error',
    title: result.message,
    showConfirmButton: false,
    timer: 1500
  });

  if (result.success) loadFaq();
}
</script>

<script src="../assets/js/vendor-all.min.js"></script>
<script src="../assets/plugins/bootstrap/js/bootstrap.min.js"></script>
<script src="../assets/js/pcoded.min.js"></script>