<?php
session_start();
require __DIR__ . '/../vendor/autoload.php'; // sesuaikan path jika perlu

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

$SUPABASE_URL = "https://fsiuefdwcbdhunfhbiwl.supabase.co"; // ganti
$SUPABASE_KEY = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImZzaXVlZmR3Y2JkaHVuZmhiaXdsIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NTk5MzM0NDMsImV4cCI6MjA3NTUwOTQ0M30.pSATGpW89fntkKRuF-qvC7wiO1oZsTruDd-1wMjOdIU"; // ganti

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = trim($_POST['email'] ?? '');
  $password = trim($_POST['password'] ?? '');

  if ($email === '' || $password === '') {
    $error = "Email dan password wajib diisi.";
  } else {
    $client = new Client([
      'base_uri' => rtrim($SUPABASE_URL, '/'),
      'timeout'  => 10,
    ]);

    try {
      // 1) LOGIN -> ambil access_token
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


      if (empty($body['access_token']) || empty($body['user']['id'])) {
        $error = "Login gagal: token tidak diterima.";
      } else {
        $access_token = $body['access_token'];
        $user_id = $body['user']['id'];

        // 2) AMBIL PROFILE via REST API
        // format query: /rest/v1/profiles?select=*&user_id=eq.<uuid>
        $query = http_build_query([
          'select' => '*',
          'id' => "eq.$user_id"
        ]);
        $profilesUrl = "/rest/v1/profiles?$query";

        $resp2 = $client->get($profilesUrl, [
          'headers' => [
            'apikey' => $SUPABASE_KEY,
            'Authorization' => 'Bearer ' . $access_token,
          ]
        ]);

        $profiles = json_decode($resp2->getBody()->getContents(), true);

        if (!empty($profiles) && isset($profiles[0])) {
          // simpan profile ke session
          foreach ($profiles[0] as $k => $v) {
            $_SESSION[$k] = $v;
          }
          $_SESSION['access_token'] = $access_token;
          $_SESSION['user'] = $user_id;

          $level = strtolower(trim($profiles[0]['level'] ?? 'user'));
          if ($level === 'admin') {
            header('Location: ../pages/admin/admin-page/dashboard-admin2.php');
          } else {
            header('Location: ../produk.php');
          }
          exit;
        } else {
          $error = "Data profil tidak ditemukan. Pastikan 'profiles.user_id' berisi id yang sama.";
        }
      }
    } catch (RequestException $e) {
      // debugging: tampilkan pesan error server (jika ada)
      $msg = $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : $e->getMessage();
      $error = "Request error: " . htmlspecialchars($msg);
    } catch (Exception $e) {
      $error = "Error: " . htmlspecialchars($e->getMessage());
    }
  }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <title>Login</title>
  <link rel="stylesheet" href="../assets/css/auth.css">
</head>

<body>
  <div class="container">
    <div class="image-section"></div>

    <div class="form-section">
      <div class="login-box">
        <h2>Selamat datang di Rahmat Kue</h2>
        <p>Login akun anda</p>
        <form method="POST">
          <label for="email">Email</label>
          <input type="text" name="email" placeholder="Masukkan email" required />
          <label for="password">Password</label>
          <input type="password" name="password" placeholder="Masukkan password" required />
          <button type="submit" class="btn" name="register">Login</button>
          <a href="register.php" style="text-decoration: none;">belum punya akun? daftar sekarang</a>
        </form>
      </div>
    </div>
  </div>
</body>

</html>