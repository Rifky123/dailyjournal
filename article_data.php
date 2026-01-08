<?php
session_start();
include "koneksi.php";


$hlm = isset($_POST['hlm']) ? $_POST['hlm'] : 1;
$limit = 3;
$limit_start = ($hlm - 1) * $limit;
$no = $limit_start + 1;

$sql = "SELECT * FROM article ORDER BY tanggal DESC LIMIT $limit_start, $limit";
$hasil = $conn->query($sql);
?>

<table class="table table-hover align-middle">
  <thead class="table-dark">
    <tr>
      <th width="5%">No</th>
      <th width="20%">Judul</th>
      <th width="35%">Isi</th>
      <th width="20%">Gambar</th>
      <th width="20%">Aksi</th>
    </tr>
  </thead>
  <tbody>
    <?php while ($row = $hasil->fetch_assoc()) { ?>
    <tr>
      <td><?= $no++ ?></td>
      <td>
        <strong><?= $row['judul'] ?></strong><br>
        <small class="text-muted">
          pada : <?= $row['tanggal'] ?> | oleh : <?= $row['username'] ?>
        </small>
      </td>
      <td><?= $row['isi'] ?></td>
      <td>
        <?php if ($row['gambar'] != '') { ?>
          <img src="/latihan_jqueryajax/img/<?= $row['gambar'] ?>" width="100">
        <?php } else { ?>
          <small class="text-muted fst-italic">Tidak ada gambar</small>
        <?php } ?>
      </td>
      <td>
        <a href="#" class="badge rounded-pill text-bg-success"
           data-bs-toggle="modal" data-bs-target="#modalEdit<?= $row['id'] ?>">
           ✏️
        </a>
        <a href="#" class="badge rounded-pill text-bg-danger"
           data-bs-toggle="modal" data-bs-target="#modalHapus<?= $row['id'] ?>">
           ❌
        </a>
      </td>
    </tr>
    <?php } ?>
  </tbody>
</table>

<?php
// QUERY ULANG KHUSUS MODAL
$hasil_modal = $conn->query($sql);
while ($row = $hasil_modal->fetch_assoc()) {
?>

<!-- MODAL EDIT -->
<div class="modal fade" id="modalEdit<?= $row['id'] ?>" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="post" enctype="multipart/form-data">
        <div class="modal-header">
          <h5 class="modal-title">Edit Article</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id" value="<?= $row['id'] ?>">
          <div class="mb-3">
            <label>Judul</label>
            <input type="text" class="form-control" name="judul"
                   value="<?= $row['judul'] ?>" required>
          </div>
          <div class="mb-3">
            <label>Isi</label>
            <textarea class="form-control" name="isi" required><?= $row['isi'] ?></textarea>
          </div>
          <div class="mb-3">
            <label>Ganti Gambar</label>
            <input type="file" class="form-control" name="gambar">
            <input type="hidden" name="gambar_lama" value="<?= $row['gambar'] ?>">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- MODAL HAPUS -->
<div class="modal fade" id="modalHapus<?= $row['id'] ?>" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="post">
        <div class="modal-header">
          <h5 class="modal-title">Hapus Artikel</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          Yakin ingin menghapus <strong><?= $row['judul'] ?></strong>?
          <input type="hidden" name="id" value="<?= $row['id'] ?>">
          <input type="hidden" name="gambar" value="<?= $row['gambar'] ?>">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tidak</button>
          <button type="submit" class="btn btn-danger">Hapus</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php } ?>
