<?php
require_once '../config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'user') {
    header('Location: ../login.php');
    exit();
}

// Fetch user orders
$conn = getDBConnection();
$user_id = $_SESSION['user_id'];
$sql = "SELECT o.*, COUNT(oi.id) as item_count 
        FROM orders o 
        LEFT JOIN order_items oi ON o.id = oi.order_id 
        WHERE o.user_id = $user_id 
        GROUP BY o.id 
        ORDER BY o.created_at DESC";
$result = $conn->query($sql);
$orders = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - Bhosale Opticians</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
</head>
<body>

    <!-- Header -->
    <header class="sticky-top bg-white shadow-sm">
        <nav class="navbar navbar-expand-lg container">
            <div class="container-fluid px-3">
                <a class="navbar-brand fw-bold fs-4" href="../index_updated.php">
                    Bhosale Opticians
                </a>
                
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item"><a class="nav-link" href="../index_updated.php">Home</a></li>
                        <li class="nav-item"><a class="nav-link" href="../products.php">Products</a></li>
                        <li class="nav-item"><a class="nav-link active" href="dashboard.php">Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link" href="../logout.php">Logout</a></li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- Dashboard -->
    <section class="container my-5">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 mb-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <div class="bg-brown text-white rounded-circle d-inline-flex align-items-center justify-content-center" 
                                 style="width: 80px; height: 80px; font-size: 2rem;">
                                <?= strtoupper(substr($_SESSION['user_name'], 0, 1)) ?>
                            </div>
                            <h5 class="mt-3 mb-0"><?= htmlspecialchars($_SESSION['user_name']) ?></h5>
                            <p class="text-muted small"><?= htmlspecialchars($_SESSION['user_email']) ?></p>
                        </div>
                        
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item active">
                                <a href="dashboard.php" class="text-decoration-none text-white">📊 Dashboard</a>
                            </li>
                            <li class="list-group-item">
                                <a href="orders.php" class="text-decoration-none text-dark">📦 My Orders</a>
                            </li>
                            <li class="list-group-item">
                                <a href="profile.php" class="text-decoration-none text-dark">👤 Profile</a>
                            </li>
                            <li class="list-group-item">
                                <a href="../logout.php" class="text-decoration-none text-danger">🚪 Logout</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9">
                <h2 class="mb-4">Welcome, <?= htmlspecialchars($_SESSION['user_name']) ?>!</h2>
                
                <!-- Stats Cards -->
                <div class="row g-4 mb-4">
                    <div class="col-md-4">
                        <div class="card shadow-sm">
                            <div class="card-body text-center">
                                <h3 class="text-brown"><?= count($orders) ?></h3>
                                <p class="text-muted mb-0">Total Orders</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card shadow-sm">
                            <div class="card-body text-center">
                                <h3 class="text-brown">
                                    <?= count(array_filter($orders, function($o) { return $o['status'] == 'pending'; })) ?>
                                </h3>
                                <p class="text-muted mb-0">Pending Orders</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card shadow-sm">
                            <div class="card-body text-center">
                                <h3 class="text-brown">
                                    <?= count(array_filter($orders, function($o) { return $o['status'] == 'completed'; })) ?>
                                </h3>
                                <p class="text-muted mb-0">Completed Orders</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Orders -->
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h4 class="mb-0">Recent Orders</h4>
                    </div>
                    <div class="card-body">
                        <?php if(count($orders) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Items</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach(array_slice($orders, 0, 5) as $order): ?>
                                    <tr>
                                        <td>#<?= $order['id'] ?></td>
                                        <td><?= $order['item_count'] ?> items</td>
                                        <td class="text-brown fw-bold">$<?= number_format($order['total_amount'], 2) ?></td>
                                        <td>
                                            <span class="badge bg-<?= $order['status'] == 'completed' ? 'success' : 'warning' ?>">
                                                <?= ucfirst($order['status']) ?>
                                            </span>
                                        </td>
                                        <td><?= date('M d, Y', strtotime($order['created_at'])) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php else: ?>
                        <p class="text-center text-muted py-4">No orders yet. <a href="../products.php">Start shopping!</a></p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="row g-4 mt-4">
                    <div class="col-md-6">
                        <div class="card shadow-sm h-100">
                            <div class="card-body text-center">
                                <h5>🛍️ Continue Shopping</h5>
                                <p class="text-muted">Browse our latest collection</p>
                                <a href="../products.php" class="btn btn-primary">Shop Now</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card shadow-sm h-100">
                            <div class="card-body text-center">
                                <h5>👤 Update Profile</h5>
                                <p class="text-muted">Manage your account settings</p>
                                <a href="profile.php" class="btn btn-outline-primary">Go to Profile</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
