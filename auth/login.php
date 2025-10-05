<?php
session_start();
include "koneksi.php";

if (isset($_POST['login'])) {
    $no_hp   = $_POST['no_hp_login']; 
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM user WHERE no_hp_login=? AND password=?");
    $stmt->bind_param("ss", $no_hp, $password);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 1) {
        $row = $res->fetch_assoc();

        $_SESSION['id_user'] = $row['id_user'];
        $_SESSION['username'] = $row['username'];
        $_SESSION['level']    = $row['level'];

        // redirect sesuai level
        if ($row['level'] == '0') {
            header("Location: pages/user/dashboard-user.php");
        } else {
            header("Location: pages/admin/dashboard-admin.php");
        }
        exit();
    } else {
        echo "❌ No HP atau Password salah!";
    }
}
?>

<form method="POST">
    <input type="text" name="no_hp_login" placeholder="No HP" required><br>
    <input type="password" name="password" placeholder="Password" required><br>
    <button type="submit" name="login">Login</button>
</form>

<a href="register.php">tidak punya akun?</a>
