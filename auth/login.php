<?php
session_start();
include "koneksi.php";

if (isset($_POST['login'])) {
  $no_hp   = $_POST['no_hp_login'];
  $password = $_POST['password'];

  $stmt = $conn->prepare("SELECT * FROM user WHERE no_hp_login=?");
  $stmt->bind_param("s", $no_hp);
  $stmt->execute();
  $res = $stmt->get_result();

  if ($res->num_rows === 1) {
    $row = $res->fetch_assoc();

    if (password_verify($password, $row['password'])) {
      $_SESSION['id_user'] = $row['id_user'];
      $_SESSION['username'] = $row['username'];
      $_SESSION['level']    = $row['level'];

      // Pastikan tidak ada output sebelum header
      ob_start();
      if ($row['level'] == '0') {
        header("Location: ../produk.php"); // dirubah pada halaman dashboard, perubahan di bisa menambahkan produk, dan melihat pesanan jika sudah menambahkan produk
      } else if ($row['level'] == '1') {
        header("Location: ../pages/admin/dashboard-admin.php");
      } else {
        echo "Level user tidak dikenali.";
      }
      ob_end_flush();
      exit();
    } else {
      echo "❌ Password salah!";
    }
  } else {
    echo "❌ No HP tidak ditemukan!";
  }
}
?>
<link rel="stylesheet" href="../assets/css/auth.css">
<div class="container">
  <div class="image-section"></div>

  <div class="form-section">
    <div class="login-box">
      <h2>Selamat datang di Rahmat Kue</h2>
      <p>Masukkan akun anda</p>

      <form method="POST">
        <label for="phone">Nomor Handphone</label>
        <input type="text" name="no_hp_login" placeholder="Masukkan nomor handphone" required />

        <label for="password">Password</label>
        <input type="password" name="password" placeholder="Masukkan password" required />

        <div class="forgot">
          <a href="#">Lupa password?</a>
        </div>

        <button type="submit" class="btn" name="login">Log in</button>

        <div class="register">
          Belum punya akun? <a href="register.php">Daftar sekarang</a>
        </div>
      </form>
    </div>
  </div>
</div>