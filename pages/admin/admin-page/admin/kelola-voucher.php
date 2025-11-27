<?php 
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Halaman Admin - Kelola Voucher</title>

    <!-- Custom fonts for this template-->
    <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="../css/sb-admin-2.min.css" rel="stylesheet">

    <style>
        .header {
            background: white;
            padding: 20px 30px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            font-size: 28px;
            margin: 0;
            color: #333;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #8B4513;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            border-left: 4px solid;
            transition: transform 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-card.blue {
            border-left-color: #4A90E2;
        }

        .stat-card.green {
            border-left-color: #7ED321;
        }

        .stat-card.orange {
            border-left-color: #F5A623;
        }

        .stat-card.red {
            border-left-color: #D0021B;
        }

        .stat-card h3 {
            color: #999;
            font-size: 14px;
            text-transform: uppercase;
            margin-bottom: 10px;
        }

        .stat-card .number {
            font-size: 32px;
            font-weight: bold;
            color: #333;
        }

        .content-card {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
        }

        .content-card h2 {
            margin-bottom: 20px;
            color: #333;
            font-size: 22px;
        }

        .btn-custom {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary-custom {
            background: #8B4513;
            color: white;
        }

        .btn-primary-custom:hover {
            background: #654321;
            color: white;
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 12px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 500;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            transition: border 0.3s;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #8B4513;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }

        .table-container {
            overflow-x: auto;
        }

        .custom-table {
            width: 100%;
            border-collapse: collapse;
        }

        .custom-table th,
        .custom-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        .custom-table th {
            background: #f9f9f9;
            font-weight: 600;
            color: #555;
            text-transform: uppercase;
            font-size: 12px;
        }

        .custom-table tr:hover {
            background: #f9f9f9;
        }

        .badge-custom {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .badge-success-custom {
            background: #d4edda;
            color: #155724;
        }

        .badge-warning-custom {
            background: #fff3cd;
            color: #856404;
        }

        .badge-danger-custom {
            background: #f8d7da;
            color: #721c24;
        }

        .action-buttons {
            display: flex;
            gap: 5px;
        }

        .modal-custom {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 2000;
            align-items: center;
            justify-content: center;
        }

        .modal-custom.active {
            display: flex;
        }

        .modal-content-custom {
            background: white;
            padding: 30px;
            border-radius: 10px;
            max-width: 600px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
        }

        .modal-header-custom {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .modal-header-custom h2 {
            margin: 0;
        }

        .close-modal {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #999;
        }

        .close-modal:hover {
            color: #333;
        }

        .card-header-custom {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .card-header-custom h2 {
            margin: 0;
        }
    </style>

</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <?php include '../../../../component/sidebar.php'; ?>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">Douglas McGee</span>
                                <img class="img-profile rounded-circle"
                                    src="img/undraw_profile.svg">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Profile
                                </a>
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Settings
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>
                        </li>
                    </ul>
                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <div class="header">
                        <h1>Kelola Voucher</h1>
                    </div>

                    <!-- Statistics -->
                    <div class="stats-container">
                        <div class="stat-card blue">
                            <h3>Total Voucher</h3>
                            <div class="number" id="totalVouchers">0</div>
                        </div>
                        <div class="stat-card green">
                            <h3>Voucher Aktif</h3>
                            <div class="number" id="activeVouchers">0</div>
                        </div>
                        <div class="stat-card orange">
                            <h3>Akan Datang</h3>
                            <div class="number" id="upcomingVouchers">0</div>
                        </div>
                        <div class="stat-card red">
                            <h3>Expired</h3>
                            <div class="number" id="expiredVouchers">0</div>
                        </div>
                    </div>

                    <!-- Voucher List -->
                    <div class="content-card">
                        <div class="card-header-custom">
                            <h2>Daftar Voucher</h2>
                            <button class="btn-custom btn-primary-custom" onclick="openModal()">
                                <i class="fas fa-plus"></i> Tambah Voucher
                            </button>
                        </div>

                        <div class="table-container">
                            <table class="custom-table">
                                <thead>
                                    <tr>
                                        <th>Kode Voucher</th>
                                        <th>Nama Voucher</th>
                                        <th>Tanggal Mulai</th>
                                        <th>Tanggal Berakhir</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="voucherTable">
                                    <tr>
                                        <td colspan="6" style="text-align: center; padding: 40px; color: #999;">
                                            Belum ada voucher. Klik tombol "Tambah Voucher" untuk membuat voucher baru.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; Rahmat Bakery 2024</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Modal Form -->
    <div id="voucherModal" class="modal-custom">
        <div class="modal-content-custom">
            <div class="modal-header-custom">
                <h2 id="modalTitle">Tambah Voucher</h2>
                <button class="close-modal" onclick="closeModal()">&times;</button>
            </div>
            <form id="voucherForm" onsubmit="handleSubmit(event)">
                <input type="hidden" id="editIndex">

                <div class="form-group">
                    <label>Nama Voucher *</label>
                    <input type="text" id="nama_voucher" required placeholder="Contoh: Diskon Hari Raya">
                </div>

                <div class="form-group">
                    <label>Kode Voucher *</label>
                    <input type="text" id="kode_voucher" required placeholder="Contoh: HARIRAYA2024">
                </div>

                <div class="form-group">
                    <label>Tanggal Mulai *</label>
                    <input type="date" id="tgl_mulai" required>
                </div>

                <div class="form-group">
                    <label>Tanggal Berakhir *</label>
                    <input type="date" id="tgl_berakhir" required>
                </div>

                <div class="form-group">
                    <label>Deskripsi</label>
                    <textarea id="deskripsi" placeholder="Deskripsi singkat tentang voucher"></textarea>
                </div>

                <div class="form-group">
                    <label>Syarat & Ketentuan</label>
                    <textarea id="syarat_ketentuan"
                        placeholder="Syarat dan ketentuan penggunaan voucher"></textarea>
                </div>

                <div style="display: flex; gap: 10px; justify-content: flex-end;">
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-primary" href="../../../auth/logout.php">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="../vendor/jquery/jquery.min.js"></script>
    <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="../vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="../js/sb-admin-2.min.js"></script>

    <script>
        let vouchers = [];

        // Load vouchers from localStorage
        function loadVouchers() {
            const saved = localStorage.getItem('vouchers');
            if (saved) {
                vouchers = JSON.parse(saved);
            }
            updateTable();
            updateStats();
        }

        // Save vouchers to localStorage
        function saveVouchers() {
            localStorage.setItem('vouchers', JSON.stringify(vouchers));
        }

        // Get status of voucher
        function getVoucherStatus(startDate, endDate) {
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            const start = new Date(startDate);
            const end = new Date(endDate);

            if (end < today) {
                return {
                    text: 'Expired',
                    class: 'badge-danger-custom'
                };
            } else if (start > today) {
                return {
                    text: 'Akan Datang',
                    class: 'badge-warning-custom'
                };
            } else {
                return {
                    text: 'Aktif',
                    class: 'badge-success-custom'
                };
            }
        }

        // Format date
        function formatDate(dateString) {
            const date = new Date(dateString);
            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const year = date.getFullYear();
            return `${day}/${month}/${year}`;
        }

        // Update table
        function updateTable() {
            const tbody = document.getElementById('voucherTable');

            if (vouchers.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 40px; color: #999;">
                            Belum ada voucher. Klik tombol "Tambah Voucher" untuk membuat voucher baru.
                        </td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = vouchers.map((voucher, index) => {
                const status = getVoucherStatus(voucher.tgl_mulai, voucher.tgl_berakhir);
                return `
                    <tr>
                        <td><strong>${voucher.kode_voucher}</strong></td>
                        <td>${voucher.nama_voucher}</td>
                        <td>${formatDate(voucher.tgl_mulai)}</td>
                        <td>${formatDate(voucher.tgl_berakhir)}</td>
                        <td><span class="badge-custom ${status.class}">${status.text}</span></td>
                        <td>
                            <div class="action-buttons">
                                <button class="btn btn-warning btn-sm" onclick="editVoucher(${index})">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <button class="btn btn-danger btn-sm" onclick="deleteVoucher(${index})">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            }).join('');
        }

        // Update statistics
        function updateStats() {
            let active = 0,
                upcoming = 0,
                expired = 0;

            vouchers.forEach(voucher => {
                const status = getVoucherStatus(voucher.tgl_mulai, voucher.tgl_berakhir);
                if (status.text === 'Aktif') active++;
                else if (status.text === 'Akan Datang') upcoming++;
                else if (status.text === 'Expired') expired++;
            });

            document.getElementById('totalVouchers').textContent = vouchers.length;
            document.getElementById('activeVouchers').textContent = active;
            document.getElementById('upcomingVouchers').textContent = upcoming;
            document.getElementById('expiredVouchers').textContent = expired;
        }

        // Open modal
        function openModal() {
            document.getElementById('voucherModal').classList.add('active');
            document.getElementById('modalTitle').textContent = 'Tambah Voucher';
            document.getElementById('voucherForm').reset();
            document.getElementById('editIndex').value = '';
        }

        // Close modal
        function closeModal() {
            document.getElementById('voucherModal').classList.remove('active');
        }

        // Edit voucher
        function editVoucher(index) {
            const voucher = vouchers[index];
            document.getElementById('voucherModal').classList.add('active');
            document.getElementById('modalTitle').textContent = 'Edit Voucher';
            document.getElementById('editIndex').value = index;
            document.getElementById('nama_voucher').value = voucher.nama_voucher;
            document.getElementById('kode_voucher').value = voucher.kode_voucher;
            document.getElementById('tgl_mulai').value = voucher.tgl_mulai;
            document.getElementById('tgl_berakhir').value = voucher.tgl_berakhir;
            document.getElementById('deskripsi').value = voucher.deskripsi || '';
            document.getElementById('syarat_ketentuan').value = voucher.syarat_ketentuan || '';
        }

        // Delete voucher
        function deleteVoucher(index) {
            if (confirm('Yakin ingin menghapus voucher ini?')) {
                vouchers.splice(index, 1);
                saveVouchers();
                updateTable();
                updateStats();
            }
        }

        // Handle form submit
        function handleSubmit(event) {
            event.preventDefault();

            const voucher = {
                nama_voucher: document.getElementById('nama_voucher').value,
                kode_voucher: document.getElementById('kode_voucher').value,
                tgl_mulai: document.getElementById('tgl_mulai').value,
                tgl_berakhir: document.getElementById('tgl_berakhir').value,
                deskripsi: document.getElementById('deskripsi').value,
                syarat_ketentuan: document.getElementById('syarat_ketentuan').value
            };

            const editIndex = document.getElementById('editIndex').value;

            if (editIndex !== '') {
                // Edit existing voucher
                vouchers[editIndex] = voucher;
            } else {
                // Add new voucher
                vouchers.push(voucher);
            }

            saveVouchers();
            updateTable();
            updateStats();
            closeModal();
        }

        // Close modal on outside click
        document.getElementById('voucherModal').addEventListener('click', function (e) {
            if (e.target === this) {
                closeModal();
            }
        });

        // Load vouchers on page load
        loadVouchers();
    </script>

</body>

</html>