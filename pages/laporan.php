<?php
// include config + layout fragments
include __DIR__ . '/../config/config.php';
include __DIR__ . '/../includes/head.php';        // DOCTYPE + <head> + open <body>
include __DIR__ . '/../includes/navigation.php';  // sidebar
include __DIR__ . '/../includes/topbar.php';      // topbar
?>
<div class="pcoded-main-container">
    <div class="pcoded-wrapper">
        <div class="pcoded-content">
            <div class="pcoded-inner-content">
                <div class="main-body">
                    <div class="page-wrapper">

                        <!-- page header / breadcrumb -->
                        <div class="page-header">
                            <div class="page-block">
                                <div class="row align-items-center">
                                    <div class="col-md-12">
                                      <div class="page-header-title">
                                       <h5>Laporan</h5>
                                      </div>
                                        <ul class="breadcrumb">
                                            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/index.php"><i class="fas fa-home"></i></a></li>
                                            <li class="breadcrumb-item"><a href="#!">Laporan</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
<div class="content-wrapper">
  <!-- Header -->
  

  <!-- Konten -->
  <section class="content">
    <div class="container-fluid">
      <!-- Filter -->
      <div class="card mb-3">
        <div class="card-body">
          <form class="row g-3">
            <div class="col-md-3">
              <label for="tanggal_mulai" class="form-label">Dari Tanggal</label>
              <input type="date" id="tanggal_mulai" class="form-control">
            </div>
            <div class="col-md-3">
              <label for="tanggal_selesai" class="form-label">Sampai Tanggal</label>
              <input type="date" id="tanggal_selesai" class="form-control">
            </div>
            <div class="col-md-3">
              <label for="kategori" class="form-label">Kategori</label>
              <select id="kategori" class="form-select">
                <option value="">Semua</option>
                <option value="pendapatan">Pendapatan</option>
                <option value="pengeluaran">Pengeluaran</option>
              </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
              <button type="submit" class="btn btn-primary me-2"><i class="fas fa-filter"></i> Filter</button>
              <button type="button" id="btnExportPDF" class="btn btn-danger me-2"><i class="fas fa-file-pdf"></i> Export PDF</button>
              <button type="button" id="btnPrint" class="btn btn-secondary"><i class="fas fa-print"></i> Cetak</button>
            </div>
          </form>
        </div>
      </div>

      <!-- Tab Navigasi -->
      <ul class="nav nav-tabs" id="laporanTab" role="tablist">
        <li class="nav-item" role="presentation">
          <button class="nav-link active" id="pendapatan-tab" data-bs-toggle="tab" data-bs-target="#pendapatan" type="button" role="tab" aria-controls="pendapatan" aria-selected="true">
            <i class="fas fa-arrow-up text-success"></i> Pendapatan
          </button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link" id="pengeluaran-tab" data-bs-toggle="tab" data-bs-target="#pengeluaran" type="button" role="tab" aria-controls="pengeluaran" aria-selected="false">
            <i class="fas fa-arrow-down text-danger"></i> Pengeluaran
          </button>
        </li>
      </ul>

      <!-- Isi Tab -->
      <div class="tab-content" id="laporanTabContent">
        <!-- TAB PENDAPATAN -->
        <div class="tab-pane fade show active" id="pendapatan" role="tabpanel" aria-labelledby="pendapatan-tab">
          <div class="card mt-3">
            <div class="card-body table-responsive">
              <table class="table table-bordered table-striped">
                <thead class="table-success">
                  <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Kategori</th>
                    <th>Keterangan</th>
                    <th>Jumlah</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>1</td>
                    <td>2025-10-10</td>
                    <td>Penjualan</td>
                    <td>Penjualan Mie Ayam</td>
                    <td>Rp 500.000</td>
                  </tr>
                  <tr>
                    <td>2</td>
                    <td>2025-10-12</td>
                    <td>Lain-lain</td>
                    <td>Pendapatan Tambahan</td>
                    <td>Rp 200.000</td>
                  </tr>
                </tbody>
                <tfoot class="table-light">
                  <tr>
                    <th colspan="4" class="text-end">Total Pendapatan:</th>
                    <th>Rp 700.000</th>
                  </tr>
                </tfoot>
              </table>
            </div>
          </div>
        </div>

        <!-- TAB PENGELUARAN -->
        <div class="tab-pane fade" id="pengeluaran" role="tabpanel" aria-labelledby="pengeluaran-tab">
          <div class="card mt-3">
            <div class="card-body table-responsive">
              <table class="table table-bordered table-striped">
                <thead class="table-danger">
                  <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Kategori</th>
                    <th>Keterangan</th>
                    <th>Jumlah</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>1</td>
                    <td>2025-10-11</td>
                    <td>Bahan Baku</td>
                    <td>Ayam dan Bumbu</td>
                    <td>Rp 300.000</td>
                  </tr>
                  <tr>
                    <td>2</td>
                    <td>2025-10-13</td>
                    <td>Operasional</td>
                    <td>Listrik dan Air</td>
                    <td>Rp 150.000</td>
                  </tr>
                </tbody>
                <tfoot class="table-light">
                  <tr>
                    <th colspan="4" class="text-end">Total Pengeluaran:</th>
                    <th>Rp 450.000</th>
                  </tr>
                </tfoot>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>

<!-- JavaScript SweetAlert & Aksi -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  document.getElementById('btnExportPDF').addEventListener('click', function() {
    Swal.fire({
      title: 'Export ke PDF?',
      text: 'File PDF laporan akan diunduh.',
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Ya, export',
      cancelButtonText: 'Batal'
    }).then((result) => {
      if (result.isConfirmed) {
        Swal.fire('Berhasil!', 'Laporan berhasil diexport ke PDF.', 'success');
      }
    });
  });

  document.getElementById('btnPrint').addEventListener('click', function() {
    Swal.fire({
      title: 'Cetak Laporan?',
      text: 'Laporan akan dicetak menggunakan printer Anda.',
      icon: 'info',
      showCancelButton: true,
      confirmButtonText: 'Cetak Sekarang',
      cancelButtonText: 'Batal'
    }).then((result) => {
      if (result.isConfirmed) {
        window.print();
      }
    });
  });
</script>
<script src="../assets/js/vendor-all.min.js"></script>
<script src="../assets/plugins/bootstrap/js/bootstrap.min.js"></script>
<script src="../assets/js/pcoded.min.js"></script>