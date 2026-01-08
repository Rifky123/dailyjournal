<?php
session_start();
include "koneksi.php";

if (!isset($_SESSION['username'])) {
    header("location:login.php");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
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
                <a href="logout.php" class="nav-link text-danger fw-bold">
                    <?= $_SESSION['username'] ?>
                </a>
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

<!-- ================= SCRIPT AJAX MENU + ARTICLE ================= -->
<script>
$(document).ready(function(){

    console.log("Admin loaded");

    function loadArticleData(hlm = 1){
        console.log("Request ke article_data.php");

        $.ajax({
            url: "article_data.php",
            method: "POST",
            data: { hlm: hlm },
            success: function(res){
                $("#article_data").html(res);
                console.log("article_data.php status 200 OK");
            },
            error: function(){
                console.log("AJAX article_data.php gagal");
            }
        });
    }

    // klik menu
    $('.menu').click(function(e){
        e.preventDefault();

        let page = $(this).data('page');
        console.log("Menu diklik:", page);

        $('#judul').text(page.charAt(0).toUpperCase() + page.slice(1));

        $('#konten').load(page + '.php', function(){
            console.log(page + ".php loaded");

            // KHUSUS ARTICLE
            if(page === 'article'){
                loadArticleData();
            }
        });
    });

    // pagination
    $(document).on('click', '.halaman', function(){
        let hlm = $(this).attr("id");
        loadArticleData(hlm);
    });

});
</script>
<!-- =============================================================== -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
