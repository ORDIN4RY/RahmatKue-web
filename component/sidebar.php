<ul class="navbar-nav sidebar sidebar-dark accordion" id="accordionSidebar" style="background-color: #8E5E48;">

  <!-- Sidebar - Brand -->
  <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
    <div class="sidebar-brand-icon">
      <img src="../../../assets/img/logo-remove.png" alt="" style="width: 40px;">
    </div>
    <div class="sidebar-brand-text mx-3">Rahmat Kue</div>
  </a>

  <!-- Divider -->
  <hr class="sidebar-divider my-0">

  <?php
  if (session_status() === PHP_SESSION_NONE) {
    session_start();
  }
  $current_page = basename($_SERVER['PHP_SELF']);
  ?>
  <!-- Nav Item - Dashboard -->
  <li class="nav-item active">
    <a class="nav-link" href="dashboard-admin2.php">
      <i class="bi bi-house-door-fill"></i>
      <span>Beranda</span></a>
  </li>
  <li class="nav-item">
    <a class="nav-link" href="index.html">
      <i class="bi bi-bag-fill"></i>
      <span>Pesanan</span></a>
  </li>
  <li class="nav-item">
    <a class="nav-link" href="tambah-produk.php">
      <i class="bi bi-plus-circle"></i>
      <span>Kelola Produk</span></a>
  </li>
  <li class="nav-item">
    <a class="nav-link" href="index.html">
      <i class="bi bi-person-gear"></i>
      <span>Kelola User</span></a>
  </li>
  <li class="nav-item">
    <a class="nav-link" href="index.html">
      <i class="bi bi-card-text"></i>
      <span>Kelola Voucher</span></a>
  </li>
  <li class="nav-item active">
    <a class="nav-link" href="../../../auth/logout.php">
      <i class="bi bi-box-arrow-left"></i>
      <span>Logout</span></a>
  </li>


</ul>