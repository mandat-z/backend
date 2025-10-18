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

            <!-- Header Halaman -->
            <div class="page-header">
              <div class="page-block">
                <div class="row align-items-center">
                  <div class="col-md-12">
                    <div class="page-header-title">
                      <h5>Kelola Pengiriman</h5>
                    </div>
                    <ul class="breadcrumb">
                      <li class="breadcrumb-item">
                        <a href="<?= BASE_URL ?>/index.php"><i class="fas fa-home"></i></a>
                      </li>
                      <li class="breadcrumb-item"><a href="#!">Kelola Pengiriman</a></li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>

            <!-- Daftar Pengiriman -->
            <section class="content">
              <div class="container-fluid">
                <div class="card mb-4">
                  <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Daftar Pengiriman</h3>
                  </div>
                  <div class="card-body table-responsive">
                    <table id="tabelPengiriman" class="table table-bordered table-striped align-middle">
                      <thead class="table-success">
                        <tr>
                          <th>No</th>
                          <th>ID Pengiriman</th>
                          <th>Tanggal</th>
                          <th>Kota Tujuan</th>
                          <th>Status</th>
                          <th>Aksi</th>
                        </tr>
                      </thead>
                      <tbody id="pengirimanBody">
                        <tr>
                          <td>1</td>
                          <td>#KIRIM001</td>
                          <td>2025-10-17</td>
                          <td>Bandung</td>
                          <td><span class="badge bg-warning">Dalam Proses</span></td>
                          <td>
                            <button class="btn btn-primary btn-sm edit-btn"><i class="fas fa-edit"></i></button>
                            <button class="btn btn-danger btn-sm delete-btn"><i class="fas fa-trash"></i></button>
                          </td>
                        </tr>
                        <tr>
                          <td>2</td>
                          <td>#KIRIM002</td>
                          <td>2025-10-18</td>
                          <td>Solo</td>
                          <td><span class="badge bg-success">Terkirim</span></td>
                          <td>
                            <button class="btn btn-primary btn-sm edit-btn"><i class="fas fa-edit"></i></button>
                            <button class="btn btn-danger btn-sm delete-btn"><i class="fas fa-trash"></i></button>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>

                <!-- Biaya Pengiriman -->
                <div class="card">
                  <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Daftar Biaya Pengiriman</h3>
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#tambahBiayaModal">
                      <i class="fas fa-plus"></i> Tambah Biaya
                    </button>
                  </div>
                  <div class="card-body table-responsive">
                    <table id="tabelBiaya" class="table table-bordered table-striped align-middle">
                      <thead class="table-success">
                        <tr>
                          <th>No</th>
                          <th>Kota Tujuan</th>
                          <th>Biaya (Rp)</th>
                          <th>Aksi</th>
                        </tr>
                      </thead>
                      <tbody id="biayaBody">
                        <tr>
                          <td>1</td>
                          <td>Bandung</td>
                          <td>10.000</td>
                          <td>
                            <button class="btn btn-primary btn-sm edit-btn"><i class="fas fa-edit"></i></button>
                            <button class="btn btn-danger btn-sm delete-btn"><i class="fas fa-trash"></i></button>
                          </td>
                        </tr>
                        <tr>
                          <td>2</td>
                          <td>Solo</td>
                          <td>20.000</td>
                          <td>
                            <button class="btn btn-primary btn-sm edit-btn"><i class="fas fa-edit"></i></button>
                            <button class="btn btn-danger btn-sm delete-btn"><i class="fas fa-trash"></i></button>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </section>

          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal Tambah Pengiriman -->
<div class="modal fade" id="tambahPengirimanModal" tabindex="-1">
  <div class="modal-dialog">
    <form id="formTambahPengiriman">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Tambah Pengiriman</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Kota Tujuan</label>
            <input type="text" name="kota" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Tanggal</label>
            <input type="date" name="tanggal" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
              <option value="Dalam Proses">Dalam Proses</option>
              <option value="Terkirim">Terkirim</option>
              <option value="Dibatalkan">Dibatalkan</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Simpan</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Modal Tambah Biaya -->
<div class="modal fade" id="tambahBiayaModal" tabindex="-1">
  <div class="modal-dialog">
    <form id="formTambahBiaya">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Tambah Biaya Pengiriman</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Kota Tujuan</label>
            <input type="text" name="kota" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Biaya (Rp)</label>
            <input type="number" name="biaya" class="form-control" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Simpan</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Script JS -->
<script src="../assets/js/vendor-all.min.js"></script>
<script src="../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/pcoded.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Tambah Pengiriman
  const formPengiriman = document.getElementById('formTambahPengiriman');
  formPengiriman.addEventListener('submit', function(e) {
    e.preventDefault();
    const kota = this.kota.value;
    const tanggal = this.tanggal.value;
    const status = this.status.value;
    const table = document.getElementById('pengirimanBody');
    const rowCount = table.rows.length + 1;
    const row = `
      <tr>
        <td>${rowCount}</td>
        <td>#KIRIM00${rowCount}</td>
        <td>${tanggal}</td>
        <td>${kota}</td>
        <td><span class="badge bg-warning">${status}</span></td>
        <td>
          <button class="btn btn-primary btn-sm edit-btn"><i class="fas fa-edit"></i></button>
          <button class="btn btn-danger btn-sm delete-btn"><i class="fas fa-trash"></i></button>
        </td>
      </tr>`;
    table.insertAdjacentHTML('beforeend', row);
    this.reset();
    bootstrap.Modal.getInstance(document.getElementById('tambahPengirimanModal')).hide();
  });

  // Tambah Biaya
  const formBiaya = document.getElementById('formTambahBiaya');
  formBiaya.addEventListener('submit', function(e) {
    e.preventDefault();
    const kota = this.kota.value;
    const biaya = this.biaya.value;
    const table = document.getElementById('biayaBody');
    const rowCount = table.rows.length + 1;
    const row = `
      <tr>
        <td>${rowCount}</td>
        <td>${kota}</td>
        <td>${parseInt(biaya).toLocaleString('id-ID')}</td>
        <td>
          <button class="btn btn-primary btn-sm edit-btn"><i class="fas fa-edit"></i></button>
          <button class="btn btn-danger btn-sm delete-btn"><i class="fas fa-trash"></i></button>
        </td>
      </tr>`;
    table.insertAdjacentHTML('beforeend', row);
    this.reset();
    bootstrap.Modal.getInstance(document.getElementById('tambahBiayaModal')).hide();
  });

  // Hapus baris
  document.body.addEventListener('click', function(e) {
    if (e.target.closest('.delete-btn')) {
      e.target.closest('tr').remove();
    }
  });
});
</script>
