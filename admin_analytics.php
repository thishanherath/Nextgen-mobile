<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

// Simple admin check
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: login.php');
    exit();
}

$conn = getDBConnection();

// Get date range from request or default to last 30 days
$end_date = date('Y-m-d');
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d', strtotime('-30 days'));

// Calculate total income from all sold products
$total_income_query = "SELECT SUM(total_amount) as total_income FROM orders WHERE status IN ('delivered', 'shipped', 'processing')";
$total_income_stmt = $conn->query($total_income_query);
$total_income = $total_income_stmt->fetch(PDO::FETCH_ASSOC)['total_income'] ?? 0;

// Calculate total number of sold products (sum of all quantities sold)
$total_sold_products_query = "SELECT SUM(oi.quantity) as total_sold_products FROM order_items oi JOIN orders o ON oi.order_id = o.id WHERE o.status IN ('delivered', 'shipped', 'processing')";
$total_sold_products_stmt = $conn->query($total_sold_products_query);
$total_sold_products = $total_sold_products_stmt->fetch(PDO::FETCH_ASSOC)['total_sold_products'] ?? 0;

// Calculate monthly revenue
$monthly_revenue_query = "SELECT 
    DATE_FORMAT(created_at, '%Y-%m') as month,
    SUM(total_amount) as revenue,
    COUNT(*) as orders_count
FROM orders 
WHERE status IN ('delivered', 'shipped', 'processing') 
AND created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
GROUP BY DATE_FORMAT(created_at, '%Y-%m') 
ORDER BY month DESC";
$monthly_revenue = $conn->query($monthly_revenue_query)->fetchAll(PDO::FETCH_ASSOC);

// Revenue Analytics - Daily breakdown
$revenue_query = "SELECT DATE(created_at) as date, SUM(total_amount) as revenue, COUNT(*) as orders_count FROM orders WHERE status IN ('delivered', 'shipped', 'processing') AND created_at BETWEEN ? AND ? GROUP BY DATE(created_at) ORDER BY date";
$stmt = $conn->prepare($revenue_query);
$stmt->execute([$start_date, $end_date]);
$revenue_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate revenue growth
$current_month_revenue = 0;
$previous_month_revenue = 0;
if (count($monthly_revenue) >= 2) {
    $current_month_revenue = $monthly_revenue[0]['revenue'] ?? 0;
    $previous_month_revenue = $monthly_revenue[1]['revenue'] ?? 0;
}
$revenue_growth = $previous_month_revenue > 0 ? (($current_month_revenue - $previous_month_revenue) / $previous_month_revenue) * 100 : 0;

// Calculate average order value
$avg_order_value = $conn->query("SELECT AVG(total_amount) as avg_value FROM orders WHERE status IN ('delivered', 'shipped', 'processing')")->fetch(PDO::FETCH_ASSOC)['avg_value'] ?? 0;

// Product Performance with Revenue
$product_performance = "SELECT 
    p.name, 
    p.brand,
    COUNT(oi.product_id) as total_sold, 
    SUM(oi.quantity) as quantity_sold, 
    SUM(oi.quantity * oi.price) as total_revenue,
    p.stock as stock_quantity 
FROM order_items oi 
JOIN products p ON oi.product_id = p.id 
JOIN orders o ON oi.order_id = o.id 
WHERE o.status IN ('delivered', 'shipped', 'processing') 
GROUP BY p.id 
ORDER BY total_revenue DESC 
LIMIT 10";
$product_data = $conn->query($product_performance)->fetchAll(PDO::FETCH_ASSOC);

// Customer Analytics
$customer_analytics = "SELECT 
    COUNT(DISTINCT CASE WHEN order_count = 1 THEN user_id END) as new_customers, 
    COUNT(DISTINCT CASE WHEN order_count > 1 THEN user_id END) as returning_customers, 
    AVG(total_spent) as avg_customer_value,
    COUNT(DISTINCT user_id) as total_customers
FROM (
    SELECT user_id, COUNT(*) as order_count, SUM(total_amount) as total_spent 
    FROM orders 
    WHERE status IN ('delivered', 'shipped', 'processing') 
    GROUP BY user_id
) as customer_stats";
$customer_data = $conn->query($customer_analytics)->fetch(PDO::FETCH_ASSOC);

// Order Status Distribution
$order_status = "SELECT status, COUNT(*) as count, SUM(total_amount) as total_value FROM orders GROUP BY status";
$status_data = $conn->query($order_status)->fetchAll(PDO::FETCH_ASSOC);

// Top Customers by Revenue
$top_customers = "SELECT 
    u.name,
    u.email,
    COUNT(o.id) as order_count,
    SUM(o.total_amount) as total_spent,
    MAX(o.created_at) as last_order
FROM orders o
JOIN users u ON o.user_id = u.id
WHERE o.status IN ('delivered', 'shipped', 'processing')
GROUP BY u.id
ORDER BY total_spent DESC
LIMIT 5";
$top_customers_data = $conn->query($top_customers)->fetchAll(PDO::FETCH_ASSOC);

// Revenue Forecasting (Simple moving average)
$forecast_query = "SELECT 
    DATE(created_at) as date, 
    AVG(total_amount) OVER (ORDER BY created_at ROWS BETWEEN 6 PRECEDING AND CURRENT ROW) as forecast,
    SUM(total_amount) as actual_revenue
FROM orders 
WHERE status IN ('delivered', 'shipped', 'processing') 
AND created_at BETWEEN ? AND ? 
GROUP BY DATE(created_at) 
ORDER BY date";
$stmt = $conn->prepare($forecast_query);
$stmt->execute([$start_date, $end_date]);
$forecast_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Payment Method Distribution
$payment_methods = "SELECT 
    payment_method, 
    COUNT(*) as count, 
    SUM(total_amount) as total_value,
    ROUND((COUNT(*) * 100.0 / (SELECT COUNT(*) FROM orders)), 2) as percentage
FROM orders 
GROUP BY payment_method 
ORDER BY total_value DESC";
$payment_data = $conn->query($payment_methods)->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics Dashboard - NEXTGEN</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Shared Admin Responsive CSS -->
    <link href="assets/css/admin-responsive.css" rel="stylesheet">
    
    <style>
        :root {
            --admin-primary: #4B49AC;
            --admin-secondary: #98BDFF;
            --admin-bg: #F4F6FC;
            --admin-card: #fff;
            --admin-sidebar: #4B49AC;
            --admin-sidebar-active: #fff;
            --admin-sidebar-text: #fff;
            --admin-sidebar-active-text: #4B49AC;
        }
        body {
            background: var(--admin-bg);
        }
        .sidebar {
            width: 250px;
            min-height: 100vh;
            background: var(--admin-sidebar);
            color: var(--admin-sidebar-text);
            position: fixed;
            left: 0;
            top: 0;
            z-index: 1000;
            transition: left 0.3s ease;
        }
        
        .main-content {
            margin-left: 250px;
            width: calc(100% - 250px);
            min-height: 100vh;
            background: var(--admin-bg);
        }
        .sidebar .nav-link {
            color: var(--admin-sidebar-text);
            font-weight: 500;
            border-radius: 8px;
            margin-bottom: 0.5rem;
            transition: background 0.2s, color 0.2s;
        }
        .sidebar .nav-link.active, .sidebar .nav-link:hover {
            background: var(--admin-sidebar-active);
            color: var(--admin-sidebar-active-text);
        }
        .dashboard-header {
            background: var(--admin-card);
            border-bottom: 2px solid var(--admin-primary);
        }
        .card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            background: var(--admin-card);
            margin-bottom: 1.5rem;
        }
        .metric-card {
            text-align: center;
            padding: 1.5rem;
        }
        .metric-value {
            font-size: 2rem;
            font-weight: bold;
            color: var(--admin-primary);
        }
        .metric-label {
            color: #666;
            font-size: 0.9rem;
        }
        
        .sidebar-logo {
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--admin-sidebar-active);
            padding: 0.5rem 1rem;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.1);
        }
        
        /* Mobile Responsive Design */
        @media (max-width: 1200px) {
            .sidebar {
                width: 220px;
            }
            .main-content {
                margin-left: 220px;
                width: calc(100% - 220px);
            }
        }
        
        @media (max-width: 992px) {
            .sidebar {
                width: 200px;
            }
            .main-content {
                margin-left: 200px;
                width: calc(100% - 200px);
            }
            .metric-value {
                font-size: 1.5rem;
            }
        }
        
        @media (max-width: 768px) {
            .sidebar {
                left: -100%;
                width: 280px;
                z-index: 1050;
            }
            .sidebar.show {
                left: 0;
            }
            .main-content {
                margin-left: 0;
                width: 100%;
            }
            .dashboard-header {
                padding: 1rem;
                flex-direction: column;
                gap: 1rem;
            }
            .dashboard-header form {
                flex-direction: column;
                gap: 0.5rem;
                width: 100%;
            }
            .dashboard-header form input,
            .dashboard-header form button {
                width: 100%;
            }
            .metric-card {
                padding: 1rem;
            }
            .metric-value {
                font-size: 1.25rem;
            }
            .card {
                margin-bottom: 1rem;
            }
            .table-responsive {
                font-size: 0.875rem;
            }
            .table th, .table td {
                padding: 0.5rem 0.25rem;
            }
        }
        
        @media (max-width: 576px) {
            .container-fluid {
                padding: 0.5rem;
            }
            .metric-card {
                padding: 0.75rem;
            }
            .metric-value {
                font-size: 1.1rem;
            }
            .metric-label {
                font-size: 0.8rem;
            }
            .dashboard-header h1 {
                font-size: 1.5rem;
            }
            .card h4 {
                font-size: 1.25rem;
            }
            .table-responsive {
                font-size: 0.8rem;
            }
            .table th, .table td {
                padding: 0.25rem 0.125rem;
            }
            .badge {
                font-size: 0.7rem;
            }
        }
        
        @media (max-width: 480px) {
            .row.g-3 {
                --bs-gutter-x: 0.5rem;
                --bs-gutter-y: 0.5rem;
            }
            .metric-card {
                padding: 0.5rem;
            }
            .metric-value {
                font-size: 1rem;
            }
            .card {
                padding: 0.75rem !important;
            }
            .table-responsive {
                font-size: 0.75rem;
            }
            .chart-container {
                height: 250px !important;
            }
        }
        
        /* Mobile Menu Toggle */
        .mobile-menu-toggle {
            display: none;
            position: fixed;
            top: 1rem;
            left: 1rem;
            z-index: 1060;
            background: var(--admin-primary);
            border: none;
            color: white;
            padding: 0.5rem;
            border-radius: 0.25rem;
            font-size: 1.25rem;
        }
        
        @media (max-width: 768px) {
            .mobile-menu-toggle {
                display: block;
            }
        }
        
        /* Overlay for mobile menu */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1040;
        }
        
        @media (max-width: 768px) {
            .sidebar-overlay.show {
                display: block;
            }
        }
    </style>
</head>
<body>
    <!-- Mobile Menu Toggle -->
    <button class="mobile-menu-toggle" id="mobileMenuToggle">
        <i class="fas fa-bars"></i>
    </button>
    
    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    
    <div class="d-flex">
        <!-- Sidebar -->
        <div class="sidebar d-flex flex-column p-3" id="sidebar">
            <div class="mb-4 text-center">
                <span class="sidebar-logo">NEXTGEN</span>
            </div>
            <nav class="nav flex-column mb-auto">
                <a href="admin.php" class="nav-link<?= basename($_SERVER['PHP_SELF']) == 'admin.php' ? ' active' : '' ?>"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a>
                <a href="admin_analytics.php" class="nav-link<?= basename($_SERVER['PHP_SELF']) == 'admin_analytics.php' ? ' active' : '' ?>"><i class="fas fa-chart-line me-2"></i>Analytics</a>
                <a href="admin_orders.php" class="nav-link<?= basename($_SERVER['PHP_SELF']) == 'admin_orders.php' ? ' active' : '' ?>"><i class="fas fa-box me-2"></i>Order Management</a>
                <a href="admin_users.php" class="nav-link<?= basename($_SERVER['PHP_SELF']) == 'admin_users.php' ? ' active' : '' ?>"><i class="fas fa-users me-2"></i>User Management</a>
                <a href="admin_products.php" class="nav-link<?= basename($_SERVER['PHP_SELF']) == 'admin_products.php' ? ' active' : '' ?>"><i class="fas fa-mobile-alt me-2"></i>Product Management</a>
                <a href="admin_audit_logs.php" class="nav-link<?= basename($_SERVER['PHP_SELF']) == 'admin_audit_logs.php' ? ' active' : '' ?>"><i class="fas fa-clipboard-list me-2"></i>Audit Logs</a>
                <a href="admin_reviews.php" class="nav-link<?= basename($_SERVER['PHP_SELF']) == 'admin_reviews.php' ? ' active' : '' ?>"><i class="fas fa-star me-2"></i>Customer Reviews</a>
            </nav>
            <div class="mt-auto text-center">
                <a href="logout.php" class="btn btn-light w-100 mt-4"><i class="fas fa-sign-out-alt me-2"></i>Logout</a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content flex-grow-1">
            <div class="dashboard-header d-flex justify-content-between align-items-center p-4 mb-4">
                <h1 class="h3 mb-0">Analytics Dashboard</h1>
                <div>
                    <form class="d-flex gap-2">
                        <input type="date" class="form-control" name="start_date" value="<?= $start_date ?>">
                        <input type="date" class="form-control" name="end_date" value="<?= $end_date ?>">
                        <button type="submit" class="btn btn-primary">Update</button>
                    </form>
                </div>
            </div>

            <div class="container-fluid">
                <!-- Key Metrics -->
                <div class="row g-3">
                    <div class="col-lg-2 col-md-4 col-sm-6">
                        <div class="card metric-card">
                            <div class="metric-value">LKR <?= number_format($total_income, 0) ?></div>
                            <div class="metric-label">Total Revenue</div>
                            <small class="text-<?= $revenue_growth >= 0 ? 'success' : 'danger' ?>">
                                <?= $revenue_growth >= 0 ? '+' : '' ?><?= number_format($revenue_growth, 1) ?>% vs last month
                            </small>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-4 col-sm-6">
                        <div class="card metric-card">
                            <div class="metric-value">LKR <?= number_format($avg_order_value, 0) ?></div>
                            <div class="metric-label">Avg Order Value</div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-4 col-sm-6">
                        <div class="card metric-card">
                            <div class="metric-value"><?= number_format($total_sold_products) ?></div>
                            <div class="metric-label">Products Sold</div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-4 col-sm-6">
                        <div class="card metric-card">
                            <div class="metric-value"><?= number_format($customer_data['total_customers'] ?? 0) ?></div>
                            <div class="metric-label">Total Customers</div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-4 col-sm-6">
                        <div class="card metric-card">
                            <div class="metric-value"><?= number_format($customer_data['new_customers'] ?? 0) ?></div>
                            <div class="metric-label">New Customers</div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-4 col-sm-6">
                        <div class="card metric-card">
                            <div class="metric-value"><?= number_format($customer_data['returning_customers'] ?? 0) ?></div>
                            <div class="metric-label">Returning Customers</div>
                        </div>
                    </div>
                </div>

                <!-- Revenue Charts Row -->
                <div class="row g-3">
                    <div class="col-lg-8 col-md-12">
                        <div class="card p-4">
                            <h4>Daily Revenue Analytics</h4>
                            <div class="chart-container" style="position: relative; height: 400px;">
                                <canvas id="revenueChart"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-12">
                        <div class="card p-4">
                            <h4>Monthly Revenue Trend</h4>
                            <div class="chart-container" style="position: relative; height: 400px;">
                                <canvas id="monthlyRevenueChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Product Performance -->
                <div class="card p-4">
                    <h4>Top Revenue Generating Products</h4>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Brand</th>
                                    <th>Orders</th>
                                    <th>Quantity Sold</th>
                                    <th>Revenue</th>
                                    <th>Stock Level</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($product_data as $product): ?>
                                <tr>
                                    <td><?= htmlspecialchars($product['name']) ?></td>
                                    <td><?= htmlspecialchars($product['brand']) ?></td>
                                    <td><?= $product['total_sold'] ?></td>
                                    <td><?= $product['quantity_sold'] ?></td>
                                    <td><strong>LKR <?= number_format($product['total_revenue'], 0) ?></strong></td>
                                    <td><?= $product['stock_quantity'] ?></td>
                                    <td>
                                        <?php if ($product['stock_quantity'] <= 5): ?>
                                            <span class="badge bg-danger">Low Stock</span>
                                        <?php elseif ($product['stock_quantity'] <= 10): ?>
                                            <span class="badge bg-warning">Medium Stock</span>
                                        <?php else: ?>
                                            <span class="badge bg-success">In Stock</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Top Customers -->
                <div class="card p-4">
                    <h4>Top Customers by Revenue</h4>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Customer</th>
                                    <th>Email</th>
                                    <th>Orders</th>
                                    <th>Total Spent</th>
                                    <th>Last Order</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($top_customers_data as $customer): ?>
                                <tr>
                                    <td><?= htmlspecialchars($customer['name']) ?></td>
                                    <td><?= htmlspecialchars($customer['email']) ?></td>
                                    <td><?= $customer['order_count'] ?></td>
                                    <td><strong>LKR <?= number_format($customer['total_spent'], 0) ?></strong></td>
                                    <td><?= date('M d, Y', strtotime($customer['last_order'])) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Analytics Charts Row -->
                <div class="row g-3">
                    <div class="col-lg-4 col-md-6 col-sm-12">
                        <div class="card p-4">
                            <h4>Order Status Distribution</h4>
                            <div class="chart-container" style="position: relative; height: 300px;">
                                <canvas id="orderStatusChart"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 col-sm-12">
                        <div class="card p-4">
                            <h4>Payment Methods</h4>
                            <div class="chart-container" style="position: relative; height: 300px;">
                                <canvas id="paymentMethodChart"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-12 col-sm-12">
                        <div class="card p-4">
                            <h4>Revenue Forecast</h4>
                            <div class="chart-container" style="position: relative; height: 300px;">
                                <canvas id="forecastChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Shared Admin Mobile JS -->
    <script src="assets/js/admin-mobile.js"></script>
    
    <script>
        // Mobile Menu Functionality
        $(document).ready(function() {
            const mobileMenuToggle = $('#mobileMenuToggle');
            const sidebar = $('#sidebar');
            const sidebarOverlay = $('#sidebarOverlay');
            
            // Toggle mobile menu
            mobileMenuToggle.on('click', function() {
                sidebar.toggleClass('show');
                sidebarOverlay.toggleClass('show');
                $('body').toggleClass('menu-open');
            });
            
            // Close menu when clicking overlay
            sidebarOverlay.on('click', function() {
                sidebar.removeClass('show');
                sidebarOverlay.removeClass('show');
                $('body').removeClass('menu-open');
            });
            
            // Close menu when clicking on a link (mobile)
            $('.sidebar .nav-link').on('click', function() {
                if ($(window).width() <= 768) {
                    sidebar.removeClass('show');
                    sidebarOverlay.removeClass('show');
                    $('body').removeClass('menu-open');
                }
            });
            
            // Handle window resize
            $(window).on('resize', function() {
                if ($(window).width() > 768) {
                    sidebar.removeClass('show');
                    sidebarOverlay.removeClass('show');
                    $('body').removeClass('menu-open');
                }
            });
        });
        
        // Revenue Chart
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: <?= json_encode(array_column($revenue_data, 'date')) ?>,
                datasets: [{
                    label: 'Daily Revenue (LKR)',
                    data: <?= json_encode(array_column($revenue_data, 'revenue')) ?>,
                    borderColor: '#4B49AC',
                    backgroundColor: 'rgba(75, 73, 172, 0.1)',
                    tension: 0.1,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Daily Revenue Trend'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'LKR ' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });

        // Monthly Revenue Chart
        const monthlyCtx = document.getElementById('monthlyRevenueChart').getContext('2d');
        new Chart(monthlyCtx, {
            type: 'bar',
            data: {
                labels: <?= json_encode(array_column($monthly_revenue, 'month')) ?>,
                datasets: [{
                    label: 'Monthly Revenue (LKR)',
                    data: <?= json_encode(array_column($monthly_revenue, 'revenue')) ?>,
                    backgroundColor: '#98BDFF',
                    borderColor: '#4B49AC',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Monthly Revenue'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'LKR ' + (value / 1000) + 'K';
                            }
                        }
                    }
                }
            }
        });

        // Order Status Chart
        const statusCtx = document.getElementById('orderStatusChart').getContext('2d');
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: <?= json_encode(array_column($status_data, 'status')) ?>,
                datasets: [{
                    data: <?= json_encode(array_column($status_data, 'count')) ?>,
                    backgroundColor: [
                        '#4B49AC',
                        '#98BDFF',
                        '#FFB74D',
                        '#4CAF50',
                        '#F44336'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Order Status Distribution'
                    }
                }
            }
        });

        // Payment Method Chart
        const paymentCtx = document.getElementById('paymentMethodChart').getContext('2d');
        new Chart(paymentCtx, {
            type: 'pie',
            data: {
                labels: <?= json_encode(array_column($payment_data, 'payment_method')) ?>,
                datasets: [{
                    data: <?= json_encode(array_column($payment_data, 'total_value')) ?>,
                    backgroundColor: [
                        '#4CAF50',
                        '#2196F3',
                        '#FF9800',
                        '#9C27B0',
                        '#F44336'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Payment Methods by Revenue'
                    }
                }
            }
        });

        // Forecast Chart
        const forecastCtx = document.getElementById('forecastChart').getContext('2d');
        new Chart(forecastCtx, {
            type: 'line',
            data: {
                labels: <?= json_encode(array_column($forecast_data, 'date')) ?>,
                datasets: [{
                    label: 'Actual Revenue',
                    data: <?= json_encode(array_column($forecast_data, 'actual_revenue')) ?>,
                    borderColor: '#4B49AC',
                    backgroundColor: 'rgba(75, 73, 172, 0.1)',
                    tension: 0.1
                }, {
                    label: 'Forecast (7-day avg)',
                    data: <?= json_encode(array_column($forecast_data, 'forecast')) ?>,
                    borderColor: '#4CAF50',
                    borderDash: [5, 5],
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Revenue Forecast vs Actual'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'LKR ' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    </script>
</body>
</html> 