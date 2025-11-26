<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navbar</title>
    <link rel="stylesheet" href="assets/css/main.css">

    <style>
        /* Dropdown container */
        
    </style>
</head>

<body>
    <?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $current_page = basename($_SERVER['PHP_SELF']);
    $isLoggedIn = isset($_SESSION['id_user']) || isset($_SESSION['access_token']);
    $username = $_SESSION['username'] ?? 'User';
    $initial = strtoupper(substr($username, 0, 1));
    ?>

    <div class="navbar" id="navbar">
        <p>Rahmat Bakery</p>

        <a href="index.php" class="<?= ($current_page == 'index.php') ? 'active' : '' ?>">Beranda</a>
        <a href="produk.php" class="<?= ($current_page == 'produk.php') ? 'active' : '' ?>">Produk</a>
        <a href="promo.php" class="<?= ($current_page == 'promo.php') ? 'active' : '' ?>">Promo</a>

        <?php if ($isLoggedIn): ?>
            <a href="keranjang.php" class="<?= ($current_page == 'keranjang.php') ? 'active' : '' ?>">Keranjang</a>
        <?php endif; ?>

        <?php if ($isLoggedIn): ?>
            <!-- DROPDOWN USERNAME -->
            <div class="dropdown" id="userDropdown">
                <button class="dropdown-btn">
                    <span class="username"><?= htmlspecialchars($username) ?></span>
                    <div class="avatar"><?= $initial ?></div>
                </button>

                <div class="dropdown-content">
                    <a href="profil.php">
                        <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Profile
                    </a>
                    <a href="auth/logout.php" onclick="return confirm('Yakin ingin logout?')">
                        <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        Logout
                    </a>
                </div>
            </div>
        <?php else: ?>
            <a href="auth/login.php" class="<?= ($current_page == 'login.php') ? 'active' : '' ?>">Login</a>
        <?php endif; ?>

    </div>

    <script>
        window.addEventListener("scroll", function() {
            const navbar = document.getElementById("navbar");
            navbar.classList.toggle("scrolled", window.scrollY > 50);
        });

        // Toggle dropdown dengan klik
        const dropdown = document.getElementById('userDropdown');
        const dropdownBtn = dropdown?.querySelector('.dropdown-btn');

        if (dropdownBtn) {
            dropdownBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                dropdown.classList.toggle('active');
            });

            // Close dropdown saat klik di luar
            document.addEventListener('click', function(e) {
                if (!dropdown.contains(e.target)) {
                    dropdown.classList.remove('active');
                }
            });
        }
    </script>
</body>

</html>