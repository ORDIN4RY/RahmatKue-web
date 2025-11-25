    <?php
session_start();
require __DIR__ . "/auth/koneksi.php"; // pastikan client Supabase sudah dibuat

// Cek login
if (!isset($_SESSION['id'])) {
    $_SESSION['message'] = "❌ Anda harus login terlebih dahulu.";
    header("Location: login.php?redirect=keranjang.php");
    exit;
}

// Cek input
$id_keranjang = $_POST['id_keranjang'] ?? null;
$action = $_POST['action'] ?? null;

if (!$id_keranjang || !$action) {
    $_SESSION['message'] = "❌ Data tidak valid.";
    header("Location: keranjang.php");
    exit;
}

// Ambil data item keranjang
try {
    $response = $client->get("/rest/v1/keranjang", [
        'query' => [
            'id_keranjang' => 'eq.' . $id_keranjang,
            'select' => 'jumlah'
        ]
    ]);

    $data = json_decode($response->getBody(), true);

    if (!$data) {
        $_SESSION['message'] = "❌ Item tidak ditemukan.";
        header("Location: keranjang.php");
        exit;
    }

    $jumlah = $data[0]['jumlah'];
} catch (Exception $e) {
    $_SESSION['message'] = "❌ Error: " . $e->getMessage();
    header("Location: keranjang.php");
    exit;
}

// Hitung jumlah baru
if ($action === "increase") {
    $jumlah_baru = $jumlah + 1;
} elseif ($action === "decrease") {
    $jumlah_baru = max(1, $jumlah - 1);
} else {
    $_SESSION['message'] = "❌ Aksi tidak valid.";
    header("Location: keranjang.php");
    exit;
}

// Update ke Supabase
try {
    $update = $client->patch("/rest/v1/keranjang?id_keranjang=eq.$id_keranjang", [
        'json' => [
            'jumlah' => $jumlah_baru
        ],
        'headers' => [
            'Prefer' => 'return=minimal'
        ]
    ]);

    $_SESSION['message'] = "✅ Kuantitas berhasil diperbarui.";

} catch (Exception $e) {
    $_SESSION['message'] = "❌ Gagal update: " . $e->getMessage();
}

header("Location: keranjang.php");
exit;
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
        <h2 class="text-center mb-4">Keranjang Belanja</h2>

        <?php if (!empty($message)) : ?>
            <div class="alert alert-<?= strpos($message, '✅') !== false ? 'success' : 'danger' ?> alert-dismissible fade show text-center" role="alert">
                <?= $message ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

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
                        $produk = $item['produk'];
                        $foto = $produk['foto_produk'] ?? 'assets/img/no-image.png';

                        if (!filter_var($foto, FILTER_VALIDATE_URL)) {
                            $foto = SUPABASE_STORAGE_URL . '/images/produk/' . rawurlencode($foto);
                        }

                        $subtotal = $produk['harga'] * $item['jumlah'];
                        ?>
                        <div class="product-item">
                            <div class="checkbox">
                                <input type="checkbox" class="item-checkbox" data-id="<?= $item['id_keranjang'] ?>">
                            </div>
                            <div class="product-info">
                                <div class="product-image">
                                    <img src="<?= htmlspecialchars($foto) ?>" alt="<?= htmlspecialchars($produk['nama_produk']) ?>" width="80" height="80">
                                </div>
                                <div class="product-details">
                                    <div class="product-title"><?= htmlspecialchars($produk['nama_produk']) ?></div>
                                    <?php if (!empty($item['size'])): ?>
                                        <div class="product-variant">Ukuran: Round <?= htmlspecialchars($item['size']) ?> cm</div>
                                    <?php endif; ?>
                                    <?php if (!empty($item['wording'])): ?>
                                        <div class="product-variant">Tulisan: <?= htmlspecialchars($item['wording']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="price">Rp<?= number_format($produk['harga'], 0, ',', '.') ?></div>
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
                                <form method="POST" action="hapus-keranjang.php">
                                    <input type="hidden" name="id_keranjang" value="<?= $item['id_keranjang'] ?>">
                                    <button type="submit" class="delete-btn" onclick="return confirm('Hapus produk ini dari keranjang?')">Hapus</button>
                                </form>
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
                    <form method="POST" action="hapus-keranjang.php" id="delete-selected-form" style="display: inline;">
                        <input type="hidden" name="delete_selected" value="1">
                        <span class="delete-link" onclick="deleteSelected()">Hapus</span>
                    </form>
                </div>
                <div class="footer-right">
                    <div class="total-section">
                        <div class="total-label">Total (<?= count($cart_items) ?> produk):</div>
                        <div class="total-amount">Rp<?= number_format($total_semua, 0, ',', '.') ?></div>
                    </div>
                    <form method="POST" action="checkout-proses.php">
                        <button type="submit" class="checkout-btn">Buat Pesanan</button>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Select all checkbox functionality
        document.getElementById('select-all')?.addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.item-checkbox');
            checkboxes.forEach(cb => cb.checked = this.checked);
        });

        // Delete selected items
        function deleteSelected() {
            const selected = document.querySelectorAll('.item-checkbox:checked');
            if (selected.length === 0) {
                alert('Pilih produk yang ingin dihapus');
                return;
            }

            if (confirm(`Hapus ${selected.length} produk yang dipilih?`)) {
                const form = document.getElementById('delete-selected-form');
                selected.forEach(cb => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'ids[]';
                    input.value = cb.dataset.id;
                    form.appendChild(input);
                });
                form.submit();
            }
        }
    </script>
</body>

</html>