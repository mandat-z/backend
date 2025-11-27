<?php
include __DIR__ . '/../config/config.php';
include __DIR__ . '/../includes/head.php';
include __DIR__ . '/../includes/navigation.php';
include __DIR__ . '/../includes/topbar.php';
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
                      <h5>Daftar Pelanggan</h5>
                    </div>
                    <ul class="breadcrumb">
                      <li class="breadcrumb-item"><a href="../index.php"><i class="fas fa-home"></i></a></li>
                      <li class="breadcrumb-item"><a href="#!">Pelanggan</a></li>
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
                      <button class="btn btn-primary" data-toggle="modal" data-target="#modalAddPelanggan">
                        <i class="fas fa-plus"></i> Tambah User
                      </button>
                    </div>
                    <div>
                      <input type="text" id="searchPelanggan" class="form-control" placeholder="Cari pelanggan..." style="width:260px;">
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- daftar pelanggan -->
            <div class="row">
              <div class="col-md-12">
                <div class="card table-card">
                  <div class="card-header">
                    <h5>Daftar User</h5>
                  </div>
                  <div class="card-body">
                    <div class="table-responsive">
                      <table class="table table-hover" id="pelangganTable">
                        <thead>
                          <tr>
                            <th>#</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Telepon</th>
                            <th>Alamat</th>
                            <th>Tanggal Bergabung</th>
                            <th>Role</th>
                            <th>Aksi</th>
                          </tr>
                        </thead>
                        <tbody id="usersTbody">
                          <?php
                          // fetch users for table (done server-side for initial render)
                          $db = get_db();
                          $stmt = $db->query('SELECT id,username,email,phone,COALESCE(birthdate,"") AS birthdate,gender,role,created_at FROM users ORDER BY created_at DESC');
                          $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
                          $n=1;
                          foreach($users as $u):
                          ?>
                          <tr data-id="<?= $u['id'] ?>">
                            <td><?= $n++ ?></td>
                            <td><?= htmlspecialchars($u['username']) ?></td>
                            <td><?= htmlspecialchars($u['email']) ?></td>
                            <td><?= htmlspecialchars($u['phone']) ?></td>
                            <td><!-- alamat count (optional) -->
                              <?php
                                $c = $db->prepare('SELECT COUNT(*) FROM user_addresses WHERE user_id=:uid');
                                $c->execute([':uid'=>$u['id']]);
                                echo intval($c->fetchColumn());
                              ?>
                            </td>
                            <td><?= htmlspecialchars($u['created_at']) ?></td>
                            <td><?= htmlspecialchars($u['role']) ?></td>
                            <td>
                              <button class="btn btn-sm btn-warning btn-edit-user" data-id="<?= $u['id'] ?>"><i class="fas fa-edit"></i></button>
                              <button class="btn btn-sm btn-danger btn-delete-user" data-id="<?= $u['id'] ?>"><i class="fas fa-trash"></i></button>
                              <button class="btn btn-sm btn-info btn-address" data-id="<?= $u['id'] ?>"><i class="fas fa-list"></i></button>
                            </td>
                          </tr>
                          <?php endforeach; ?>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>

          </div> <!-- end page-wrapper -->
        </div>
      </div>
    </div>
  </div>
</div>
<!-- [ Main Content ] end -->

<!-- Modal Tambah Pelanggan -->
<div class="modal fade" id="modalAddPelanggan" tabindex="-1" aria-labelledby="modalAddPelangganLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form id="formAddUser">
        <div class="modal-header">
          <h5 class="modal-title">Tambah User</h5>
          <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Nama</label>
            <input type="text" class="form-control" placeholder="Masukkan nama" name="username" required>
          </div>
          <div class="form-group">
            <label>Email</label>
            <input type="email" class="form-control" placeholder="Masukkan email" name="email" required>
          </div>
          <div class="form-row">
            <div class="form-group col-md-6">
              <label>Password</label>
              <input type="password" class="form-control" placeholder="Password" name="password" required>
            </div>
            <div class="form-group col-md-6">
              <label>Role</label>
              <select class="form-control" name="role">
                <option value="pelanggan">Pelanggan</option>
                <option value="admin">Admin</option>
              </select>
            </div>
          </div>

          <div class="form-group">
            <label>Telepon</label>
            <input type="text" class="form-control" placeholder="Masukkan nomor telepon" name="phone">
          </div>

          <hr>
          <h6>Alamat (opsional)</h6>
          <div class="form-group">
            <label>Nama Penerima</label>
            <input type="text" class="form-control" name="recipient_name" placeholder="Nama penerima">
          </div>
          <div class="form-group">
            <label>Alamat Lengkap</label>
            <textarea class="form-control" name="address" rows="2" placeholder="Jalan, RT/RW, Blok, dsb"></textarea>
          </div>
          <div class="form-row">
            <div class="form-group col-md-4">
              <label>Kota</label>
              <input type="text" class="form-control" name="city">
            </div>
            <div class="form-group col-md-4">
              <label>Kode Pos</label>
              <input type="text" class="form-control" name="postcode">
            </div>
            <div class="form-group col-md-4">
              <label>Telepon Penerima</label>
              <input type="text" class="form-control" name="addr_phone">
            </div>
          </div>
          <div class="form-group form-check">
            <input type="checkbox" class="form-check-input" id="addrDefault" name="is_default" value="1">
            <label class="form-check-label" for="addrDefault">Jadikan alamat default</label>
          </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Edit Pelanggan -->
<div class="modal fade" id="modalEditPelanggan" tabindex="-1" aria-labelledby="modalEditPelangganLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form id="formEditUser">
        <input type="hidden" name="id">
        <div class="modal-header">
          <h5 class="modal-title">Edit Pelanggan</h5>
          <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Nama</label>
            <input type="text" class="form-control" name="username" required>
          </div>
          <div class="form-group">
            <label>Email</label>
            <input type="email" class="form-control" name="email" required>
          </div>
          <div class="form-row">
            <div class="form-group col-md-6">
              <label>Telepon</label>
              <input type="text" class="form-control" name="phone">
            </div>
            <div class="form-group col-md-6">
              <label>Birthdate</label>
              <input type="date" class="form-control" name="birthdate">
            </div>
          </div>

          <div class="form-row">
            <div class="form-group col-md-6">
              <label>Gender</label>
              <select class="form-control" name="gender">
                <option value="">-</option>
                <option value="L">Laki-laki</option>
                <option value="P">Perempuan</option>
              </select>
            </div>
            <div class="form-group col-md-6">
              <label>Role</label>
              <select class="form-control" name="role">
                <option value="pelanggan">Pelanggan</option>
                <option value="admin">Admin</option>
              </select>
            </div>
          </div>

          <div class="form-group">
            <label>Password baru (kosongkan jika tidak diubah)</label>
            <input type="password" class="form-control" name="password">
          </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Riwayat Pesanan -->
<div class="modal fade" id="modalRiwayat" tabindex="-1" aria-labelledby="modalRiwayatLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Riwayat Pesanan Pelanggan</h5>
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <div class="modal-body">
        <div class="table-responsive">
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>#</th>
                <th>Tanggal Pesanan</th>
                <th>Produk</th>
                <th>Jumlah</th>
                <th>Total Harga</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <!-- Contoh data dummy -->
              <tr>
                <td>1</td>
                <td>2025-10-14</td>
                <td>Mi Ayam Spesial</td>
                <td>2</td>
                <td>Rp 30.000</td>
                <td><span class="badge bg-success text-white">Selesai</span></td>
              </tr>
              <tr>
                <td>2</td>
                <td>2025-10-15</td>
                <td>Es Teh Manis</td>
                <td>3</td>
                <td>Rp 15.000</td>
                <td><span class="badge bg-warning text-dark">Diproses</span></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

<script src="../assets/js/vendor-all.min.js"></script>
<script src="../assets/plugins/bootstrap/js/bootstrap.min.js"></script>
<script src="../assets/js/pcoded.min.js"></script>
<script>
const BACKEND_API = '<?= BACKEND_URL ?>/api';

// Add user (admin create) -> create user, lalu alamat jika diisi
document.querySelector('#formAddUser')?.addEventListener('submit', async function(e){
  e.preventDefault();
  const f = new FormData(this);
  const payload = {
    username: f.get('username'), email: f.get('email'), password: f.get('password'),
    phone: f.get('phone'), role: f.get('role') || 'pelanggan'
  };

  try {
    const res = await fetch(`${BACKEND_API}/user_create.php`, {
      method: 'POST',
      headers: {'Content-Type':'application/json'},
      body: JSON.stringify(payload)
    });
    const j = await res.json();
    if (!j.success) return alert('Gagal membuat user: ' + (j.message||''));

    const userId = j.id;
    // jika alamat diisi -> kirim ke address_create.php
    const addr = f.get('address');
    if (addr && addr.trim() !== '') {
      const addrPayload = {
        user_id: userId,
        recipient_name: f.get('recipient_name') || payload.username,
        address: addr,
        city: f.get('city'),
        postcode: f.get('postcode'),
        phone: f.get('addr_phone') || payload.phone,
        is_default: f.get('is_default') ? 1 : 0
      };
      await fetch(`${BACKEND_API}/address_create.php`, {
        method: 'POST',
        headers: {'Content-Type':'application/json'},
        body: JSON.stringify(addrPayload)
      });
    }

    location.reload();
  } catch (err) {
    alert('Error: ' + err.message);
  }
});

// Edit user submit
document.querySelector('#formEditUser')?.addEventListener('submit', async function(e){
  e.preventDefault();
  const f = new FormData(this);
  const payload = {
    id: f.get('id'),
    username: f.get('username'),
    email: f.get('email'),
    phone: f.get('phone'),
    birthdate: f.get('birthdate'),
    gender: f.get('gender'),
    role: f.get('role'),
    password: f.get('password') || null
  };

  try {
    const res = await fetch(`${BACKEND_API}/user_update.php`, {
      method: 'POST',
      headers: {'Content-Type':'application/json'},
      body: JSON.stringify(payload)
    });
    const j = await res.json();
    if (!j.success) return alert('Gagal: ' + (j.message||''));
    location.reload();
  } catch (err) {
    alert('Error: ' + err.message);
  }
});

// Event listeners for buttons
document.addEventListener('DOMContentLoaded', function() {
  // Edit button
  document.querySelectorAll('.btn-edit-user').forEach(btn => {
    btn.addEventListener('click', function() {
      const id = this.getAttribute('data-id');
      // Fetch user data
      fetch(`${BACKEND_API}/user_get.php?id=${id}`)
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            const user = data.data;
            document.querySelector('#formEditUser input[name="id"]').value = user.id;
            document.querySelector('#formEditUser input[name="username"]').value = user.username;
            document.querySelector('#formEditUser input[name="email"]').value = user.email;
            document.querySelector('#formEditUser input[name="phone"]').value = user.phone || '';
            document.querySelector('#formEditUser input[name="birthdate"]').value = user.birthdate || '';
            document.querySelector('#formEditUser select[name="gender"]').value = user.gender || '';
            document.querySelector('#formEditUser select[name="role"]').value = user.role;
            $('#modalEditPelanggan').modal('show');
          } else {
            alert('Gagal mengambil data user');
          }
        })
        .catch(err => alert('Error: ' + err.message));
    });
  });

  // Delete button
  document.querySelectorAll('.btn-delete-user').forEach(btn => {
    btn.addEventListener('click', function() {
      const id = this.getAttribute('data-id');
      if (confirm('Yakin hapus user ini?')) {
        fetch(`${BACKEND_API}/user_delete.php`, {
          method: 'POST',
          headers: {'Content-Type':'application/json'},
          body: JSON.stringify({id: id})
        })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            location.reload();
          } else {
            alert('Gagal hapus user: ' + (data.message || ''));
          }
        })
        .catch(err => alert('Error: ' + err.message));
      }
    });
  });

  // Address button
  document.querySelectorAll('.btn-address').forEach(btn => {
    btn.addEventListener('click', function() {
      const id = this.getAttribute('data-id');
      // Fetch addresses
      fetch(`${BACKEND_API}/address_get.php?user_id=${id}`)
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            let content = '<ul class="list-group">';
            if (data.data.length > 0) {
              data.data.forEach(addr => {
                content += `<li class="list-group-item">
                  <strong>${addr.recipient_name}</strong><br>
                  ${addr.address}, ${addr.city} ${addr.postcode}<br>
                  Tel: ${addr.phone}
                  ${addr.is_default ? '<span class="badge badge-primary">Default</span>' : ''}
                </li>`;
              });
            } else {
              content += '<li class="list-group-item">Tidak ada alamat</li>';
            }
            content += '</ul>';
            document.querySelector('#modalRiwayat .modal-body').innerHTML = content;
            document.querySelector('#modalRiwayat .modal-title').textContent = 'Alamat User';
            $('#modalRiwayat').modal('show');
          } else {
            alert('Gagal mengambil alamat');
          }
        })
        .catch(err => alert('Error: ' + err.message));
    });
  });

  // Role select change
  document.querySelectorAll('.role-select').forEach(sel => {
    sel.addEventListener('change', function() {
      const id = this.getAttribute('data-id');
      const role = this.value;
      fetch(`${BACKEND_API}/user_update.php`, {
        method: 'POST',
        headers: {'Content-Type':'application/json'},
        body: JSON.stringify({id: id, role: role})
      })
      .then(res => res.json())
      .then(data => {
        if (!data.success) {
          alert('Gagal update role: ' + (data.message || ''));
          location.reload(); // revert
        }
      })
      .catch(err => alert('Error: ' + err.message));
    });
  });

  // Search functionality
  document.querySelector('#searchPelanggan')?.addEventListener('input', function() {
    const query = this.value.toLowerCase();
    document.querySelectorAll('#usersTbody tr').forEach(row => {
      const text = row.textContent.toLowerCase();
      row.style.display = text.includes(query) ? '' : 'none';
    });
  });
});
</script>
