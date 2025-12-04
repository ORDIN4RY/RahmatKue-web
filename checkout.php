<?php
session_start();
require 'auth/koneksi.php';

// CEK LOGIN
if (!isset($_SESSION['id'])) {
    header("Location: login.php?redirect=checkout.php");
    exit;
}

$id_user        = $_SESSION['id'];
// $id_alamat = $_POST['id_alamat'] ?? $selected_id_alamat ?? null;
$access_token   = $_SESSION['access_token'] ?? null;

function hitungJarakKm($lat1, $lon1, $lat2, $lon2)
{
    $earthRadius = 6371; // KM

    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);

    $a = sin($dLat / 2) * sin($dLat / 2) +
        cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
        sin($dLon / 2) * sin($dLon / 2);

    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    return $earthRadius * $c;
}


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
            'select' => 'id_keranjang,id_produk,id_paket,jumlah,produk(nama_produk,harga,foto_produk),paket(nama_paket,harga_paket,foto_paket)'
        ]
    ]);

    $checkout_items = json_decode($response->getBody(), true);
} catch (Exception $e) {
    die("Gagal memuat checkout: " . $e->getMessage());
}

// ==========================
// HITUNG TOTAL SUBTOTAL
// ==========================
$total_harga = 0;

foreach ($checkout_items as $item) {
    $harga = !empty($item['produk'])
        ? ($item['produk']['harga'] ?? 0)  // Fallback ke 0 jika null
        : ($item['paket']['harga_paket'] ?? 0);  // Fallback ke 0 jika null

    $total_harga += ($harga * $item['jumlah']);
}

// DP minimal (pastikan total_harga > 0)
$dp_minimal = ($total_harga > 0) ? ceil($total_harga * 0.5) : 0;

// ==========================
// AMBIL ALAMAT USER
// ==========================
$alamat = getAlamatCheckout($id_user);

// Jika tidak ada alamat, redirect ke profil
if (empty($alamat)) {
    header("Location: profil.php?error=no_address");
    exit;
}

// Cari alamat utama untuk auto-select, atau yang pertama
$selected_id_alamat = null;
$selected_address_text = 'Belum dipilih';
foreach ($alamat as $a) {
    if (!empty($a['alamat_utama']) && $a['alamat_utama'] == 1) {
        $selected_id_alamat = $a['id_alamat'];
        $selected_address_text = htmlspecialchars($a['nama_lengkap']) . ' — ' . htmlspecialchars($a['alamat_rumah']) . ' (Utama)';
        break;
    }
}
if (!$selected_id_alamat && !empty($alamat)) {
    $selected_id_alamat = $alamat[0]['id_alamat'];
    $selected_address_text = htmlspecialchars($alamat[0]['nama_lengkap']) . ' — ' . htmlspecialchars($alamat[0]['alamat_rumah']);
}

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

// ========================== Ongkir ==========================
// ========================== ONGKIR FIX ==========================

// PASTIKAN id_alamat SELALU AMBIL DARI HIDDEN INPUT
$id_alamat = $_POST['id_alamat'] ?? $selected_id_alamat;

// METODE PENGAMBILAN (DEFAULT DIAMBIL)
$metode_pengambilan = $_POST['metode_pengambilan'] ?? 'diambil';

$lat_toko = -8.163745;
$lng_toko = 113.445406;

$ongkir = 0;

// AMBIL ALAMAT USER DENGAN BENAR
$alamatUser = getSupabaseData("alamat", [
    "id_alamat" => "eq." . $id_alamat
]);

if (!empty($alamatUser)) {

    $latUser = $alamatUser[0]['latitude'] ?? 0;
    $lonUser = $alamatUser[0]['longitude'] ?? 0;

    if ($metode_pengambilan === "diantar" && $latUser != 0 && $lonUser != 0) {

        $jarak = hitungJarakKm($lat_toko, $lng_toko, $latUser, $lonUser);
        $ongkir = ceil($jarak) * 1000;

        error_log("ONGKIR DIHITUNG: $jarak KM = Rp $ongkir");
    }
}

// VALIDASI ONGKIR
if ($ongkir < 0 || $ongkir > 500000) {
    die("Ongkir tidak valid");
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
    <link rel="stylesheet" href="./assets/css/profil.css">
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

            <input type="hidden" name="lat_user" id="lat">
            <input type="hidden" name="lng_user" id="lng">

            <!-- NILAI YANG BENAR (tidak lagi NULL) -->
            <input type="hidden" id="total_harga" name="total_harga" value="<?= $total_harga ?>">
            <input type="hidden" id="dp_minimal" name="dp_minimal" value="<?= $dp_minimal ?>">
            <input type="hidden" id="ongkir_hidden" name="ongkir" value="<?= $ongkir ?>"

            <!-- Hidden untuk id_alamat -->
            <input type="hidden" name="id_alamat" id="selected_id_alamat" value="<?= htmlspecialchars($selected_id_alamat) ?>">

            <?php foreach ($checkout_items as $item): ?>
                <input type="hidden"
                    name="keranjang[<?= $item['id_keranjang'] ?>][harga]"
                    value="<?= !empty($item['paket']) ? ($item['paket']['harga_paket'] ?? 0) : ($item['produk']['harga'] ?? 0) ?>">
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

                        <div class="address-selection">
                            <button type="button" class="btn btn-outline-primary"
                                data-bs-toggle="modal" data-bs-target="#addressModal">
                                <i class="fas fa-map-marker-alt"></i> Pilih Alamat
                            </button>
                            <p id="selected_address_text" class="mt-2 text-muted">
                                <?= $selected_address_text ?>
                            </p>
                        </div>
                    </div>

                    <!-- Modal untuk Pilih Alamat -->
                    <div class="modal fade" id="addressModal" tabindex="-1" aria-labelledby="addressModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="addressModalLabel">Pilih Alamat Pengiriman</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <?php foreach ($alamat as $a): ?>
                                            <div class="col-md-6 mb-3">
                                                <div class="card address-card" data-id="<?= htmlspecialchars($a['id_alamat']) ?>"
                                                    onclick="selectAddress('<?= htmlspecialchars($a['id_alamat']) ?>', '<?= htmlspecialchars($a['nama_lengkap']) ?> — <?= htmlspecialchars($a['alamat_rumah']) ?><?= !empty($a['alamat_utama']) ? ' (Utama)' : '' ?>')">
                                                    <div class="card-body">
                                                        <h6 class="card-title">
                                                            <?= htmlspecialchars($a['nama_lengkap']) ?>
                                                            <?= !empty($a['alamat_utama']) ? '<span class="badge bg-primary">Utama</span>' : '' ?>
                                                        </h6>
                                                        <p class="card-text"><?= htmlspecialchars($a['alamat_rumah']) ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- METODE PENGAMBILAN -->
                    <div class="form-card">
                        <h5 class="section-title">
                            <i class="fas fa-truck"></i> Metode Pengambilan
                        </h5>
                        <!-- DIAMBIL SENDIRI -->
                        <div class="payment-method">
                            <input type="radio" name="metode_pengambilan" value="diambil" id="diambil" checked onchange="updateOngkir()">
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
                            <input type="radio" name="metode_pengambilan" value="diantar" id="diantar" onchange="updateOngkir()">
                            <label for="diantar">
                                <div class="payment-icon"><i class="fas fa-motorcycle"></i></div>
                                <div class="payment-info">
                                    <div class="payment-name">Diantar</div>
                                    <div class="payment-desc">Kirim ke alamat</div>
                                </div>
                                <strong class="ms-auto" id="ongkir_display">
                                    Ongkir : Rp 0
                                </strong>
                            </label>
                        </div>
                    </div>

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
                        foreach ($checkout_items as $item):
                            if (!empty($item['produk'])) {
                                $nama = $item['produk']['nama_produk'];
                                $harga = $item['produk']['harga'] ?? 0;
                                $foto = $item['produk']['foto_produk'] ?? 'assets/img/no-image.png';
                            } else {
                                $nama = $item['paket']['nama_paket'];
                                $harga = $item['paket']['harga_paket'] ?? 0;
                                $foto = $item['paket']['foto_paket'] ?? 'assets/img/no-image.png';
                            }
                            if (!filter_var($foto, FILTER_VALIDATE_URL)) {
                                $foto = SUPABASE_STORAGE_URL . '/images/produk/' . rawurlencode($foto);
                            }
                        ?>
                            <div class="product-item">
                                <div class="product-image">
                                    <img src="<?= $foto ?>" alt="<?= htmlspecialchars($nama) ?>">
                                </div>

                                <div class="product-details">
                                    <div class="product-name"><?= htmlspecialchars($nama) ?></div>
                                    <div class="product-quantity">
                                        Qty: <?= $item['jumlah'] ?> × Rp<?= number_format($harga, 0, ',', '.') ?>
                                    </div>
                                </div>

                                <div class="product-price">
                                    Rp<?= number_format($harga * $item['jumlah'], 0, ',', '.') ?>
                                </div>
                            </div>
                        <?php endforeach; ?>

                        <div class="price-summary">
                            <div class="price-row">
                                <span>Subtotal Produk</span>
                                <span>Rp<?= number_format($total_harga, 0, ',', '.') ?></span>
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
                                <?php $grand_total = $total_harga + $ongkir; ?>
                                <span>Rp<?= number_format($grand_total, 0, ',', '.') ?></span>

                            </div>
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
        // Fungsi untuk pilih alamat di modal
        function selectAddress(id, text) {
            document.getElementById('selected_id_alamat').value = id;
            document.getElementById('selected_address_text').innerText = 'Dipilih: ' + text;
            // Tutup modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('addressModal'));
            if (modal) modal.hide();
        }

        // Validasi form
        document.getElementById('checkoutForm').addEventListener('submit', function(e) {
            const idAlamat = document.getElementById('selected_id_alamat').value;
            const metodePengambilan = document.querySelector('input[name="metode_pengambilan"]:checked').value;

            if (!idAlamat) {
                e.preventDefault();
                alert('Silakan pilih alamat sebelum melanjutkan.');
                return;
            }

            const waktuSelesai = document.querySelector('input[name="waktu_selesai"]').value;
            if (!waktuSelesai) {
                e.preventDefault();
                alert('Mohon pilih tanggal dan waktu pengambilan pesanan');
                return;
            }

            // Logging untuk debugging
            console.log('Form submitted with id_alamat:', idAlamat, 'metode:', metodePengambilan);
        });
    </script>

    <!-- CSS Tambahan untuk Modal -->
    <style>
        .address-card {
            cursor: pointer;
            transition: border-color 0.3s;
        }

        .address-card:hover {
            border-color: #007bff;
        }

        .address-selection {
            margin-bottom: 1rem;
        }
    </style>
</body>

</html>