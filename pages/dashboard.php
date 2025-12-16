<?php
include __DIR__ . '/../config/config.php';
include __DIR__ . '/../includes/head.php';
include __DIR__ . '/../includes/navigation.php';
include __DIR__ . '/../includes/topbar.php';
?>

<style>
    /* ===== SIMPLE CHART ===== */
    .simple-chart {
        display: flex;
        align-items: flex-end;
        gap: 6px;
        height: 260px;
        padding: 10px;
        border-left: 1px solid #ddd;
        border-bottom: 1px solid #ddd;
    }

    .simple-bar {
        flex: 1;
        background: #4fc3f7;
        border-radius: 4px 4px 0 0;
        position: relative;
    }

    .simple-bar span {
        position: absolute;
        bottom: 100%;
        left: 50%;
        transform: translateX(-50%);
        font-size: 11px;
        color: #555;
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
                                        <h5>Tren Pesanan (30 Hari)</h5>
                                    </div>
                                    <div class="card-body">
                                        <div id="ordersChart" class="text-center text-muted">Memuat grafik...</div>
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

<script>
    const apiUrl = "<?= BASE_URL ?>/api/dashboard_analytics.php";

    function rupiah(n) {
        return 'Rp ' + Number(n || 0).toLocaleString('id-ID');
    }

    function renderSimpleChart(data) {
        const el = document.getElementById('ordersChart');
        if (!data || data.length === 0) {
            el.innerHTML = 'Tidak ada data';
            return;
        }
        const max = Math.max(...data.map(d => +d.total));
        el.className = 'simple-chart';
        el.innerHTML = '';
        data.forEach(d => {
            const b = document.createElement('div');
            b.className = 'simple-bar';
            b.style.height = (max ? d.total / max * 100 : 0) + '%';
            b.innerHTML = `<span>${d.total}</span>`;
            el.appendChild(b);
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

            renderSimpleChart(res.order_trend);

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