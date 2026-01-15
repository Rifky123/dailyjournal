<?php
session_start();
include "koneksi.php";

// ====== Proteksi Login ======
if(!isset($_SESSION['username'])){
    header("Location: login.php");
    exit;
}

// ====== ACTION TAMBAH ======
if(isset($_POST['aksi']) && $_POST['aksi']=='tambah'){
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $foto = '';
    if(isset($_FILES['foto']) && $_FILES['foto']['name']!=''){
        $foto = $_FILES['foto']['name'];
        move_uploaded_file($_FILES['foto']['tmp_name'], "img/".$foto);
    }
    mysqli_query($conn,"INSERT INTO user VALUES(NULL,'$username','$password','$foto')");
    exit('success');
}

// ====== ACTION EDIT ======
if(isset($_POST['aksi']) && $_POST['aksi']=='edit'){
    $id = $_POST['id'];
    $username = $_POST['username'];
    $foto_lama = $_POST['foto_lama'];
    $password_sql = '';
    if($_POST['password'] != ''){
        $password_sql = ", password='".password_hash($_POST['password'],PASSWORD_DEFAULT)."'";
    }

    $foto = $foto_lama;
    if(isset($_FILES['foto']) && $_FILES['foto']['name']!=''){
        $foto = $_FILES['foto']['name'];
        move_uploaded_file($_FILES['foto']['tmp_name'], "img/".$foto);
        if($foto_lama != '' && file_exists("img/".$foto_lama)) unlink("img/".$foto_lama);
    }

    mysqli_query($conn,"UPDATE user SET username='$username' $password_sql, foto='$foto' WHERE id='$id'");
    exit('success');
}

// ====== ACTION HAPUS ======
if(isset($_GET['aksi']) && $_GET['aksi']=='hapus'){
    $id = $_GET['id'];
    $data = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM user WHERE id='$id'"));
    if($data['foto']!='' && file_exists("img/".$data['foto'])) unlink("img/".$data['foto']);
    mysqli_query($conn,"DELETE FROM user WHERE id='$id'");
    exit('success');
}

// ====== LOAD TABEL ======
$batas = 4;
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

<!-- TABEL USER -->
<div id="userTable">
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
        <?php if($u['foto']!=''){ ?>
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
<div class="modal fade" id="edit<?= $u['id'] ?>" tabindex="-1">
<div class="modal-dialog">
<div class="modal-content">
<form class="formEdit" enctype="multipart/form-data">
    <div class="modal-header">
        <h5>Edit User</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
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
        <button class="btn btn-sm btn-secondary halaman" data-hal="<?= $i ?>"><?= $i ?></button>
    </li>
<?php } ?>
</ul>
</nav>
</div>

<script>
$(document).ready(function(){
    // ====== Tambah User ======
    $('#formTambah').submit(function(e){
        e.preventDefault();
        var formData = new FormData(this);
        formData.append('aksi','tambah');
        $.ajax({
            url:'user_data.php',
            type:'POST',
            data:formData,
            contentType:false,
            processData:false,
            success:function(){
                $('#formTambah')[0].reset();
                loadUserTable(1);
            }
        });
    });

    // ====== Edit User ======
    $(document).on('submit','.formEdit',function(e){
        e.preventDefault();
        var formData = new FormData(this);
        formData.append('aksi','edit');
        $.ajax({
            url:'user_data.php',
            type:'POST',
            data:formData,
            contentType:false,
            processData:false,
            success:function(){
                $('.modal').modal('hide');
                loadUserTable(1);
            }
        });
    });

    // ====== Hapus User ======
    $(document).on('click','.hapus',function(){
        var id = $(this).data('id');
        if(confirm('Hapus user?')){
            $.get('user_data.php',{aksi:'hapus',id:id},function(){
                loadUserTable(1);
            });
        }
    });

    // ====== Pagination ======
    $(document).on('click','.halaman',function(){
        var hlm = $(this).data('hal');
        loadUserTable(hlm);
    });

    // ====== Fungsi reload tabel ======
    function loadUserTable(hlm){
        $('#userTable').load('user_data.php?hal='+hlm);
    }
});
</script>
