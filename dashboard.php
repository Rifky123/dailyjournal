<?php
include "koneksi.php";

// Ambil jumlah data article
$jumlah_article = $conn->query("SELECT * FROM article")->num_rows;

// Ambil jumlah data gallery
$jumlah_gallery = $conn->query("SELECT * FROM gallery")->num_rows;
?>

<div class="row row-cols-1 row-cols-md-2 g-4 justify-content-center pt-4">

    <!-- CARD ARTICLE -->
    <div class="col">
        <div class="card border border-danger mb-3 shadow clickable-card" 
             style="max-width: 18rem; cursor:pointer;" 
             data-page="article">
            <div class="card-body d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0"><i class="bi bi-newspaper"></i> Article</h5>
                <span class="badge rounded-pill text-bg-danger fs-2"><?php echo $jumlah_article; ?></span>
            </div>
        </div>
    </div> 

    <!-- CARD GALLERY -->
    <div class="col">
        <div class="card border border-danger mb-3 shadow clickable-card" 
             style="max-width: 18rem; cursor:pointer;" 
             data-page="gallery">
            <div class="card-body d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0"><i class="bi bi-camera"></i> Gallery</h5>
                <span class="badge rounded-pill text-bg-danger fs-2"><?php echo $jumlah_gallery; ?></span>
            </div>
        </div>
    </div> 

</div>

<!-- SCRIPT AJAX UNTUK LOAD PAGE -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script>
$(document).ready(function(){

    $('.clickable-card').click(function(){
        let page = $(this).data('page');
        
        // ganti judul
        $('#judul').text(page.charAt(0).toUpperCase() + page.slice(1));

        // load konten via AJAX
        $('#konten').load(page + '.php', function(){
            console.log(page + ".php loaded via dashboard card");

            // jalankan pagination khusus
            if(page === 'article' && typeof loadArticleData === 'function'){
                loadArticleData(); // pastikan fungsi ini ada di admin.php
            }

            if(page === 'gallery' && typeof loadGalleryData === 'function'){
                loadGalleryData(); // pastikan fungsi ini ada di admin.php
            }
        });
    });

});
</script>
