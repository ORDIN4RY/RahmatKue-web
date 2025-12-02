<?php
session_start();
require 'auth/koneksi.php';

// Ambil id_user dari session
$id_user = $_SESSION['id'] ?? null;

// Query untuk mengambil data alamat user dengan primary address
$alamat_list = [];
$alamat_utama = null;

if ($id_user) {
    try {
        $response = $client->get("/rest/v1/alamat", [
            'query' => [
                'id_user' => 'eq.' . $id_user,
                'select' => 'id_alamat,alamat_rumah,no_hp_penerima,name,is_primary',
                'order' => 'is_primary.desc,created_at.desc'
            ]
        ]);

        $alamat_list = json_decode($response->getBody(), true);

        // Cari alamat utama
        foreach ($alamat_list as $alamat) {
            if ($alamat['is_primary']) {
                $alamat_utama = $alamat;
                break;
            }
        }
    } catch (Exception $e) {
        error_log("Error mengambil alamat: " . $e->getMessage());
    }
}

$username = $_SESSION['username'] ?? 'User';

$dataUser = getSupabaseData("profiles?id=eq.$id_user&select=*");
$no_hp = $dataUser[0]['no_hp_pengguna'] ?? '';
$email = $dataUser[0]['email'] ?? '';
$email = $_SESSION['email'] ?? null;

$alamat_rumah = getAlamatRumah($id_user);


if (isset($_POST['ubah_password'])) {
    $password_lama = trim($_POST['password_lama']);
    $password_baru = trim($_POST['password_baru']);
    $email = $_SESSION["email"] ?? null;

    if (!$email) {
        echo "<script>alert('Email tidak ditemukan dalam session!');</script>";
        exit;
    }

    try {
        // ============================================================
        // 1. LOGIN ULANG UNTUK VALIDASI PASSWORD LAMA
        // ============================================================
        $authLogin = $client->post("/auth/v1/token?grant_type=password", [
            'headers' => [
                'apikey' => SUPABASE_KEY,
                'Content-Type' => 'application/json'
            ],
            'json' => [
                'email' => $email,
                'password' => $password_lama
            ]
        ]);

        $loginData = json_decode($authLogin->getBody(), true);

        if (!isset($loginData['access_token'])) {
            echo "<script>alert('Password lama salah!');</script>";
            exit;
        }

        // TOKEN USER DARI LOGIN ULANG
        $accessToken = $loginData['access_token'];

        // ============================================================
        // 2. UBAH PASSWORD MENGGUNAKAN PATCH /auth/v1/user
        // ============================================================
        $updatePw = $client->request("PATCH", "/auth/v1/user", [
            'headers' => [
                'apikey'        => SUPABASE_KEY,
                'Authorization' => "Bearer $accessToken",
                'Content-Type'  => 'application/json'
            ],
            'json' => [
                'password' => $password_baru
            ]
        ]);


        echo "<script>alert('Password berhasil diubah! Silakan login ulang.');</script>";
        session_destroy();
        echo "<script>window.location.href='login.php';</script>";
        exit;
    } catch (Exception $e) {
        echo "<script>alert('Gagal mengubah password: " . $e->getMessage() . "');</script>";
    }
}



$pesanan = getRiwayatPesanan($id_user);

?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya - Sweet Bakery</title>
    <link rel="stylesheet" href="./assets/css/profil.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>

    </style>
</head>

<body>

    <?php include 'component/navbar.php'; ?>

    <!-- Main Content -->
    <div class="main-container">
        <div class="content-wrapper">
            <!-- Sidebar -->
            <div class="sidebar">
                <div class="user-info">
                    <div class="user-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <div>
                        <div class="user-name"><?= htmlspecialchars($username) ?></div>
                        <!-- <a href="#" class="edit-profile">
                            <i class="fas fa-pen"></i> Ubah Profil
                        </a> -->
                    </div>
                </div>

                <div class="menu-section">
                    <div class="menu-title">
                        <i class="fas fa-user-circle"></i>
                        <span>Akun Saya</span>
                    </div>
                    <a href="#" class="menu-item" onclick="showPage('profil'); return false;">Profil</a>
                    <a href="#" class="menu-item" onclick="showPage('alamat'); return false;">Alamat</a>
                    <a href="#" class="menu-item" onclick="showPage('ubah-pw'); return false;">Ubah Password</a>
                    <a href="#" class="menu-item" onclick="showPage('pesanan-saya'); return false;">Pesanan Saya</a>
                    <a href="#" class="menu-item" onclick="showPage('voucher'); return false;">Voucher</a>
                </div>
            </div>

            <!-- Main Content Area -->
            <div class="main-content">
                <!-- Profil Page -->
                <div id="profil-page" class="page-content">
                    <h1 class="page-title">Profil Saya</h1>
                    <p class="page-subtitle">Kelola informasi profil Anda untuk mengontrol, melindungi dan mengamankan akun</p>

                    <div class="form-section">
                        <div class="form-left">
                            <div class="form-group">
                                <div class="form-row">
                                    <label class="form-label">Nama Lengkap</label>
                                    <input type="text" class="form-control" value="<?= htmlspecialchars($username) ?>">
                                </div>
                                <div class="form-hint">Username hanya dapat diubah satu (1) kali.</div>
                            </div>
                            <div class="form-group">
                                <div class="form-row">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control" value="<?= htmlspecialchars($email) ?>" readonly>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="form-row">
                                    <label class="form-label">Nomor Telepon</label>
                                    <div class="flex-1">
                                        <div class="d-flex align-items-center gap-2">
                                            <input type="text" class="form-control" value="<?= htmlspecialchars($no_hp ?? '') ?>">
                                            <a href="#" class="link-button">Ubah</a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <button class="submit-button">Simpan</button>
                        </div>

                    </div>
                </div>

                <!-- Alamat Page -->
                <div id="alamat-page" class="page-content" style="display: none;">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h1 class="page-title mb-0">Alamat Saya</h1>
                        <button class="add-address-btn" onclick="showAddAddressForm()">
                            <i class="fas fa-plus"></i> Tambah Alamat Baru
                        </button>
                    </div>

                    <!-- Address List -->
                    <div class="address-list">
                        <?php
                        // Ambil data alamat user
                        $alamat_list = [];
                        if ($id_user) {
                            try {
                                $response = $client->get("/rest/v1/alamat", [
                                    'query' => [
                                        'id_user' => 'eq.' . $id_user,
                                        'select' => 'id_alamat, alamat_rumah, nama_lengkap, no_hp_penerima, alamat_utama',
                                        'order'  => 'id_alamat.desc' // aman jika created_at tidak ada
                                    ]
                                ]);

                                $alamat_list = json_decode($response->getBody(), true);
                            } catch (Exception $e) {
                                echo "<div class='alert alert-danger'>
                        Error mengambil data alamat: " . $e->getMessage() . "
                      </div>";
                            }
                        }
                        ?>

                        <?php if (!empty($alamat_list)): ?>
                            <?php foreach ($alamat_list as $index => $alamat): ?>
                                <div class="address-card">

                                    <div class="address-header">
                                        <div>
                                            <h3 class="address-name">
                                                <?= htmlspecialchars($alamat['nama_lengkap'] ?? 'Nama tidak tersedia') ?>
                                            </h3>

                                            <span class="address-phone">
                                                <?= htmlspecialchars($alamat['no_hp_penerima'] ?? 'No HP tidak tersedia') ?>
                                            </span>
                                        </div>

                                        <div class="address-actions">
                                            <a href="#" class="action-link" onclick="editAddress('<?= $alamat['id_alamat'] ?>')">Ubah</a>

                                            <?php if (empty($alamat['alamat_utama'])): ?>
                                                <a href="#" class="action-link text-danger" onclick="deleteAddress('<?= $alamat['id_alamat'] ?>')">
                                                    Hapus
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="address-detail">
                                        <?= nl2br(htmlspecialchars($alamat['alamat_rumah'] ?? 'Alamat tidak tersedia')) ?>
                                    </div>

                                    <div class="address-footer">
                                        <?php if (!empty($alamat['alamat_utama'])): ?>
                                            <span class="address-badge badge-primary">Utama</span>
                                        <?php else: ?>
                                            <button class="default-btn" onclick="setPrimaryAddress('<?= $alamat['id_alamat'] ?>')">
                                                Atur sebagai utama
                                            </button>
                                        <?php endif; ?>
                                    </div>

                                </div>
                            <?php endforeach; ?>

                        <?php else: ?>
                            <div class="text-center py-4">
                                <p>Belum ada alamat yang tersimpan.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>


                <div id="ubah-pw-page" class="page-content" style="display: none;">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h1 class="page-title mb-0">Ubah Password Akun Anda</h1>
                    </div>

                    <form method="POST">
                        <div class="form-section">
                            <div class="form-left">

                                <div class="form-group">
                                    <div class="form-row">
                                        <label class="form-label">Email</label>
                                        <input type="email" class="form-control" value="<?= htmlspecialchars($email) ?>" readonly>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="form-row">
                                        <label class="form-label">Password Lama</label>
                                        <input type="password" class="form-control" name="password_lama" required>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="form-row">
                                        <label class="form-label">Password Baru</label>
                                        <input type="password" class="form-control" name="password_baru" required>
                                    </div>
                                </div>

                                <button class="submit-button" type="submit" name="ubah_password">Simpan</button>

                            </div>
                        </div>
                    </form>
                </div>



                <div id="pesanan-saya-page" class="page-content" style="display: none;">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h1 class="page-title mb-0">Pesanan Saya</h1>
                    </div>

                    <div class="address-list">

                        <?php if (!empty($pesanan)) : ?>
                            <?php foreach ($pesanan as $trx) : ?>
                                <div class="address-card">

                                    <!-- HEADER -->
                                    <div class="address-header">
                                        <div>
                                            <h3 class="address-name">
                                                <?= $trx['alamat']['nama_penerima'] ?? 'Tanpa Nama' ?>
                                            </h3>
                                            <span class="address-phone">
                                                (<?= $trx['alamat']['no_hp'] ?? '-' ?>)
                                            </span>
                                        </div>
                                        <div class="address-actions">
                                            <a href="detail-pesanan.php?id=<?= $trx['id_transaksi'] ?>" class="action-link">
                                                Detail
                                            </a>
                                        </div>
                                    </div>

                                    <!-- DETAIL ALAMAT -->
                                    <div class="address-detail">
                                        <?= $trx['alamat']['alamat_lengkap'] ?? '-' ?><br>
                                        <?php if (!empty($trx['nomor_pesanan'])) : ?>
                                            Nomor Pesanan: <strong><?= $trx['nomor_pesanan'] ?></strong><br>
                                        <?php endif; ?>

                                        Total Harga:
                                        <strong>Rp <?= number_format($trx['total_harga'], 0, ',', '.') ?></strong><br>

                                        Status: <strong><?= ucfirst($trx['status']) ?></strong><br>

                                        Metode Pengambilan:
                                        <strong><?= ucfirst($trx['metode_pengambilan']) ?></strong><br>

                                        <?php if ($trx['ongkir'] > 0) : ?>
                                            Ongkir: Rp <?= number_format($trx['ongkir'], 0, ',', '.') ?><br>
                                        <?php endif; ?>

                                        <?php if ($trx['potongan'] > 0) : ?>
                                            Potongan: -Rp <?= number_format($trx['potongan'], 0, ',', '.') ?><br>
                                        <?php endif; ?>

                                        <span class="text-muted">Dibuat: <?= date("d M Y H:i", strtotime($trx['created_at'])) ?></span>
                                    </div>

                                    <!-- FOOTER -->
                                    <div class="address-footer">
                                        <span class="address-badge badge-primary">
                                            #<?= $trx['id_transaksi'] ?>
                                        </span>

                                        <button class="default-btn"
                                            onclick="window.location.href='detail-pesanan.php?id=<?= $trx['id_transaksi'] ?>'">
                                            Lihat Pesanan
                                        </button>
                                    </div>

                                </div>
                            <?php endforeach; ?>

                        <?php else : ?>
                            <p>Tidak ada riwayat pesanan.</p>
                        <?php endif; ?>

                    </div>

                </div>

                <div id="voucher-page" class="page-content" style="display: none;">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h1 class="page-title mb-0">Alamat Saya</h1>
                    </div>

                    <div class="address-section-title mb-3">Alamat</div>

                    <!-- Address List -->
                    <div class="address-list">
                        <!-- Address 1 -->
                        <div class="address-card">
                            <div class="address-header">
                                <div>
                                    <h3 class="address-name">Muhammad Rafi Naufal</h3>
                                    <span class="address-phone">(+62) 857 5558 1947</span>
                                </div>
                                <div class="address-actions">
                                    <a href="#" class="action-link">Ubah</a>
                                </div>
                            </div>
                            <div class="address-detail">
                                Dusun Krajan 1, RT 2 RW 14, Desa Jombang, Kecamatan Jombang, 68168<br>
                                JOMBANG, KAB. JEMBER, JAWA TIMUR, ID, 68168
                            </div>
                            <div class="address-footer">
                                <span class="address-badge badge-primary">Utama</span>
                                <button class="default-btn">Atur sebagai utama</button>
                            </div>
                        </div>

                        <!-- Address 2 -->
                        <div class="address-card">
                            <div class="address-header">
                                <div>
                                    <h3 class="address-name">muhammadrafinaufa071</h3>
                                    <span class="address-phone">(+62) 857 5558 1947</span>
                                </div>
                                <div class="address-actions">
                                    <a href="#" class="action-link">Ubah</a>
                                    <a href="#" class="action-link text-danger">Hapus</a>
                                </div>
                            </div>
                            <div class="address-detail">
                                Perumahan Grand Kaliurang, Jambuan, Antirogo<br>
                                SUMBER SARI, KAB. JEMBER, JAWA TIMUR, ID, 68125
                            </div>
                            <div class="address-footer">
                                <button class="default-btn">Atur sebagai utama</button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function showPage(pageName) {
            // Hide all pages
            document.querySelectorAll('.page-content').forEach(page => {
                page.style.display = 'none';
            });

            // Remove active class from all menu items
            document.querySelectorAll('.menu-item').forEach(item => {
                item.classList.remove('active');
            });

            // Show selected page
            document.getElementById(pageName + '-page').style.display = 'block';

            // Add active class to clicked menu item
            event.target.classList.add('active');
        }

        function showAddAddressForm() {
            alert('Form tambah alamat akan ditampilkan');
        }

        // Set profil as default active page
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelector('.menu-item').classList.add('active');
        });
    </script>
</body>

</html>