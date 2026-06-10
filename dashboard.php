<?php
require_once '../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header('Location: ../login.php');
    exit();
}

$conn = getDBConnection();

// Get statistics
$total_products = $conn->query("SELECT COUNT(*) as count FROM products")->fetch_assoc()['count'];
$total_orders = $conn->query("SELECT COUNT(*) as count FROM orders")->fetch_assoc()['count'];
$total_users = $conn->query("SELECT COUNT(*) as count FROM users WHERE role='user'")->fetch_assoc()['count'];
$total_revenue = $conn->query("SELECT SUM(total_amount) as total FROM orders WHERE status='completed'")->fetch_assoc()['total'] ?? 0;

// Get feedback statistics (handle if table doesn't exist)
$total_feedback = 0;
$avg_rating = 0;
try {
    $feedback_stats = $conn->query("SELECT COUNT(*) as total_feedback, AVG(rating) as avg_rating FROM feedback");
    if ($feedback_stats) {
        $stats = $feedback_stats->fetch_assoc();
        $total_feedback = $stats['total_feedback'] ?? 0;
        $avg_rating = $stats['avg_rating'] ?? 0;
    }
} catch (Exception $e) {
    // Feedback table doesn't exist yet - ignore error
    $total_feedback = 0;
    $avg_rating = 0;
}

// Appointment stats
$today_appointments = 0;
$pending_appointments = 0;
try {
    $today_appointments  = $conn->query("SELECT COUNT(*) as cnt FROM appointments WHERE appointment_date = CURDATE() AND status != 'cancelled'")->fetch_assoc()['cnt'] ?? 0;
    $pending_appointments = $conn->query("SELECT COUNT(*) as cnt FROM appointments WHERE status = 'pending'")->fetch_assoc()['cnt'] ?? 0;
} catch (Exception $e) {
    $today_appointments = 0;
    $pending_appointments = 0;
}

// Recent orders
$recent_orders = [];
$sql = "SELECT o.*, u.name as user_name FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC LIMIT 5";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $recent_orders[] = $row;
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Bhosale Opticians</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
</head>
<body>

    <header class="sticky-top bg-white shadow-sm">
        <nav class="navbar navbar-expand-lg container">
            <div class="container-fluid px-3">
                <a class="navbar-brand fw-bold fs-4" href="../index.php">
                    Bhosale Opticians <span class="badge bg-danger">Admin</span>
                </a>
                
                <div class="collapse navbar-collapse">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item"><a class="nav-link active" href="dashboard.php">Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link" href="products.php">Products</a></li>
                        <li class="nav-item"><a class="nav-link" href="orders.php">Orders</a></li>
                        <li class="nav-item"><a class="nav-link" href="appointments.php">Appointments</a></li>
                        <li class="nav-item"><a class="nav-link" href="sales-report.php">Sales Report</a></li>
                        <li class="nav-item"><a class="nav-link" href="feedback.php">Feedback</a></li>
                        <li class="nav-item"><a class="nav-link" href="users.php">Users</a></li>
                        <li class="nav-item"><a class="nav-link" href="../logout.php">Logout</a></li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <section class="container my-5">
        <h2 class="mb-4">Admin Dashboard</h2>
        <p class="text-muted">Welcome back, <?= htmlspecialchars($_SESSION['user_name']) ?>!</p>

        <!-- Stats Cards -->
        <div class="row g-4 mb-5">
            <div class="col-md-3 col-lg-2">
                <div class="card shadow-sm border-0" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <div class="card-body text-white text-center">
                        <h2 class="mb-0"><?= $total_products ?></h2>
                        <p class="mb-0">Products</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-lg-2">
                <div class="card shadow-sm border-0" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                    <div class="card-body text-white text-center">
                        <h2 class="mb-0"><?= $total_orders ?></h2>
                        <p class="mb-0">Orders</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-lg-2">
                <div class="card shadow-sm border-0" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                    <div class="card-body text-white text-center">
                        <h2 class="mb-0"><?= $total_users ?></h2>
                        <p class="mb-0">Users</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-lg-3">
                <div class="card shadow-sm border-0" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                    <div class="card-body text-white text-center">
                        <h2 class="mb-0">₹<?= number_format($total_revenue, 0) ?></h2>
                        <p class="mb-0">Revenue</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-lg-3">
                <div class="card shadow-sm border-0" style="background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);">
                    <div class="card-body text-center">
                        <h2 class="mb-0 text-dark"><?= $total_feedback ?></h2>
                        <p class="mb-0 text-dark">Reviews</p>
                        <?php if($avg_rating > 0): ?>
                        <small class="text-dark">⭐ <?= number_format($avg_rating, 1) ?></small>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Recent Orders</h4>
                <a href="orders.php" class="btn btn-sm btn-primary">View All</a>
            </div>
            <div class="card-body">
                <?php if(count($recent_orders) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Bill</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($recent_orders as $order): ?>
                            <tr>
                                <td>#<?= $order['id'] ?></td>
                                <td><?= htmlspecialchars($order['user_name']) ?></td>
                                <td class="text-brown fw-bold">₹<?= number_format($order['total_amount'], 2) ?></td>
                                <td>
                                    <span class="badge bg-<?= $order['status'] == 'completed' ? 'success' : 'warning' ?>">
                                        <?= ucfirst($order['status']) ?>
                                    </span>
                                </td>
                                <td><?= date('M d, Y', strtotime($order['created_at'])) ?></td>
                                <td>
                                    <a href="bill.php?order_id=<?= $order['id'] ?>" target="_blank" class="btn btn-sm btn-success">View Bill</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <p class="text-center text-muted py-4">No orders yet.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row g-4 mt-4">
            <div class="col-md-3">
                <div class="card shadow-sm h-100">
                    <div class="card-body text-center">
                        <h5>📦 Manage Products</h5>
                        <p class="text-muted">Add, edit, or delete products</p>
                        <a href="products.php" class="btn btn-primary">Go to Products</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm h-100">
                    <div class="card-body text-center">
                        <h5>📋 Manage Orders</h5>
                        <p class="text-muted">View and update order status</p>
                        <a href="orders.php" class="btn btn-primary">Go to Orders</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm h-100">
                    <div class="card-body text-center">
                        <h5>💬 Customer Feedback</h5>
                        <p class="text-muted">View reviews and ratings</p>
                        <a href="feedback.php" class="btn btn-primary">View Feedback</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm h-100">
                    <div class="card-body text-center">
                        <h5>👥 Manage Users</h5>
                        <p class="text-muted">View and manage user accounts</p>
                        <a href="users.php" class="btn btn-primary">Go to Users</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
