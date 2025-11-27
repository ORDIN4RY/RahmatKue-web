<?php 
session_start();
require 'auth/koneksi.php';

$kategoriData = getSupabaseData('kategori');

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rahmat Kue - Produk</title>
    <link rel="icon" type="image/x-icon" href="assets/img/icon.png">
    <link rel="stylesheet" href="assets/css/produk.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include 'component/navbar.php'; ?>

    <section class="produk-section">
        <h2>Kustom Pesanan</h2>
        <p>
            Temukan berbagai pilihan kue terbaik dari Rahmat Bakery.<br>
            Sesuaikan kue impian Anda dengan berbagai opsi kustomisasi yang kami tawarkan!
        </p>
        <div class="search-container">
            <form action="" method="get" class="search-form">
                <div class="search-box">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" name="search" placeholder="Cari produk" value="<?= htmlspecialchars($keyword ?? '') ?>">
                </div>
            </form>
        </div>

        <div class="kategori-container">
            <a class="kategori-btn <?= $kategoriDipilih === 'Semua' ? 'active' : '' ?>" href="produk.php?kategori=Semua">
                Semua
            </a>
            <?php foreach ($kategoriData as $k) : ?>
                <a class="kategori-btn <?= $kategoriDipilih === $k['nama_kategori'] ? 'active' : '' ?>"
                    href="produk.php?kategori=<?= urlencode($k['nama_kategori']) ?>">
                    <?= htmlspecialchars($k['nama_kategori']) ?>
                </a>
            <?php endforeach; ?>
        </div>


        <div class="produk-container">
            <?php if (!empty($data)) { ?>
                <?php foreach ($data as $row) {

                    $id = htmlspecialchars($row['id_produk'] ?? '');
                    $nama = htmlspecialchars($row['nama_produk'] ?? 'Nama kosong');
                    $deskripsi = htmlspecialchars($row['deskripsi'] ?? '');
                    $harga = isset($row['harga']) ? number_format($row['harga'], 0, ',', '.') : '-';
                    $foto = htmlspecialchars($row['foto_produk'] ?? '');

                    // URL Foto
                    if ($foto !== '') {
                        if (filter_var($foto, FILTER_VALIDATE_URL)) {
                            $imgUrl = $foto;
                        } else {
                            $imgUrl = SUPABASE_STORAGE_URL . '/images/produk/' . rawurlencode($foto);
                        }
                    } else {
                        $imgUrl = 'assets/img/no-image.png';
                    }

                    // Kategori produk (harus muncul dari Supabase join)
                    $nama_kategori = htmlspecialchars($row['kategori']['nama_kategori'] ?? 'Tanpa Kategori');

                ?>
                    <div class="produk-card"
                        style="cursor: pointer;"
                        <?php if ($isLoggedIn): ?>
                        onclick="window.location.href='produk-detail.php?id=<?= urlencode($id) ?>'"
                        <?php else: ?>
                        data-bs-toggle="modal"
                        data-bs-target="#loginModal"
                        <?php endif; ?>>

                        <div class="produk-image-wrapper">
                            <img src="<?= $imgUrl ?>" alt="<?= $nama ?>" onerror="this.src='assets/img/no-image.png';" loading="lazy">
                        </div>

                        <div class="produk-card-content">
                            <h3><?= $nama ?></h3>
                            <p class="deskripsi"><?= $deskripsi ?></p>
                            <p class="harga">Rp <?= $harga ?></p>
                        </div>

                    </div>
                <?php } ?>
            <?php } else { ?>
                <p>Tidak ada produk tersedia saat ini.</p>
            <?php } ?>
        </div>



        <div class="modal fade" id="loginModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Login Diperlukan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        Anda harus login terlebih dahulu untuk melihat detail produk.
                    </div>
                    <div class="modal-footer">
                        <a href="./auth/login.php" class="btn btn-primary">Login</a>
                    </div>
                </div>
            </div>
        </div>

    </section>

    <?php include 'component/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Update cart when modal is opened
        const cartModal = document.getElementById('cartModal');
        if (cartModal) {
            cartModal.addEventListener('show.bs.modal', loadCart);
        }

        // Function to load cart contents
        async function loadCart() {
            try {
                const response = await fetch('auth/get_cart.php');
                const data = await response.json();

                if (data.status === 'success') {
                    updateCartDisplay(data);
                } else {
                    throw new Error(data.message);
                }
            } catch (error) {
                console.error('Error loading cart:', error);
                showEmptyCart();
            }
        }

        // Function to update cart display
        function updateCartDisplay(data) {
            const cartItems = document.getElementById('cartItems');
            const emptyCart = document.getElementById('emptyCart');
            const cartTotal = document.getElementById('cartTotal');
            const checkoutBtn = document.getElementById('checkoutBtn');

            if (data.items && data.items.length > 0) {
                // Show items
                cartItems.innerHTML = data.items.map(item => `
                    <div class="cart-item">
                        <div>
                            <span class="fw-bold">${item.nama}</span>
                            ${item.size ? `<br><small>Size: ${item.size}cm</small>` : ''}
                            ${item.wording ? `<br><small>Wording: ${item.wording}</small>` : ''}
                        </div>
                        <div class="text-end">
                            <span>${item.quantity}x</span>
                            <span class="ms-2">Rp ${new Intl.NumberFormat('id-ID').format(item.subtotal)}</span>
                        </div>
                    </div>
                `).join('');

                cartTotal.textContent = data.formattedTotal;
                cartItems.style.display = 'block';
                emptyCart.style.display = 'none';
                checkoutBtn.style.display = 'inline-block';
            } else {
                showEmptyCart();
            }
        }

        // Function to show empty cart state
        function showEmptyCart() {
            document.getElementById('cartItems').style.display = 'none';
            document.getElementById('emptyCart').style.display = 'block';
            document.getElementById('cartTotal').textContent = '0';
            document.getElementById('checkoutBtn').style.display = 'none';
        }
    </script>
</body>

</html>