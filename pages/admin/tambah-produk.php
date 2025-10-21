<?php
include '../../auth/koneksi.php';

$id_kategori = 1; // ubah sesuai kebutuhan

$query = mysqli_query($conn, "SELECT nama_kategori FROM kategori WHERE id_kategori = '$id_kategori'");
$row = mysqli_fetch_assoc($query);


if (isset($_POST['tambah'])) {
  $nama_produk = $_POST['nama_produk'];
  $deskripsi = $_POST['deskripsi'];
  $harga = $_POST['harga'];
  $id_kategori = $_POST['id_kategori'];

  // Upload gambar
  $namaFile = basename($_FILES['foto_produk']['name']);
  $tmpName = $_FILES['foto_produk']['tmp_name'];
  $folderTujuan = "uploads/";

  // Pastikan folder ada
  if (!is_dir($folderTujuan)) {
    mkdir($folderTujuan, 0777, true);
  }

  // Pindahkan file ke folder tujuan
  $pathFile = $folderTujuan . $namaFile;
  if (move_uploaded_file($tmpName, $pathFile)) {
    // Query insert - hanya simpan nama file ke database
    $query = "INSERT INTO produk (nama_produk, deskripsi, harga, foto_produk, id_kategori) 
              VALUES ('$nama_produk', '$deskripsi', '$harga', '$namaFile', '$id_kategori')";

    if (mysqli_query($conn, $query)) {
      echo "<script>alert('Produk berhasil ditambahkan!'); window.location='tambah-produk.php';</script>";
    } else {
      echo "Error: " . mysqli_error($conn);
    }
  } else {
    echo "<script>alert('Gagal mengupload gambar!');</script>";
  }
}

if (isset($_POST['tambah_kategori'])) {
  $nama_kategori = $_POST['nama_kategori'];

  if (!empty($nama_kategori)) {
    $query = "INSERT INTO kategori (nama_kategori) VALUES ('$nama_kategori')";
    if (mysqli_query($conn, $query)) {
      echo "<script>alert('Kategori berhasil ditambahkan!'); window.location='tambah-produk.php';</script>";
    } else {
      echo "Error: " . mysqli_error($conn);
    }
  } else {
    echo "<script>alert('Nama kategori tidak boleh kosong!');</script>";
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tambah Produk</title>
  <link rel="stylesheet" href="../../assets/css/dashboard-admin.css">
  <link rel="stylesheet" href="../../assets/css/tambah-produk.css">
</head>
<body>
  <?php include '../../component/sidebar.php'; ?>

  <div class="main-content">
    <div class="header">
      <button class="toggle-btn" id="toggleBtn">☰</button>
      <h2>Tambah Produk</h2>
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

    <!-- Form Tambah Produk -->
    <form method="POST" enctype="multipart/form-data">
      <div class="left-column">
        <label>Nama Produk</label>
        <input type="text" name="nama_produk" required>

        <label>Harga Produk</label>
        <input type="text" name="harga" required>

        <label>Gambar Produk</label>
        <input type="file" name="foto_produk" accept="image/*" required>
      </div>

      <div class="right-column">
        <label>Deskripsi</label>
        <textarea name="deskripsi" required></textarea>

        <div class="form-bottom">
          <div style="flex:1;">
            <label>Kategori Produk</label>
            <select name="id_kategori" required>
              <option value="">-- Pilih Kategori --</option>
              <?php
              $kategori = mysqli_query($conn, "SELECT * FROM kategori");
              while ($row = mysqli_fetch_assoc($kategori)) {
                echo "<option value='" . $row['id_kategori'] . "'>" . $row['nama_kategori'] . "</option>";
              }
              ?>
            </select>
          </div>
          <button class="btn-tambah-kategori" id="openModalBtn">+ Tambah Kategori</button>
          <button type="submit" name="tambah" class="btn">Tambah</button>
        </div>
      </div>
    </form>
    <br><br>

    <!-- Daftar Produk -->

    <?php 
      while ($row = mysqli_fetch_assoc($query)) {
        $nama_kategori = $row['nama_kategori'];
      }
    ?>

    <div class="produk-list">
      <?php
      $query = mysqli_query($conn, "SELECT produk.*, kategori.nama_kategori FROM produk JOIN kategori ON produk.id_kategori = kategori.id_kategori");

      while ($row = mysqli_fetch_assoc($query)) {
        echo "
        <div class='produk-card'>
          <img src='../../pages/admin/uploads/" . $row['foto_produk'] . "' alt='" . htmlspecialchars($row['nama_produk']) . "'>
          <div class='produk-info'>
            <h3>" . htmlspecialchars($row['nama_produk']) . "</h3>
            <p>" . htmlspecialchars($row['deskripsi']) . "</p>
            <p class='harga'>Rp " . number_format($row['harga'], 0, ',', '.') . "</p>
            <span class='kategori'>" . htmlspecialchars($row['nama_kategori']) . "</span>
          </div>
        </div>
        ";
      }
      ?>
    </div>
  </div>

  <!-- Popup Modal -->
  <div class="modal" id="kategoriModal">
    <div class="modal-content">
      <span class="close" id="closeModalBtn">&times;</span>
      <h3>Tambah Kategori Baru</h3>
      <form method="POST">
        <label>Nama Kategori</label>
        <input type="text" name="nama_kategori" placeholder="Masukkan nama kategori" required>
        <button type="submit" name="tambah_kategori" class="btn">Simpan</button>
      </form>
    </div>
  </div>
  
  <script>
    const modal = document.getElementById('kategoriModal');
    const openBtn = document.getElementById('openModalBtn');
    const closeBtn = document.getElementById('closeModalBtn');

    openBtn.addEventListener('click', () => {
      modal.style.display = 'block';
    });

    closeBtn.addEventListener('click', () => {
      modal.style.display = 'none';
    });

    window.addEventListener('click', (e) => {
      if (e.target === modal) {
        modal.style.display = 'none';
      }
    });
  </script>

</body>

</html>