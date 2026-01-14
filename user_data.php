<?php
session_start();
include "koneksi.php";

// ====== Proteksi Login ======
if(!isset($_SESSION['username'])){
    header("Location: login.php");
    exit;
}

// ====== TAMBAH USER ======
if(isset($_POST['simpan'])){
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // hash password
    $foto = '';

    if(isset($_FILES['foto']) && $_FILES['foto']['name'] != ''){
        $foto = $_FILES['foto']['name'];
        $tmp = $_FILES['foto']['tmp_name'];
        move_uploaded_file($tmp, "img/".$foto);
    }

    mysqli_query($conn, "INSERT INTO user VALUES(NULL,'$username','$password','$foto')");
    exit('success');
}

// ====== UPDATE USER ======
if(isset($_POST['update'])){
    $id = $_POST['id'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $password_sql = $password != '' ? ", password='".password_hash($password,PASSWORD_DEFAULT)."'" : "";
    $foto_lama = $_POST['foto_lama'];

    if(isset($_FILES['foto']) && $_FILES['foto']['name'] != ''){
        $foto = $_FILES['foto']['name'];
        $tmp = $_FILES['foto']['tmp_name'];
        move_uploaded_file($tmp, "img/".$foto);
        if($foto_lama != '' && file_exists("img/".$foto_lama)) unlink("img/".$foto_lama);
    } else {
        $foto = $foto_lama;
    }

    mysqli_query($conn, "UPDATE user SET username='$username' $password_sql, foto='$foto' WHERE id='$id'");
    exit('success');
}

// ====== HAPUS USER ======
if(isset($_GET['hapus'])){
    $id = $_GET['hapus'];
    $data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM user WHERE id='$id'"));
    if($data['foto'] != '' && file_exists("img/".$data['foto'])) unlink("img/".$data['foto']);
    mysqli_query($conn, "DELETE FROM user WHERE id='$id'");
    exit('success');
}

// ====== PAGINATION ======
$batas = 5;
$halaman = isset($_GET['hal']) ? (int)$_GET['hal'] : 1;
$mulai = ($halaman-1)*$batas;

$total = mysqli_num_rows(mysqli_query($conn,"SELECT * FROM user"));
$total_halaman = ceil($total/$batas);

$data = mysqli_query($conn,"SELECT * FROM user ORDER BY id DESC LIMIT $mulai,$batas");
?>

<div class="container py-4">
<h3 class="mb-4">Manajemen User</h3>

<!-- FORM TAMBAH -->
<form id="formTambah" enctype="multipart/form-data" class="mb-4">
    <div class="row g-2">
        <div class="col-md-3">
            <input type="text" name="username" class="form-control" placeholder="Username" required>
        </div>
        <div class="col-md-3">
            <input type="password" name="password" class="form-control" placeholder="Password" required>
        </div>
        <div class="col-md-3">
            <input type="file" name="foto" class="form-control">
        </div>
        <div class="col-md-3">
            <button type="submit" class="btn btn-primary w-100">Tambah</button>
        </div>
    </div>
</form>

<!-- TABEL -->
<table class="table table-bordered table-hover align-middle">
    <thead class="table-dark text-center">
        <tr>
            <th>No</th>
            <th>Foto</th>
            <th>Username</th>
            <th>Password</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php $no=$mulai+1; while($u=mysqli_fetch_assoc($data)){ ?>
        <tr>
            <td><?= $no++ ?></td>
            <td>
                <?php if($u['foto'] != ''){ ?>
                    <img src="img/<?= $u['foto'] ?>" width="60">
                <?php } else { echo "<small class='text-muted'>Tidak ada foto</small>"; } ?>
            </td>
            <td><?= $u['username'] ?></td>
            <td>•••••••</td>
            <td class="text-center">
                <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#edit<?= $u['id'] ?>">Edit</button>
                <button class="btn btn-danger btn-sm hapus" data-id="<?= $u['id'] ?>">Hapus</button>
            </td>
        </tr>

        <!-- MODAL EDIT -->
        <div class="modal fade" id="edit<?= $u['id'] ?>">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form class="formEdit" enctype="multipart/form-data">
                        <div class="modal-header">
                            <h5>Edit User</h5>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="id" value="<?= $u['id'] ?>">
                            <input type="hidden" name="foto_lama" value="<?= $u['foto'] ?>">
                            <input type="text" name="username" class="form-control mb-2" value="<?= $u['username'] ?>" required>
                            <input type="password" name="password" class="form-control mb-2" placeholder="Kosongkan jika tidak ganti">
                            <input type="file" name="foto" class="form-control">
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Simpan</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <?php } ?>
    </tbody>
</table>

<!-- PAGINATION -->
<nav>
    <ul class="pagination justify-content-center">
        <?php for($i=1;$i<=$total_halaman;$i++){ ?>
            <li class="page-item <?= ($i==$halaman)?'active':'' ?>">
                <button class="btn btn-sm btn-secondary halaman" id="<?= $i ?>"><?= $i ?></button>
            </li>
        <?php } ?>
    </ul>
</nav>
</div>

<script>
$(document).ready(function(){
    // Tambah user
    $('#formTambah').submit(function(e){
        e.preventDefault();
        var formData = new FormData(this);
        $.ajax({
            url: 'user_data.php',
            type: 'POST',
            data: formData,
            contentType:false,
            processData:false,
            success:function(res){
                loadUserData(1);
                $('#formTambah')[0].reset();
            }
        });
    });

    // Edit user
    $('.formEdit').submit(function(e){
        e.preventDefault();
        var formData = new FormData(this);
        $.ajax({
            url: 'user_data.php',
            type: 'POST',
            data: formData,
            contentType:false,
            processData:false,
            success:function(res){
                loadUserData(1);
                $('.modal').modal('hide');
            }
        });
    });

    // Hapus user
    $('.hapus').click(function(){
        var id = $(this).data('id');
        if(confirm('Hapus user?')){
            $.get('user_data.php', {hapus:id}, function(){
                loadUserData(1);
            });
        }
    });

    // Pagination
    $(document).on('click', '.halaman', function(){
        var hlm = $(this).attr('id');
        loadUserData(hlm);
    });

    // Fungsi load data user via AJAX
    function loadUserData(hlm){
        $('#konten').load('user_data.php?hal='+hlm);
    }
});
</script>
