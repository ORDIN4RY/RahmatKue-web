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
            <center><h1>Produk Terlaris</h1></center>

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


        <section class="tentang">
            <div class="icon-tentang"><img src="./assets/img/tentang-foto.jpeg" alt="" srcset="" style="position: relative; z-index: 2;"></div>
            <div class="tentang-text">
                <h1>Tentang Kami</h1>
                <p>Rahmat Kue adalah toko kue yang telah berdiri sejak 8 Februari.
                    2007 di Kecamatan Tanggul, Kabupaten Jember, Jawa Timur.
                    Kami menghadirkan beragam pilihan kue lezat serta layanan katering berkualitas untuk melengkapi
                    berbagai momen spesial Anda.
                    Mulai dari kue tradisional hingga kue modern dan tart, semua produk kami dibuat dari bahan-bahan pilihan dengan
                    resep unggulan dan dikerjakan oleh tangan-tangan berpengalaman.
                    Rahmat Kue senantiasa berkomitmen untuk menyajikan produk yang segar, higienis, dan penuh cita rasa,
                    menghadirkan kebahagiaan manis di setiap gigitan. </p>
            </div>
        </section>

        <section class="layanan-section">
            <h1>Layanan Rahmat Kue</h1>
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
                        <a href="produk.php?kategori=custom" class="btn-layanan">Hubungi Kami</a>
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
                        <a href="produk.php?kategori=paket" class="btn-layanan">Hubungi Kami</a>
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
                        <a href="produk.php" class="btn-layanan">Hubungi Kami</a>
                    </div>
                </div>

            </div>
        </section>

        <?php include 'component/footer.php'; ?>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            window.addEventListener('DOMContentLoaded', function() {
                const welcomeModal = new bootstrap.Modal(document.getElementById('welcomeModal'));
                welcomeModal.show();
            });
        </script>
    </body>

</html>