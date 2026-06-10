<?php
require_once '../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header('Location: ../login.php');
    exit();
}

// Fetch all users with order count and feedback count (handle if feedback table doesn't exist)
$conn = getDBConnection();
try {
    $sql = "SELECT u.*, 
            COUNT(DISTINCT o.id) as order_count,
            COUNT(DISTINCT f.id) as feedback_count,
            AVG(f.rating) as avg_rating
            FROM users u 
            LEFT JOIN orders o ON u.id = o.user_id 
            LEFT JOIN feedback f ON u.id = f.user_id
            GROUP BY u.id 
            ORDER BY u.created_at DESC";
    $result = $conn->query($sql);
} catch (Exception $e) {
    // Feedback table doesn't exist - use simpler query
    $sql = "SELECT u.*, COUNT(o.id) as order_count, 0 as feedback_count, 0 as avg_rating
            FROM users u 
            LEFT JOIN orders o ON u.id = o.user_id 
            GROUP BY u.id 
            ORDER BY u.created_at DESC";
    $result = $conn->query($sql);
}

$users = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Admin</title>
    
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
                        <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link" href="products.php">Products</a></li>
                        <li class="nav-item"><a class="nav-link" href="orders.php">Orders</a></li>
                        <li class="nav-item"><a class="nav-link" href="appointments.php">Appointments</a></li>
                        <li class="nav-item"><a class="nav-link" href="sales-report.php">Sales Report</a></li>
                        <li class="nav-item"><a class="nav-link" href="feedback.php">Feedback</a></li>
                        <li class="nav-item"><a class="nav-link active" href="users.php">Users</a></li>
                        <li class="nav-item"><a class="nav-link" href="../logout.php">Logout</a></li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <section class="container my-5">
        <h2 class="mb-4">Manage Users</h2>

        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <h3 class="text-brown"><?= count(array_filter($users, function($u) { return $u['role'] == 'user'; })) ?></h3>
                        <p class="text-muted mb-0">Total Customers</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <h3 class="text-brown"><?= count(array_filter($users, function($u) { return $u['role'] == 'admin'; })) ?></h3>
                        <p class="text-muted mb-0">Total Admins</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <h3 class="text-brown"><?= array_sum(array_column($users, 'order_count')) ?></h3>
                        <p class="text-muted mb-0">Total Orders</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Orders</th>
                                <th>Feedback</th>
                                <th>Joined Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($users as $user): ?>
                            <tr>
                                <td><?= $user['id'] ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-brown text-white rounded-circle d-inline-flex align-items-center justify-content-center me-2" 
                                             style="width: 35px; height: 35px; font-size: 0.9rem;">
                                            <?= strtoupper(substr($user['name'], 0, 1)) ?>
                                        </div>
                                        <?= htmlspecialchars($user['name']) ?>
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                                <td>
                                    <span class="badge bg-<?= $user['role'] == 'admin' ? 'danger' : 'primary' ?>">
                                        <?= ucfirst($user['role']) ?>
                                    </span>
                                </td>
                                <td><?= $user['order_count'] ?> orders</td>
                                <td>
                                    <?php if ($user['feedback_count'] > 0): ?>
                                        <div>
                                            <span class="badge bg-success"><?= $user['feedback_count'] ?> reviews</span>
                                            <?php if ($user['avg_rating']): ?>
                                                <small class="text-muted d-block">⭐ <?= number_format($user['avg_rating'], 1) ?> avg</small>
                                            <?php endif; ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted">No feedback</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= date('M d, Y', strtotime($user['created_at'])) ?></td>
                                <td>
                                    <?php if ($user['feedback_count'] > 0): ?>
                                        <a href="feedback.php?user_id=<?= $user['id'] ?>" class="btn btn-sm btn-outline-primary">
                                            View Feedback
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
