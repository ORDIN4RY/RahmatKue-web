<?php
session_start();
if ($_SESSION['level'] != 'seller') {
    header("Location: login.php");
    exit();
}
echo "<h1>Selamat datang Seller, " . $_SESSION['username'] . "</h1>";
