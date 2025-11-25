<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Rahmat Kue</title>
    <link rel="icon" type="image/x-icon" href="assets/img/icon.png">
    <link rel="stylesheet" href="assets/css/pesan.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

<?php include "component/navbar.php"; ?>

<div class="container my-5">
    <h2 class="text-center mb-4">Checkout</h2>

    <div class="row">
        <!-- ==========================
             DATA PEMBELI
        =========================== -->
        <div class="col-md-6">
            <div class="card p-4 shadow-sm">
                <h5 class="mb-3">Informasi Pembeli</h5>

                <form action="proses_checkout.php" method="POST">
                    <input type="hidden" name="selected_ids" value="<?= htmlspecialchars($_GET['selected_ids']) ?>">

                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nomor WhatsApp</label>
                        <input type="text" name="nohp" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Alamat Lengkap</label>
                        <textarea name="alamat" class="form-control" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Catatan</label>
                        <textarea name="catatan" class="form-control"></textarea>
                    </div>

                    <button type="submit" class="btn btn-success w-100 btn-lg">
                        Buat Pesanan
                    </button>
                </form>
            </div>
        </div>

        <!-- ==========================
             RINGKASAN PESANAN
        =========================== -->
        <div class="col-md-6">
            <div class="card p-4 shadow-sm">
                <h5 class="mb-3">Produk yang Dipesan</h5>

                <?php foreach ($checkout_items as $item): ?>
                    <?php
                        $produk = $item['produk'];
                        $foto = $produk['foto_produk'] ?? 'assets/img/no-image.png';

                        if (!filter_var($foto, FILTER_VALIDATE_URL)) {
                            $foto = SUPABASE_STORAGE_URL . '/images/produk/' . rawurlencode($foto);
                        }

                        $subtotal = $produk['harga'] * $item['jumlah'];
                    ?>

                    <div class="product-item" style="border-bottom: 1px solid #eee; padding-bottom: 15px;">
                        <div class="product-info">
                            <div class="product-image">
                                <img src="<?= $foto ?>" width="70">
                            </div>

                            <div class="product-details">
                                <div class="product-title">
                                    <?= htmlspecialchars($produk['nama_produk']) ?>
                                </div>
                                <div>Qty: <?= $item['jumlah'] ?></div>
                            </div>
                        </div>

                        <div class="total-price">
                            Rp<?= number_format($subtotal, 0, ',', '.') ?>
                        </div>
                    </div>
                <?php endforeach; ?>

                <hr>

                <div class="d-flex justify-content-between">
                    <strong>Total Pembayaran:</strong>
                    <strong>Rp<?= number_format($total, 0, ',', '.') ?></strong>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>