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
?>


<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Rahmat Kue</title>
    <link rel="icon" type="image/x-icon" href="assets/img/icon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary-color: #8b6f47;
            --secondary-color: #6d5738;
            --light-bg: #f8f9fa;
            --border-color: #e5e7eb;
            --text-dark: #2d3748;
            --text-muted: #718096;
            --success-color: #48bb78;
        }

        body {
            background-color: var(--light-bg);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--text-dark);
        }

        /* Progress Steps */
        .checkout-progress {
            background: white;
            padding: 30px 0;
            margin-bottom: 30px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .progress-steps {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 20px;
            max-width: 800px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .step {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .step-circle {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: #e5e7eb;
            color: #9ca3af;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s;
        }

        .step.active .step-circle {
            background: var(--primary-color);
            color: white;
            box-shadow: 0 4px 12px rgba(139, 111, 71, 0.3);
        }

        .step.completed .step-circle {
            background: var(--success-color);
            color: white;
        }

        .step-label {
            font-weight: 500;
            color: #9ca3af;
            display: none;
        }

        .step.active .step-label {
            color: var(--primary-color);
        }

        .step-divider {
            width: 50px;
            height: 2px;
            background: #e5e7eb;
        }

        /* Main Container */
        .checkout-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px 60px;
        }

        /* Section Title */
        .section-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-title i {
            color: var(--primary-color);
        }

        /* Form Card */
        .form-card {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            margin-bottom: 20px;
        }

        .form-label {
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 8px;
            font-size: 0.9rem;
        }

        .form-control,
        .form-select {
            border: 2px solid var(--border-color);
            border-radius: 8px;
            padding: 12px 15px;
            font-size: 0.95rem;
            transition: all 0.3s;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(139, 111, 71, 0.15);
        }

        textarea.form-control {
            min-height: 100px;
            resize: vertical;
        }

        /* Order Summary Card */
        .order-summary {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            position: sticky;
            top: 20px;
        }

        /* Product Item */
        .product-item {
            display: flex;
            gap: 15px;
            padding: 15px 0;
            border-bottom: 1px solid var(--border-color);
        }

        .product-item:last-child {
            border-bottom: none;
        }

        .product-image {
            width: 80px;
            height: 80px;
            border-radius: 8px;
            overflow: hidden;
            flex-shrink: 0;
            background: var(--light-bg);
        }

        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .product-details {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .product-name {
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 5px;
            font-size: 0.95rem;
        }

        .product-quantity {
            color: var(--text-muted);
            font-size: 0.85rem;
        }

        .product-price {
            font-weight: 600;
            color: var(--text-dark);
            text-align: right;
            font-size: 1rem;
        }

        /* Price Summary */
        .price-summary {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 2px solid var(--border-color);
        }

        .price-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            font-size: 0.95rem;
        }

        .price-row.total {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-top: 15px;
            padding-top: 15px;
            border-top: 2px solid var(--border-color);
        }

        .price-row.discount {
            color: var(--success-color);
        }

        /* Payment Method */
        .payment-method {
            border: 2px solid var(--border-color);
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .payment-method:hover {
            border-color: var(--primary-color);
            background: #fafafa;
        }

        .payment-method input[type="radio"] {
            width: 20px;
            height: 20px;
            cursor: pointer;
        }

        .payment-method.selected {
            border-color: var(--primary-color);
            background: #fef9f5;
        }

        .payment-icon {
            width: 50px;
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--light-bg);
            border-radius: 6px;
        }

        .payment-icon i {
            font-size: 1.5rem;
            color: var(--primary-color);
        }

        .payment-info {
            flex: 1;
        }

        .payment-name {
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 3px;
        }

        .payment-desc {
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        /* Checkout Button */
        .btn-checkout {
            width: 100%;
            padding: 15px;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 20px;
        }

        .btn-checkout:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(139, 111, 71, 0.3);
        }

        .btn-checkout i {
            margin-right: 8px;
        }

        /* Security Badge */
        .security-badge {
            text-align: center;
            padding: 15px;
            background: #f0fdf4;
            border-radius: 8px;
            margin-top: 15px;
        }

        .security-badge i {
            color: var(--success-color);
            font-size: 1.2rem;
            margin-right: 8px;
        }

        .security-badge span {
            color: #166534;
            font-size: 0.85rem;
            font-weight: 500;
        }

        /* Promo Code */
        .promo-section {
            background: #fef3c7;
            border: 1px solid #fcd34d;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .promo-input-group {
            display: flex;
            gap: 10px;
        }

        .promo-input-group input {
            flex: 1;
        }

        .btn-apply-promo {
            padding: 10px 20px;
            background: #f59e0b;
            color: white;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            white-space: nowrap;
        }

        .btn-apply-promo:hover {
            background: #d97706;
        }

        /* Responsive */
        @media (min-width: 768px) {
            .step-label {
                display: block;
            }
        }

        @media (max-width: 991px) {
            .order-summary {
                position: static;
                margin-top: 30px;
            }
        }

        @media (max-width: 576px) {

            .form-card,
            .order-summary {
                padding: 20px;
            }

            .section-title {
                font-size: 1.1rem;
            }

            .product-image {
                width: 60px;
                height: 60px;
            }

            .step-divider {
                width: 30px;
            }
        }
    </style>
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
                            <i class="fas fa-user-circle"></i>
                            Informasi Pembeli
                        </h5>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" name="nama" class="form-control" placeholder="Masukkan nama lengkap" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nomor WhatsApp <span class="text-danger">*</span></label>
                                <input type="text" name="nohp" class="form-control" placeholder="08xx xxxx xxxx" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" placeholder="email@example.com">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Alamat Lengkap <span class="text-danger">*</span></label>
                            <textarea name="alamat" class="form-control" placeholder="Masukkan alamat lengkap pengiriman" required></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kota</label>
                                <input type="text" name="kota" class="form-control" placeholder="Kota">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kode Pos</label>
                                <input type="text" name="kode_pos" class="form-control" placeholder="12345">
                            </div>
                        </div>
                    </div>

                    <!-- Metode Pengiriman -->
                    <div class="form-card">
                        <h5 class="section-title">
                            <i class="fas fa-truck"></i>
                            Metode Pengiriman
                        </h5>

                        <div class="payment-method">
                            <input type="radio" name="pengiriman" value="reguler" checked>
                            <div class="payment-icon">
                                <i class="fas fa-box"></i>
                            </div>
                            <div class="payment-info">
                                <div class="payment-name">Pengiriman Reguler</div>
                                <div class="payment-desc">Estimasi 3-5 hari kerja</div>
                            </div>
                            <strong>Gratis</strong>
                        </div>

                        <div class="payment-method">
                            <input type="radio" name="pengiriman" value="express">
                            <div class="payment-icon">
                                <i class="fas fa-shipping-fast"></i>
                            </div>
                            <div class="payment-info">
                                <div class="payment-name">Pengiriman Express</div>
                                <div class="payment-desc">Estimasi 1-2 hari kerja</div>
                            </div>
                            <strong>Rp 15.000</strong>
                        </div>

                        <div class="payment-method">
                            <input type="radio" name="pengiriman" value="sameday">
                            <div class="payment-icon">
                                <i class="fas fa-motorcycle"></i>
                            </div>
                            <div class="payment-info">
                                <div class="payment-name">Same Day Delivery</div>
                                <div class="payment-desc">Dikirim hari ini</div>
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
                                <div class="payment-name">Transfer Bank</div>
                                <div class="payment-desc">BCA, BNI, Mandiri, BRI</div>
                            </div>
                        </div>

                        <div class="payment-method">
                            <input type="radio" name="pembayaran" value="ewallet">
                            <div class="payment-icon">
                                <i class="fas fa-wallet"></i>
                            </div>
                            <div class="payment-info">
                                <div class="payment-name">E-Wallet</div>
                                <div class="payment-desc">GoPay, OVO, Dana, ShopeePay</div>
                            </div>
                        </div>

                        <div class="payment-method">
                            <input type="radio" name="pembayaran" value="cod">
                            <div class="payment-icon">
                                <i class="fas fa-money-bill-wave"></i>
                            </div>
                            <div class="payment-info">
                                <div class="payment-name">Bayar di Tempat (COD)</div>
                                <div class="payment-desc">Bayar saat pesanan diterima</div>
                            </div>
                        </div>
                    </div>

                    <!-- Catatan -->
                    <div class="form-card">
                        <h5 class="section-title">
                            <i class="fas fa-sticky-note"></i>
                            Catatan Pesanan (Opsional)
                        </h5>

                        <textarea name="catatan" class="form-control" placeholder="Tambahkan catatan untuk pesanan Anda (misal: waktu pengiriman, permintaan khusus, dll)"></textarea>
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
                        // Sample data - replace with actual data
                        // $checkout_items = [
                        //     [
                        //         'produk' => [
                        //             'nama_produk' => 'Kue Lapis Surabaya',
                        //             'harga' => 200000,
                        //             'foto_produk' => 'https://images.unsplash.com/photo-1586985289688-ca3cf47d3e6e?w=300'
                        //         ],
                        //         'jumlah' => 1
                        //     ],
                        //     [
                        //         'produk' => [
                        //             'nama_produk' => 'Brownies Cokelat',
                        //             'harga' => 75000,
                        //             'foto_produk' => 'https://images.unsplash.com/photo-1607920591413-4ec007e70023?w=300'
                        //         ],
                        //         'jumlah' => 2
                        //     ]
                        // ];

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

                        <!-- Promo Code -->
                        <div class="promo-section">
                            <label class="form-label mb-2">
                                <i class="fas fa-ticket-alt me-2"></i>Punya Kode Promo?
                            </label>
                            <div class="promo-input-group">
                                <input type="text" name="promo_code" class="form-control" placeholder="Masukkan kode promo">
                                <button type="button" class="btn-apply-promo">Terapkan</button>
                            </div>
                        </div>

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