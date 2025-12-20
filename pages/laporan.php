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

                  <!-- STATISTIK RINGKAS -->
                  <div class="row mb-4">
                    <div class="col-md-4">
                      <div class="card border-0 shadow-sm">
                        <div class="card-body">
                          <div class="row align-items-center">
                            <div class="col-8">
                              <h6 class="text-muted mb-2">Total Pendapatan</h6>
                              <h3 class="mb-0 text-success" id="statPendapatan">Rp 0</h3>
                            </div>
                            <div class="col-4 text-right">
                              <i class="fas fa-arrow-up" style="font-size: 30px; color: #28a745; opacity: 0.2;"></i>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="card border-0 shadow-sm">
                        <div class="card-body">
                          <div class="row align-items-center">
                            <div class="col-8">
                              <h6 class="text-muted mb-2">Total Pengeluaran</h6>
                              <h3 class="mb-0 text-danger" id="statPengeluaran">Rp 0</h3>
                            </div>
                            <div class="col-4 text-right">
                              <i class="fas fa-arrow-down" style="font-size: 30px; color: #dc3545; opacity: 0.2;"></i>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="card border-0 shadow-sm">
                        <div class="card-body">
                          <div class="row align-items-center">
                            <div class="col-8">
                              <h6 class="text-muted mb-2">Keuntungan Bersih</h6>
                              <h3 class="mb-0" id="statKeuntungan" style="color: #2196F3;">Rp 0</h3>
                            </div>
                            <div class="col-4 text-right">
                              <i class="fas fa-chart-line" style="font-size: 30px; color: #2196F3; opacity: 0.2;"></i>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="card mb-3">
                    <div class="card-body">
                      <form id="filterForm" class="row g-3">
                        <div class="col-sm-3">
                          <label class="form-label">Tampilkan</label>
                          <select id="filterLaporan" class="form-control">
                            <option value="semua">Pendapatan & Pengeluaran</option>
                            <option value="pendapatan">Pendapatan</option>
                            <option value="pengeluaran">Pengeluaran</option>
                          </select>
                        </div>
                        <div class="col-sm-3">
                          <label class="form-label">Dari Tanggal</label>
                          <input type="date" id="tanggal_mulai" class="form-control">
                        </div>
                        <div class="col-sm-3">
                          <label class="form-label">Sampai Tanggal</label>
                          <input type="date" id="tanggal_selesai" class="form-control">
                        </div>
                        <div class="col-sm-3 d-flex align-items-end gap-2">
                          <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter"></i> Filter
                          </button>
                          <button type="button" class="btn btn-danger" id="btnExportPDF">
                            <i class="fas fa-file-pdf"></i> PDF
                          </button>
                          <button type="button" class="btn btn-secondary" id="btnPrint">
                            <i class="fas fa-print"></i> Cetak
                          </button>
                        </div>
                      </form>
                    </div>
                  </div>

                  <!-- GRAFIK -->
                  <div class="card mb-4">
                    <div class="card-header">
                      Grafik Keuangan (<span id="judulGrafik">Pendapatan vs Pengeluaran</span>)
                    </div>
                    <div class="card-body">
                      <canvas id="chartKeuangan" height="100"></canvas>
                    </div>
                  </div>

                  <!-- TAB LAPORAN -->
                  <ul class="nav nav-tabs" id="laporanTab" role="tablist">
                    <li class="nav-item laporan-tab-item" id="tab-pendapatan-item">
                      <button class="nav-link active" id="pendapatan-tab" data-bs-toggle="tab" data-bs-target="#pendapatan" type="button">
                        <i class="fas fa-arrow-up text-success"></i> Pendapatan
                      </button>
                    </li>
                    <li class="nav-item laporan-tab-item" id="tab-pengeluaran-item">
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

              // set default tanggal (setahun ke belakang)
              (function setDefaultDates() {
                const startEl = document.getElementById('tanggal_mulai');
                const endEl = document.getElementById('tanggal_selesai');
                const today = new Date();
                const past = new Date();
                past.setFullYear(past.getFullYear() - 1); // 1 tahun ke belakang
                if (!startEl.value) startEl.value = past.toISOString().slice(0, 10);
                if (!endEl.value) endEl.value = today.toISOString().slice(0, 10);
              })();

              async function safeJson(res) {
                const text = await res.text();
                try {
                  return JSON.parse(text);
                } catch (e) {
                  return {
                    success: false,
                    message: text
                  }
                }
              }

              async function loadLaporan() {
                const mulai = document.getElementById("tanggal_mulai").value || '';
                const selesai = document.getElementById("tanggal_selesai").value || '';

                const url = `<?= BASE_URL ?>/api/keuangan.php?mulai=${encodeURIComponent(mulai)}&selesai=${encodeURIComponent(selesai)}`;

                try {
                  const res = await fetch(url, {
                    credentials: 'same-origin'
                  });
                  const data = await safeJson(res);
                  console.log('API Response:', data);

                  if (!data || (data.success === false)) {
                    console.error('API error', data.message || data);
                    // show empty tables
                    updateTabVisibility();
                    renderTable({
                      pendapatan: [],
                      pengeluaran: [],
                      total_pendapatan: 0,
                      total_pengeluaran: 0,
                      keuntungan_bersih: 0
                    });
                    renderChart({
                      pendapatan: [],
                      pengeluaran: [],
                      total_pendapatan: 0,
                      total_pengeluaran: 0
                    });
                    return;
                  }

                  // ensure arrays exist
                  data.pendapatan = Array.isArray(data.pendapatan) ? data.pendapatan : [];
                  data.pengeluaran = Array.isArray(data.pengeluaran) ? data.pengeluaran : [];

                  updateTabVisibility();
                  renderTable(data);
                  renderChart(data);
                } catch (e) {
                  console.error(e);
                  updateTabVisibility();
                  renderTable({
                    pendapatan: [],
                    pengeluaran: [],
                    total_pendapatan: 0,
                    total_pengeluaran: 0,
                    keuntungan_bersih: 0
                  });
                  renderChart({
                    pendapatan: [],
                    pengeluaran: [],
                    total_pendapatan: 0,
                    total_pengeluaran: 0
                  });
                }
              }

              function formatRp(v) {
                return 'Rp ' + Number(v || 0).toLocaleString('id-ID');
              }

              function getFilterLaporan() {
                return document.getElementById('filterLaporan').value;
              }

              function updateTabVisibility() {
                const filter = getFilterLaporan();
                const tabPendapatanItem = document.getElementById('tab-pendapatan-item');
                const tabPengeluaranItem = document.getElementById('tab-pengeluaran-item');
                const pendapatanTab = document.getElementById('pendapatan-tab');
                const pengeluaranTab = document.getElementById('pengeluaran-tab');
                const pendapatanPane = document.getElementById('pendapatan');
                const pengeluaranPane = document.getElementById('pengeluaran');

                if (filter === 'pendapatan') {
                  tabPendapatanItem.style.display = 'block';
                  tabPengeluaranItem.style.display = 'none';
                  pendapatanTab.classList.add('active');
                  pengeluaranTab.classList.remove('active');
                  pendapatanPane.classList.add('show', 'active');
                  pengeluaranPane.classList.remove('show', 'active');
                } else if (filter === 'pengeluaran') {
                  tabPendapatanItem.style.display = 'none';
                  tabPengeluaranItem.style.display = 'block';
                  pendapatanTab.classList.remove('active');
                  pengeluaranTab.classList.add('active');
                  pendapatanPane.classList.remove('show', 'active');
                  pengeluaranPane.classList.add('show', 'active');
                } else {
                  // semua (both tabs visible)
                  tabPendapatanItem.style.display = 'block';
                  tabPengeluaranItem.style.display = 'block';
                  pendapatanTab.classList.add('active');
                  pengeluaranTab.classList.remove('active');
                  pendapatanPane.classList.add('show', 'active');
                  pengeluaranPane.classList.remove('show', 'active');
                }
              }

              function renderTable(data) {
                const filter = getFilterLaporan();
                console.log('renderTable called with filter:', filter, 'data:', data);

                // Tabel Pendapatan
                const tP = document.getElementById("tPendapatan");
                tP.innerHTML = "";
                // Render pendapatan jika filter bukan 'pengeluaran'
                if (filter !== 'pengeluaran') {
                  const pendapatanData = Array.isArray(data.pendapatan) ? data.pendapatan : [];
                  console.log('Pendapatan data:', pendapatanData);
                  if (!pendapatanData.length) {
                    tP.innerHTML = '<tr><td colspan="5" class="text-center text-muted">Tidak ada data pendapatan</td></tr>';
                  } else {
                    pendapatanData.forEach((row, i) => {
                      tP.insertAdjacentHTML('beforeend', `
                        <tr>
                          <td>${i+1}</td>
                          <td>${row.tanggal}</td>
                          <td>${row.kategori}</td>
                          <td>${row.keterangan}</td>
                          <td class="text-end">${formatRp(row.jumlah)}</td>
                        </tr>
                      `);
                    });
                  }
                }
                document.getElementById("totalPendapatan").innerHTML = formatRp(data.total_pendapatan || 0);

                // Tabel Pengeluaran
                const tX = document.getElementById("tPengeluaran");
                tX.innerHTML = "";
                // Render pengeluaran jika filter bukan 'pendapatan'
                if (filter !== 'pendapatan') {
                  const pengeluaranData = Array.isArray(data.pengeluaran) ? data.pengeluaran : [];
                  console.log('Pengeluaran data:', pengeluaranData);
                  if (!pengeluaranData.length) {
                    tX.innerHTML = '<tr><td colspan="5" class="text-center text-muted">Tidak ada data pengeluaran</td></tr>';
                  } else {
                    pengeluaranData.forEach((row, i) => {
                      tX.insertAdjacentHTML('beforeend', `
                        <tr>
                          <td>${i+1}</td>
                          <td>${row.tanggal}</td>
                          <td>${row.kategori}</td>
                          <td>${row.keterangan}</td>
                          <td class="text-end">${formatRp(row.jumlah)}</td>
                        </tr>
                      `);
                    });
                  }
                }
                document.getElementById("totalPengeluaran").innerHTML = formatRp(data.total_pengeluaran || 0);

                // Update statistik di kartu
                updateStatistics(data);
              }

              function updateStatistics(data) {
                const filter = getFilterLaporan();

                if (filter === 'pendapatan') {
                  document.getElementById("statPendapatan").innerHTML = formatRp(data.total_pendapatan || 0);
                  document.getElementById("statPengeluaran").innerHTML = '0';
                  document.getElementById("statKeuntungan").innerHTML = formatRp(data.total_pendapatan || 0);
                } else if (filter === 'pengeluaran') {
                  document.getElementById("statPendapatan").innerHTML = '0';
                  document.getElementById("statPengeluaran").innerHTML = formatRp(data.total_pengeluaran || 0);
                  document.getElementById("statKeuntungan").innerHTML = formatRp(-(data.total_pengeluaran || 0));
                  document.getElementById("statKeuntungan").style.color = '#dc3545';
                } else {
                  document.getElementById("statPendapatan").innerHTML = formatRp(data.total_pendapatan || 0);
                  document.getElementById("statPengeluaran").innerHTML = formatRp(data.total_pengeluaran || 0);

                  const keuntungan = (data.total_pendapatan || 0) - (data.total_pengeluaran || 0);
                  const statKeuntungan = document.getElementById("statKeuntungan");
                  statKeuntungan.innerHTML = formatRp(keuntungan);

                  if (keuntungan >= 0) {
                    statKeuntungan.style.color = '#28a745';
                  } else {
                    statKeuntungan.style.color = '#dc3545';
                  }
                }
              }

              function renderChart(data) {
                const ctx = document.getElementById("chartKeuangan");
                const filter = getFilterLaporan();
                if (chart) chart.destroy();

                // Update judul grafik
                let judulGrafik = 'Pendapatan vs Pengeluaran';
                if (filter === 'pendapatan') {
                  judulGrafik = 'Pendapatan';
                } else if (filter === 'pengeluaran') {
                  judulGrafik = 'Pengeluaran';
                }
                document.getElementById('judulGrafik').textContent = judulGrafik;

                // build date map combining pendapatan dan pengeluaran
                const map = {};

                if (filter !== 'pengeluaran') {
                  data.pendapatan.forEach(r => {
                    const d = r.tanggal;
                    map[d] = map[d] || {
                      pendapatan: 0,
                      pengeluaran: 0
                    };
                    map[d].pendapatan += Number(r.jumlah || 0);
                  });
                }

                if (filter !== 'pendapatan') {
                  data.pengeluaran.forEach(r => {
                    const d = r.tanggal;
                    map[d] = map[d] || {
                      pendapatan: 0,
                      pengeluaran: 0
                    };
                    map[d].pengeluaran += Number(r.jumlah || 0);
                  });
                }

                // if no per-date data, fallback to totals
                const dates = Object.keys(map).sort();
                if (!dates.length) {
                  if (filter === 'pendapatan') {
                    chart = new Chart(ctx, {
                      type: 'bar',
                      data: {
                        labels: ['Pendapatan'],
                        datasets: [{
                          label: 'Nominal (Rp)',
                          data: [data.total_pendapatan || 0],
                          backgroundColor: ['#28a745']
                        }]
                      },
                      options: {
                        responsive: true,
                        maintainAspectRatio: false
                      }
                    });
                  } else if (filter === 'pengeluaran') {
                    chart = new Chart(ctx, {
                      type: 'bar',
                      data: {
                        labels: ['Pengeluaran'],
                        datasets: [{
                          label: 'Nominal (Rp)',
                          data: [data.total_pengeluaran || 0],
                          backgroundColor: ['#dc3545']
                        }]
                      },
                      options: {
                        responsive: true,
                        maintainAspectRatio: false
                      }
                    });
                  } else {
                    chart = new Chart(ctx, {
                      type: 'bar',
                      data: {
                        labels: ['Pendapatan', 'Pengeluaran'],
                        datasets: [{
                          label: 'Nominal (Rp)',
                          data: [data.total_pendapatan || 0, data.total_pengeluaran || 0],
                          backgroundColor: ['#28a745', '#dc3545']
                        }]
                      },
                      options: {
                        responsive: true,
                        maintainAspectRatio: false
                      }
                    });
                  }
                  return;
                }

                const labels = dates;
                let datasets = [];

                if (filter !== 'pengeluaran') {
                  const pend = dates.map(d => map[d].pendapatan);
                  datasets.push({
                    label: 'Pendapatan',
                    data: pend,
                    backgroundColor: '#28a745'
                  });
                }

                if (filter !== 'pendapatan') {
                  const peng = dates.map(d => map[d].pengeluaran);
                  datasets.push({
                    label: 'Pengeluaran',
                    data: peng,
                    backgroundColor: '#dc3545'
                  });
                }

                chart = new Chart(ctx, {
                  type: 'bar',
                  data: {
                    labels: labels,
                    datasets: datasets
                  },
                  options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                      mode: 'index',
                      intersect: false
                    },
                    stacked: false,
                    scales: {
                      y: {
                        ticks: {
                          callback: v => 'Rp ' + Number(v).toLocaleString('id-ID')
                        }
                      }
                    }
                  }
                });
              }

              // Event Filter
              document.getElementById("filterForm").addEventListener("submit", function(e) {
                e.preventDefault();
                loadLaporan();
              });

              // Event untuk pilihan laporan (Pendapatan/Pengeluaran/Semua)
              document.getElementById('filterLaporan').addEventListener('change', function() {
                updateTabVisibility();
                loadLaporan();
              });

              // initial load
              loadLaporan();
            </script>

            <!-- SweetAlert & Print Handler -->
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <script>
              document.getElementById('btnExportPDF').addEventListener('click', function() {
                const mulai = document.getElementById('tanggal_mulai').value || '';
                const selesai = document.getElementById('tanggal_selesai').value || '';
                Swal.fire({
                  title: 'Export ke PDF?',
                  text: 'Laporan akan diunduh dalam format PDF',
                  icon: 'question',
                  showCancelButton: true,
                  confirmButtonText: 'Export',
                  cancelButtonText: 'Batal'
                }).then((r) => {
                  if (r.isConfirmed) {
                    window.location.href = `export_laporan_pdf.php?mulai=${encodeURIComponent(mulai)}&selesai=${encodeURIComponent(selesai)}`;
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