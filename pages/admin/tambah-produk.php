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
</head>
<style>
  .form-container {
    background-color: #fff9f6;
    border-radius: 10px;
    padding: 20px 25px;
    box-shadow: 0 0 5px rgba(180, 130, 90, 0.1);
    max-width: 900px;
    margin: auto;
  }

  form {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
  }

  label {
    display: block;
    font-size: 12px;
    color: #8b5e3c;
    margin-bottom: 4px;
  }

  input[type="text"],
  input[type="file"],
  textarea,
  select {
    width: 100%;
    padding: 8px;
    border: 1px solid #b67a58;
    border-radius: 4px;
    background-color: #fff;
    font-size: 13px;
    box-sizing: border-box;
    color: #5a3e2b;
  }

  textarea {
    resize: none;
    height: 118px;
  }

  .left-column,
  .right-column {
    display: flex;
    flex-direction: column;
    gap: 10px;
  }

  .form-bottom {
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .btn {
    background-color: #8b5e3c;
    color: white;
    border: none;
    margin-top: 18px;
    padding: 8px 18px;
    border-radius: 5px;
    font-size: 13px;
    cursor: pointer;
    transition: background 0.3s ease;
  }

  .btn:hover {
    background-color: #a36b46;
  }

  .produk-list {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-top: 30px;
  }

  .produk-card {
    background-color: #fff;
    border: 1px solid #d6bfae;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(150, 110, 90, 0.1);
    overflow: hidden;
    transition: 0.3s;
  }

  .produk-card:hover {
    transform: scale(1.02);
    box-shadow: 0 4px 12px rgba(150, 110, 90, 0.2);
  }

  .produk-card img {
    width: 100%;
    height: 180px;
    object-fit: cover;
  }

  .produk-info {
    padding: 15px;
  }

  .produk-info h3 {
    margin: 0;
    font-size: 18px;
    color: #5c3b2e;
  }

  .produk-info p {
    margin: 5px 0;
    color: #7a5540;
    font-size: 14px;
  }

  .harga {
    font-weight: bold;
    color: #b3744a;
    margin-top: 10px;
  }

  .kategori {
    font-size: 13px;
    background-color: #f4e1d2;
    display: inline-block;
    padding: 4px 10px;
    border-radius: 15px;
    margin-top: 5px;
  }


  /* Tombol tambah kategori */
  .btn-tambah-kategori {
    background-color: #b47b48;
    color: #fff;
    border: none;
    padding: 8px 16px;
    border-radius: 5px;
    cursor: pointer;
    transition: background 0.3s ease;
    margin-top: 19px;
  }

  .btn-tambah-kategori:hover {
    background-color: #8c5a33;
  }

  /* Modal Overlay */
  .modal {
    display: none;
    /* disembunyikan default */
    position: fixed;
    z-index: 999;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
  }

  /* Isi modal */
  .modal-content {
    background-color: #fff9f6;
    margin: 10% auto;
    padding: 20px 25px;
    border-radius: 10px;
    width: 400px;
    box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
    position: relative;
    text-align: left;
  }

  /* Tombol close (X) */
  .close {
    position: absolute;
    top: 10px;
    right: 15px;
    font-size: 24px;
    cursor: pointer;
    color: #a36b46;
  }

  /* Input di modal */
  .modal-content label {
    margin-top: 15px;
    font-size: 15px;
    color: #8b5e3c;
  }

  .modal-content input[type="text"] {
    width: 100%;
    padding: 8px;
    border: 1px solid #b67a58;
    border-radius: 5px;
    margin-bottom: 15px;
    font-size: 13px;
  }


  /* Responsive */
  @media (max-width: 768px) {
    form {
      grid-template-columns: 1fr;
    }

    .form-bottom {
      flex-direction: column;
      align-items: flex-start;
    }
  }
</style>

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


    <?php if ($row) : ?>
      <h2><?= htmlspecialchars($row['nama_kategori']); ?></h2>
    <?php else : ?>
      <h2>Kategori tidak ditemukan</h2>
    <?php endif; ?>

    <div class="produk-list">
      <?php
      $query = mysqli_query($conn, "SELECT produk.*, kategori.nama_kategori 
                                    FROM produk 
                                    JOIN kategori ON produk.id_kategori = kategori.id_kategori");

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