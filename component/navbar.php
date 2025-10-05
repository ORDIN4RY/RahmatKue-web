<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navbar</title>
    <link rel="stylesheet" href="assets/css/main.css">
</head>

<body>
    <?php
    // ambil nama file halaman sekarang
    $current_page = basename($_SERVER['PHP_SELF']);
    ?>

    <div class="navbar" id="navbar">
        <p>Rahmat Kue</p>
        <a href="index.php" class="<?= ($current_page == 'index.php') ? 'active' : '' ?>">Beranda</a>
        <a href="produk.php" class="<?= ($current_page == 'produk.php') ? 'active' : '' ?>">Produk</a>
        <a href="pesan.php" class="<?= ($current_page == 'pesan.php') ? 'active' : '' ?>">Pesanan</a>
        <a href="auth/login.php">Login</a>
    </div>
    
    <script>
        window.addEventListener("scroll", function() {
            const navbar = document.getElementById("navbar");
            navbar.classList.toggle("scrolled", window.scrollY > 50);
        });
    </script>
</body>

</html>