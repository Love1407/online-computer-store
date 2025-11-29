<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Dashboard</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            display: flex;
            min-height: 100vh;
            background: #f4f4f4;
        }

        /* Sidebar */
        .sidebar {
            width: 250px;
            background: #1e1e2d;
            color: #fff;
            transition: width 0.3s ease;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }

        .sidebar.collapsed {
            width: 70px;
        }

        .sidebar .toggle-btn {
            background: #27293d;
            padding: 15px;
            cursor: pointer;
            text-align: center;
            font-size: 18px;
        }

        .menu {
            list-style: none;
            margin-top: 20px;
        }

        .menu-item {
            padding: 15px 20px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 15px;
            transition: background 0.3s ease;
        }

        .menu-item:hover {
            background: #34344a;
        }

        .menu-item i {
            font-size: 20px;
        }

        .menu-item span {
            white-space: nowrap;
            transition: opacity 0.3s ease;
        }

        .collapsed .menu-item span {
            opacity: 0;
        }

        /* Content */
        .content {
            margin-left: 250px;
            padding: 20px;
            width: 100%;
            transition: margin-left 0.3s ease;
        }

        .collapsed-content {
            margin-left: 70px;
        }

        header {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        /* Responsive */
        @media(max-width: 768px) {
            .sidebar {
                position: absolute;
                z-index: 20;
                left: -250px;
            }
            .sidebar.open {
                left: 0;
            }
            .content {
                margin-left: 0;
            }
        }
         .wrap { max-width:900px; margin:30px auto; padding:20px; background:white; border-radius:6px; box-shadow:0 6px 18px rgba(0,0,0,0.06); }
    h2 { margin-top:0; }
    form { max-width:480px; }
    label { display:block; margin-top:12px; font-weight:600; }
    input[type="text"], select { width:100%; padding:10px; margin-top:6px; box-sizing:border-box; border:1px solid #ddd; border-radius:4px; }
    button { margin-top:14px; padding:10px 14px; border:none; background:#2b6cb0; color:white; border-radius:6px; cursor:pointer; }
    .messages { margin-top:12px; }
    .error { background:#ffe7e7; color:#900; padding:10px; border-radius:4px; margin-bottom:8px; }
    .success { background:#e6ffed; color:#085f2a; padding:10px; border-radius:4px; margin-bottom:8px; }
    .note { color:#666; font-size:0.9rem; margin-top:6px; }
    .form-box { background: #f8f8f8; padding: 15px; border-radius: 8px; width: 400px; }
input, select { width: 100%; padding: 8px; margin: 8px 0; }
button { padding: 8px 15px; cursor: pointer; }
table { width: 100%; border-collapse: collapse; margin-top: 25px; }
table, th, td { border: 1px solid #888; }
th, td { padding: 10px; text-align: left; }
.actions a { margin-right: 10px; }
    </style>
</head>
<body>
    <div class="sidebar" id="sidebar">
        <div class="toggle-btn" onclick="toggleSidebar()">☰</div>
        <ul class="menu">
            <li class="menu-item"><a href="/online-computer-store/admincategories.php"><i>📁</i><span>Categories</span></li></a>
            <li class="menu-item"><a href="/online-computer-store/adminsubcategories.php"><i>📂</i><span>Sub Categories</span></li></a>
            <li class="menu-item"><a href="/online-computer-store/products.php"><i>📦</i><span>Products</span></li></a>
            <li class="menu-item"><a href="/online-computer-store/userdetails.php"><i>👤</i><span>Users</span></li></a>
            <li class="menu-item"><a href="/online-computer-store/logout.php"><i>⚙️</i><span>Logout</span></li></a>
        </ul>
    </div>

       <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const content = document.getElementById('content');

            // Desktop collapse
            if (window.innerWidth > 768) {
                sidebar.classList.toggle('collapsed');
                content.classList.toggle('collapsed-content');
            } else {
                // Mobile slide-in
                sidebar.classList.toggle('open');
            }
        }
    </script>