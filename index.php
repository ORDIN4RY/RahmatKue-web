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
            <img src="./assets/img/bg-header.png" alt="">
            <div class="header-text">
                <h1>Selamat Datang di <br>
                    Rahmat Kue !
                </h1>
                Kami hadir dengan beragam pilihan kue lezat dan layanan katering terbaik untuk setiap momen spesial Anda. <br>
                Semua dibuat dari bahan berkualitas dan sentuhan cinta. <br>
                agar menghadirkan rasa manis di setiap gigitan. <br>
                Nikmati cita rasa istimewa, pelayanan ramah, dan pengalaman kuliner yang berkesan <br>
                <br>Buka setiap hari:<br>
                06.00 - 21.00
                </p>
                <a href="produk.php" class="btn">Jelajahi Produk</a>
            </div>
            <div class="modal fade show" id="welcomeModal" tabindex="-1" aria-labelledby="welcomeModalLabel" aria-hidden="false">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="welcomeModalLabel">
                                <i class="bi bi-hand-wave wave-animation"></i> Selamat Datang!
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <h4 class="mb-3">Terima kasih telah mengunjungi website Rahmat Kue</h4>
                            <p class="text-muted">
                                <b>Perhatian:</b> Pemesanan melalui website ini menggunakan sistem pre order.
                            </p>
                            <div class="preorder-note">
                                <i class="bi bi-info-circle"></i>
                                <span>Pastikan untuk membaca informasi pre order dengan teliti</span>
                            </div>
                        </div>
                        <div class="modal-footer border-0 justify-content-center pb-4" style="background-color: #FFF8F0;">
                            <button type="button" class="btn btn-custom" data-bs-dismiss="modal">Mengerti, Lanjutkan</button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="produk-terlaris">
            <center>
                <h1>Produk Terlaris</h1>
            </center>
            <div id="carouselKue" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">

                    <!-- Slide 1 -->
                    <div class="carousel-item active">
                        <div class="d-flex justify-content-center gap-4">
                            <div>
                                <img src="./assets/img/kue-tart-lemon.jpg" class="d-block" width="300">
                                <div class="produk-info">
                                    <h5>kue tart lemon</h5>
                                    <p>Rp. 120.000</p>
                                </div>
                            </div>
                            <div>
                                <img src="./assets/img/kue-tart-lemon.jpg" class="d-block" width="300">
                                <div class="produk-info">
                                    <h5>kue tart lemon</h5>
                                    <p>Rp. 120.000</p>
                                </div>
                            </div>
                            <div>
                                <img src="./assets/img/kue-tart-lemon.jpg" class="d-block" width="300">
                                <div class="produk-info">
                                    <h5>kue tart lemon</h5>
                                    <p>Rp. 120.000</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Slide 2 -->
                    <div class="carousel-item">
                        <div class="d-flex justify-content-center gap-4">
                            <div>
                                <img src="./assets/img/kue-tart-lemon.jpg" class="d-block" width="300">
                                <div class="produk-info">
                                    <h5>kue tart coklat</h5>
                                    <p>Rp. 135.000</p>
                                </div>
                            </div>
                            <div>
                                <img src="./assets/img/kue-tart-lemon.jpg" class="d-block" width="300">
                                <div class="produk-info">
                                    <h5>kue tart stroberi</h5>
                                    <p>Rp. 140.000</p>
                                </div>
                            </div>
                            <div>
                                <img src="./assets/img/kue-tart-lemon.jpg" class="d-block" width="300">
                                <div class="produk-info">
                                    <h5>kue tart vanila</h5>
                                    <p>Rp. 125.000</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <button class="carousel-control-prev" type="button" data-bs-target="#carouselKue" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carouselKue" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                </button>
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
                    <img src="./assets/img/layanan1.png" alt="layanan" class="layanan-card-img">
                    <div class="layanan-card-overlay"></div>
                    <div class="layanan-card-content">
                        <h3>Layanan Pelanggan</h3>
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas consequat neque metus.</p>
                        <a href="#" class="btn-layanan">Hubungi Kami</a>
                    </div>
                </div>

                <!-- Card 2 -->
                <div class="layanan-card">
                    <img src="./assets/img/layanan2.png" alt="layanan" class="layanan-card-img">
                    <div class="layanan-card-overlay"></div>
                    <div class="layanan-card-content">
                        <h3>Layanan Pelanggan</h3>
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas consequat neque metus.</p>
                        <a href="#" class="btn-layanan">Hubungi Kami</a>
                    </div>
                </div>

                <!-- Card 3 -->
                <div class="layanan-card">
                    <img src="./assets/img/layanan3.png" alt="layanan" class="layanan-card-img">
                    <div class="layanan-card-overlay"></div>
                    <div class="layanan-card-content">
                        <h3>Layanan Pelanggan</h3>
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas consequat neque metus.</p>
                        <a href="#" class="btn-layanan">Hubungi Kami</a>
                    </div>
                </div>

            </div>
        </section>

        <section class="testimoni">
            <div class="container">
                <h1>Testimoni Pelanggan</h1>

                <div id="testimoniCarousel" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        <!-- Slide 1 -->
                        <div class="carousel-item active">
                            <div class="d-flex justify-content-center gap-4">
                                <div class="testimoni-card">
                                    <div class="rating mb-3">
                                        <i class="fas fa-star text-warning"></i>
                                        <i class="fas fa-star text-warning"></i>
                                        <i class="fas fa-star text-warning"></i>
                                        <i class="fas fa-star text-warning"></i>
                                        <i class="fas fa-star-half-alt text-warning"></i>
                                    </div>
                                    <p class="testimoni-text">"Kue sangat enak dan tetap fresh"</p>
                                    <p class="testimoni-author">- Abdullah Arya</p>
                                </div>
                                <div class="testimoni-card">
                                    <div class="rating mb-3">
                                        <i class="fas fa-star text-warning"></i>
                                        <i class="fas fa-star text-warning"></i>
                                        <i class="fas fa-star text-warning"></i>
                                        <i class="fas fa-star text-warning"></i>
                                        <i class="fas fa-star text-warning"></i>
                                    </div>
                                    <p class="testimoni-text">"Pelayanannya sangat memuaskan dan terbaik"</p>
                                    <p class="testimoni-author">- Anzelina Sumiati</p>
                                </div>
                                <div class="testimoni-card">
                                    <div class="rating mb-3">
                                        <i class="fas fa-star text-warning"></i>
                                        <i class="fas fa-star text-warning"></i>
                                        <i class="fas fa-star text-warning"></i>
                                        <i class="fas fa-star text-warning"></i>
                                        <i class="far fa-star text-warning"></i>
                                    </div>
                                    <p class="testimoni-text">"Rasanya sangat enak dan tidak kalah lezat"</p>
                                    <p class="testimoni-author">- Anzelina Sumiati</p>
                                </div>
                            </div>
                        </div>

                        <!-- Slide 2 -->
                        <div class="carousel-item">
                            <div class="d-flex justify-content-center gap-4">
                                <div class="testimoni-card">
                                    <div class="rating mb-3">
                                        <i class="fas fa-star text-warning"></i>
                                        <i class="fas fa-star text-warning"></i>
                                        <i class="fas fa-star text-warning"></i>
                                        <i class="fas fa-star text-warning"></i>
                                        <i class="fas fa-star text-warning"></i>
                                    </div>
                                    <p class="testimoni-text">"Saya sangat puas dengan pelayanannya"</p>
                                    <p class="testimoni-author">- Sarah Johnson</p>
                                </div>
                                <div class="testimoni-card">
                                    <div class="rating mb-3">
                                        <i class="fas fa-star text-warning"></i>
                                        <i class="fas fa-star text-warning"></i>
                                        <i class="fas fa-star text-warning"></i>
                                        <i class="fas fa-star text-warning"></i>
                                        <i class="fas fa-star-half-alt text-warning"></i>
                                    </div>
                                    <p class="testimoni-text">"Kuenya lembut dan tidak terlalu manis"</p>
                                    <p class="testimoni-author">- Michael Chen</p>
                                </div>
                                <div class="testimoni-card">
                                    <div class="rating mb-3">
                                        <i class="fas fa-star text-warning"></i>
                                        <i class="fas fa-star text-warning"></i>
                                        <i class="fas fa-star text-warning"></i>
                                        <i class="fas fa-star text-warning"></i>
                                        <i class="fas fa-star text-warning"></i>
                                    </div>
                                    <p class="testimoni-text">"Pelayanan cepat dan ramah"</p>
                                    <p class="testimoni-author">- Linda Wijaya</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Carousel Controls -->
                    <button class="carousel-control-prev" type="button" data-bs-target="#testimoniCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#testimoniCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon"></span>
                    </button>

                    <!-- Carousel Indicators -->
                    <div class="carousel-indicators">
                        <button type="button" data-bs-target="#testimoniCarousel" data-bs-slide-to="0" class="active" aria-current="true"></button>
                        <button type="button" data-bs-target="#testimoniCarousel" data-bs-slide-to="1"></button>
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