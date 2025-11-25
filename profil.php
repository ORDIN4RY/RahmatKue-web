<?php 

session_start();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya - Sweet Bakery</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
        }

        /* Header */
        .top-header {
            background: linear-gradient(135deg, #ff9a56 0%, #ff6b6b 100%);
            padding: 12px 0;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .logo {
            font-size: 28px;
            font-weight: 700;
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo i {
            font-size: 32px;
        }

        .search-box {
            position: relative;
            flex: 1;
            max-width: 600px;
        }

        .search-box input {
            width: 100%;
            padding: 10px 50px 10px 20px;
            border: none;
            border-radius: 25px;
            font-size: 14px;
        }

        .search-box button {
            position: absolute;
            right: 5px;
            top: 50%;
            transform: translateY(-50%);
            background: #ff6b6b;
            border: none;
            color: white;
            padding: 8px 20px;
            border-radius: 20px;
            cursor: pointer;
        }

        .cart-icon {
            position: relative;
            color: white;
            font-size: 28px;
            text-decoration: none;
        }

        .cart-badge {
            position: absolute;
            top: -8px;
            right: -10px;
            background: white;
            color: #ff6b6b;
            font-size: 11px;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 10px;
        }

        /* Promo Banner */
        .promo-banner {
            background: #fff4e6;
            padding: 8px 0;
            overflow: hidden;
            white-space: nowrap;
        }

        .promo-scroll {
            display: inline-block;
            animation: scroll 30s linear infinite;
        }

        .promo-item {
            display: inline-block;
            margin: 0 30px;
            color: #ff6b6b;
            font-size: 13px;
            font-weight: 500;
        }

        @keyframes scroll {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }

        /* Main Content */
        .main-container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 15px;
        }

        .content-wrapper {
            display: flex;
            gap: 20px;
        }

        /* Sidebar */
        .sidebar {
            width: 280px;
            background: white;
            border-radius: 8px;
            padding: 20px;
            height: fit-content;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
            padding-bottom: 20px;
            border-bottom: 1px solid #f0f0f0;
            margin-bottom: 20px;
        }

        .user-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, #ff9a56 0%, #ff6b6b 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
        }

        .user-name {
            font-weight: 600;
            color: #333;
            font-size: 15px;
        }

        .edit-profile {
            color: #999;
            font-size: 13px;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .menu-section {
            margin-bottom: 25px;
        }

        .menu-title {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #333;
            font-weight: 600;
            font-size: 14px;
            margin-bottom: 12px;
        }

        .menu-title i {
            color: #ff6b6b;
        }

        .menu-item {
            display: block;
            padding: 10px 15px;
            color: #666;
            text-decoration: none;
            font-size: 14px;
            border-radius: 5px;
            transition: all 0.3s;
        }

        .menu-item:hover {
            background: #fff4e6;
            color: #ff6b6b;
        }

        .menu-item.active {
            background: linear-gradient(135deg, #ff9a56 0%, #ff6b6b 100%);
            color: white;
        }

        /* Main Content Area */
        .main-content {
            flex: 1;
            background: white;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        .page-title {
            font-size: 20px;
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
        }

        .page-subtitle {
            color: #999;
            font-size: 14px;
            margin-bottom: 30px;
        }

        .form-section {
            display: flex;
            gap: 40px;
        }

        .form-left {
            flex: 1;
        }

        .form-right {
            width: 200px;
            text-align: center;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-label {
            display: block;
            color: #666;
            font-size: 14px;
            margin-bottom: 8px;
            text-align: right;
            width: 140px;
        }

        .form-row {
            display: flex;
            align-items: start;
            gap: 15px;
        }

        .form-control {
            flex: 1;
            padding: 10px 15px;
            border: 1px solid #e0e0e0;
            border-radius: 5px;
            font-size: 14px;
        }

        .form-control:focus {
            outline: none;
            border-color: #ff6b6b;
        }

        .form-hint {
            color: #999;
            font-size: 12px;
            margin-top: 5px;
            margin-left: 155px;
        }

        .link-button {
            color: #ff6b6b;
            font-size: 13px;
            text-decoration: none;
            cursor: pointer;
        }

        .link-button:hover {
            text-decoration: underline;
        }

        /* Profile Image Upload */
        .profile-image-wrapper {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(135deg, #ff9a56 0%, #ff6b6b 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            position: relative;
        }

        .profile-image-wrapper i {
            font-size: 50px;
            color: white;
        }

        .upload-button {
            background: white;
            border: 1px solid #e0e0e0;
            padding: 8px 20px;
            border-radius: 5px;
            font-size: 13px;
            cursor: pointer;
            color: #666;
            margin-bottom: 10px;
        }

        .upload-button:hover {
            border-color: #ff6b6b;
            color: #ff6b6b;
        }

        .image-hint {
            font-size: 11px;
            color: #999;
            line-height: 1.4;
        }

        /* Gender Radio */
        .gender-options {
            display: flex;
            gap: 20px;
        }

        .radio-option {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .radio-option input[type="radio"] {
            width: 18px;
            height: 18px;
            accent-color: #ff6b6b;
        }

        /* Date Selects */
        .date-inputs {
            display: flex;
            gap: 10px;
        }

        .date-inputs select {
            flex: 1;
            padding: 10px;
            border: 1px solid #e0e0e0;
            border-radius: 5px;
            font-size: 14px;
            color: #999;
        }

        /* Submit Button */
        .submit-button {
            background: linear-gradient(135deg, #ff9a56 0%, #ff6b6b 100%);
            color: white;
            border: none;
            padding: 12px 40px;
            border-radius: 5px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            margin-left: 155px;
            transition: transform 0.2s;
        }

        .submit-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 107, 107, 0.3);
        }

        @media (max-width: 968px) {
            .content-wrapper {
                flex-direction: column;
            }
            
            .sidebar {
                width: 100%;
            }

            .form-section {
                flex-direction: column;
            }

            .form-right {
                width: 100%;
            }

            .form-row {
                flex-direction: column;
            }

            .form-label {
                text-align: left;
                width: 100%;
            }

            .form-hint, .submit-button {
                margin-left: 0;
            }
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
                        <div class="user-name">muhammadrafinaufa071</div>
                        <a href="#" class="edit-profile">
                            <i class="fas fa-pen"></i> Ubah Profil
                        </a>
                    </div>
                </div>

                <div class="menu-section">
                    <div class="menu-title">
                        <i class="fas fa-user-circle"></i>
                        <span>Akun Saya</span>
                    </div>
                    <a href="#" class="menu-item active">Profil</a>
                    <a href="#" class="menu-item">Bank & Kartu</a>
                    <a href="#" class="menu-item">Alamat</a>
                    <a href="#" class="menu-item">Ubah Password</a>
                    <a href="#" class="menu-item">Pengaturan Notifikasi</a>
                    <a href="#" class="menu-item">Pengaturan Privasi</a>
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
                <h1 class="page-title">Profil Saya</h1>
                <p class="page-subtitle">Kelola informasi profil Anda untuk mengontrol, melindungi dan mengamankan akun</p>

                <div class="form-section">
                    <div class="form-left">
                        <div class="form-group">
                            <div class="form-row">
                                <label class="form-label">Username</label>
                                <input type="text" class="form-control" value="muhammadrafinaufa071" readonly>
                            </div>
                            <div class="form-hint">Username hanya dapat diubah satu (1) kali.</div>
                        </div>

                        <div class="form-group">
                            <div class="form-row">
                                <label class="form-label">Nama</label>
                                <input type="text" class="form-control" placeholder="Masukkan nama">
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="form-row">
                                <label class="form-label">Email</label>
                                <div class="flex-1">
                                    <div class="d-flex align-items-center gap-2">
                                        <input type="text" class="form-control" value="m**********@gmail.com" readonly>
                                        <a href="#" class="link-button">Ubah</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="form-row">
                                <label class="form-label">Nomor Telepon</label>
                                <div class="flex-1">
                                    <div class="d-flex align-items-center gap-2">
                                        <input type="text" class="form-control" value="***********47" readonly>
                                        <a href="#" class="link-button">Ubah</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="form-row">
                                <label class="form-label">Nama Toko</label>
                                <input type="text" class="form-control" value="muhammadrafinaufa071">
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="form-row">
                                <label class="form-label">Jenis Kelamin</label>
                                <div class="gender-options">
                                    <div class="radio-option">
                                        <input type="radio" name="gender" id="male">
                                        <label for="male">Laki-laki</label>
                                    </div>
                                    <div class="radio-option">
                                        <input type="radio" name="gender" id="female">
                                        <label for="female">Perempuan</label>
                                    </div>
                                    <div class="radio-option">
                                        <input type="radio" name="gender" id="other">
                                        <label for="other">Lainnya</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="form-row">
                                <label class="form-label">Tanggal lahir</label>
                                <div class="date-inputs">
                                    <select>
                                        <option>Tanggal</option>
                                    </select>
                                    <select>
                                        <option>Bulan</option>
                                    </select>
                                    <select>
                                        <option>Tahun</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <button class="submit-button">Simpan</button>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>