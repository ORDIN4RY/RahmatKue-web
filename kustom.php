<?php
session_start();
require 'auth/koneksi.php';

$wadah = getSupabaseData("wadah");
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rahmat Kue - Produk</title>
    <link rel="icon" type="image/x-icon" href="assets/img/icon.png">
    <link rel="stylesheet" href="assets/css/produk.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include 'component/navbar.php'; ?>

    <section class="produk-section">
        <h2>Kustom Pesanan</h2>
        <p>
            Temukan berbagai pilihan kue terbaik dari Rahmat Bakery.<br>
            Sesuaikan kue impian Anda dengan berbagai opsi kustomisasi yang kami tawarkan!
        </p>
        <div class="search-container">
            <form action="" method="get" class="search-form">
                <div class="search-box">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" name="search" placeholder="Cari produk" value="<?= htmlspecialchars($keyword ?? '') ?>">
                </div>
            </form>
        </div>

        <div class="produk-container">
            <?php if (!empty($wadah)): ?>
                <?php foreach ($wadah as $row): ?>

                    <div class="wadah-card">

                        <div class="wadah-badge">
                            <?= htmlspecialchars($row['varian']) ?>
                        </div>

                        <img
                            src="<?= !empty($row['foto_wadah'])
                                        ? htmlspecialchars($row['foto_wadah'])
                                        : 'https://images.unsplash.com/photo-1548943487-a2e4e43b4853?q=80&w=600' ?>"
                            alt="Foto <?= htmlspecialchars($row['nama_wadah']) ?>"
                            class="wadah-img">

                        <h3><?= htmlspecialchars($row['nama_wadah']) ?></h3>

                        <p><?= htmlspecialchars($row['deskripsi']) ?></p>

                        <p>Kapasitas: <strong><?= htmlspecialchars($row['kapasitas']) ?></strong> item</p>

                        <p class="harga">Rp <?= number_format($row['harga_wadah'], 0, ',', '.') ?></p>

                    </div>


                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-danger">Tidak ada data wadah.</p>
            <?php endif; ?>

        </div>


        <div class="modal fade" id="loginModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Login Diperlukan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        Anda harus login terlebih dahulu untuk melihat detail produk.
                    </div>
                    <div class="modal-footer">
                        <a href="./auth/login.php" class="btn btn-primary">Login</a>
                    </div>
                </div>
            </div>
        </div>

    </section>

    <?php include 'component/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>