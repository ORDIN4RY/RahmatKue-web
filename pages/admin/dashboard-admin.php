<?php
session_start();
// Cek login admin Supabase
if (!isset($_SESSION['user']) || (strtolower($_SESSION['level'] ?? '') !== 'admin')) {
  header("Location: ../../auth/login.php");
  exit();
}
$NamaAdmin = isset($_SESSION['nama']) ? $_SESSION['nama'] : (isset($_SESSION['username']) ? $_SESSION['username'] : 'Admin');

?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Admin</title>
  <link rel="stylesheet" href="../../assets/css/dashboard-admin.css">
</head>

<body>

  <?php include '../../component/sidebar.php'; ?>

  <div class="main-content">
    <div class="header">
      <button class="toggle-btn" id="toggleBtn">☰</button>
      <h2>Dashboard</h2>
      <p>Selamat datang admin <?php echo $NamaAdmin ?></?php>
      <div class="profile" id="profileButton">
        <img src="https://i.pravatar.cc/30" alt="user" />
        <div class="profile-info">
          <strong>Raffin</strong>
          <small>Admin</small>
        </div>
      </div>

      <div class="logout-menu" id="logoutMenu">
        <a href="../../auth/logout.php">Logout</a>
      </div>
    </div>

    <div class="cards">
      <div class="card"></div>
      <div class="card"></div>
      <div class="card"></div>
    </div>
  </div>

  <script>
    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('toggleBtn');
    const overlay = document.getElementById('overlay');

    // Toggle sidebar saat tombol ditekan
    toggleBtn.addEventListener('click', () => {
      const isOpen = sidebar.classList.toggle('show');
      overlay.classList.toggle('show', isOpen);
      toggleBtn.textContent = isOpen ? '✕' : '☰';
    });

    // Klik di luar sidebar untuk menutup (overlay)
    overlay.addEventListener('click', () => {
      sidebar.classList.remove('show');
      overlay.classList.remove('show');
      toggleBtn.textContent = '☰';
    });

    // Saat ukuran layar berubah
    window.addEventListener('resize', () => {
      if (window.innerWidth > 768) {
        sidebar.classList.remove('show');
        overlay.classList.remove('show');
        toggleBtn.textContent = '☰';
      }
    });

    const profileBtn = document.getElementById("profileButton");
    const logoutMenu = document.getElementById("logoutMenu");

    profileBtn.addEventListener("click", () => {
      logoutMenu.classList.toggle("show");
    });

    // Tutup menu jika klik di luar
    document.addEventListener("click", (e) => {
      if (!profileBtn.contains(e.target) && !logoutMenu.contains(e.target)) {
        logoutMenu.classList.remove("show");
      }
    });
  </script>

</body>

</html>