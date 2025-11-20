<?php
session_start();
require __DIR__ . '/../vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

// === 🔧 Konfigurasi Supabase ===
$SUPABASE_URL = "https://fsiuefdwcbdhunfhbiwl.supabase.co";
$SUPABASE_ANON_KEY = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImZzaXVlZmR3Y2JkaHVuZmhiaXdsIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NTk5MzM0NDMsImV4cCI6MjA3NTUwOTQ0M30.pSATGpW89fntkKRuF-qvC7wiO1oZsTruDd-1wMjOdIU";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = trim($_POST['username'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $password = trim($_POST['password'] ?? '');

  if ($email === '' || $password === '' || $username === '') {
    $error = "❌ Semua field wajib diisi.";
  } else {
    $client = new Client([
      'base_uri' => rtrim($SUPABASE_URL, '/'),
      'timeout'  => 15,
    ]);

    try {
      // === 1️⃣ Daftarkan user ke Supabase Auth tanpa verifikasi email ===
      $resp = $client->post('/auth/v1/signup', [
        'headers' => [
          'apikey' => $SUPABASE_ANON_KEY,
          'Content-Type' => 'application/json'
        ],
        'json' => [
          'email' => $email,
          'password' => $password,
          'data' => ['username' => $username], // metadata tambahan
          'autoConfirm' => true // nonaktifkan verifikasi email
        ]
      ]);

      $body = json_decode($resp->getBody()->getContents(), true);

      // Jika Supabase mengembalikan ID user (berarti email confirmation OFF)
      if (!empty($body['user']['id'])) {
        $user_id = $body['user']['id'];

        // === Insert ke tabel profiles ===
        try {
          $resp2 = $client->post('/rest/v1/profiles', [
            'headers' => [
              'apikey' => $SUPABASE_SERVICE_KEY,
              'Authorization' => 'Bearer ' . $SUPABASE_SERVICE_KEY,
              'Content-Type' => 'application/json',
              'Prefer' => 'return=representation'
            ],
            'json' => [
              'id' => $user_id,
              'username' => $username,
              'email' => $email,
              'level' => 'user',
              'point' => 0
            ]
          ]);

          $_SESSION['user_id'] = $user_id;
          $_SESSION['username'] = $username;
          $_SESSION['email'] = $email;
          $_SESSION['level'] = 'user';
          $_SESSION['point'] = 0;

          header('Location: ../index.php?status=registered');
          exit;
        } catch (RequestException $e) {
          if ($e->hasResponse()) {
            $msg = $e->getResponse()->getBody()->getContents();
          } else {
            $msg = $e->getMessage();
          }
          $error = "⚠️ Gagal insert ke profiles: " . htmlspecialchars($msg);
        }
      } else {
        // === Jika tidak ada user.id, berarti butuh verifikasi email ===
        $error = "✅ Akun berhasil dibuat. Silakan cek email untuk verifikasi sebelum login.";
      }
    } catch (RequestException $e) {
      if ($e->hasResponse()) {
        $msg = $e->getResponse()->getBody()->getContents();
      } else {
        $msg = $e->getMessage();
      }
      $error = "⚠️ Request error: " . htmlspecialchars($msg);
    }
  }
}
?>

<head>
  <meta charset="UTF-8">
  <title>Register</title>
  <link rel="stylesheet" href="../assets/css/auth.css">
</head>

<div class="container">
  <div class="image-section"></div>

  <div class="form-section">
    <div class="login-box">
      <h2>Selamat datang di Rahmat Kue</h2>
      <p>Buat akun anda</p>

      <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>

      <form method="POST">
        <label for="username">Nama Pengguna</label>
        <input type="text" name="username" placeholder="Masukkan nama pengguna" required />

        <label for="email">Email</label>
        <input type="email" name="email" placeholder="Masukkan email" required />

        <label for="password">Password</label>
        <input type="password" name="password" placeholder="Masukkan password" required />

        <button type="submit" class="btn" name="register">Daftar</button>
        <a href="login.php" style="text-decoration: none;">Sudah punya akun? Login disini</a>
      </form>
    </div>
  </div>
</div>


<!-- 
<form method="POST">
    <input type="text" name="username" placeholder="Username" required><br>
    <input type="text" name="no_hp_login" placeholder="No HP" required><br>
    <input type="password" name="password" placeholder="Password" required><br>

    <select name="level" required>
        <option value="0">Customer</option>
        <option value="1">Seller</option>
    </select><br>

    <button type="submit" name="register">Register</button>
</form> -->