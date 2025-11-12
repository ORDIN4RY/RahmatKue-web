<?php
include '../../../auth/koneksi.php'; // harus menyediakan fungsi: getSupabaseData(), insertSupabaseData(), uploadToSupabaseStorage()

// === Ambil data kategori & produk dari Supabase ===
$kategori = getSupabaseData('kategori'); // array kategori
$data = getSupabaseData('produk?select=*,kategori(nama_kategori)'); // join kategori

// === TAMBAH PRODUK ===
if (isset($_POST['tambah'])) {
  $nama_produk = trim($_POST['nama_produk']);
  $deskripsi   = trim($_POST['deskripsi']);
  $harga       = (int) $_POST['harga'];
  $id_kategori = $_POST['id_kategori'];

  if ($nama_produk === '' || $deskripsi === '' || $harga <= 0 || empty($id_kategori)) {
    echo "<script>alert('Semua field wajib diisi!');</script>";
  } else {
    // === Upload gambar produk ===
    if (!isset($_FILES['foto_produk']) || $_FILES['foto_produk']['error'] !== UPLOAD_ERR_OK) {
      echo "<script>alert('Silakan pilih gambar produk.')</script>";
    } else {
      $tmpName  = $_FILES['foto_produk']['tmp_name'];
      $oriName  = basename($_FILES['foto_produk']['name']);
      $ext      = strtolower(pathinfo($oriName, PATHINFO_EXTENSION));
      $allowed  = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
      if (!in_array($ext, $allowed)) {
        echo "<script>alert('File gambar harus jpg, jpeg, png, gif, atau webp!');</script>";
      } else {
        $namaFile = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $oriName);
        $folderTujuan = __DIR__ . '/uploads/';
        if (!is_dir($folderTujuan)) mkdir($folderTujuan, 0777, true);
        $localPath = $folderTujuan . $namaFile;
        if (!move_uploaded_file($tmpName, $localPath)) {
          echo "<script>alert('Gagal upload file ke server.');</script>";
        } else {
          // Upload ke Supabase Storage (bucket: images)
          $uploadRes = uploadToSupabaseStorage('images', $localPath, 'produk/' . $namaFile);
          // (opsional) hapus file lokal setelah upload
          if (file_exists($localPath)) @unlink($localPath);
          if ($uploadRes === false) {
            echo "<script>alert('Gagal upload gambar ke Supabase Storage.');</script>";
          } else {
            // Simpan hanya nama file ke database
            $newProduct = [
              'nama_produk' => $nama_produk,
              'deskripsi'   => $deskripsi,
              'harga'       => $harga,
              'id_kategori' => $id_kategori,
              'foto_produk' => "https://fsiuefdwcbdhunfhbiwl.supabase.co/storage/v1/object/public/images/produk/" . $namaFile // hanya nama file
            ];
            $insertRes = insertSupabaseData('produk', $newProduct);
            if ($insertRes) {
              echo "<script>alert('Produk berhasil ditambahkan!'); window.location='tambah-produk.php';</script>";
              exit;
            } else {
              echo "<script>alert('Gagal menyimpan produk ke Supabase.');</script>";
            }
          }
        }
      }
    }
  }
}

// === TAMBAH KATEGORI ===
if (isset($_POST['tambah_kategori'])) {
  $nama_kategori = trim($_POST['nama_kategori']);
  if ($nama_kategori === '') {
    echo "<script>alert('Nama kategori tidak boleh kosong!');</script>";
  } else {
    $insertCat = insertSupabaseData('kategori', ['nama_kategori' => $nama_kategori]);
    if ($insertCat) {
      echo "<script>alert('Kategori berhasil ditambahkan!'); window.location='tambah-produk.php';</script>";
      exit;
    } else {
      echo "<script>alert('Gagal menambahkan kategori.');</script>";
    }
  }
}

// === HAPUS PRODUK ===
if (isset($_GET['hapus'])) {
  $id_produk = $_GET['hapus'];

  if (deleteSupabaseData('produk', 'id_produk', $id_produk)) {
    echo "<script>alert('Produk berhasil dihapus!'); window.location='tambah-produk.php';</script>";
    exit;
  } else {
    echo "<script>alert('Gagal menghapus produk.');</script>";
  }
}
?>



<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Tambah Produk - Rahmat Kue</title>
  <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
  <link href="css/sb-admin-2.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../../../assets/css/tambah-produk.css">
</head>

<body id="page-top">

  <!-- Page Wrapper -->
  <div id="wrapper">
    <!-- Sidebar -->

    <?php include '../../../component/sidebar.php'; ?>
    <!-- End of Sidebar -->

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">
      <!-- Main Content -->

      <div id="content">
        <!-- Topbar -->
        <?php include '../../../component/topbar.php'; ?>
        <!-- End of Topbar -->

        <!-- Begin Page Content -->
        <div class="container-fluid">

          <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
            Tambah Produk Baru
          </button>
          
          <!-- Modal tambah kategori -->
          <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" data-bs-backdrop="false" data-bs-keyboard="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLabel">Tambah Produk</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <form>
                    <div class="mb-3">
                      <label for="recipient-name" class="col-form-label">Nama Produk:</label>
                      <input type="text" class="form-control" id="recipient-name">
                    </div>
                    <div class="mb-3">
                      <label for="message-text" class="col-form-label">Harga Produk:</label>
                      <input type="number" class="form-control" id="message-text"></input>
                    </div>
                    <div class="mb-3">
                      <label for="message-text" class="col-form-label">Harga Produk:</label>
                      <input type="file" class="form-control" id="message-text"></input>
                    </div>
                    <div class="mb-3">
                      <label for="message-text" class="col-form-label">Deskripsi Produk:</label>
                      <textarea type="file" class="form-control" id="message-text"></textarea>
                    </div>
                    <div class="mb-3">
                      <select class="form-select" id="inputGroupSelect01">
                        <option selected>Choose...</option>
                        <option value="1">One</option>
                        <option value="2">Two</option>
                        <option value="3">Three</option>
                      </select>
                    </div>
                    <div class="mb-3">
                      <button class="btn btn-primary">Tambah Produk</button>
                    </div>
                  </form>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                  <button type="button" class="btn btn-primary">Save changes</button>
                </div>
              </div>
            </div>
          </div>
          <!-- FORM DAN DAFTAR PRODUK -->

          <form method="POST" enctype="multipart/form-data">
            <div class="left-column">
              <label>Nama Produk</label>
              <input type="text" name="nama_produk" required>

              <label>Harga Produk</label>
              <input type="number" class="harga" name="harga" required>

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
                    <?php foreach ($kategori as $kat): ?>
                      <option value="<?= htmlspecialchars($kat['id_kategori']) ?>"><?= htmlspecialchars($kat['nama_kategori']) ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>

                <button type="button" class="btn-tambah-kategori" id="openModalBtn">+ Tambah Kategori</button>
                <button type="submit" name="tambah" class="btn">Tambah</button>
              </div>
            </div>
          </form>

          <br><br>

          <!-- Daftar Produk -->
          <div class="produk-list">
            <?php if (!empty($data)): ?>
              <?php foreach ($data as $row):
                $foto = htmlspecialchars($row['foto_produk'] ?? '');
                if ($foto !== '') {
                  if (filter_var($foto, FILTER_VALIDATE_URL)) {
                    $imgUrl = $foto;
                  } else {
                    $imgUrl = SUPABASE_STORAGE_URL . '/images/produk/' . rawurlencode($foto);
                  }
                } else {
                  $imgUrl = '../../assets/img/no-image.png';
                }
              ?>

                <div class="produk-card mb-3">
                  <img src="<?= $imgUrl ?>" alt="<?= htmlspecialchars($row['nama_produk'] ?? '') ?>" loading="lazy" onerror="this.src='../../assets/img/no-image.png'">
                  <div class="produk-info">
                    <h3><?= htmlspecialchars($row['nama_produk'] ?? '') ?></h3>
                    <p><?= htmlspecialchars($row['deskripsi'] ?? '') ?></p>
                    <p class="harga">Rp <?= isset($row['harga']) ? number_format($row['harga'], 0, ',', '.') : '-' ?></p>
                    <span class="kategori"><?= htmlspecialchars($row['kategori']['nama_kategori'] ?? 'Tanpa Kategori') ?></span>
                    <a href="tambah-produk.php?hapus=<?= htmlspecialchars($row['id_produk'] ?? '') ?>" class="btn-hapus" onclick="return confirm('Yakin ingin menghapus produk ini?');">Hapus</a>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <p>Tidak ada produk tersedia saat ini.</p>
            <?php endif; ?>
          </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <script>
          document.getElementById('openModalBtn').addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('kategoriModal').style.display = 'block';
          });
          document.getElementById('closeModalBtn').addEventListener('click', function() {
            document.getElementById('kategoriModal').style.display = 'none';
          });
          window.addEventListener('click', function(e) {
            if (e.target === document.getElementById('kategoriModal')) document.getElementById('kategoriModal').style.display = 'none';
          });
        </script>
</body>

</html>