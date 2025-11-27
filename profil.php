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
$email = $_SESSION['email'];
$no_hp = $_SESSION['no_hp_pengguna'];
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
                </div>

                <div class="menu-section">
                    <div class="menu-title">
                        <i class="fas fa-clipboard-list"></i>
                        <span>Pesanan Saya</span>
                    </div>
                </div>

                <div class="menu-section">
                    <div class="menu-title">
                        <i class="fas fa-bell"></i>
                        <span>Notifikasi</span>
                    </div>
                </div>

                <div class="menu-section">
                    <div class="menu-title">
                        <i class="fas fa-ticket-alt"></i>
                        <span>Voucher Saya</span>
                    </div>
                </div>

                <div class="menu-section">
                    <div class="menu-title">
                        <i class="fas fa-coins"></i>
                        <span>Koin Bakery Saya</span>
                    </div>
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
                                    <input type="text" class="form-control" value="<?= htmlspecialchars($username) ?>" readonly>
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
                                            <input type="text" class="form-control" value="<?= htmlspecialchars($no_hp) ?>" readonly>
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

                    <div class="address-section-title mb-3">Alamat</div>

                    <!-- Address List -->
                    <div class="address-list">
                        <?php
                        // Query untuk mengambil data alamat user
                        $alamat_list = [];
                        if ($id_user) {
                            try {
                                $response = $client->get("/rest/v1/alamat", [
                                    'query' => [
                                        'id_user' => 'eq.' . $id_user,
                                        'select' => 'id_alamat,alamat_rumah,no_hp_penetrima,name',
                                        'order' => 'created_at.desc'
                                    ]
                                ]);

                                $alamat_list = json_decode($response->getBody(), true);
                            } catch (Exception $e) {
                                echo "<div class='alert alert-danger'>Error mengambil data alamat: " . $e->getMessage() . "</div>";
                            }
                        }
                        ?>

                        <?php if (!empty($alamat_list)): ?>
                            <?php foreach ($alamat_list as $index => $alamat): ?>
                                <div class="address-card">
                                    <div class="address-header">
                                        <div>
                                            <h3 class="address-name">
                                                <?= htmlspecialchars($alamat['name'] ?? 'Nama tidak tersedia') ?>
                                            </h3>
                                            <span class="address-phone">
                                                <?= htmlspecialchars($alamat['no_hp_penetrima'] ?? 'No HP tidak tersedia') ?>
                                            </span>
                                        </div>
                                        <div class="address-actions">
                                            <a href="#" class="action-link" onclick="editAddress('<?= $alamat['id_alamat'] ?? '' ?>')">Ubah</a>
                                            <?php if ($index > 0): ?>
                                                <a href="#" class="action-link text-danger" onclick="deleteAddress('<?= $alamat['id_alamat'] ?? '' ?>')">Hapus</a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="address-detail">
                                        <?= nl2br(htmlspecialchars($alamat['alamat_rumah'] ?? 'Alamat tidak tersedia')) ?>
                                    </div>
                                    <div class="address-footer">
                                        <?php if ($index === 0): ?>
                                            <span class="address-badge badge-primary">Utama</span>
                                        <?php else: ?>
                                            <button class="default-btn" onclick="setPrimaryAddress('<?= $alamat['id_alamat'] ?? '' ?>')">
                                                Atur sebagai utama
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <p>Belum ada alamat yang tersimpan.</p>
                                <button class="add-address-btn" onclick="showAddAddressForm()">
                                    <i class="fas fa-plus"></i> Tambah Alamat Pertama
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div id="ubah-pw-page" class="page-content" style="display: none;">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h1 class="page-title mb-0">Alamat Saya</h1>
                        <button class="add-address-btn" onclick="showAddAddressForm()">
                            <i class="fas fa-plus"></i> Tambah Alamat Baru
                        </button>
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