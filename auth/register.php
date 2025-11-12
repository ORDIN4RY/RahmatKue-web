<?php
session_start();
require __DIR__ . '/../vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

// === Konfigurasi Supabase ===
$SUPABASE_URL = "https://fsiuefdwcbdhunfhbiwl.supabase.co";
$SUPABASE_KEY = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImZzaXVlZmR3Y2JkaHVuZmhiaXdsIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NTk5MzM0NDMsImV4cCI6MjA3NTUwOTQ0M30.pSATGpW89fntkKRuF-qvC7wiO1oZsTruDd-1wMjOdIU";
$SUPABASE_SERVICE_KEY = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImZzaXVlZmR3Y2JkaHVuZmhiaXdsIiwicm9sZSI6InNlcnZpY2Vfcm9sZSIsImlhdCI6MTc1OTkzMzQ0MywiZXhwIjoyMDc1NTA5NDQzfQ.Fuj3tINEzdkmIzJQ6YPegk--_AGPTN7HIiupCWM6mU4";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = trim($_POST['email'] ?? '');
  $password = trim($_POST['password'] ?? '');
  $username = trim($_POST['username'] ?? '');

  if ($email === '' || $password === '' || $username === '') {
    $error = "❌ Semua field wajib diisi.";
  } else {
    $client = new Client([
      'base_uri' => rtrim($SUPABASE_URL, '/'),
      'timeout'  => 15,
    ]);

    try {
      // === 1️⃣ Registrasi user ke Supabase Auth ===
      $resp = $client->post('/auth/v1/signup', [
        'headers' => [
          'Content-Type' => 'application/json',
          'apikey' => $SUPABASE_KEY,
        ],
        'json' => [
          'email' => $email,
          'password' => $password,
        ]
      ]);

      $body = json_decode($resp->getBody()->getContents(), true);

      if (empty($body['user']['id'])) {
        $error = "❌ Gagal mendaftar. Tidak ada ID user diterima dari Supabase.";
      } else {
        $user_id = $body['user']['id'];

        // === 2️⃣ Tambahkan data ke tabel "profiles" ===
        $resp2 = $client->post('/rest/v1/profiles', [
          'headers' => [
            'Content-Type' => 'application/json',
            'apikey' => $SUPABASE_SERVICE_KEY,
            'Authorization' => 'Bearer ' . $SUPABASE_SERVICE_KEY,
            'Prefer' => 'return=representation',
          ],
          'json' => [[  // gunakan array (bulk insert 1 record)
            'id' => $user_id,
            'username' => $username,
            'email' => $email,
            'level' => 'user',
            'point' => 0,
            'created_at' => date('c')
          ]]
        ]);

        $insertResult = json_decode($resp2->getBody()->getContents(), true);

        // Debug jika gagal insert
        if (isset($insertResult['message']) || empty($insertResult)) {
          echo "<pre>Insert Error: " . htmlspecialchars(json_encode($insertResult, JSON_PRETTY_PRINT)) . "</pre>";
          exit;
        }

        // === 3️⃣ Simpan session dan redirect ===
        $_SESSION['user_id'] = $user_id;
        $_SESSION['username'] = $username;
        $_SESSION['email'] = $email;
        $_SESSION['level'] = 'user';
        $_SESSION['point'] = 0;

        header('Location: ../index.php?status=registered');
        exit;
      }
    } catch (RequestException $e) {
      $msg = $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : $e->getMessage();
      $error = "⚠️ Request error: " . htmlspecialchars($msg);
    } catch (Exception $e) {
      $error = "⚠️ Error: " . htmlspecialchars($e->getMessage());
    }
  }
}
?>


<!-- === Tampilan Form === -->
<link rel="stylesheet" href="../assets/css/auth.css">
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