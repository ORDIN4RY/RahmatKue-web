<?php
session_start();

require 'auth/koneksi.php';

// =====================
// 1️⃣ CEK & AMBIL PRODUK DARI SUPABASE
// =====================
$id_produk = $_GET['id'] ?? null;
if (!$id_produk) die("ID Produk tidak ditemukan");

// Ambil produk
try {
    $response = $client->get("/rest/v1/produk", [
        'query' => [
            'id_produk' => 'eq.' . $id_produk,
            'select' => '*,kategori(nama_kategori)'
        ]
    ]);
    $product = json_decode($response->getBody(), true);
    if (empty($product)) die("Produk tidak ditemukan");
    $product = $product[0];
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}

// Format data
$nama = htmlspecialchars($product['nama_produk']);
$deskripsi = htmlspecialchars($product['deskripsi']);
$harga = $product['harga'];
$kategori = htmlspecialchars($product['kategori']['nama_kategori']);
$foto = htmlspecialchars($product['foto_produk'] ?? 'assets/img/no-image.png');

// =====================
// TENTUKAN MINIMAL PEMBELIAN BERDASARKAN KATEGORI
// =====================
$min_pembelian = ($kategori === 'cake') ? 1 : 15;

// Cek URL foto
if (!filter_var($foto, FILTER_VALIDATE_URL)) {
    $foto = SUPABASE_STORAGE_URL . '/images/produk/' . rawurlencode($foto);
}

// Pastikan ada session keranjang
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}


// ================================================
// 2️⃣ FUNGSI UNTUK INSERT / UPDATE KERANJANG SUPABASE
// ================================================
function addToCartSupabase($client, $data)
{
    try {
        // Cek apakah item sudah ada
        $check = $client->get("/rest/v1/keranjang", [
            'query' => [
                'id_user'   => 'eq.' . $data['id_user'],
                'id_produk' => 'eq.' . $data['id_produk'],
                'select'    => '*'
            ]
        ]);

        $exists = json_decode($check->getBody(), true);

        // Jika sudah ada → update jumlah
        if (!empty($exists)) {
            $id_keranjang = $exists[0]['id_keranjang'];
            $newQty = $exists[0]['jumlah'] + $data['jumlah'];

            $client->patch("/rest/v1/keranjang?id_keranjang=eq.$id_keranjang", [
                'json' => ['jumlah' => $newQty],
                'headers' => ['Prefer' => 'return=representation']
            ]);

            return true;
        }

        // Jika belum ada → insert baru
        $client->post("/rest/v1/keranjang", [
            'json' => $data,
            'headers' => ['Prefer' => 'return=representation']
        ]);

        return true;

    } catch (Exception $e) {
        return "❌ Gagal insert keranjang: " . $e->getMessage();
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
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id_user = $_SESSION['id'];
    
    if (empty($id_user)) {
        $_SESSION['message'] = "❌ Anda harus login terlebih dahulu.";
        header("Location: login.php?redirect=produk-detail.php?id=$id_produk");
        exit;
    }

    $quantity = (int)$_POST['quantity'];

    // ===============================
    // VALIDASI MINIMAL PEMBELIAN BERDASARKAN KATEGORI
    // ===============================
    if ($kategori === 'cake') {
        // Untuk kategori Kue: minimal 1
        if ($quantity < 1) {
            $_SESSION['message'] = "❌ Jumlah produk untuk kategori Kue minimal 1.";
            header("Location: produk-detail.php?id=$id_produk");
            exit;
        }
    } else {
        // Untuk kategori selain Kue: minimal 15
        if ($quantity < 15) {
            $_SESSION['message'] = "❌ Jumlah produk untuk kategori $kategori minimal 15.";
            header("Location: produk-detail.php?id=$id_produk");
            exit;
        }
    }

    // Tambah ke SESSION (tetap)
    $item = [
        'id' => uniqid(),
        'id_produk' => $id_produk,
        'nama' => $nama,
        'harga' => $harga,
        'foto' => $foto,
        'quantity' => $quantity
    ];

    // cek apakah produk sama sudah ada di session
    $found = false;
    foreach ($_SESSION['cart'] as &$cart_item) {
        if ($cart_item['id_produk'] === $id_produk) {
            $cart_item['quantity'] += $quantity;
            $found = true;
            break;
        }
    }

    if (!$found) {
        $_SESSION['cart'][] = $item;
    }

    // ========================================
    // ⬇⬇ INSERT KE SUPABASE TABEL KERANJANG
    // ========================================
    $dataKeranjang = [
        'id_user'   => $id_user,
        'id_produk' => $id_produk, // UUID
        'jumlah'    => $quantity,
        'id_paket'  => null
    ];

    $result = addToCartSupabase($client, $dataKeranjang);

    if ($result !== true) {
        $_SESSION['message'] = $result;
        header("Location: produk-detail.php?id=$id_produk");
        exit;
    }


    // Tombol beli langsung
    if (isset($_POST['buy_now'])) {
        header("Location: pesan.php");
        exit;
    } else {
        $_SESSION['message'] = "🛒 Produk berhasil ditambahkan ke keranjang!";
        header("Location: produk-detail.php?id=$id_produk");
        exit;
    }
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
                <img src="<?= $foto ?>" alt="<?= $nama ?>" class="img-fluid rounded">
            </div>

            <div id="produk-detail-form" class="col-md-6">
                <h2><?= $nama ?></h2>
                <p><?= $deskripsi ?></p>
                <h4>Rp <?= number_format($harga, 0, ',', '.') ?></h4>
                <p><b>Kategori:</b> <?= $kategori ?></p>

                <!-- Informasi Minimal Pembelian -->
                <?php if ($kategori === 'cake'): ?>
                    <div class="min-order-info">
                        <i class="bi bi-info-circle"></i> 
                        <strong>Minimal Pembelian:</strong> 1 pcs (kategori kue)
                    </div>
                <?php else: ?>
                    <div class="min-order-warning">
                        <i class="bi bi-exclamation-triangle"></i> 
                        <strong>Minimal Pembelian:</strong> 15 pcs (kategori <?= $kategori ?>)
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <?php if ($kategori === 'Kue'): ?>
                        <div class="mb-3">
                            <label for="size" class="form-label">Ukuran</label>
                            <select name="size" id="size" class="form-select">
                                <option value="16">Round 16 cm</option>
                                <option value="20">Round 20 cm</option>
                                <option value="24">Round 24 cm</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="wording" class="form-label">Tulisan di kue</label>
                            <input type="text" name="wording" id="wording" class="form-control" maxlength="25" placeholder="Max. 25 karakter">
                        </div>
                    <?php endif; ?>

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

                    <div class="d-flex gap-3 mb-3">
                        <button type="submit" name="add_to_cart" class="btn-submit">
                            <i class="bi bi-cart-plus"></i> Tambahkan ke Keranjang
                        </button>
                        <button type="submit" name="buy_now" class="btn btn-success">
                            <i class="bi bi-lightning-fill"></i> Beli Sekarang
                        </button>
                    </div>

                    <p class="wishlist"><i class="bi bi-heart"></i> Tambahkan ke wishlist</p>
                </form>
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