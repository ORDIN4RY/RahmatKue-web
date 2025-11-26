<?php
session_start();
require 'auth/koneksi.php';

// ==========================
// CEK LOGIN
// ==========================
if (!isset($_SESSION['id'])) {
    header("Location: login.php?redirect=checkout.php");
    exit;
}

$id_user = $_SESSION['id'];
$access_token = $_SESSION['access_token'] ?? null;

// ==========================
// AMBIL DATA PRODUK TERPILIH
// ==========================
if (!isset($_GET['selected_ids']) || empty($_GET['selected_ids'])) {
    header("Location: keranjang.php");
    exit;
}

$selected_ids = explode(",", $_GET['selected_ids']);

try {
    $response = $client->get("/rest/v1/keranjang", [
        'headers' => [
            "apikey"        => SUPABASE_KEY,
            "Authorization" => "Bearer $access_token"
        ],
        'query' => [
            "id_keranjang" => "in.(" . implode(",", $selected_ids) . ")",
            "select"       => "id_keranjang,id_produk,jumlah,produk(nama_produk,harga,foto_produk)"
        ]
    ]);

    $checkout_items = json_decode($response->getBody(), true);
} catch (Exception $e) {
    die("Gagal memuat checkout: " . $e->getMessage());
}

// HITUNG TOTAL
$total = 0;
foreach ($checkout_items as $item) {
    $total += $item['produk']['harga'] * $item['jumlah'];
}

$alamat_rumah = '';
if ($id_user) {
    try {
        $response = $client->get("/rest/v1/alamat", [
            'query' => [
                'id_user' => 'eq.' . $id_user,
                'select' => 'alamat_rumah',
                'limit' => 1
            ]
        ]);

        $alamat_data = json_decode($response->getBody(), true);

        if (!empty($alamat_data)) {
            $alamat_rumah = htmlspecialchars($alamat_data[0]['alamat_rumah']);
        }
    } catch (Exception $e) {
        // Handle error, bisa di-log atau ditampilkan pesan default
        error_log("Error mengambil alamat: " . $e->getMessage());
    }
}
?>


<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Rahmat Kue</title>
    <link rel="icon" type="image/x-icon" href="assets/img/icon.png">
    <link rel="stylesheet" href="./assets/css/checkout.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

</head>

<body>

    <?php include "component/navbar.php"; ?>

    <!-- Progress Steps -->
    <div class="checkout-progress">
        <div class="progress-steps">
            <div class="step completed">
                <div class="step-circle">
                    <i class="fas fa-check"></i>
                </div>
                <span class="step-label">Keranjang</span>
            </div>
            <div class="step-divider"></div>
            <div class="step active">
                <div class="step-circle">2</div>
                <span class="step-label">Checkout</span>
            </div>
            <div class="step-divider"></div>
            <div class="step">
                <div class="step-circle">3</div>
                <span class="step-label">Selesai</span>
            </div>
        </div>
    </div>

    <div class="checkout-container">
        <form action="proses_checkout.php" method="POST" id="checkoutForm">
            <input type="hidden" name="selected_ids" value="<?= htmlspecialchars($_GET['selected_ids'] ?? '') ?>">

            <div class="row">
                <!-- Left Column - Forms -->
                <div class="col-lg-7">

                    <!-- Informasi Pembeli -->
                    <div class="form-card">
                        <h5 class="section-title">
                            <i class="bi bi-geo-alt-fill"></i>
                            Alamat Pengiriman
                        </h5>

                        <div class="alamat-method">
                            <div class="alamat-info">
                                <div class="alamat-name" style="font-weight: bold;">Rumah</div>
                                <div class="alamat-desc">
                                    <?php if (!empty($alamat_rumah)): ?>
                                        <?= $alamat_rumah ?>
                                    <?php else: ?>
                                        Alamat belum diatur. <a href="profil.php">Tambah alamat</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        2. Versi den

                        <div class="mb-3">
                            <label class="form-label">Patokan Alamat <span class="text-danger">*</span></label>
                            <textarea name="alamat" class="form-control" placeholder="Masukkan patokan pada alamat anda" required></textarea>
                        </div>
                    </div>

                    <!-- Metode Pengiriman -->
                    <div class="form-card">
                        <h5 class="section-title">
                            <i class="fas fa-truck"></i>
                            Pilih Opsi
                        </h5>

                        <div class="payment-method">
                            <input type="radio" name="pengiriman" value="reguler" checked>
                            <div class="payment-icon">
                                <i class="fas fa-box"></i>
                            </div>
                            <div class="payment-info">
                                <div class="payment-name">Diambil</div>
                                <div class="payment-desc">Pesanan diambil oleh pelanggan ke toko kami</div>
                            </div>
                            <strong>Gratis</strong>
                        </div>

                        <div class="payment-method">
                            <input type="radio" name="pengiriman" value="sameday">
                            <div class="payment-icon">
                                <i class="fas fa-motorcycle"></i>
                            </div>
                            <div class="payment-info">
                                <div class="payment-name">Diantar</div>
                                <div class="payment-desc">Dikirim ke alamat anda</div>
                            </div>
                            <strong>Rp 25.000</strong>
                        </div>
                    </div>

                    <!-- Metode Pembayaran -->
                    <div class="form-card">
                        <h5 class="section-title">
                            <i class="fas fa-credit-card"></i>
                            Metode Pembayaran
                        </h5>

                        <div class="payment-method">
                            <input type="radio" name="pembayaran" value="transfer" checked>
                            <div class="payment-icon">
                                <i class="fas fa-university"></i>
                            </div>
                            <div class="payment-info">
                                <div class="payment-name">DP (Minimal 50%)</div>
                                <div class="payment-desc">BCA, BNI, Mandiri, BRI</div>
                            </div>
                        </div>

                        <div class="payment-method">
                            <input type="radio" name="pembayaran" value="ewallet">
                            <div class="payment-icon">
                                <i class="fas fa-wallet"></i>
                            </div>
                            <div class="payment-info">
                                <div class="payment-name">Lunas</div>
                            </div>
                        </div>
                    </div>

                    <!-- pengiriman -->
                    <div class="form-card">
                        <h5 class="section-title">
                            <i class="fas fa-sticky-note"></i>
                            Tanggal Ambil Pesanan
                        </h5>
                        <input type="date" name="tanggal_ambil" class="form-control" required>

                    </div>

                </div>

                <!-- Right Column - Order Summary -->
                <div class="col-lg-5">
                    <div class="order-summary">
                        <h5 class="section-title">
                            <i class="fas fa-shopping-bag"></i>
                            Ringkasan Pesanan
                        </h5>

                        <!-- Products List -->
                        <?php
                        $subtotal = 0;
                        foreach ($checkout_items as $item):
                            $produk = $item['produk'];
                            $foto = $produk['foto_produk'] ?? 'assets/img/no-image.png';

                            if (!filter_var($foto, FILTER_VALIDATE_URL)) {
                                $foto = SUPABASE_STORAGE_URL . '/images/produk/' . rawurlencode($foto);
                            }

                            $subtotal = $produk['harga'] * $item['jumlah'];
                        ?>
                            <div class="product-item">
                                <div class="product-image">
                                    <img src="<?= $foto ?>" alt="<?= htmlspecialchars($produk['nama_produk']) ?>">
                                </div>
                                <div class="product-details">
                                    <div class="product-name"><?= htmlspecialchars($produk['nama_produk']) ?></div>
                                    <div class="product-quantity">Qty: <?= $item['jumlah'] ?> × Rp<?= number_format($produk['harga'], 0, ',', '.') ?></div>
                                </div>
                                <div class="product-price">
                                    Rp<?= number_format($subtotal, 0, ',', '.') ?>
                                </div>
                            </div>
                        <?php endforeach;

                        $discount = 0;
                        $shipping = 0;
                        $total = $subtotal - $discount + $shipping;
                        ?>

                        <!-- Price Summary -->
                        <div class="price-summary">
                            <div class="price-row">
                                <span>Subtotal</span>
                                <span>Rp<?= number_format($subtotal, 0, ',', '.') ?></span>
                            </div>
                            <div class="price-row">
                                <span>Ongkos Kirim</span>
                                <span id="shippingCost">Gratis</span>
                            </div>
                            <?php if ($discount > 0): ?>
                                <div class="price-row discount">
                                    <span>Diskon</span>
                                    <span>-Rp<?= number_format($discount, 0, ',', '.') ?></span>
                                </div>
                            <?php endif; ?>
                            <div class="price-row total">
                                <span>Total Pembayaran</span>
                                <span id="totalPrice">Rp<?= number_format($total, 0, ',', '.') ?></span>
                            </div>
                        </div>

                        <!-- Checkout Button -->
                        <button type="submit" class="btn-checkout">
                            <i class="fas fa-lock"></i>
                            Buat Pesanan
                        </button>

                        <!-- Security Badge -->
                        <div class="security-badge">
                            <i class="fas fa-shield-alt"></i>
                            <span>Transaksi Anda Aman & Terlindungi</span>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Payment method selection styling
        document.querySelectorAll('.payment-method').forEach(method => {
            method.addEventListener('click', function() {
                // Remove selected class from all
                document.querySelectorAll('.payment-method').forEach(m => m.classList.remove('selected'));
                // Add to clicked
                this.classList.add('selected');
                // Check the radio button
                this.querySelector('input[type="radio"]').checked = true;
            });
        });

        // Update shipping cost
        document.querySelectorAll('input[name="pengiriman"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const shippingCosts = {
                    'reguler': 0,
                    'express': 15000,
                    'sameday': 25000
                };

                const subtotal = <?= $subtotal ?>;
                const discount = <?= $discount ?>;
                const shippingCost = shippingCosts[this.value];
                const total = subtotal - discount + shippingCost;

                document.getElementById('shippingCost').textContent = shippingCost === 0 ? 'Gratis' : 'Rp' + shippingCost.toLocaleString('id-ID');
                document.getElementById('totalPrice').textContent = 'Rp' + total.toLocaleString('id-ID');
            });
        });

        // Form validation
        document.getElementById('checkoutForm').addEventListener('submit', function(e) {
            const requiredFields = this.querySelectorAll('[required]');
            let isValid = true;

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('is-invalid');
                } else {
                    field.classList.remove('is-invalid');
                }
            });

            if (!isValid) {
                e.preventDefault();
                alert('Mohon lengkapi semua data yang diperlukan');
            }
        });

        // Promo code functionality
        document.querySelector('.btn-apply-promo').addEventListener('click', function() {
            const promoInput = document.querySelector('input[name="promo_code"]');
            const promoCode = promoInput.value.trim().toUpperCase();

            if (promoCode) {
                // Simulate promo code validation
                if (promoCode === 'DISKON10') {
                    alert('Kode promo berhasil diterapkan! Anda mendapat diskon 10%');
                    // Update prices here
                } else {
                    alert('Kode promo tidak valid');
                }
            }
        });
    </script>

</body>

</html>