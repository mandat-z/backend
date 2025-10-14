<?php
include __DIR__ . '/../config/config.php';
include __DIR__ . '/../includes/head.php';         // head (CSS) dulu
include __DIR__ . '/../includes/navigation.php';   // sidebar
include __DIR__ . '/../includes/topbar.php';       // topbar
?>


<!-- [ Main Content ] start -->
<div class="pcoded-main-container">
    <div class="pcoded-wrapper">
        <div class="pcoded-content">
            <div class="pcoded-inner-content">
                <div class="main-body">
                    <div class="page-wrapper">

                        <!-- page header -->
                        <div class="page-header">
                            <div class="page-block">
                                <div class="row align-items-center">
                                    <div class="col-md-12">
                                        <div class="page-header-title">
                                            <h5>Daftar Produk</h5>
                                        </div>
                                        <ul class="breadcrumb">
                                            <li class="breadcrumb-item"><a href="../index.php"><i class="fas fa-home"></i></a></li>
                                            <li class="breadcrumb-item"><a href="#!">Produk</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- controls -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-body d-flex justify-content-between align-items-center">
                                        <div>
                                            <button class="btn btn-primary" data-toggle="modal" data-target="#modalAddProduct">
                                                <i class="fas fa-plus"></i> Tambah Produk
                                            </button>
                                            <button class="btn btn-outline-secondary" data-toggle="modal" data-target="#modalKategori">
                                                <i class="fas fa-list"></i> Kategori Produk
                                            </button>
                                            <button class="btn btn-outline-info" data-toggle="modal" data-target="#modalGambarProduk">
                                                <i class="fas fa-image"></i> Gambar Produk
                                            </button>
                                        </div>
                                        <div>
                                            <input type="text" id="searchProduct" class="form-control" placeholder="Cari produk..." style="width:260px;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- daftar produk -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card table-card">
                                    <div class="card-header">
                                        <h5>Daftar Produk</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-hover" id="productTable">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Gambar</th>
                                                        <th>Nama Produk</th>
                                                        <th>SKU</th>
                                                        <th>Kategori</th>
                                                        <th>Harga</th>
                                                        <th>Stok</th>
                                                        <th>Status</th>
                                                        <th>Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <!-- contoh baris; di implementasi nyata, generate dari DB -->
                                                    <tr>
                                                        <td>1</td>
                                                        <td><img src="../assets/images/widget/p1.jpg" alt="" style="width:60px;height:40px;object-fit:cover;border-radius:4px;"></td>
                                                        <td>Contoh Produk A</td>
                                                        <td>SKU-001</td>
                                                        <td>Elektronik</td>
                                                        <td>Rp 250.000</td>
                                                        <td>120</td>
                                                        <td><span class="badge badge-success">Aktif</span></td>
                                                        <td>
                                                            <button class="btn btn-sm btn-warning btn-edit" 
                                                                data-id="1"
                                                                data-nama="Contoh Produk A"
                                                                data-sku="SKU-001"
                                                                data-kategori="Elektronik"
                                                                data-harga="250000"
                                                                data-stok="120"
                                                                data-deskripsi="Deskripsi singkat..."
                                                                data-gambar="../assets/images/widget/p1.jpg"
                                                                title="Edit"><i class="fas fa-edit"></i></button>

                                                            <button class="btn btn-sm btn-danger btn-delete" data-id="1" title="Hapus"><i class="fas fa-trash-alt"></i></button>
                                                        </td>
                                                    </tr>

                                                    <tr>
                                                        <td>2</td>
                                                        <td><img src="../assets/images/widget/p2.jpg" alt="" style="width:60px;height:40px;object-fit:cover;border-radius:4px;"></td>
                                                        <td>Contoh Produk B</td>
                                                        <td>SKU-002</td>
                                                        <td>Fashion</td>
                                                        <td>Rp 120.000</td>
                                                        <td>35</td>
                                                        <td><span class="badge badge-secondary">Draft</span></td>
                                                        <td>
                                                            <button class="btn btn-sm btn-warning btn-edit" 
                                                                data-id="2"
                                                                data-nama="Contoh Produk B"
                                                                data-sku="SKU-002"
                                                                data-kategori="Fashion"
                                                                data-harga="120000"
                                                                data-stok="35"
                                                                data-deskripsi="Deskripsi produk B..."
                                                                data-gambar="../assets/images/widget/p2.jpg"
                                                                title="Edit"><i class="fas fa-edit"></i></button>

                                                            <button class="btn btn-sm btn-danger btn-delete" data-id="2" title="Hapus"><i class="fas fa-trash-alt"></i></button>
                                                        </td>
                                                    </tr>

                                                    <!-- tambah baris produk disini -->
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="card-footer text-right">
                                        <nav>
                                            <ul class="pagination">
                                                <li class="page-item disabled"><a class="page-link" href="#">Prev</a></li>
                                                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                                                <li class="page-item"><a class="page-link" href="#">2</a></li>
                                                <li class="page-item"><a class="page-link" href="#">Next</a></li>
                                            </ul>
                                        </nav>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end daftar produk -->

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Tambah Produk -->
<div class="modal fade" id="modalAddProduct" tabindex="-1" role="dialog" aria-labelledby="modalAddProductLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form id="formAddProduct" enctype="multipart/form-data">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Produk</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label>Nama Produk</label>
                                <input type="text" name="nama" class="form-control" required>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>SKU</label>
                                    <input type="text" name="sku" class="form-control">
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Harga (Rp)</label>
                                    <input type="number" name="harga" class="form-control" required>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Kategori</label>
                                    <select name="kategori" class="form-control">
                                        <option>Elektronik</option>
                                        <option>Fashion</option>
                                        <option>Rumah Tangga</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Stok</label>
                                    <input type="number" name="stok" class="form-control">
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Deskripsi</label>
                                <textarea name="deskripsi" class="form-control" rows="4"></textarea>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group text-center">
                                <label>Gambar Produk</label>
                                <div class="mb-2">
                                    <img id="previewAdd" src="../assets/images/widget/img-round1.jpg" alt="preview" style="width:100%;height:150px;object-fit:cover;border-radius:6px;">
                                </div>
                                <input type="file" name="gambar" id="inputAddImage" accept="image/*" class="form-control-file">
                            </div>
                            <div class="form-group">
                                <label>Status</label>
                                <select name="status" class="form-control">
                                    <option value="aktif">Aktif</option>
                                    <option value="draft">Draft</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Produk</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Edit Produk (sama struktur dengan Add) -->
<div class="modal fade" id="modalEditProduct" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form id="formEditProduct" enctype="multipart/form-data">
            <input type="hidden" name="id" id="edit_id">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Produk</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label>Nama Produk</label>
                                <input type="text" id="edit_nama" name="nama" class="form-control" required>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>SKU</label>
                                    <input type="text" id="edit_sku" name="sku" class="form-control">
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Harga (Rp)</label>
                                    <input type="number" id="edit_harga" name="harga" class="form-control" required>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Kategori</label>
                                    <select id="edit_kategori" name="kategori" class="form-control">
                                        <option>Elektronik</option>
                                        <option>Fashion</option>
                                        <option>Rumah Tangga</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Stok</label>
                                    <input type="number" id="edit_stok" name="stok" class="form-control">
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Deskripsi</label>
                                <textarea id="edit_deskripsi" name="deskripsi" class="form-control" rows="4"></textarea>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group text-center">
                                <label>Gambar Produk</label>
                                <div class="mb-2">
                                    <img id="previewEdit" src="../assets/images/widget/img-round1.jpg" alt="preview" style="width:100%;height:150px;object-fit:cover;border-radius:6px;">
                                </div>
                                <input type="file" name="gambar" id="inputEditImage" accept="image/*" class="form-control-file">
                            </div>
                            <div class="form-group">
                                <label>Status</label>
                                <select id="edit_status" name="status" class="form-control">
                                    <option value="aktif">Aktif</option>
                                    <option value="draft">Draft</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update Produk</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Hapus Konfirmasi -->
<div class="modal fade" id="modalDelete" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-body text-center">
                <h5>Yakin ingin menghapus produk ini?</h5>
                <p class="text-muted">Tindakan ini tidak dapat dikembalikan.</p>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Hapus</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Kategori Produk -->
<div class="modal fade" id="modalKategori" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form id="formKategori">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Kategori Produk</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <ul class="list-group mb-3" id="kategoriList">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Elektronik
                            <span>
                                <button class="btn btn-sm btn-outline-warning"><i class="fas fa-edit"></i></button>
                                <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash-alt"></i></button>
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Fashion
                            <span>
                                <button class="btn btn-sm btn-outline-warning"><i class="fas fa-edit"></i></button>
                                <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash-alt"></i></button>
                            </span>
                        </li>
                    </ul>

                    <div class="input-group">
                        <input type="text" id="newKategori" class="form-control" placeholder="Tambah kategori baru">
                        <div class="input-group-append">
                            <button class="btn btn-primary" id="addKategoriBtn" type="button"><i class="fas fa-plus"></i> Tambah</button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Gambar Produk -->
<div class="modal fade" id="modalGambarProduk" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form id="formGambarProduk" enctype="multipart/form-data">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Gambar Produk</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="row" id="imageGallery">
                        <div class="col-md-3 mb-3">
                            <div class="card">
                                <img src="../assets/images/widget/p1.jpg" class="card-img-top" style="height:140px;object-fit:cover;">
                                <div class="card-body p-2 text-center">
                                    <button class="btn btn-sm btn-danger btn-block">Hapus</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card">
                                <img src="../assets/images/widget/p2.jpg" class="card-img-top" style="height:140px;object-fit:cover;">
                                <div class="card-body p-2 text-center">
                                    <button class="btn btn-sm btn-danger btn-block">Hapus</button>
                                </div>
                            </div>
                        </div>
                        <!-- more thumbnails -->
                    </div>

                    <hr>
                    <div class="form-group">
                        <label>Upload Gambar Baru</label>
                        <input type="file" name="gambars[]" accept="image/*" class="form-control-file" multiple>
                        <small class="form-text text-muted">Pilih beberapa gambar sekaligus.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button class="btn btn-primary" type="submit">Upload</button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php
// include footer script (jika ada global footer)
// include __DIR__ . '/../includes/footer.php';
?>

<!-- minimal JS untuk demo (jQuery sudah dimuat pada template) -->
<script src="../assets/js/vendor-all.min.js"></script>
<script src="../assets/plugins/bootstrap/js/bootstrap.min.js"></script>
<script src="../assets/js/pcoded.min.js"></script>
<script>
    // preview image add
    document.getElementById('inputAddImage')?.addEventListener('change', function (e) {
        const file = e.target.files[0];
        if (!file) return;
        const url = URL.createObjectURL(file);
        document.getElementById('previewAdd').src = url;
    });

    // preview image edit
    document.getElementById('inputEditImage')?.addEventListener('change', function (e) {
        const file = e.target.files[0];
        if (!file) return;
        const url = URL.createObjectURL(file);
        document.getElementById('previewEdit').src = url;
    });

    // klik edit => buka modal dan isi data
    Array.from(document.querySelectorAll('.btn-edit')).forEach(btn=>{
        btn.addEventListener('click', function(){
            const id = this.dataset.id;
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_nama').value = this.dataset.nama || '';
            document.getElementById('edit_sku').value = this.dataset.sku || '';
            document.getElementById('edit_kategori').value = this.dataset.kategori || '';
            document.getElementById('edit_harga').value = this.dataset.harga || '';
            document.getElementById('edit_stok').value = this.dataset.stok || '';
            document.getElementById('edit_deskripsi').value = this.dataset.deskripsi || '';
            if(this.dataset.gambar){
                document.getElementById('previewEdit').src = this.dataset.gambar;
            }
            $('#modalEditProduct').modal('show');
        });
    });

    // delete flow (demo)
    let deleteTargetId = null;
    Array.from(document.querySelectorAll('.btn-delete')).forEach(btn=>{
        btn.addEventListener('click', function(){
            deleteTargetId = this.dataset.id;
            $('#modalDelete').modal('show');
        });
    });
    document.getElementById('confirmDelete')?.addEventListener('click', function(){
        if(!deleteTargetId) return;
        // panggil AJAX untuk hapus di server, sekarang demo: hapus baris dari tabel
        const row = document.querySelector('.btn-delete[data-id="'+deleteTargetId+'"]').closest('tr');
        row?.parentNode.removeChild(row);
        $('#modalDelete').modal('hide');
    });

    // tambah kategori demo
    document.getElementById('addKategoriBtn')?.addEventListener('click', function(){
        const val = document.getElementById('newKategori').value.trim();
        if(!val) return;
        const ul = document.getElementById('kategoriList');
        const li = document.createElement('li');
        li.className = 'list-group-item d-flex justify-content-between align-items-center';
        li.innerHTML = val + ' <span><button class="btn btn-sm btn-outline-warning"><i class="fas fa-edit"></i></button> <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash-alt"></i></button></span>';
        ul.appendChild(li);
        document.getElementById('newKategori').value = '';
    });
</script>