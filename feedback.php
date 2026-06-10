<?php
require_once '../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header('Location: ../login.php');
    exit();
}

$conn = getDBConnection();

// Check if feedback table exists
$table_exists = false;
try {
    $conn->query("SELECT 1 FROM feedback LIMIT 1");
    $table_exists = true;
} catch (Exception $e) {
    $table_exists = false;
}

if (!$table_exists) {
    // Redirect to setup page or show error
    echo "<!DOCTYPE html><html><head><title>Setup Required</title></head><body>";
    echo "<div style='text-align: center; margin-top: 100px;'>";
    echo "<h2>Feedback System Setup Required</h2>";
    echo "<p>The feedback table needs to be created first.</p>";
    echo "<p><a href='../setup-feedback-table.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Setup Feedback Table</a></p>";
    echo "</div></body></html>";
    exit();
}

// Handle filtering
$filter_user = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
$filter_rating = isset($_GET['rating']) ? intval($_GET['rating']) : 0;

// Build WHERE clause
$where_conditions = [];
if ($filter_user > 0) {
    $where_conditions[] = "f.user_id = $filter_user";
}
if ($filter_rating > 0) {
    $where_conditions[] = "f.rating = $filter_rating";
}
$where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";

// Get all feedback with user details
$sql = "SELECT f.*, u.name as user_name, u.email as user_email, o.id as order_number 
        FROM feedback f 
        JOIN users u ON f.user_id = u.id 
        LEFT JOIN orders o ON f.order_id = o.id 
        $where_clause
        ORDER BY f.created_at DESC";
$result = $conn->query($sql);
$feedback_list = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $feedback_list[] = $row;
    }
}

// Get all users who have given feedback for filter dropdown
$users_sql = "SELECT DISTINCT u.id, u.name, u.email 
              FROM users u 
              JOIN feedback f ON u.id = f.user_id 
              ORDER BY u.name";
$users_result = $conn->query($users_sql);
$users_list = [];
if ($users_result->num_rows > 0) {
    while($row = $users_result->fetch_assoc()) {
        $users_list[] = $row;
    }
}

// Get feedback statistics
$stats_sql = "SELECT 
    COUNT(*) as total_feedback,
    AVG(rating) as avg_rating,
    COUNT(CASE WHEN rating = 5 THEN 1 END) as five_star,
    COUNT(CASE WHEN rating = 4 THEN 1 END) as four_star,
    COUNT(CASE WHEN rating = 3 THEN 1 END) as three_star,
    COUNT(CASE WHEN rating = 2 THEN 1 END) as two_star,
    COUNT(CASE WHEN rating = 1 THEN 1 END) as one_star
    FROM feedback";
$stats_result = $conn->query($stats_sql);
$stats = $stats_result->fetch_assoc();

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Feedback - Bhosale Opticians Admin</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
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
                        <li class="nav-item"><a class="nav-link" href="orders.php">Orders</a></li>
                        <li class="nav-item"><a class="nav-link" href="appointments.php">Appointments</a></li>
                        <li class="nav-item"><a class="nav-link" href="sales-report.php">Sales Report</a></li>
                        <li class="nav-item"><a class="nav-link active" href="feedback.php">Feedback</a></li>
                        <li class="nav-item"><a class="nav-link" href="users.php">Users</a></li>
                        <li class="nav-item"><a class="nav-link" href="../logout.php">Logout</a></li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <section class="container my-5">
        <?php if (isset($_GET['setup']) && $_GET['setup'] == 'success'): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <h5 class="alert-heading">✅ Feedback System Setup Complete!</h5>
            <p class="mb-0">The feedback table has been created successfully. You can now view and manage customer feedback.</p>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Customer Feedback</h2>
            <div class="text-muted">
                <small>Total: <?= $stats['total_feedback'] ?> reviews | Average: <?= number_format($stats['avg_rating'], 1) ?> ⭐</small>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Filter by User</label>
                        <select name="user_id" class="form-select">
                            <option value="0">All Users</option>
                            <?php foreach($users_list as $user): ?>
                            <option value="<?= $user['id'] ?>" <?= $filter_user == $user['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($user['name']) ?> (<?= htmlspecialchars($user['email']) ?>)
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Filter by Rating</label>
                        <select name="rating" class="form-select">
                            <option value="0">All Ratings</option>
                            <option value="5" <?= $filter_rating == 5 ? 'selected' : '' ?>>⭐⭐⭐⭐⭐ (5 Stars)</option>
                            <option value="4" <?= $filter_rating == 4 ? 'selected' : '' ?>>⭐⭐⭐⭐ (4 Stars)</option>
                            <option value="3" <?= $filter_rating == 3 ? 'selected' : '' ?>>⭐⭐⭐ (3 Stars)</option>
                            <option value="2" <?= $filter_rating == 2 ? 'selected' : '' ?>>⭐⭐ (2 Stars)</option>
                            <option value="1" <?= $filter_rating == 1 ? 'selected' : '' ?>>⭐ (1 Star)</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Filter</button>
                            <a href="feedback.php" class="btn btn-outline-secondary">Clear</a>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div>
                            <small class="text-muted">Showing <?= count($feedback_list) ?> result(s)</small>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Feedback Statistics -->
        <div class="row g-4 mb-5">
            <div class="col-md-2">
                <div class="card text-center h-100" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                    <div class="card-body text-white">
                        <h3 class="mb-0"><?= $stats['total_feedback'] ?></h3>
                        <small>Total Reviews</small>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card text-center h-100" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <div class="card-body text-white">
                        <h3 class="mb-0"><?= number_format($stats['avg_rating'], 1) ?></h3>
                        <small>Avg Rating</small>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card h-100">
                    <div class="card-body">
                        <h6 class="mb-3">Rating Distribution</h6>
                        <div class="row text-center">
                            <div class="col">
                                <div class="text-warning fs-5">⭐⭐⭐⭐⭐</div>
                                <div class="fw-bold"><?= $stats['five_star'] ?></div>
                            </div>
                            <div class="col">
                                <div class="text-warning fs-5">⭐⭐⭐⭐</div>
                                <div class="fw-bold"><?= $stats['four_star'] ?></div>
                            </div>
                            <div class="col">
                                <div class="text-warning fs-5">⭐⭐⭐</div>
                                <div class="fw-bold"><?= $stats['three_star'] ?></div>
                            </div>
                            <div class="col">
                                <div class="text-warning fs-5">⭐⭐</div>
                                <div class="fw-bold"><?= $stats['two_star'] ?></div>
                            </div>
                            <div class="col">
                                <div class="text-warning fs-5">⭐</div>
                                <div class="fw-bold"><?= $stats['one_star'] ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Feedback List -->
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h4 class="mb-0">All Customer Reviews</h4>
            </div>
            <div class="card-body">
                <?php if(count($feedback_list) > 0): ?>
                    <?php foreach($feedback_list as $feedback): ?>
                    <div class="feedback-item">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <h6 class="mb-1"><?= htmlspecialchars($feedback['user_name']) ?></h6>
                                <small class="text-muted"><?= htmlspecialchars($feedback['user_email']) ?></small>
                                <?php if($feedback['order_number']): ?>
                                    <small class="text-muted"> • Order #<?= $feedback['order_number'] ?></small>
                                <?php endif; ?>
                            </div>
                            <div class="text-end">
                                <div class="feedback-rating mb-1">
                                    <?php for($i = 1; $i <= 5; $i++): ?>
                                        <?= $i <= $feedback['rating'] ? '⭐' : '☆' ?>
                                    <?php endfor; ?>
                                </div>
                                <small class="text-muted"><?= date('M d, Y', strtotime($feedback['created_at'])) ?></small>
                            </div>
                        </div>
                        <div class="feedback-text">
                            <?= nl2br(htmlspecialchars($feedback['feedback_text'])) ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center py-5">
                        <div class="text-muted mb-3">
                            <i class="fas fa-comments fa-3x"></i>
                        </div>
                        <h5 class="text-muted">No feedback yet</h5>
                        <p class="text-muted">Customer reviews will appear here once they start providing feedback.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Export Options -->
        <?php if(count($feedback_list) > 0): ?>
        <div class="mt-4 text-center">
            <button onclick="window.print()" class="btn btn-primary">
                🖨️ Print Feedback Report
            </button>
        </div>
        <?php endif; ?>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <style>
        @media print {
            .navbar, .btn, .card-header { display: none !important; }
            .feedback-item { break-inside: avoid; }
        }
    </style>
</body>
</html>