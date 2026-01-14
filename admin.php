<?php
session_start();
include "koneksi.php";

// Proteksi login
if(!isset($_SESSION['username'])){
    header("location:login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>My Daily Journal | Admin</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-sm" style="background-color:#d4e2b6;">
    <div class="container">
        <a class="navbar-brand" href="#">My Daily Journal</a>
        <ul class="navbar-nav ms-auto">
            <li class="nav-item">
                <a href="#" class="nav-link menu" data-page="dashboard">Dashboard</a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link menu" data-page="article">Article</a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link menu" data-page="gallery">Gallery</a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link menu" data-page="user_data">User</a>
            </li>
            <li class="nav-item">
                <a href="logout.php" class="nav-link text-danger fw-bold"><?= $_SESSION['username'] ?></a>
            </li>
        </ul>
    </div>
</nav>

<!-- CONTENT -->
<section class="p-4">
    <div class="container">
        <h4 id="judul" class="border-bottom pb-2 mb-3">Dashboard</h4>
        <div id="konten">
            <?php include "dashboard.php"; ?>
        </div>
    </div>
</section>

<!-- ================= SCRIPT AJAX MENU ================= -->
<script>
$(document).ready(function(){

    // Fungsi load Article dengan pagination AJAX
    function loadArticleData(hlm = 1){
        $.ajax({
            url: "article_data.php",
            method: "POST",
            data: { hlm: hlm },
            success: function(res){
                $("#konten").html(res);
            },
            error: function(){
                alert("Gagal load data article.");
            }
        });
    }

    // Fungsi load Gallery dengan pagination AJAX
    function loadGalleryData(hlm = 1){
        $.ajax({
            url: "gallery_data.php",
            method: "GET",
            data: { hal: hlm },
            success: function(res){
                $("#konten").html(res);
            },
            error: function(){
                alert("Gagal load data gallery.");
            }
        });
    }

    // Klik menu
    $('.menu').click(function(e){
        e.preventDefault();
        let page = $(this).data('page');

        // Ganti judul
        $('#judul').text(page.charAt(0).toUpperCase() + page.slice(1));

        // Load konten
        if(page === 'article'){
            loadArticleData();
        } else if(page === 'gallery'){
            loadGalleryData();
        } else {
            $('#konten').load(page + '.php');
        }
    });

    // Pagination untuk Article
    $(document).on('click', '.halaman-article', function(e){
        e.preventDefault();
        let hlm = $(this).data('page');
        loadArticleData(hlm);
    });

    // Pagination untuk Gallery
    $(document).on('click', '.halaman-gallery', function(e){
        e.preventDefault();
        let hlm = $(this).data('page');
        loadGalleryData(hlm);
    });

});
</script>
<!-- ==================================================== -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
