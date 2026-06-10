<?php
require_once '../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header('Location: ../login.php');
    exit();
}

$success = '';
$error = '';

// Update appointment status / admin notes
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_appointment'])) {
    $conn = getDBConnection();
    $appt_id    = intval($_POST['appt_id']);
    $status     = $conn->real_escape_string($_POST['status']);
    $admin_notes = $conn->real_escape_string(trim($_POST['admin_notes'] ?? ''));

    $sql = "UPDATE appointments SET status='$status', admin_notes='$admin_notes' WHERE id=$appt_id";
    if ($conn->query($sql)) {
        $success = 'Appointment updated successfully!';
    } else {
        $error = 'Failed to update appointment.';
    }
    $conn->close();
}

// Filters
$conn = getDBConnection();
$where = "1=1";

if (!empty($_GET['status'])) {
    $fs = $conn->real_escape_string($_GET['status']);
    $where .= " AND a.status = '$fs'";
}
if (!empty($_GET['date'])) {
    $fd = $conn->real_escape_string($_GET['date']);
    $where .= " AND a.appointment_date = '$fd'";
}
if (!empty($_GET['search'])) {
    $fq = $conn->real_escape_string($_GET['search']);
    $where .= " AND (a.patient_name LIKE '%$fq%' OR a.patient_phone LIKE '%$fq%')";
}

$result = $conn->query("SELECT a.*, u.name as user_name, u.email as user_email
    FROM appointments a
    JOIN users u ON a.user_id = u.id
    WHERE $where
    ORDER BY a.appointment_date ASC, a.appointment_time ASC");

$appointments = [];
while ($row = $result->fetch_assoc()) $appointments[] = $row;

// Stats
$stats = [];
foreach (['pending','confirmed','completed','cancelled'] as $s) {
    $r = $conn->query("SELECT COUNT(*) as cnt FROM appointments WHERE status='$s'");
    $stats[$s] = $r->fetch_assoc()['cnt'];
}
$today_count = $conn->query("SELECT COUNT(*) as cnt FROM appointments WHERE appointment_date = CURDATE() AND status != 'cancelled'")->fetch_assoc()['cnt'];

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Appointments - Admin</title>
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
                    <li class="nav-item"><a class="nav-link" href="orders.php">Orders</a></li>
                    <li class="nav-item"><a class="nav-link active" href="appointments.php">Appointments</a></li>
                    <li class="nav-item"><a class="nav-link" href="users.php">Users</a></li>
                    <li class="nav-item"><a class="nav-link text-danger" href="../logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>
</header>

<section class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0">📅 Manage Appointments</h2>
        <span class="badge bg-primary fs-6"><?= count($appointments) ?> shown</span>
    </div>

    <?php if($success): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle me-2"></i><?= $success ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>
    <?php if($error): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <?= $error ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <!-- Stats -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm text-center py-3">
                <div class="fs-2 fw-bold text-warning"><?= $stats['pending'] ?></div>
                <div class="text-muted small">Pending</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm text-center py-3">
                <div class="fs-2 fw-bold text-success"><?= $stats['confirmed'] ?></div>
                <div class="text-muted small">Confirmed</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm text-center py-3">
                <div class="fs-2 fw-bold text-primary"><?= $stats['completed'] ?></div>
                <div class="text-muted small">Completed</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm text-center py-3">
                <div class="fs-2 fw-bold" style="color:#667eea;"><?= $today_count ?></div>
                <div class="text-muted small">Today's Appointments</div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Search Patient</label>
                    <input type="text" name="search" class="form-control" placeholder="Name or phone..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Filter by Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="pending"   <?= ($_GET['status'] ?? '') == 'pending'   ? 'selected' : '' ?>>Pending</option>
                        <option value="confirmed" <?= ($_GET['status'] ?? '') == 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                        <option value="completed" <?= ($_GET['status'] ?? '') == 'completed' ? 'selected' : '' ?>>Completed</option>
                        <option value="cancelled" <?= ($_GET['status'] ?? '') == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Filter by Date</label>
                    <input type="date" name="date" class="form-control" value="<?= htmlspecialchars($_GET['date'] ?? '') ?>">
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100">🔍 Filter</button>
                    <a href="appointments.php" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Appointments Table -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <?php if (!empty($appointments)): ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>#ID</th>
                            <th>Patient</th>
                            <th>Phone</th>
                            <th>Type</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Booked By</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($appointments as $appt):
                            $badge = match($appt['status']) {
                                'confirmed' => 'success',
                                'completed' => 'primary',
                                'cancelled' => 'danger',
                                default     => 'warning'
                            };
                            $is_today = $appt['appointment_date'] == date('Y-m-d');
                        ?>
                        <tr class="<?= $is_today ? 'table-info' : '' ?>">
                            <td><strong>#<?= $appt['id'] ?></strong><?= $is_today ? ' <span class="badge bg-info text-dark">Today</span>' : '' ?></td>
                            <td>
                                <?= htmlspecialchars($appt['patient_name']) ?>
                                <?php if($appt['patient_age']): ?>
                                <br><small class="text-muted">Age: <?= $appt['patient_age'] ?></small>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($appt['patient_phone']) ?></td>
                            <td><small><?= htmlspecialchars($appt['appointment_type']) ?></small></td>
                            <td><?= date('d M Y', strtotime($appt['appointment_date'])) ?></td>
                            <td><?= date('h:i A', strtotime($appt['appointment_time'])) ?></td>
                            <td>
                                <small><?= htmlspecialchars($appt['user_name']) ?></small><br>
                                <small class="text-muted"><?= htmlspecialchars($appt['user_email']) ?></small>
                            </td>
                            <td><span class="badge bg-<?= $badge ?>"><?= ucfirst($appt['status']) ?></span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modal<?= $appt['id'] ?>">
                                    <i class="bi bi-pencil"></i> Manage
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="text-center py-5">
                <div style="font-size:4rem;">📅</div>
                <h5 class="text-muted mt-3">No appointments found</h5>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Modals -->
<?php foreach($appointments as $appt): ?>
<div class="modal fade" id="modal<?= $appt['id'] ?>" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Appointment #<?= $appt['id'] ?> — <?= htmlspecialchars($appt['patient_name']) ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="appt_id" value="<?= $appt['id'] ?>">
                    <table class="table table-sm table-bordered mb-3">
                        <tr><th>Type</th><td><?= htmlspecialchars($appt['appointment_type']) ?></td></tr>
                        <tr><th>Date</th><td><?= date('d M Y', strtotime($appt['appointment_date'])) ?></td></tr>
                        <tr><th>Time</th><td><?= date('h:i A', strtotime($appt['appointment_time'])) ?></td></tr>
                        <tr><th>Phone</th><td><?= htmlspecialchars($appt['patient_phone']) ?></td></tr>
                        <?php if($appt['notes']): ?>
                        <tr><th>Patient Notes</th><td><?= htmlspecialchars($appt['notes']) ?></td></tr>
                        <?php endif; ?>
                    </table>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Update Status</label>
                        <select name="status" class="form-select">
                            <option value="pending"   <?= $appt['status']=='pending'   ? 'selected':'' ?>>⏳ Pending</option>
                            <option value="confirmed" <?= $appt['status']=='confirmed' ? 'selected':'' ?>>✅ Confirmed</option>
                            <option value="completed" <?= $appt['status']=='completed' ? 'selected':'' ?>>🏁 Completed</option>
                            <option value="cancelled" <?= $appt['status']=='cancelled' ? 'selected':'' ?>>❌ Cancelled</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Admin Notes (visible to patient)</label>
                        <textarea name="admin_notes" class="form-control" rows="3" placeholder="e.g. Please bring your previous prescription..."><?= htmlspecialchars($appt['admin_notes'] ?? '') ?></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" name="update_appointment" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endforeach; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
