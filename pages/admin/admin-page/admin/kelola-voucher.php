<?php
session_start();

require '../../../../auth/koneksi.php';
require __DIR__ . '/../../../../vendor/autoload.php';
require '../../../../auth/voucher.php';


use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;


function getVoucher($searchTerm = null)
{
    global $client;

    // Siapkan payload untuk RPC
    $payload = [];
    if ($searchTerm) {
        // Jika ada istilah pencarian, tambahkan ke payload
        $payload = [
            'search_term' => $searchTerm
        ];
    }

    try {
        $response = $client->post('/rest/v1/rpc/get_voucher_with_claims', [
            'headers' => [
                'apikey'        => SUPABASE_SERVICE_KEY,
                'Authorization' => 'Bearer ' . SUPABASE_SERVICE_KEY,
                'Content-Type'  => 'application/json' // Penting untuk RPC dengan payload
            ],

            'body' => json_encode($payload) // Kirim payload yang berisi search_term
        ]);

        return json_decode($response->getBody()->getContents(), true) ?? [];
    } catch (RequestException $e) {
        // ... (kode error handling Anda)
        echo "<pre>Error: " . $e->getMessage() . "</pre>";
        if ($e->hasResponse()) {
            echo "<pre>Response: " . $e->getResponse()->getBody()->getContents() . "</pre>";
        }
        return [];
    }
}


function voucherStats()
{
    global $client;

    try {
        $response = $client->post('/rest/v1/rpc/get_voucher_stats', [
            'headers' => [
                'apikey'        => SUPABASE_SERVICE_KEY,
                'Authorization' => 'Bearer ' . SUPABASE_SERVICE_KEY
            ],

            'body' => json_encode([]) // RPC membutuhkan payload kosong
        ]);

        return json_decode($response->getBody()->getContents(), true) ?? [];
    } catch (RequestException $e) {
        echo "<pre>Error: " . $e->getMessage() . "</pre>";
        if ($e->hasResponse()) {
            echo "<pre>Response: " . $e->getResponse()->getBody()->getContents() . "</pre>";
        }
        return [];
    }
}

function loadKategori()
{
    global $client;
    try {
        $response = $client->get(SUPABASE_URL . '/rest/v1/kategori', [
            'headers' => [
                'apikey' => SUPABASE_KEY,
                'Authorization' => 'Bearer ' . SUPABASE_KEY,
                'Content-Type'  => 'application/json'
            ],
        ]);

        $body = $response->getBody()->getContents();
        $data = json_decode($body, true);


        return $data ?? [];
    } catch (RequestException $e) {
        echo "<pre>Request error: " . $e->getMessage() . "</pre>";
        if ($e->hasResponse()) {
            echo "<pre>Response: " . $e->getResponse()->getBody()->getContents() . "</pre>";
        }
        return [];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nama_voucher_add'])) {

    $result = addVoucher($_POST, $_FILES);

    if ($result['success']) {
        $_SESSION['success'] = "Voucher berhasil ditambahkan!";
    } else {
        $_SESSION['error'] = "Gagal menambahkan voucher: " . $result['error'];
    }

    header("Location: kelola-voucher.php");
    exit;
}

if (isset($_POST['nama_voucher_edit'])) {
    $result = updateVoucher($_POST['id_voucher'], $_POST, $_FILES);

    if ($result['success']) {
        $_SESSION['success'] = "Voucher berhasil diupdate!";
    } else {
        $_SESSION['error'] = $result['error'];
    }

    header("Location: kelola-voucher.php");
    exit;
}

if (isset($_GET['delete_voucher'])) {
    $id = $_GET['delete_voucher'];

    $result = deleteVoucher($id);

    if ($result['success']) {
        $_SESSION['success'] = "Voucher berhasil dihapus!";
    } else {
        $_SESSION['error'] = $result['error'];
    }

    header("Location: kelola-voucher.php");
    exit;
}

if (isset($_GET['get_voucher_by_id'])) {
    header("Content-Type: application/json; charset=UTF-8");
    error_reporting(0);

    $id = $_GET['get_voucher_by_id'];

    // gunakan RELATION NAME sesuai FK: voucher_kategori_id_voucher_fkey
    $url = SUPABASE_URL . "/rest/v1/voucher?select=*,voucher_kategori_id_voucher_fkey(id_kategori)&id_voucher=eq.$id";

    try {
        $response = $client->get($url, [
            "headers" => [
                "apikey" => SUPABASE_KEY,
                "Authorization" => "Bearer " . SUPABASE_KEY
            ]
        ]);

        echo $response->getBody()->getContents();
    } catch (Exception $e) {
        echo json_encode(["error" => $e->getMessage()]);
    }

    exit;
}



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
        body {
            overflow-x: hidden;
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

        .card-header-custom {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .card-header-custom h2 {
            margin: 0;
        }

        #contextMenu {
            position: absolute;
            display: none;
            z-index: 9999;
        }

        /* Layout filter */
        .filter-row {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
        }

        .filter-label {
            font-weight: 600;
            font-size: 14px;
        }

        /* Bootstrap-like form-select-sm */
        .form-select-sm-custom {
            padding: 4px 8px;
            font-size: 0.875rem;
            line-height: 1.5;
            border: 1px solid #ced4da;
            border-radius: 4px;
            background-color: #fff;
            outline: none;
            cursor: pointer;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .form-select-sm-custom:focus {
            border-color: #86b7fe;
            box-shadow: 0 0 0 3px rgba(13, 110, 253, .25);
        }

        /* ===== BADGES (mirip bootstrap) ===== */
        .badge {
            display: inline-block;
            padding: 0.35em 0.6em;
            font-size: 0.75rem;
            font-weight: 600;
            line-height: 1;
            color: #fff;
            text-align: center;
            white-space: nowrap;
            border-radius: 0.375rem;
            text-transform: uppercase;
        }

        .bg-success {
            background-color: #198754;
        }

        .bg-danger {
            background-color: #dc3545;
        }

        .bg-warning {
            background-color: #ffc107;
            color: #000;
        }

        /* Optional biar tabel lebih rapi */
        table tbody tr td {
            vertical-align: middle;
            padding: 8px;
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
                <?php include '../../../../component/topbar.php'; ?>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">


                    <!-- Statistics -->
                    <div class="stats-container">
                        <?php

                        $voucherStat = voucherStats();

                        // var_dump($voucherStat[0]);

                        // die;

                        ?>
                        <div class="stat-card blue">
                            <h3>Total Voucher</h3>
                            <div class="number" id="totalVouchers"><?= $voucherStat[0]['jumlah_voucher'] ?></div>
                        </div>
                        <div class="stat-card green">
                            <h3>Voucher Aktif</h3>
                            <div class="number" id="activeVouchers"><?= $voucherStat[0]['voucher_aktif'] ?></div>
                        </div>
                        <div class="stat-card orange">
                            <h3>Akan Datang</h3>
                            <div class="number" id="upcomingVouchers"><?= $voucherStat[0]['voucher_akan_datang'] ?></div>
                        </div>
                        <div class="stat-card red">
                            <h3>Expired</h3>
                            <div class="number" id="expiredVouchers"><?= $voucherStat[0]['voucher_expired'] ?></div>
                        </div>
                    </div>

                    <!-- Voucher List -->
                    <div class="content-card">
                        <div class="card-header-custom">
                            <div>
                                <form method="GET" action="">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="search"
                                            placeholder="Cari berdasarkan nama"
                                            value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="submit">
                                                <i class="fas fa-search"></i> Cari
                                            </button>
                                            <?php if (isset($_GET['search']) && $_GET['search'] != ''): ?>
                                                <a href="?" class="btn btn-secondary">
                                                    <i class="fas fa-times"></i> Reset
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="row mb-3">
                                <div class="col-auto">
                                    <label for="filterStatus" class="col-form-label fw-bold">Filter Status:</label>
                                </div>
                                <div class="col-auto">
                                    <select id="filterStatus" class="form-select form-select-sm form-select-sm-custom">
                                        <option value="all">Semua</option>
                                        <option value="aktif">Aktif</option>
                                        <option value="expired">Expired</option>
                                        <option value="akan datang">Akan Datang</option>
                                    </select>
                                </div>
                            </div>

                            <button class="btn-custom btn-primary-custom" data-toggle="modal" data-target="#addVoucher">
                                <i class="fas fa-plus"></i> Tambah Voucher
                            </button>
                        </div>

                        <div class="table-container table-responsive">
                            <table class="custom-table text-center table-bordered table-hover" id="myTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Nama Voucher</th>
                                        <th>Harga tukar</th>
                                        <th>Status</th>
                                        <th>jumlah klaim</th>
                                        <th>Tanggal Mulai</th>
                                        <th>Tanggal Berakhir</th>
                                    </tr>
                                </thead>
                                <tbody id="voucherTable">

                                    <?php
                                    $searchTerm = $_GET['search'] ?? null;
                                    $vouchers = getVoucher($searchTerm);

                                    
                                    if (!empty($vouchers)):
                                        foreach ($vouchers as $voucher):

                                            $today = date('Y-m-d');
                                            $start  = $voucher['tgl_mulai'];
                                            $end    = $voucher['tgl_berakhir'];

                                            if ($end < $today) {
                                                // sudah lewat
                                                $status = "expired";
                                                $badge = "badge-danger-custom";
                                            } elseif ($start > $today) {
                                                // belum mulai
                                                $status = "akan datang";
                                                $badge = "badge-warning-custom";
                                            } else {
                                                // sedang berlaku
                                                $status = "aktif";
                                                $badge = "badge-success-custom";
                                            }
                                    ?>

                                            <tr class="text-center" data-id="<?= $voucher['id_voucher'] ?>" data-status="<?= $status ?>">
                                                <td><?= $voucher['nama_voucher'] ?></td>
                                                <td><?= $voucher['poin_tukar'] ?></td>
                                                <td><span class="badge-custom <?= $badge ?>">
                                                        <?= $status ?>
                                                    </span></td>
                                                <td><?= $voucher['jumlah_klaim'] ?></td>
                                                <td><?= $voucher['tgl_mulai'] ?></td>
                                                <td><?= $voucher['tgl_berakhir'] ?></td>

                                            </tr>
                                        <?php
                                        endforeach;

                                    else: ?>

                                        <tr>
                                            <td colspan="6" style="text-align: center; padding: 40px; color: #999;">
                                                Belum ada voucher. Klik tombol "Tambah Voucher" untuk membuat voucher baru.
                                            </td>
                                        </tr>

                                    <?php endif; ?>
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
    <!-- contextMenu -->
    <div id="contextMenu" class="dropdown-menu">
        <p></p>
        <button class="dropdown-item" id="btnEdit">
            <i class="fas fa-edit"></i>
            Edit</button>
        <button class="dropdown-item text-danger" id="btnDelete">
            <i class="fas fa-trash"></i>
            Hapus</button>
    </div>
    <!-- Modal Form -->
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

    <div class="modal fade" id="deleteVoucher" tabindex="-1" role="dialog"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">hapus voucher</h5>
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

    <div class="modal fade" id="addVoucher" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Tambah Voucher Baru</h5>
                    <button class="close" type="button" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <form id="formAddVoucher" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <!-- NAV TAB -->
                        <ul class="nav nav-tabs mb-3" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="tab" href="#tabInfo">Info Utama</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#tabPengaturan">Pengaturan</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#tabLainnya">Kategori & Foto</a>
                            </li>
                        </ul>

                        <div class="tab-content">

                            <!-- TAB 1 -->
                            <div class="tab-pane fade show active" id="tabInfo">
                                <div class="form-group">
                                    <label>Nama Voucher</label>
                                    <input type="text" class="form-control" name="nama_voucher_add" required>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label>Tanggal Mulai</label>
                                        <input type="date" class="form-control" name="tgl_mulai" required>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Tanggal Berakhir</label>
                                        <input type="date" class="form-control" name="tgl_berakhir" required>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Deskripsi</label>
                                    <textarea class="form-control" name="deskripsi" rows="3"></textarea>
                                </div>
                            </div>

                            <!-- TAB 2 -->
                            <div class="tab-pane fade" id="tabPengaturan">

                                <div class="form-row">

                                    <div class="form-group col-md-6">
                                        <label>Poin Tukar</label>
                                        <input type="number" class="form-control" name="poin_tukar" min="0" value="0">
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Minimal Pembelian</label>
                                        <input type="number" class="form-control" name="minimal_pembelian" min="0" value="0">
                                    </div>

                                </div>

                                <div class="form-row">

                                    <div class="form-group col-md-4">
                                        <label>Persentase Potongan (%)</label>
                                        <input type="number" class="form-control" name="persentase_potongan" min="0" max="100" value="0">
                                    </div>

                                    <div class="form-group col-md-8">
                                        <label>Maksimal Potongan (Rp)</label>
                                        <input type="number" class="form-control" name="maksimal_potongan" min="0" value="0">
                                    </div>

                                </div>

                                <div class="form-group">
                                    <label>Jenis Voucher</label>
                                    <select class="form-control" name="jenis_voucher" required>
                                        <option value="">Pilih Jenis Voucher</option>
                                        <option value="diskon">Diskon</option>
                                        <option value="ongkir">Ongkir</option>
                                    </select>
                                </div>
                            </div>

                            <!-- TAB 3 -->
                            <div class="tab-pane fade" id="tabLainnya">

                                <?php

                                $kateg = loadKategori();
                                $no = 1;
                                ?>

                                <div class="form-group">
                                    <label>Kategori (Optional)</label>
                                    <select multiple class="form-control" name="id_kategori[]">
                                        <?php foreach ($kateg as $kat) : ?>
                                            <option value="<?= $kat['id_kategori'] ?>"><?= $kat['nama_kategori'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Upload Foto Voucher</label>
                                    <input type="file" class="form-control-file" name="foto">
                                    <small class="text-muted">Opsional — jika kosong akan memakai default</small>
                                </div>

                            </div>

                        </div>

                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button class="btn btn-primary" type="submit" name="add">Simpan</button>
                    </div>
                </form>

            </div>
        </div>


    </div>

    <div class="modal fade" id="editVoucher" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Edit Voucher</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span>&times;</span>
                    </button>
                </div>

                <form id="formEditVoucher" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id_voucher" id="edit_id_voucher">

                    <div class="modal-body">

                        <!-- NAV TAB -->
                        <ul class="nav nav-tabs mb-3" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="tab" href="#editTabInfo">Info Utama</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#editTabPengaturan">Pengaturan</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#editTabLainnya">Kategori & Foto</a>
                            </li>
                        </ul>

                        <div class="tab-content">

                            <!-- TAB Info Utama -->
                            <div class="tab-pane fade show active" id="editTabInfo">
                                <div class="form-group">
                                    <label>Nama Voucher</label>
                                    <input type="text" class="form-control" name="nama_voucher_edit" id="edit_nama_voucher" required>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label>Tanggal Mulai</label>
                                        <input type="date" class="form-control" name="tgl_mulai" id="edit_tgl_mulai" required>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Tanggal Berakhir</label>
                                        <input type="date" class="form-control" name="tgl_berakhir" id="edit_tgl_berakhir" required>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Deskripsi</label>
                                    <textarea class="form-control" name="deskripsi" id="edit_deskripsi" rows="3"></textarea>
                                </div>
                            </div>

                            <!-- TAB Pengaturan -->
                            <div class="tab-pane fade" id="editTabPengaturan">

                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label>Poin Tukar</label>
                                        <input type="number" class="form-control" name="poin_tukar" id="edit_poin_tukar" min="0">
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Minimal Pembelian</label>
                                        <input type="number" class="form-control" name="minimal_pembelian" id="edit_minimal_pembelian" min="0">
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label>Persentase Potongan (%)</label>
                                        <input type="number" class="form-control" name="persentase_potongan" id="edit_persentase_potongan" min="0" max="100">
                                    </div>

                                    <div class="form-group col-md-8">
                                        <label>Maksimal Potongan (Rp)</label>
                                        <input type="number" class="form-control" name="maksimal_potongan" id="edit_maksimal_potongan" min="0">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Jenis Voucher</label>
                                    <select class="form-control" name="jenis_voucher" id="edit_jenis_voucher" required>
                                        <option value="">Pilih Jenis Voucher</option>
                                        <option value="diskon">Diskon</option>
                                        <option value="ongkir">Ongkir</option>
                                    </select>
                                </div>

                            </div>

                            <!-- TAB Kategori & Foto -->
                            <div class="tab-pane fade" id="editTabLainnya">

                                <?php

                                $kateg = loadKategori();
                                $no = 1;
                                ?>

                                <div class="form-group">
                                    <label>Kategori (Optional)</label>
                                    <select multiple class="form-control" name="id_kategori[]" id="edit_list_kategori">
                                        <?php foreach ($kateg as $kat) : ?>
                                            <option value="<?= $kat['id_kategori'] ?>"><?= $kat['nama_kategori'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Foto Voucher</label>
                                    <input type="file" class="form-control-file" name="foto">
                                    <img id="edit_preview_foto" src="" class="img-fluid rounded mt-2" style="max-height: 150px;">
                                </div>

                            </div>

                        </div>

                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-dismiss="modal" type="reset" id="closeBtn">Batal</button>
                        <button class="btn btn-primary" type="submit" name="update">Simpan Perubahan</button>
                    </div>

                </form>

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

    <!-- Page level plugins -->
    <script src="../vendor/chart.js/Chart.min.js"></script>


    <script>
        // Di dalam tag <script>
        document.querySelector('#editVoucher .close').addEventListener('click', function() {
            $('#editVoucher').modal('hide');
        });
        document.querySelector('#closeBtn').addEventListener('click', function() {
            $('#editVoucher').modal('hide');
        });



        const table = document.getElementById("myTable");
        const menu = document.getElementById("contextMenu");


        let selectedRow = null;
        let pressTimer;

        function showMenu(x, y, row) {
            selectedRow = row;
            menu.style.left = x + "px";
            menu.style.top = y + "px";
            menu.style.display = "block";
            menu.querySelector('p').textContent = selectedRow.getAttribute('data-id');
        }

        // Hide menu on click anywhere
        document.addEventListener("click", () => menu.style.display = "none");

        // Right click event (Desktop)
        table.addEventListener("contextmenu", function(e) {
            e.preventDefault();
            const row = e.target.closest("tr");
            if (!row) return;

            showMenu(e.pageX, e.pageY, row);
        });

        // Long press for Mobile
        table.addEventListener("touchstart", function(e) {
            const row = e.target.closest("tr");
            if (!row) return;

            pressTimer = setTimeout(() => {
                const touch = e.touches[0];
                showMenu(touch.pageX, touch.pageY, row);
            }, 600); // tahan 0.6 detik
        });

        table.addEventListener("touchend", function() {
            clearTimeout(pressTimer);
        });

        // Saat tombol EDIT di klik
        document.getElementById("btnEdit").addEventListener("click", function() {
            const id = selectedRow.getAttribute("data-id");
            menu.style.display = "none";

            // Request data lengkap voucher
            fetch("?get_voucher_by_id=" + id)
                .then(res => res.json())
                .then(data => {
                    const v = data[0];

                    document.getElementById("edit_id_voucher").value = v.id_voucher;
                    document.getElementById("edit_nama_voucher").value = v.nama_voucher;
                    document.getElementById("edit_deskripsi").value = v.deskripsi;
                    document.getElementById("edit_tgl_mulai").value = v.tgl_mulai;
                    document.getElementById("edit_tgl_berakhir").value = v.tgl_berakhir;

                    document.getElementById("edit_poin_tukar").value = v.poin_tukar;
                    document.getElementById("edit_minimal_pembelian").value = v.minimal_pembelian;
                    document.getElementById("edit_persentase_potongan").value = v.persentase_potongan;
                    document.getElementById("edit_maksimal_potongan").value = v.maksimal_potongan;

                    document.getElementById("edit_jenis_voucher").value = v.jenis_voucher;

                    // kategori multiple
                    const selectKategori = document.getElementById("edit_list_kategori");

                    const kategoriList = v.voucher_kategori_id_voucher_fkey || [];

                    // reset selection
                    for (let opt of selectKategori.options) {
                        opt.selected = false;
                    }

                    kategoriList.forEach(k => {
                        for (let opt of selectKategori.options) {
                            if (opt.value === k.id_kategori) {
                                opt.selected = true;
                            }
                        }
                    });

                    document.getElementById("edit_preview_foto").src = v.foto;

                    $("#editVoucher").modal("show");

                })
                .catch(err => console.error(err));



        });


        // Saat tombol HAPUS di klik

        document.getElementById("btnDelete").addEventListener("click", function() {
            if (confirm("Yakin ingin menghapus voucher ini?")) {

                menu.style.display = "none";
                window.location.href = "?delete_voucher=" + selectedRow.getAttribute('data-id');
            }
        });

        document.getElementById("filterStatus").addEventListener("change", function() {
            const selected = this.value;
            const rows = document.querySelectorAll("table tbody tr");

            rows.forEach(row => {
                const status = row.getAttribute("data-status");

                if (selected === "all" || status === selected) {
                    row.style.display = "";
                } else {
                    row.style.display = "none";
                }
            });
        });

        // Di dalam tag <script>
        $('#editVoucher').on('hidden.bs.modal', function() {
            // Reset form ketika modal ditutup
            $('#formEditVoucher')[0].reset();

            // Opsional: Hapus parameter URL yang mungkin tersisa dari proses edit
            if (window.history.replaceState) {
                const url = new URL(window.location.href);
                url.searchParams.delete('get_voucher_by_id'); // Hapus parameter yang mungkin Anda gunakan
                window.history.replaceState({
                    path: url.href
                }, '', url.href);
            }
        });
    </script>

</body>

</html>