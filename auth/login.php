<?php
session_start();
require __DIR__ . '/../vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

$SUPABASE_URL = "https://fsiuefdwcbdhunfhbiwl.supabase.co";
$SUPABASE_KEY = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImZzaXVlZmR3Y2JkaHVuZmhiaXdsIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NTk5MzM0NDMsImV4cCI6MjA3NTUwOTQ0M30.pSATGpW89fntkKRuF-qvC7wiO1oZsTruDd-1wMjOdIU";

$toastMessage = "";
$toastType = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  $email = trim($_POST['email'] ?? '');
  $password = trim($_POST['password'] ?? '');

  if ($email === '' || $password === '') {
    $toastMessage = "Email dan password wajib diisi.";
    $toastType = "danger";
  } else {

    $client = new Client([
      'base_uri' => rtrim($SUPABASE_URL, '/'),
      'timeout' => 10,
    ]);

    try {
      // 1. LOGIN
      $resp = $client->post('/auth/v1/token?grant_type=password', [
        'headers' => [
          'Content-Type' => 'application/json',
          'apikey' => $SUPABASE_KEY,
        ],
        'json' => [
          'email' => $email,
          'password' => $password
        ]
      ]);

      $body = json_decode($resp->getBody()->getContents(), true);

      if (empty($body['access_token'])) {
        $toastMessage = "Login gagal: email atau password salah.";
        $toastType = "danger";
      } else {

        $access_token = $body['access_token'];
        $user_id      = $body['user']['id'];

        $_SESSION['email'] = $email;

        // 2. AMBIL DATA PROFILE
        $profileUrl = "/rest/v1/profiles?id=eq.$user_id&select=*";

        $resp2 = $client->get($profileUrl, [
          'headers' => [
            'apikey' => $SUPABASE_KEY,
            'Authorization' => 'Bearer ' . $access_token,
          ]
        ]);

        $profiles = json_decode($resp2->getBody()->getContents(), true);

        if (!empty($profiles)) {

          // 3. CEK BLOKIR
          if (!empty($profiles[0]['is_blocked'])) {

            echo "<script>alert('Akun Anda diblokir. Hubungi Admin'); window.location='login.php';</script>";
            exit;
          }

          // 4. SIMPAN SESSION PROFIL
          foreach ($profiles[0] as $key => $val) {
            $_SESSION[$key] = $val;
          }

          $_SESSION['access_token'] = $access_token;
          $_SESSION['id_user']      = $user_id;

          $level = strtolower($profiles[0]['level'] ?? 'user');

          $toastMessage = "Login berhasil! Selamat datang, " . htmlspecialchars($profiles[0]['username']) . ".";
          $toastType = "success";

          echo "<script>
                setTimeout(function() {
                  window.location.href = '" . ($level === 'admin'
            ? "../pages/admin/admin-page/admin/index.php"
            : "../produk.php") . "';
                }, 1200);
          </script>";
        } else {
          $toastMessage = "Profile tidak ditemukan.";
          $toastType = "danger";
        }
      }
    } catch (RequestException $e) {

      if ($e->hasResponse()) {
        $err = json_decode($e->getResponse()->getBody()->getContents(), true);

        // 🔥 NOTIFIKASI JWT EXPIRED — (SATU-SATUNYA PERUBAHAN BESAR)
        if (($err["message"] ?? "") === "JWT expired") {
          $toastMessage = "Sesi login Anda telah berakhir. Silakan logout dan login kembali.";
          $toastType = "warning";

          echo "<script>
            alert('Sesi login Anda telah berakhir. Silakan logout dan login kembali.');
            window.location.href = 'logout.php';
          </script>";
          exit;
        }

        // ERROR BIASA
        $toastMessage = $err['msg'] ?? "Login gagal. Periksa kembali email dan password.";
      } else {
        $toastMessage = "Tidak dapat terhubung ke server Supabase.";
      }

      $toastType = "danger";
    }
  }
}

?>


<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <title>Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/auth.css">
</head>

<body>
  <div class="container">
    <div class="image-section"></div>

    <div class="form-section">
      <div class="login-box">
        <h2>Selamat datang di Rahmat Kue</h2>
        <p>Login akun anda</p>
        <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
        <form method="POST">
          <label for="email">Email</label>
          <input type="text" name="email" placeholder="Masukkan email" required />
          <label for="password">Password</label>
          <input type="password" name="password" placeholder="Masukkan password" required />
          <button type="submit" class="btn" name="register">Login</button>
          <a href="register.php" style="text-decoration: none; color: #8b5e3c;">belum punya akun? daftar sekarang</a>
        </form>
      </div>
    </div>

    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 9999;">
      <div id="liveToast" class="toast text-white border-0 <?php echo $toastType === 'success' ? 'bg-success' : ($toastType === 'danger' ? 'bg-danger' : ''); ?>" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
          <div class="toast-body">
            <?php echo htmlspecialchars($toastMessage); ?>
          </div>
          <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
      <?php if (!empty($toastMessage)) : ?>
        const toastLive = document.getElementById('liveToast');
        const toastBootstrap = bootstrap.Toast.getOrCreateInstance(toastLive);
        toastBootstrap.show();
      <?php endif; ?>
    </script>
  </div>
</body>

</html>