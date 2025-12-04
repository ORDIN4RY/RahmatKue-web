<?php
include '../../../../auth/koneksi.php'; // harus menyediakan fungsi: getSupabaseData(), insertSupabaseData(), uploadToSupabaseStorage()

// === Ambil data paket dari Supabase ===
$paket = getSupabaseData('paket'); // array paket

// === TAMBAH PAKET ===
if (isset($_POST['tambah'])) {
  $nama_paket  = trim($_POST['nama_paket']);
  $deskripsi   = trim($_POST['deskripsi']);
  $harga_paket = (int) $_POST['harga_paket'];

  if ($nama_paket === '' || $deskripsi === '' || $harga_paket <= 0) {
    echo "<script>alert('Semua field wajib diisi!');</script>";
  } else {
    // === Upload gambar paket ===
    if (!isset($_FILES['foto_paket']) || $_FILES['foto_paket']['error'] !== UPLOAD_ERR_OK) {
      echo "<script>alert('Silakan pilih gambar paket.')</script>";
    } else {
      $tmpName  = $_FILES['foto_paket']['tmp_name'];
      $oriName  = basename($_FILES['foto_paket']['name']);
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
          $uploadRes = uploadToSupabaseStorage('', $localPath, 'paket/'.$namaFile);

          // (opsional) hapus file lokal setelah upload
          if (file_exists($localPath)) @unlink($localPath);
          if ($uploadRes === false) {
            echo "<script>alert('Gagal upload gambar ke Supabase Storage.');</script>";
          } else {
            // Simpan hanya nama file ke database
            $newProduct = [
              'nama_paket'  => $nama_paket,
              'deskripsi'   => $deskripsi,
              'harga_paket' => $harga_paket,
              'foto_paket'  => "https://fsiuefdwcbdhunfhbiwl.supabase.co/storage/v1/object/public/images/paket/" . $namaFile // hanya nama file
            ];
            $insertRes = insertSupabaseData('paket', $newProduct);
            if ($insertRes) {
              echo "<script>alert('Paket berhasil ditambahkan!'); window.location='tambah-produk.php';</script>";
              exit;
            } else {
              echo "<script>alert('Gagal menyimpan paket ke Supabase.');</script>";
            }
          }
        }
      }
    }
  }
}

// // === UPDATE PAKET ===
if (isset($_POST['update_paket'])) {

  $id_paket  = $_POST['id_paket'];
  $nama_paket = trim($_POST['nama_paket']);
  $deskripsi   = trim($_POST['deskripsi']);
  $harga_paket = (int) $_POST['harga_paket'];

  $updateData = [
    'nama_paket'  => $nama_paket,
    'deskripsi'   => $deskripsi,
    'harga_paket' => $harga_paket,
  ];

  // ===== Jika user upload foto baru =====
  if (!empty($_FILES['foto_paket']['name'])) {

    $tmpName = $_FILES['foto_paket']['tmp_name'];
    $oriName = basename($_FILES['foto_paket']['name']);
    $ext = strtolower(pathinfo($oriName, PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    if (!in_array($ext, $allowed)) {
      echo "<script>alert('Format gambar tidak didukung');</script>";
      exit;
    }

    $namaFile = uniqid() . "_" . $oriName;

    $folder = __DIR__ . "/uploads/";
    if (!is_dir($folder)) mkdir($folder, 0777, true);

    $localPath = $folder . $namaFile;

    // Simpan sementara di server lokal
    move_uploaded_file($tmpName, $localPath);

    // Upload ke Supabase Storage (HANYA SEKALI)
   $upload = uploadToSupabaseStorage("", $localPath, $namaFile);

    // Hapus file lokal
    @unlink($localPath);

    if ($upload === false) {
      echo "<script>alert('Gagal upload foto baru');</script>";
      exit;
    }

    // MASUKKAN URL BARU KE DATABASE
    $updateData['foto_paket'] =
      "https://fsiuefdwcbdhunfhbiwl.supabase.co/storage/v1/object/public/images/paket/" . $namaFile;
  }

  // ===== UPDATE KE DATABASE (TIDAK ADA UPLOAD ULANG) =====
  $res = updateSupabaseData('paket', $id_paket, $updateData);

  if ($res) {
    echo "<script>alert('Paket berhasil diperbarui'); window.location='kelola-paket.php';</script>";
    exit;
  } else {
    echo "<script>alert('Gagal update paket');</script>";
  }
}


    // === DELETE PAKET ===
    if (isset($_GET['hapus'])) {
    $id_paket = $_GET['hapus'];

  if ($result['success']) {
    echo "<script>alert('Paket berhasil dihapus!'); window.location='kelola-paket.php';</script>";
    exit;
  } else {
    echo "<script>alert('Gagal menghapus paket: {$result['message']}'); console.log(`Debug: {$result['debug']}`);</script>";
  }
}


?>


<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Kelola Paket - Rahmat Kue</title>
  <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
  <link href="../css/sb-admin-2.min.css" rel="stylesheet">
  <style>
    .paket-card {
      transition: transform 0.2s, box-shadow 0.2s;
      height: 100%;
    }

    .paket-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
    }

    .paket-image {
      height: 200px;
      object-fit: cover;
      width: 100%;
      border-radius: 8px 8px 0 0;
    }

    .paket-price {
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

    .paket-card {
      border-radius: 10px;
      overflow: hidden;
    }

    .paket-image {
      width: 100%;
      height: 180px;
      object-fit: cover;
      border-bottom: 1px solid #f1f1f1;
    }

    .paket-title {
      font-size: 1rem;
      font-weight: 600;
      margin-bottom: 5px;
    }

    .paket-desc {
      font-size: 0.85rem;
      color: #6c757d;
      display: -webkit-box;
      -webkit-line-clamp: 3;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }

    .badge-sm {
      font-size: 0.7rem;
      margin-right: 6px;
      padding: 5px 8px;
    }

    .price-badge {
      font-size: 0.9rem;
      padding: 6px 10px;
      border-radius: 6px;
    }

    .btn-paket {
      font-size: 0.75rem;
      padding: 6px;
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
                      placeholder="Cari nama paket..."
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
              <div class="col-auto">
                <div class="action-buttons">
                  <button type="button" class="btn btn-success btn-lg btn-action" data-bs-toggle="modal" data-bs-target="#modalTambahPaket">
                    <p>Tambah Paket</p>
                  </button>
                  <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalTambahKategori">
                    Tambah Kategori
                  </button>
                </div>
              </div>
            </div>
          </div>

          <?php
          // di bagian atas body setelah session_start() dan sebelum output penting
          if (isset($_GET['deleted'])) {
            if ($_GET['deleted'] == '1') {
              echo '<div class="alert alert-success">Paket berhasil dihapus!</div>';
            } else {
              echo '<div class="alert alert-danger">Gagal menghapus paket.</div>';
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
                      <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Paket</div>
                      <div class="h5 mb-0 font-weight-bold text-gray-800">
                        <?php echo !empty($paket) ? count($paket) : 0; ?>
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

          <!-- Daftar Paket -->
          <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
              <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list"></i> Daftar Paket
              </h6>
              <span class="badge bg-primary">
                <?= !empty($paket) ? count($paket) : 0; ?> Paket
              </span>
            </div>

            <div class="card-body">
              <?php if (!empty($paket)): ?>
                <div class="row">

                  <?php foreach ($paket as $row):
                    $foto = htmlspecialchars($row['foto_paket'] ?? '');
                    if ($foto !== '') {
                      $imgUrl = filter_var($foto, FILTER_VALIDATE_URL)
                        ? $foto
                        : SUPABASE_STORAGE_URL . '/images/paket/' . rawurlencode($foto);
                    } else {
                      $imgUrl = '../../assets/img/no-image.png';
                    }

                    // Filter search
                    $search = isset($_GET['search']) ? strtolower($_GET['search']) : '';
                    if ($search != '') {
                      $nama_paket = strtolower($row['nama_paket'] ?? '');
                      if (strpos($nama_paket, $search) === false) {
                        continue;
                      }
                    }
                  ?>

                    <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                      <div class="card product-card shadow-sm">

                        <img src="<?= $imgUrl ?>"
                          class="product-image"
                          alt="<?= htmlspecialchars($row['nama_paket']) ?>"
                          loading="lazy"
                          onerror="this.src='../../assets/img/no-image.png'">

                        <div class="card-body">

                          <h5 class="product-title text-truncate"
                            title="<?= htmlspecialchars($row['nama_paket']) ?>">
                            <?= htmlspecialchars($row['nama_paket']) ?>
                          </h5>

                          <span class="badge bg-success text-white price-badge">
                            Rp <?= number_format($row['harga_paket'], 0, ',', '.') ?>
                          </span>

                        </div>

                        <div class="card-footer bg-transparent border-top-0">
                          <div class="d-grid gap-2">
                            <button class="btn btn-primary btn-product"
                              data-bs-toggle="modal"
                              data-bs-target="#modalEditPaket<?= $row['id_paket'] ?>">
                              <i class="fas fa-edit"></i> Edit
                            </button>

                            <a href="kelola-paket.php?hapus=<?= urlencode($row['id_paket']) ?>"
                              class="btn btn-danger btn-product"
                              onclick="return confirm('Yakin ingin menghapus paket ini?');">
                              <i class="fas fa-trash"></i> Hapus
                            </a>
                          </div>
                        </div>

                      </div>
                    </div>



                    <!-- ========================================================= -->
                    <!-- MODAL EDIT PAKET — DITARUH DI DALAM FOREACH -->
                    <!-- ========================================================= -->
                    <div class="modal fade" id="modalEditPaket<?= $row['id_paket'] ?>" tabindex="-1">
                      <div class="modal-dialog modal-lg">
                        <div class="modal-content">

                          <div class="modal-header bg-success text-white">
                            <h5 class="modal-title">
                              <i class="fas fa-edit"></i> Edit Paket
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                          </div>

                          <form method="POST" enctype="multipart/form-data">
                            <div class="modal-body">

                              <input type="hidden" name="id_paket" value="<?= $row['id_paket'] ?>">

                              <div class="row">
                                <div class="col-md-6">
                                  <div class="mb-3">
                                    <label class="form-label">Nama Paket</label>
                                    <input type="text" name="nama_paket" value="<?= htmlspecialchars($row['nama_paket']) ?>" class="form-control" required>
                                  </div>

                                  <div class="mb-3">
                                    <label class="form-label">Harga Paket</label>
                                    <input type="number" name="harga_paket" value="<?= $row['harga_paket'] ?>" class="form-control" required>
                                  </div>

                                  <div class="mb-3">
                                    <label class="form-label">Foto Paket</label><br>
                                    <img src="<?= $imgUrl ?>" class="mb-2" style="max-height:120px;">
                                    <input type="file" name="foto_paket" class="form-control">
                                  </div>
                                </div>

                                <div class="col-md-6">
                                  <div class="mb-3">
                                    <label class="form-label">Deskripsi Paket</label>
                                    <textarea name="deskripsi" class="form-control" rows="5" required><?= htmlspecialchars($row['deskripsi']) ?></textarea>
                                  </div>

                                </div>
                              </div>

                            </div>

                            <div class="modal-footer">
                              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                Batal
                              </button>
                              <button type="submit" name="update_paket" class="btn btn-success">
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
                  <h4 class="text-muted">Belum Ada Paket</h4>
                  <p class="text-muted">Klik tombol "Tambah Paket" untuk menambahkan paket baru</p>
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

  <!-- Modal Tambah Paket -->
  <div class="modal fade" id="modalTambahPaket" tabindex="-1" aria-labelledby="modalTambahPaketLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header bg-success text-white">
          <h5 class="modal-title" id="modalTambahPaketLabel">
            <i class="fas fa-plus-circle"></i> Tambah Paket Baru
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form method="POST" enctype="multipart/form-data">
          <div class="modal-body">
            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="nama_paket" class="form-label">
                    <i class="fas fa-box"></i> Nama Paket <span class="text-danger">*</span>
                  </label>
                  <input type="text" class="form-control" id="nama_paket" name="nama_paket" required>
                </div>

                <div class="mb-3">
                  <label for="harga" class="form-label">
                    <i class="fas fa-money-bill-wave"></i> Harga Paket <span class="text-danger">*</span>
                  </label>
                  <div class="input-group">
                    <span class="input-group-text">Rp</span>
                    <input type="number" class="form-control" id="harga_paket" name="harga_paket" required>
                  </div>
                </div>

                <div class="mb-3">
                  <label for="foto_paket" class="form-label">
                    <i class="fas fa-image"></i> Gambar Paket <span class="text-danger">*</span>
                  </label>
                  <input type="file" class="form-control" id="foto_paket" name="foto_paket" accept="image/*" required>
                  <small class="text-muted">Format: JPG, PNG, JPEG (Max 2MB)</small>
                </div>
              </div>

              <div class="col-md-6">
                <div class="mb-3">
                  <label for="deskripsi" class="form-label">
                    <i class="fas fa-align-left"></i> Deskripsi Paket <span class="text-danger">*</span>
                  </label>
                  <textarea class="form-control" id="deskripsi" name="deskripsi" rows="5" required></textarea>
                </div
                  </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                <i class="fas fa-times"></i> Batal
              </button>
              <button type="submit" name="tambah" class="btn btn-success">
                <i class="fas fa-save"></i> Simpan Paket
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

  <!-- Core plugin JavaScript-->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="../vendor/jquery-easing/jquery.easing.min.js"></script>

  <!-- Custom scripts for all pages-->
  <script src="js/sb-admin-2.min.js"></script>

</body>

</html>