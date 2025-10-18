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

            <!-- Header -->
            <div class="page-header">
              <div class="page-block">
                <div class="row align-items-center">
                  <div class="col-md-12">
                    <div class="page-header-title">
                      <h5>Kelola Pesanan</h5>
                    </div>
                    <ul class="breadcrumb">
                      <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/index.php"><i class="fas fa-home"></i></a></li>
                      <li class="breadcrumb-item"><a href="#!">Kelola Pesanan</a></li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>

            <!-- Daftar Pesanan -->
            <section class="content">
              <div class="container-fluid">
                <div class="card">
                  <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Daftar Pesanan</h3>
                  </div>
                  <div class="card-body table-responsive">
                    <table id="tabelPesanan" class="table table-bordered table-striped align-middle">
                      <thead class="table-success">
                        <tr>
                          <th>No</th>
                          <th>ID Pesanan</th>
                          <th>Tanggal</th>
                          <th>Pelanggan</th>
                          <th>Total (Rp)</th>
                          <th>Status</th>
                          <th>Aksi</th>
                        </tr>
                      </thead>
                      <tbody id="pesananBody">
                        <tr>
                          <td>1</td>
                          <td>#ORD001</td>
                          <td>2025-10-17</td>
                          <td>Andi Saputra</td>
                          <td>150.000</td>
                          <td>
                            <select class="form-select form-select-sm status-select">
                              <option selected>Menunggu</option>
                              <option>Diproses</option>
                              <option>Dikirim</option>
                              <option>Selesai</option>
                            </select>
                          </td>
                          <td>
                            <button class="btn btn-info btn-sm detail-btn"><i class="fas fa-eye"></i></button>
                            <button class="btn btn-secondary btn-sm invoice-btn"><i class="fas fa-file-invoice"></i></button>
                            <button class="btn btn-success btn-sm label-btn"><i class="fas fa-tag"></i></button>
                          </td>
                        </tr>
                        <tr>
                          <td>2</td>
                          <td>#ORD002</td>
                          <td>2025-10-18</td>
                          <td>Siti Nurhaliza</td>
                          <td>220.000</td>
                          <td>
                            <select class="form-select form-select-sm status-select">
                              <option>Menunggu</option>
                              <option selected>Diproses</option>
                              <option>Dikirim</option>
                              <option>Selesai</option>
                            </select>
                          </td>
                          <td>
                            <button class="btn btn-info btn-sm detail-btn"><i class="fas fa-eye"></i></button>
                            <button class="btn btn-secondary btn-sm invoice-btn"><i class="fas fa-file-invoice"></i></button>
                            <button class="btn btn-success btn-sm label-btn"><i class="fas fa-tag"></i></button>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </section>

          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal Detail Pesanan -->
<div class="modal fade" id="detailPesananModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Detail Pesanan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <table class="table table-bordered">
          <thead class="table-light">
            <tr>
              <th>Produk</th>
              <th>Qty</th>
              <th>Harga</th>
              <th>Subtotal</th>
            </tr>
          </thead>
          <tbody id="detailOrderBody">
            <tr>
              <td>Mi Ayam Original</td>
              <td>2</td>
              <td>25.000</td>
              <td>50.000</td>
            </tr>
            <tr>
              <td>Es Teh Manis</td>
              <td>2</td>
              <td>10.000</td>
              <td>20.000</td>
            </tr>
            <tr>
              <td colspan="3" class="text-end fw-bold">Total</td>
              <td><strong>70.000</strong></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Modal Invoice -->
<div class="modal fade" id="invoiceModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content p-3">
      <div class="modal-header border-0">
        <h5 class="modal-title">Invoice Pesanan #ORD001</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="d-flex justify-content-between">
          <div>
            <h6>Toko Mi Ayam Bahagia</h6>
            <p>Depok, Jawa Barat<br>Email: admin@miayam.com</p>
          </div>
          <div>
            <p><strong>Tanggal:</strong> 17 Oktober 2025<br><strong>Pelanggan:</strong> Andi Saputra</p>
          </div>
        </div>
        <table class="table table-bordered mt-3">
          <thead class="table-light">
            <tr>
              <th>Produk</th>
              <th>Qty</th>
              <th>Harga</th>
              <th>Subtotal</th>
            </tr>
          </thead>
          <tbody>
            <tr><td>Mi Ayam Original</td><td>2</td><td>25.000</td><td>50.000</td></tr>
            <tr><td>Es Teh Manis</td><td>2</td><td>10.000</td><td>20.000</td></tr>
            <tr><td colspan="3" class="text-end fw-bold">Total</td><td><strong>70.000</strong></td></tr>
          </tbody>
        </table>
        <div class="text-end mt-3">
          <button class="btn btn-primary"><i class="fas fa-print"></i> Cetak Invoice</button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal Label Pengiriman -->
<div class="modal fade" id="labelModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content p-3">
      <div class="modal-header border-0">
        <h5 class="modal-title">Label Pengiriman</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="border p-3 text-center">
          <h6 class="fw-bold">Toko Mi Ayam Bahagia</h6>
          <p>Depok, Jawa Barat</p>
          <hr>
          <p><strong>Kepada:</strong><br>Andi Saputra<br>Jl. Melati No.12, Bandung</p>
          <hr>
          <p><strong>No. Pesanan:</strong> #ORD001<br><strong>Tanggal:</strong> 17/10/2025</p>
        </div>
        <div class="text-end mt-3">
          <button class="btn btn-success"><i class="fas fa-print"></i> Cetak Label</button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- JS -->
<script src="../assets/js/vendor-all.min.js"></script>
<script src="../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/pcoded.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Modal handler
  const detailModal = new bootstrap.Modal(document.getElementById('detailPesananModal'));
  const invoiceModal = new bootstrap.Modal(document.getElementById('invoiceModal'));
  const labelModal = new bootstrap.Modal(document.getElementById('labelModal'));

  // Tombol Detail
  document.querySelectorAll('.detail-btn').forEach(btn => {
    btn.addEventListener('click', () => detailModal.show());
  });

  // Tombol Invoice
  document.querySelectorAll('.invoice-btn').forEach(btn => {
    btn.addEventListener('click', () => invoiceModal.show());
  });

  // Tombol Label
  document.querySelectorAll('.label-btn').forEach(btn => {
    btn.addEventListener('click', () => labelModal.show());
  });

  // Update Status Pesanan
  document.querySelectorAll('.status-select').forEach(select => {
    select.addEventListener('change', function() {
      const status = this.value;
      this.classList.remove('bg-warning', 'bg-info', 'bg-primary', 'bg-success');
      if (status === 'Menunggu') this.classList.add('bg-warning');
      else if (status === 'Diproses') this.classList.add('bg-info');
      else if (status === 'Dikirim') this.classList.add('bg-primary');
      else if (status === 'Selesai') this.classList.add('bg-success');
    });
  });
});
</script>
