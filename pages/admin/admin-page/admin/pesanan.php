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

function getDetailPesanan($idTransaksi)
{
    global $client;
    try {
        $response = $client->get('/rest/v1/transaksi', [
            'query' => [
                'id_transaksi' => 'eq.' . $idTransaksi,
                'select' => '*,alamat(*),pembayaran:pembayaran(*),batal(*),detail_transaksi_produk(*,produk(*)),detail_transaksi_paket(*,paket(*)),voucher(*)'
            ],
            'headers' => [
                'apikey'        => SUPABASE_SERVICE_KEY,
                'Authorization' => 'Bearer ' . SUPABASE_SERVICE_KEY
            ]
        ]);
        $data = json_decode($response->getBody()->getContents(), true);
        return $data[0] ?? null; // Kembalikan array transaksi atau null
    } catch (RequestException $e) {
        return null; // Handle error dengan return null
    }
}


if (isset($_GET['action']) && $_GET['action'] === 'get_detail' && isset($_GET['id_transaksi'])) {
    $id = $_GET['id_transaksi'];
    $detail = getDetailPesanan($id);
    header('Content-Type: application/json');
    echo json_encode($detail);
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
                                                        placeholder="Cari berdasarkan kode Pesanan"
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
                                    <div class="table-responsive" id="myTable">
                                        <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th>No</th>
                                                    <th>Nomer Pesanan</th>
                                                    <th>Total Harga</th>
                                                    <th>Status</th>
                                                    <th>Metode Pengambilan</th>
                                                    <th>Waktu Selesai</th>
                                                    <th width="5%">Aksi</th>
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
                                                        <tr data-id="<?= $user['id_transaksi'] ?>">
                                                            <td><?= $no++; ?></td>

                                                            <td><?= htmlspecialchars($user['nomor_pesanan'] ?? '-') ?></td>
                                                            <td>Rp <?= number_format($user['total_harga'], 0, ',', '.') ?></td>
                                                            <td><?= htmlspecialchars($user['status'] ?? '-') ?></td>
                                                            <td><?= htmlspecialchars($user['metode_pengambilan'] ?? '-') ?></td>
                                                            <td><?= !empty($user['waktu_selesai'])
                                                                    ? date('d/m/Y', strtotime($user['waktu_selesai']))
                                                                    : '-' ?></td>
                                                            <td>
                                                                <button class="btn btn-light btn-sm action-btn">
                                                                    <i class="bi bi-three-dots-vertical"></i>
                                                                </button>
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

    <!-- contextMenu -->
    <div id="contextMenu" class="dropdown-menu">
        <!-- <p></p> -->
        <button class="dropdown-item text-warning" id="btnDetail">
            <i class="fas fa-info-circle"></i>
            Detail Pesanan</button>
        <button class="dropdown-item text-danger" id="btnEdit">
            <i class="fas fa-window-close"></i>
            Batalkan Pesanan</button>
    </div>

    <!-- Modal Detail Pesanan -->
    <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailModalLabel">Detail Pesanan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Nav Tabs -->
                    <ul class="nav nav-tabs" id="detailTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="info-tab" data-bs-toggle="tab" data-bs-target="#info" type="button" role="tab">Informasi Umum</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="produk-tab" data-bs-toggle="tab" data-bs-target="#produk" type="button" role="tab">Detail Produk/Paket</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="alamat-tab" data-bs-toggle="tab" data-bs-target="#alamat" type="button" role="tab">Alamat</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="pembayaran-tab" data-bs-toggle="tab" data-bs-target="#pembayaran" type="button" role="tab">Pembayaran</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="batal-tab" data-bs-toggle="tab" data-bs-target="#batal" type="button" role="tab" style="display: none;">Pembatalan</button>
                        </li>
                    </ul>
                    <!-- Tab Content -->
                    <div class="tab-content mt-3" id="detailTabContent">
                        <div class="tab-pane fade show active" id="info" role="tabpanel">
                            <p><strong>Nomor Pesanan:</strong> <span id="nomor_pesanan">-</span></p>
                            <p><strong>Status:</strong> <span id="status" style="color:white">-</span></p>
                            <p><strong>Tanggal Dibuat:</strong> <span id="created_at">-</span></p>
                            <p><strong>Waktu Selesai:</strong> <span id="waktu_selesai">-</span></p>
                            <p><strong>Metode Pengambilan:</strong> <span id="metode_pengambilan">-</span></p>
                            <p><strong>Catatan:</strong> <span id="catatan">-</span></p>
                            <p><strong>Voucher:</strong> <span id="voucher">-</span></p>
                        </div>
                        <div class="tab-pane fade" id="produk" role="tabpanel">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th>Jumlah</th>
                                        <th>Harga Satuan</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody id="produkTableBody"></tbody>
                            </table>
                        </div>
                        <div class="tab-pane fade" id="alamat" role="tabpanel">
                            <p><strong>Nama Penerima:</strong> <span id="nama_lengkap">-</span></p>
                            <p><strong>No. HP:</strong> <span id="no_hp_penerima">-</span></p>
                            <p><strong>Alamat:</strong> <span id="alamat_rumah">-</span></p>
                            <div id="mapContainer" style="height: 300px; width: 100%; border: 1px solid #ddd; border-radius: 5px;">
                                <p style="text-align: center; padding-top: 100px;">Koordinat tidak tersedia</p>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="pembayaran" role="tabpanel">
                            <p><strong>Subtotal:</strong> <span id="subtotal">-</span></p>
                            <p><strong>Ongkir:</strong> <span id="ongkir">-</span></p>
                            <p><strong>Potongan:</strong> <span id="potongan">-</span></p>
                            <p><strong>Total:</strong> <span id="total_harga">-</span></p>
                            <hr>
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Metode</th>
                                        <th>Nominal</th>
                                        <th>Status</th>
                                        <th>Tanggal</th>
                                        <th>Invoice</th>
                                    </tr>
                                </thead>
                                <tbody id="pembayaranTableBody"></tbody>
                            </table>
                        </div>
                        <div class="tab-pane fade" id="batal" role="tabpanel">
                            <p><strong>Alasan:</strong> <span id="alasan_batal">-</span></p>
                            <p><strong>Tipe:</strong> <span id="tipe_batal">-</span></p>
                            <p><strong>Status:</strong> <span id="status_batal" style="color:white">-</span></p>
                            <p><strong>Dikonfirmasi Pada:</strong> <span id="dikonfirmasi_pada">-</span></p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
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
        const table = document.getElementById("myTable");
        const menu = document.getElementById("contextMenu");


        let selectedRow = null;
        let pressTimer;

        function showMenu(x, y, row) {
            if (row.getAttribute('data-id') != null) {
                selectedRow = row;
                const btnEdit = document.getElementById("btnEdit");
                const btnDetail = document.getElementById("btnDetail");

                // ===== Auto adjust posisi =====
                const menuWidth = 180; // lebar perkiraan context menu
                const screenWidth = window.innerWidth;

                let posX = x;
                let posY = y;

                // Jika posisi terlalu dekat kanan → pindahkan ke kiri tombol
                if (x + menuWidth > screenWidth) {
                    posX = x - menuWidth;
                    if (posX < 5) posX = 5; // jangan keluar kiri
                }

                menu.style.left = posX + "px";
                menu.style.top = posY + "px";
                menu.style.display = "block";
            }
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

        document.querySelectorAll(".action-btn").forEach(btn => {
            btn.addEventListener("click", function(e) {
                e.stopPropagation();

                const row = this.closest("tr");
                const rect = this.getBoundingClientRect();

                // tampilkan menu di samping tombol
                console.log(rect);
                showMenu(rect.left + window.scrollX, rect.bottom + window.scrollY - 50, row);
            });
        });

        // Fungsi untuk format harga
        function formatHarga(harga) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(harga);
        }

        // Fungsi untuk badge status
        function getStatusBadge(status) {
            const badges = {
                'Menunggu Pembayaran': 'warning',
                'Sedang Diproses': 'info',
                'Siap Diambil/Diantar': 'primary',
                'Selesai': 'success',
                'Dibatalkan': 'danger'
            };
            return `<span class="badge bg-${badges[status] || 'secondary'}">${status}</span>`;
        }

        // Event listener untuk tombol Detail Pesanan
        document.getElementById("btnDetail").addEventListener("click", function() {
            if (!selectedRow) return;

            const idTransaksi = selectedRow.getAttribute('data-id');
            if (!idTransaksi) return;

            // Fetch data via AJAX
            fetch('?action=get_detail&id_transaksi=' + encodeURIComponent(idTransaksi))
                .then(response => response.json())
                .then(data => {
                    if (data) {
                        console.log(data);
                        // Populate Produk/Paket
                        let produkHtml = '';
                        let totalSubtotal = 0;
                        if (data.detail_transaksi_produk && Array.isArray(data.detail_transaksi_produk)) {
                            data.detail_transaksi_produk.forEach(detail => {
                                const produk = detail.produk;
                                const harga = produk.harga - (produk.harga * (produk.diskon || 0) / 100);
                                totalSubtotal += detail.subtotal;
                                produkHtml += `<tr><td>${produk.nama_produk}<br></td><td>${detail.jumlah}</td><td>${formatHarga(harga)}</td><td>${formatHarga(detail.subtotal)}</td></tr>`;
                            });
                        }
                        if (data.detail_transaksi_paket && Array.isArray(data.detail_transaksi_paket)) {
                            data.detail_transaksi_paket.forEach(detail => {
                                const paket = detail.paket;
                                const harga = paket.harga_paket - (paket.harga_paket * (paket.diskon || 0) / 100);
                                totalSubtotal += detail.subtotal;
                                produkHtml += `<tr><td>${paket.nama_paket}<br></td><td>${detail.jumlah}</td><td>${formatHarga(harga)}</td><td>${formatHarga(detail.subtotal)}</td></tr>`;
                            });
                        }
                        document.getElementById('produkTableBody').innerHTML = produkHtml;

                        // Populate Alamat
                        if (data.alamat) {
                            document.getElementById('nama_lengkap').textContent = data.alamat.nama_lengkap || '-';
                            document.getElementById('no_hp_penerima').textContent = data.alamat.no_hp_penerima || '-';
                            document.getElementById('alamat_rumah').textContent = (data.alamat.alamat_rumah || '') + (data.alamat.detail_lain ? ' (' + data.alamat.detail_lain + ')' : '');
                            // Map handling
                            const mapContainer = document.getElementById('mapContainer');
                            if (data.alamat.latitude && data.alamat.longitude) {
                                const lat = data.alamat.latitude;
                                const lng = data.alamat.longitude;
                                mapContainer.innerHTML = `<iframe src="https://maps.google.com/maps?q=${lat},${lng}&output=embed" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>`;
                            } else {
                                mapContainer.innerHTML = '<p style="text-align: center; padding-top: 100px;">Koordinat tidak tersedia</p>';
                            }
                        } else {
                            document.getElementById('nama_lengkap').textContent = '-';
                            document.getElementById('no_hp_penerima').textContent = '-';
                            document.getElementById('alamat_rumah').textContent = '-';
                            document.getElementById('mapContainer').innerHTML = '<p style="text-align: center; padding-top: 100px;">Koordinat tidak tersedia</p>';
                        }

                        document.getElementById('nomor_pesanan').textContent = data.nomor_pesanan || '-';
                        document.getElementById('status').innerHTML = getStatusBadge(data.status);
                        document.getElementById('created_at').textContent = data.created_at ? new Date(data.created_at).toLocaleString('id-ID') : '-';
                        document.getElementById('waktu_selesai').textContent = data.waktu_selesai ?
                            new Date(data.waktu_selesai).toLocaleString('id-ID') :
                            '-';
                        document.getElementById('metode_pengambilan').textContent = data.metode_pengambilan || '-';
                        document.getElementById('catatan').textContent = data.catatan || '-';

                        // Populate Pembayaran
                        document.getElementById('subtotal').textContent = formatHarga(totalSubtotal);
                        document.getElementById('ongkir').textContent = formatHarga(data.ongkir || 0);
                        document.getElementById('potongan').textContent = formatHarga(data.potongan || 0);
                        document.getElementById('total_harga').textContent = formatHarga(data.total_harga);
                        // Populate Semua Pembayaran (Array)
                        let pembayaranHtml = '';
                        pembayaranTotal = 0;
                        if (data.pembayaran && Array.isArray(data.pembayaran)) {
                            data.pembayaran.forEach(pay => {

                                let metode = pembayaranTotal >= data.total_harga ? 'Lunas' : 'DP';
                                pembayaranTotal += pay.nominal;

                                pembayaranHtml += `
                                <tr>
                                    <td>${metode}</td>
                                    <td>${formatHarga(pay.nominal)}</td>
                                    <td style='color:white'>${getStatusBadge(pay.status)}</td>
                                    <td>${pay.tgl_pembayaran ? new Date(pay.tgl_pembayaran).toLocaleString('id-ID') : '-'}</td>
                                    <td>
                                        ${pay.invoice_url 
                                            ? `<a href="${pay.invoice_url}" target="_blank" class="btn btn-primary btn-sm">
                                                   Buka Invoice
                                               </a>`
                                            : '-'
                                        }
                                    </td>
                                </tr>`;
                            });
                        } else {
                            pembayaranHtml = `
                                <tr><td colspan="5" class="text-center">Tidak ada data pembayaran</td></tr>
                            `;
                        }

                        document.getElementById('pembayaranTableBody').innerHTML = pembayaranHtml;


                        // Populate Batal (jika ada)
                        if (data.status === 'Dibatalkan' && data.batal) {
                            document.getElementById('alasan_batal').textContent = data.batal.alasan || '-';
                            document.getElementById('tipe_batal').textContent = data.batal.tipe || '-';
                            document.getElementById('status_batal').innerHTML = getStatusBadge(data.batal.status);
                            document.getElementById('dikonfirmasi_pada').textContent = data.batal.dikonfirmasi_pada ? new Date(data.batal.dikonfirmasi_pada).toLocaleString('id-ID') : 'Belum dikonfirmasi';
                            document.getElementById('batal-tab').style.display = 'block';
                        } else {
                            document.getElementById('batal-tab').style.display = 'none';
                        }

                        // Show modal
                        new bootstrap.Modal(document.getElementById('detailModal')).show();

                    } else {
                        alert('Data tidak ditemukan.');
                    }
                })
                .catch(error => {
                    console.error('Error fetching detail:', error);
                    alert('Gagal memuat detail pesanan.');
                });
        });
    </script>
</body>

</html>