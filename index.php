<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rahmat Kue - Beranda</title>
    <link rel="icon" type="image/x-icon" href="assets/img/icon.png">
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/beranda.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    </head>

    <body>

        <?php include 'component/navbar.php'; ?>

        <!-- Header -->

        <section class="header">
            <div class="header-text">
                <h1>Selamat Datang di <br> Rahmat Bakery !</h1>

                <p>
                    Nikmati beragam pilihan kue lezat dan layanan katering <br>
                    terbaik untuk melengkapi setiap momen spesial Anda. <br><br>
                    Kami buka setiap hari pukul 06.00 - 21.00 <br>
                    Pemesanan menggunakan sistem Pre-Order (PO) <br>
                    dan maksimal 2 hari sebelum acara atau pengiriman.
                </p>
                <a href="produk.php" class="btn">Jelajahi Produk</a>
            </div>
        </section>

        <section class="produk-terlaris">
            <div class="container text-center">
                <h2 class="produk-title produk-title-animate mb-4">Produk Terlaris!</h2>
        <p class="produk-subtitle mb-5">Temukan ragam produk yang selalu menjadi pilihan utama para pelanggan</p>

            <div id="carouselKue" class="carousel slide" data-bs-ride="carousel" data-bs-interval="2000">
                <div class="carousel-inner">

                    <!-- Slide 1 -->
                    <div class="carousel-item active">
                        <div class="container">
                            <div class="row justify-content-center g-4">

                                <!-- Card 1 -->
                                <div class="col-12 col-md-6 col-lg-4">
                                    <div class="card produk-card">
                                        <img src="./assets/img/kue-tart-lemon.jpg" class="card-img-top">
                                        <div class="card-body text-center">
                                            <h5 class="card-title">kue tart lemon</h5>
                                            <p class="card-text">Rp. 120.000</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Card 2 -->
                                <div class="col-12 col-md-6 col-lg-4">
                                    <div class="card produk-card">
                                        <img src="./assets/img/kue-tart-lemon.jpg" class="card-img-top">
                                        <div class="card-body text-center">
                                            <h5 class="card-title">kue tart coklat</h5>
                                            <p class="card-text">Rp. 135.000</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Card 3 -->
                                <div class="col-12 col-md-6 col-lg-4">
                                    <div class="card produk-card">
                                        <img src="./assets/img/kue-tart-lemon.jpg" class="card-img-top">
                                        <div class="card-body text-center">
                                            <h5 class="card-title">kue tart stroberi</h5>
                                            <p class="card-text">Rp. 140.000</p>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- Slide 2 -->
                    <div class="carousel-item">
                        <div class="container">
                            <div class="row justify-content-center g-4">

                                <!-- Card 4 -->
                                <div class="col-12 col-md-6 col-lg-4">
                                    <div class="card produk-card">
                                        <img src="./assets/img/kue-tart-lemon.jpg" class="card-img-top">
                                        <div class="card-body text-center">
                                            <h5 class="card-title">kue tart vanila</h5>
                                            <p class="card-text">Rp. 125.000</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Card 5 -->
                                <div class="col-12 col-md-6 col-lg-4">
                                    <div class="card produk-card">
                                        <img src="./assets/img/kue-tart-lemon.jpg" class="card-img-top">
                                        <div class="card-body text-center">
                                            <h5 class="card-title">kue tart blueberry</h5>
                                            <p class="card-text">Rp. 150.000</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Card 6 -->
                                <div class="col-12 col-md-6 col-lg-4">
                                    <div class="card produk-card">
                                        <img src="./assets/img/kue-tart-lemon.jpg" class="card-img-top">
                                        <div class="card-body text-center">
                                            <h5 class="card-title">kue tart matcha</h5>
                                            <p class="card-text">Rp. 160.000</p>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                </div>

                <!-- Prev -->
                <button class="carousel-control-prev tombol-carousel" type="button" data-bs-target="#carouselKue" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                </button>

                <!-- Next -->
                <button class="carousel-control-next tombol-carousel" type="button" data-bs-target="#carouselKue" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                </button>

            </div>
        </section>

 <section id="promo" class="promo-section py-5">
    <div class="container text-center">
       <h2 class="promo-title promo-title-animate mb-4">Promo Event Spesial!</h2>
        <p class="promo-subtitle mb-5">Dapatkan penawaran menarik untuk berbagai acara istimewa Anda</p>

        <div class="row g-4 justify-content-center">

            <!-- Card Promo 1 - Promo Ulang Tahun -->
            <div class="col-lg-4 col-md-6 col-sm-12">
                <div class="promo-card">
                    <img src="./assets/img/promo-natal.png" class="promo-img" alt="Promo Ulang Tahun">
                    <h5 class="mt-3">Promo Natal</h5>
                    <p class="promo-desc">Lengkapi moment natal kalian bersama keluarga dengan pormo spesial natal kue bolu jadul hanya 80k saja.</p>
                    <p class="promo-price">Rp. 80.000</p>
                </div>
            </div>

            <!-- Card Promo 2 - Promo Wedding -->
            <div class="col-lg-4 col-md-6 col-sm-12">
                <div class="promo-card">
                    <img src="./assets/img/promo-pernikahan.png" class="promo-img" alt="Promo Pernikahan">
                    <h5 class="mt-3">Promo Pernikahan</h5>
                    <p class="promo-desc">Lengkapi moment istimewa anda dengan promo spesial potonngan harga kue pernikahan hanya 100k  </p>
                    <p class="promo-price">Rp. 100.000</p>
                </div>
            </div>

            <!-- Card Promo 3 - Promo Lebaran -->
            <div class="col-lg-4 col-md-6 col-sm-12">
                <div class="promo-card">
                    <img src="./assets/img/promo-lebaran.png" class="promo-img" alt="Promo Lebaran">
                    <h5 class="mt-3">Promo Lebaran</h5>
                    <p class="promo-desc">Hampers kue basah & kering premium untuk moment kebersamaan</p>
                    <p class="promo-price">Rp. 70.000</p>
                </div>
            </div>
        </div>
    </div>
</section>

        <section class="tentang">
            <div class="icon-tentang"><img src="./assets/img/tentang-foto.jpeg" alt="" srcset="" style="position: relative; z-index: 2;"></div>
            <div class="tentang-text">
                <h1 class="tentang-title">Tentang Kami</h1>
                <p>Rahmat Kue adalah toko kue yang telah berdiri sejak 2 Januari
                    2005 di Kecamatan Tanggul, Kabupaten Jember, Jawa Timur.
                    Kami menghadirkan beragam pilihan kue lezat serta layanan katering berkualitas untuk melengkapi
                    berbagai momen spesial Anda.
                    Mulai dari kue tradisional hingga kue modern dan tart, semua produk kami dibuat dari bahan-bahan pilihan dengan
                    resep unggulan dan dikerjakan oleh tangan-tangan berpengalaman.
                    Rahmat Kue senantiasa berkomitmen untuk menyajikan produk yang segar, higienis, dan penuh cita rasa,
                    menghadirkan kebahagiaan manis di setiap gigitan. </p>
            </div>
        </section>

   <section class="layanan-section">
            <h1 class="layanan-title">Layanan Rahmat Kue</h1>
            <p>Nikmati berbagai layanan unggulan yang siap memenuhi kebutuhan pemesanan Anda</p>
            <div class="layanan-container">

                <!-- Card 1 -->
                <div class="layanan-card">
                    <img src="./assets/img/layanan1.jpg" alt="layanan" class="layanan-card-img">
                    <div class="layanan-card-overlay"></div>
                    <div class="layanan-card-content">
                        <h3>Layanan <br>Katering</h3>
                        <p>Hidangan lezat untuk berbagai acara, dari rapat hingga perayaan besar.
                             Menu fleksibel dan bisa disesuaikan dengan kebutuhan tamu Anda.
                        </p>
                    </div>
                </div>

                <!-- Card 2 -->
                <div class="layanan-card">
                    <img src="./assets/img/layanan2.jpg" alt="layanan" class="layanan-card-img">
                    <div class="layanan-card-overlay"></div>
                    <div class="layanan-card-content">
                        <h3>Layanan <br>Paket</h3>
                        <p>Paket siap dipakai untuk ulang tahun, arisan, lamaran, hingga acara spesial lainnya.
                             Praktis, lengkap, dan siap meramaikan momen Anda.
                        </p>
                    </div>
                </div>

                <!-- Card 3 -->
                <div class="layanan-card">
                    <img src="./assets/img/layanan3.jpg" alt="layanan" class="layanan-card-img">
                    <div class="layanan-card-overlay"></div>
                    <div class="layanan-card-content">
                        <h3>Layanan <br>Kue Satu Jenis</h3>
                        <p>Pilihan kue tradisional, modern, donat, cake dan tart premium. 
                            Selalu segar dan cocok untuk hadiah, suguhan, atau perayaan.
                        </p>
                    </div>
                </div>

            </div>
        </section>

        <?php include 'component/footer.php'; ?>

        
<script>
    // Animasi untuk Tentang Kami (bisa reset)
    function checkTentangAnimation() {
        const title = document.querySelector('.tentang-title');
        if (!title) return;
        
        const rect = title.getBoundingClientRect();
        const windowHeight = window.innerHeight;
        const isVisible = rect.top < windowHeight * 0.8 && rect.bottom >= 0;
        
        if (isVisible) {
            title.classList.add('animate');
        } else {
            title.classList.remove('animate');
        }
    }
    
    // Animasi untuk Layanan (bisa reset)
    function checkLayananAnimation() {
        const title = document.querySelector('.layanan-title');
        if (!title) return;
        
        const rect = title.getBoundingClientRect();
        const windowHeight = window.innerHeight;
        const isVisible = rect.top < windowHeight * 0.8 && rect.bottom >= 0;
        
        if (isVisible) {
            title.classList.add('animate');
        } else {
            title.classList.remove('animate');
        }
    }
    
    // Animasi untuk Produk Terlaris (bisa reset)
    function checkProdukAnimation() {
        const title = document.querySelector('.produk-title-animate');
        if (!title) return;
        
        const rect = title.getBoundingClientRect();
        const windowHeight = window.innerHeight;
        const isVisible = rect.top < windowHeight * 0.8 && rect.bottom >= 0;
        
        if (isVisible) {
            title.classList.add('animate');
        } else {
            title.classList.remove('animate');
        }
    }
    
    // Animasi untuk Promo (bisa reset)
    function checkPromoAnimation() {
        const title = document.querySelector('.promo-title-animate');
        if (!title) return;
        
        const rect = title.getBoundingClientRect();
        const windowHeight = window.innerHeight;
        const isVisible = rect.top < windowHeight * 0.8 && rect.bottom >= 0;
        
        if (isVisible) {
            title.classList.add('animate');
        } else {
            title.classList.remove('animate');
        }
    }
    
    // Event listeners
    window.addEventListener('scroll', function() {
        checkTentangAnimation();
        checkLayananAnimation();
        checkProdukAnimation();
        checkPromoAnimation();
    });
    
    window.addEventListener('load', function() {
        checkTentangAnimation();
        checkLayananAnimation();
        checkProdukAnimation();
        checkPromoAnimation();
    });
</script>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            window.addEventListener('DOMContentLoaded', function() {
                const welcomeModal = new bootstrap.Modal(document.getElementById('welcomeModal'));
                welcomeModal.show();
            });
        </script>
    </body>

</html>