<?php
include "../config/config.php";
include "../includes/head.php";
include "../includes/navigation.php";
include "../includes/topbar.php";
?>

<div class="pcoded-main-container">
    <div class="pcoded-wrapper">
        <div class="pcoded-content">
            <div class="pcoded-inner-content">
                <div class="main-body">
                    <div class="page-wrapper">

                        <!-- ================= HEADER ================= -->
                        <div class="page-header mb-4">
                            <h5>CRM – Kampanye Penawaran</h5>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="<?= BASE_URL ?>/index.php"><i class="fas fa-home"></i></a>
                                </li>
                                <li class="breadcrumb-item">CRM</li>
                            </ul>
                        </div>

                        <!-- ================= TABLE ================= -->
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Daftar Kampanye</h5>
                                <button class="btn btn-primary btn-sm" onclick="openCreate()">
                                    <i class="fas fa-plus"></i> Kampanye
                                </button>
                            </div>

                            <div class="card-body table-responsive">
                                <table class="table table-bordered table-sm" id="tabelCRM">
                                    <thead class="thead-light">
                                        <tr>
                                            <th width="50">ID</th>
                                            <th>Judul</th>
                                            <th width="100">Jenis</th>
                                            <th width="90">Status</th>
                                            <th width="150">Dibuat</th>
                                            <th width="220">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>

                        <!-- ================= MODAL FORM ================= -->
                        <div class="modal fade" id="modalForm" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <form id="formCRM" class="modal-content">

                                    <div class="modal-header">
                                        <h5 class="modal-title" id="formTitle">Buat Kampanye</h5>
                                        <button type="button" class="close" data-dismiss="modal">
                                            <span>&times;</span>
                                        </button>
                                    </div>

                                    <div class="modal-body">
                                        <input type="hidden" id="campaign_id">

                                        <div class="form-group">
                                            <label>Judul</label>
                                            <input type="text" id="judul" class="form-control" required>
                                        </div>

                                        <div class="form-group">
                                            <label>Isi Pesan</label>
                                            <textarea id="isi" rows="4" class="form-control" required></textarea>
                                        </div>

                                        <div class="form-group">
                                            <label>Jenis</label>
                                            <select id="jenis" class="form-control">
                                                <option value="email">Email</option>
                                                <option value="whatsapp">WhatsApp</option>
                                                <option value="sms">SMS</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="modal-footer">
                                        <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">
                                            Batal
                                        </button>
                                        <button class="btn btn-success btn-sm" type="submit">
                                            Simpan
                                        </button>
                                    </div>

                                </form>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    /* ================= CONFIG ================= */
    const API_BASE = "../api/crm";
    let editId = null;

    /* ================= LOAD ================= */
    function loadCRM() {
        fetch(API_BASE + "/campaign_list.php")
            .then(r => r.json())
            .then(res => {
                if (!res.success) {
                    console.error(res.message);
                    return;
                }

                let html = "";
                res.data.forEach(c => {
                    html += `
                <tr>
                    <td>${c.id}</td>
                    <td>${c.judul}</td>
                    <td>${c.jenis}</td>
                    <td>
                        <span class="badge badge-secondary">
                            ${c.status}
                        </span>
                    </td>
                    <td>${c.created_at}</td>
                    <td>
                        <button class="btn btn-warning btn-sm"
                            onclick="editCRM(${c.id})">
                            Edit
                        </button>
                        <button class="btn btn-danger btn-sm"
                            onclick="deleteCRM(${c.id})">
                            Hapus
                        </button>
                    </td>
                </tr>`;
                });

                $("#tabelCRM tbody").html(html);
            })
            .catch(err => console.error(err));
    }
    loadCRM();

    /* ================= CREATE ================= */
    function openCreate() {
        editId = null;
        $("#formTitle").text("Buat Kampanye");
        $("#campaign_id").val("");
        $("#judul").val("");
        $("#isi").val("");
        $("#jenis").val("email");
        $("#modalForm").modal("show");
    }

    /* ================= EDIT ================= */
    function editCRM(id) {
        fetch(API_BASE + "/campaign_detail.php?id=" + id)
            .then(r => r.json())
            .then(res => {
                if (!res.success) return alert(res.message);

                editId = id;
                $("#formTitle").text("Edit Kampanye");
                $("#campaign_id").val(id);
                $("#judul").val(res.data.judul);
                $("#isi").val(res.data.isi);
                $("#jenis").val(res.data.jenis);

                $("#modalForm").modal("show");
            })
            .catch(err => console.error(err));
    }

    /* ================= SAVE ================= */
    $("#formCRM").on("submit", function(e) {
        e.preventDefault();

        let fd = new FormData();
        fd.append("judul", $("#judul").val());
        fd.append("isi", $("#isi").val());
        fd.append("jenis", $("#jenis").val());

        let url = API_BASE + "/campaign_create.php";
        if (editId) {
            fd.append("id", editId);
            url = API_BASE + "/campaign_update.php";
        }

        fetch(url, {
                method: "POST",
                body: fd
            })
            .then(r => r.json())
            .then(res => {
                if (!res.success) return alert(res.message);
                $("#modalForm").modal("hide");
                loadCRM();
            })
            .catch(err => console.error(err));
    });

    /* ================= DELETE ================= */
    function deleteCRM(id) {
        if (!confirm("Hapus kampanye ini?")) return;

        let fd = new FormData();
        fd.append("id", id);

        fetch(API_BASE + "/campaign_delete.php", {
                method: "POST",
                body: fd
            })
            .then(r => r.json())
            .then(res => {
                if (!res.success) return alert(res.message);
                loadCRM();
            })
            .catch(err => console.error(err));
    }
</script>