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

            <!-- PAGE HEADER -->
            <div class="page-header mb-4">
              <div class="page-block">
                <h5 class="m-0">Kelola Pengiriman & Ongkir</h5>
              </div>
            </div>

            <section class="content">
              <div class="container-fluid">

                <!-- ================= DAFTAR PENGIRIMAN ================= -->
                <div class="card mb-4">
                  <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Daftar Pengiriman</h6>
                  </div>
                  <div class="card-body table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                      <thead class="table-success">
                        <tr>
                          <th width="5%">No</th>
                          <th>Order</th>
                          <th>Kota</th>
                          <th>Kurir</th>
                          <th>Resi</th>
                          <th>Status</th>
                          <th width="10%">Aksi</th>
                        </tr>
                      </thead>
                      <tbody id="pengirimanBody">
                        <tr>
                          <td colspan="7" class="text-center">Memuat data...</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>

                <!-- ================= ONGKIR / CITIES ================= -->
                <div class="card">
                  <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Kelola Ongkir (Cities)</h6>
                    <button type="button" class="btn btn-success btn-sm" id="btnTambahKota">
                      Tambah Kota
                    </button>
                  </div>
                  <div class="card-body table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                      <thead class="table-success">
                        <tr>
                          <th width="5%">No</th>
                          <th>Kota</th>
                          <th>Ongkir (Rp)</th>
                          <th width="15%">Aksi</th>
                        </tr>
                      </thead>
                      <tbody id="cityBody">
                        <tr>
                          <td colspan="4" class="text-center">Memuat data...</td>
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

<!-- ================= MODAL UPDATE PENGIRIMAN ================= -->
<div class="modal fade" id="modalShipping" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form id="formShipping" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Update Pengiriman</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <input type="hidden" id="ship_id">

        <div class="form-group">
          <label>Kurir</label>
          <input type="text" id="ship_kurir" class="form-control" required>
        </div>

        <div class="form-group">
          <label>Nomor Resi</label>
          <input type="text" id="ship_resi" class="form-control">
        </div>

        <div class="form-group">
          <label>Status</label>
          <select id="ship_status" class="form-control">
            <option value="Diproses">Diproses</option>
            <option value="Dikirim">Dikirim</option>
            <option value="Terkirim">Terkirim</option>
          </select>
        </div>
      </div>

      <div class="modal-footer">
        <button class="btn btn-success" type="submit">Simpan</button>
      </div>
    </form>
  </div>
</div>

<!-- ================= MODAL ONGKIR ================= -->
<div class="modal fade" id="modalCity" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form id="formCity" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Ongkir Kota</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <input type="hidden" id="city_id">

        <div class="form-group">
          <label>Nama Kota</label>
          <input type="text" id="city_name" class="form-control" required>
        </div>

        <div class="form-group">
          <label>Ongkir</label>
          <input type="number" id="city_ongkir" class="form-control" required min="0">
        </div>
      </div>

      <div class="modal-footer">
        <button class="btn btn-success" type="submit">Simpan</button>
      </div>
    </form>
  </div>
</div>

<!-- ================= VENDOR SCRIPT (SAMA DENGAN PAGE PEMBAYARAN) ================= -->
<script src="../assets/js/vendor-all.min.js"></script>
<script src="../assets/plugins/bootstrap/js/bootstrap.min.js"></script>
<script src="../assets/js/pcoded.min.js"></script>

<!-- ================= CUSTOM SCRIPT ================= -->
<script>
  document.addEventListener("DOMContentLoaded", () => {
    const API = "<?= BASE_URL ?>/api";

    // Elements
    const pengirimanBody = document.getElementById("pengirimanBody");
    const cityBody = document.getElementById("cityBody");

    const formShipping = document.getElementById("formShipping");
    const shipId = document.getElementById("ship_id");
    const shipKurir = document.getElementById("ship_kurir");
    const shipResi = document.getElementById("ship_resi");
    const shipStatus = document.getElementById("ship_status");

    const formCity = document.getElementById("formCity");
    const cityId = document.getElementById("city_id");
    const cityName = document.getElementById("city_name");
    const cityOngkir = document.getElementById("city_ongkir");
    const btnTambahKota = document.getElementById("btnTambahKota");

    const esc = (s) => String(s ?? "")
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#039;");

    async function safeJson(res) {
      const text = await res.text();
      try {
        return JSON.parse(text);
      } catch {
        return {
          success: false,
          message: text
        };
      }
    }

    /* ===================== SHIPPING ===================== */
    async function loadShipping() {
      pengirimanBody.innerHTML = `<tr><td colspan="7" class="text-center">Memuat data...</td></tr>`;

      try {
        const res = await fetch(API + "/shipping_list.php", {
          credentials: "same-origin"
        });
        const j = await safeJson(res);

        if (!j.success) {
          pengirimanBody.innerHTML = `<tr><td colspan="7" class="text-center text-danger">Gagal memuat data pengiriman</td></tr>`;
          return;
        }

        if (!j.data || !j.data.length) {
          pengirimanBody.innerHTML = `<tr><td colspan="7" class="text-center">Tidak ada data</td></tr>`;
          return;
        }

        pengirimanBody.innerHTML = "";
        j.data.forEach((r, i) => {
          const kurir = r.kurir ? esc(r.kurir) : "-";
          const resi = r.no_resi ? esc(r.no_resi) : "-";
          const status = esc(r.status_pengiriman || "-");

          pengirimanBody.insertAdjacentHTML("beforeend", `
          <tr>
            <td>${i + 1}</td>
            <td>${esc(r.order_code)}</td>
            <td>${esc(r.nama_kota)}</td>
            <td>${kurir}</td>
            <td>${resi}</td>
            <td>${status}</td>
            <td>
              <button type="button"
                class="btn btn-sm btn-primary btnEditShipping"
                data-order-id="${esc(r.order_id)}"
                data-kurir="${esc(r.kurir || "")}"
                data-resi="${esc(r.no_resi || "")}"
                data-status="${esc(r.status_pengiriman || "Diproses")}">
                Edit
              </button>
            </td>
          </tr>
        `);
        });

        // bind edit buttons
        document.querySelectorAll(".btnEditShipping").forEach(btn => {
          btn.addEventListener("click", () => {
            shipId.value = btn.dataset.orderId;
            shipKurir.value = btn.dataset.kurir || "";
            shipResi.value = btn.dataset.resi || "";
            shipStatus.value = btn.dataset.status || "Diproses";
            $("#modalShipping").modal("show");
          });
        });

      } catch (e) {
        pengirimanBody.innerHTML = `<tr><td colspan="7" class="text-center text-danger">Error: ${esc(e.message)}</td></tr>`;
      }
    }

    formShipping.addEventListener("submit", async (e) => {
      e.preventDefault();

      try {
        const payload = {
          order_id: shipId.value,
          kurir: shipKurir.value,
          no_resi: shipResi.value,
          status_pengiriman: shipStatus.value
        };

        const res = await fetch(API + "/shipping_update.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/json"
          },
          body: JSON.stringify(payload),
          credentials: "same-origin"
        });

        const j = await safeJson(res);
        if (!j.success) {
          alert(j.message || "Gagal update pengiriman");
          return;
        }

        $("#modalShipping").modal("hide");
        loadShipping();
      } catch (e) {
        alert("Error: " + e.message);
      }
    });

    /* ===================== CITIES ===================== */
    async function loadCities() {
      cityBody.innerHTML = `<tr><td colspan="4" class="text-center">Memuat data...</td></tr>`;

      try {
        const res = await fetch(API + "/cities_list.php", {
          credentials: "same-origin"
        });
        const j = await safeJson(res);

        if (!j.success) {
          cityBody.innerHTML = `<tr><td colspan="4" class="text-center text-danger">Gagal memuat data kota</td></tr>`;
          return;
        }

        if (!j.data || !j.data.length) {
          cityBody.innerHTML = `<tr><td colspan="4" class="text-center">Belum ada kota</td></tr>`;
          return;
        }

        cityBody.innerHTML = "";
        j.data.forEach((r, i) => {
          cityBody.insertAdjacentHTML("beforeend", `
          <tr>
            <td>${i + 1}</td>
            <td>${esc(r.nama_kota)}</td>
            <td>${Number(r.ongkir || 0).toLocaleString("id-ID")}</td>
            <td>
              <button type="button"
                class="btn btn-sm btn-primary btnEditCity"
                data-order-id="${esc(r.order_id)}"
                data-nama="${esc(r.nama_kota)}"
                data-ongkir="${esc(r.ongkir)}">
                Edit
              </button>
              <button type="button"
                class="btn btn-sm btn-danger btnDeleteCity"
                data-order-id="${esc(r.order_id)}">
                Hapus
              </button>
            </td>
          </tr>
        `);
        });

        document.querySelectorAll(".btnEditCity").forEach(btn => {
          btn.addEventListener("click", () => {
            cityId.value = btn.dataset.id;
            cityName.value = btn.dataset.nama;
            cityOngkir.value = btn.dataset.ongkir;
            $("#modalCity").modal("show");
          });
        });

        document.querySelectorAll(".btnDeleteCity").forEach(btn => {
          btn.addEventListener("click", async () => {
            const id = btn.dataset.id;
            if (!confirm("Hapus kota ini?")) return;

            const res = await fetch(API + "/cities_delete.php", {
              method: "POST",
              headers: {
                "Content-Type": "application/json"
              },
              body: JSON.stringify({
                id
              }),
              credentials: "same-origin"
            });

            const j = await safeJson(res);
            if (!j.success) {
              alert(j.message || "Gagal hapus kota");
              return;
            }
            loadCities();
          });
        });

      } catch (e) {
        cityBody.innerHTML = `<tr><td colspan="4" class="text-center text-danger">Error: ${esc(e.message)}</td></tr>`;
      }
    }

    btnTambahKota.addEventListener("click", () => {
      cityId.value = "";
      cityName.value = "";
      cityOngkir.value = "";
      $("#modalCity").modal("show");
    });

    formCity.addEventListener("submit", async (e) => {
      e.preventDefault();

      try {
        const payload = {
          id: cityId.value,
          nama_kota: cityName.value,
          ongkir: cityOngkir.value
        };

        const res = await fetch(API + "/cities_save.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/json"
          },
          body: JSON.stringify(payload),
          credentials: "same-origin"
        });

        const j = await safeJson(res);
        if (!j.success) {
          alert(j.message || "Gagal simpan kota");
          return;
        }

        $("#modalCity").modal("hide");
        loadCities();
      } catch (e) {
        alert("Error: " + e.message);
      }
    });

    // init
    loadShipping();
    loadCities();
  });
</script>