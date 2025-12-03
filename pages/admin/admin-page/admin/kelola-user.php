<?php
require '../../../../auth/koneksi.php';
require __DIR__ . '/../../../../vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

function getAllUsers()
{
    global $client;
    try {
        $response = $client->get(SUPABASE_URL . '/rest/v1/profiles?select=*&id=neq.' .
            $_SESSION["id"] . '&order=created_at.desc', [
            'headers' => [
                'apikey' => SUPABASE_KEY,
                'Authorization' => 'Bearer ' . SUPABASE_KEY,
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

if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = $_GET['id'];
    $aksi = $_GET['action'];

    switch ($aksi) {
        case 'ban':
            banUser($id);
            break;
        case 'unban':
            unbanUser($id);
            break;
        case 'promote':

            break;
        case 'demote':

            break;
        default:
            # code...
            break;
    }


    header("Location: kelola-user.php");
    exit();
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
    <style>
        #contextMenu {
            position: absolute;
            display: none;
            z-index: 9999;
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

                    <!-- Content Row -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                                    <h6 class="m-0 font-weight-bold text-primary">Daftar User</h6>
                                </div>
                                <div class="card-body">
                                    <?php if (!empty($message)) echo $message; ?>
                                    <!-- Search Bar -->
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <form method="GET" action="">
                                                <div class="input-group">
                                                    <input type="text" class="form-control" name="search"
                                                        placeholder="Cari user berdasarkan username..."
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
                                        <div class="col-md-6 text-right">
                                            <button class="btn btn-success" data-toggle="modal" data-target="#addUserModal">
                                                <i class="fas fa-plus"></i> Tambah User
                                            </button>
                                        </div>
                                    </div>


                                    <!-- Table -->
                                    <div class="table-responsive" id="myTable">
                                        <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th width="5%">No</th>
                                                    <th width="20%">Nama Lengkap</th>
                                                    <th width="20%">Email</th>
                                                    <th width="20%">no telp</th>
                                                    <th width="10%">Point</th>
                                                    <th width="10%">Status</th>
                                                    <th width="15%">Tanggal Daftar</th>
                                                    <!-- <th width="15%">Aksi</th> -->
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                // Ambil data dari Supabase
                                                $users = getAllUsers();
                                                $search = isset($_GET['search']) ? trim($_GET['search']) : '';

                                                // Filter pencarian
                                                if ($search !== '') {
                                                    $users = array_filter($users, function ($user) use ($search) {
                                                        return (
                                                            stripos($user['username'] ?? '', $search) !== false ||
                                                            stripos($user['email'] ?? '', $search) !== false ||
                                                            stripos($user['nama'] ?? '', $search) !== false
                                                        );
                                                    });
                                                }

                                                // Tampilkan hasil
                                                if (!empty($users)):
                                                    $no = 1;
                                                    foreach ($users as $user):
                                                        $banStatus = isset($user['is_blocked']) ? (bool)$user['is_blocked'] : false;
                                                ?>
                                                        <tr data-id="<?= $user['id'] ?>" data-blocked="<?= $banStatus ?>"
                                                            data-is-admin="<?= (bool) ($user['level'] === 'admin') ?>">
                                                            <td><?= $no++; ?></td>

                                                            <td>
                                                                <span class="badge badge-<?= ($user['level'] ?? 'user') === 'admin' ? 'primary' : 'secondary'; ?>">
                                                                    <?= htmlspecialchars($user['level'] ?? 'User') ?>
                                                                </span>
                                                                <?= htmlspecialchars($user['username'] ?? '-') ?>
                                                            </td>
                                                            <td><?= $user['email'] ?></td>
                                                            <td><?= $user['no_hp_pengguna'] ?></td>
                                                            <!-- <td>
                                                                <span class="badge badge-<?= ($user['level'] ?? 'user') === 'admin' ? 'primary' : 'secondary'; ?>">
                                                                    <?= htmlspecialchars($user['level'] ?? 'User') ?>
                                                                </span>
                                                            </td> -->

                                                            <td><?= htmlspecialchars($user['point'] ?? '0') ?></td>

                                                            <!-- Status User -->
                                                            <td>
                                                                <?php if ($banStatus): ?>
                                                                    <span class="badge bg-danger" style="color:white">Diblokir</span>
                                                                <?php else: ?>
                                                                    <span class="badge bg-success" style="color:white">Aktif</span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td>
                                                                <?= !empty($user['created_at'])
                                                                    ? date('d/m/Y', strtotime($user['created_at']))
                                                                    : '-' ?>
                                                            </td>


                                                            <!-- <td>
                                                                    <?php if ($banStatus): ?>
                                                                        <a href="?action=unban&id=<?= $user['id'] ?>"
                                                                            class="btn btn-sm btn-success" title="Buka blokir">
                                                                            <i class="fas fa-lock-open"></i>
                                                                        </a>
                                                                    <?php else: ?>
                                                                        <a href="?action=ban&id=<?= $user['id'] ?>"
                                                                            class="btn btn-sm btn-danger" title="Blokir user">
                                                                            <i class="fas fa-user-slash"></i>
                                                                        </a>
                                                                    <?php endif; ?>
                                                                </td> -->
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
                                                                    : 'Belum ada data user.' ?>
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

        <!-- contextMenu -->
        <div id="contextMenu" class="dropdown-menu">
            <!-- <p></p> -->
            <button class="dropdown-item text-warning" id="btnAdmin">
                <i class="fas fa-arrow-up"></i>
                Jadikan admin</button>
            <button class="dropdown-item text-danger" id="btnEdit">
                <i class="fas fa-shield"></i>
                Blokir user</button>
        </div>

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

    <!-- Add User Modal -->
    <div class="modal fade" id="addUserModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah User Baru</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="form-group">
                            <label>Nama Lengkap</label>
                            <input type="text" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Username</label>
                            <input type="text" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Role</label>
                            <select class="form-control" required>
                                <option value="">Pilih Role</option>
                                <option value="Admin">Admin</option>
                                <option value="User">User</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                    <button class="btn btn-primary" type="submit">Simpan</button>
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
                const isBlocked = row.getAttribute('data-blocked') === "1";
                const isAdmin = row.getAttribute('data-is-admin') === "1";

                // Ubah isi tombol berdasarkan status
                const btnEdit = document.getElementById("btnEdit");
                const btnAdmin = document.getElementById("btnAdmin")

                if (isBlocked) {
                    btnEdit.innerHTML = '<i class="fas fa-unlock"></i> Buka blokir user';
                    btnEdit.classList.remove("text-danger");
                    btnEdit.classList.add("text-success");

                    // arahkan ke action unban
                    btnEdit.onclick = () => {
                        window.location.href = `?action=unban&id=${row.getAttribute('data-id')}`;
                    };
                } else {
                    btnEdit.innerHTML = '<i class="fas fa-ban"></i> Blokir user';
                    btnEdit.classList.remove("text-success");
                    btnEdit.classList.add("text-danger");

                    // arahkan ke action ban
                    btnEdit.onclick = () => {
                        window.location.href = `?action=ban&id=${row.getAttribute('data-id')}`;
                    };
                }

                if (isAdmin) {
                    btnAdmin.innerHTML = '<i class="fas fa-hammer"></i> demote user';
                    btnAdmin.classList.remove("text-warning");
                    btnAdmin.classList.add("text-danger");
                    btnEdit.disabled = 'true'
                    btnEdit.classList.add("text-muted")
                    // arahkan ke action unban
                    btnAdmin.onclick = () => {
                        window.location.href = `?action=demote&id=${row.getAttribute('data-id')}`;
                    };
                } else {
                    btnAdmin.innerHTML = '<i class="fas fa-hammer"></i> promote user';
                    btnAdmin.classList.remove("text-danger");
                    btnAdmin.classList.add("text-warning");
                    btnEdit.disable = 'false'
                    btnEdit.classList.remove("text-muted")

                    // arahkan ke action ban
                    btnAdmin.onclick = () => {
                        window.location.href = `?action=promote&id=${row.getAttribute('data-id')}`;
                    };
                }


                menu.style.left = x + "px";
                menu.style.top = y + "px";
                menu.style.display = "block";
                // menu.querySelector('p').textContent = selectedRow.getAttribute('data-id');
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
    </script>


</body>

</html>