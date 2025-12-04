<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<?php
require 'auth/koneksi.php';

// ==========================
// CEK LOGIN
// ==========================
if (!isset($_SESSION['id'])) {
    $_SESSION['message'] = "❌ Anda harus login terlebih dahulu.";
    header("Location: login.php?redirect=keranjang.php");
    exit;
}

$id_user = $_SESSION['id'];
$access_token = $_SESSION['access_token'] ?? null;

// ==========================
// AMBIL DATA KERANJANG
// ==========================
try {
    $response = $client->get("/rest/v1/keranjang", [
        'headers' => [
            'apikey'        => SUPABASE_KEY,
            'Authorization' => "Bearer $access_token"
        ],
        'query' => [
            'id_user' => 'eq.' . $id_user,
            'select' => 'id_keranjang,id_produk,id_paket,jumlah,produk(nama_produk,harga,foto_produk),paket(nama_paket,harga_paket,foto_paket)'
        ]
    ]);

    $cart_items = json_decode($response->getBody(), true);
} catch (Exception $e) {
    // Handle JWT expired
    if (strpos($e->getMessage(), 'JWT expired') !== false || 
        strpos($e->getMessage(), 'PGRST303') !== false ||
        strpos($e->getMessage(), '401') !== false) {
        session_destroy();
        session_start();
        $_SESSION['message'] = "⏰ Sesi Anda telah berakhir. Silakan login kembali.";
        header("Location: login.php?redirect=keranjang.php");
        exit;
    }
    die("Gagal memuat keranjang: " . $e->getMessage());
}

// ==========================
// HAPUS ITEM KERANJANG
// ==========================
if (isset($_GET['hapus'])) {

    $id_keranjang = $_GET['hapus'];

    function hapusKeranjang($id)
    {
        return deleteSupabaseData("keranjang", "id_keranjang", $id);
    }

    if (hapusKeranjang($id_keranjang)) {
        header("Location: keranjang.php?status=deleted");
        exit;
    } else {
        header("Location: keranjang.php?status=error");
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_selected'])) {

    if (!empty($_POST['selected_ids'])) {

        $ids = explode(",", $_POST['selected_ids']);

        foreach ($ids as $id) {
            deleteSupabaseData("keranjang", "id_keranjang", trim($id), $accessToken);
        }

        header("Location: keranjang.php?status=deleted");
        exit;
    }
}

// ==========================
// PROSES CHECKOUT
// ==========================
if (isset($_POST['checkout'])) {
    $result = checkoutUser($id_user, $access_token);

    if ($result['success']) {
        header("Location: sukses.php?id=" . $result['id_transaksi']);
        exit;
    } else {
        $message = $result['message'];
    }
}



// ==========================
// HITUNG TOTAL SEMUA
// ==========================
$total_semua = 0;

foreach ($cart_items as $item) {
    if (!empty($item['paket'])) {
        $total_semua += $item['paket']['harga_paket'] * $item['jumlah'];
    } else {
        $total_semua += $item['produk']['harga'] * $item['jumlah'];
    }
}


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
    <title>Keranjang - Rahmat Kue</title>
    <link rel="icon" type="image/x-icon" href="assets/img/icon.png">
    <link rel="stylesheet" href="assets/css/pesan.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Custom CSS untuk Keranjang -->
    <style>
        body {
            background-color: #fff5e6;
            padding-bottom: 120px;
        }

        .container {
            max-width: 1400px;
        }

        h2 {
            color: #8e5e48;
            font-weight: 700;
            margin-bottom: 30px;
        }

        /* Cart Container */
        .cart-container {
            background-color: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 12px rgba(142, 94, 72, 0.1);
        }

        /* Cart Header */
        .cart-header {
            display: grid;
            grid-template-columns: 60px 2fr 1fr 1fr 1fr 120px;
            padding: 20px 25px;
            background: linear-gradient(135deg, #8e5e48 0%, #a66d5a 100%);
            color: white;
            font-weight: 600;
            font-size: 15px;
            gap: 15px;
        }

        /* Product Item */
        .product-item {
            display: grid;
            grid-template-columns: 60px 2fr 1fr 1fr 1fr 120px;
            padding: 25px;
            align-items: center;
            gap: 15px;
            border-bottom: 1px solid #f0f0f0;
            transition: all 0.3s ease;
        }

        .product-item:hover {
            background-color: #fffbf5;
            transform: translateX(5px);
        }

        /* Checkbox */
        .checkbox {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .checkbox-wrapper input[type="checkbox"] {
            appearance: none;
            width: 24px;
            height: 24px;
            border: 2px solid #d0d0d0;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
        }

        .checkbox-wrapper input[type="checkbox"]:hover {
            border-color: #8e5e48;
            transform: scale(1.1);
        }

        .checkbox-wrapper input[type="checkbox"]:checked {
            background-color: #8e5e48;
            border-color: #8e5e48;
        }

        .checkbox-wrapper input[type="checkbox"]:checked::after {
            content: "✓";
            position: absolute;
            color: white;
            font-size: 16px;
            font-weight: bold;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        /* Product Info */
        .product-info {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .product-image {
            width: 90px;
            height: 90px;
            border: 2px solid #f0f0f0;
            border-radius: 10px;
            overflow: hidden;
            flex-shrink: 0;
            transition: all 0.3s ease;
        }

        .product-image:hover {
            border-color: #8e5e48;
            transform: scale(1.05);
        }

        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .product-title {
            font-size: 16px;
            color: #333;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .product-variant {
            color: #666;
            font-size: 13px;
            margin-top: 5px;
            padding: 4px 8px;
            background: #f8f8f8;
            border-radius: 4px;
            display: inline-block;
        }

        /* Price */
        .price {
            color: #333;
            font-size: 17px;
            font-weight: 600;
        }

        .total-price {
            color: #8e5e48;
            font-size: 18px;
            font-weight: 700;
        }

        /* Quantity Control */
        .quantity-control {
            display: flex;
            align-items: center;
            gap: 12px;
            justify-content: center;
        }

        .qty-btn {
            width: 36px;
            height: 36px;
            border: 2px solid #e0e0e0;
            background-color: white;
            cursor: pointer;
            font-size: 20px;
            color: #8e5e48;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .qty-btn:hover:not(:disabled) {
            background-color: #8e5e48;
            color: white;
            border-color: #8e5e48;
            transform: scale(1.1);
        }

        .qty-btn:disabled {
            opacity: 0.4;
            cursor: not-allowed;
        }

        .quantity-control span {
            font-size: 16px;
            font-weight: 600;
            color: #333;
            min-width: 35px;
            text-align: center;
            padding: 6px 12px;
            background: #f8f8f8;
            border-radius: 6px;
        }

        /* Delete Button */
        .delete-btn {
            background: transparent;
            border: 2px solid #e74c3c;
            color: #e74c3c;
            padding: 8px 16px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }

        .delete-btn:hover {
            background-color: #e74c3c;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(231, 76, 60, 0.3);
        }

        /* Cart Footer */
        .cart-footer {
            background: white;
            border-top: 2px solid #e5e5e5;
            padding: 20px 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.1);
        }

        .select-all {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 15px;
            font-weight: 600;
            color: #333;
            cursor: pointer;
        }

        .select-all input[type="checkbox"] {
            width: 24px;
            height: 24px;
            cursor: pointer;
            accent-color: #8e5e48;
        }

        .total-section {
            text-align: right;
        }

        .total-label {
            font-size: 14px;
            color: #666;
            margin-bottom: 5px;
        }

        .total-amount {
            font-size: 28px;
            color: #8e5e48;
            font-weight: 700;
        }

        .checkout-btn {
            background: linear-gradient(135deg, #8e5e48 0%, #a66d5a 100%);
            color: white;
            border: none;
            padding: 14px 45px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(142, 94, 72, 0.3);
        }

        .checkout-btn:hover {
            background: linear-gradient(135deg, #7a4d3a 0%, #8e5e48 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(142, 94, 72, 0.4);
        }

        .footer-right {
            display: flex;
            align-items: center;
            gap: 35px;
        }

        /* Empty Cart */
        .text-center.py-5 {
            background: white;
            border-radius: 12px;
            padding: 60px 20px;
            box-shadow: 0 2px 12px rgba(142, 94, 72, 0.1);
        }

        /* Responsive */
        @media (max-width: 992px) {
            .cart-header {
                display: none;
            }

            .product-item {
                grid-template-columns: 1fr;
                gap: 15px;
                padding: 20px;
                position: relative;
                padding-left: 60px;
            }

            .checkbox {
                position: absolute;
                top: 20px;
                left: 20px;
            }

            .cart-footer {
                flex-direction: column;
                gap: 15px;
            }

            .footer-right {
                width: 100%;
                justify-content: space-between;
            }
        }
    </style>
</head>

<body>
    <?php include 'component/navbar.php'; ?>

    <div class="container my-5">
        <h2 class="text-center mb-4">Keranjang Pesanan</h2>
        <!-- <?php if (!empty($message)) : ?>
            <div class="alert alert-<?= strpos($message, '✅') !== false ? 'danger' : 'success' ?> alert-dismissible fade show text-center" role="alert">
                <?= $message ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?> -->

        <?php if (empty($cart_items)): ?>
            <div class="text-center py-5">
                <h4>Keranjang Anda Kosong</h4>
                <p>Silakan tambahkan produk ke keranjang</p>
                <a href="produk.php" class="btn btn-primary">Belanja Sekarang</a>
            </div>
        <?php else: ?>
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
                    <?php foreach ($cart_items as $item): ?>
                        <?php
                        $produk = $item['produk'] ?? null;
                        $paket  = $item['paket'] ?? null;

                        if (!empty($item['produk'])) {
                            // ITEM PRODUK
                            $produk = $item['produk'];

                            $nama = $produk['nama_produk'];
                            $harga = $produk['harga'];
                            $foto = $produk['foto_produk'] ?? 'default/default%20product.png';
                        } elseif (!empty($item['paket'])) {
                            // ITEM PAKET
                            $paket = $item['paket'];

                            $nama = $paket['nama_paket'];
                            $harga = $paket['harga_paket'];
                            $foto = $paket['foto_paket'] ?? 'default/default%20paket.png';
                        } else {
                            // fallback jika tidak ada yang sesuai
                            $nama = "Produk Tidak Dikenal";
                            $harga = 0;
                            $foto = 'default/default%20product.png';
                        }


                        $foto = $produk['foto_produk'] ?? 'default/default%20product.png';

                        if (!filter_var($foto, FILTER_VALIDATE_URL)) {
                            $foto = SUPABASE_STORAGE_URL . '/images/' . $foto;
                        }

                        $subtotal = $harga * $item['jumlah'];
                        ?>
                        <div class="product-item">
                            <div class="checkbox">
                                <label class="checkbox-wrapper">
                                    <input
                                        type="checkbox"
                                        class="item-checkbox"
                                        name="selected_items[]"
                                        value="<?= $item['id_keranjang'] ?>"
                                        checked>
                                </label>
                            </div>

                            <div class="product-info">
                                <div class="product-image">
                                    <img src="<?= htmlspecialchars($foto) ?>"
                                        width="80" height="80">
                                </div>

                                <div class="product-details">
                                    <div class="product-title"><?= htmlspecialchars($nama) ?></div>

                                </div>
                            </div>

                            <div class="price">Rp<?= number_format($harga, 0, ',', '.') ?></div>

                            <div class="quantity-control">
                                <form method="POST" action="update-keranjang.php" style="display: inline;">
                                    <input type="hidden" name="id_keranjang" value="<?= $item['id_keranjang'] ?>">
                                    <input type="hidden" name="action" value="decrease">
                                    <button type="submit" class="qty-btn" <?= $item['jumlah'] <= 1 ? 'disabled' : '' ?>>-</button>
                                </form>

                                <span><?= $item['jumlah'] ?></span>

                                <form method="POST" action="update-keranjang.php" style="display: inline;">
                                    <input type="hidden" name="id_keranjang" value="<?= $item['id_keranjang'] ?>">
                                    <input type="hidden" name="action" value="increase">
                                    <button type="submit" class="qty-btn">+</button>
                                </form>
                            </div>

                            <div class="total-price">Rp<?= number_format($subtotal, 0, ',', '.') ?></div>

                            <div class="action-column">
                                <a c href="keranjang.php?hapus=<?= $item['id_keranjang'] ?>"
                                    class="btn btn-danger"
                                    onclick="return confirm('Hapus produk ini dari keranjang?')">
                                    Hapus
                                </a>
                            </div>
                        </div>

                    <?php endforeach; ?>
                </div>
            </div>


            <div class="cart-footer mt-4">
                <div class="footer-left">
                    <div class="select-all">
                        <input type="checkbox" id="select-all" checked>
                        <span>Pilih Semua (<?= count($cart_items) ?>)</span>
                    </div>
                    <form method="POST" action="keranjang.php" id="delete-selected-form">
                        <input type="hidden" name="delete_selected" value="1">
                        <input type="hidden" id="selected_ids" name="selected_ids">
                        <span class="delete-link" onclick="deleteSelected()">Hapus</span>
                    </form>
                </div>
                <div class="footer-right">
                    <div class="total-section">
                        <div class="total-label">Total (<span id="selected-count">0</span> produk):</div>
                        <div class="total-amount" id="total-amount">Rp0</div>
                    </div>
                    <form method="GET" action="checkout.php" id="checkout-form">
                        <input type="hidden" name="selected_ids" id="checkout-selected-ids">
                        <button type="submit" name="checkout" class="checkout-btn">Checkout Sekarang</button>
                    </form>

                </div>
            </div>
        <?php endif; ?>
    </div><br><br><br>

    <script>
        document.getElementById('checkout-form').addEventListener('submit', function(e) {
            var selected = document.getElementById('checkout-selected-ids').value;
            if (!selected || selected.trim() === '') {
                e.preventDefault();
                alert('Anda harus memilih item untuk checkout!');
            }
        });

        // ============================
        // DATA DARI PHP → JS (Sudah FIX)
        // ============================
        const cartData = <?php
                            $jsArr = [];
                            foreach ($cart_items as $item) {
                                // Cari harga: dari produk ATAU paket
                                $harga = null;

                                if (!empty($item['produk'])) {
                                    $harga = $item['produk']['harga'];
                                } elseif (!empty($item['paket'])) {
                                    $harga = $item['paket']['harga_paket'];
                                }

                                // fallback agar tidak null
                                if ($harga === null) $harga = 0;

                                $jsArr[$item['id_keranjang']] = [
                                    'harga' => $harga,
                                    'jumlah' => $item['jumlah']
                                ];
                            }
                            echo json_encode($jsArr);
                            ?>;

        // ============================
        // SELECT ALL
        // ============================
        document.getElementById('select-all')?.addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.item-checkbox');
            checkboxes.forEach(cb => cb.checked = this.checked);
            updateTotal();
        });

        // ============================
        // UPDATE TOTAL
        // ============================
        document.querySelectorAll('.item-checkbox').forEach(cb => {
            cb.addEventListener('change', updateTotal);
        });

        function updateTotal() {
            let total = 0;
            let count = 0;
            let selectedIds = [];

            document.querySelectorAll('.item-checkbox:checked').forEach(cb => {
                const id = cb.value;
                if (cartData[id]) {
                    total += cartData[id].harga * cartData[id].jumlah;
                    count++;
                    selectedIds.push(id);
                }
            });

            document.getElementById('total-amount').textContent =
                'Rp' + total.toLocaleString('id-ID');

            document.getElementById('selected-count').textContent = count;
            document.getElementById('checkout-selected-ids').value = selectedIds.join(',');
        }

        // Inisialisasi total awal
        updateTotal();

        // ============================
        // DELETE SELECTED
        // ============================
        function deleteSelected() {
            let selected = [];

            document.querySelectorAll('.item-checkbox:checked').forEach(cb => {
                selected.push(cb.value);
            });

            if (selected.length === 0) {
                alert("Tidak ada item yang dipilih!");
                return;
            }

            if (!confirm("Hapus item terpilih dari keranjang?")) return;

            document.getElementById("selected_ids").value = selected.join(',');
            document.getElementById("delete-selected-form").submit();
        }
    </script>

</body>

</html>