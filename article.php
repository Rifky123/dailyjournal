<div class="container">

    <button class="btn btn-secondary mb-3" data-bs-toggle="modal" data-bs-target="#modalTambah">
        <i class="bi bi-plus-lg"></i> Tambah Article
    </button>

    <div id="article_data"></div>

</div>


<!-- ================= MODAL TAMBAH ================= -->
<div class="modal fade" id="modalTambah" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5">Tambah Article</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="post" action="" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Judul</label>
                        <input type="text" class="form-control" name="judul" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Isi</label>
                        <textarea class="form-control" name="isi" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Gambar</label>
                        <input type="file" class="form-control" name="gambar">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <input type="submit" value="simpan" name="simpan" class="btn btn-primary">
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ================= AJAX SCRIPT (WAJIB ADA) ================= -->
<script>
$(document).ready(function(){

    console.log("Article page loaded");

    load_data(1);

    function load_data(hlm){
        console.log("Request ke article_data.php | halaman:", hlm);

        $.ajax({
            url : "article_data.php",
            method : "POST",
            data : { hlm: hlm },
            success : function(data){
                console.log("Status 200 OK - article_data.php berhasil");
                $('#article_data').html(data);
            },
            error: function(xhr){
                console.log("AJAX ERROR - Status:", xhr.status);
            }
        });
    }

    $(document).on('click', '.halaman', function(){
        var hlm = $(this).attr("id");
        load_data(hlm);
    });

});
</script>
<!-- ============================================================ -->

<?php
include "upload_foto.php";

/* ================= SIMPAN / UPDATE ================= */
if (isset($_POST['simpan'])) {
    $judul    = $_POST['judul'];
    $isi      = $_POST['isi'];
    $tanggal  = date("Y-m-d H:i:s");
    $username = $_SESSION['username'];
    $gambar   = '';
    $nama_gambar = $_FILES['gambar']['name'];

    if ($nama_gambar != '') {
        $cek_upload = upload_foto($_FILES["gambar"]);
        if ($cek_upload['status']) {
            $gambar = $cek_upload['message'];
        } else {
            echo "<script>alert('".$cek_upload['message']."');document.location='admin.php?page=article';</script>";
            die;
        }
    }

    if (isset($_POST['id'])) {
        $id = $_POST['id'];

        if ($nama_gambar == '') {
            $gambar = $_POST['gambar_lama'];
        } else {
            unlink("img/" . $_POST['gambar_lama']);
        }

        $stmt = $conn->prepare(
            "UPDATE article SET judul=?, isi=?, gambar=?, tanggal=?, username=? WHERE id=?"
        );
        $stmt->bind_param("sssssi", $judul, $isi, $gambar, $tanggal, $username, $id);
        $simpan = $stmt->execute();
    } else {
        $stmt = $conn->prepare(
            "INSERT INTO article (judul,isi,gambar,tanggal,username) VALUES (?,?,?,?,?)"
        );
        $stmt->bind_param("sssss", $judul, $isi, $gambar, $tanggal, $username);
        $simpan = $stmt->execute();
    }

    if ($simpan) {
        echo "<script>alert('Simpan data sukses');document.location='admin.php?page=article';</script>";
    } else {
        echo "<script>alert('Simpan data gagal');document.location='admin.php?page=article';</script>";
    }

    $stmt->close();
    $conn->close();
}

/* ================= HAPUS ================= */
if (isset($_POST['hapus'])) {
    $id     = $_POST['id'];
    $gambar = $_POST['gambar'];

    if ($gambar != '') {
        unlink("img/" . $gambar);
    }

    $stmt = $conn->prepare("DELETE FROM article WHERE id=?");
    $stmt->bind_param("i", $id);
    $hapus = $stmt->execute();

    if ($hapus) {
        echo "<script>alert('Hapus data sukses');document.location='admin.php?page=article';</script>";
    } else {
        echo "<script>alert('Hapus data gagal');document.location='admin.php?page=article';</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
