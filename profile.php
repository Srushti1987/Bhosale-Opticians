<?php
require_once '../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'user') {
    header('Location: ../login.php');
    exit();
}

$success = '';
$error = '';

// Update profile
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $conn = getDBConnection();
    $user_id = $_SESSION['user_id'];

    $name    = $conn->real_escape_string($_POST['name']);
    $email   = $conn->real_escape_string($_POST['email']);
    $phone   = $conn->real_escape_string($_POST['phone'] ?? '');
    $address = $conn->real_escape_string($_POST['address'] ?? '');
    $city    = $conn->real_escape_string($_POST['city'] ?? '');
    $state   = $conn->real_escape_string($_POST['state'] ?? '');
    $pincode = $conn->real_escape_string($_POST['pincode'] ?? '');

    $sql = "UPDATE users SET name='$name', email='$email', phone='$phone', address='$address', city='$city', state='$state', pincode='$pincode' WHERE id=$user_id";

    if ($conn->query($sql)) {
        $_SESSION['user_name']  = $name;
        $_SESSION['user_email'] = $email;
        $success = 'Profile updated successfully!';
    } else {
        $error = 'Failed to update profile.';
    }
    $conn->close();
}

// Change password
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    if ($new_password !== $confirm_password) {
        $error = 'New passwords do not match';
    } else {
        $conn = getDBConnection();
        $user_id = $_SESSION['user_id'];
        
        $sql = "SELECT password FROM users WHERE id = $user_id";
        $result = $conn->query($sql);
        $user = $result->fetch_assoc();
        
        if (password_verify($current_password, $user['password'])) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update_sql = "UPDATE users SET password = '$hashed_password' WHERE id = $user_id";
            
            if ($conn->query($update_sql)) {
                $success = 'Password changed successfully!';
            } else {
                $error = 'Failed to change password.';
            }
        } else {
            $error = 'Current password is incorrect.';
        }
        $conn->close();
    }
}

// Fetch current user data
$conn = getDBConnection();
$user_id = $_SESSION['user_id'];
$user_result = $conn->query("SELECT * FROM users WHERE id = $user_id");
$user_data = $user_result->fetch_assoc();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Bhosale Opticians</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
</head>
<body>

    <header class="sticky-top bg-white shadow-sm">
        <nav class="navbar navbar-expand-lg container">
            <div class="container-fluid px-3">
                <a class="navbar-brand fw-bold fs-4" href="../index_updated.php">
                    Bhosale Opticians
                </a>
                
                <div class="collapse navbar-collapse">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item"><a class="nav-link" href="../index_updated.php">Home</a></li>
                        <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link" href="../logout.php">Logout</a></li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <section class="container my-5">
        <div class="row">
            <div class="col-md-3 mb-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                <a href="dashboard.php" class="text-decoration-none text-dark">📊 Dashboard</a>
                            </li>
                            <li class="list-group-item">
                                <a href="orders.php" class="text-decoration-none text-dark">📦 My Orders</a>
                            </li>
                            <li class="list-group-item active">
                                <a href="profile.php" class="text-decoration-none text-white">👤 Profile</a>
                            </li>
                            <li class="list-group-item">
                                <a href="../logout.php" class="text-decoration-none text-danger">🚪 Logout</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-md-9">
                <h2 class="mb-4">My Profile</h2>
                
                <?php if($success): ?>
                <div class="alert alert-success"><?= $success ?></div>
                <?php endif; ?>
                <?php if($error): ?>
                <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>
                
                <!-- Update Profile -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">👤 Profile Information</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Full Name</label>
                                    <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($user_data['name'] ?? '') ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user_data['email'] ?? '') ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Phone</label>
                                    <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($user_data['phone'] ?? '') ?>" placeholder="10-digit mobile number">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Pincode</label>
                                    <input type="text" name="pincode" class="form-control" value="<?= htmlspecialchars($user_data['pincode'] ?? '') ?>" placeholder="6-digit pincode">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Address</label>
                                    <input type="text" name="address" class="form-control" value="<?= htmlspecialchars($user_data['address'] ?? '') ?>" placeholder="House no, Street, Area">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">City</label>
                                    <input type="text" name="city" class="form-control" value="<?= htmlspecialchars($user_data['city'] ?? '') ?>" placeholder="City">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">State</label>
                                    <select name="state" class="form-select">
                                        <option value="">Select State</option>
                                        <?php
                                        $states = ['Andhra Pradesh','Arunachal Pradesh','Assam','Bihar','Chhattisgarh','Goa','Gujarat','Haryana','Himachal Pradesh','Jharkhand','Karnataka','Kerala','Madhya Pradesh','Maharashtra','Manipur','Meghalaya','Mizoram','Nagaland','Odisha','Punjab','Rajasthan','Sikkim','Tamil Nadu','Telangana','Tripura','Uttar Pradesh','Uttarakhand','West Bengal','Delhi'];
                                        foreach($states as $s) {
                                            $sel = ($user_data['state'] ?? '') == $s ? 'selected' : '';
                                            echo "<option value=\"$s\" $sel>$s</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <button type="submit" name="update_profile" class="btn btn-primary mt-3">Save Changes</button>
                        </form>
                    </div>
                </div>

                <!-- Change Password -->
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">🔒 Change Password</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label">Current Password</label>
                                    <input type="password" name="current_password" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">New Password</label>
                                    <input type="password" name="new_password" class="form-control" required minlength="6">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Confirm New Password</label>
                                    <input type="password" name="confirm_password" class="form-control" required>
                                </div>
                            </div>
                            <button type="submit" name="change_password" class="btn btn-primary mt-3">Change Password</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>