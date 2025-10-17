<?php
include_once __DIR__ . '/../config/config.php';
include __DIR__ . '/../includes/head.php';
include __DIR__ . '/../includes/navigation.php';
include __DIR__ . '/../includes/topbar.php';

// prepare data for charts (best-effort; if tables missing show empty)
$visitors_data = [];
$sales_data = [];
$top_products = [];

try {
    $db = get_db();

    // visitors per day (expects table `visits` with `created_at` timestamp)
    try {
        $sth = $db->query("SELECT DATE(created_at) dt, COUNT(*) cnt FROM visits GROUP BY DATE(created_at) ORDER BY dt DESC LIMIT 30");
        $rows = $sth->fetchAll();
        if ($rows) {
            $visitors_data = array_reverse(array_map(function($r){ return ['date'=>$r['dt'],'value'=> (int)$r['cnt']]; }, $rows));
        }
    } catch (Exception $e) {
        // leave visitors_data empty
    }

    // sales per day (expects table `orders` with `created_at` and `total`)
    try {
        $sth = $db->query("SELECT DATE(created_at) dt, SUM(total) total, COUNT(*) orders_count FROM orders GROUP BY DATE(created_at) ORDER BY dt DESC LIMIT 30");
        $rows = $sth->fetchAll();
        if ($rows) {
            $sales_data = array_reverse(array_map(function($r){ return ['date'=>$r['dt'],'total'=> (float)$r['total'], 'orders'=>(int)$r['orders_count']]; }, $rows));
        }
    } catch (Exception $e) {
        // leave sales_data empty
    }

    // top products (try order_items -> product_id, quantity; fallback to produk table if has 'sold' or similar)
    try {
        $sth = $db->query("SELECT oi.product_id, p.nama, SUM(oi.quantity) sold_qty
                            FROM order_items oi
                            JOIN produk p ON p.id = oi.product_id
                            GROUP BY oi.product_id
                            ORDER BY sold_qty DESC
                            LIMIT 10");
        $rows = $sth->fetchAll();
        if ($rows) {
            foreach ($rows as $r) {
                $top_products[] = $r;
            }
        } else {
            // fallback: top by stok small (not ideal) or by a 'sold' column if exists
            $sth2 = $db->query("SELECT id, nama, 0 as sold_qty FROM produk ORDER BY id DESC LIMIT 10");
            $rows2 = $sth2->fetchAll();
            foreach ($rows2 as $r) $top_products[] = $r;
        }
    } catch (Exception $e) {
        // fallback to produk simple list
        try {
            $sth2 = $db->query("SELECT id, nama, 0 as sold_qty FROM produk ORDER BY id DESC LIMIT 10");
            $rows2 = $sth2->fetchAll();
            foreach ($rows2 as $r) $top_products[] = $r;
        } catch (Exception $e2) {
            // nothing
        }
    }

} catch (Exception $e) {
    // DB not available; keep arrays empty
}
?>

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
                                            <h5>Analitik</h5>
                                        </div>
                                        <ul class="breadcrumb">
                                            <li class="breadcrumb-item"><a href="../index.php"><i class="fas fa-home"></i></a></li>
                                            <li class="breadcrumb-item"><a href="#!">Analitik</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- laporan row -->
                        <div class="row">

                            <!-- Laporan Pengunjung -->
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header"><h5>Laporan Pengunjung (30 hari terakhir)</h5></div>
                                    <div class="card-body">
                                        <?php if (!empty($visitors_data)): ?>
                                            <div id="visitorsChart" style="height:250px;"></div>
                                        <?php else: ?>
                                            <div class="alert alert-secondary">Data pengunjung belum tersedia. Pastikan tabel <code>visits</code> ada atau integrasi tracking telah aktif.</div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Laporan Penjualan -->
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header"><h5>Laporan Penjualan (30 hari terakhir)</h5></div>
                                    <div class="card-body">
                                        <?php if (!empty($sales_data)): ?>
                                            <div id="salesChart" style="height:250px;"></div>
                                        <?php else: ?>
                                            <div class="alert alert-secondary">Data penjualan belum tersedia. Pastikan tabel <code>orders</code> ada dan memiliki kolom <code>total</code>.</div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- Produk Terlaris -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header"><h5>Produk Terlaris</h5></div>
                                    <div class="card-body">
                                        <?php if (!empty($top_products)): ?>
                                            <div class="table-responsive">
                                                <table class="table table-striped">
                                                    <thead>
                                                        <tr><th>#</th><th>Nama Produk</th><th>Terjual</th></tr>
                                                    </thead>
                                                    <tbody>
                                                    <?php $i=1; foreach ($top_products as $tp): ?>
                                                        <tr>
                                                            <td><?= $i++ ?></td>
                                                            <td><?= htmlspecialchars($tp['nama'] ?? 'N/A') ?></td>
                                                            <td><?= intval($tp['sold_qty'] ?? 0) ?></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        <?php else: ?>
                                            <div class="alert alert-secondary">Tidak ada data produk terlaris. Pastikan tabel <code>order_items</code> atau <code>produk</code> ada.</div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div> <!-- page-wrapper -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- include required scripts for charts -->
<script src="<?= ASSET ?>/plugins/chart-morris/js/raphael.min.js"></script>
<script src="<?= ASSET ?>/plugins/chart-morris/js/morris.min.js"></script>

<script>
    // pass PHP data to JS
    const visitorsData = <?= json_encode(array_values($visitors_data)) ?: '[]' ?>;
    const salesDataRaw = <?= json_encode(array_values($sales_data)) ?: '[]' ?>;

    // Morris expects objects with x/y
    if (visitorsData.length) {
        const vData = visitorsData.map(d => ({ y: d.date, a: d.value }));
        new Morris.Line({
            element: 'visitorsChart',
            data: vData,
            xkey: 'y',
            ykeys: ['a'],
            labels: ['Pengunjung'],
            resize: true,
            lineColors: ['#1f77b4'],
            parseTime: false
        });
    }

    if (salesDataRaw.length) {
        const sData = salesDataRaw.map(d => ({ y: d.date, total: parseFloat(d.total) }));
        new Morris.Bar({
            element: 'salesChart',
            data: sData,
            xkey: 'y',
            ykeys: ['total'],
            labels: ['Total (Rp)'],
            resize: true,
            barColors: ['#28a745'],
            parseTime: false
        });
    }
</script>