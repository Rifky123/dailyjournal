<?php include "koneksi.php"; ?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Daily Journal - Rifky Maulana</title>

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

  <style>
    html { scroll-behavior: smooth; scroll-padding-top: 90px; }

    body {
      background-color: #f8f9fa;
      color: #212529;
      transition: all 0.4s ease;
    }

    .navbar { background-color: #ffffff; transition: background-color 0.4s ease, box-shadow 0.3s ease; }
    .navbar.shadow-sm { box-shadow: 0 2px 10px rgba(0,0,0,0.1); }

    /* DARK MODE */
    body.dark-mode { background-color: #1e1e1e; color: #f1f1f1; }
    body.dark-mode .navbar { background-color: #2c2c2c !important; }
    body.dark-mode .card { background-color: #2c2c2c; color: #f1f1f1; }
    body.dark-mode .card-header { background: linear-gradient(135deg, #3a3a3a, #4a4a4a); color: #ffffff !important; }

    /* Section Profile */
    #profile { background-color: #f8f9fa; transition: background-color 0.4s ease; }
    body.dark-mode #profile { background-color: #1e1e1e !important; }

    /* Buttons */
    .toggle-btn { border: none; padding: 8px 10px; border-radius: 6px; cursor: pointer; transition: all 0.3s ease; }
    .toggle-btn:hover { opacity: 0.85; }

    /* Greeting */
    #greeting { display:block; margin-top:20px; margin-bottom:20px; font-weight:600; font-size:1.1rem; color: #0d6efd;
      animation: fadeIn 1s ease-in-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(-10px);} to { opacity:1; transform: translateY(0);} }
    body.dark-mode #greeting { color: #93caff; }

    /* Hero */
    .hero { min-height:100vh; display:flex; flex-direction:column; justify-content:flex-start; padding-top:100px; }

    /* Card */
    .card { transition: transform 0.3s ease, box-shadow 0.3s ease; border:none; border-radius:15px; opacity:0; animation:fadeInUp 0.8s ease forwards; }
    @keyframes fadeInUp { from { opacity:0; transform: translateY(30px);} to { opacity:1; transform: translateY(0);} }
    .card:hover { transform: scale(1.03); box-shadow: 0 8px 20px rgba(0,0,0,0.1); }
    .card-header { background-color:#e3f2fd; font-weight:600; color:#0d47a1; }

    /* Profile image & layout */
    .profile-img {
      width:300px; height:300px; border-radius:50%; object-fit:cover;
      border:5px solid #e3f2fd; box-shadow:0 4px 10px rgba(0,0,0,0.1);
      transition: transform 0.3s ease; display:block; margin-left:auto; margin-right:auto;
    }
    body.dark-mode .profile-img { border:5px solid #93caff; }
    .profile-img:hover { transform: scale(1.05); }
    .profile-name { text-align:left; }

    /* Table - light mode */
    #profile .table { background-color: #ffffff; color: #212529; border-color: #dee2e6; transition: all 0.3s ease; }
    #profile td, #profile th { padding: 8px 16px; vertical-align: middle; }
    #profile .table tbody tr:hover { background-color: rgba(13,110,253,0.05); }

    /* DARK MODE TABLE FIX */
    body.dark-mode #profile .table,
    body.dark-mode #profile .table * {
      background-color: transparent !important;
      color: inherit !important;
      border-color: inherit !important;
      box-shadow: none !important;
    }

    body.dark-mode #profile .table,
    body.dark-mode #profile .table tbody,
    body.dark-mode #profile .table thead,
    body.dark-mode #profile .table tr,
    body.dark-mode #profile .table th,
    body.dark-mode #profile .table td {
      background-color: #2c2c2c !important;
      color: #f8f9fa !important;
      border-color: #444 !important;
    }

    body.dark-mode #profile .table tbody tr:hover {
      background-color: rgba(147, 202, 255, 0.08) !important;
    }

    footer { border-top: 1px solid #ddd; margin-top: 50px; padding-top: 12px; }
  </style>
</head>
<body>

  <!-- NAVBAR -->
  <nav class="navbar navbar-expand-lg fixed-top">
    <div class="container">
      <a class="navbar-brand fw-bold text-primary" href="#">Daily Journal</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarMenu">
        <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
          <li class="nav-item"><a class="nav-link fw-semibold" href="#jadwal">Schedule</a></li>
          <li class="nav-item"><a class="nav-link fw-semibold" href="#article">Article</a></li>
          <li class="nav-item"><a class="nav-link fw-semibold" href="#profile">Profile</a></li>
          <li class="nav-item"><a class="nav-link" href="login.php" target="_blank">Login</a></li>
        </ul>

        <div class="d-flex ms-lg-3">
          <button id="darkBtn" class="toggle-btn bg-dark text-white me-2"><i class="bi bi-moon-fill"></i></button>
          <button id="lightBtn" class="toggle-btn bg-warning text-dark"><i class="bi bi-brightness-high-fill"></i></button>
        </div>
      </div>
    </div>
  </nav>

  <!-- DARK MODE + GREETING SCRIPT -->
  <script>
    if (localStorage.getItem("darkMode") === "true") document.body.classList.add("dark-mode");

    const hour = new Date().getHours();
    if (!localStorage.getItem("darkMode")) {
      if (hour >= 18 || hour < 6) document.body.classList.add("dark-mode");
    }

    document.getElementById("darkBtn").onclick = () => {
      document.body.classList.add("dark-mode");
      localStorage.setItem("darkMode", "true");
    };

    document.getElementById("lightBtn").onclick = () => {
      document.body.classList.remove("dark-mode");
      localStorage.setItem("darkMode", "false");
    };

    window.onload = function() {
      const g = document.getElementById("greeting");
      if (hour < 12) g.textContent = "Selamat pagi, Rifky!";
      else if (hour < 18) g.textContent = "Selamat siang, Rifky!";
      else g.textContent = "Selamat malam, Rifky!";
    };

    window.addEventListener('scroll', () =>
      window.scrollY > 50
        ? document.querySelector('.navbar').classList.add('shadow-sm')
        : document.querySelector('.navbar').classList.remove('shadow-sm')
    );
  </script>

  <!-- SECTION: JADWAL -->
  <section id="jadwal" class="hero py-5">
    <div class="container text-center mb-5">
      <h5 id="greeting"></h5>
      <h1 class="text-primary mb-3">Jadwal Kuliah & Kegiatan Mahasiswa</h1>
      <small id="tanggal"></small> | <small id="jam"></small>
    </div>

    <div class="container">
      <div class="row row-cols-1 row-cols-md-4 g-4 text-center justify-content-center">

        <div class="col"><div class="card"><div class="card-header">Senin</div><div class="card-body">
          <h5>09:00 - 10:30</h5><p>Basis Data<br>Ruang H.3.4</p>
          <h5>13:00 - 15:00</h5><p>Dasar Pemrograman<br>Ruang H.3.1</p>
        </div></div></div>

        <div class="col"><div class="card"><div class="card-header">Selasa</div><div class="card-body">
          <h5>08:00 - 09:30</h5><p>Pemrograman Berbasis Web<br>Ruang D.2.J</p>
          <h5>14:00 - 16:00</h5><p>Basis Data<br>Ruang D.3.M</p>
        </div></div></div>

        <div class="col"><div class="card"><div class="card-header">Rabu</div><div class="card-body">
          <h5>10:00 - 12:00</h5><p>Pemrograman Berbasis Objek<br>Ruang D.2.A</p>
          <h5>13:30 - 15:00</h5><p>Pemrograman Sisi Server<br>Ruang D.2.A</p>
        </div></div></div>

        <div class="col"><div class="card"><div class="card-header">Kamis</div><div class="card-body">
          <h5>08:00 - 10:00</h5><p>Pengantar Teknologi Informasi<br>Ruang D.3.N</p>
          <h5>11:00 - 13:00</h5><p>Rapat Koordinasi DOSCOM<br>Ruang Rapat G.1</p>
        </div></div></div>

        <div class="col"><div class="card"><div class="card-header">Jumat</div><div class="card-body">
          <h5>09:00 - 11:00</h5><p>Data Mining<br>Ruang G.2.3</p>
          <h5>13:00 - 15:00</h5><p>Information Retrieval<br>Ruang G.2.4</p>
        </div></div></div>

        <div class="col"><div class="card"><div class="card-header">Sabtu</div><div class="card-body">
          <h5>08:00 - 10:00</h5><p>Bimbingan Karier<br>Online</p>
          <h5>10:30 - 12:00</h5><p>Bimbingan Skripsi<br>Online</p>
        </div></div></div>

        <div class="col"><div class="card"><div class="card-header">Minggu</div><div class="card-body">
          <p>Tidak Ada Jadwal</p>
        </div></div></div>

      </div>
    </div>
  </section>

  <!-- SECTION: ARTICLE -->
<section id="article" class="py-5">
  <div class="container mb-5 text-center">
    <h1 class="text-primary">Artikel Terbaru</h1>
  </div>

  <div class="container">
    <div class="row row-cols-1 row-cols-md-3 g-4">

      <?php
      $query = "SELECT * FROM article ORDER BY id DESC";
      $result = mysqli_query($conn, $query);

      if (mysqli_num_rows($result) > 0) {
        while ($data = mysqli_fetch_assoc($result)) {
      ?>
          <div class="col">
            <div class="card h-100">

              <?php if (!empty($data['gambar'])) { ?>
                <img src="img/<?php echo $data['gambar']; ?>" class="card-img-top" style="height:200px; object-fit:cover;">
              <?php } ?>

              <div class="card-body">
                <h5 class="card-title"><?php echo $data['judul']; ?></h5>
                <p class="card-text">
                  <?php echo substr($data['isi'], 0, 120); ?>...
                </p>
              </div>

              <div class="card-footer text-end">
                <a href="detail.php?id=<?php echo $data['id']; ?>" class="btn btn-primary btn-sm">
                  Baca Selengkapnya
                </a>
              </div>

            </div>
          </div>
      <?php
        }
      } else {
        echo "<p class='text-center'>Tidak ada artikel yang tersedia.</p>";
      }
      ?>

    </div>
  </div>
</section>


  <!-- SECTION: PROFILE -->
  <section id="profile" class="py-5">
    <div class="container text-center mb-5">
      <h1 class="text-primary">Profile Mahasiswa</h1>
    </div>

    <div class="row align-items-center justify-content-center">
      <div class="col-md-4 text-center mb-4 mb-md-0">
        <img src="rifky.jpg" alt="Foto Rifky Maulana" class="profile-img">
      </div>
      <div class="col-md-6 profile-name">
        <h4 class="fw-bold mb-3">Rifky Maulana</h4>
        <table class="table table-bordered shadow-sm">
          <tr><td>NIM</td><td>A11.2024.15950</td></tr>
          <tr><td>Program Studi</td><td>Teknik Informatika</td></tr>
          <tr><td>Email</td><td>111202415950@mhs.dinus.ac.id</td></tr>
          <tr><td>Telepon</td><td>+62 895 1777 0289</td></tr>
          <tr><td>Alamat</td><td>Ds. Rowobranten Rt03/04 Kec. Ringinarum Kab. Kendal</td></tr>
        </table>
      </div>
    </div>
  </section>

  <!-- FOOTER -->
  <footer class="text-center py-3">
    <small>© 2025 Rifky Maulana | Pemrograman Berbasis Web | A11.4314</small>
  </footer>

  <!-- SCRIPT WAKTU -->
  <script>
    function tampilWaktu() {
      const waktu = new Date();
      const bulan = waktu.getMonth() + 1;
      document.getElementById("tanggal").innerHTML = waktu.getDate() + "/" + bulan + "/" + waktu.getFullYear();
      document.getElementById("jam").innerHTML =
        waktu.getHours().toString().padStart(2, "0") + ":" +
        waktu.getMinutes().toString().padStart(2, "0") + ":" +
        waktu.getSeconds().toString().padStart(2, "0");
    }
    setInterval(tampilWaktu, 1000);
    tampilWaktu();
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
