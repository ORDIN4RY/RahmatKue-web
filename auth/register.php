<?php
include "koneksi.php";

if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $no_hp    = $_POST['no_hp_login'];
    $password = $_POST['password'];
    $level    = $_POST['level']; // 0 = customer, 1 = seller

    // cek apakah username atau no_hp sudah dipakai
    $cek = $conn->prepare("SELECT * FROM user WHERE username=? OR no_hp_login=?");
    $cek->bind_param("ss", $username, $no_hp);
    $cek->execute();
    $res = $cek->get_result();

    if ($res->num_rows > 0) {
        echo "❌ Username atau No HP sudah digunakan!";
    } else {
        $stmt = $conn->prepare("INSERT INTO user (username,no_hp_login,password,level) VALUES (?,?,?,?)");
        $stmt->bind_param("sssi", $username, $no_hp, $password, $level);

        if ($stmt->execute()) {
            echo "✅ Registrasi berhasil!";
        } else {
            echo "❌ Registrasi gagal!";
        }
    }
}
?>

<form method="POST">
    <input type="text" name="username" placeholder="Username" required><br>
    <input type="text" name="no_hp_login" placeholder="No HP" required><br>
    <input type="password" name="password" placeholder="Password" required><br>

    <select name="level" required>
        <option value="0">Customer</option>
        <option value="1">Seller</option>
    </select><br>

    <button type="submit" name="register">Register</button>
</form>
