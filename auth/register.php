<?php
session_start();
require __DIR__ . '/../vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

/* ===============================
   KONFIGURASI SUPABASE
================================*/

$SUPABASE_URL = "https://fsiuefdwcbdhunfhbiwl.supabase.co";
$SUPABASE_ANON_KEY = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImZzaXVlZmR3Y2JkaHVuZmhiaXdsIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NTk5MzM0NDMsImV4cCI6MjA3NTUwOTQ0M30.pSATGpW89fntkKRuF-qvC7wiO1oZsTruDd-1wMjOdIU"; // ganti dengan milikmu

$client = new Client([
  'base_uri' => rtrim($SUPABASE_URL, '/'),
  'timeout'  => 15
]);

/* ===============================
   PROSES REGISTER
================================*/
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  $email = trim($_POST['email'] ?? '');
  $password = trim($_POST['password'] ?? '');

  if ($email === '' || $password === '') {
    $error = "❌ Email dan password wajib diisi.";
  } else {

    try {

      /* ===============================
               1) SIGNUP USER
            ================================*/
      $signup = $client->post('/auth/v1/signup', [
        'headers' => [
          'apikey' => $SUPABASE_ANON_KEY,
          'Content-Type' => 'application/json'
        ],
        'json' => [
          'email' => $email,
          'password' => $password,
          'autoConfirm' => true
        ]
      ]);

      $signupData = json_decode($signup->getBody()->getContents(), true);

      if (empty($signupData['user']['id'])) {
        $error = "❌ Signup gagal.";
      } else {
        $user_id = $signupData['user']['id'];

        /* ===============================
                   2) LOGIN UNTUK GET ACCESS TOKEN
                ================================*/
        $login = $client->post('/auth/v1/token?grant_type=password', [
          'headers' => [
            'apikey' => $SUPABASE_ANON_KEY,
            'Content-Type' => 'application/json'
          ],
          'json' => [
            'email' => $email,
            'password' => $password
          ]
        ]);

        $loginData = json_decode($login->getBody()->getContents(), true);
        $token = $loginData['access_token'];

        $profileUpsert = $client->request('POST', '/rest/v1/profiles', [
          'headers' => [
            'apikey'        => $SUPABASE_ANON_KEY,
            'Authorization' => 'Bearer ' . $token,
            'Content-Type'  => 'application/json',
            'Prefer'        => 'resolution=merge-duplicates,return=minimal'
          ],
          'query' => ['on_conflict' => 'id'],
          'json' => [
            'id'    => $user_id,
            'level' => 'user',
            'point' => 0
          ]
        ]);


        /* ===============================
                   4) SET SESSION LOGIN
                ================================*/
        $_SESSION['id_user'] = $user_id;
        $_SESSION['email'] = $email;
        $_SESSION['level'] = 'user';
        $_SESSION['point'] = 0;

        header("Location: login.php?status=registered");
        exit;
      }
    } catch (RequestException $e) {

      $msg = $e->hasResponse()
        ? $e->getResponse()->getBody()->getContents()
        : $e->getMessage();

      $error = "⚠️ Error: " . htmlspecialchars($msg);
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
        <label for="email">Nama Lengkap</label>
        <input type="text" name="nama_lengkap" placeholder="Masukkan nama lengkap" required />

        <label for="email">Nomor Telepon</label>
        <input type="text" name="nomor_telepon" placeholder="Masukkan nomor telepon" required />

        <label for="email">Email</label>
        <input type="email" name="email" placeholder="Masukkan email" required />

        <label for="password">Password</label>
        <input type="password" name="password" placeholder="Masukkan password" required />

        <button type="submit" class="btn" name="register">Daftar</button><br>
        <a href="login.php" style="text-decoration: none; margin-top: 20px;">Sudah punya akun? Login disini</a>
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