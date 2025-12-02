<?php
session_start();
require 'auth/koneksi.php';

// CEK LOGIN
if (!isset($_SESSION['id'])) {
    header("Location: login.php?redirect=checkout.php");
    exit;
}

$id_user        = $_SESSION['id'];
$access_token   = $_SESSION['access_token'] ?? null;

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

// ==========================
// HITUNG TOTAL SUBTOTAL
// ==========================
$subtotal = 0;
foreach ($checkout_items as $item) {
    $subtotal += $item['produk']['harga'] * $item['jumlah'];
}

// ==========================
// AMBIL ALAMAT USER
// ==========================
$alamat = getSupabaseData('alamat');

// ==========================
// AMBIL DATA VOUCHER
// ==========================
$vouchers = [];
try {
    $response = $client->get("/rest/v1/voucher", [
        'headers' => [
            "apikey"        => SUPABASE_KEY,
            "Authorization" => "Bearer $access_token"
        ],
        'query' => [
            'select' => 'id_voucher,kode_voucher,diskon_persen,diskon_nominal,min_pembelian'
        ]
    ]);
    $vouchers = json_decode($response->getBody(), true);
} catch (Exception $e) {
    error_log("Error mengambil voucher: " . $e->getMessage());
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

            <!-- Hidden fields -->
            <input type="hidden" name="selected_ids" value="<?= htmlspecialchars($_GET['selected_ids'] ?? '') ?>">
            <input type="hidden" name="id_user" value="<?= $_SESSION['id'] ?>">
            <input type="hidden" name="subtotal" value="<?= $total ?>">
            <input type="hidden" name="subtotal" value="<?= $subtotal ?>">
            <?php foreach ($checkout_items as $item): ?>
                <input type="hidden" name="keranjang[<?= $item['id_keranjang'] ?>][harga]" value="<?= $item['produk']['harga'] ?>">
                <input type="hidden" name="keranjang[<?= $item['id_keranjang'] ?>][qty]" value="<?= $item['jumlah'] ?>">
            <?php endforeach; ?>


            <div class="row">

                <!-- LEFT SECTION -->
                <div class="col-lg-7">

                    <!-- ALAMAT -->
                    <div class="form-card">
                        <h5 class="section-title">
                            <i class="bi bi-geo-alt-fill"></i> Alamat Pengiriman
                        </h5>

                        <select class="form-select" name="id_alamat" required>
                            <option value="">Pilih Alamat Anda</option>
                            <?php foreach ($alamat as $kat): ?>
                                <option value="<?= $kat['id_alamat'] ?>">
                                    <?= htmlspecialchars($kat['alamat_rumah']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- METODE PENGAMBILAN -->
                    <div class="form-card">
                        <h5 class="section-title">
                            <i class="fas fa-truck"></i> Metode Pengambilan
                        </h5>

                        <!-- DIAMBIL SENDIRI -->
                        <div class="payment-method">
                            <input type="radio" name="metode_pengambilan" value="diambil" id="diambil" checked>
                            <label for="diambil">
                                <div class="payment-icon"><i class="fas fa-box"></i></div>
                                <div class="payment-info">
                                    <div class="payment-name">Diambil Sendiri</div>
                                    <div class="payment-desc">Ambil di toko</div>
                                </div>
                                <strong class="ms-auto">Gratis</strong>
                            </label>
                        </div>

                        <!-- DIANTAR -->
                        <div class="payment-method">
                            <input type="radio" name="metode_pengambilan" value="diantar" id="diantar">
                            <label for="diantar">
                                <div class="payment-icon"><i class="fas fa-motorcycle"></i></div>
                                <div class="payment-info">
                                    <div class="payment-name">Diantar</div>
                                    <div class="payment-desc">Kirim ke alamat</div>
                                </div>
                                <strong class="ms-auto">Rp 25.000</strong>
                            </label>
                        </div>
                    </div>

                    <!-- METODE PEMBAYARAN -->
                    <!-- <div class="form-card">
                        <h5 class="section-title">
                            <i class="fas fa-credit-card"></i> Metode Pembayaran
                        </h5>

                        <div class="payment-method">
                            <input type="radio" name="metode_pembayaran" value="dp" id="dp" checked>
                            <label for="dp">
                                <div class="payment-icon"><i class="fas fa-university"></i></div>
                                <div class="payment-info">
                                    <div class="payment-name">DP (Minimal 50%)</div>
                                    <input type="text" name="dp_minimal" placeholder="Masukkan nominal DP" class="form-control">
                                </div>
                            </label>
                        </div>

                        <div class="payment-method">
                            <input type="radio" name="metode_pembayaran" value="lunas" id="lunas">
                            <label for="lunas">
                                <div class="payment-icon"><i class="fas fa-wallet"></i></div>
                                <div class="payment-info">
                                    <div class="payment-name">Lunas</div>
                                    <div class="payment-desc">Bayar penuh</div>
                                </div>
                            </label>
                        </div>
                    </div> -->

                    <!-- TANGGAL SELESAI -->
                    <div class="form-card">
                        <h5 class="section-title">
                            <i class="fas fa-calendar-alt"></i> Tanggal Selesai Pesanan
                        </h5>

                        <label class="form-label">Tanggal & Waktu <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="waktu_selesai" class="form-control"
                            required min="<?= date('Y-m-d\TH:i', strtotime('+2 day')) ?>">
                        <small class="text-muted">Minimal H+2 dari sekarang</small>
                    </div>

                    <!-- CATATAN OPSIONAL -->
                    <div class="form-card">
                        <h5 class="section-title">
                            <i class="fas fa-file-alt"></i> Catatan (Opsional)
                        </h5>
                        <textarea name="catatan" class="form-control" placeholder="Catatan tambahan..."></textarea>
                    </div>

                    <!-- VOUCHER OPSIONAL -->
                    <?php if (!empty($vouchers)): ?>
                        <div class="form-card">
                            <h5 class="section-title"><i class="fas fa-tag"></i> Voucher Diskon</h5>

                            <select name="id_voucher" class="form-select">
                                <option value="">Tidak menggunakan voucher</option>
                                <?php foreach ($vouchers as $v): ?>
                                    <option value="<?= $v['id_voucher'] ?>">
                                        <?= htmlspecialchars($v['kode_voucher']) ?>
                                        <?php if ($v['diskon_persen']): ?>
                                            - <?= $v['diskon_persen'] ?>%
                                        <?php elseif ($v['diskon_nominal']): ?>
                                            - Rp <?= number_format($v['diskon_nominal'], 0, ',', '.') ?>
                                        <?php endif; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- RIGHT COLUMN -->
                <div class="col-lg-5">
                    <div class="order-summary">
                        <h5 class="section-title">
                            <i class="fas fa-shopping-bag"></i> Ringkasan Pesanan
                        </h5>

                        <?php
                        $subtotal = 0;
                        foreach ($checkout_items as $item):
                            $produk = $item['produk'];
                            $foto = $produk['foto_produk'] ?? 'assets/img/no-image.png';

                            if (!filter_var($foto, FILTER_VALIDATE_URL)) {
                                $foto = SUPABASE_STORAGE_URL . '/images/produk/' . rawurlencode($foto);
                            }

                            $item_total = $produk['harga'] * $item['jumlah'];
                            $subtotal += $item_total;
                        ?>
                            <div class="product-item">
                                <div class="product-image">
                                    <img src="<?= $foto ?>" alt="<?= htmlspecialchars($produk['nama_produk']) ?>">
                                </div>
                                <div class="product-details">
                                    <div class="product-name"><?= htmlspecialchars($produk['nama_produk']) ?></div>
                                    <div class="product-quantity">
                                        Qty: <?= $item['jumlah'] ?> × Rp<?= number_format($produk['harga'], 0, ',', '.') ?>
                                    </div>
                                </div>
                                <div class="product-price">
                                    Rp<?= number_format($item_total, 0, ',', '.') ?>
                                </div>
                            </div>
                        <?php endforeach; ?>

                        <div class="price-summary">
                            <div class="price-row">
                                <span>Subtotal Produk</span>
                                <span>Rp<?= number_format($subtotal, 0, ',', '.') ?></span>
                            </div>
                            <div class="price-row">
                                <span>Ongkos Kirim</span>
                                <span>Gratis</span>
                            </div>
                            <div class="price-row">
                                <span>Diskon Voucher</span>
                                <span>-</span>
                            </div>
                            <div class="price-row total">
                                <span>Total Pembayaran</span>
                                <span>Rp<?= number_format($subtotal, 0, ',', '.') ?></span>
                            </div>
                            <!-- 
                            <div class="alert alert-info mt-3">
                                <i class="fas fa-info-circle"></i>
                                <strong>DP Minimal (50%):</strong>
                                Rp<?= number_format($subtotal * 0.5, 0, ',', '.') ?>
                            </div> -->
                        </div>

                        <button type="submit" class="btn-checkout">
                            <i class="fas fa-lock"></i> Buat Pesanan
                        </button>

                        <div class="security-badge">
                            <i class="fas fa-shield-alt"></i> Transaksi Aman
                        </div>
                    </div>
                </div>

            </div>
        </form>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Simple validation before submit
        document.getElementById('checkoutForm').addEventListener('submit', function(e) {
            const idAlamat = document.querySelector('input[name="id_alamat"]').value;
            const metodePengambilan = document.querySelector('input[name="metode_pengambilan"]:checked').value;

            // Validasi alamat jika pilih "antar"
            if (metodePengambilan === 'antar' && !idAlamat) {
                e.preventDefault();
                alert('Anda harus menambahkan alamat terlebih dahulu untuk metode pengiriman "Diantar". Silakan tambah alamat di halaman profil.');
                return false;
            }

            // Validasi waktu
            const waktuSelesai = document.querySelector('input[name="waktu_selesai"]').value;
            if (!waktuSelesai) {
                e.preventDefault();
                alert('Mohon pilih tanggal dan waktu pengambilan pesanan');
                return false;
            }
        });
    </script>

</body>

</html>