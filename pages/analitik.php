<?php
include __DIR__ . '/../config/config.php';
include __DIR__ . '/../includes/head.php';
include __DIR__ . '/../includes/navigation.php';
include __DIR__ . '/../includes/topbar.php';
?>

<style>
    /* sederhana, stabil, tidak tergantung plugin tambahan */
    .kpi-card .card-body {
        padding: 18px;
    }

    .kpi-card h4 {
        margin: 0;
        font-weight: 700;
    }

    .kpi-card p {
        margin: 6px 0 0;
        color: #6c757d;
    }

    /* placeholder loading / error */
    .block-placeholder {
        height: 260px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #6c757d;
        background: #fafafa;
        border: 1px dashed #e1e1e1;
        border-radius: 6px;
    }

    /* tambahan styling chart modern */
    .chart-box {
        height: 320px;
        position: relative;
    }

    .chart-legend {
        display: flex;
        gap: 12px;
        align-items: center;
        margin-top: 12px;
    }

    .legend-dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        display: inline-block;
    }
</style>

<div class="pcoded-main-container">
    <div class="pcoded-wrapper">
        <div class="pcoded-content">
            <div class="pcoded-inner-content">
                <div class="main-body">
                    <div class="page-wrapper">

                        <!-- HEADER -->
                        <div class="page-header mb-4">
                            <div class="page-block">
                                <div class="row align-items-center">
                                    <div class="col-md-12">
                                        <div class="page-header-title">
                                            <h5>Dashboard Analitik</h5>
                                        </div>
                                        <ul class="breadcrumb">
                                            <li class="breadcrumb-item">
                                                <a href="<?= BASE_URL ?>/index.php"><i class="fas fa-home"></i></a>
                                            </li>
                                            <li class="breadcrumb-item">Analitik</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ALERT AREA -->
                        <div id="alertBox"></div>

                        <!-- KPI -->
                        <div class="row" id="kpiBox">
                            <div class="col-md-4">
                                <div class="card kpi-card text-center">
                                    <div class="card-body">
                                        <h4>0</h4>
                                        <p>Total User</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card kpi-card text-center">
                                    <div class="card-body">
                                        <h4>0</h4>
                                        <p>Total Order</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card kpi-card text-center">
                                    <div class="card-body">
                                        <h4>Rp 0</h4>
                                        <p>Total Omzet</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- CHARTS -->
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">User Baru (30 Hari)</h6>
                                    </div>
                                    <div class="card-body">
                                        <div id="userChart" class="block-placeholder">Memuat grafik...</div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">Omzet Penjualan (30 Hari)</h6>
                                    </div>
                                    <div class="card-body">
                                        <div id="salesChart" class="block-placeholder">Memuat grafik...</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- TABLE -->
                        <div class="card mt-4">
                            <div class="card-header">
                                <h6 class="mb-0">Produk Terlaris</h6>
                            </div>
                            <div class="card-body table-responsive">
                                <table class="table table-striped mb-0" id="produkTable">
                                    <thead>
                                        <tr>
                                            <th style="width:60px;">No</th>
                                            <th>Produk</th>
                                            <th style="width:120px;">Terjual</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td colspan="3" class="text-muted">Memuat...</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div> <!-- page-wrapper -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- gunakan Chart.js, lebih modern dan interaktif -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const API_URL = "<?= BASE_URL ?>/api/analytics.php";
    let userChartInst = null;
    let salesChartInst = null;

    function rupiah(n) {
        return 'Rp ' + Number(n || 0).toLocaleString('id-ID');
    }

    function showAlert(type, msg) {
        document.getElementById('alertBox').innerHTML =
            `<div class="alert alert-${type}">${msg}</div>`;
    }

    function setLoadingPlaceholders() {
        document.getElementById('userChart').className = 'block-placeholder';
        document.getElementById('salesChart').className = 'block-placeholder';
        document.getElementById('userChart').innerText = 'Memuat grafik...';
        document.getElementById('salesChart').innerText = 'Memuat grafik...';
    }

    function setEmptyChart(elId, text) {
        const el = document.getElementById(elId);
        el.className = 'block-placeholder';
        el.innerText = text || 'Tidak ada data';
    }

    function safeTableEmpty(text) {
        document.querySelector("#produkTable tbody").innerHTML =
            `<tr><td colspan="3" class="text-muted">${text || 'Tidak ada data'}</td></tr>`;
    }

    setLoadingPlaceholders();

    fetch(API_URL, {
            credentials: 'include'
        })
        .then(res => {
            if (!res.ok) throw new Error('HTTP ' + res.status);
            return res.json();
        })
        .then(res => {
            if (!res || res.success !== true) {
                const serverMsg = res && res.message ? res.message : 'Gagal memuat data analitik (akses/response tidak valid).';
                showAlert('warning', serverMsg);
                setEmptyChart('userChart', 'Tidak ada data');
                setEmptyChart('salesChart', 'Tidak ada data');
                safeTableEmpty('Tidak ada data');
                return;
            }

            // KPI
            document.getElementById("kpiBox").innerHTML = `
        <div class="col-md-4">
          <div class="card kpi-card text-center">
            <div class="card-body">
              <h4>${res.summary?.total_user ?? 0}</h4>
              <p>Total User</p>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card kpi-card text-center">
            <div class="card-body">
              <h4>${res.summary?.total_order ?? 0}</h4>
              <p>Total Order</p>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card kpi-card text-center">
            <div class="card-body">
              <h4>${rupiah(res.summary?.total_omzet ?? 0)}</h4>
              <p>Total Omzet</p>
            </div>
          </div>
        </div>
      `;

            // Data arrays
            const userData = Array.isArray(res.user) ? res.user : [];
            const salesData = Array.isArray(res.sales) ? res.sales : [];

            // USER chart (line)
            if (!userData.length) {
                setEmptyChart('userChart', 'Data user belum tersedia');
            } else {
                const container = document.getElementById('userChart');
                container.className = 'chart-box';
                container.innerHTML = '<canvas id="userCanvas"></canvas>';
                const ctx = document.getElementById('userCanvas').getContext('2d');

                const labels = userData.map(d => d.tanggal);
                const values = userData.map(d => Number(d.total || 0));

                if (userChartInst) userChartInst.destroy();
                userChartInst = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'User Baru',
                            data: values,
                            borderColor: 'rgb(102,126,234)',
                            backgroundColor: 'rgba(102,126,234,0.12)',
                            tension: 0.3,
                            pointRadius: 3,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            tooltip: {
                                mode: 'index',
                                intersect: false
                            }
                        },
                        scales: {
                            x: {
                                ticks: {
                                    color: '#666'
                                },
                                grid: {
                                    display: false
                                }
                            },
                            y: {
                                ticks: {
                                    color: '#666',
                                    beginAtZero: true
                                },
                                grid: {
                                    color: 'rgba(0,0,0,0.03)'
                                }
                            }
                        }
                    }
                });
            }

            // SALES chart (bar)
            if (!salesData.length) {
                setEmptyChart('salesChart', 'Data penjualan belum tersedia');
            } else {
                const container = document.getElementById('salesChart');
                container.className = 'chart-box';
                container.innerHTML = '<canvas id="salesCanvas"></canvas>';
                const ctx2 = document.getElementById('salesCanvas').getContext('2d');

                const labels = salesData.map(d => d.tanggal);
                const values = salesData.map(d => Number(d.total || 0));

                if (salesChartInst) salesChartInst.destroy();
                salesChartInst = new Chart(ctx2, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Omzet (Rp)',
                            data: values,
                            backgroundColor: 'rgba(240,147,251,0.9)',
                            borderRadius: 8,
                            barThickness: 'flex'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return 'Omzet: ' + rupiah(context.parsed.y);
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    color: '#666'
                                }
                            },
                            y: {
                                grid: {
                                    color: 'rgba(0,0,0,0.03)'
                                },
                                ticks: {
                                    color: '#666',
                                    callback: v => 'Rp ' + Number(v).toLocaleString('id-ID')
                                }
                            }
                        }
                    }
                });
            }

            // Table Produk
            const produk = Array.isArray(res.produk) ? res.produk : [];
            if (!produk.length) {
                safeTableEmpty('Belum ada data produk terlaris');
            } else {
                document.querySelector("#produkTable tbody").innerHTML = produk.map((p, i) => `
          <tr>
            <td>${i+1}</td>
            <td>${(p.nama || '').toString()}</td>
            <td>${Number(p.terjual || 0).toLocaleString('id-ID')}</td>
          </tr>
        `).join('');
            }
        })
        .catch(err => {
            console.error(err);
            showAlert('danger', 'Gagal memuat API analitik. Cek URL API / session admin / error server.');
            setEmptyChart('userChart', 'Gagal memuat');
            setEmptyChart('salesChart', 'Gagal memuat');
            safeTableEmpty('Gagal memuat');
        });
</script>

</body>

</html>