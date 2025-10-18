<?php
// include config + layout fragments
include __DIR__ . '/../config/config.php';
include __DIR__ . '/../includes/head.php';        // DOCTYPE + <head> + open <body>
include __DIR__ . '/../includes/navigation.php';  // sidebar
include __DIR__ . '/../includes/topbar.php';      // topbar
?>

<!-- [ Main Content ] start -->
<div class="pcoded-main-container">
    <div class="pcoded-wrapper">
        <div class="pcoded-content">
            <div class="pcoded-inner-content">
                <div class="main-body">
                    <div class="page-wrapper">

                      
        <div class="page-header">
          <div class="page-block">
            <div class="row align-items-center">
              <div class="col-md-12">
                <div class="page-header-title">
                  <h5>Pengaturan Website</h5>
                </div>
                <ul class="breadcrumb">
                  <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/index.php"><i class="fas fa-home"></i></a></li>
                  <li class="breadcrumb-item">Pengaturan</li>
                  <li class="breadcrumb-item active">Website</li>
                </ul>
              </div>
            </div>
          </div>
        </div>

        <!-- Tabs -->
        <ul class="nav nav-tabs mb-3" id="websiteSettingTabs">
          <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#tabBanner">Banner Promo</a></li>
          <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tabFAQ">FAQ</a></li>
          <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tabHelp">Help</a></li>
          <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tabSupport">Support</a></li>
          <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tabInfoToko">Info Toko</a></li>
        </ul>

        <div class="tab-content">

          <!-- Tab Banner -->
          <div class="tab-pane fade show active" id="tabBanner">
            <div class="card">
              <div class="card-header d-flex justify-content-between align-items-center">
                <h5>Daftar Banner Promo</h5>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalBanner"><i class="fas fa-plus"></i> Tambah Banner</button>
              </div>
              <div class="card-body table-responsive">
                <table class="table table-striped">
                  <thead class="table-success">
                    <tr><th>No</th><th>Judul</th><th>Gambar</th><th>Status</th><th>Aksi</th></tr>
                  </thead>
                  <tbody>
                    <tr><td>1</td><td>Promo Diskon 50%</td><td><img src="../assets/img/banner1.jpg" width="80"></td><td><span class="badge bg-success">Aktif</span></td><td><button class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></button> <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button></td></tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <!-- Tab FAQ -->
          <div class="tab-pane fade" id="tabFAQ">
            <div class="card">
              <div class="card-header d-flex justify-content-between align-items-center">
                <h5>Daftar FAQ</h5>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalFAQ"><i class="fas fa-plus"></i> Tambah FAQ</button>
              </div>
              <div class="card-body table-responsive">
                <table class="table table-striped">
                  <thead class="table-success"><tr><th>No</th><th>Pertanyaan</th><th>Jawaban</th><th>Status</th><th>Aksi</th></tr></thead>
                  <tbody>
                    <tr><td>1</td><td>Bagaimana cara memesan?</td><td>Anda bisa memesan melalui menu produk.</td><td><span class="badge bg-success">Aktif</span></td><td><button class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></button> <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button></td></tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <!-- Tab Help -->
          <div class="tab-pane fade" id="tabHelp">
            <div class="card">
              <div class="card-header d-flex justify-content-between align-items-center">
                <h5>Daftar Help / Panduan</h5>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalHelp"><i class="fas fa-plus"></i> Tambah Help</button>
              </div>
              <div class="card-body table-responsive">
                <table class="table table-striped">
                  <thead class="table-success"><tr><th>No</th><th>Judul</th><th>Deskripsi</th><th>Aksi</th></tr></thead>
                  <tbody>
                    <tr><td>1</td><td>Cara Melacak Pesanan</td><td>Buka menu Lacak Pesanan di website utama.</td><td><button class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></button> <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button></td></tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <!-- Tab Support -->
          <div class="tab-pane fade" id="tabSupport">
            <div class="card">
              <div class="card-header d-flex justify-content-between align-items-center">
                <h5>Kontak Support</h5>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalSupport"><i class="fas fa-edit"></i> Edit Support</button>
              </div>
              <div class="card-body">
                <p><strong>Email:</strong> support@tokoku.com</p>
                <p><strong>WhatsApp:</strong> +62 812-3456-7890</p>
                <p><strong>Jam Operasional:</strong> 08.00 - 20.00 WIB</p>
              </div>
            </div>
          </div>

          <!-- Tab Info Toko -->
          <div class="tab-pane fade" id="tabInfoToko">
            <div class="card">
              <div class="card-header d-flex justify-content-between align-items-center">
                <h5>Informasi Toko</h5>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalInfoToko"><i class="fas fa-edit"></i> Edit Info</button>
              </div>
              <div class="card-body">
                <p><strong>Nama Toko:</strong> J.Chicken Store</p>
                <p><strong>Alamat:</strong> Jalan Raya Sawangan No.12, Depok</p>
                <p><strong>Email:</strong> info@jchicken.id</p>
                <p><strong>Telepon:</strong> 021-1234567</p>
                <p><strong>Media Sosial:</strong> @jchicken.id</p>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>
</div> </div> <!-- Modal contoh --> <div class="modal fade" id="modalBanner" tabindex="-1"> <div class="modal-dialog"> <form> <div class="modal-content"> <div class="modal-header"><h5>Tambah Banner Promo</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div> <div class="modal-body"> <div class="mb-3"><label>Judul Banner</label><input type="text" class="form-control" required></div> <div class="mb-3"><label>Upload Gambar</label><input type="file" class="form-control"></div> <div class="mb-3"><label>Status</label><select class="form-control"><option>Aktif</option><option>Nonaktif</option></select></div> </div> <div class="modal-footer"><button type="submit" class="btn btn-success">Simpan</button></div> </div> </form> </div> </div> <!-- JS --> <script src="../assets/js/vendor-all.min.js"></script> <script src="../assets/plugins/bootstrap/js/bootstrap.min.js"></script> <script src="../assets/js/pcoded.min.js"></script> <script> // Responsive Tabs + Simulasi filter document.addEventListener('DOMContentLoaded', () => { console.log("Pengaturan Website aktif dan responsif!"); }); </script>

<script src="<?= ASSET ?>/js/vendor-all.min.js"></script>
<script src="<?= ASSET ?>/plugins/bootstrap/js/bootstrap.min.js"></script>
<script src="<?= ASSET ?>/js/pcoded.min.js"></script>
<!-- optional page chart script (template has example) -->
<script src="<?= ASSET ?>/js/pages/dashboard-analytics.js"></script>