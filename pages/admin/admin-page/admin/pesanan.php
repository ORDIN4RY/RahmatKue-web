<?php
require '../../../../auth/koneksi.php';
require __DIR__ . '/../../../../vendor/autoload.php';


use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;


function getAllPesanan()
{
    global $client;

    try {
        $response = $client->get('/rest/v1/transaksi', [
            'query' => [
                'select' => '*',
                'order'  => 'created_at.desc'
            ],
            'headers' => [
                'apikey'        => SUPABASE_SERVICE_KEY,
                'Authorization' => 'Bearer ' . SUPABASE_SERVICE_KEY
            ]
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

?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Halaman Admin</title>

    <!-- Custom fonts for this template-->
    <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="../css/sb-admin-2.min.css" rel="stylesheet">

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

                    <div class="row">
                        <div class="col-12">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                                    <h6 class="m-0 font-weight-bold text-primary">Daftar Pesanan</h6>
                                </div>
                                <div class="card-body">
                                    <!-- Search Bar -->
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <form method="GET" action="">
                                                <div class="input-group">
                                                    <input type="text" class="form-control" name="search"
                                                        placeholder="Cari pesanan berdasarkan id keranjang..."
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
                                    </div>

                                    <!-- Table -->
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th width="5%">No</th>
                                                    <th width="20%">Nomer Pesanan</th>
                                                    <th width="20%">Total Harga</th>
                                                    <th width="15%">Status</th>
                                                    <th width="10%">Metode Pengambilan</th>
                                                    <th width="15%">Waktu Selesai</th>
                                                    <th width="15%">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                // Ambil data dari Supabase
                                                $pesanan = getAllPesanan();
                                                $search = isset($_GET['search']) ? trim($_GET['search']) : '';

                                                // Filter pencarian
                                                if ($search !== '') {
                                                    $pesanan = array_filter($pesanan, function ($user) use ($search) {
                                                        return (
                                                            stripos($user['nomor_pesanan'] ?? '', $search) !== false
                                                        );
                                                    });
                                                }

                                                // Tampilkan hasil
                                                if (!empty($pesanan)):
                                                    $no = 1;
                                                    foreach ($pesanan as $user):
                                                ?>
                                                        <tr>
                                                            <td><?= $no++; ?></td>

                                                            <td><?= htmlspecialchars($user['nomor_pesanan'] ?? '-') ?></td>
                                                            <td><?= htmlspecialchars($user['total_harga'] ?? '-') ?></td>
                                                            <td><?= htmlspecialchars($user['status'] ?? '-') ?></td>
                                                            <td><?= htmlspecialchars($user['metode_pengambilan'] ?? '-') ?></td>
                                                            <td><?= htmlspecialchars($user['waktu_selesai'] ?? '-') ?></td>
                                                            <td>
                                                                <i class="bi bi-x-square-fill" title="batal"></i>
                                                                <i class="bi bi-info-circle-fill" title="detail"></i>
                                                            </td>
                                                        </tr>
                                                    <?php
                                                    endforeach;
                                                else:
                                                    ?>
                                                    <tr>
                                                        <td colspan="6" class="text-center">
                                                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                                            <p class="text-muted">
                                                                <?= $search !== ''
                                                                    ? 'Tidak ada hasil untuk pencarian "' . htmlspecialchars($search) . '"'
                                                                    : 'Belum ada data pesanan.' ?>
                                                            </p>
                                                        </td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
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
                        <span>Copyright &copy; Your Website 2021</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

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
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>

    <!-- Page level plugins -->
    <script src="vendor/chart.js/Chart.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="js/demo/chart-area-demo.js"></script>
    <script src="js/demo/chart-pie-demo.js"></script>

</body>

</html>