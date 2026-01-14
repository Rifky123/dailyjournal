<?php
//memulai session atau melanjutkan session yang sudah ada
session_start();

//menyertakan code dari file koneksi
include "koneksi.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $username = $_POST['user'];
  
  //menggunakan fungsi enkripsi md5 supaya sama dengan password  yang tersimpan di database
  $password = md5($_POST['pass']);

	//prepared statement
  $stmt = $conn->prepare("SELECT username 
                          FROM user 
                          WHERE username=? AND password=?");

	//parameter binding 
  $stmt->bind_param("ss", $username, $password);//username string dan password string
  
  //database executes the statement
  $stmt->execute();
  
  //menampung hasil eksekusi
  $hasil = $stmt->get_result();
  
  //mengambil baris dari hasil sebagai array asosiatif
  $row = $hasil->fetch_array(MYSQLI_ASSOC);

  //check apakah ada baris hasil data user yang cocok
  if (!empty($row)) {
    //jika ada, simpan variable username pada session
    $_SESSION['username'] = $row['username'];

    //mengalihkan ke halaman admin
    header("location:admin.php");
  } else {
	  //jika tidak ada (gagal), alihkan kembali ke halaman login
    header("location:login.php");
  }

	//menutup koneksi database
  $stmt->close();
  $conn->close();
} else {
?>
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login | Daily Journal</title>
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
      rel="stylesheet"
      integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN"
      crossorigin="anonymous"
    />
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css"
    />
    <link rel="icon" href="img/logo.png" />
    <style>
      .bg-pink-soft {
        background-color: #f7d6d9; 
      }
      .info-pill {
        border-radius: 18px;
        padding: 12px 18px;
        display: inline-block;
        min-width: 220px;
      }
      .soft-shadow {
        box-shadow: 0 8px 18px rgba(0,0,0,0.08);
      }
    </style>
  </head>
  <body class="bg-pink-soft">
    <div class="container min-vh-100 d-flex align-items-center">
      <div class="row w-100">
        <div class="col-12 col-sm-10 col-md-8 col-lg-6 mx-auto">
          <!-- Card utama -->
          <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="card-body px-4 py-5">
              <div class="text-center mb-3">
                <div class="rounded-circle bg-white d-inline-flex align-items-center justify-content-center mb-2"
                     style="width:72px;height:72px; box-shadow:0 2px 6px rgba(0,0,0,0.06);">
                  <i class="bi bi-person-circle" style="font-size:36px;color:#333;"></i>
                </div>
                <h6 class="mb-1" style="font-weight:600;">My Daily Journal</h6>
                <hr class="mt-3 mb-2" />
              </div>

              <form action="" method="post" autocomplete="off">
                <input
                  type="text"
                  name="user"
                  class="form-control form-control-lg mb-3 rounded-3"
                  placeholder="Username"
                />
                <input
                  type="password"
                  name="pass"
                  class="form-control form-control-lg mb-3 rounded-3"
                  placeholder="Password"
                />
                <div class="d-grid">
                  <button type="submit" class="btn btn-danger btn-lg rounded-3">
                    Login
                  </button>
                </div>
              </form>
            </div>
          </div>

          <!-- Kotak info (muncul selalu, dan akan diisi PHP sesuai hasil) -->
          <div class="text-center mt-4">
            <?php
            // set variable username dan password dummy
            $username = "admin";
            $password = "123456";


            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                echo '<div class="d-inline-block info-pill bg-white soft-shadow">';
                foreach($_POST as $key => $val){
                 
                    echo htmlspecialchars($key, ENT_QUOTES, 'UTF-8') . " : " . htmlspecialchars($val, ENT_QUOTES, 'UTF-8') . "<br>";
                }
                echo '</div><br><br>';

                if( isset($_POST['user']) && isset($_POST['pass']) && $_POST['user'] == $username && $_POST['pass'] == $password ){
                    echo '<div class="d-inline-block px-3 py-2 bg-success-subtle text-success rounded-3 soft-shadow" style="min-width:220px;">';
                    echo '<small><strong>Username dan Password Benar</strong></small>';
                    echo '</div>';
                } else {
                    echo '<div class="d-inline-block px-3 py-2 bg-warning-subtle text-warning rounded-3 soft-shadow" style="min-width:220px;">';
                    echo '<small><strong>Username dan Password Salah</strong></small>';
                    echo '</div>';
                }
            } else 
                echo '<div class="d-inline-block px-3 py-2 bg-info-subtle text-info rounded-3 soft-shadow" style="min-width:220px;">';
               
                echo '</div>';
            
            ?>
          </div>
        </div>
      </div>
    </div>

    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
      integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
      crossorigin="anonymous"
    ></script>
  </body>
</html>
<?php
}
?>