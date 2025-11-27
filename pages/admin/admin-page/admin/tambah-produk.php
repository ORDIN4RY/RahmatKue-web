<?php
include '../../../../auth/koneksi.php'; // harus menyediakan fungsi: getSupabaseData(), insertSupabaseData(), uploadToSupabaseStorage()

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
  $minimal_pembelian = trim($_POST['minimal_pembelian']);

  if ($nama_kategori === '') {
    echo "<script>alert('Nama kategori tidak boleh kosong!');</script>";
  } else {

    $data = [
      'nama_kategori' => $nama_kategori,
      'minimal_pembelian' => $minimal_pembelian
    ];

    $insertCat = insertSupabaseData('kategori', $data);

    if ($insertCat) {
      echo "<script>alert('Kategori berhasil ditambahkan!'); window.location='tambah-produk.php';</script>";
      exit;
    } else {
      echo "<script>alert('Gagal menambahkan kategori.');</script>";
    }
  }
}


if (isset($_GET['hapus'])) {
  $id_produk = $_GET['hapus'];

  $result = deleteProdukDenganRelasi($id_produk);

  if ($result['success']) {
    echo "<script>alert('Produk berhasil dihapus!'); window.location='tambah-produk.php';</script>";
    exit;
  } else {
    echo "<script>alert('Gagal menghapus produk: {$result['message']}'); console.log(`Debug: {$result['debug']}`);</script>";
  }
}


// === UPDATE PRODUK ===
if (isset($_POST['update_produk'])) {
  $id_produk  = $_POST['id_produk'];
  $nama_produk = trim($_POST['nama_produk']);
  $deskripsi   = trim($_POST['deskripsi']);
  $harga       = (int) $_POST['harga'];
  $id_kategori = $_POST['id_kategori'];

  // Data dasar
  $updateData = [
    'nama_produk' => $nama_produk,
    'deskripsi'   => $deskripsi,
    'harga'       => $harga,
    'id_kategori' => $id_kategori
  ];

  // Jika upload file baru
  if (isset($_FILES['foto_produk']) && $_FILES['foto_produk']['error'] === UPLOAD_ERR_OK) {

    // --- HANDLE FILE BARU ---
    $tmpName  = $_FILES['foto_produk']['tmp_name'];
    $oriName  = basename($_FILES['foto_produk']['name']);
    $ext      = strtolower(pathinfo($oriName, PATHINFO_EXTENSION));

    $allowed  = ['jpg', 'jpeg', 'png', 'webp'];
    if (!in_array($ext, $allowed)) {
      echo "<script>alert('Format gambar tidak didukung');</script>";
      exit;
    }

    $namaFile = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $oriName);

    // --- PATH LOKAL HARUS ABSOLUT ---
    $folder = __DIR__ . '/uploads/';
    if (!is_dir($folder)) mkdir($folder, 0777, true);

    $localPath = $folder . $namaFile;

    // SIMPAN SEMENTARA DI LOKAL
    if (!move_uploaded_file($tmpName, $localPath)) {
      echo "<script>alert('Gagal memindahkan file lokal');</script>";
      exit;
    }

    // --- UPLOAD KE SUPABASE ---
    $upload = uploadToSupabaseStorage('images', $localPath, 'produk/' . $namaFile);


    if ($upload === false) {
      echo "<script>alert('Gagal upload ke Supabase');</script>";
      exit;
    }

    // Hapus file lokal setelah sukses
    if (file_exists($localPath)) unlink($localPath);

    // simpan URL untuk database
    $updateData['foto_produk'] =
      "https://fsiuefdwcbdhunfhbiwl.supabase.co/storage/v1/object/public/images/produk/" . $namaFile;
  }

  // --- PROSES UPDATE KE DATABASE ---
  $upload = uploadToSupabaseStorage(
    'images',
    $localPath,
    'produk/' . $namaFile
  );


  if ($res) {
    echo "<script>alert('Produk berhasil diperbarui'); window.location='tambah-produk.php';</script>";
    exit;
  } else {
    echo "<script>alert('Gagal update produk');</script>";
  }
}




?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Kelola Produk - Rahmat Kue</title>
  <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
  <link href="../css/sb-admin-2.min.css" rel="stylesheet">
  <style>
    .product-card {
      transition: transform 0.2s, box-shadow 0.2s;
      height: 100%;
    }

    .product-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
    }

    .product-image {
      height: 200px;
      object-fit: cover;
      width: 100%;
      border-radius: 8px 8px 0 0;
    }

    .badge-price {
      font-size: 1.1rem;
      padding: 8px 12px;
    }

    .search-section {
      padding: 2rem;
      border-radius: 10px;
      margin-bottom: 2rem;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .action-buttons {
      display: flex;
      gap: 10px;
      width: auto;
      height: 45px;
    }

    .action-buttons p {
      display: flex;
      gap: 10px;
      font-size: 15px;
    }

    .btn-action {
      flex: 1;
    }
  </style>
</head>

<body id="page-top">

  <!-- Page Wrapper -->
  <div id="wrapper">
    <!-- Sidebar -->
    <?php include '../../../../component/sidebar.php'; ?>
    <!-- End of Sidebar -->

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">
      <!-- Main Content -->
      <div id="content">
        <!-- Topbar -->
        <?php include '../../../../component/topbar.php'; ?>
        <!-- End of Topbar -->

        <!-- Begin Page Content -->
        <div class="container-fluid">

          <!-- Search and Action Section -->
          <div class="search-section">
            <div class="row g-3 align-items-end">
              <!-- Search Bar -->
              <div class="col-md-6">
                <form method="GET" action="">
                  <div class="input-group">
                    <input type="text" class="form-control form-control-lg" name="search"
                      placeholder="Cari berdasarkan nama produk atau kategori..."
                      value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                    <button class="btn btn-dark" type="submit">
                      <i class="fas fa-search"></i> Cari
                    </button>
                    <?php if (isset($_GET['search']) && $_GET['search'] != ''): ?>
                      <a href="?" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Reset
                      </a>
                    <?php endif; ?>
                  </div>
                </form>
              </div>

              <!-- Action Buttons -->
              <div class="col-md-6">
                <div class="action-buttons">
                  <button type="button" class="btn btn-success btn-lg btn-action" data-bs-toggle="modal" data-bs-target="#modalTambahProduk">
                    <p>Tambah Produk</p>
                  </button>
                  <button type="button" class="btn btn-warning btn-lg btn-action" data-bs-toggle="modal" data-bs-target="#modalTambahKategori">
                    <p>Tambah Kategori</p>
                  </button>
                  <button type="button" class="btn btn-primary btn-lg btn-action" data-bs-toggle="modal" data-bs-target="#modalTambahPaket">
                    <p>Tambah Paket</p>
                  </button>
                </div>
              </div>
            </div>
          </div>

          <?php
          // di bagian atas body setelah session_start() dan sebelum output penting
          if (isset($_GET['deleted'])) {
            if ($_GET['deleted'] == '1') {
              echo '<div class="alert alert-success">Produk berhasil dihapus!</div>';
            } else {
              echo '<div class="alert alert-danger">Gagal menghapus produk.</div>';
              if (!empty($_SESSION['delete_error'])) {
                echo '<div class="small text-muted">Debug: ' . htmlspecialchars($_SESSION['delete_error']) . '</div>';
                unset($_SESSION['delete_error']);
              }
            }
          }
          ?>


          <!-- Statistics Cards -->
          <div class="row mb-4">
            <div class="col-xl-4 col-md-6 mb-4">
              <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                  <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Produk</div>
                      <div class="h5 mb-0 font-weight-bold text-gray-800">
                        <?php echo !empty($data) ? count($data) : 0; ?>
                      </div>
                    </div>
                    <div class="col-auto">
                      <i class="fas fa-box fa-2x text-gray-300"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-xl-4 col-md-6 mb-4">
              <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                  <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Kategori</div>
                      <div class="h5 mb-0 font-weight-bold text-gray-800">
                        <?php echo !empty($kategori) ? count($kategori) : 0; ?>
                      </div>
                    </div>
                    <div class="col-auto">
                      <i class="fas fa-tags fa-2x text-gray-300"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-xl-4 col-md-6 mb-4">
              <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                  <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Produk Terbaru</div>
                      <div class="h5 mb-0 font-weight-bold text-gray-800">Hari Ini</div>
                    </div>
                    <div class="col-auto">
                      <i class="fas fa-clock fa-2x text-gray-300"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Daftar Produk -->
          <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
              <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list"></i> Daftar Produk
              </h6>
              <span class="badge bg-primary">
                <?= !empty($data) ? count($data) : 0; ?> Produk
              </span>
            </div>

            <div class="card-body">
              <?php if (!empty($data)): ?>
                <div class="row">

                  <?php foreach ($data as $row):
                    $foto = htmlspecialchars($row['foto_produk'] ?? '');
                    if ($foto !== '') {
                      $imgUrl = filter_var($foto, FILTER_VALIDATE_URL)
                        ? $foto
                        : SUPABASE_STORAGE_URL . '/images/produk/' . rawurlencode($foto);
                    } else {
                      $imgUrl = '../../assets/img/no-image.png';
                    }

                    // Filter search
                    $search = isset($_GET['search']) ? strtolower($_GET['search']) : '';
                    if ($search != '') {
                      $nama_produk = strtolower($row['nama_produk'] ?? '');
                      $kategori_nama = strtolower($row['kategori']['nama_kategori'] ?? '');
                      if (strpos($nama_produk, $search) === false && strpos($kategori_nama, $search) === false) {
                        continue;
                      }
                    }
                  ?>

                    <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                      <div class="card product-card shadow-sm">

                        <img src="<?= $imgUrl ?>" class="product-image"
                          alt="<?= htmlspecialchars($row['nama_produk']) ?>"
                          loading="lazy"
                          onerror="this.src='../../assets/img/no-image.png'">

                        <div class="card-body">
                          <h5 class="card-title text-truncate"
                            title="<?= htmlspecialchars($row['nama_produk']) ?>">
                            <?= htmlspecialchars($row['nama_produk']) ?>
                          </h5>

                          <p class="card-text text-muted small"
                            style="height: 60px; overflow: hidden;">
                            <?= htmlspecialchars($row['deskripsi']) ?>
                          </p>

                          <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge bg-info text-white">
                              <i class="fas fa-tag"></i>
                              <?= htmlspecialchars($row['kategori']['nama_kategori'] ?? 'Tanpa Kategori') ?>
                            </span>
                          </div>

                          <div class="d-flex justify-content-between align-items-center">
                            <span class="badge badge-price bg-success text-white">
                              Rp <?= number_format($row['harga'], 0, ',', '.') ?>
                            </span>
                          </div>
                        </div>

                        <div class="card-footer bg-transparent border-top-0">
                          <div class="d-grid gap-2">

                            <!-- BUTTON EDIT -->
                            <button class="btn btn-sm btn-primary"
                              data-bs-toggle="modal"
                              data-bs-target="#modalEditProduk<?= $row['id_produk'] ?>">
                              <i class="fas fa-edit"></i> Edit
                            </button>

                            <a href="tambah-produk.php?hapus=<?= urlencode($row['id_produk']) ?>"
                              class="btn btn-sm btn-danger"
                              onclick="return confirm('Yakin ingin menghapus produk ini?');">
                              <i class="fas fa-trash"></i> Hapus
                            </a>

                          </div>
                        </div>

                      </div>
                    </div>


                    <!-- ========================================================= -->
                    <!-- MODAL EDIT PRODUK — DITARUH DI DALAM FOREACH -->
                    <!-- ========================================================= -->
                    <div class="modal fade" id="modalEditProduk<?= $row['id_produk'] ?>" tabindex="-1">
                      <div class="modal-dialog modal-lg">
                        <div class="modal-content">

                          <div class="modal-header bg-success text-white">
                            <h5 class="modal-title">
                              <i class="fas fa-edit"></i> Edit Produk
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                          </div>

                          <form method="POST" enctype="multipart/form-data">
                            <div class="modal-body">

                              <input type="hidden" name="id_produk" value="<?= $row['id_produk'] ?>">

                              <div class="row">
                                <div class="col-md-6">
                                  <div class="mb-3">
                                    <label class="form-label">Nama Produk</label>
                                    <input type="text" name="nama_produk" value="<?= htmlspecialchars($row['nama_produk']) ?>" class="form-control" required>
                                  </div>

                                  <div class="mb-3">
                                    <label class="form-label">Harga Produk</label>
                                    <input type="number" name="harga" value="<?= $row['harga'] ?>" class="form-control" required>
                                  </div>

                                  <div class="mb-3">
                                    <label class="form-label">Foto Produk</label><br>
                                    <img src="<?= $imgUrl ?>" class="mb-2" style="max-height:120px;">
                                    <input type="file" name="foto_produk" class="form-control">
                                  </div>
                                </div>

                                <div class="col-md-6">
                                  <div class="mb-3">
                                    <label class="form-label">Deskripsi Produk</label>
                                    <textarea name="deskripsi" class="form-control" rows="5" required><?= htmlspecialchars($row['deskripsi']) ?></textarea>
                                  </div>

                                  <div class="mb-3">
                                    <label class="form-label">Kategori Produk</label>
                                    <select name="id_kategori" class="form-select" required>
                                      <?php foreach ($kategori as $kat): ?>
                                        <option value="<?= $kat['id_kategori'] ?>"
                                          <?= ($kat['id_kategori'] == $row['id_kategori']) ? 'selected' : '' ?>>
                                          <?= htmlspecialchars($kat['nama_kategori']) ?>
                                        </option>
                                      <?php endforeach; ?>
                                    </select>
                                  </div>

                                </div>
                              </div>

                            </div>

                            <div class="modal-footer">
                              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                Batal
                              </button>
                              <button type="submit" name="update_produk" class="btn btn-success">
                                <i class="fas fa-save"></i> Simpan Perubahan
                              </button>
                            </div>
                          </form>

                        </div>
                      </div>
                    </div>
                    <!-- ========================================================= -->

                  <?php endforeach; ?>

                </div>

              <?php else: ?>

                <div class="text-center py-5">
                  <i class="fas fa-box-open fa-5x text-muted mb-3"></i>
                  <h4 class="text-muted">Belum Ada Produk</h4>
                  <p class="text-muted">Klik tombol "Tambah Produk" untuk menambahkan produk baru</p>
                </div>

              <?php endif; ?>

            </div>
          </div>


        </div>
        <!-- /.container-fluid -->

      </div>
      <!-- End of Main Content -->

      <!-- Footer -->
      <footer class="sticky-footer bg-white">
        <div class="container my-auto">
          <div class="copyright text-center my-auto">
            <span>Copyright &copy; Rahmat Kue 2024</span>
          </div>
        </div>
      </footer>
      <!-- End of Footer -->

    </div>
    <!-- End of Content Wrapper -->

  </div>
  <!-- End of Page Wrapper -->

  <!-- Modal Tambah Produk -->
  <div class="modal fade" id="modalTambahProduk" tabindex="-1" aria-labelledby="modalTambahProdukLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header bg-success text-white">
          <h5 class="modal-title" id="modalTambahProdukLabel">
            <i class="fas fa-plus-circle"></i> Tambah Produk Baru
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form method="POST" enctype="multipart/form-data">
          <div class="modal-body">
            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="nama_produk" class="form-label">
                    <i class="fas fa-box"></i> Nama Produk <span class="text-danger">*</span>
                  </label>
                  <input type="text" class="form-control" id="nama_produk" name="nama_produk" required>
                </div>

                <div class="mb-3">
                  <label for="harga" class="form-label">
                    <i class="fas fa-money-bill-wave"></i> Harga Produk <span class="text-danger">*</span>
                  </label>
                  <div class="input-group">
                    <span class="input-group-text">Rp</span>
                    <input type="number" class="form-control" id="harga" name="harga" required>
                  </div>
                </div>

                <div class="mb-3">
                  <label for="foto_produk" class="form-label">
                    <i class="fas fa-image"></i> Gambar Produk <span class="text-danger">*</span>
                  </label>
                  <input type="file" class="form-control" id="foto_produk" name="foto_produk" accept="image/*" required>
                  <small class="text-muted">Format: JPG, PNG, JPEG (Max 2MB)</small>
                </div>
              </div>

              <div class="col-md-6">
                <div class="mb-3">
                  <label for="deskripsi" class="form-label">
                    <i class="fas fa-align-left"></i> Deskripsi Produk <span class="text-danger">*</span>
                  </label>
                  <textarea class="form-control" id="deskripsi" name="deskripsi" rows="5" required></textarea>
                </div>

                <div class="mb-3">
                  <label for="id_kategori" class="form-label">
                    <i class="fas fa-tags"></i> Kategori Produk <span class="text-danger">*</span>
                  </label>
                  <select class="form-select" id="id_kategori" name="id_kategori" required>
                    <option value="">-- Pilih Kategori --</option>
                    <?php if (!empty($kategori)): ?>
                      <?php foreach ($kategori as $kat): ?>
                        <option value="<?= htmlspecialchars($kat['id_kategori']) ?>">
                          <?= htmlspecialchars($kat['nama_kategori']) ?>
                        </option>
                      <?php endforeach; ?>
                    <?php endif; ?>
                  </select>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
              <i class="fas fa-times"></i> Batal
            </button>
            <button type="submit" name="tambah" class="btn btn-success">
              <i class="fas fa-save"></i> Simpan Produk
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="modal fade" id="modalTambahPaket" tabindex="-1" aria-labelledby="modalTambahProdukLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header bg-success text-white">
          <h5 class="modal-title" id="modalTambahProdukLabel">
            <i class="fas fa-plus-circle"></i> Tambah Paket Baru
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form method="POST" enctype="multipart/form-data">
          <div class="modal-body">
            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="nama_produk" class="form-label">
                    <i class="fas fa-box"></i> Nama Produk <span class="text-danger">*</span>
                  </label>
                  <input type="text" class="form-control" id="nama_produk" name="nama_produk" required>
                </div>

                <div class="mb-3">
                  <label for="harga" class="form-label">
                    <i class="fas fa-money-bill-wave"></i> Harga Produk <span class="text-danger">*</span>
                  </label>
                  <div class="input-group">
                    <span class="input-group-text">Rp</span>
                    <input type="number" class="form-control" id="harga" name="harga" required>
                  </div>
                </div>

                <div class="mb-3">
                  <label for="foto_produk" class="form-label">
                    <i class="fas fa-image"></i> Gambar Produk <span class="text-danger">*</span>
                  </label>
                  <input type="file" class="form-control" id="foto_produk" name="foto_produk" accept="image/*" required>
                  <small class="text-muted">Format: JPG, PNG, JPEG (Max 2MB)</small>
                </div>
              </div>

              <div class="col-md-6">
                <div class="mb-3">
                  <label for="deskripsi" class="form-label">
                    <i class="fas fa-align-left"></i> Deskripsi Produk <span class="text-danger">*</span>
                  </label>
                  <textarea class="form-control" id="deskripsi" name="deskripsi" rows="5" required></textarea>
                </div>

                <div class="mb-3">
                  <label for="id_kategori" class="form-label">
                    <i class="fas fa-tags"></i> Kategori Produk <span class="text-danger">*</span>
                  </label>
                  <select class="form-select" id="id_kategori" name="id_kategori" required>
                    <option value="">-- Pilih Kategori --</option>
                    <?php if (!empty($kategori)): ?>
                      <?php foreach ($kategori as $kat): ?>
                        <option value="<?= htmlspecialchars($kat['id_kategori']) ?>">
                          <?= htmlspecialchars($kat['nama_kategori']) ?>
                        </option>
                      <?php endforeach; ?>
                    <?php endif; ?>
                  </select>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
              <i class="fas fa-times"></i> Batal
            </button>
            <button type="submit" name="tambah" class="btn btn-success">
              <i class="fas fa-save"></i> Simpan Produk
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="modal fade" id="modalEditProduk<?= $row['id_produk'] ?>" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">

        <div class="modal-header bg-success text-white">
          <h5 class="modal-title">Edit Produk</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>

        <form method="POST" enctype="multipart/form-data">

          <div class="modal-body">

            <input type="hidden" name="id_produk" value="<?= $row['id_produk'] ?>">

            <div class="mb-3">
              <label class="form-label">Nama Produk</label>
              <input type="text" class="form-control" name="nama_produk"
                value="<?= htmlspecialchars($row['nama_produk']) ?>" required>
            </div>

            <div class="mb-3">
              <label class="form-label">Harga</label>
              <input type="number" class="form-control" name="harga"
                value="<?= $row['harga'] ?>" required>
            </div>

            <div class="mb-3">
              <label class="form-label">Deskripsi</label>
              <textarea class="form-control" name="deskripsi" rows="4" required><?= htmlspecialchars($row['deskripsi']) ?></textarea>
            </div>

            <div class="mb-3">
              <label class="form-label">Kategori</label>
              <select class="form-select" name="id_kategori" required>
                <option value="">-- Pilih Kategori --</option>

                <?php foreach ($kategori as $kat): ?>
                  <option value="<?= $kat['id_kategori'] ?>"
                    <?= $kat['id_kategori'] == $row['id_kategori'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($kat['nama_kategori']) ?>
                  </option>
                <?php endforeach; ?>

              </select>
            </div>

            <div class="mb-3">
              <label class="form-label">Gambar Produk</label><br>
              <img src="<?= $imgUrl ?>" style="max-height:120px;" class="mb-2">
              <input type="file" class="form-control" name="foto_produk" accept="image/*">
            </div>

          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" name="update_produk" class="btn btn-success">Simpan Perubahan</button>
          </div>

        </form>
      </div>
    </div>
  </div>


  <!-- Modal Tambah Kategori -->
  <div class="modal fade" id="modalTambahKategori" tabindex="-1" aria-labelledby="modalTambahKategoriLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header bg-warning text-dark">
          <h5 class="modal-title" id="modalTambahKategoriLabel">
            <i class="fas fa-tags"></i> Tambah Kategori Baru
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form method="POST" action="">
          <div class="modal-body">
            <div class="mb-3">
              <label for="nama_kategori" class="form-label">
                <i class="fas fa-tag"></i> Nama Kategori <span class="text-danger">*</span>
              </label>
              <input type="text" class="form-control" id="nama_kategori" name="nama_kategori" placeholder="Contoh: Kue Kering" required>
            </div>
            <div class="mb-3">
              <label for="minimal_pembelian" class="form-label">
                <i class="fas fa-tag"></i> Minimal Pembelian <span class="text-danger">*</span>
              </label>
              <input type="number" class="form-control" id="minimal_pembelian" name="minimal_pembelian" placeholder="" required>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
              <i class="fas fa-times"></i> Batal
            </button>
            <button type="submit" name="tambah_kategori" class="btn btn-warning">
              <i class="fas fa-save"></i> Simpan Kategori
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Scroll to Top Button-->
  <a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
  </a>

  <!-- Scripts -->

  <!-- 
  <script src="vendor/jquery/jquery.min.js"></script>
  <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script> -->

  <!-- Core plugin JavaScript-->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="../vendor/jquery-easing/jquery.easing.min.js"></script>

  <!-- Custom scripts for all pages-->
  <script src="js/sb-admin-2.min.js"></script>

</body>

</html>