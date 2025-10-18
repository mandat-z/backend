<?php
include __DIR__ . '/../config/config.php';
include __DIR__ . '/../includes/head.php';
include __DIR__ . '/../includes/navigation.php';
include __DIR__ . '/../includes/topbar.php';
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
                      <h5>Daftar Pelanggan</h5>
                    </div>
                    <ul class="breadcrumb">
                      <li class="breadcrumb-item"><a href="../index.php"><i class="fas fa-home"></i></a></li>
                      <li class="breadcrumb-item"><a href="#!">Pelanggan</a></li>
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
                      <button class="btn btn-primary" data-toggle="modal" data-target="#modalAddPelanggan">
                        <i class="fas fa-plus"></i> Tambah User
                      </button>
                    </div>
                    <div>
                      <input type="text" id="searchPelanggan" class="form-control" placeholder="Cari pelanggan..." style="width:260px;">
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- daftar pelanggan -->
            <div class="row">
              <div class="col-md-12">
                <div class="card table-card">
                  <div class="card-header">
                    <h5>Daftar User</h5>
                  </div>
                  <div class="card-body">
                    <div class="table-responsive">
                      <table class="table table-hover" id="pelangganTable">
                        <thead>
                          <tr>
                            <th>#</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Telepon</th>
                            <th>Alamat</th>
                            <th>Tanggal Bergabung</th>
                            <th>Aksi</th>
                          </tr>
                        </thead>
                        <tbody>
                          <!-- Contoh data dummy -->
                          <tr>
                            <td>1</td>
                            <td>Amanda Aura</td>
                            <td>amanda@example.com</td>
                            <td>08123456789</td>
                            <td>Depok</td>
                            <td>2025-10-10</td>
                            <td>
                              <button class="btn btn-sm btn-warning" data-toggle="modal" data-target="#modalEditPelanggan"><i class="fas fa-edit"></i></button>
                              <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                              <button class="btn btn-sm btn-info" data-toggle="modal" data-target="#modalRiwayat"><i class="fas fa-list"></i></button>
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>

          </div> <!-- end page-wrapper -->
        </div>
      </div>
    </div>
  </div>
</div>
<!-- [ Main Content ] end -->

<!-- Modal Tambah Pelanggan -->
<div class="modal fade" id="modalAddPelanggan" tabindex="-1" aria-labelledby="modalAddPelangganLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form>
        <div class="modal-header">
          <h5 class="modal-title">Tambah User</h5>
          <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Nama Pelanggan</label>
            <input type="text" class="form-control" placeholder="Masukkan nama pelanggan">
          </div>
          <div class="form-group">
            <label>Email</label>
            <input type="email" class="form-control" placeholder="Masukkan email">
          </div>
          <div class="form-group">
            <label>Telepon</label>
            <input type="text" class="form-control" placeholder="Masukkan nomor telepon">
          </div>
          <div class="form-group">
            <label>Alamat</label>
            <textarea class="form-control" rows="2" placeholder="Masukkan alamat"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Edit Pelanggan -->
<div class="modal fade" id="modalEditPelanggan" tabindex="-1" aria-labelledby="modalEditPelangganLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form>
        <div class="modal-header">
          <h5 class="modal-title">Edit Pelanggan</h5>
          <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Nama Pelanggan</label>
            <input type="text" class="form-control" value="Amanda Aura">
          </div>
          <div class="form-group">
            <label>Email</label>
            <input type="email" class="form-control" value="amanda@example.com">
          </div>
          <div class="form-group">
            <label>Telepon</label>
            <input type="text" class="form-control" value="08123456789">
          </div>
          <div class="form-group">
            <label>Alamat</label>
            <textarea class="form-control" rows="2">Depok</textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Riwayat Pesanan -->
<div class="modal fade" id="modalRiwayat" tabindex="-1" aria-labelledby="modalRiwayatLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Riwayat Pesanan Pelanggan</h5>
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <div class="modal-body">
        <div class="table-responsive">
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>#</th>
                <th>Tanggal Pesanan</th>
                <th>Produk</th>
                <th>Jumlah</th>
                <th>Total Harga</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <!-- Contoh data dummy -->
              <tr>
                <td>1</td>
                <td>2025-10-14</td>
                <td>Mi Ayam Spesial</td>
                <td>2</td>
                <td>Rp 30.000</td>
                <td><span class="badge bg-success text-white">Selesai</span></td>
              </tr>
              <tr>
                <td>2</td>
                <td>2025-10-15</td>
                <td>Es Teh Manis</td>
                <td>3</td>
                <td>Rp 15.000</td>
                <td><span class="badge bg-warning text-dark">Diproses</span></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

<script src="../assets/js/vendor-all.min.js"></script>
<script src="../assets/plugins/bootstrap/js/bootstrap.min.js"></script>
<script src="../assets/js/pcoded.min.js"></script>
