<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header('Location: ../login.php');
    exit();
}

$page_title = "Sales Report - Admin";

// Set default date range (last 30 days)
$end_date = date('Y-m-d');
$start_date = date('Y-m-d', strtotime('-30 days'));

// Get date range from form if submitted
if (isset($_GET['start_date']) && isset($_GET['end_date'])) {
    $start_date = $_GET['start_date'];
    $end_date = $_GET['end_date'];
}

$conn = getDBConnection();

// Get total orders in date range
$sql = "SELECT COUNT(*) as total_orders, SUM(total_amount) as total_revenue 
        FROM orders 
        WHERE DATE(created_at) BETWEEN '$start_date' AND '$end_date'";
$result = $conn->query($sql);
$stats = $result->fetch_assoc();

// Get orders by date
$sql = "SELECT DATE(created_at) as order_date, COUNT(*) as orders, SUM(total_amount) as revenue 
        FROM orders 
        WHERE DATE(created_at) BETWEEN '$start_date' AND '$end_date'
        GROUP BY DATE(created_at)
        ORDER BY order_date DESC";
$daily_stats = $conn->query($sql);

// Get top selling products in date range
$sql = "SELECT p.name, p.price, SUM(oi.quantity) as total_sold, SUM(oi.quantity * oi.price) as revenue
        FROM order_items oi
        JOIN orders o ON oi.order_id = o.id
        JOIN products p ON oi.product_id = p.id
        WHERE DATE(o.created_at) BETWEEN '$start_date' AND '$end_date'
        GROUP BY oi.product_id
        ORDER BY total_sold DESC
        LIMIT 10";
$top_products = $conn->query($sql);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard.php">Admin Panel</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="products.php">Products</a></li>
                    <li class="nav-item"><a class="nav-link" href="orders.php">Orders</a></li>
                    <li class="nav-item"><a class="nav-link" href="appointments.php">Appointments</a></li>
                    <li class="nav-item"><a class="nav-link active" href="sales-report.php">Sales Report</a></li>
                    <li class="nav-item"><a class="nav-link" href="feedback.php">Feedback</a></li>
                    <li class="nav-item"><a class="nav-link" href="users.php">Users</a></li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="../index_updated.php">View Site</a></li>
                    <li class="nav-item"><a class="nav-link" href="../logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <h1 class="mb-4"><i class="bi bi-graph-up"></i> Sales Report</h1>

        <!-- Date Range Filter -->
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Select Date Range</h5>
                <form method="GET" class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Start Date</label>
                        <input type="date" name="start_date" class="form-control" value="<?= $start_date ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">End Date</label>
                        <input type="date" name="end_date" class="form-control" value="<?= $end_date ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary w-100">Generate Report</button>
                    </div>
                </form>
                <div class="mt-3">
                    <small class="text-muted">
                        Quick filters: 
                        <a href="?start_date=<?= date('Y-m-d') ?>&end_date=<?= date('Y-m-d') ?>">Today</a> | 
                        <a href="?start_date=<?= date('Y-m-d', strtotime('-7 days')) ?>&end_date=<?= date('Y-m-d') ?>">Last 7 Days</a> | 
                        <a href="?start_date=<?= date('Y-m-d', strtotime('-30 days')) ?>&end_date=<?= date('Y-m-d') ?>">Last 30 Days</a> | 
                        <a href="?start_date=<?= date('Y-m-01') ?>&end_date=<?= date('Y-m-t') ?>">This Month</a>
                    </small>
                </div>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card text-white bg-primary">
                    <div class="card-body">
                        <h5 class="card-title"><i class="bi bi-cart-check"></i> Total Orders</h5>
                        <h2 class="mb-0"><?= $stats['total_orders'] ?? 0 ?></h2>
                        <small>From <?= date('M d, Y', strtotime($start_date)) ?> to <?= date('M d, Y', strtotime($end_date)) ?></small>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card text-white bg-success">
                    <div class="card-body">
                        <h5 class="card-title"><i class="bi bi-currency-rupee"></i> Total Revenue</h5>
                        <h2 class="mb-0">₹<?= number_format($stats['total_revenue'] ?? 0, 2) ?></h2>
                        <small>From <?= date('M d, Y', strtotime($start_date)) ?> to <?= date('M d, Y', strtotime($end_date)) ?></small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Daily Sales -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-calendar3"></i> Daily Sales Breakdown</h5>
            </div>
            <div class="card-body">
                <?php if ($daily_stats->num_rows > 0): ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Orders</th>
                                <th>Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $daily_stats->fetch_assoc()): ?>
                            <tr>
                                <td><?= date('M d, Y (D)', strtotime($row['order_date'])) ?></td>
                                <td><?= $row['orders'] ?></td>
                                <td>₹<?= number_format($row['revenue'], 2) ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <p class="text-muted">No orders found in this date range.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Top Selling Products -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-trophy"></i> Top Selling Products</h5>
            </div>
            <div class="card-body">
                <?php if ($top_products->num_rows > 0): ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Product Name</th>
                                <th>Units Sold</th>
                                <th>Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $rank = 1;
                            while ($row = $top_products->fetch_assoc()): 
                            ?>
                            <tr>
                                <td><?= $rank++ ?></td>
                                <td><?= htmlspecialchars($row['name']) ?></td>
                                <td><?= $row['total_sold'] ?> units</td>
                                <td>₹<?= number_format($row['revenue'], 2) ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <p class="text-muted">No products sold in this date range.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Print Button -->
        <div class="mt-4 text-center">
            <button onclick="window.print()" class="btn btn-secondary">
                <i class="bi bi-printer"></i> Print Report
            </button>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <style>
        @media print {
            .navbar, button, .btn { display: none !important; }
        }
    </style>
</body>
</html>
