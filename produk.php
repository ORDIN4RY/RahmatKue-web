<?php
session_start();
include 'auth/koneksi.php';

// Cek login dan level user
$isLoggedIn = isset($_SESSION['id_user']);
$level = $isLoggedIn ? $_SESSION['level'] : null;

// Ambil data produk + kategori
$query = mysqli_query($conn, "
    SELECT produk.*, kategori.nama_kategori 
    FROM produk 
    JOIN kategori ON produk.id_kategori = kategori.id_kategori
");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rahmat Kue</title>
    <link rel="icon" type="image/x-icon" href="assets/img/icon.png">
    <link rel="stylesheet" href="assets/css/produk.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>

    <?php include 'component/navbar.php'; ?>

    <!-- Header -->
    <section class="produk-section">
        <h2>Produk Kami</h2>
        <p>Dolor sit amet, consectetur adipiscing elit. Vivamus at sapien velit.<br>
            Sed ultrices vulputate mi in mollis. Integer pulvinar quam at tortor facilisis varius a sit amet dolor.
        </p>

        <div class="btn-search">
            <input type="text" placeholder="Cari produk...">
            <button type="submit"><i class="fa fa-search"></i></button>
        </div>

        <div class="kategori">
            <button class="btn-kategori-active">Semua</button>
            <button class="btn-kategori">Paket</button>
            <button class="btn-kategori">Tradisional</button>
            <button class="btn-kategori">Roti</button>
            <button class="btn-kategori">Cake</button>
            <button class="btn-kategori">Donat</button>
            <button class="btn-kategori">Kotak</button>
        </div>

        <div class="produk-container">
            <?php while ($row = mysqli_fetch_assoc($query)) { ?>
                <div class="produk-card" data-id="<?= $row['id_produk'] ?>">
                    <img src="pages/admin/uploads/<?= htmlspecialchars($row['foto_produk']) ?>" 
                         alt="<?= htmlspecialchars($row['nama_produk']) ?>">
                    <div class="produk-card-content">
                        <h3><?= htmlspecialchars($row['nama_produk']) ?></h3>
                        <p><?= htmlspecialchars($row['deskripsi']) ?></p>
                        <p class="harga">Rp <?= number_format($row['harga'], 0, ',', '.') ?></p>
                        <span class="kategori-item"><?= htmlspecialchars($row['nama_kategori']) ?></span>
                        
                        <?php if (!$isLoggedIn) { ?>
                            <button class="btn-produk" onclick="alert('Silakan login terlebih dahulu!')">Tambah Pesanan</button>
                        <?php } elseif ($level == '0') { ?>
                            <button class="btn-produk tambah-btn">Tambah Pesanan</button>
                            <div class="pesanan-controls">
                                <button class="minus">−</button>
                                <span class="jumlah">1</span>
                                <button class="plus">+</button>
                            </div>
                            <div class="catatan"><i class="fa fa-pen"></i> Catatan</div>
                        <?php } else { ?>
                            <button class="btn-produk" onclick="alert('Admin tidak dapat menambahkan pesanan!')">Tambah Pesanan</button>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>
        </div>
    </section>

    <?php include 'component/footer.php'; ?>

    <script>
        // Fungsi interaktif tombol tambah pesanan
        document.querySelectorAll('.tambah-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const card = this.closest('.produk-card');
                this.style.display = 'none';
                card.querySelector('.pesanan-controls').style.display = 'flex';
                card.querySelector('.catatan').style.display = 'block';
            });
        });

        document.querySelectorAll('.plus').forEach(plus => {
            plus.addEventListener('click', function() {
                const jumlahEl = this.parentElement.querySelector('.jumlah');
                jumlahEl.textContent = parseInt(jumlahEl.textContent) + 1;
            });
        });

        document.querySelectorAll('.minus').forEach(minus => {
            minus.addEventListener('click', function() {
                const jumlahEl = this.parentElement.querySelector('.jumlah');
                let j = parseInt(jumlahEl.textContent);
                if (j > 1) jumlahEl.textContent = j - 1;
                else {
                    // Jika kembali ke 0, tampilkan tombol "Tambah Pesanan" lagi
                    const card = this.closest('.produk-card');
                    card.querySelector('.pesanan-controls').style.display = 'none';
                    card.querySelector('.catatan').style.display = 'none';
                    card.querySelector('.tambah-btn').style.display = 'inline-block';
                }
            });
        });
    </script>

</body>
</html>
