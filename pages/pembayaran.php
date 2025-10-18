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

            <!-- Page Header -->
            <div class="page-header">
              <div class="page-block">
                <div class="row align-items-center">
                  <div class="col-md-12">
                    <div class="page-header-title">
                      <h5>Kelola Metode Pembayaran</h5>
                    </div>
                    <ul class="breadcrumb">
                      <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/index.php"><i class="fas fa-home"></i></a></li>
                      <li class="breadcrumb-item"><a href="#!">Pembayaran</a></li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>

            <!-- Konten -->
            <section class="content">
              <div class="container-fluid">
                <div class="card">
                  <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Daftar Metode Pembayaran</h3>
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#tambahPembayaranModal">
                      <i class="fas fa-plus"></i> Tambah Metode
                    </button>
                  </div>

                  <div class="card-body table-responsive">
                    <table class="table table-bordered table-striped">
                      <thead class="table-success">
                        <tr>
                          <th>No</th>
                          <th>Nama Metode</th>
                          <th>Jenis</th>
                          <th>Tujuan Pembayaran</th>
                          <th>Keterangan</th>
                          <th>Aksi</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td>1</td>
                          <td>Bank BCA</td>
                          <td>Transfer Bank</td>
                          <td>No. Rekening: 1234567890 a.n. Warung Mie Ayam</td>
                          <td>Transfer antar bank dikenakan biaya admin</td>
                          <td>
                            <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editPembayaranModal1">
                              <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger">
                              <i class="fas fa-trash"></i>
                            </button>
                          </td>
                        </tr>

                        <tr>
                          <td>2</td>
                          <td>DANA</td>
                          <td>E-Wallet</td>
                          <td>No. HP: 081234567890</td>
                          <td>Bisa kirim QR ke pelanggan</td>
                          <td>
                            <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editPembayaranModal2">
                              <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger">
                              <i class="fas fa-trash"></i>
                            </button>
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

<!-- Modal Tambah -->
<div class="modal fade" id="tambahPembayaranModal" tabindex="-1">
  <div class="modal-dialog">
    <form method="post" action="tambah_pembayaran.php">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Tambah Metode Pembayaran</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Nama Metode</label>
            <input type="text" name="nama_metode" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Jenis Pembayaran</label>
            <select name="jenis" class="form-select" required>
              <option value="">-- Pilih Jenis --</option>
              <option value="Transfer Bank">Transfer Bank</option>
              <option value="E-Wallet">E-Wallet</option>
              <option value="COD">Cash On Delivery (COD)</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Tujuan Pembayaran</label>
            <textarea name="tujuan" class="form-control" rows="2" placeholder="Contoh: No. Rekening atau No. HP"></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">Keterangan</label>
            <textarea name="keterangan" class="form-control" rows="2"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Simpan</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Modal Edit (contoh dummy) -->
<div class="modal fade" id="editPembayaranModal1" tabindex="-1">
  <div class="modal-dialog">
    <form method="post" action="edit_pembayaran.php">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Edit Metode Pembayaran</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id_pembayaran" value="1">
          <div class="mb-3">
            <label class="form-label">Nama Metode</label>
            <input type="text" name="nama_metode" class="form-control" value="Bank BCA">
          </div>
          <div class="mb-3">
            <label class="form-label">Jenis Pembayaran</label>
            <select name="jenis" class="form-select">
              <option selected>Transfer Bank</option>
              <option>E-Wallet</option>
              <option>COD</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Tujuan Pembayaran</label>
            <textarea name="tujuan" class="form-control" rows="2">No. Rekening: 1234567890 a.n. Warung Mie Ayam</textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">Keterangan</label>
            <textarea name="keterangan" class="form-control" rows="2">Transfer antar bank dikenakan biaya admin</textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </div>
      </div>
    </form>
  </div>
</div>

<script src="../assets/js/vendor-all.min.js"></script>
<script src="../assets/plugins/bootstrap/js/bootstrap.min.js"></script>
<script src="../assets/js/pcoded.min.js"></script>
