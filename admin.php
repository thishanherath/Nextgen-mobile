<?php
session_start();

// Enhanced admin authentication
require_once 'config/database.php';
require_once 'includes/functions.php';

// Proper admin check with role verification
if (!isLoggedIn() || !isAdmin()) {
    header('Location: login.php');
    exit();
}

// Log admin activity
log_user_activity($_SESSION['user_id'], 'admin_dashboard_access', 'Accessed admin dashboard');

$conn = getDBConnection();

// Enhanced dashboard statistics
try {
    // Basic counts
    $orderCount = $conn->query("SELECT COUNT(*) FROM orders")->fetchColumn();
    $userCount = $conn->query("SELECT COUNT(*) FROM users WHERE role = 'user'")->fetchColumn();
    $productCount = $conn->query("SELECT COUNT(*) FROM products")->fetchColumn();
    
    // Revenue statistics
    $totalRevenue = $conn->query("SELECT SUM(total_amount) FROM orders WHERE status IN ('delivered', 'shipped', 'processing')")->fetchColumn() ?? 0;
    $pendingOrders = $conn->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'")->fetchColumn();
    $lowStockProducts = $conn->query("SELECT COUNT(*) FROM products WHERE stock <= 5")->fetchColumn();
    
    // Recent activity
    $recentOrders = $conn->query("SELECT o.*, u.name as customer_name FROM orders o LEFT JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC LIMIT 5")->fetchAll();
    $recentUsers = $conn->query("SELECT * FROM users WHERE role = 'user' ORDER BY created_at DESC LIMIT 5")->fetchAll();
    
    // System health
    $systemHealth = [
        'database_connected' => true,
        'uploads_writable' => is_writable('uploads/'),
        'session_active' => session_status() === PHP_SESSION_ACTIVE,
        'php_version' => PHP_VERSION
    ];
    
} catch (PDOException $e) {
    error_log("Admin Dashboard Error: " . $e->getMessage());
    $systemHealth['database_connected'] = false;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - NEXTGEN</title>
    
    <!-- Essential CSS Libraries -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Chart.js for analytics -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- DataTables for better tables -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    
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
            --success-color: #28a745;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --info-color: #17a2b8;
        }
        
        body {
            background: var(--admin-bg);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .sidebar {
            min-height: 100vh;
            background: var(--admin-sidebar);
            color: var(--admin-sidebar-text);
            position: fixed;
            width: 250px;
            z-index: 1000;
        }
        
        .sidebar .nav-link {
            color: var(--admin-sidebar-text);
            font-weight: 500;
            border-radius: 8px;
            margin-bottom: 0.5rem;
            transition: all 0.3s ease;
            padding: 0.75rem 1rem;
        }
        
        .sidebar .nav-link.active, .sidebar .nav-link:hover {
            background: var(--admin-sidebar-active);
            color: var(--admin-sidebar-active-text);
            transform: translateX(5px);
        }
        
        .main-content {
            margin-left: 250px;
        }
        
        .dashboard-header {
            background: var(--admin-card);
            border-bottom: 2px solid var(--admin-primary);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            background: var(--admin-card);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(0,0,0,0.1);
        }
        
        .card-icon {
            font-size: 2.5rem;
            color: var(--admin-primary);
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
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .btn-primary {
            background: var(--admin-primary);
            border: none;
            border-radius: 8px;
            padding: 0.5rem 1.5rem;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background: var(--admin-secondary);
            transform: translateY(-1px);
        }
        
        .sidebar-logo {
            font-size: 1.8rem;
            font-weight: 700;
            letter-spacing: 2px;
            color: var(--admin-primary);
            background: var(--admin-sidebar-active);
            padding: 0.5rem 1.5rem;
            border-radius: 1rem;
            display: inline-block;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            margin-bottom: 2rem;
        }
        
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .status-pending { background: var(--warning-color); color: #000; }
        .status-processing { background: var(--info-color); color: #fff; }
        .status-shipped { background: var(--primary-color); color: #fff; }
        .status-delivered { background: var(--success-color); color: #fff; }
        .status-cancelled { background: var(--danger-color); color: #fff; }
        
        .quick-actions .btn {
            margin: 0.25rem;
            border-radius: 8px;
            font-weight: 500;
        }
        
        .system-health {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .recent-activity {
            max-height: 400px;
            overflow-y: auto;
        }
        
        .activity-item {
            padding: 0.75rem;
            border-left: 3px solid var(--admin-primary);
            margin-bottom: 0.5rem;
            background: rgba(75, 73, 172, 0.05);
            border-radius: 0 8px 8px 0;
        }
        
        /* Mobile Responsive Design */
        @media (max-width: 1200px) {
            .sidebar {
                width: 220px;
            }
            .main-content {
                margin-left: 220px;
            }
        }
        
        @media (max-width: 992px) {
            .sidebar {
                width: 200px;
            }
            .main-content {
                margin-left: 200px;
            }
            .metric-value {
                font-size: 1.5rem;
            }
            .card-icon {
                font-size: 2rem;
            }
        }
        
        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                left: -100%;
                width: 280px;
                transition: left 0.3s ease;
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
            }
            .metric-card {
                padding: 1rem;
            }
            .metric-value {
                font-size: 1.25rem;
            }
            .card-icon {
                font-size: 1.75rem;
            }
            .quick-actions .btn {
                margin: 0.125rem;
                font-size: 0.875rem;
                padding: 0.375rem 0.75rem;
            }
            .activity-item {
                padding: 0.5rem;
            }
            .system-health {
                margin-top: 1rem;
            }
        }
        
        @media (max-width: 576px) {
            .container-fluid {
                padding: 0.5rem;
            }
            .dashboard-header {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
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
            .card-icon {
                font-size: 1.5rem;
            }
            .quick-actions {
                justify-content: center;
            }
            .quick-actions .btn {
                width: calc(50% - 0.25rem);
                margin: 0.125rem;
                font-size: 0.8rem;
                padding: 0.25rem 0.5rem;
            }
            .activity-item {
                padding: 0.5rem;
                font-size: 0.875rem;
            }
            .status-badge {
                font-size: 0.7rem;
                padding: 0.2rem 0.5rem;
            }
            .sidebar-logo {
                font-size: 1.4rem;
                padding: 0.4rem 1rem;
            }
        }
        
        @media (max-width: 480px) {
            .row.g-4 {
                --bs-gutter-x: 0.5rem;
                --bs-gutter-y: 0.5rem;
            }
            .metric-card {
                padding: 0.5rem;
            }
            .metric-value {
                font-size: 1rem;
            }
            .card-icon {
                font-size: 1.25rem;
            }
            .quick-actions .btn {
                width: 100%;
                margin: 0.125rem 0;
            }
            .activity-item {
                font-size: 0.8rem;
            }
            .system-health {
                font-size: 0.875rem;
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
            <div class="text-center">
                <span class="sidebar-logo">NEXTGEN</span>
            </div>
            <nav class="nav flex-column mb-auto">
                <a href="admin.php" class="nav-link active">
                    <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                </a>
                <a href="admin_analytics.php" class="nav-link">
                    <i class="fas fa-chart-line me-2"></i>Analytics
                </a>
                <a href="admin_orders.php" class="nav-link">
                    <i class="fas fa-box me-2"></i>Order Management
                </a>
                <a href="admin_users.php" class="nav-link">
                    <i class="fas fa-users me-2"></i>User Management
                </a>
                <a href="admin_products.php" class="nav-link">
                    <i class="fas fa-mobile-alt me-2"></i>Product Management
                </a>
                <a href="admin_reviews.php" class="nav-link">
                    <i class="fas fa-star me-2"></i>Customer Reviews
                </a>
                <a href="admin_audit_logs.php" class="nav-link">
                    <i class="fas fa-clipboard-list me-2"></i>Audit Logs
                </a>
                <a href="admin_user_activity.php" class="nav-link">
                    <i class="fas fa-user-clock me-2"></i>User Activity
                </a>
            </nav>
            <div class="mt-auto text-center">
                <div class="mb-3">
                    <small class="text-light">Logged in as:</small><br>
                    <strong><?php echo htmlspecialchars($_SESSION['user_name']); ?></strong>
                </div>
                <a href="logout.php" class="btn btn-light w-100">
                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content flex-grow-1">
            <div class="dashboard-header d-flex justify-content-between align-items-center p-4">
                <div>
                    <h1 class="h3 mb-0">Admin Dashboard</h1>
                    <p class="text-muted mb-0">Welcome back, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</p>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <span class="text-muted"><?php echo date('l, F j, Y'); ?></span>
                    <a href="index.php" class="btn btn-outline-primary btn-sm" target="_blank">
                        <i class="fas fa-external-link-alt me-1"></i>View Site
                    </a>
                </div>
            </div>

            <div class="container-fluid p-4">
                <!-- Key Metrics -->
                <div class="row g-4 mb-4">
                    <div class="col-md-3">
                        <div class="card metric-card">
                            <div class="card-icon mb-2"><i class="fas fa-box"></i></div>
                            <div class="metric-value"><?php echo number_format($orderCount); ?></div>
                            <div class="metric-label">Total Orders</div>
                            <?php if ($pendingOrders > 0): ?>
                                <small class="text-warning"><?php echo $pendingOrders; ?> pending</small>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card metric-card">
                            <div class="card-icon mb-2"><i class="fas fa-users"></i></div>
                            <div class="metric-value"><?php echo number_format($userCount); ?></div>
                            <div class="metric-label">Registered Users</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card metric-card">
                            <div class="card-icon mb-2"><i class="fas fa-mobile-alt"></i></div>
                            <div class="metric-value"><?php echo number_format($productCount); ?></div>
                            <div class="metric-label">Products</div>
                            <?php if ($lowStockProducts > 0): ?>
                                <small class="text-danger"><?php echo $lowStockProducts; ?> low stock</small>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card metric-card">
                            <div class="card-icon mb-2"><i class="fas fa-money-bill-wave"></i></div>
                            <div class="metric-value">LKR <?php echo number_format($totalRevenue, 0); ?></div>
                            <div class="metric-label">Total Revenue</div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions & System Health -->
                <div class="row g-4 mb-4">
                    <div class="col-md-8">
                        <div class="card p-4">
                            <h4 class="mb-3"><i class="fas fa-bolt me-2"></i>Quick Actions</h4>
                            <div class="quick-actions d-flex flex-wrap">
                                <a href="admin_orders.php" class="btn btn-primary">
                                    <i class="fas fa-box me-2"></i>Manage Orders
                                </a>
                                <a href="admin_products.php" class="btn btn-success">
                                    <i class="fas fa-plus me-2"></i>Add Product
                                </a>
                                <a href="admin_users.php" class="btn btn-info">
                                    <i class="fas fa-user-plus me-2"></i>Manage Users
                                </a>
                                <a href="admin_analytics.php" class="btn btn-warning">
                                    <i class="fas fa-chart-bar me-2"></i>View Analytics
                                </a>
                                <a href="admin_orders.php?status=pending" class="btn btn-danger">
                                    <i class="fas fa-clock me-2"></i>Pending Orders
                                </a>
                                <a href="admin_products.php?stock=low" class="btn btn-secondary">
                                    <i class="fas fa-exclamation-triangle me-2"></i>Low Stock
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card system-health p-4">
                            <h4 class="mb-3"><i class="fas fa-heartbeat me-2"></i>System Health</h4>
                            <div class="d-flex flex-column gap-2">
                                <div class="d-flex justify-content-between">
                                    <span>Database:</span>
                                    <span class="badge bg-<?php echo $systemHealth['database_connected'] ? 'success' : 'danger'; ?>">
                                        <?php echo $systemHealth['database_connected'] ? 'Connected' : 'Error'; ?>
                                    </span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>Uploads:</span>
                                    <span class="badge bg-<?php echo $systemHealth['uploads_writable'] ? 'success' : 'warning'; ?>">
                                        <?php echo $systemHealth['uploads_writable'] ? 'Writable' : 'Read-only'; ?>
                                    </span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>Session:</span>
                                    <span class="badge bg-<?php echo $systemHealth['session_active'] ? 'success' : 'danger'; ?>">
                                        <?php echo $systemHealth['session_active'] ? 'Active' : 'Inactive'; ?>
                                    </span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>PHP Version:</span>
                                    <span class="badge bg-info"><?php echo $systemHealth['php_version']; ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="card p-4">
                            <h4 class="mb-3"><i class="fas fa-clock me-2"></i>Recent Orders</h4>
                            <div class="recent-activity">
                                <?php if (!empty($recentOrders)): ?>
                                    <?php foreach ($recentOrders as $order): ?>
                                        <div class="activity-item">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <strong>Order #<?php echo $order['id']; ?></strong>
                                                    <br>
                                                    <small class="text-muted">
                                                        <?php echo htmlspecialchars($order['customer_name'] ?? 'Guest'); ?> - 
                                                        LKR <?php echo number_format($order['total_amount'], 0); ?>
                                                    </small>
                                                </div>
                                                <span class="status-badge status-<?php echo $order['status']; ?>">
                                                    <?php echo ucfirst($order['status']); ?>
                                                </span>
                                            </div>
                                            <small class="text-muted">
                                                <?php echo date('M j, Y g:i A', strtotime($order['created_at'])); ?>
                                            </small>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p class="text-muted">No recent orders</p>
                                <?php endif; ?>
                            </div>
                            <div class="text-center mt-3">
                                <a href="admin_orders.php" class="btn btn-outline-primary btn-sm">View All Orders</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card p-4">
                            <h4 class="mb-3"><i class="fas fa-user-plus me-2"></i>Recent Users</h4>
                            <div class="recent-activity">
                                <?php if (!empty($recentUsers)): ?>
                                    <?php foreach ($recentUsers as $user): ?>
                                        <div class="activity-item">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <strong><?php echo htmlspecialchars($user['name']); ?></strong>
                                                    <br>
                                                    <small class="text-muted"><?php echo htmlspecialchars($user['email']); ?></small>
                                                </div>
                                                <span class="badge bg-success">New</span>
                                            </div>
                                            <small class="text-muted">
                                                Joined <?php echo date('M j, Y', strtotime($user['created_at'])); ?>
                                            </small>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p class="text-muted">No recent users</p>
                                <?php endif; ?>
                            </div>
                            <div class="text-center mt-3">
                                <a href="admin_users.php" class="btn btn-outline-primary btn-sm">View All Users</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Essential JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    
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
            
            // Initialize DataTables with responsive options
            $('.datatable').DataTable({
                responsive: {
                    details: {
                        display: $.fn.dataTable.Responsive.display.modal({
                            header: function(row) {
                                var data = row.data();
                                return 'Details for ' + data[0];
                            }
                        }),
                        renderer: $.fn.dataTable.Responsive.renderer.tableAll()
                    }
                },
                language: {
                    search: "Search:",
                    lengthMenu: "Show _MENU_ entries per page",
                    info: "Showing _START_ to _END_ of _TOTAL_ entries",
                    paginate: {
                        first: "First",
                        last: "Last",
                        next: "Next",
                        previous: "Previous"
                    }
                }
            });
            
            // Responsive table handling for mobile
            $('.table-responsive').on('show.bs.dropdown', function () {
                $('.table-responsive').css("overflow", "inherit");
            });
            
            $('.table-responsive').on('hide.bs.dropdown', function () {
                $('.table-responsive').css("overflow", "auto");
            });
        });

        // Auto-refresh dashboard every 5 minutes
        setTimeout(function() {
            location.reload();
        }, 300000);

        // Add loading states to buttons
        $('.btn').on('click', function() {
            $(this).prop('disabled', true);
            $(this).html('<i class="fas fa-spinner fa-spin me-2"></i>Loading...');
        });
        
        // Touch gesture support for mobile
        let touchStartX = 0;
        let touchEndX = 0;
        
        document.addEventListener('touchstart', e => {
            touchStartX = e.changedTouches[0].screenX;
        });
        
        document.addEventListener('touchend', e => {
            touchEndX = e.changedTouches[0].screenX;
            handleSwipe();
        });
        
        function handleSwipe() {
            const swipeThreshold = 50;
            const sidebar = document.getElementById('sidebar');
            const sidebarOverlay = document.getElementById('sidebarOverlay');
            
            if (window.innerWidth <= 768) {
                if (touchEndX < touchStartX - swipeThreshold) {
                    // Swipe left - close menu
                    sidebar.classList.remove('show');
                    sidebarOverlay.classList.remove('show');
                    document.body.classList.remove('menu-open');
                } else if (touchEndX > touchStartX + swipeThreshold) {
                    // Swipe right - open menu
                    sidebar.classList.add('show');
                    sidebarOverlay.classList.add('show');
                    document.body.classList.add('menu-open');
                }
            }
        }
        
        // Prevent body scroll when mobile menu is open
        $('body').on('menu-open', function() {
            $(this).css('overflow', 'hidden');
        }).on('menu-close', function() {
            $(this).css('overflow', 'auto');
        });
    </script>
</body>
</html> 