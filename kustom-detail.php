<?php
session_start();

require 'auth/koneksi.php';

// =====================
// 1️⃣ CEK & AMBIL PRODUK DARI SUPABASE
// =====================
$id_paket = $_GET['id'] ?? null;
if (!$id_paket) die("ID Produk tidak ditemukan");

// Ambil produk + relasi wadah
try {
    $response = $client->get("/rest/v1/paket", [
        'query' => [
            'id_paket' => 'eq.' . $id_paket,
            'select' => '*, wadah(*)'
        ]
    ]);

    $productPaket = json_decode($response->getBody(), true);

    if (empty($productPaket)) {
        die("Produk tidak ditemukan - data paket kosong");
    }

    $productPaket = $productPaket[0];
    $id_wadah = $productPaket['id_wadah'] ?? null;
} catch (Exception $e) {
    die("Error Supabase: " . $e->getMessage());
}


// Format data
$nama = htmlspecialchars($productPaket['nama_paket']);
$harga = $productPaket['harga_paket'];
$deskripsi = htmlspecialchars($productPaket['deskripsi']);
$diskon = htmlspecialchars($productPaket['diskon']);
$wadah = strtolower($productPaket['wadah']['nama_wadah'] ?? 'unknown');
$foto = htmlspecialchars($productPaket['foto_paket'] ?? 'assets/img/no-image.png');


// Cek URL foto
if (!filter_var($foto, FILTER_VALIDATE_URL)) {
    $foto = SUPABASE_STORAGE_URL . '/images/produk/' . rawurlencode($foto);
}

// Pastikan ada session keranjang
$min_pembelian = ($wadah === 'kotak') ? 15 : 1;

// pastikan session cart ada
if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}


// ================================================
// 2️⃣ FUNGSI UNTUK INSERT / UPDATE KERANJANG SUPABASE
// ================================================
function addToCartPaket($client, $data)
{
    try {
        // validasi basic
        if (empty($data['id_user']) || empty($data['id_paket']) || empty($data['jumlah'])) {
            return "❌ Parameter id_user, id_paket, jumlah wajib diisi.";
        }

        // 1) cek apakah sudah ada di keranjang (per user + paket)
        $check = $client->get("/rest/v1/keranjang", [
            'query' => [
                'id_user'  => 'eq.' . $data['id_user'],
                'id_paket' => 'eq.' . $data['id_paket'],
                'select'   => 'id_keranjang,jumlah'
            ]
        ]);
        $exists = json_decode($check->getBody(), true);

        if (!empty($exists)) {
            // update jumlah
            $id_keranjang = $exists[0]['id_keranjang'];
            $newQty = (int)$exists[0]['jumlah'] + (int)$data['jumlah'];

            $client->patch("/rest/v1/keranjang?id_keranjang=eq.$id_keranjang", [
                'json' => ['jumlah' => $newQty],
                'headers' => ['Prefer' => 'return=representation']
            ]);

            return true;
        }

        // insert baru
        $client->post("/rest/v1/keranjang", [
            'json' => [
                'id_user'  => $data['id_user'],
                'id_paket' => $data['id_paket'],
                'jumlah'   => $data['jumlah']
            ],
            'headers' => ['Prefer' => 'return=representation']
        ]);

        return true;
    } catch (Exception $e) {
        // kembalikan pesan kesalahan untuk ditampilkan ke user
        return "❌ Gagal insert keranjang paket: " . $e->getMessage();
    }
}



if (isset($_GET['hapus'])) {

    $id_keranjang = $_GET['hapus'];

    // Fungsi hapus
    function hapusKeranjang($id)
    {
        return deleteSupabaseData("keranjang", "id", $id);
    }

    $hapus = hapusKeranjang($id_keranjang);

    if ($hapus) {
        header("Location: keranjang.php?status=deleted");
        exit;
    } else {
        header("Location: keranjang.php?status=error");
        exit;
    }
}


// ===============================
// 3️⃣ PROSES SAAT USER MENAMBAH KE KERANJANG
// ===============================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {

    $id_user = $_SESSION['id'] ?? null;

    if (empty($id_user)) {
        $_SESSION['message'] = "❌ Anda harus login terlebih dahulu.";
        header("Location: login.php?redirect=paket-detail.php?id=" . urlencode($id_paket));
        exit;
    }

    // ambil quantity, validasi numeric & minimal pembelian
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;

    if ($quantity <= 0) {
        $_SESSION['message'] = "❌ Jumlah tidak valid.";
        header("Location: paket-detail.php?id=" . urlencode($id_paket));
        exit;
    }

    if ($quantity < $min_pembelian) {
        $_SESSION['message'] = "❌ Minimal pembelian untuk paket ini adalah $min_pembelian pcs.";
        header("Location: paket-detail.php?id=" . urlencode($id_paket));
        exit;
    }

    // tambah ke SESSION cart (untuk fitur cart lokal)
    $item = [
        'uid'         => uniqid(),
        'id_paket'    => $id_paket,
        'id_wadah'    => $id_wadah,
        'nama_paket'  => $nama,
        'harga_paket' => $harga,
        'deskripsi'   => $deskripsi,
        'diskon'      => $diskon,
        'foto'        => $foto,
        'quantity'    => $quantity
    ];

    // jika sudah ada di session, tambahkan qty
    $found = false;
    foreach ($_SESSION['cart'] as &$cart_item) {
        if (isset($cart_item['id_paket']) && $cart_item['id_paket'] === $id_paket) {
            $cart_item['quantity'] += $quantity;
            $found = true;
            break;
        }
    }
    if (!$found) {
        $_SESSION['cart'][] = $item;
    }

    // insert ke Supabase keranjang
    $dataKeranjang = [
        'id_user'  => $id_user,
        'id_paket' => $id_paket,
        'jumlah'   => $quantity
    ];

    $result = addToCartPaket($client, $dataKeranjang);

    if ($result !== true) {
        // error string dikembalikan dari fungsi
        $_SESSION['message'] = $result;
        header("Location: paket-detail.php?id=" . urlencode($id_paket));
        exit;
    }

    // sukses
    $_SESSION['message'] = "🛒 Paket berhasil ditambahkan ke keranjang!";
    header("Location: keranjang.php");
    exit;
}

if (isset($_POST['checkout'])) {
    $result = checkoutUser($id_user, $access_token);

    if ($result['success']) {
        header("Location: sukses.php?id=" . $result['id_transaksi']);
        exit;
    } else {
        $message = $result['message'];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['buy_now'])) {

    $id_user = $_SESSION['id'] ?? null;
    if (empty($id_user)) {
        $_SESSION['message'] = "❌ Anda harus login terlebih dahulu.";
        header("Location: login.php?redirect=paket-detail.php?id=" . urlencode($id_paket));
        exit;
    }

    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;
    if ($quantity <= 0 || $quantity < $min_pembelian) {
        $_SESSION['message'] = "❌ Jumlah tidak valid atau kurang dari minimal pembelian ($min_pembelian).";
        header("Location: paket-detail.php?id=" . urlencode($id_paket));
        exit;
    }

    // tambahkan ke supabase keranjang (sama seperti add)
    $dataKeranjang = [
        'id_user'  => $id_user,
        'id_paket' => $id_paket,
        'jumlah'   => $quantity
    ];

    $result = addToCartPaket($client, $dataKeranjang);

    if ($result !== true) {
        $_SESSION['message'] = $result;
        header("Location: paket-detail.php?id=" . urlencode($id_paket));
        exit;
    }

    // redirect ke halaman pesan / checkout
    header("Location: pesan.php");
    exit;
}


// =====================
// 4️⃣ AMBIL PESAN NOTIFIKASI
// =====================
$message = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $nama ?> - Rahmat Kue</title>
    <link rel="icon" type="image/x-icon" href="assets/img/icon.png">
    <link rel="stylesheet" href="assets/css/produk.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .min-order-info {
            background-color: #f8f9fa;
            border-left: 4px solid #0d6efd;
            padding: 10px 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            font-size: 0.9rem;
        }

        .min-order-warning {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 10px 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            font-size: 0.9rem;
        }
    </style>
</head>

<body>

    <?php include 'component/navbar.php'; ?>

    <div class="container my-5">
        <?php if (!empty($message)) : ?>
            <div class="alert alert-<?= strpos($message, '❌') !== false ? 'danger' : 'success' ?> alert-dismissible fade show text-center" role="alert">
                <?= $message ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row align-items-center">
            <div class="col-md-6">
                <img src="<?= htmlspecialchars($foto) ?>"
                    alt="<?= htmlspecialchars($nama) ?>"
                    class="img-fluid rounded">
            </div>

            <div id="produk-detail-form" class="col-md-6">

                <h2><?= htmlspecialchars($nama) ?></h2>
                <p><?= nl2br(htmlspecialchars($deskripsi)) ?></p>
                <h4>Rp <?= number_format($harga, 0, ',', '.') ?></h4>

                <?php if ($wadah === 'kotak'): ?>
                    <div class="min-order-info">
                        <i class="bi bi-info-circle"></i>
                        <strong>Minimal Pembelian:</strong> 15 pcs (kategori kotak)
                    </div>
                <?php else: ?>
                    <div class="min-order-warning">
                        <i class="bi bi-exclamation-triangle"></i>
                        <strong>Minimal Pembelian:</strong> 1 pcs
                        <?php printf("untuk kategori %s", htmlspecialchars($wadah)); ?>
                    </div>
                <?php endif; ?>


                <!-- FORM TAMBAH KE KERANJANG -->
                <form method="POST">

                    <div class="mb-3">
                        <label for="quantity" class="form-label">Jumlah</label>
                        <input type="number"
                            name="quantity"
                            id="quantity"
                            value="<?= $min_pembelian ?>"
                            min="<?= $min_pembelian ?>"
                            class="form-control"
                            style="width: 150px;"
                            required>

                        <small class="form-text text-muted">
                            Minimal pembelian: <?= $min_pembelian ?> pcs
                        </small>
                    </div>

                    <button type="submit" name="add_to_cart" class="btn-submit">
                        <i class="bi bi-cart-plus"></i> Tambahkan ke Keranjang
                    </button>

                </form>

                <!-- FORM BELI SEKARANG (TERPISAH) -->
                <form method="GET" action="checkout.php" class="mt-3">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($id_paket) ?>">
                    <input type="hidden" name="quantity" id="checkout-quantity">

                    <button type="submit" name="buy_now" class="btn btn-success" onclick="
                        document.getElementById('checkout-quantity').value = 
                        document.getElementById('quantity').value;
            ">
                        <i class="bi bi-lightning-fill"></i> Beli Sekarang
                    </button>
                </form>

                <p class="wishlist mt-3">
                    <i class="bi bi-heart"></i> Tambahkan ke wishlist
                </p>

            </div>
        </div>

    </div>

    <?php include 'component/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Validasi client-side untuk minimal pembelian
        document.addEventListener('DOMContentLoaded', function() {
            const quantityInput = document.getElementById('quantity');
            const form = document.querySelector('form');

            form.addEventListener('submit', function(e) {
                const minOrder = <?= $min_pembelian ?>;
                const quantity = parseInt(quantityInput.value);

                if (quantity < minOrder) {
                    e.preventDefault();
                    alert('Minimal pembelian untuk produk ini adalah ' + minOrder + ' pcs');
                    quantityInput.focus();
                }
            });
        });
    </script>

</body>

</html>