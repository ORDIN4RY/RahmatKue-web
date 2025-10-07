<?php
include "koneksi.php";

if (isset($_POST['register'])) {
    $username   = $_POST['username'];
    $no_hp      = $_POST['no_hp_login'];
    $password   = $_POST['password'];
    $level      = '0'; // default level = 0 (customer)

    // Enkripsi password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Query simpan data
    $stmt = $conn->prepare("INSERT INTO user (username, no_hp_login, password, level) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $no_hp, $hashed_password, $level);

    if ($stmt->execute()) {
        echo "<script>alert('Registrasi berhasil! Silakan login.'); window.location='login.php';</script>";
    } else {
        echo "Terjadi kesalahan: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

<link rel="stylesheet" href="../assets/css/auth.css">
  <div class="container">
    <div class="image-section"></div>

    <div class="form-section">
      <div class="login-box">
        <h2>Selamat datang di Rahmat Kue</h2>
        <p>Buat akun anda</p>

        <form method="POST">
          <label for="phone">Nama Pengguna</label>
          <input type="text" name="username" placeholder="Masukkan nama pengguna" required />

          <label for="phone">Nomor Handphone</label>
          <input type="text" name="no_hp_login" placeholder="Masukkan nomor handphone" required />

          <label for="password">Password</label>
          <input type="password" name="password" placeholder="Masukkan password" required />

          <button type="submit" class="btn" name="register">Daftar</button>
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
