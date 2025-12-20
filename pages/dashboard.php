<?php
include __DIR__ . '/../config/config.php';
include __DIR__ . '/../includes/head.php';
include __DIR__ . '/../includes/navigation.php';
include __DIR__ . '/../includes/topbar.php';
?>

<style>
    /* ===== DASHBOARD MODERN STYLES ===== */


    .prod-p-card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        overflow: hidden;
    }

    .prod-p-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }

    .prod-p-card.bg-c-blue {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .prod-p-card.bg-c-green {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }

    .prod-p-card.bg-c-yellow {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }

    .prod-p-card.bg-c-red {
        background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
    }

    .card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        transition: box-shadow 0.3s ease;
    }

    .card:hover {
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12);
    }

    .card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 12px 12px 0 0;
        padding: 20px;
        border: none;
    }

    .card-header h5 {
        margin: 0;
        font-weight: 600;
        font-size: 16px;
    }

    .card-body {
        padding: 25px;
    }

    /* CHART STYLING */
    #salesChart {
        max-height: 400px;
    }

    .chart-container {
        position: relative;
        height: 400px;
        margin-bottom: 20px;
    }

    /* QUICK SUMMARY */
    .list-group-item {
        background: white;
        border: 1px solid #f0f0f0;
        border-radius: 8px;
        margin-bottom: 12px;
        padding: 15px;
        transition: all 0.3s ease;
    }

    .list-group-item:hover {
        background: #f8f9fa;
        border-color: #667eea;
    }

    .badge {
        padding: 8px 12px;
        border-radius: 6px;
        font-weight: 600;
        font-size: 12px;
    }

    .badge-danger {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }

    .badge-warning {
        background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
    }

    .badge-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    /* TABLE STYLING */
    .table {
        color: #666;
    }

    .table th {
        background: #f8f9fa;
        color: #667eea;
        font-weight: 600;
        border-bottom: 2px solid #667eea;
        padding: 15px;
    }

    .table td {
        padding: 15px;
        border-bottom: 1px solid #f0f0f0;
        vertical-align: middle;
    }

    .table tbody tr:hover {
        background: #f8f9fa;
    }


    .row {
        margin-bottom: 25px;
    }

    .col-xl-3 {
        margin-bottom: 15px;
    }
</style>

<div class="pcoded-main-container">
    <div class="pcoded-wrapper">
        <div class="pcoded-content">
            <div class="pcoded-inner-content">
                <div class="main-body">
                    <div class="page-wrapper">

                        <!-- HEADER -->
                        <div class="page-header">
                            <div class="page-block">
                                <h5>Dashboard</h5>
                                <ul class="breadcrumb">
                                    <li class="breadcrumb-item">
                                        <a href="<?= BASE_URL ?>/index.php"><i class="fas fa-home"></i></a>
                                    </li>
                                    <li class="breadcrumb-item">Dashboard</li>
                                </ul>
                            </div>
                        </div>

                        <!-- SUMMARY -->
                        <div class="row">
                            <?php
                            $cards = [
                                ['Total Produk', 'totalProduk', 'box', 'blue'],
                                ['Total Pesanan', 'totalPesanan', 'shopping-cart', 'green'],
                                ['Pendapatan', 'totalPendapatan', 'coins', 'yellow'],
                                ['Total Pelanggan', 'totalPelanggan', 'users', 'red'],
                            ];
                            foreach ($cards as $c):
                            ?>
                                <div class="col-xl-3 col-md-6">
                                    <div class="card prod-p-card bg-c-<?= $c[3] ?>">
                                        <div class="card-body d-flex justify-content-between">
                                            <div>
                                                <h6 class="text-white"><?= $c[0] ?></h6>
                                                <h3 class="text-white" id="<?= $c[1] ?>">0</h3>
                                            </div>
                                            <i class="fas fa-<?= $c[2] ?> f-24 text-<?= $c[3] ?>"></i>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- CHART + QUICK -->
                        <div class="row">
                            <div class="col-xl-8">
                                <div class="card">
                                    <div class="card-header">
                                        <h5>Penjualan Per Hari (30 Hari Terakhir)</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="chart-container">
                                            <canvas id="salesChart"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h5>Ringkasan Cepat</h5>
                                    </div>
                                    <div class="card-body">
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item d-flex justify-content-between">
                                                Stok Rendah <span class="badge badge-danger" id="stokRendah">0</span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between">
                                                Pesanan Pending <span class="badge badge-warning" id="pesananPending">0</span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between">
                                                Produk Aktif <span class="badge badge-primary" id="produkAktif">0</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- TABLES -->
                        <div class="row">
                            <div class="col-xl-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5>Top Produk</h5>
                                    </div>
                                    <div class="card-body table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Produk</th>
                                                    <th>Terjual</th>
                                                    <th>Stok</th>
                                                </tr>
                                            </thead>
                                            <tbody id="topProdukBody">
                                                <tr>
                                                    <td colspan="4">Memuat...</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5>Pesanan Terbaru</h5>
                                    </div>
                                    <div class="card-body table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Order</th>
                                                    <th>Pelanggan</th>
                                                    <th>Total</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody id="latestOrdersBody">
                                                <tr>
                                                    <td colspan="4">Memuat...</td>
                                                </tr>
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
</div>

<!-- FOOTER JS -->
<script src="<?= ASSET ?>/js/vendor-all.min.js"></script>
<script src="<?= ASSET ?>/plugins/bootstrap/js/bootstrap.min.js"></script>
<script src="<?= ASSET ?>/js/pcoded.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const apiUrl = "<?= BASE_URL ?>/api/dashboard_analytics.php";
    let chartInstance = null;

    function rupiah(n) {
        return 'Rp ' + Number(n || 0).toLocaleString('id-ID');
    }

    function renderSalesChart(data) {
        const ctx = document.getElementById('salesChart');
        if (!ctx) return;

        if (chartInstance) {
            chartInstance.destroy();
        }

        if (!data || data.length === 0) {
            ctx.parentElement.innerHTML = '<div class="text-center text-muted">Tidak ada data penjualan</div>';
            return;
        }

        const labels = data.map(d => {
            const date = new Date(d.tanggal);
            return date.toLocaleDateString('id-ID', {
                month: 'short',
                day: 'numeric'
            });
        });
        const values = data.map(d => parseFloat(d.total_penjualan) || 0);

        chartInstance = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Penjualan Harian',
                    data: values,
                    backgroundColor: [
                        'rgba(102, 126, 234, 0.8)',
                        'rgba(240, 147, 251, 0.8)',
                        'rgba(74, 144, 226, 0.8)',
                        'rgba(250, 112, 154, 0.8)',
                        'rgba(0, 242, 254, 0.8)',
                        'rgba(102, 126, 234, 0.8)',
                        'rgba(240, 147, 251, 0.8)'
                    ],
                    borderColor: [
                        'rgb(102, 126, 234)',
                        'rgb(240, 147, 251)',
                        'rgb(74, 144, 226)',
                        'rgb(250, 112, 154)',
                        'rgb(0, 242, 254)',
                        'rgb(102, 126, 234)',
                        'rgb(240, 147, 251)'
                    ],
                    borderWidth: 0,
                    borderRadius: 8,
                    borderSkipped: false,
                    barThickness: 'flex',
                    maxBarThickness: 30,
                    minBarLength: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        labels: {
                            font: {
                                size: 13,
                                weight: '600'
                            },
                            color: '#666',
                            padding: 20,
                            usePointStyle: true
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleFont: {
                            size: 14,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 13
                        },
                        padding: 12,
                        displayColors: false,
                        borderColor: '#667eea',
                        borderWidth: 1,
                        callbacks: {
                            label: function(context) {
                                return 'Penjualan: Rp ' + Number(context.parsed.y).toLocaleString('id-ID');
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(200, 200, 200, 0.1)',
                            drawBorder: false
                        },
                        ticks: {
                            color: '#666',
                            font: {
                                size: 12
                            },
                            callback: function(value) {
                                return 'Rp ' + Number(value).toLocaleString('id-ID');
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            color: '#666',
                            font: {
                                size: 12
                            }
                        }
                    }
                }
            }
        });
    }

    fetch(apiUrl, {
            credentials: 'include'
        })
        .then(r => r.json())
        .then(res => {
            if (!res.success) return;

            const s = res.summary;
            totalProduk.innerText = s.total_produk;
            totalPesanan.innerText = s.total_pesanan;
            totalPendapatan.innerText = rupiah(s.pendapatan);
            totalPelanggan.innerText = s.total_pelanggan;

            stokRendah.innerText = s.stok_rendah;
            pesananPending.innerText = s.pesanan_pending;
            produkAktif.innerText = s.total_produk;

            renderSalesChart(res.daily_sales);

            topProdukBody.innerHTML = res.top_produk.map((p, i) => `
        <tr><td>${i+1}</td><td>${p.nama}</td><td>${p.terjual}</td><td>${p.stok}</td></tr>
    `).join('');

            latestOrdersBody.innerHTML = res.latest_orders.map(o => `
        <tr><td>${o.order_code}</td><td>${o.username}</td><td>${rupiah(o.total_harga)}</td><td>${o.status}</td></tr>
    `).join('');
        });
</script>

</body>

</html>