<?php require_once __DIR__ . '/includes/sidebar.php'; ?>

<div class="adm-content" id="content">
    <div class="adm-welcome">
        <h1 class="adm-welcome-title">Welcome back, Admin! 👋</h1>
        <p class="adm-welcome-subtitle">Here's what's happening with your store today</p>
    </div>

    <div class="adm-stats-grid">
        <div class="adm-stat-card">
            <div class="adm-stat-header">
                <div>
                    <div class="adm-stat-label">Total Revenue</div>
                </div>
                <div class="adm-stat-icon">💰</div>
            </div>
            <div class="adm-stat-value">$1,24,532</div>
            <div class="adm-stat-change adm-up">
                ↑ 12.5% from last month
            </div>
        </div>

        <div class="adm-stat-card adm-success">
            <div class="adm-stat-header">
                <div>
                    <div class="adm-stat-label">Total Orders</div>
                </div>
                <div class="adm-stat-icon">📦</div>
            </div>
            <div class="adm-stat-value">2,847</div>
            <div class="adm-stat-change adm-up">
                ↑ 8.2% from last month
            </div>
        </div>

        <div class="adm-stat-card adm-warning">
            <div class="adm-stat-header">
                <div>
                    <div class="adm-stat-label">Products</div>
                </div>
                <div class="adm-stat-icon">📱</div>
            </div>
            <div class="adm-stat-value">567</div>
            <div class="adm-stat-change adm-up">
                ↑ 15 new products
            </div>
        </div>

        <div class="adm-stat-card adm-info">
            <div class="adm-stat-header">
                <div>
                    <div class="adm-stat-label">Active Users</div>
                </div>
                <div class="adm-stat-icon">👥</div>
            </div>
            <div class="adm-stat-value">12,432</div>
            <div class="adm-stat-change adm-up">
                ↑ 18.7% from last month
            </div>
        </div>
    </div>

    <div class="adm-card">
        <div class="adm-card-header">
            <h3 class="adm-card-title">Quick Actions</h3>
        </div>
        <div class="adm-card-actions">
            <a href="/online-computer-store/products.php" class="adm-btn adm-btn-primary">
                ➕ Add New Product
            </a>
            <a href="/online-computer-store/admincategories.php" class="adm-btn adm-btn-success">
                📁 Manage Categories
            </a>
            <a href="/online-computer-store/admin_order_history.php" class="adm-btn adm-btn-secondary">
                📋 View Orders
            </a>
            <a href="/online-computer-store/userdetails.php" class="adm-btn adm-btn-secondary">
                👥 Manage Users
            </a>
        </div>
    </div>

    <div class="adm-card">
        <div class="adm-card-header">
            <h3 class="adm-card-title">Recent Orders</h3>
            <a href="/online-computer-store/admin_order_history.php" class="adm-btn adm-btn-sm adm-btn-secondary">
                View All Orders
            </a>
        </div>
        <div class="adm-table-wrapper">
            <table class="adm-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Product</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>#ORD-12345</strong></td>
                        <td>John Doe</td>
                        <td>Smart Phone X Pro</td>
                        <td><strong>$24,999</strong></td>
                        <td><span class="adm-badge adm-badge-success">Delivered</span></td>
                        <td>Dec 01, 2025</td>
                        <td>
                            <a href="#" class="adm-btn adm-btn-sm adm-btn-secondary">View</a>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>#ORD-12344</strong></td>
                        <td>Jane Smith</td>
                        <td>Gaming Laptop Ultra</td>
                        <td><strong>$89,999</strong></td>
                        <td><span class="adm-badge adm-badge-warning">Processing</span></td>
                        <td>Nov 30, 2025</td>
                        <td>
                            <a href="#" class="adm-btn adm-btn-sm adm-btn-secondary">View</a>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>#ORD-12343</strong></td>
                        <td>Mike Johnson</td>
                        <td>Wireless Headphones</td>
                        <td><strong>$2,999</strong></td>
                        <td><span class="adm-badge adm-badge-primary">Shipped</span></td>
                        <td>Nov 30, 2025</td>
                        <td>
                            <a href="#" class="adm-btn adm-btn-sm adm-btn-secondary">View</a>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>#ORD-12342</strong></td>
                        <td>Sarah Williams</td>
                        <td>Tablet Pro 12.9"</td>
                        <td><strong>$54,999</strong></td>
                        <td><span class="adm-badge adm-badge-danger">Cancelled</span></td>
                        <td>Nov 29, 2025</td>
                        <td>
                            <a href="#" class="adm-btn adm-btn-sm adm-btn-secondary">View</a>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>#ORD-12341</strong></td>
                        <td>Robert Brown</td>
                        <td>Smart Watch Series 8</td>
                        <td><strong>$18,999</strong></td>
                        <td><span class="adm-badge adm-badge-success">Delivered</span></td>
                        <td>Nov 29, 2025</td>
                        <td>
                            <a href="#" class="adm-btn adm-btn-sm adm-btn-secondary">View</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="adm-card">
        <div class="adm-card-header">
            <h3 class="adm-card-title">Top Selling Products</h3>
            <a href="/online-computer-store/products.php" class="adm-btn adm-btn-sm adm-btn-secondary">
                View All Products
            </a>
        </div>
        <div class="adm-table-wrapper">
            <table class="adm-table">
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Sales</th>
                        <th>Revenue</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Smart Phone X Pro</strong></td>
                        <td>Smartphones</td>
                        <td>$24,999</td>
                        <td><span class="adm-badge adm-badge-success">In Stock</span></td>
                        <td>156 units</td>
                        <td><strong>$38,99,844</strong></td>
                    </tr>
                    <tr>
                        <td><strong>Gaming Laptop Ultra</strong></td>
                        <td>Laptops</td>
                        <td>$89,999</td>
                        <td><span class="adm-badge adm-badge-warning">Low Stock</span></td>
                        <td>87 units</td>
                        <td><strong>$78,29,913</strong></td>
                    </tr>
                    <tr>
                        <td><strong>Wireless Headphones</strong></td>
                        <td>Accessories</td>
                        <td>$2,999</td>
                        <td><span class="adm-badge adm-badge-success">In Stock</span></td>
                        <td>234 units</td>
                        <td><strong>$7,01,766</strong></td>
                    </tr>
                    <tr>
                        <td><strong>Tablet Pro 12.9"</strong></td>
                        <td>Tablets</td>
                        <td>$54,999</td>
                        <td><span class="adm-badge adm-badge-danger">Out of Stock</span></td>
                        <td>64 units</td>
                        <td><strong>$35,19,936</strong></td>
                    </tr>
                    <tr>
                        <td><strong>Smart Watch Series 8</strong></td>
                        <td>Wearables</td>
                        <td>$18,999</td>
                        <td><span class="adm-badge adm-badge-success">In Stock</span></td>
                        <td>143 units</td>
                        <td><strong>$27,16,857</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>