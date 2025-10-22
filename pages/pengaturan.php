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

            <!-- Page Header -->
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

            <!-- Content -->
            <div class="row">
              <!-- Banner -->
              <div class="col-md-4">
                <div class="card text-center hover-shadow">
                  <div class="card-body">
                    <div class="mb-3"><i class="fas fa-images fa-3x text-primary"></i></div>
                    <h5 class="card-title">Banner Promo</h5>
                    <p class="text-muted">Kelola banner promosi yang tampil di halaman utama.</p>
                    <a href="banner.php" class="btn btn-primary">Kelola</a>
                  </div>
                </div>
              </div>

              <!-- FAQ -->
              <div class="col-md-4">
                <div class="card text-center hover-shadow">
                  <div class="card-body">
                    <div class="mb-3"><i class="fas fa-question-circle fa-3x text-success"></i></div>
                    <h5 class="card-title">FAQ</h5>
                    <p class="text-muted">Atur daftar pertanyaan umum untuk pengguna.</p>
                    <a href="faq.php" class="btn btn-success">Kelola</a>
                  </div>
                </div>
              </div>

              <!-- Help -->
              <div class="col-md-4">
                <div class="card text-center hover-shadow">
                  <div class="card-body">
                    <div class="mb-3"><i class="fas fa-book fa-3x text-info"></i></div>
                    <h5 class="card-title">Panduan / Help</h5>
                    <p class="text-muted">Tambah atau ubah panduan penggunaan sistem.</p>
                    <a href="help.php" class="btn btn-info">Kelola</a>
                  </div>
                </div>
              </div>

              <!-- Support -->
              <div class="col-md-4">
                <div class="card text-center hover-shadow">
                  <div class="card-body">
                    <div class="mb-3"><i class="fas fa-headset fa-3x text-warning"></i></div>
                    <h5 class="card-title">Kontak Support</h5>
                    <p class="text-muted">Edit informasi kontak bantuan dan layanan pelanggan.</p>
                    <a href="support.php" class="btn btn-warning text-white">Kelola</a>
                  </div>
                </div>
              </div>

              <!-- Info Toko -->
              <div class="col-md-4">
                <div class="card text-center hover-shadow">
                  <div class="card-body">
                    <div class="mb-3"><i class="fas fa-store fa-3x text-danger"></i></div>
                    <h5 class="card-title">Informasi Toko</h5>
                    <p class="text-muted">Perbarui nama toko, alamat, dan media sosial.</p>
                    <a href="info_toko.php" class="btn btn-danger">Kelola</a>
                  </div>
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

<!-- JS -->
<script src="../assets/js/vendor-all.min.js"></script>
<script src="../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/pcoded.min.js"></script>
