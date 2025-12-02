<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Dashboard - LoveMart</title>
    <link rel="stylesheet" href="assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
    <div class="adm-sidebar" id="admSidebar">
        <div class="adm-logo">
            <h1 class="adm-logo-text">LoveMart Admin</h1>
            <div class="adm-logo-icon"></div>
        </div>

        <button class="adm-toggle-btn" onclick="toggleSidebar()">
            ☰
        </button>
        <ul class="adm-menu">
            <li class="adm-menu-item <?= basename($_SERVER['PHP_SELF']) == 'admin.php' ? 'adm-active' : '' ?>">
                <a href="/online-computer-store/admin.php">
                    <span class="adm-menu-icon"><i class="fas fa-chart-line"></i></span>
                    <span class="adm-menu-text">Dashboard</span>
                </a>
            </li>
            
            <li class="adm-menu-item <?= basename($_SERVER['PHP_SELF']) == 'admincategories.php' ? 'adm-active' : '' ?>">
                <a href="/online-computer-store/admincategories.php">
                    <span class="adm-menu-icon"><i class="fas fa-th-large"></i></span>
                    <span class="adm-menu-text">Categories</span>
                </a>
            </li>
            
            <li class="adm-menu-item <?= basename($_SERVER['PHP_SELF']) == 'adminsubcategories.php' ? 'adm-active' : '' ?>">
                <a href="/online-computer-store/adminsubcategories.php">
                    <span class="adm-menu-icon"><i class="fas fa-layer-group"></i></span>
                    <span class="adm-menu-text">Subcategories</span>
                </a>
            </li>
            
            <li class="adm-menu-item <?= basename($_SERVER['PHP_SELF']) == 'products.php' ? 'adm-active' : '' ?>">
                <a href="/online-computer-store/products.php">
                    <span class="adm-menu-icon"><i class="fas fa-box-open"></i></span>
                    <span class="adm-menu-text">Products</span>
                </a>
            </li>
            
            <li class="adm-menu-item <?= basename($_SERVER['PHP_SELF']) == 'userdetails.php' ? 'adm-active' : '' ?>">
                <a href="/online-computer-store/userdetails.php">
                    <span class="adm-menu-icon"><i class="fas fa-users"></i></span>
                    <span class="adm-menu-text">Users</span>
                </a>
            </li>
            
            <li class="adm-menu-item <?= basename($_SERVER['PHP_SELF']) == 'admin_order_history.php' ? 'adm-active' : '' ?>">
                <a href="/online-computer-store/admin_order_history.php">
                    <span class="adm-menu-icon"><i class="fas fa-shopping-cart"></i></span>
                    <span class="adm-menu-text">Order History</span>
                </a>
            </li>
            
            <li class="adm-menu-item">
                <a href="/online-computer-store/logout.php">
                    <span class="adm-menu-icon"><i class="fas fa-sign-out-alt"></i></span>
                    <span class="adm-menu-text">Logout</span>
                </a>
            </li>
        </ul>
    </div>

    <button class="adm-mobile-toggle" onclick="toggleSidebar()">
        ☰
    </button>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('admSidebar');

            if (window.innerWidth > 1024) {
                sidebar.classList.toggle('adm-collapsed');
            } else {
                sidebar.classList.toggle('adm-open');
            }
        }

        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('admSidebar');
            const toggleBtn = document.querySelector('.adm-mobile-toggle');
            const sidebarToggle = document.querySelector('.adm-toggle-btn');
            
            if (window.innerWidth <= 1024 && 
                !sidebar.contains(event.target) && 
                !toggleBtn.contains(event.target) &&
                sidebar.classList.contains('adm-open')) {
                sidebar.classList.remove('adm-open');
            }
        });

        let resizeTimer;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                const sidebar = document.getElementById('admSidebar');
                
                if (window.innerWidth > 1024) {
                    sidebar.classList.remove('adm-open');
                } else {
                    sidebar.classList.remove('adm-collapsed');
                }
            }, 250);
        });
    </script>