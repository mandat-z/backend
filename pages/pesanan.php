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
                    <input type="text" id="searchPesanan" class="form-control" placeholder="Cari pesanan..." style="width: 250px;">
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
                        <?php
                        $db = get_db();
                        $stmt = $db->query('
                          SELECT o.id, o.order_code, o.user_id, o.subtotal, o.ongkir, o.total_harga,
                                 o.status, o.tanggal_pesan,
                                 u.username, u.email
                          FROM orders o
                          JOIN users u ON o.user_id = u.id
                          ORDER BY o.tanggal_pesan DESC
                        ');
                        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        $n = 1;
                        foreach ($orders as $order):
                        ?>
                          <tr data-id="<?= $order['id'] ?>">
                            <td><?= $n++ ?></td>
                            <td><?= htmlspecialchars($order['order_code'] ?: $order['id']) ?></td>
                            <td><?= htmlspecialchars(date('d/m/Y', strtotime($order['tanggal_pesan']))) ?></td>
                            <td><?= htmlspecialchars($order['username']) ?></td>
                            <td>Rp <?= number_format($order['total_harga'], 0, ',', '.') ?></td>
                            <td>
                              <select class="form-select status-select" data-order-id="<?= $order['id'] ?>">
                                <option value="pending" <?= $order['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="dikemas" <?= $order['status'] === 'dikemas' ? 'selected' : '' ?>>Dikemas</option>
                                <option value="dikirim" <?= $order['status'] === 'dikirim' ? 'selected' : '' ?>>Dikirim</option>
                                <option value="selesai" <?= $order['status'] === 'selesai' ? 'selected' : '' ?>>Selesai</option>
                                <option value="dibatalkan" <?= $order['status'] === 'dibatalkan' ? 'selected' : '' ?>>Dibatalkan</option>
                              </select>
                            </td>
                            <td>
                              <button type="button" class="btn btn-info btn-sm detail-btn" data-order-id="<?= $order['id'] ?>">Detail</button>
                              <button type="button" class="btn btn-primary btn-sm invoice-btn" data-order-id="<?= $order['id'] ?>">Invoice</button>
                              <button type="button" class="btn btn-success btn-sm label-btn" data-order-id="<?= $order['id'] ?>">Label</button>
                            </td>
                          </tr>
                        <?php endforeach; ?>
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
<div class="modal fade" id="detailPesananModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Detail Pesanan</h5>
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <div class="modal-body">
        <div id="orderDetails"></div>
      </div>
    </div>
  </div>
</div>

<!-- Modal Invoice -->
<div class="modal fade" id="invoiceModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content p-3">
      <div class="modal-header border-0">
        <h5 class="modal-title">Invoice Pesanan</h5>
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <div class="modal-body" id="invoiceBody">
        <!-- Konten invoice akan diisi via JS -->
      </div>
    </div>
  </div>
</div>

<!-- Modal Label Pengiriman -->
<div class="modal fade" id="labelModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content p-3">
      <div class="modal-header border-0">
        <h5 class="modal-title">Label Pengiriman</h5>
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <div class="modal-body" id="labelBody">
        <!-- Konten label akan diisi via JS -->
      </div>
    </div>
  </div>
</div>

<script src="../assets/js/vendor-all.min.js"></script>
<script src="../assets/plugins/bootstrap/js/bootstrap.min.js"></script>
<script src="../assets/js/pcoded.min.js"></script>

<script>
  const BACKEND_API = '<?= BACKEND_URL ?>/api';

  document.addEventListener('DOMContentLoaded', function() {
    // Delegasi klik ke tombol (pakai closest supaya klik icon tetap kena)
    document.addEventListener('click', function(e) {
      const btn = e.target.closest('button');
      if (!btn) return;

      if (btn.classList.contains('detail-btn')) {
        const orderId = btn.getAttribute('data-order-id');
        loadOrderDetails(orderId);
        $('#detailPesananModal').modal('show');

      } else if (btn.classList.contains('invoice-btn')) {
        const orderId = btn.getAttribute('data-order-id');
        loadInvoice(orderId);
        $('#invoiceModal').modal('show');

      } else if (btn.classList.contains('label-btn')) {
        const orderId = btn.getAttribute('data-order-id');
        loadLabel(orderId);
        $('#labelModal').modal('show');
      }
    });

    // Update Status Pesanan
    document.addEventListener('change', function(e) {
      if (e.target.classList.contains('status-select')) {
        const orderId = e.target.getAttribute('data-order-id');
        const status = e.target.value;
        updateOrderStatus(orderId, status);

        e.target.classList.remove('bg-warning', 'bg-info', 'bg-primary', 'bg-success', 'bg-danger');
        if (status === 'pending') e.target.classList.add('bg-warning');
        else if (status === 'dikemas') e.target.classList.add('bg-info');
        else if (status === 'dikirim') e.target.classList.add('bg-primary');
        else if (status === 'selesai') e.target.classList.add('bg-success');
        else if (status === 'dibatalkan') e.target.classList.add('bg-danger');
      }
    });

    // Pencarian di tabel
    const searchInput = document.querySelector('#searchPesanan');
    if (searchInput) {
      searchInput.addEventListener('input', function() {
        const query = this.value.toLowerCase();
        document.querySelectorAll('#pesananBody tr').forEach(row => {
          const text = row.textContent.toLowerCase();
          row.style.display = text.includes(query) ? '' : 'none';
        });
      });
    }

    // ======================
    // DETAIL PESANAN
    // ======================
    function loadOrderDetails(orderId) {
      fetch(`${BACKEND_API}/order_get.php?id=${orderId}`)
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            const order = data.data;
            const items = data.items || [];
            const payment = data.payment;

            let html = `
              <div class="row">
                <div class="col-md-6">
                  <h6>Informasi Pesanan</h6>
                  <p><strong>ID Pesanan:</strong> ${order.order_code || order.id}</p>
                  <p><strong>Tanggal:</strong> ${new Date(order.tanggal_pesan).toLocaleDateString('id-ID')}</p>
                  <p><strong>Pelanggan:</strong> ${order.username} (${order.email})</p>
                  <p><strong>Status:</strong> ${order.status}</p>
                </div>
                <div class="col-md-6">
                  <h6>Alamat Pengiriman</h6>
                  <p><strong>Penerima:</strong> ${order.nama_penerima}</p>
                  <p><strong>Alamat:</strong> ${order.jalan}, ${order.rt_rw ? 'RT/RW ' + order.rt_rw : ''}, ${order.kelurahan}, ${order.kecamatan}, ${order.nama_kota}, ${order.provinsi} ${order.kode_pos}</p>
                  <p><strong>Telepon:</strong> ${order.alamat_phone || order.user_phone || '-'}</p>
                </div>
              </div>
              <h6 class="mt-3">Detail Produk</h6>
              <table class="table table-bordered">
                <thead class="table-light">
                  <tr>
                    <th>Produk</th>
                    <th>Qty</th>
                    <th>Harga Satuan</th>
                    <th>Subtotal</th>
                  </tr>
                </thead>
                <tbody>
            `;

            items.forEach(item => {
              html += `
                <tr>
                  <td>${item.product_name}</td>
                  <td>${item.qty}</td>
                  <td>Rp ${parseFloat(item.harga_satuan).toLocaleString('id-ID')}</td>
                  <td>Rp ${parseFloat(item.subtotal).toLocaleString('id-ID')}</td>
                </tr>
              `;
            });

            html += `
                </tbody>
                <tfoot>
                  <tr>
                    <td colspan="3" class="text-end fw-bold">Subtotal</td>
                    <td>Rp ${parseFloat(order.subtotal).toLocaleString('id-ID')}</td>
                  </tr>
                  <tr>
                    <td colspan="3" class="text-end fw-bold">Ongkir</td>
                    <td>Rp ${parseFloat(order.ongkir).toLocaleString('id-ID')}</td>
                  </tr>
                  <tr>
                    <td colspan="3" class="text-end fw-bold">Total</td>
                    <td><strong>Rp ${parseFloat(order.total_harga).toLocaleString('id-ID')}</strong></td>
                  </tr>
                </tfoot>
              </table>
            `;

            if (payment) {
              html += `
                <h6 class="mt-3">Informasi Pembayaran</h6>
                <p><strong>Metode:</strong> ${payment.nama_metode} (${payment.jenis})</p>
                <p><strong>Total Bayar:</strong> Rp ${parseFloat(payment.total_bayar).toLocaleString('id-ID')}</p>
                <p><strong>Jumlah Dibayar:</strong> Rp ${parseFloat(payment.jumlah_dibayar).toLocaleString('id-ID')}</p>
                <p><strong>Status:</strong> ${payment.payment_status}</p>
              `;
            }

            document.getElementById('orderDetails').innerHTML = html;
          } else {
            document.getElementById('orderDetails').innerHTML = '<p>Error loading order details.</p>';
          }
        })
        .catch(error => {
          console.error('Error:', error);
          document.getElementById('orderDetails').innerHTML = '<p>Error loading order details.</p>';
        });
    }

    // ======================
    // INVOICE (DINAMIS)
    // ======================
    function loadInvoice(orderId) {
      fetch(`${BACKEND_API}/order_invoice.php?id=${orderId}`)
        .then(res => res.json())
        .then(json => {
          if (!json.success) {
            alert('Gagal memuat invoice');
            return;
          }

          const o = json.order;
          const items = json.items || [];

          let rows = '';
          items.forEach(i => {
            rows += `
              <tr>
                <td>${i.product_name}</td>
                <td>${i.qty}</td>
                <td>Rp ${Number(i.harga_satuan).toLocaleString('id-ID')}</td>
                <td>Rp ${Number(i.subtotal).toLocaleString('id-ID')}</td>
              </tr>
            `;
          });

          document.querySelector('#invoiceModal .modal-title').innerHTML =
            `Invoice Pesanan #${o.order_code || o.id}`;

          document.querySelector('#invoiceModal .modal-body').innerHTML = `
            <div class="d-flex justify-content-between">
              <div>
                <h6>Effort Outdoor</h6>
                <p>Depok, Jawa Barat<br>Email: support@effortoutdoor.com</p>
              </div>
              <div>
                <p><strong>Tanggal:</strong> ${new Date(o.tanggal_pesan).toLocaleDateString('id-ID')}<br>
                <strong>Pelanggan:</strong> ${o.username}</p>
              </div>
            </div>

            <hr>

            <h6>Alamat Pengiriman</h6>
            <p><strong>${o.nama_penerima}</strong><br>
            ${o.jalan}, ${o.rt_rw}<br>
            ${o.kelurahan}, ${o.kecamatan}, ${o.nama_kota}<br>
            ${o.provinsi} ${o.kode_pos}<br>
            Telp: ${o.penerima_phone || o.alamat_phone || o.user_phone || '-'}</p>

            <table class="table table-bordered mt-3">
              <thead class="table-light">
                <tr>
                  <th>Produk</th>
                  <th>Qty</th>
                  <th>Harga</th>
                  <th>Subtotal</th>
                </tr>
              </thead>
              <tbody>${rows}</tbody>
              <tfoot>
                <tr>
                  <td colspan="3" class="text-end fw-bold">Subtotal</td>
                  <td>Rp ${Number(o.subtotal).toLocaleString('id-ID')}</td>
                </tr>
                <tr>
                  <td colspan="3" class="text-end fw-bold">Ongkir</td>
                  <td>Rp ${Number(o.ongkir).toLocaleString('id-ID')}</td>
                </tr>
                ${o.potongan_voucher > 0 ? `
                <tr>
                  <td colspan="3" class="text-end fw-bold">Voucher (${o.kode_voucher})</td>
                  <td>- Rp ${Number(o.potongan_voucher).toLocaleString('id-ID')}</td>
                </tr>` : ''}
                <tr>
                  <td colspan="3" class="text-end fw-bold">Total</td>
                  <td><strong>Rp ${Number(o.total_harga).toLocaleString('id-ID')}</strong></td>
                </tr>
              </tfoot>
            </table>

            <div class="text-end mt-3">
              <button class="btn btn-primary" onclick="window.print()">
                <i class="fas fa-print"></i> Cetak Invoice
              </button>
            </div>
          `;
        })
        .catch(err => {
          console.error(err);
          alert('Terjadi kesalahan saat memuat invoice');
        });
    }

    // ======================
    // LABEL PENGIRIMAN
    // ======================
    function loadLabel(orderId) {
      // kita pakai data yang sama dengan invoice (order + alamat)
      fetch(`${BACKEND_API}/order_invoice.php?id=${orderId}`)
        .then(res => res.json())
        .then(json => {
          if (!json.success) {
            alert('Gagal memuat label');
            return;
          }

          const o = json.order;

          document.querySelector('#labelModal .modal-body').innerHTML = `
            <div class="border p-3 text-center">
              <h6 class="fw-bold">Effort Outdoor</h6>
              <p>Depok, Jawa Barat</p>
              <hr>
              <p><strong>Kepada:</strong><br>
              ${o.nama_penerima}<br>
              ${o.jalan}, ${o.kelurahan}, ${o.kecamatan}<br>
              ${o.nama_kota}, ${o.provinsi} ${o.kode_pos}<br>
              Telp: ${o.penerima_phone || o.alamat_phone || '-'}</p>
              <hr>
              <p><strong>No. Pesanan:</strong> ${o.order_code || o.id}<br>
              <strong>Tanggal:</strong> ${new Date(o.tanggal_pesan).toLocaleDateString('id-ID')}</p>
            </div>
            <div class="text-end mt-3">
              <button class="btn btn-success" onclick="window.print()">
                <i class="fas fa-print"></i> Cetak Label
              </button>
            </div>
          `;
        })
        .catch(err => {
          console.error(err);
          alert('Terjadi kesalahan saat memuat label');
        });
    }

    // ======================
    // UPDATE STATUS
    // ======================
    function updateOrderStatus(orderId, status) {
      fetch(`${BACKEND_API}/order_update.php`, {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ id: orderId, status: status })
      })
        .then(response => response.json())
        .then(data => {
          if (!data.success) {
            alert('Error updating status: ' + (data.message || 'Unknown error'));
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('Error updating status');
        });
    }
  });
</script>
