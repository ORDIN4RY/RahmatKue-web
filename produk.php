<?php
session_start();
require 'auth/koneksi.php';

/* ============================================================
    CEK LOGIN USER
============================================================ */
$isLoggedIn = isset($_SESSION['id_user']);
$level = isset($_SESSION['level']) ? strtolower($_SESSION['level']) : null;

/* ============================================================
    GET KATEGORI DARI SUPABASE
============================================================ */
$kategoriData = getSupabaseData('kategori');

/* FILTER */
$kategoriDipilih = $_GET['kategori'] ?? 'Semua';
$keyword = $_GET['search'] ?? '';

/* ============================================================
    LOGIKA MENGAMBIL PRODUK & PAKET
============================================================ */
/* LOGIKA AMBIL PRODUK */
if ($keyword !== "") {

    // Gunakan search yang menampilkan PRODUK + PAKET
    $result = searchSupabaseAll($keyword);
} elseif (strtolower($kategoriDipilih) === 'paket') {

    // Jika kategori = Paket → ambil paket saja
    $paket = getSupabaseData('paket');
    $produk = [];

    $result = [
        "produk" => [],
        "paket"  => $paket
    ];
} else {

    // Default ambil produk per kategori
    $result = getProdukByKategori($kategoriDipilih);
}



$produk = $result["produk"] ?? [];
$paket  = $result["paket"] ?? [];


/* ============================================================
    KERANJANG SESSION
============================================================ */
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Hapus item dari keranjang
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hapus_item'])) {
    $hapus_id = $_POST['hapus_id'];

    foreach ($_SESSION['cart'] as $key => $item) {
        if ($item['id'] === $hapus_id) {
            unset($_SESSION['cart'][$key]);
            $_SESSION['cart'] = array_values($_SESSION['cart']);
            break;
        }
    }

    header("Location: produk.php?status=deleted");
    exit;
}

/* ============================================================
    NOTIFIKASI
============================================================ */
$message = '';
if (isset($_GET['status']) && $_GET['status'] === 'deleted') {
    $message = "Produk berhasil dihapus dari keranjang!";
}

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
         <h2 class="produk-section produk-section-animate mb-4">Produk Kami</h2>
        <p>
            Temukan berbagai pilihan kue terbaik dari Rahmat Bakery.<br>
            Pesan dengan mudah dan nikmati cita rasa lezat kami.
        </p>
        <div class="search-container">
            <form action="" method="get" class="search-form">
                <div class="search-box">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" name="search" placeholder="Cari produk" value="<?= htmlspecialchars($keyword ?? '') ?>">
                </div>
            </form>
        </div>

        <script>
            function checkProdukAnimation() {
        const title = document.querySelector('.produk-section-animate');
        if (!title) return;
        
        const rect = title.getBoundingClientRect();
        const windowHeight = window.innerHeight;
        const isVisible = rect.top < windowHeight * 0.8 && rect.bottom >= 0;
        
        if (isVisible) {
            title.classList.add('animate');
        } else {
            title.classList.remove('animate');
        }
    }
     window.addEventListener('scroll', function() {
        checkProdukAnimation();
    });
    
    window.addEventListener('load', function() {
        checkProdukAnimation();
    });
        </script>

        <div class="kategori-container">
            <a class="kategori-btn <?= $kategoriDipilih === 'Semua' ? 'active' : '' ?>"
                href="produk.php?kategori=Semua">
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

            <!-- ============== PRODUK ============== -->
            <?php if (!empty($produk)) { ?>
                <?php foreach ($produk as $row) {

                    $id   = htmlspecialchars($row['id_produk'] ?? '');
                    $nama = htmlspecialchars($row['nama_produk'] ?? 'Nama kosong');
                    $deskripsi = htmlspecialchars($row['deskripsi'] ?? '');
                    $harga = isset($row['harga']) ? number_format($row['harga'], 0, ',', '.') : '-';
                    $varian = !empty($row['varian']) ? htmlspecialchars($row['varian']) : 'Tidak ada varian';

                    // Perbaikan: foto tidak ditimpa diskon
                    $foto = htmlspecialchars($row['foto_produk'] ?? '');
                    $diskon = htmlspecialchars($row['diskon'] ?? '');

                    // URL Foto Produk
                    $imgUrl = (!empty($foto))
                        ? (filter_var($foto, FILTER_VALIDATE_URL)
                            ? $foto
                            : SUPABASE_STORAGE_URL . '/images/produk/' . rawurlencode($foto))
                        : 'assets/img/no-image.png';
                ?>

                    <div class="produk-card" style="cursor: pointer;"
                        <?php if ($isLoggedIn): ?>
                        onclick="window.location.href='produk-detail.php?id=<?= urlencode($id) ?>'"
                        <?php else: ?>
                        data-bs-toggle="modal" data-bs-target="#loginModal"
                        <?php endif; ?>>

                        <div class="produk-image-wrapper">
                            <img src="<?= $imgUrl ?>" alt="<?= $nama ?>"
                                onerror="this.src='assets/img/no-image.png';" loading="lazy">
                        </div>

                        <div class="produk-card-content">
                            <h3><?= $nama ?></h3>
                            <p class="harga">Rp <?= $harga ?></p>
                            <h10>Varian: <b><?= $varian ?></b></h10>
                        </div>
                    </div>
                <?php } ?>
            <?php } ?>

            <!-- ============== PAKET ============== -->
            <?php if (!empty($paket)) { ?>
                <?php foreach ($paket as $row) {

                    $id   = htmlspecialchars($row['id_paket'] ?? '');
                    $nama = htmlspecialchars($row['nama_paket'] ?? 'Paket');
                    $deskripsi = htmlspecialchars($row['deskripsi'] ?? '');
                    $harga = isset($row['harga_paket']) ? number_format($row['harga_paket'], 0, ',', '.') : '-';
                    $foto = htmlspecialchars($row['foto_paket'] ?? '');

                    // URL Foto Paket (gunakan folder yang sama dengan produk agar konsisten)
                    $imgUrl = (!empty($foto))
                        ? (filter_var($foto, FILTER_VALIDATE_URL)
                            ? $foto
                            : SUPABASE_STORAGE_URL . '/images/produk/' . rawurlencode($foto))
                        : 'assets/img/no-image.png';

                ?>
                    <div class="produk-card paket-card" style="cursor: pointer;"
                        <?php if ($isLoggedIn): ?>
                        onclick="window.location.href='paket-detail.php?id=<?= urlencode($id) ?>'"
                        <?php else: ?>
                        data-bs-toggle="modal" data-bs-target="#loginModal"
                        <?php endif; ?>>

                        <div class="produk-image-wrapper">
                            <img src="<?= $imgUrl ?>" alt="<?= $nama ?>"
                                onerror="this.src='assets/img/no-image.png';" loading="lazy">
                        </div>

                        <div class="produk-card-content">
                            <h3><?= $nama ?> <span class="badge bg-success">Paket</span></h3>
                            <p class="harga">Rp <?= $harga ?></p>
                            <small><?= $deskripsi ?></small>
                        </div>
                    </div>
                <?php } ?>
            <?php } ?>

            <?php if (empty($produk) && empty($paket)): ?>
                <p>Tidak ada produk atau paket tersedia saat ini.</p>
            <?php endif; ?>

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