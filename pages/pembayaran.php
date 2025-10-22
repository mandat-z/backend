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

            <div class="page-header">
              <div class="page-block">
                <div class="row align-items-center">
                  <div class="col-md-12">
                    <div class="page-header-title">
                      <h5>Kelola Metode Pembayaran</h5>
                    </div>
                    <ul class="breadcrumb">
                      <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i></a></li>
                      <li class="breadcrumb-item"><a href="#">Pembayaran</a></li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>

            <section class="content">
              <div class="container-fluid">
                <div class="card">
                  <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Daftar Metode Pembayaran</h3>
                    <button class="btn btn-success" data-toggle="modal" data-target="#tambahPembayaranModal">
                      <i class="fas fa-plus"></i> Tambah Metode
                    </button>
                  </div>

                  <div class="card-body table-responsive">
                    <table class="table table-bordered table-striped">
                      <thead class="bg-success text-white text-center">
                        <tr>
                          <th width="5%">No</th>
                          <th>Nama Metode</th>
                          <th>Jenis</th>
                          <th>Tujuan</th>
                          <th>QR Code</th>
                          <th>Keterangan</th>
                          <th>Status</th>
                          <th width="15%">Aksi</th>
                        </tr>
                      </thead>
                      <tbody id="pembayaranTable">
                        <tr><td colspan="8" class="text-center">Memuat data...</td></tr>
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

<!-- Modal Tambah -->
<div class="modal fade" id="tambahPembayaranModal" tabindex="-1" role="dialog" aria-labelledby="tambahLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form id="tambahForm" enctype="multipart/form-data">
      <div class="modal-content">
        <div class="modal-header bg-success text-white">
          <h5 class="modal-title" id="tambahLabel">Tambah Metode Pembayaran</h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Nama Metode</label>
            <input type="text" name="nama_metode" class="form-control" required>
          </div>
          <div class="form-group">
            <label>Jenis</label>
            <select name="jenis" class="form-control" required>
              <option value="">-- Pilih Jenis --</option>
              <option value="Transfer Bank">Transfer Bank</option>
              <option value="E-Wallet">E-Wallet</option>
              <option value="COD">COD</option>
              <option value="QRIS">QRIS</option>
            </select>
          </div>
          <div class="form-group">
            <label>Tujuan Pembayaran</label>
            <textarea name="tujuan" class="form-control" rows="2"></textarea>
          </div>
          <div class="form-group">
            <label>Keterangan</label>
            <textarea name="keterangan" class="form-control" rows="2"></textarea>
          </div>
          <div class="form-group">
            <label>Upload QR (Opsional)</label>
            <input type="file" name="qr_image" class="form-control" accept="image/*">
          </div>
          <div class="form-group">
            <label>Status</label>
            <select name="status" class="form-control">
              <option value="Aktif">Aktif</option>
              <option value="Nonaktif">Nonaktif</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Simpan</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="editPembayaranModal" tabindex="-1" role="dialog" aria-labelledby="editLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form id="editForm" enctype="multipart/form-data">
      <div class="modal-content">
        <div class="modal-header bg-warning text-white">
          <h5 class="modal-title" id="editLabel">Edit Metode Pembayaran</h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id_pembayaran" id="edit_id">
          <div class="form-group">
            <label>Nama Metode</label>
            <input type="text" name="nama_metode" id="edit_nama" class="form-control" required>
          </div>
          <div class="form-group">
            <label>Jenis</label>
            <select name="jenis" id="edit_jenis" class="form-control" required>
              <option value="Transfer Bank">Transfer Bank</option>
              <option value="E-Wallet">E-Wallet</option>
              <option value="COD">COD</option>
              <option value="QRIS">QRIS</option>
            </select>
          </div>
          <div class="form-group">
            <label>Tujuan</label>
            <textarea name="tujuan" id="edit_tujuan" class="form-control" rows="2"></textarea>
          </div>
          <div class="form-group">
            <label>Keterangan</label>
            <textarea name="keterangan" id="edit_keterangan" class="form-control" rows="2"></textarea>
          </div>
          <div class="form-group">
            <label>Upload QR Baru (Opsional)</label>
            <input type="file" name="qr_image" class="form-control" accept="image/*">
          </div>
          <div class="form-group">
            <label>Status</label>
            <select name="status" id="edit_status" class="form-control">
              <option value="Aktif">Aktif</option>
              <option value="Nonaktif">Nonaktif</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-warning">Simpan Perubahan</button>
        </div>
      </div>
    </form>
  </div>
</div>

<script src="../assets/js/vendor-all.min.js"></script>
<script src="../assets/plugins/bootstrap/js/bootstrap.min.js"></script>
<script src="../assets/js/pcoded.min.js"></script>

<script>
$(document).ready(function() {
  loadData();

  function loadData() {
    fetch('../api/payment_get.php')
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          renderTable(data.data);
        } else {
          alert('Error: ' + data.message);
        }
      })
      .catch(err => alert('Error loading data: ' + err));
  }

  function renderTable(pembayaran) {
    const tbody = $('#pembayaranTable');
    tbody.empty();

    if (pembayaran.length === 0) {
      tbody.html('<tr><td colspan="8" class="text-center">Belum ada data metode pembayaran.</td></tr>');
      return;
    }

    pembayaran.forEach((row, i) => {
      const qrImg = row.qr_image 
        ? `<img src="../assets/images/qr/${row.qr_image}" width="60" height="60" class="border rounded">`
        : '<span class="text-muted">-</span>';

      const statusBadge = `<span class="badge ${row.status === 'Aktif' ? 'badge-success' : 'badge-secondary'}">${row.status}</span>`;

      tbody.append(`
        <tr>
          <td class="text-center">${i + 1}</td>
          <td>${escapeHtml(row.nama_metode)}</td>
          <td>${escapeHtml(row.jenis)}</td>
          <td>${escapeHtml(row.tujuan).replace(/\n/g, '<br>')}</td>
          <td class="text-center">${qrImg}</td>
          <td>${escapeHtml(row.keterangan).replace(/\n/g, '<br>')}</td>
          <td class="text-center">${statusBadge}</td>
          <td class="text-center">
            <button class="btn btn-sm btn-warning editBtn" 
              data-id="${row.id_pembayaran}" 
              data-nama="${escapeHtml(row.nama_metode)}" 
              data-jenis="${escapeHtml(row.jenis)}" 
              data-tujuan="${escapeHtml(row.tujuan)}" 
              data-keterangan="${escapeHtml(row.keterangan)}" 
              data-status="${escapeHtml(row.status)}">
              <i class="fas fa-edit"></i>
            </button>
            <button class="btn btn-sm btn-danger deleteBtn" data-id="${row.id_pembayaran}">
              <i class="fas fa-trash"></i>
            </button>
          </td>
        </tr>
      `);
    });

    attachEventListeners();
  }

  function attachEventListeners() {
    $('.editBtn').off('click').on('click', function() {
      const btn = $(this);
      $('#edit_id').val(btn.data('id'));
      $('#edit_nama').val(btn.data('nama'));
      $('#edit_jenis').val(btn.data('jenis'));
      $('#edit_tujuan').val(btn.data('tujuan'));
      $('#edit_keterangan').val(btn.data('keterangan'));
      $('#edit_status').val(btn.data('status'));
      $('#editPembayaranModal').modal('show');
    });

    $('.deleteBtn').off('click').on('click', function() {
      const id = $(this).data('id');
      if (confirm('Yakin ingin menghapus metode ini?')) {
        fetch(`../api/payment_delete.php?id=${id}`)
          .then(res => res.json())
          .then(data => {
            alert(data.message);
            if (data.success) loadData();
          })
          .catch(err => alert('Error: ' + err));
      }
    });
  }

  // Form Tambah
  $('#tambahForm').on('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    fetch('../api/payment_add.php', { method: 'POST', body: formData })
      .then(res => res.json())
      .then(data => {
        alert(data.message);
        if (data.success) {
          $('#tambahPembayaranModal').modal('hide');
          this.reset();
          loadData();
        }
      })
      .catch(err => alert('Error: ' + err));
  });

  // Form Edit
  $('#editForm').on('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    fetch('../api/payment_edit.php', { method: 'POST', body: formData })
      .then(res => res.json())
      .then(data => {
        alert(data.message);
        if (data.success) {
          $('#editPembayaranModal').modal('hide');
          loadData();
        }
      })
      .catch(err => alert('Error: ' + err));
  });

  function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
  }
});
</script>