<?php
session_start();
include "koneksi.php";

// Proteksi login
if(!isset($_SESSION['username'])){
    echo "<div class='alert alert-danger'>Silakan login terlebih dahulu.</div>";
    exit;
}

// CRUD: Tambah Data
if(isset($_POST['simpan'])){
    $judul = $_POST['judul'];
    $keterangan = $_POST['keterangan'];

    $gambar = '';
    if(isset($_FILES['gambar']) && $_FILES['gambar']['name'] != ''){
        $gambar = $_FILES['gambar']['name'];
        $tmp = $_FILES['gambar']['tmp_name'];
        move_uploaded_file($tmp, "img/".$gambar);
    }

    $sql = "INSERT INTO gallery(judul, keterangan, gambar) VALUES(
        '$judul','$keterangan','$gambar'
    )";
    $conn->query($sql);
}

// CRUD: Hapus Data
if(isset($_POST['hapus'])){
    $id = $_POST['id'];
    $gambar = $_POST['gambar'];
    if($gambar != '' && file_exists("img/".$gambar)){
        unlink("img/".$gambar);
    }
    $conn->query("DELETE FROM gallery WHERE id='$id'");
}

// Pagination
$limit = 4;
$halaman = isset($_POST['hlm']) ? (int)$_POST['hlm'] : 1;
$offset = ($halaman - 1) * $limit;

// Ambil data
$hasil = $conn->query("SELECT * FROM gallery ORDER BY id DESC LIMIT $offset,$limit");
$total_data = $conn->query("SELECT * FROM gallery")->num_rows;
$total_hlm = ceil($total_data / $limit);
?>

<!-- FORM TAMBAH -->
<form id="formTambah" method="post" enctype="multipart/form-data" class="mb-4">
  <div class="row g-2">
    <div class="col-md-3">
      <input type="text" name="judul" class="form-control" placeholder="Judul" required>
    </div>
    <div class="col-md-4">
      <input type="text" name="keterangan" class="form-control" placeholder="Keterangan" required>
    </div>
    <div class="col-md-3">
      <input type="file" name="gambar" class="form-control" required>
    </div>
    <div class="col-md-2">
      <button type="submit" name="simpan" class="btn btn-primary w-100">Tambah</button>
    </div>
  </div>
</form>

<!-- TABEL DATA -->
<table class="table table-bordered table-hover">
  <thead class="table-dark text-center">
    <tr>
      <th>No</th>
      <th>Gambar</th>
      <th>Judul</th>
      <th>Keterangan</th>
      <th>Aksi</th>
    </tr>
  </thead>
  <tbody>
  <?php $no=$offset+1; while($row=$hasil->fetch_assoc()){ ?>
    <tr>
      <td class="text-center"><?= $no++ ?></td>
      <td class="text-center">
        <?php if($row['gambar'] != ''){ ?>
            <img src="img/<?= $row['gambar'] ?>" width="80">
        <?php } else { echo "<small class='text-muted fst-italic'>Tidak ada gambar</small>"; } ?>
      </td>
      <td><?= $row['judul'] ?></td>
      <td><?= $row['keterangan'] ?></td>
      <td class="text-center">
        <button class="btn btn-sm btn-success editBtn" 
                data-id="<?= $row['id'] ?>" 
                data-judul="<?= $row['judul'] ?>" 
                data-keterangan="<?= $row['keterangan'] ?>" 
                data-gambar="<?= $row['gambar'] ?>">✏️</button>
        <button class="btn btn-sm btn-danger hapusBtn" 
                data-id="<?= $row['id'] ?>" 
                data-gambar="<?= $row['gambar'] ?>">❌</button>
      </td>
    </tr>
  <?php } ?>
  </tbody>
</table>

<!-- PAGINATION -->
<nav>
  <ul class="pagination justify-content-center">
    <?php for($i=1;$i<=$total_hlm;$i++){ ?>
      <li class="page-item <?= ($i==$halaman)?'active':'' ?>">
        <button class="btn btn-sm btn-secondary halaman" data-hlm="<?= $i ?>"><?= $i ?></button>
      </li>
    <?php } ?>
  </ul>
</nav>

<script>
$(document).ready(function(){

    // Tambah data via AJAX
    $('#formTambah').submit(function(e){
        e.preventDefault();
        var formData = new FormData(this);
        formData.append('simpan', true);

        $.ajax({
            url:'gallery_data.php',
            type:'POST',
            data:formData,
            contentType:false,
            processData:false,
            success:function(res){
                $('#konten').load('gallery.php');
            }
        });
    });

    // Pagination
    $(document).on('click','.halaman', function(){
        var hlm = $(this).data('hlm');
        $.post('gallery_data.php', {hlm: hlm}, function(res){
            $('#konten').html(res);
        });
    });

    // Edit modal sederhana
    $(document).on('click','.editBtn',function(){
        var id=$(this).data('id');
        var judul=$(this).data('judul');
        var ket=$(this).data('keterangan');
        var gambar=$(this).data('gambar');

        var html = `<form id="formEdit" method="post" enctype="multipart/form-data">
            <input type="hidden" name="id" value="${id}">
            <input type="hidden" name="gambar_lama" value="${gambar}">
            <div class="mb-2"><input type="text" name="judul" class="form-control" value="${judul}" required></div>
            <div class="mb-2"><input type="text" name="keterangan" class="form-control" value="${ket}" required></div>
            <div class="mb-2"><input type="file" name="gambar" class="form-control"></div>
            <button type="submit" name="simpan" class="btn btn-primary">Simpan</button>
            <button type="button" class="btn btn-secondary" id="batalEdit">Batal</button>
        </form>`;
        $('#konten').html(html);
    });

    $(document).on('click','#batalEdit',function(){
        $('#konten').load('gallery_data.php');
    });

    // Hapus data via AJAX
    $(document).on('click','.hapusBtn', function(){
        if(confirm('Hapus data ini?')){
            var id = $(this).data('id');
            var gambar = $(this).data('gambar');
            $.post('gallery_data.php',{hapus:true, id:id, gambar:gambar},function(res){
                $('#konten').load('gallery_data.php');
            });
        }
    });

});
</script>
