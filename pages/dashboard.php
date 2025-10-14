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

                        <!-- page header / breadcrumb -->
                        <div class="page-header">
                            <div class="page-block">
                                <div class="row align-items-center">
                                    <div class="col-md-12">
                                        <div class="page-header-title">
                                            <h5>Dashboard</h5>
                                        </div>
                                        <ul class="breadcrumb">
                                            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/index.php"><i class="fas fa-home"></i></a></li>
                                            <li class="breadcrumb-item"><a href="#!">Dashboard</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- summary cards -->
                        <div class="row">
                            <div class="col-xl-3 col-md-6">
                                <div class="card prod-p-card bg-c-blue">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col">
                                                <h6 class="m-b-5 text-white">Total Produk</h6>
                                                <h3 class="m-b-0 text-white">1,234</h3>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-box text-white f-24"></i>
                                            </div>
                                        </div>
                                        <p class="m-b-0"><span class="label label-primary m-r-10">+4%</span> dari bulan lalu</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-3 col-md-6">
                                <div class="card prod-p-card bg-c-green">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col">
                                                <h6 class="m-b-5 text-white">Pesanan</h6>
                                                <h3 class="m-b-0 text-white">587</h3>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-shopping-cart text-white f-24"></i>
                                            </div>
                                        </div>
                                        <p class="m-b-0"><span class="label label-success m-r-10">+8%</span> minggu ini</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-3 col-md-6">
                                <div class="card prod-p-card bg-c-yellow">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col">
                                                <h6 class="m-b-5 text-white">Pelanggan</h6>
                                                <h3 class="m-b-0 text-white">2,103</h3>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-users text-white f-24"></i>
                                            </div>
                                        </div>
                                        <p class="m-b-0"><span class="label label-warning m-r-10">+2%</span> dari bulan lalu</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-3 col-md-6">
                                <div class="card prod-p-card bg-c-red">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col">
                                                <h6 class="m-b-5 text-white">Pendapatan</h6>
                                                <h3 class="m-b-0 text-white">Rp 125.400.000</h3>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-dollar-sign text-white f-24"></i>
                                            </div>
                                        </div>
                                        <p class="m-b-0"><span class="label label-danger m-r-10">+12%</span> kuartal ini</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end summary cards -->

                        <div class="row">
                            <!-- orders chart -->
                            <div class="col-xl-8 col-md-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5>Tren Pesanan</h5>
                                    </div>
                                    <div class="card-body">
                                        <div id="ordersChart" style="height:300px;"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- quick links / stats -->
                            <div class="col-xl-4 col-md-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5>Ringkasan Cepat</h5>
                                    </div>
                                    <div class="card-body">
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                Produk aktif
                                                <span class="badge badge-primary">1,120</span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                Pesanan tertunda
                                                <span class="badge badge-warning">42</span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                Pengembalian
                                                <span class="badge badge-danger">8</span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                Stok rendah
                                                <span class="badge badge-danger">23</span>
                                            </li>
                                        </ul>

                                        <hr>

                                        <div class="row text-center">
                                            <div class="col-6">
                                                <a href="<?= BASE_URL ?>/pages/produk.php" class="btn btn-outline-primary btn-block"><i class="fas fa-box"></i> Produk</a>
                                            </div>
                                            <div class="col-6">
                                                <a href="<?= BASE_URL ?>/pages/pesanan.php" class="btn btn-outline-success btn-block"><i class="fas fa-shopping-cart"></i> Pesanan</a>
                                            </div>
                                            <div class="col-6 mt-2">
                                                <a href="<?= BASE_URL ?>/pages/pelanggan.php" class="btn btn-outline-info btn-block"><i class="fas fa-users"></i> Pelanggan</a>
                                            </div>
                                            <div class="col-6 mt-2">
                                                <a href="<?= BASE_URL ?>/pages/laporan.php" class="btn btn-outline-warning btn-block"><i class="fas fa-file-alt"></i> Laporan</a>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- top products + recent orders -->
                        <div class="row">
                            <div class="col-xl-6 col-md-12">
                                <div class="card table-card">
                                    <div class="card-header">
                                        <h5>Top Produk</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Produk</th>
                                                        <th>Terjual</th>
                                                        <th>Stok</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>1</td>
                                                        <td>Produk A</td>
                                                        <td>1,240</td>
                                                        <td>56</td>
                                                    </tr>
                                                    <tr>
                                                        <td>2</td>
                                                        <td>Produk B</td>
                                                        <td>980</td>
                                                        <td>12</td>
                                                    </tr>
                                                    <tr>
                                                        <td>3</td>
                                                        <td>Produk C</td>
                                                        <td>720</td>
                                                        <td>0 <span class="badge badge-danger ml-2">Habis</span></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-6 col-md-12">
                                <div class="card table-card">
                                    <div class="card-header">
                                        <h5>Pesanan Terbaru</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Order</th>
                                                        <th>Pelanggan</th>
                                                        <th>Total</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>#OR-00123</td>
                                                        <td>Asep</td>
                                                        <td>Rp 1.250.000</td>
                                                        <td><span class="badge badge-warning">Pending</span></td>
                                                    </tr>
                                                    <tr>
                                                        <td>#OR-00122</td>
                                                        <td>Siti</td>
                                                        <td>Rp 450.000</td>
                                                        <td><span class="badge badge-success">Selesai</span></td>
                                                    </tr>
                                                    <tr>
                                                        <td>#OR-00121</td>
                                                        <td>Budi</td>
                                                        <td>Rp 780.000</td>
                                                        <td><span class="badge badge-danger">Dibatalkan</span></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end tables -->

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- [ Main Content ] end -->

<!-- footer scripts -->
<script src="<?= ASSET ?>/js/vendor-all.min.js"></script>
<script src="<?= ASSET ?>/plugins/bootstrap/js/bootstrap.min.js"></script>
<script src="<?= ASSET ?>/js/pcoded.min.js"></script>
<!-- optional page chart script (template has example) -->
<script src="<?= ASSET ?>/js/pages/dashboard-analytics.js"></script>

<script>
    // placeholder chart init (if dashboard-analytics available it will override)
    (function(){
        if (typeof Morris === 'undefined' && document.getElementById('ordersChart')) {
            // simple fallback: show message when chart lib not loaded
            document.getElementById('ordersChart').innerHTML = '<div class="text-center text-muted" style="padding-top:110px">Chart library not loaded</div>';
        }
    })();
</script>

</body>
</html>
