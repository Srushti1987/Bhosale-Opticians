<?php
require_once '../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header('Location: ../login.php');
    exit();
}

$success = '';

// Update order status
if (isset($_POST['update_status'])) {
    $order_id = intval($_POST['order_id']);
    $status = $_POST['status'];
    
    $conn = getDBConnection();
    $status = $conn->real_escape_string($status);
    $sql = "UPDATE orders SET status = '$status' WHERE id = $order_id";
    
    if ($conn->query($sql)) {
        $success = 'Order status updated successfully!';
    }
    $conn->close();
}

// Fetch all orders
$conn = getDBConnection();
$sql = "SELECT o.*, u.name as user_name, u.email as user_email 
        FROM orders o 
        JOIN users u ON o.user_id = u.id 
        ORDER BY o.created_at DESC";
$result = $conn->query($sql);
$orders = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        // Fetch order items for each order
        $order_id = $row['id'];
        $items_sql = "SELECT oi.*, p.name as product_name, p.image_url 
                      FROM order_items oi 
                      JOIN products p ON oi.product_id = p.id 
                      WHERE oi.order_id = $order_id";
        $items_result = $conn->query($items_sql);
        $row['items'] = [];
        if ($items_result->num_rows > 0) {
            while($item = $items_result->fetch_assoc()) {
                $row['items'][] = $item;
            }
        }
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
    <title>Manage Orders - Admin</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
</head>
<body>

    <header class="sticky-top bg-white shadow-sm">
        <nav class="navbar navbar-expand-lg container">
            <div class="container-fluid px-3">
                <a class="navbar-brand fw-bold fs-4" href="../index_updated.php">
                    Bhosale Opticians <span class="badge bg-danger">Admin</span>
                </a>
                
                <div class="collapse navbar-collapse">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link" href="products.php">Products</a></li>
                        <li class="nav-item"><a class="nav-link active" href="orders.php">Orders</a></li>
                        <li class="nav-item"><a class="nav-link" href="appointments.php">Appointments</a></li>
                        <li class="nav-item"><a class="nav-link" href="users.php">Users</a></li>
                        <li class="nav-item"><a class="nav-link" href="../logout.php">Logout</a></li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <section class="container my-5">
        <h2 class="mb-4">Manage Orders</h2>

        <?php if($success): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?= $success ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <div class="card shadow-sm">
            <div class="card-body">
                <?php if(count($orders) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Email</th>
                                <th>Total Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Bill</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($orders as $order): ?>
                            <tr>
                                <td><strong>#<?= $order['id'] ?></strong></td>
                                <td><?= htmlspecialchars($order['user_name']) ?></td>
                                <td><?= htmlspecialchars($order['user_email']) ?></td>
                                <td class="text-brown fw-bold">₹<?= number_format($order['total_amount'], 2) ?></td>
                                <td>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                        <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                            <option value="pending" <?= $order['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                                            <option value="processing" <?= $order['status'] == 'processing' ? 'selected' : '' ?>>Processing</option>
                                            <option value="shipped" <?= $order['status'] == 'shipped' ? 'selected' : '' ?>>Shipped</option>
                                            <option value="completed" <?= $order['status'] == 'completed' ? 'selected' : '' ?>>Completed</option>
                                            <option value="cancelled" <?= $order['status'] == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                        </select>
                                        <input type="hidden" name="update_status" value="1">
                                    </form>
                                </td>
                                <td><?= date('M d, Y H:i', strtotime($order['created_at'])) ?></td>
                                <td>
                                    <a href="../bill.php?order_id=<?= $order['id'] ?>" target="_blank" class="btn btn-sm btn-success">
                                        <i class="bi bi-file-earmark-text"></i> View Bill
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Order Modals (Outside Table) -->
                <?php foreach($orders as $order): ?>
                            <div class="modal fade" id="orderModal<?= $order['id'] ?>" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Order #<?= $order['id'] ?> Details</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <h6 class="text-primary">Customer Information</h6>
                                                    <p class="mb-1"><strong>Name:</strong> <?= htmlspecialchars($order['user_name']) ?></p>
                                                    <p class="mb-0"><strong>Email:</strong> <?= htmlspecialchars($order['user_email']) ?></p>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <h6 class="text-primary">Order Information</h6>
                                                    <p class="mb-1"><strong>Status:</strong> <span class="badge bg-primary"><?= ucfirst($order['status']) ?></span></p>
                                                    <p class="mb-0"><strong>Date:</strong> <?= date('M d, Y H:i', strtotime($order['created_at'])) ?></p>
                                                </div>
                                            </div>
                                            
                                            <hr>
                                            
                                            <h6 class="text-primary mb-3">Shipping Address</h6>
                                            <p class="mb-3"><?= nl2br(htmlspecialchars($order['shipping_address'] ?? 'Not provided')) ?></p>
                                            
                                            <hr>
                                            
                                            <h6 class="text-primary mb-3">Order Items</h6>
                                            <?php if(!empty($order['items'])): ?>
                                            <div class="table-responsive">
                                                <table class="table table-sm">
                                                    <thead>
                                                        <tr>
                                                            <th>Product</th>
                                                            <th>Image</th>
                                                            <th>Price</th>
                                                            <th>Quantity</th>
                                                            <th>Subtotal</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach($order['items'] as $item): ?>
                                                        <tr>
                                                            <td><?= htmlspecialchars($item['product_name']) ?></td>
                                                            <td>
                                                                <img src="<?= htmlspecialchars($item['image_url']) ?>" 
                                                                     alt="<?= htmlspecialchars($item['product_name']) ?>" 
                                                                     style="width: 50px; height: 50px; object-fit: cover;">
                                                            </td>
                                                            <td>₹<?= number_format($item['price'], 2) ?></td>
                                                            <td><?= $item['quantity'] ?></td>
                                                            <td class="fw-bold">₹<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
                                                        </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                    <tfoot>
                                                        <tr>
                                                            <td colspan="4" class="text-end"><strong>Total:</strong></td>
                                                            <td class="fw-bold text-brown">₹<?= number_format($order['total_amount'], 2) ?></td>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                            <?php else: ?>
                                            <p class="text-muted">No items found for this order.</p>
                                            <?php endif; ?>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            <a href="../bill.php?order_id=<?= $order['id'] ?>" target="_blank" class="btn btn-primary">Print Invoice</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <p class="text-center text-muted py-5">No orders found.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
