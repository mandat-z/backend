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

                        <!-- Page Header -->
                        <div class="page-header">
                            <div class="page-block">
                                <div class="row align-items-center">
                                    <div class="col-md-12">
                                        <div class="page-header-title">
                                            <h5>Kelola Pengeluaran</h5>
                                        </div>
                                        <ul class="breadcrumb">
                                            <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i></a></li>
                                            <li class="breadcrumb-item"><a href="#">Keuangan</a></li>
                                            <li class="breadcrumb-item"><a href="#">Pengeluaran</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <section class="content">
                            <div class="container-fluid">

                                <!-- Statistics Cards -->
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <div class="card border-0 shadow-sm">
                                            <div class="card-body">
                                                <div class="row align-items-center">
                                                    <div class="col-8">
                                                        <h6 class="text-muted mb-2">Pengeluaran Bulan Ini</h6>
                                                        <h3 class="mb-0" id="totalBulanIni">Rp 0</h3>
                                                    </div>
                                                    <div class="col-4 text-right">
                                                        <i class="fas fa-money-bill-wave" style="font-size: 30px; color: #ff5252; opacity: 0.2;"></i>
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
                                                        <h6 class="text-muted mb-2">Pengeluaran Tahun Ini</h6>
                                                        <h3 class="mb-0" id="totalTahunIni">Rp 0</h3>
                                                    </div>
                                                    <div class="col-4 text-right">
                                                        <i class="fas fa-chart-bar" style="font-size: 30px; color: #4CAF50; opacity: 0.2;"></i>
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
                                                        <h6 class="text-muted mb-2">Jumlah Transaksi</h6>
                                                        <h3 class="mb-0" id="jumlahTransaksi">0</h3>
                                                    </div>
                                                    <div class="col-4 text-right">
                                                        <i class="fas fa-receipt" style="font-size: 30px; color: #2196F3; opacity: 0.2;"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Filter & Tambah Button -->
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <div class="row align-items-end">
                                            <div class="col-md-2">
                                                <label>Filter Bulan</label>
                                                <input type="month" id="filterBulan" class="form-control">
                                            </div>
                                            <div class="col-md-2">
                                                <label>Filter Kategori</label>
                                                <select id="filterKategori" class="form-control">
                                                    <option value="">Semua Kategori</option>
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <button class="btn btn-secondary btn-block" onclick="resetFilter()">
                                                    <i class="fas fa-redo"></i> Filter
                                                </button>
                                            </div>
                                            <div class="col-md-3"></div>
                                            <div class="col-md-3">
                                                <button class="btn btn-success btn-block" data-toggle="modal" data-target="#tambahPengeluaranModal">
                                                    <i class="fas fa-plus"></i> Tambah Pengeluaran
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Data Table -->
                                <div class="card">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h3 class="card-title mb-0">Daftar Pengeluaran</h3>
                                    </div>

                                    <div class="card-body table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead class="bg-primary text-white text-center">
                                                <tr>
                                                    <th width="5%">No</th>
                                                    <th width="12%">Tanggal</th>
                                                    <th width="15%">Kategori</th>
                                                    <th>Deskripsi</th>
                                                    <th width="15%">Nominal</th>
                                                    <th width="12%">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody id="pengeluaranTable">
                                                <tr>
                                                    <td colspan="6" class="text-center">Memuat data...</td>
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

<!-- Modal Tambah Pengeluaran -->
<div class="modal fade" id="tambahPengeluaranModal" tabindex="-1" role="dialog" aria-labelledby="tambahLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form id="tambahForm">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="tambahLabel">Tambah Pengeluaran</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Tanggal <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Kategori <span class="text-danger">*</span></label>
                        <select name="kategori" class="form-control" required>
                            <option value="">-- Pilih Kategori --</option>
                            <option value="Operasional">Operasional</option>
                            <option value="Gaji Karyawan">Gaji Karyawan</option>
                            <option value="Sewa Tempat">Sewa Tempat</option>
                            <option value="Listrik/Air">Listrik/Air</option>
                            <option value="Pengiriman">Pengiriman</option>
                            <option value="Marketing">Marketing</option>
                            <option value="Maintenance">Maintenance</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Deskripsi</label>
                        <textarea name="deskripsi" class="form-control" rows="3" placeholder="Masukkan deskripsi pengeluaran (opsional)"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Nominal <span class="text-danger">*</span></label>
                        <input type="text" name="nominal" class="form-control" id="nominalInput" placeholder="Rp 0" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Pengeluaran -->
<div class="modal fade" id="editPengeluaranModal" tabindex="-1" role="dialog" aria-labelledby="editLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form id="editForm">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="editLabel">Edit Pengeluaran</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_pengeluaran" id="editId">
                    <div class="form-group">
                        <label>Tanggal <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Kategori <span class="text-danger">*</span></label>
                        <select name="kategori" class="form-control" required>
                            <option value="">-- Pilih Kategori --</option>
                            <option value="Operasional">Operasional</option>
                            <option value="Gaji Karyawan">Gaji Karyawan</option>
                            <option value="Sewa Tempat">Sewa Tempat</option>
                            <option value="Listrik/Air">Listrik/Air</option>
                            <option value="Pengiriman">Pengiriman</option>
                            <option value="Marketing">Marketing</option>
                            <option value="Maintenance">Maintenance</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Deskripsi</label>
                        <textarea name="deskripsi" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Nominal <span class="text-danger">*</span></label>
                        <input type="text" name="nominal" class="form-control" id="editNominalInput" placeholder="Rp 0" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="<?php echo ASSET; ?>/js/vendor-all.min.js"></script>
<script src="<?php echo ASSET; ?>/plugins/bootstrap/js/bootstrap.min.js"></script>
<script src="<?php echo ASSET; ?>/js/pcoded.min.js"></script>

<script>
    const API_URL = '<?php echo BACKEND_URL; ?>/api';

    // Format rupiah
    function formatRupiah(num) {
        if (!num) return 'Rp 0';
        return 'Rp ' + parseInt(num).toLocaleString('id-ID');
    }

    // Parse angka dari input rupiah
    function parseNominal(str) {
        return str.replace(/[^\d]/g, '');
    }

    // Load data pengeluaran
    function loadPengeluaran() {
        const bulan = document.getElementById('filterBulan').value;
        const kategori = document.getElementById('filterKategori').value;

        let url = API_URL + '/pengeluaran_list.php?';

        if (bulan) {
            const [tahun, bln] = bulan.split('-');
            url += `tahun=${tahun}&bulan=${bln}&`;
        } else {
            url += `tahun=${new Date().getFullYear()}&`;
        }

        if (kategori) {
            url += `kategori=${kategori}&`;
        }

        fetch(url)
            .then(res => res.json())
            .then(result => {
                if (result.status === 'success') {
                    renderTable(result.data, result.total);
                    updateStatistics(result.data);
                }
            })
            .catch(err => console.error('Error:', err));
    }

    // Render tabel
    function renderTable(data, total) {
        const tbody = document.getElementById('pengeluaranTable');

        if (data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">Tidak ada data pengeluaran</td></tr>';
            return;
        }

        tbody.innerHTML = data.map((row, index) => `
        <tr>
            <td class="text-center">${index + 1}</td>
            <td>${row.tanggal}</td>
            <td><span class="badge badge-info">${row.kategori}</span></td>
            <td>${row.deskripsi || '-'}</td>
            <td class="text-right font-weight-bold">${formatRupiah(row.nominal)}</td>
            <td class="text-center">
                <button class="btn btn-sm btn-primary editBtn" data-id="${row.id_pengeluaran}">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-sm btn-danger deleteBtn" data-id="${row.id_pengeluaran}">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>
    `).join('');

        attachEventListeners();
    }

    // Attach event listeners untuk edit dan delete buttons
    function attachEventListeners() {
        $('.editBtn').off('click').on('click', function() {
            const id = $(this).data('id');
            editPengeluaran(id);
        });

        $('.deleteBtn').off('click').on('click', function() {
            const id = $(this).data('id');
            deletePengeluaran(id);
        });
    }

    // Update statistik
    function updateStatistics(data) {
        const now = new Date();
        const bulanIni = now.getMonth() + 1;
        const tahunIni = now.getFullYear();

        let totalBulanIni = 0;
        let totalTahunIni = 0;

        data.forEach(row => {
            const date = new Date(row.tanggal);
            const bulan = date.getMonth() + 1;
            const tahun = date.getFullYear();
            const nominal = parseFloat(row.nominal);

            if (tahun === tahunIni) {
                totalTahunIni += nominal;
                if (bulan === bulanIni) {
                    totalBulanIni += nominal;
                }
            }
        });

        document.getElementById('totalBulanIni').textContent = formatRupiah(totalBulanIni);
        document.getElementById('totalTahunIni').textContent = formatRupiah(totalTahunIni);
        document.getElementById('jumlahTransaksi').textContent = data.length;
    }

    // Load kategori unik
    function loadKategoriFilter() {
        fetch(API_URL + '/pengeluaran_list.php?tahun=' + new Date().getFullYear())
            .then(res => res.json())
            .then(result => {
                if (result.status === 'success') {
                    const kategoris = [...new Set(result.data.map(d => d.kategori))];
                    const select = document.getElementById('filterKategori');
                    kategoris.forEach(kat => {
                        const option = document.createElement('option');
                        option.value = kat;
                        option.textContent = kat;
                        select.appendChild(option);
                    });
                }
            });
    }

    // Set bulan ke bulan sekarang
    function setCurrentMonth() {
        const now = new Date();
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        document.getElementById('filterBulan').value = `${year}-${month}`;
    }

    // Reset filter
    function resetFilter() {
        document.getElementById('filterBulan').value = '';
        document.getElementById('filterKategori').value = '';
        loadPengeluaran();
    }

    // Edit pengeluaran
    function editPengeluaran(id) {
        fetch(API_URL + '/pengeluaran_get.php?id=' + id)
            .then(res => res.json())
            .then(result => {
                if (result.status === 'success') {
                    const data = result.data;
                    $('#editId').val(data.id_pengeluaran);
                    $('#editForm input[name="tanggal"]').val(data.tanggal);
                    $('#editForm select[name="kategori"]').val(data.kategori);
                    $('#editForm textarea[name="deskripsi"]').val(data.deskripsi || '');
                    $('#editNominalInput').val(formatRupiah(data.nominal));
                    $('#editPengeluaranModal').modal('show');
                } else {
                    alert('Error: ' + result.message);
                }
            })
            .catch(err => alert('Error: ' + err));
    }

    // Delete pengeluaran
    function deletePengeluaran(id) {
        if (!confirm('Apakah Anda yakin ingin menghapus pengeluaran ini?')) return;

        const formData = new FormData();
        formData.append('id_pengeluaran', id);

        fetch(API_URL + '/pengeluaran_delete.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(result => {
                alert(result.message);
                if (result.status === 'success') {
                    loadPengeluaran();
                }
            })
            .catch(err => alert('Error: ' + err));
    }

    $(document).ready(function() {
        // Format input nominal
        $(document).on('input', 'input[name="nominal"]', function() {
            const value = parseNominal($(this).val());
            $(this).val(value ? 'Rp ' + parseInt(value).toLocaleString('id-ID') : '');
        });

        // Form tambah
        $('#tambahForm').on('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const nominal = formData.get('nominal');
            formData.set('nominal', parseNominal(nominal));

            fetch(API_URL + '/pengeluaran_create.php', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(result => {
                    alert(result.message);
                    if (result.status === 'success') {
                        $('#tambahPengeluaranModal').modal('hide');
                        this.reset();
                        loadPengeluaran();
                    }
                })
                .catch(err => alert('Error: ' + err));
        });

        // Form edit
        $('#editForm').on('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const nominal = formData.get('nominal');
            formData.set('nominal', parseNominal(nominal));

            fetch(API_URL + '/pengeluaran_update.php', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(result => {
                    alert(result.message);
                    if (result.status === 'success') {
                        $('#editPengeluaranModal').modal('hide');
                        loadPengeluaran();
                    }
                })
                .catch(err => alert('Error: ' + err));
        });

        // Event listeners untuk filter
        $('#filterBulan').on('change', loadPengeluaran);
        $('#filterKategori').on('change', loadPengeluaran);

        // Initialize
        setCurrentMonth();
        loadKategoriFilter();
        loadPengeluaran();
    });
</script>