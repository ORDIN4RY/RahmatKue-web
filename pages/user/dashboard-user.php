<?php
session_start();
if ($_SESSION['level'] != 'customer') {
    header("Location: login.php");
    exit();
}
echo "<h1>Selamat datang Customer, " . $_SESSION['username'] . "</h1>";
