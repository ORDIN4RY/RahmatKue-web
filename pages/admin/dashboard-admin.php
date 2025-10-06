<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Admin</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Poppins', sans-serif;
    }

    body {
      display: flex;
      min-height: 100vh;
      background-color: #fdf5f2;
      overflow-x: hidden;
    }

    /* ===== SIDEBAR ===== */
    .sidebar {
      width: 240px;
      background-color: #f8e3d9;
      padding: 20px;
      transition: transform 0.3s ease;
      z-index: 200;
    }

    .sidebar.hidden {
      transform: translateX(-100%);
    }

    .logo {
      font-weight: bold;
      font-size: 20px;
      margin-bottom: 30px;
    }

    .menu a {
      display: block;
      padding: 10px 15px;
      margin-bottom: 10px;
      border-radius: 10px;
      text-decoration: none;
      color: #333;
      transition: background-color 0.2s;
    }

    .menu a.active,
    .menu a:hover {
      background-color: #fddbc8;
    }

    /* ===== MAIN CONTENT ===== */
    .main-content {
      flex: 1;
      padding: 20px;
    }

    .header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
    }

    .header h2 {
      color: #4a2b16;
    }

    .profile {
      display: flex;
      align-items: center;
      background: #fff;
      padding: 5px 10px;
      border-radius: 10px;
      box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);
      gap: 8px;
    }

    .profile img {
      width: 30px;
      height: 30px;
      border-radius: 50%;
    }

    /* ===== DASHBOARD CARDS ===== */
    .cards {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 20px;
    }

    .card {
      background-color: #fde6d9;
      padding: 20px;
      border-radius: 15px;
      height: 120px;
    }

    /* ===== TOGGLE BUTTON ===== */
    .toggle-btn {
      display: none;
      font-size: 24px;
      cursor: pointer;
      background: none;
      border: none;
      color: #4a2b16;
      z-index: 300;
    }

    /* ===== OVERLAY ===== */
    .overlay {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.4);
      z-index: 150;
    }

    .overlay.show {
      display: block;
    }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 768px) {
      .sidebar {
        position: fixed;
        top: 0;
        left: 0;
        height: 100%;
        transform: translateX(-100%);
      }

      .sidebar.show {
        transform: translateX(0);
      }

      .toggle-btn {
        display: block;
      }
    }
  </style>
</head>
<body>

  <div class="sidebar" id="sidebar">
    <div class="logo"><img src="../../assets/img/icon-admin-cake.png" alt="Gambar"> Rahmat Kue</div>
    <div class="menu">
      <a href="#" class="active">Dashboard</a>
      <a href="#">Tambah Produk</a>
      <a href="#">Pesanan</a>
      <a href="#">Kelola Voucher</a>
      <a href="#">Edit Profil User</a>
    </div>
  </div>

  <div class="overlay" id="overlay"></div>

  <div class="main-content">
    <div class="header">
      <button class="toggle-btn" id="toggleBtn">☰</button>
      <h2>Dashboard</h2>
      <div class="profile">
        <img src="https://i.pravatar.cc/30" alt="user" />
        <div>
          <strong>Raffin</strong><br />
          <small>Admin</small>
        </div>
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
  </script>

</body>
</html>
