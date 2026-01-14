<?php
include "koneksi.php";
include "gallery_data.php";

/* ================= PAGINATION ================= */
$limit = 4;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Gallery</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body {
      background-color: #f8f9fa;
    }
    .card img {
      height: 200px;
      object-fit: cover;
    }
  </style>
</head>
<body>

<div class="container py-5">
  <h2 class="text-center text-primary mb-4">Gallery</h2>

  <div class="row row-cols-1 row-cols-md-4 g-4">

    <?php
    $query = mysqli_query($conn, "SELECT * FROM gallery LIMIT $start, $limit");
    if (mysqli_num_rows($query) > 0) {
      while ($g = mysqli_fetch_assoc($query)) {
    ?>
        <div class="col">
          <div class="card h-100 shadow-sm">
            <img src="img/<?= $g['gambar'] ?>" class="card-img-top" alt="<?= $g['judul'] ?>">
            <div class="card-body">
              <h6 class="card-title fw-bold"><?= $g['judul'] ?></h6>
              <p class="card-text"><?= $g['keterangan'] ?></p>
            </div>
          </div>
        </div>
    <?php
      }
    } else {
      echo "<p class='text-center'>Belum ada gallery.</p>";
    }
    ?>

  </div>

  <!-- PAGINATION -->
  <nav class="mt-4">
    <ul class="pagination justify-content-center">
      <?php
      $total = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM gallery"));
      $pages = ceil($total / $limit);

      for ($i = 1; $i <= $pages; $i++) {
      ?>
        <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
          <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
        </li>
      <?php } ?>
    </ul>
  </nav>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
