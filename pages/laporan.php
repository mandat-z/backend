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

            <!-- PAGE HEADER -->
            <div class="page-header">
              <div class="page-block">
                <div class="row align-items-center">
                  <div class="col-md-12">
                    <div class="page-header-title">
                      <h5>Laporan Keuangan</h5>
                    </div>
                    <ul class="breadcrumb">
                      <li class="breadcrumb-item">
                        <a href="<?= BASE_URL ?>/index.php"><i class="fas fa-home"></i></a>
                      </li>
                      <li class="breadcrumb-item">Laporan</li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>

            <!-- CONTENT -->
            <div class="content-wrapper">
              <section class="content">
                <div class="container-fluid">

                  <!-- FILTER -->
                  <div class="card mb-3">
                    <div class="card-body">
                      <form id="filterForm" class="row g-3">
                        <div class="col-md-3">
                          <label class="form-label">Dari Tanggal</label>
                          <input type="date" id="tanggal_mulai" class="form-control">
                        </div>
                        <div class="col-md-3">
                          <label class="form-label">Sampai Tanggal</label>
                          <input type="date" id="tanggal_selesai" class="form-control">
                        </div>
                        <div class="col-md-3">
                          <label class="form-label">Kategori</label>
                          <select id="kategori" class="form-select">
                            <option value="">Semua</option>
                            <option value="pendapatan">Pendapatan</option>
                            <option value="pengeluaran">Pengeluaran</option>
                          </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                          <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-filter"></i> Filter
                          </button>
                          <button type="button" id="btnExportPDF" class="btn btn-danger me-2">
                            <i class="fas fa-file-pdf"></i> Export PDF
                          </button>
                          <button type="button" id="btnPrint" class="btn btn-secondary">
                            <i class="fas fa-print"></i> Cetak
                          </button>
                        </div>
                      </form>
                    </div>
                  </div>

                  <!-- GRAFIK -->
                  <div class="card mb-4">
                    <div class="card-header">
                      Grafik Keuangan (Pendapatan vs Pengeluaran)
                    </div>
                    <div class="card-body">
                      <canvas id="chartKeuangan" height="100"></canvas>
                    </div>
                  </div>

                  <!-- TAB LAPORAN -->
                  <ul class="nav nav-tabs" id="laporanTab" role="tablist">
                    <li class="nav-item">
                      <button class="nav-link active" id="pendapatan-tab" data-bs-toggle="tab" data-bs-target="#pendapatan" type="button">
                        <i class="fas fa-arrow-up text-success"></i> Pendapatan
                      </button>
                    </li>
                    <li class="nav-item">
                      <button class="nav-link" id="pengeluaran-tab" data-bs-toggle="tab" data-bs-target="#pengeluaran" type="button">
                        <i class="fas fa-arrow-down text-danger"></i> Pengeluaran
                      </button>
                    </li>
                  </ul>

                  <div class="tab-content" id="laporanContent">

                    <!-- TAB PENDAPATAN -->
                    <div class="tab-pane fade show active" id="pendapatan">
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
                            <tbody id="tPendapatan"></tbody>
                            <tfoot class="table-light">
                              <tr>
                                <th colspan="4" class="text-end">Total Pendapatan:</th>
                                <th id="totalPendapatan">Rp 0</th>
                              </tr>
                            </tfoot>
                          </table>
                        </div>
                      </div>
                    </div>

                    <!-- TAB PENGELUARAN -->
                    <div class="tab-pane fade" id="pengeluaran">
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
                            <tbody id="tPengeluaran"></tbody>
                            <tfoot class="table-light">
                              <tr>
                                <th colspan="4" class="text-end">Total Pengeluaran:</th>
                                <th id="totalPengeluaran">Rp 0</th>
                              </tr>
                            </tfoot>
                          </table>
                        </div>
                      </div>
                    </div>

                  </div> <!-- END TAB CONTENT -->

                </div>
              </section>
            </div>

            <!-- JS UTAMA -->
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
              let chart = null;

              function loadLaporan() {
                const mulai = document.getElementById("tanggal_mulai").value;
                const selesai = document.getElementById("tanggal_selesai").value;
                const kategori = document.getElementById("kategori").value;

                fetch(`<?= BASE_URL ?>/api/keuangan.php?mulai=${mulai}&selesai=${selesai}&kategori=${kategori}`)
                  .then(res => res.json())
                  .then(data => {
                    renderTable(data);
                    renderChart(data);
                  });
              }

              function renderTable(data) {
                // Tabel Pendapatan
                const tP = document.getElementById("tPendapatan");
                tP.innerHTML = "";
                data.pendapatan.forEach((row, i) => {
                  tP.innerHTML += `
                                    <tr>
                                        <td>${i+1}</td>
                                        <td>${row.tanggal}</td>
                                        <td>${row.kategori}</td>
                                        <td>${row.keterangan}</td>
                                        <td>Rp ${Number(row.jumlah).toLocaleString()}</td>
                                    </tr>
                                `;
                });
                document.getElementById("totalPendapatan").innerHTML = "Rp " + Number(data.total_pendapatan).toLocaleString();

                // Tabel Pengeluaran
                const tX = document.getElementById("tPengeluaran");
                tX.innerHTML = "";
                data.pengeluaran.forEach((row, i) => {
                  tX.innerHTML += `
                                    <tr>
                                        <td>${i+1}</td>
                                        <td>${row.tanggal}</td>
                                        <td>${row.kategori}</td>
                                        <td>${row.keterangan}</td>
                                        <td>Rp ${Number(row.jumlah).toLocaleString()}</td>
                                    </tr>
                                `;
                });
                document.getElementById("totalPengeluaran").innerHTML = "Rp " + Number(data.total_pengeluaran).toLocaleString();
              }

              function renderChart(data) {
                const ctx = document.getElementById("chartKeuangan");

                if (chart) chart.destroy();

                chart = new Chart(ctx, {
                  type: 'bar',
                  data: {
                    labels: ["Pendapatan", "Pengeluaran"],
                    datasets: [{
                      label: "Nominal (Rp)",
                      data: [data.total_pendapatan, data.total_pengeluaran],
                      backgroundColor: ['#28a745', '#dc3545']
                    }]
                  }
                });
              }

              // Event Filter
              document.getElementById("filterForm").addEventListener("submit", function(e) {
                e.preventDefault();
                loadLaporan();
              });

              // Load awal
              loadLaporan();
            </script>

            <!-- SweetAlert Export & Print -->
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <script>
              document.getElementById('btnExportPDF').addEventListener('click', function() {
                Swal.fire({
                  title: 'Export ke PDF?',
                  icon: 'question',
                  showCancelButton: true
                }).then((r) => {
                  if (r.isConfirmed) {
                    window.location.href = "export_laporan_pdf.php";
                  }
                });
              });

              document.getElementById('btnPrint').addEventListener('click', function() {
                window.print();
              });
            </script>

            <script src="../assets/js/vendor-all.min.js"></script>
            <script src="../assets/plugins/bootstrap/js/bootstrap.min.js"></script>
            <script src="../assets/js/pcoded.min.js"></script>

          </div><!-- page-wrapper -->
        </div><!-- main-body -->
      </div><!-- inner -->
    </div><!-- content -->
  </div><!-- wrapper -->
</div><!-- container -->