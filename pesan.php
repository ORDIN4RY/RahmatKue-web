<?php
session_start();

// Pastikan keranjang ada
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: produk.php"); // jika keranjang kosong, arahkan kembali
    exit;
}

// Hitung total keseluruhan
$total_semua = 0;
foreach ($_SESSION['cart'] as $item) {
    $total_semua += $item['harga'] * $item['quantity'];
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Anda - Rahmat Kue</title>
    <link rel="icon" type="image/x-icon" href="assets/img/icon.png">
    <link rel="stylesheet" href="assets/css/pesan.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include 'component/navbar.php'; ?>

    <div class="container my-5">
        <h2 class="text-center mb-4">Pesanan Anda</h2>

        <div class="cart-container">
            <div class="cart-header">
                <div></div>
                <div>Produk</div>
                <div>Harga Satuan</div>
                <div>Kuantitas</div>
                <div>Total Harga</div>
                <div style="text-align: center;">Aksi</div>
            </div>

            <div class="store-section">
                <?php foreach ($_SESSION['cart'] as $item): ?>
                    <?php
                    $subtotal = $item['harga'] * $item['quantity'];
                    $total_semua += $subtotal;
                    ?>
                    <div class="product-item">
                        <div class="checkbox"></div>
                        <div class="product-info">
                            <div class="product-image">
                                <img src="<?= htmlspecialchars($item['foto']) ?>" alt="<?= htmlspecialchars($item['nama']) ?>" width="80" height="80">
                            </div>
                            <div class="product-details">
                                <div class="product-promo">PROMO</div>
                                <div class="product-title"><?= htmlspecialchars($item['nama']) ?></div>
                                <?php if (!empty($item['size'])): ?>
                                    <div class="product-variant">Ukuran: <?= htmlspecialchars($item['size']) ?></div>
                                <?php endif; ?>
                                <?php if (!empty($item['wording'])): ?>
                                    <div class="product-variant">Tulisan: <?= htmlspecialchars($item['wording']) ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="price">Rp<?= number_format($item['harga'], 0, ',', '.') ?></div>
                        <div class="quantity-control">
                            <span><?= $item['quantity'] ?></span>
                        </div>
                        <div class="total-price">Rp<?= number_format($subtotal, 0, ',', '.') ?></div>
                        <div class="action-column">
                            <form method="POST" action="hapus-item.php">
                                <input type="hidden" name="hapus_id" value="<?= htmlspecialchars($item['id']) ?>">
                                <button type="submit" class="delete-btn">Hapus</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="cart-footer mt-4">
            <div class="footer-left">
                <div class="select-all">
                    <input type="checkbox" style="width: 20px; height: 20px;">
                    <span>Pilih Semua (<?= count($_SESSION['cart']) ?>)</span>
                </div>
                <span class="delete-link">Hapus</span>
                <span class="delete-link" style="color: #999;">Hapus produk dari Penjual tidak aktif</span>
                <span class="save-link">Tambahkan ke Favorit Saya</span>
            </div>
            <div class="footer-right">
                <div class="total-section">
                    <div class="total-label">Total (<?= count($_SESSION['cart']) ?> produk):</div>
                    <div class="total-amount">Rp<?= number_format($total_semua, 0, ',', '.') ?></div>
                </div>
                <form method="POST" action="checkout-proses.php">
                    <button type="submit" class="checkout-btn">Buat Pesanan</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
