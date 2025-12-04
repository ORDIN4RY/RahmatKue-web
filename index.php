<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rahmat Kue - Beranda</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @import url("https://fonts.googleapis.com/css2?family=Abhaya+Libre:wght@400;500;600;700;800&display=swap");
        @import url("https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap");

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Montserrat", sans-serif;
        }

        body {
            background-color: #fff6f0;
        }

        /* Header */
        .header {
            position: relative;
            display: flex;
            align-items: center;
            padding: 80px 120px;
            min-height: 85vh;
            background: linear-gradient(rgba(255, 246, 240, 0.9), rgba(255, 246, 240, 0.9)),
            url("assets/img/bg-header.png") center right/cover no-repeat;
            background-color: #fff6f0;
        }

        .header-text {
            max-width: 520px;
            z-index: 1;
            font-family: "Montserrat", sans-serif;
            color: #5b3b2e;
        }

        .header-text h1 {
            font-weight: 800;
            font-size: 48px;
            line-height: 1.2;
            margin-bottom: 20px;
            font-family: "Montserrat", sans-serif;
        }

        .header-text p {
            font-size: 18px;
            line-height: 1.6;
            font-weight: 500;
            margin-bottom: 30px;
        }

        .header-text .btn {
            background: #5b3b2e;
            color: #fff;
            padding: 12px 28px;
            border-radius: 30px;
            font-size: 18px;
            text-decoration: none;
            display: inline-block;
            transition: 0.3s ease;
        }

        .header-text .btn:hover {
            background: #3e2920;
        }

        @media (max-width: 992px) {
            .header {
                padding: 60px 60px;
                background-position: center;
            }

            .header-text h1 {
                font-size: 40px;
            }
        }

        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                padding: 50px 25px;
                text-align: center;
                min-height: 90vh;
                background-position: top center;
            }

            .header-text {
                max-width: 100%;
            }

            .header-text h1 {
                font-size: 32px;
            }

            .header-text p {
                font-size: 16px;
            }
        }

        /* Produk Terlaris */
        .produk-terlaris {
            padding: 80px 20px;
            background-color: #fff6f0;
        }

        .produk-title {
            font-size: 2.5rem;
            font-weight: bold;
            color: #8b4513;
            font-family: "Montserrat", sans-serif;
            position: relative;
            display: inline-block;
            padding-bottom: 10px;
        }

        .produk-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 3px;
            background-color: #8b4513;
            transition: width 1.2s ease-out;
        }

        .produk-title.animate::after {
            width: 100%;
        }

        .produk-subtitle {
            font-size: 1.1rem;
            color: #666;
        }

        .produk-card {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0px 4px 14px rgba(0, 0, 0, 0.1);
            transition: 0.3s;
            background: white;
        }

        .produk-card:hover {
            transform: translateY(-5px);
        }

        .produk-card img {
            width: 100%;
            height: 250px;
            object-fit: cover;
            display: block;
        }

        .card-title {
            font-weight: 600;
            text-transform: capitalize;
            margin-bottom: 5px;
            color: #5b3b2e;
        }

        .card-text {
            font-weight: bold;
            margin: 0;
            color: #8b4513;
        }

        .carousel-control-prev-icon,
        .carousel-control-next-icon {
            background-color: #8b4513;
            border-radius: 50%;
            padding: 12px;
        }

        .tombol-carousel {
            width: 5%;
        }

        @media(max-width: 768px) {
            .tombol-carousel {
                width: 10%;
            }
        }

        /* Promo Section */
        .promo-section {
            background-color: #fff5f0;
            padding: 80px 20px;
        }

        .promo-title {
            font-size: 2.5rem;
            font-weight: bold;
            color: #8b4513;
            font-family: "Montserrat", sans-serif;
            position: relative;
            display: inline-block;
            padding-bottom: 10px;
        }

        .promo-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 3px;
            background-color: #8b4513;
            transition: width 1.2s ease-out;
        }

        .promo-title.animate::after {
            width: 100%;
        }

        .promo-subtitle {
            font-size: 1.1rem;
            color: #666;
        }

        .promo-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            padding: 25px;
            text-align: center;
            position: relative;
            transition: all 0.3s ease;
            height: 100%;
        }

        .promo-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .promo-img {
            width: 100%;
            height: 250px;
            object-fit: cover;
            border-radius: 10px;
        }

        .promo-card h5 {
            font-size: 1.5rem;
            font-weight: 600;
            color: #5b3b2e;
            margin-top: 1.2rem;
            margin-bottom: 0.8rem;
        }

        .promo-desc {
            font-size: 14px;
            color: #666;
            line-height: 1.6;
            margin-bottom: 1rem;
            min-height: 80px;
        }

        .promo-price {
            font-size: 20px;
            font-weight: bold;
            color: #d9534f;
        }

        @media (max-width: 768px) {
            .promo-section {
                padding: 40px 20px;
            }

            .promo-title {
                font-size: 2rem;
            }

            .promo-img {
                height: 200px;
            }
        }

        /* Tentang */
        .tentang {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 2.5rem;
            padding: 100px 80px;
            background: #fff6f0;
            flex-wrap: wrap;
        }

        .tentang-text {
            flex: 1 1 450px;
            max-width: 600px;
        }

        .tentang-text h1 {
            font-size: 2.5rem;
            font-weight: bold;
            color: #8b4513;
            margin-bottom: 15px;
            display: inline-block;
            font-family: "Montserrat", sans-serif;
            position: relative;
            padding-bottom: 10px;
        }

        .tentang-text h1::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 3px;
            background-color: #654321;
            transition: width 1.2s ease-out;
        }

        .tentang-text h1.animate::after {
            width: 100%;
        }

        .tentang-text p {
            margin-bottom: 20px;
            line-height: 1.8;
            font-weight: 400;
            font-size: 17px;
            color: #5b3b2e;
        }

        .tentang .icon-tentang img {
            width: 100%;
            max-width: 450px;
            height: auto;
            object-fit: cover;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        @media (max-width: 1024px) {
            .tentang {
                padding: 80px 50px;
            }

            .icon-tentang img {
                max-width: 400px;
            }

            .tentang-text h1 {
                font-size: 2rem;
            }
        }

        @media (max-width: 768px) {
            .tentang {
                flex-direction: column;
                text-align: center;
                padding: 60px 30px;
            }

            .icon-tentang img {
                max-width: 350px;
            }

            .tentang-text {
                max-width: 95%;
            }

            .tentang-text p {
                font-size: 16px;
            }
        }

        /* Layanan */
        .layanan-section {
            padding: 80px 20px;
            background: #fff6f0;
            text-align: center;
        }

        .layanan-section h1 {
            margin-bottom: 15px;
            font-size: 2.5rem;
            display: inline-block;
            padding-bottom: 10px;
            font-weight: bold;
            color: #8b4513;
            font-family: "Montserrat", sans-serif;
            position: relative;
        }

        .layanan-section h1::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 3px;
            background-color: #8b4513;
            transition: width 1.5s ease-out;
        }

        .layanan-section h1.animate::after {
            width: 100%;
        }

        .layanan-section p {
            font-size: 1.1rem;
            color: #666;
            margin-bottom: 50px;
        }

        .layanan-container {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 25px;
            max-width: 1200px;
            margin: auto;
        }

        .layanan-card {
            border-radius: 12px;
            overflow: hidden;
            height: 400px;
            position: relative;
            cursor: pointer;
        }

        .layanan-card-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .layanan-card:hover .layanan-card-img {
            transform: scale(1.05);
        }

        .layanan-card-overlay {
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0);
            z-index: 1;
            transition; background:  0.4s ease;
        }

        .layanan-card:hover .layanan-card-overlay {
            background: rgba(0, 0, 0, 0.75);
        }

        .layanan-card-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 88%;
            color: #fff;
            z-index: 2;
            text-align: center;
            padding: 20px;
            opacity: 0;  /* ← TAMBAHKAN INI */
            transition: opacity 0.4s ease;  /* ← GANTI INI */
        }

        .layanan-card:hover .layanan-card-content {
           opacity: 1; 
        }

        .layanan-card-content h3 {
            margin-bottom: 15px;
            font-weight: bold;
            font-family: "Montserrat", sans-serif;
            font-size: 24px;
            line-height: 1.3;
        }

        .layanan-card-content p {
            margin-bottom: 0;
            line-height: 1.6;
            font-family: "Montserrat", sans-serif;
            font-size: 14px;
            color: #fff;
        }

        @media (max-width: 900px) {
            .layanan-container {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 600px) {
            .layanan-container {
                grid-template-columns: 1fr;
            }

            .layanan-card {
                height: 350px;
            }
        }
    </style>
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
            <a href="#produk" class="btn">Jelajahi Produk</a>
        </div>
    </section>

    <!-- Produk Terlaris -->
    <section id="produk" class="produk-terlaris">
        <div class="container text-center">
            <h2 class="produk-title produk-title-animate mb-4">Produk Terlaris!</h2>
            <p class="produk-subtitle mb-5">Temukan ragam produk yang selalu menjadi pilihan utama para pelanggan</p>

            <div id="carouselKue" class="carousel slide" data-bs-ride="carousel" data-bs-interval="3000">
                <div class="carousel-inner">
                    <!-- Slide 1 -->
                    <div class="carousel-item active">
                        <div class="container">
                            <div class="row justify-content-center g-4">
                                <div class="col-12 col-md-6 col-lg-4">
                                    <div class="card produk-card">
                                        <img src="assets/img/kue-tart-lemon.jpg"
                                            class="card-img-top" alt="Kue Tart Lemon">
                                        <div class="card-body text-center">
                                            <h5 class="card-title">Kue Tart Lemon</h5>
                                            <p class="card-text">Rp. 120.000</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 col-lg-4">
                                    <div class="card produk-card">
                                        <img src="https://images.unsplash.com/photo-1578985545062-69928b1d9587?w=400"
                                            class="card-img-top" alt="Kue Tart Coklat">
                                        <div class="card-body text-center">
                                            <h5 class="card-title">Kue Tart Coklat</h5>
                                            <p class="card-text">Rp. 135.000</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 col-lg-4">
                                    <div class="card produk-card">
                                        <img src="https://images.unsplash.com/photo-1464349095431-e9a21285b5f3?w=400"
                                            class="card-img-top" alt="Kue Tart Stroberi">
                                        <div class="card-body text-center">
                                            <h5 class="card-title">Kue Tart Stroberi</h5>
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
                                <div class="col-12 col-md-6 col-lg-4">
                                    <div class="card produk-card">
                                        <img src="https://images.unsplash.com/photo-1588195538326-c5b1e5b8c8b0?w=400"
                                            class="card-img-top" alt="Kue Tart Vanila">
                                        <div class="card-body text-center">
                                            <h5 class="card-title">Kue Tart Vanila</h5>
                                            <p class="card-text">Rp. 125.000</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 col-lg-4">
                                    <div class="card produk-card">
                                        <img src="https://images.unsplash.com/photo-1606313564200-e75d5e30476c?w=400"
                                            class="card-img-top" alt="Kue Tart Blueberry">
                                        <div class="card-body text-center">
                                            <h5 class="card-title">Kue Tart Blueberry</h5>
                                            <p class="card-text">Rp. 150.000</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 col-lg-4">
                                    <div class="card produk-card">
                                        <img src="https://images.unsplash.com/photo-1587241321921-91a834d82e38?w=400"
                                            class="card-img-top" alt="Kue Tart Matcha">
                                        <div class="card-body text-center">
                                            <h5 class="card-title">Kue Tart Matcha</h5>
                                            <p class="card-text">Rp. 160.000</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <button class="carousel-control-prev tombol-carousel" type="button" data-bs-target="#carouselKue"
                    data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                </button>
                <button class="carousel-control-next tombol-carousel" type="button" data-bs-target="#carouselKue"
                    data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                </button>
            </div>
        </div>
    </section>

    <!-- Promo Section -->
    <section id="promo" class="promo-section">
        <div class="container text-center">
            <h2 class="promo-title promo-title-animate mb-4">Promo Event Spesial!</h2>
            <p class="promo-subtitle mb-5">Dapatkan penawaran menarik untuk berbagai acara istimewa Anda</p>

            <div class="row g-4 justify-content-center">
                <div class="col-lg-4 col-md-6 col-sm-12">
                    <div class="promo-card">
                        <img src="assets/img/promo-natal.png" class="promo-img"
                            alt="Promo Natal">
                        <h5 class="mt-3">Promo Natal</h5>
                        <p class="promo-desc">Lengkapi moment natal kalian bersama keluarga dengan promo spesial natal
                            kue bolu jadul hanya 80k saja.</p>
                        <p class="promo-price">Rp. 80.000</p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 col-sm-12">
                    <div class="promo-card">
                        <img src="assets/img/promo-pernikahan.png" class="promo-img"
                            alt="Promo Pernikahan">
                        <h5 class="mt-3">Promo Pernikahan</h5>
                        <p class="promo-desc">Lengkapi moment istimewa anda dengan promo spesial potongan harga kue
                            pernikahan hanya 100k</p>
                        <p class="promo-price">Rp. 100.000</p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 col-sm-12">
                    <div class="promo-card">
                        <img src="assets/img/promo-lebaran.png" class="promo-img"
                            alt="Promo Lebaran">
                        <h5 class="mt-3">Promo Lebaran</h5>
                        <p class="promo-desc">Hampers kue basah & kering premium untuk moment kebersamaan</p>
                        <p class="promo-price">Rp. 70.000</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Tentang -->
    <section id="tentang" class="tentang">
        <div class="icon-tentang">
            <img src="assets/img/tentang-foto.jpeg" alt="Tentang Kami">
        </div>
        <div class="tentang-text">
            <h1 class="tentang-title">Tentang Kami</h1>
            <p>Rahmat Kue adalah toko kue yang telah berdiri sejak 2 Januari 2005 di Kecamatan Tanggul, Kabupaten
                Jember, Jawa Timur. Kami menghadirkan beragam pilihan kue lezat serta layanan katering berkualitas untuk
                melengkapi berbagai momen spesial Anda. Mulai dari kue tradisional hingga kue modern dan tart, semua
                produk kami dibuat dari bahan-bahan pilihan dengan resep unggulan dan dikerjakan oleh tangan-tangan
                berpengalaman. Rahmat Kue senantiasa berkomitmen untuk menyajikan produk yang segar, higienis, dan penuh
                cita rasa, menghadirkan kebahagiaan manis di setiap gigitan.</p>
        </div>
    </section>

    <!-- Layanan -->
    <section id="kontak" class="layanan-section">
        <h1 class="layanan-title">Layanan Rahmat Kue</h1>
        <p>Nikmati berbagai layanan unggulan yang siap memenuhi kebutuhan pemesanan Anda</p>
        <div class="layanan-container">
            <div class="layanan-card">
                <img src="assets/img/layanan1.jpg" alt="Layanan Katering"
                    class="layanan-card-img">
                <div class="layanan-card-overlay"></div>
                <div class="layanan-card-content">
                    <h3>Layanan <br>Katering</h3>
                    <p>Hidangan lezat untuk berbagai acara, dari rapat hingga perayaan besar. Menu fleksibel dan bisa
                        disesuaikan dengan kebutuhan tamu Anda.</p>
                </div>
            </div>

            <div class="layanan-card">
                <img src="assets/img/layanan2.jpg" alt="Layanan Paket"
                    class="layanan-card-img">
                <div class="layanan-card-overlay"></div>
                <div class="layanan-card-content">
                    <h3>Layanan <br>Paket</h3>
                    <p>Paket siap dipakai untuk ulang tahun, arisan, lamaran, hingga acara spesial lainnya. Praktis,
                        lengkap, dan siap meramaikan momen Anda.</p>
                </div>
            </div>

            <div class="layanan-card">
                <img src="assets/img/layanan3.jpg" alt="Layanan Kue"
                    class="layanan-card-img">
                <div class="layanan-card-overlay"></div>
                <div class="layanan-card-content">
                    <h3>Layanan <br>Kue Satu Jenis</h3>
                    <p>Pilihan kue tradisional, modern, donat, cake dan tart premium. Selalu segar dan cocok untuk
                        hadiah, suguhan, atau perayaan.</p>
                </div>
            </div>
        </div>
    </section>

    <?php include 'component/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Animasi scroll
        function checkAnimation() {
            const elements = document.querySelectorAll('.tentang-title, .layanan-title, .produk-title-animate, .promo-title-animate');

            elements.forEach(el => {
                const rect = el.getBoundingClientRect();
                const windowHeight = window.innerHeight;
                const isVisible = rect.top < windowHeight * 0.8 && rect.bottom >= 0;

                if (isVisible) {
                    el.classList.add('animate');
                } else {
                    el.classList.remove('animate');
                }
            });
        }

        window.addEventListener('scroll', checkAnimation);
        window.addEventListener('load', checkAnimation);
    </script>
</body>

</html>