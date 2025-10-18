<?php include __DIR__ . '/../config/config.php'; ?>

<!-- [ navigation menu ] start -->
<nav class="pcoded-navbar menupos-fixed menu-light brand-blue ">
    <div class="navbar-wrapper ">
        <div class="navbar-brand header-logo">
            <a href="<?= BASE_URL ?>/index.php" class="b-brand" style="display:flex;align-items:center;">
                <!-- brand icon + text: icon akan tetap tampil saat sidebar mengecil -->
                <span class="pcoded-micon" style="color:#fff;"><i class="fas fa-mountain"></i></span>
                <span class="pcoded-mtext brand-text" style="font-weight:700;font-size:16px;color:#fff;margin-left:8px;">Effort Outdoor</span>
            </a>
            <a class="mobile-menu" id="mobile-collapse" href="#!"><span></span></a>
        </div>
        <div class="navbar-content scroll-div">
            <ul class="nav pcoded-inner-navbar">
                
                <li class="nav-item">
                    <a href="<?= BASE_URL ?>/pages/dashboard.php" class="nav-link"><span class="pcoded-micon"><i class="fas fa-home"></i></span><span class="pcoded-mtext">Dashboard</span></a>
                </li>

                <li class="nav-item">
                    <a href="<?= BASE_URL ?>/pages/produk.php" class="nav-link"><span class="pcoded-micon"><i class="fas fa-box"></i></span><span class="pcoded-mtext">Kelola Produk</span></a>
                </li>

                <li class="nav-item">
                    <a href="<?= BASE_URL ?>/pages/pesanan.php" class="nav-link"><span class="pcoded-micon"><i class="fas fa-shopping-cart"></i></span><span class="pcoded-mtext">Kelola Pesanan</span></a>
                </li>

                <li class="nav-item">
                    <a href="<?= BASE_URL ?>/pages/pengiriman.php" class="nav-link"><span class="pcoded-micon"><i class="fas fa-truck"></i></span><span class="pcoded-mtext">Kelola Pengiriman</span></a>
                </li>

                <li class="nav-item">
                    <a href="<?= BASE_URL ?>/pages/pembayaran.php" class="nav-link"><span class="pcoded-micon"><i class="fas fa-credit-card"></i></span><span class="pcoded-mtext">Kelola Pembayaran</span></a>
                </li>

                <li class="nav-item">
                    <a href="<?= BASE_URL ?>/pages/users.php" class="nav-link"><span class="pcoded-micon"><i class="fas fa-user"></i></span><span class="pcoded-mtext">Kelola User</span></a>
                </li>

                <li class="nav-item">
                    <a href="<?= BASE_URL ?>/pages/laporan.php" class="nav-link"><span class="pcoded-micon"><i class="fas fa-file-alt"></i></span><span class="pcoded-mtext">Laporan Keuangan</span></a>
                </li>

                <li class="nav-item">
                    <a href="<?= BASE_URL ?>/pages/crm.php" class="nav-link"><span class="pcoded-micon"><i class="fas fa-briefcase"></i></span><span class="pcoded-mtext">CRM</span></a>
                </li>

                <li class="nav-item">
                    <a href="<?= BASE_URL ?>/pages/analitik.php" class="nav-link"><span class="pcoded-micon"><i class="fas fa-chart-bar"></i></span><span class="pcoded-mtext">Analitik & Statistik</span></a>
                </li>

                <li class="nav-item">
                    <a href="<?= BASE_URL ?>/pages/pengaturan.php" class="nav-link"><span class="pcoded-micon"><i class="fas fa-cog"></i></span><span class="pcoded-mtext">Pengaturan</span></a>
                </li>

            </ul>
        </div>
    </div>
</nav>
<!-- [ navigation menu ] end -->