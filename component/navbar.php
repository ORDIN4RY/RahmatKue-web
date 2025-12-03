<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$current_page = basename($_SERVER['PHP_SELF']);
$isLoggedIn = isset($_SESSION['id_user']) || isset($_SESSION['access_token']);
$username = $_SESSION['username'] ?? 'User';
$initial = strtoupper(substr($username, 0, 1));
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navbar</title>
    <link rel="stylesheet" href="assets/css/main.css">

    <style>
        .navbar {
            position: sticky;
            top: 0;
            left: 0;
            right: 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1.2rem 3rem;
            background: transparent;
            transition: all 0.3s ease;
            z-index: 1000;
            backdrop-filter: blur(0px);
        }

        .navbar.scrolled {
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
        }

        .navbar p {
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0;
            color: #5d4037;
        }

        .navbar a {
            text-decoration: none;
            color: #5d4037;
            font-weight: 500;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            white-space: nowrap;
        }

        .navbar a:hover {
            background: rgba(93, 64, 55, 0.1);
        }

        .navbar a.active {
            background: #5d4037;
            color: white;
        }

        /* Hamburger Menu */
        .hamburger {
            display: none;
            flex-direction: column;
            gap: 5px;
            cursor: pointer;
            padding: 0.5rem;
        }

        .hamburger span {
            width: 25px;
            height: 3px;
            background: #5d4037;
            border-radius: 3px;
            transition: all 0.3s ease;
        }

        .hamburger.active span:nth-child(1) {
            transform: rotate(45deg) translate(8px, 8px);
        }

        .hamburger.active span:nth-child(2) {
            opacity: 0;
        }

        .hamburger.active span:nth-child(3) {
            transform: rotate(-45deg) translate(7px, -7px);
        }

        /* Dropdown Styles */
        .dropdown {
            position: relative;
        }

        .dropdown-btn {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            background: none;
            border: none;
            cursor: pointer;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .dropdown-btn:hover {
            background: rgba(93, 64, 55, 0.1);
        }

        .username {
            font-weight: 500;
            color: #5d4037;
        }

        .avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: #6c5ce7;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 1rem;
        }

        .dropdown-content {
            position: absolute;
            top: calc(100% + 0.5rem);
            right: 0;
            min-width: 200px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s ease;
        }

        .dropdown.active .dropdown-content {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .dropdown-content a {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            padding: 0.9rem 1.2rem;
            color: #5d4037;
            text-decoration: none;
            transition: all 0.2s ease;
            border-radius: 0;
        }

        .dropdown-content a:first-child {
            border-radius: 12px 12px 0 0;
        }

        .dropdown-content a:last-child {
            border-radius: 0 0 12px 12px;
        }

        .dropdown-content a:hover {
            background: rgba(93, 64, 55, 0.1);
        }

        .icon {
            width: 20px;
            height: 20px;
        }

        /* Mobile Navigation */
        .nav-links {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .navbar {
                padding: 1rem 1.5rem;
            }

            .hamburger {
                display: flex;
            }

            .nav-links {
                position: absolute;
                top: 70px;
                left: -100%;
                width: 100%;
                background: rgba(255, 255, 255, 0.98);
                backdrop-filter: blur(10px);
                flex-direction: column;
                justify-content: flex-start;
                padding: 2rem;
                gap: 0;
                transition: left 0.3s ease;
            }

            .nav-links.active {
                left: 0;
            }

            .navbar a {
                width: 100%;
                text-align: left;
                padding: 1rem;
                border-bottom: 1px solid rgba(93, 64, 55, 0.1);
            }

            .dropdown {
                width: 100%;
            }

            .dropdown-btn {
                width: 100%;
                justify-content: space-between;
                padding: 1rem;
                border-bottom: 1px solid rgba(93, 64, 55, 0.1);
            }

            .dropdown-content {
                position: static;
                box-shadow: none;
                opacity: 1;
                visibility: visible;
                transform: none;
                max-height: 0;
                overflow: hidden;
                transition: max-height 0.3s ease;
            }

            .dropdown.active .dropdown-content {
                max-height: 200px;
            }

            .dropdown-content a {
                padding-left: 2rem;
            }
        }

        @media (max-width: 480px) {
            .navbar p {
                font-size: 1.2rem;
            }

            .navbar {
                padding: 0.8rem 1rem;
            }
        }
    </style>
</head>

<body>
    <div class="navbar" id="navbar">
        <p>Rahmat Bakery</p>

        <div class="hamburger" id="hamburger">
            <span></span>
            <span></span>
            <span></span>
        </div>

        <div class="nav-links" id="navLinks">
            <a href="index.php" class="<?= ($current_page == 'index.php') ? 'active' : '' ?>">Beranda</a>
            <a href="produk.php" class="<?= ($current_page == 'produk.php') ? 'active' : '' ?>">Produk</a>

            <?php if ($isLoggedIn): ?>
                <a href="kustom.php" class="<?= ($current_page == 'kustom.php') ? 'active' : '' ?>">Kustom Pesanan</a>
                <a href="keranjang.php" class="<?= ($current_page == 'keranjang.php') ? 'active' : '' ?>">Keranjang</a>
            <?php endif; ?>

            <?php if ($isLoggedIn): ?>
                <div class="dropdown" id="userDropdown">
                    <button class="dropdown-btn">
                        <div style="display: flex; align-items: center; gap: 0.8rem;">
                            <span class="username"><?= htmlspecialchars($username) ?></span>
                            <div class="avatar"><?= $initial ?></div>
                        </div>
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
    </div>

    <script>
        // Scroll Effect
        window.addEventListener("scroll", function() {
            const navbar = document.getElementById("navbar");
            navbar.classList.toggle("scrolled", window.scrollY > 50);
        });

        // Hamburger Menu Toggle
        const hamburger = document.getElementById('hamburger');
        const navLinks = document.getElementById('navLinks');

        hamburger.addEventListener('click', function() {
            this.classList.toggle('active');
            navLinks.classList.toggle('active');
        });

        // Close menu when clicking on a link (mobile)
        const links = navLinks.querySelectorAll('a:not(.dropdown-content a)');
        links.forEach(link => {
            link.addEventListener('click', function() {
                hamburger.classList.remove('active');
                navLinks.classList.remove('active');
            });
        });

        // Dropdown Toggle
        const dropdown = document.getElementById('userDropdown');
        const dropdownBtn = dropdown?.querySelector('.dropdown-btn');

        if (dropdownBtn) {
            dropdownBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                dropdown.classList.toggle('active');
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!dropdown.contains(e.target)) {
                    dropdown.classList.remove('active');
                }
            });
        }
    </script>
</body>

</html>