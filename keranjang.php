<?php
session_start();
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
                                        value="<?= $item['id_keranjang'] ?>">
                                    <span class="checkmark"></span>
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
                        <input type="checkbox" id="select-all" style="width: 20px; height: 20px;">
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