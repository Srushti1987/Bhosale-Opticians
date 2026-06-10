<?php
require_once '../config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'user') {
    header('Location: ../login.php');
    exit();
}

// Fetch user orders with items
$conn = getDBConnection();
$user_id = $_SESSION['user_id'];

$sql = "SELECT o.*, 
        (SELECT GROUP_CONCAT(CONCAT(p.name, ' (x', oi.quantity, ')') SEPARATOR ', ') 
         FROM order_items oi 
         LEFT JOIN products p ON oi.product_id = p.id 
         WHERE oi.order_id = o.id) as items
        FROM orders o 
        WHERE o.user_id = $user_id 
        ORDER BY o.created_at DESC";

$result = $conn->query($sql);
$orders = [];
if ($result && $result->num_rows > 0) {
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
    <title>My Orders - Bhosale Opticians</title>
    
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
                        <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link" href="../logout.php">Logout</a></li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- Orders Section -->
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
                            <li class="list-group-item">
                                <a href="dashboard.php" class="text-decoration-none text-dark">📊 Dashboard</a>
                            </li>
                            <li class="list-group-item active">
                                <a href="orders.php" class="text-decoration-none text-white">📦 My Orders</a>
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
                <h2 class="mb-4">My Orders</h2>
                
                <?php if(count($orders) > 0): ?>
                    <?php foreach($orders as $order): ?>
                    <div class="card shadow-sm mb-3">
                        <div class="card-header bg-white">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <h5 class="mb-0">Order #<?= $order['id'] ?></h5>
                                    <small class="text-muted">Placed on <?= date('F d, Y', strtotime($order['created_at'])) ?></small>
                                </div>
                                <div class="col-md-6 text-md-end">
                                    <span class="badge bg-<?= $order['status'] == 'completed' ? 'success' : ($order['status'] == 'pending' ? 'warning' : 'info') ?> fs-6">
                                        <?= ucfirst($order['status']) ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Order Status Timeline -->
                            <?php
                            $statuses = ['pending','processing','shipped','completed'];
                            $current = $order['status'];
                            $current_idx = array_search($current, $statuses);
                            if($current == 'cancelled') $current_idx = -1;
                            ?>
                            <?php if($current == 'cancelled'): ?>
                            <div class="alert alert-danger py-2 mb-3">❌ This order has been cancelled.</div>
                            <?php else: ?>
                            <div class="d-flex justify-content-between align-items-center mb-4 px-2">
                                <?php
                                $labels = ['📋 Confirmed','⚙️ Processing','🚚 Shipped','✅ Delivered'];
                                foreach($statuses as $i => $s):
                                    $done = $i <= $current_idx;
                                ?>
                                <div class="text-center flex-fill">
                                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-1"
                                         style="width:36px;height:36px;background:<?= $done ? '#198754' : '#dee2e6' ?>;color:white;font-size:0.8em;">
                                        <?= $done ? '✓' : ($i+1) ?>
                                    </div>
                                    <div class="small <?= $done ? 'text-success fw-bold' : 'text-muted' ?>"><?= $labels[$i] ?></div>
                                </div>
                                <?php if($i < count($statuses)-1): ?>
                                <div class="flex-fill" style="height:3px;background:<?= $i < $current_idx ? '#198754' : '#dee2e6' ?>;margin-bottom:20px;"></div>
                                <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>

                            <div class="row">
                                <div class="col-md-8">
                                    <p class="mb-2"><strong>Items:</strong> <?= htmlspecialchars($order['items'] ?? 'N/A') ?></p>
                                    <p class="mb-2"><strong>Shipping Address:</strong><br><?= nl2br(htmlspecialchars($order['shipping_address'])) ?></p>
                                    <p class="mb-0"><strong>Payment:</strong> Cash on Delivery (COD)</p>
                                </div>
                                <div class="col-md-4 text-md-end">
                                    <h4 class="text-brown mb-3">₹<?= number_format($order['total_amount'], 2) ?></h4>
                                    <a href="../bill.php?order_id=<?= $order['id'] ?>" class="btn btn-outline-primary btn-sm" target="_blank">
                                        📄 View Bill
                                    </a>
                                    <a href="../bill.php?order_id=<?= $order['id'] ?>&download=1" class="btn btn-outline-danger btn-sm" target="_blank" title="Download PDF">
                                        📥 PDF
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="card shadow-sm">
                        <div class="card-body text-center py-5">
                            <div class="mb-4" style="font-size: 4rem;">📦</div>
                            <h4 class="text-muted mb-3">No Orders Yet</h4>
                            <p class="text-muted mb-4">You haven't placed any orders yet. Start shopping to see your orders here!</p>
                            <a href="../products.php" class="btn btn-primary">Browse Products</a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
