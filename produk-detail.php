<?php
session_start();
require 'auth/koneksi.php';

// =====================
// 1️⃣ CEK & AMBIL PRODUK DARI SUPABASE
// =====================
$id_produk = $_GET['id'] ?? null;
if (!$id_produk) die("ID Produk tidak ditemukan");

$query = sprintf(
    "produk?id_produk=eq.%s&select=*,kategori(nama_kategori)",
    urlencode($id_produk)
);

$product = getSupabaseData($query);
if (empty($product)) die("Produk tidak ditemukan");
$product = $product[0];

// Format data
$nama = htmlspecialchars($product['nama_produk']);
$deskripsi = htmlspecialchars($product['deskripsi']);
$harga = $product['harga'];
$kategori = htmlspecialchars($product['kategori']['nama_kategori']);
$foto = htmlspecialchars($product['foto_produk'] ?? 'assets/img/no-image.png');

if (!filter_var($foto, FILTER_VALIDATE_URL)) {
    $foto = SUPABASE_STORAGE_URL . '/images/produk/' . rawurlencode($foto);
}

// Pastikan ada session keranjang
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// =====================
// 2️⃣ TAMBAHKAN PRODUK KE KERANJANG
// =====================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $quantity = (int)$_POST['quantity'];
    $size = $_POST['size'] ?? null;
    $wording = $_POST['wording'] ?? null;
    $id_user = $_SESSION['user_id'] ?? null;
    $id_produk = $_GET['id'] ?? null;

    if (empty($id_user) || empty($id_produk) || $quantity <= 0) {
        $_SESSION['message'] = "❌ Data tidak lengkap untuk menambahkan ke keranjang.";
    } else {
        // Simpan ke session
        $item = [
            'id_produk' => $id_produk,
            'nama_produk' => $nama,
            'harga' => $harga,
            'foto_produk' => $foto,
            'quantity' => $quantity,
            'size' => $size,
            'wording' => $wording,
        ];

        $found = false;
        foreach ($_SESSION['cart'] as &$cart_item) {
            if (
                $cart_item['id_produk'] === $id_produk &&
                $cart_item['size'] === $size &&
                $cart_item['wording'] === $wording
            ) {
                $cart_item['quantity'] += $quantity;
                $found = true;
                break;
            }
        }

        if (!$found) {
            $_SESSION['cart'][] = $item;
        }

        // Simpan ke Supabase
        $data = [
            "id_user" => $id_user,
            "id_produk" => $id_produk,
            "id_paket" => null,
            "jumlah" => $quantity
        ];

        $insertResult = insertSupabaseData('keranjang', $data);

        if ($insertResult === false) {
            $_SESSION['message'] = "⚠️ Gagal menyimpan ke database Supabase.";
        } else {
            $_SESSION['message'] = "✅ Produk berhasil ditambahkan ke keranjang!";
        }
    }

    header("Location: produk-detail.php?id=$id_produk");
    exit;
}




// =====================
// 4️⃣ HAPUS ITEM DARI KERANJANG
// =====================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hapus_item'])) {
    $hapus_id = $_POST['hapus_id'];

    foreach ($_SESSION['cart'] as $key => $item) {
        if ($item['id'] === $hapus_id) {
            unset($_SESSION['cart'][$key]);
            $_SESSION['cart'] = array_values($_SESSION['cart']);
            break;
        }
    }

    header("Location: produk-detail.php?id=$id_produk&status=deleted");
    exit;
}

// =====================
// 5️⃣ NOTIFIKASI STATUS
// =====================
$message = '';
if (isset($_GET['status'])) {
    if ($_GET['status'] === 'added') {
        $message = "Produk berhasil ditambahkan ke keranjang!";
    } elseif ($_GET['status'] === 'deleted') {
        $message = "Produk berhasil dihapus dari keranjang!";
    }
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
</head>

<body>

    <?php include 'component/navbar.php'; ?>

    <div class="container my-5">
        <?php if (!empty($message)) : ?>
            <div class="alert alert-success text-center"><?= $message ?></div>
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
                        <input type="number" name="quantity" id="quantity" value="1" min="1" class="form-control" style="width: 100px;">
                    </div>

                    <div class="d-flex gap-3 mb-3">
                        <button type="submit" name="add_to_cart" class="btn-submit">
                            <i class="bi bi-cart-plus"></i> Tambahkan ke Keranjang
                        </button>
                        <a href="checkout.php" class="btn btn-success">
                            <i class="bi bi-lightning-fill"></i> Beli Sekarang
                        </a>
                    </div>

                    <p class="wishlist"><i class="bi bi-heart"></i> Tambahkan ke wishlist</p>
                </form>
            </div>
        </div>
    </div>

    <?php include 'component/footer.php'; ?>
</body>

</html>