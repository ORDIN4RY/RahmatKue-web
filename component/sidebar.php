<ul class="navbar-nav sidebar sidebar-dark accordion sticky-sidebar" id="accordionSidebar" style="background-color: #8E5E48;">
  
  <!-- Sidebar - Brand -->
  <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
    <div class="sidebar-brand-icon">
      <img src="../../../../assets/img/logofix.png" alt="" style="width: 50px;">
    </div>
    <div class="sidebar-brand-text mx-3">Rahmat Bakery</div>
  </a>

  <!-- Divider -->
  <hr class="sidebar-divider my-0">

  <?php
  if (session_status() === PHP_SESSION_NONE) {
    session_start();
  }
  $current_page = basename($_SERVER['PHP_SELF']);

  // Helper untuk menentukan active
  function sidebar_active($file)
  {
    global $current_page;
    return $current_page === $file ? 'active animated-active' : '';
  }
  ?>
  <style>
    .animated-active {
      background: linear-gradient(90deg, #a97c5a 0%, #8E5E48 100%);
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
      transition: background 0.4s, box-shadow 0.4s;
      border-radius: 0 30px 30px 0;
      position: relative;
    }

    .animated-active::before {
      content: '';
      position: absolute;
      left: 0;
      top: 0;
      bottom: 0;
      width: 5px;
      background: #fff;
      border-radius: 0 5px 5px 0;
      transition: background 0.4s;
    }
    .sticky-sidebar {
      position: sticky;
      top: 0;
      height: 100vh;
      z-index: 100;
    }
  </style>
  <!-- Nav Item - Dashboard -->
  <li class="nav-item <?= sidebar_active('index.php') ?>">
    <a class="nav-link" href="index.php">
      <i class="bi bi-house-door-fill"></i>
      <span>Beranda</span></a>
  </li>
  <li class="nav-item <?= sidebar_active('pesanan.php') ?>">
    <a class="nav-link" href="pesanan.php">
      <i class="bi bi-bag-fill"></i>
      <span>Pesanan</span></a>
  </li>
  <li class="nav-item <?= sidebar_active('tambah-produk.php') ?>">
    <a class="nav-link" href="tambah-produk.php">
      <i class="bi bi-plus-circle"></i>
      <span>Kelola Produk</span></a>
  </li>
    <li class="nav-item <?= sidebar_active('kelola-paket.php') ?>">
    <a class="nav-link" href="kelola-paket.php">
      <i class="bi bi-box-seam"></i>
      <span>Kelola Paket</span></a>
  </li>
  <li class="nav-item <?= sidebar_active('kelola-user.php') ?>">
    <a class="nav-link" href="kelola-user.php">
      <i class="bi bi-person-gear"></i>
      <span>Kelola User</span></a>
  </li>
  <li class="nav-item <?= sidebar_active('kelola-voucher.php') ?>">
    <a class="nav-link" href="kelola-voucher.php">
      <i class="bi bi-card-text"></i>
      <span>Kelola Voucher</span></a>
  </li>
  <li class="nav-item <?= sidebar_active('laporan.php') ?>">
    <a class="nav-link" href="laporan.php">
      <i class="bi bi-journal"></i>
      <span>Lihat Laporan</span></a>
  </li>

</ul>