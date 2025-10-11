<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sidebar</title>
</head>
<body>

    <?php
        $current_page = basename($_SERVER['PHP_SELF']);
    ?>

    <div class="sidebar" id="sidebar">
    <div class="logo"><img src="../../assets/img/icon-admin-cake.png" alt="Gambar">Rahmat Kue</div>
    <div class="menu">
      <a href="dashboard-admin.php" class="<?= ($current_page == 'dashboard-admin.php') ? 'active' : '' ?>">Dashboard</a>
      <a href="tambah-produk.php" class="<?= ($current_page == 'tambah-produk.php') ? 'active' : '' ?>">Tambah Produk</a>
      <a href="#">Pesanan</a>
      <a href="#">Kelola Voucher</a>
      <a href="#">Edit Profil</a>
    </div>
  </div>

  <div class="overlay" id="overlay"></div>
</body>
</html>