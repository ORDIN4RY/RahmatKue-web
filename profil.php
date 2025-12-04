<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<?php
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

// --- Blok Kode untuk Menambah Alamat Baru ---

if (isset($_POST['ubah_no_hp'])) {
    // Pastikan ID pengguna tersedia
    $id_user = $_SESSION['id'] ?? null;

    if (!$id_user) {
        echo "<script>alert('Sesi pengguna tidak ditemukan. Silakan login ulang.');</script>";
        exit;
    }

    $no_hp_baru = trim($_POST['no_hp_baru']);

    // Validasi input
    if (empty($no_hp_baru)) {
        echo "<script>alert('Nomor HP baru wajib diisi.');</script>";
    } elseif (!preg_match('/^[0-9]{10,15}$/', $no_hp_baru)) {
        echo "<script>alert('Format Nomor HP tidak valid.');</script>";
    } else {
        $dataUpdate = [
            'no_hp_pengguna' => $no_hp_baru
        ];
        $filter = [
            "id" => "eq.$id_user"
        ];


        // Panggil fungsi updateSupabaseData yang telah direvisi
        $updateResult = updateSupabaseData("profiles", $dataUpdate, $filter);


        // Cek apakah update berhasil (asumsi updateSupabaseData mengembalikan array jika berhasil)
        if (is_array($updateResult)) {
            echo "<script>alert('Nomor HP berhasil diperbarui.'); window.location.href='profil.php';</script>";
            exit;
        } else {
            // Tampilkan pesan error yang lebih detail dari fungsi updateSupabaseData
            echo "<script>alert('Gagal memperbarui Nomor HP: {$updateResult}');</script>";
        }
    }
}


if (isset($_POST['tambah_alamat'])) {

    $id_user        = $_SESSION['id_user']; // atau POST, sesuai flow kamu
    $nama_lengkap   = $_POST['nama_lengkap'] ?? '';
    $no_hp          = $_POST['no_hp_penerima'] ?? '';
    $alamat_rumah   = $_POST['alamat_rumah'] ?? '';
    $detail_lain    = $_POST['detail_lain'] ?? '';
    $latitude       = $_POST['latitude'] ?? null;
    $longitude      = $_POST['longitude'] ?? null;

    if (!$nama_lengkap || !$no_hp || !$alamat_rumah) {
        die("Semua field wajib diisi!");
    }

    // DATA UNTUK DITAMBAHKAN
    $dataInsert = [
        "id_user"       => $id_user,
        "nama_lengkap" => $nama_lengkap,
        "no_hp_penerima" => $no_hp,
        "alamat_rumah" => $alamat_rumah,
        "detail_lain"   => $detail_lain,
        "latitude"      => $latitude,
        "longitude"     => $longitude
    ];

    $insert = insertSupabaseData("alamat", $dataInsert);

    if ($insert) {
        header("Location: profil.php?success=1");
        exit;
    } else {
        echo "Gagal menambahkan alamat";
    }
}

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
    <link href="https://unpkg.com/maplibre-gl@3.6.1/dist/maplibre-gl.css" rel="stylesheet" />
    <style>
        .sidebar {
            position: sticky;
            top: 80px;
            height: fit-content;
        }
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

                            <form action="" method="POST">
                                <input type="hidden" name="ubah_no_hp" value="1">
                                <div class="form-group">
                                    <div class="form-row">
                                        <label class="form-label">Nomor Telepon</label>
                                        <div class="flex-1">
                                            <div class="d-flex align-items-center gap-2">
                                                <input type="text" id="no_hp_baru" name="no_hp_baru" value="<?= htmlspecialchars($no_hp ?? '') ?>" required readonly>
                                                <button type="button" class="link-button" onclick="enableEditHP()">Ubah</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <button class="submit-button" type="submit">Simpan</button>
                            </form>
                        </div>

                    </div>
                </div>


                <!-- Alamat Page -->
                <div id="alamat-page" class="page-content" style="display: none;">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h1 class="page-title mb-0">Alamat Saya</h1>
                        <a style="text-decoration: none;" href="#" class="add-address-btn" onclick="showPage('tambah-alamat'); return false;"><i class="fas fa-plus"></i>Tambah Alamat Baru</a>
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


                <div id="tambah-alamat-page" class="page-content" style="display: none;">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h1 class="page-title mb-0">Tambah Alamat Anda</h1>
                    </div>

                    <form method="POST">
                        <div class="form-section">
                            <div class="form-left">
                                <input type="hidden" name="tambah_alamat" value="1">

                                <div class="form-group">
                                    <div class="form-row">
                                        <label class="form-label">Nama Lengkap</label>
                                        <input type="text" name="nama_lengkap" id="nama_lengkap" class="form-control" placeholder="Masukkan nama lengkap">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="form-row">
                                        <label class="form-label">Nomor Hp Penerima</label>
                                        <input type="text" name="no_hp_penerima" id="no_hp_penerima" class="form-control" placeholder="Masukkan nomor hp penerima">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="form-row">
                                        <label class="form-label">Alamat Rumah</label>
                                        <input type="text" name="alamat_rumah" id="alamat_rumah" class="form-control" placeholder="Masukkan alamat rumah lengkap">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="form-row">
                                        <label class="form-label">Detail Alamat (opsional)</label>
                                        <input type="text" name="detail_lain" id="detail_lain" class="form-control" placeholder="Masukkan detail alamat seperti kode pos, patokan, dll.">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Pilih Lokasi Pada Map</label>
                                    <button type="button" class="btn btn-primary mb-2" onclick="centerUserLocation()">
                                        Pusatkan Lokasi Saya
                                    </button>

                                    <div id="map"
                                        style="width: 70%; height: 250px; border-radius: 10px; border: 1px solid #ddd;"></div>

                                    <!-- Input Hidden untuk disimpan ke DB -->
                                    <input type="hidden" name="latitude" id="lat">
                                    <input type="hidden" name="longitude" id="lng">
                                </div>
                                <button class="submit-button" type="submit">Simpan</button>
                            </div>
                        </div>
                    </form>
                </div>

                <div id="detail-pesanan-page" class="page-content" style="display: none;">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h1 class="page-title mb-0">Tambah Alamat Anda</h1>
                    </div>

                    <form method="POST">
                        <div class="form-section">
                            <div class="form-left">
                                <input type="hidden" name="tambah_alamat" value="1">

                                <div class="form-group">
                                    <div class="form-row">
                                        <label class="form-label">Nama Lengkap</label>
                                        <input type="text" name="nama_lengkap" id="nama_lengkap" class="form-control" placeholder="Masukkan nama lengkap">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="form-row">
                                        <label class="form-label">Nomor Hp Penerima</label>
                                        <input type="text" name="no_hp_penerima" id="no_hp_penerima" class="form-control" placeholder="Masukkan nomor hp penerima">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="form-row">
                                        <label class="form-label">Alamat Rumah</label>
                                        <input type="text" name="alamat_rumah" id="alamat_rumah" class="form-control" placeholder="Masukkan alamat rumah lengkap">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="form-row">
                                        <label class="form-label">Detail Alamat (opsional)</label>
                                        <input type="text" name="detail_lain" id="detail_lain" class="form-control" placeholder="Masukkan detail alamat seperti kode pos, patokan, dll.">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Pilih Lokasi Pada Map</label>
                                    <button type="button" class="btn btn-primary mb-2" onclick="centerUserLocation()">
                                        Pusatkan Lokasi Saya
                                    </button>

                                    <div id="map"
                                        style="width: 70%; height: 250px; border-radius: 10px; border: 1px solid #ddd;"></div>

                                    <!-- Input Hidden untuk disimpan ke DB -->
                                    <input type="hidden" name="latitude" id="lat">
                                    <input type="hidden" name="longitude" id="lng">
                                </div>
                                <button class="submit-button" type="submit">Simpan</button>
                            </div>
                        </div>
                    </form>
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
                                                <?= $trx['alamat']['nama_lengkap'] ?? 'Tanpa Nama' ?>
                                            </h3>
                                            <span class="address-phone">
                                                (<?= $trx['alamat']['no_hp_penerima'] ?? '-' ?>)
                                            </span>
                                        </div>
                                        <div class="address-actions">
                                            <a style="text-decoration: none;" href="#" class="add-address-btn" onclick="showPage('detail-pesanan'); return false;"><i class="fas fa-plus"></i>Detail</a>
                                        </div>
                                    </div>

                                    <!-- DETAIL ALAMAT -->
                                    <div class="address-detail">
                                        <?= $trx['alamat']['alamat_rumah'] ?? '-' ?><br>
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


                
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/maplibre-gl@3.6.1/dist/maplibre-gl.js"></script>
    <script>
        // Lokasi default (Jember)
        let defaultJember = [113.687, -8.172];

        // Inisialisasi map
        var map = new maplibregl.Map({
            container: 'map',
            style: 'https://tiles.openfreemap.org/styles/liberty',
            center: defaultJember,
            zoom: 13
        });

        // Buat marker draggable
        var marker = new maplibregl.Marker({
                draggable: true,
                color: "#007BFF"
            })
            .setLngLat(defaultJember)
            .addTo(map);

        // Update input hidden
        function updateInputs(lngLat) {
            document.getElementById('lat').value = lngLat.lat;
            document.getElementById('lng').value = lngLat.lng;
        }

        // Set nilai awal
        updateInputs({
            lat: defaultJember[1],
            lng: defaultJember[0]
        });

        // Saat marker digeser
        marker.on('dragend', () => updateInputs(marker.getLngLat()));

        // Saat klik map → pindahkan marker
        map.on('click', e => {
            marker.setLngLat(e.lngLat);
            updateInputs(e.lngLat);
        });

        // Fungsi pusatkan ke lokasi user
        function centerUserLocation() {
            if (!navigator.geolocation) {
                alert("Browser tidak mendukung GPS.");
                return;
            }

            navigator.geolocation.getCurrentPosition(
                pos => {
                    let userLng = pos.coords.longitude;
                    let userLat = pos.coords.latitude;

                    map.flyTo({
                        center: [userLng, userLat],
                        zoom: 15,
                        essential: true
                    });

                    marker.setLngLat([userLng, userLat]);
                    updateInputs({
                        lat: userLat,
                        lng: userLng
                    });
                },
                err => {
                    alert("Tidak bisa mendapatkan lokasi. Pastikan GPS aktif.");
                }
            );
        }

        function enableEditHP() {
            document.getElementById('no_hp_baru').removeAttribute('readonly');
            document.getElementById('no_hp_baru').focus();
        }

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