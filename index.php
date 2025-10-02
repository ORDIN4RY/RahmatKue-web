<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rahmat Kue</title>
    <link rel="icon" type="image/x-icon" href="assets/img/icon.png">
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/beranda.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>

<body>
    </head>
<!--hy-->
    <body>

        <?php include 'component/navbar.php'; ?>

        <!-- Header -->
        <section class="header">
            <img src="./assets/img/bg-header.png" alt="">
            <div class="header-text">
                <h1>Rahmat Tahalu asik</h1>
                <p>
                    dolor sit amet, consectetur adipiscing elit. Vivamus at sapien velit.
                    Sed ultrices vulputatemi in mollis. Integer pulvinar quam at tortor facilisis varius <br>
                    <br>Jam Operasional (setiap hari):<br>
                    06.00 - 21.00
                </p>
                <a href="produk.php" class="btn">Jelajahi Produk</a>
            </div>
        </section>

        <section class="tentang">
            <div class="icon-tentang"><img src="./assets/img/logo-remove.png" alt="" srcset="" style="position: relative; z-index: 2;"></div>
            <img src="./assets/img/Dot1.png" style="position: absolute; left: 0; height: 700px; z-index: 1;" alt="">
            <div class="tentang-text">
                <h1>Tentang Kami</h1>
                <p>dolor sit amet, consectetur adipiscing elit. Vivamus at sapien velit.
                    Sed ultrices vulputatemi in mollis. Integer pulvinar quam at tortor facilisis varius
                    a sit amet dolor. dolor sit amet, consectetur adipiscing elit. Vivamus at sapien velit.
                    Sed ultrices vulputate mi in mollis.Integer pulvinar quam at tortor facilisis varius
                    a sit amet dolor. dolor sit amet, consectetur adipiscing elit. Vivamus at sapien velit.
                    Sed ultrices vulputate mi in mollis. Integer pulvinar </p>
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

        <section class="tentang-owner">
            <div class="icon-tentang-owner"><img src="./assets/img/ex-hero.png" alt="" srcset="" style="position: relative; z-index: 2;"></div>
            <img src="./assets/img/dot2.png" style="position: absolute; right: 0; height: 700px; z-index: 1; margin-bottom: 1rem; flex-direction: row-reverse;" alt="">
            <div class="tentang-owner-text">
                <h1>Tentang Owner</h1>
                <p>dolor sit amet, consectetur adipiscing elit. Vivamus at sapien velit.
                    Sed ultrices vulputatemi in mollis. Integer pulvinar quam at tortor facilisis varius
                    a sit amet dolor. dolor sit amet, consectetur adipiscing elit. Vivamus at sapien velit.
                    Sed ultrices vulputate mi in mollis.Integer pulvinar quam at tortor facilisis varius
                    a sit amet dolor. dolor sit amet, consectetur adipiscing elit. Vivamus at sapien velit.
                    Sed ultrices vulputate mi in mollis. Integer pulvinar </p>
            </div>
        </section>

        <?php include 'component/footer.php'; ?>

    </body>

</html>